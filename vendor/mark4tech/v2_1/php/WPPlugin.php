<?php
/**
Jim - MVC wordpress plugin development framework
Version:           2.1
Copyright (C) 2018  Naveen

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

 */
 
namespace mark4tech\v2_1;

require_once('Utils.php');
require_once('WPDebug.php');
require_once('WPErrorHandler.php');
require_once('WPValidator.php');
require_once('WPNotifier.php');
require_once('ClassLoader.php');
require_once('WPView.php');
require_once('WPController.php');
require_once('WPRouter.php');
require_once('WPPostType.php');

use mark4tech\v2_1\ClassLoader;
use mark4tech\v2_1\WPDebug;
use mark4tech\v2_1\WPErrorHandler;
use mark4tech\v2_1\WPRouter;

class WPPlugin{

	public $notifier;
    public $config;
    private $plugin_namespace;
    private $router;
    public $classLoader;
    public $debug;
    public $basePath;
    private $defaultScripts;
    public $registeredPosts;

    public function __construct($namespace, $config){

        if(function_exists('spl_autoload_register')){
            spl_autoload_register(array($this, 'loadModuleClass'));
        }
        else{
            foreach (glob($this->config['app_path'] . "/Modules/*.php") as $filename){
                include_once($filename);
            }
        }
        
        WPNotifier::init();
        $this->config = $config;
        $this->plugin_namespace = $namespace;
        $this->defaultScripts = array();
        $this->classLoader = new ClassLoader($namespace, $this);
        $this->debug = new WPDebug($this);
        $this->errorHandler = new WPErrorHandler($this);
        $this->init_actions();
    }
    public function pheobeVersion(){
        $pattern = '/\\v([0-9_]+)\\?/i';
        if(preg_match($pattern, __NAMESPACE__, $matches)){
            return str_replace('_', '.', $matches[1]);
        }
        return '';
    }
    public function version(){
        return isset($this->config['version']) ? $this->config['version'] : null;
    }
    private function loadModuleClass($className){
        $filename = $this->config['app_path'] . '/Modules/'. $className . '.php';
        if(file_exists($filename)) include_once($filename);
    }
    private function init_actions(){
        //add wp hooks

        add_action('plugins_loaded', array($this, "update_db"));
        add_action('init', array($this, '_wp_init'));
        add_action('activated_plugin', array($this, '_activate'));
        add_action('deactivated_plugin', array($this, '_deactivate'));

        if(method_exists($this, 'adminPages')) add_action('admin_menu', array($this, '_admin_pages'));
        if(method_exists($this, 'adminScripts')) add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        if(method_exists($this, 'scripts')) add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        if(method_exists($this, 'ajaxActions')){
            $ajaxActions = $this->ajaxActions();
            foreach($ajaxActions as $priv=>$actions){
                foreach($actions as $action=>$path){
                    if($priv=='admin'){
                        add_action('wp_ajax_'. $action, $this->classLoader->load($path));
                    }
                    elseif($priv=='frontend' || $priv=='*'){
                        add_action('wp_ajax_nopriv'. $action, $this->classLoader->load($path));
                    }
                }
            }
        }
    }
    private function process_routes(){
        $this->router = new WPRouter($this, $this->routes());
    }
    public function _activate(){
        flush_rewrite_rules();
        if(@$this->config['environment']=='development'){
            $this->debug->log('Activation : '. ob_get_contents());
        }
        if(method_exists($this, 'activate')) $this->activate();
    }
    public function _deactivate(){
        if(method_exists($this, 'deactivate')) $this->deactivate();
    }
    public function _wp_init(){
        if(method_exists($this, 'routes')){
          if(isset($this->config['environment']) && $this->config['environment'] == 'development') flush_rewrite_rules();
          $this->process_routes();  
        } 
        $this->loadCustomPostTypes();

        if(method_exists($this, 'init')) $this->init();
        if(method_exists($this, 'admin_ajax_actions')) {
            $ajax_actions = $this->admin_ajax_actions();
            foreach($ajax_actions as $action=>$path){
                add_action( 'wp_ajax_'. $action, $this->classLoader->load($path));
            }
        }
        if(method_exists($this, 'shortcodes')){
            $shortcodes = $this->shortcodes();
            foreach($shortcodes as $shortcode=>$path){
                add_shortcode($shortcode, $this->classLoader->load($path));
            }
        }

    }
    public function _admin_pages(){
        $menus = $this->adminPages();
        foreach($menus as $filter=>$menu)
        {
            $filter = trim($filter);
            $filter = ($filter=='*' ? 'read' : $filter);
            if(isset($menu['title'])){
                $menu = array($menu);
            }
            if(current_user_can($filter)){
                for($i=0; $i<count($menu); $i++){
                    $defaultValues = array('nomenu'=>false, 'title'=>null, 'slug'=>null, 'use'=>null, 'icon'=>null, 'position'=>null, 'href'=>'');
                    $item = array_merge($defaultValues, $menu[$i]);
                    if($item['nomenu'] === true){
                        add_submenu_page('', $item['title'], $item['title'], $filter, $item['slug'], $this->classLoader->load($item['use']));
                    }
                    elseif(isset($item['topbar']) && $item['topbar']===true){
                        global $wp_admin_bar;
                        $wp_admin_bar->add_menu(
                            array(
                                'parent'=>@$item['parent'],
                                'id'=>$item['slug'],
                                'title'=>$item['title'],
                                'href'=>$item['href']
                            )
                        );
                    }
                    elseif(isset($item['parent'])){
                        add_submenu_page($item['parent'], $item['title'], $item['title'], $filter, $item['slug'], $this->classLoader->load($item['use']));
                    }
                    else{       
                        add_menu_page($item['title'], $item['title'], $filter, $item['slug'], $this->classLoader->load($item['use']), $item['icon'], $item['position']);
                        if(isset($item['submenu_alias_title'])){
                            add_submenu_page($item['slug'], $item['submenu_alias_title'], $item['submenu_alias_title'], $filter, $item['slug']);
                        }
                    }
                }
            }
        }
    }
    public function enqueue_admin_scripts($hook){
        $this->enqueue_scripts($hook, true);
    }
    public function enqueue_scripts($hook, $admin=false){
        //echo "plugin hook:".$hook;
        $scripts = array();
        if($admin){
            
            if(method_exists($this, 'adminScripts')){
                $scripts = $this->adminScripts();
            }
            $scripts = array_merge($this->defaultScripts, $scripts);
        }
        else{
            $scripts = $this->scripts();
        }
        $script_queue = array();
        $style_queue = array();

        foreach($scripts as $filter=>$script)
        {
            $filter = trim($filter);
            if(isset($script['src'])){
                $script = array($script);
            }


            if(in_array($hook, array('post.php', 'post-new.php'))){
                $screen = get_current_screen();

                if( is_object( $screen ) && $filter == $screen->post_type ){
                    $filter = '*';
                }
            }
            $_GET['page'] = isset($_GET['page']) ? $_GET['page'] : null;

            if(in_array($_GET['page'], array_map('trim', explode(',', $filter))) || ($filter!='' && $filter!='*' && preg_match('/'.trim($filter,'/').'/', $_GET['page'])) || '*' === $filter){
                for($i=0; $i<count($script);$i++){
                    $defaultValues = array('type'=>null, 'deps'=>null, 'version'=>false, 'footer'=>false, 'variables'=>null, 'overwrite'=>false);
                    $item = array_merge($defaultValues, $script[$i]);
                    if('script' === $item['type']){
                        if($item['overwrite']===true){
                            wp_deregister_script($item['as']);
                        }
                        wp_register_script($item['as'], $item['src'], $item['deps'], (isset($item['version']) ? $item['version'] : false), (isset($item['footer']) && $item['footer']));
                        if(isset($item['variables']) && is_array($item['variables']) && count($item['variables'])==2){
                            wp_localize_script($item['as'], $item['variables'][0], $item['variables'][1]);
                        }
                        if(is_array($item['deps']) && count($item['deps'])>0){
                            foreach($item['deps'] as $dep){
                                $dep_index = array_search($dep, $script_queue);
                                if($dep_index !== false){
                                    $script_queue[$dep_index] = $item['as'];
                                }
                                else{
                                    array_push($script_queue, $item['as']);
                                }
                            }
                        }
                        else{
                            array_push($script_queue, $item['as']);
                        }
                    }
                    else{
                        wp_register_style($item['as'], $item['src'], $item['deps'], (isset($item['version']) ? $item['version'] : false));
                        if(is_array($item['deps']) && count($item['deps'])>0){
                            foreach($item['deps'] as $dep){
                                $dep_index = array_search($dep, $style_queue);
                                if($dep_index !== false){
                                    $style_queue[$dep_index] = $item['as'];
                                }
                                else{
                                    array_push($style_queue, $item['as']);
                                }
                            }
                        }
                        else{
                            array_push($style_queue, $item['as']);
                        }
                    }
                }
            }
        }
        $script_queue = array_unique($script_queue);
        $style_queue = array_unique($style_queue);
        
        foreach($script_queue as $script) wp_enqueue_script($script);
        foreach($style_queue as $style) wp_enqueue_style($style);
    }
    public function update_option( $option, $new_value, $autoload=null ){
        return update_option( $this->plugin_namespace . '_' . $option, $new_value, $autoload );
    }
    public function get_option($option, $default=false){
        return stripslashes(get_option($this->plugin_namespace . '_' . $option, $default));
    }
    private function normalize_admin_menu(){
        $menus = $this->admin_menu();
        $new_menu = array();
        foreach($menus as $filter=>$menu)
        {
            $filter = trim($filter);
            if(isset($menu['title'])){
                $menu = array($menu);
            }
            if('*' === $filter || current_user_can($filter)){
                $new_menu[] = $menu;
            }
        }
        return $new_menu;
    }
    public function assetUrl($asset_path){
        return $this->config['url'] . 'assets/' . ltrim($asset_path, '/');
    }
    public function frameworkUrl($path){
        return $this->config['framework_url'] . ltrim($path, '/');
    }
    public function update_db(){
        if(isset($this->config['dbSchema'])){
            if ($this->get_option('db_version') !== $this->config['dbSchema']['version']) {
              require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
              $schema = $this->build_schema();
              if(strlen($schema)>0) dbDelta($schema);
              $this->update_option('db_version', $this->config['dbSchema']['version']);
            }
        }
    }
    private function build_schema(){
        global $wpdb;
        $collate = '';
        if ( $wpdb->has_cap( 'collation' ) ) {
            if ( ! empty( $wpdb->charset ) ) {
                $collate .= "DEFAULT CHARACTER SET {$wpdb->charset}";
            }
            if ( ! empty( $wpdb->collate ) ) {
                $collate .= " COLLATE {$wpdb->collate}";
            }
        }
        $tables = @$this->config['dbSchema']['tables'];

        $q = '';
        if(is_string($tables)){
            $q = $tables;
        }
        elseif(is_array($tables)){
            foreach($tables as $table_name=>$fields){
                $q .= 'CREATE TABLE '. $wpdb->prefix . $table_name . ' (';

                if(is_string($fields)){
                    $q .= $fields;
                    $q = rtrim($q, ', ') . ') ' . $collate . ";";
                    continue;
                }

                $keys = array('primary'=>'', 'unique'=>array(), 'index'=>array());
                
                
                foreach($fields as $field_name=>$options){
                    if(is_string($options)){
                        $q .= '`' . $field_name . '` ' . $options . ', ';
                    }
                    elseif(is_array($options) && isset($options['type'])){
                        $q .= '`' . $field_name . '` ' . $options['type'];
                        if(@$options['unsigned']===true){
                            $q .= ' UNSIGNED';
                        }
                        if(@$options['primary']===true || @$options['index']===true  || @$options['null'] === false){
                            if(strpos($options['type'], 'NOT NULL')===false){
                                $q .= ' NOT NULL';
                            }
                            if($options['primary']===true){
                                $keys['primary'] = $field_name;
                            }
                            elseif(isset($options['index'])){
                                if(is_string($options['index'])){
                                    if(@$keys['index'][$options['index']] === null){
                                        $keys['index'][$options['index']] = array();
                                    }
                                    array_push($keys['index'][$options['index']], $field_name);
                                }
                                elseif($options['index'] === true){
                                    $keys['index'][$field_name] = $field_name;
                                }
                            }
                        }
                        
                        if(@$options['default'] !== null){
                            $q .= ' DEFAULT \'' . $options['default'] . '\'';
                        }
                        if(@$options['autoincrement']===true){
                            $q .= ' AUTO_INCREMENT';
                        }
                        if(isset($options['unique'])){
                            if(is_string($options['unique'])){
                                if(@$keys['unique'][$options['unique']] === null){
                                    $keys['unique'][$options['unique']] = array();
                                }
                                array_push($keys['unique'][$options['unique']], $field_name);
                            }
                            elseif($options['unique'] === true){
                                $keys['unique'][$field_name] = $field_name;
                            }
                        }
                        $q .= ',';
                    }
                }
                if($keys['primary']){
                    $q .= ' PRIMARY KEY ('. $keys['primary'] .'),';
                }
                if($keys['unique']){
                    foreach($keys['unique'] as $constraint_name => $unique_field){
                        if(is_array($unique_field)){
                            $q .= ' CONSTRAINT '. $constraint_name . ' UNIQUE (' . implode(',', $unique_field) . '),';
                        }
                        elseif(is_string($unique_field)){
                            $q .= ' UNIQUE KEY `' . $unique_field . '` (`'. $unique_field . '`),';
                        }
                        
                    }
                }
                if($keys['index']){
                    foreach($keys['index'] as $index_name => $index_field){
                        if(is_array($index_field)){
                            $q .= ' KEY '. $index_name . ' (' . implode(',', $index_field) . '),';
                        }
                        elseif(is_string($index_field)){
                            $q .= ' KEY `' . $index_name . '` (`'. $index_field . '`),';
                        }
                    }
                }
                $q = rtrim($q, ', ') . ') ' . $collate . ";";
            }
        }
        return $q;
    }
    private function loadCustomPostTypes(){

        foreach (glob($this->config['app_path'] . "/Posts/*.php") as $filename)
        {
            $classCode =  file_get_contents($filename);
            $matches = array();
            if(preg_match('/class\s+(\w+)\s*/', $classCode, $matches)){
                $customPostType = $this->classLoader->load('Posts/'. $matches[1]);
                $customPostType->register();
                $this->registeredPosts[$customPostType->postType] = $customPostType;
                add_action( 'wp_ajax_validatepost_'. $customPostType->postType, array($customPostType, 'ajaxValidate'));
                $scripts = $customPostType->getScripts();
                if(is_array($scripts)){
                    $this->defaultScripts = array_merge($this->defaultScripts, $scripts);
                }
            }
        }
        add_action( 'save_post', array($this, 'onPostSave'), 10, 3);
    }
    public function onPostSave($post_ID, $post, $update){
        if (!current_user_can('edit_post', $post_ID)) { // relying on nonce is not recommended.
            return $post_ID;
          }
         // check if there is a registered post with the current post's post_type and call onSave method of the WPPostType object if found.
        if(array_key_exists($post->post_type, $this->registeredPosts)){
            remove_action('save_post', array($this, 'onPostSave'), 10);
            $customPostType = $this->registeredPosts[$post->post_type];
            $customPostType->onSave($post, !$update);
            add_action( 'save_post', array($this, 'onPostSave'), 10, 3);
        }
    }
    public function isRequest( $type ) {
        switch ( $type ) {
            case 'admin' :
                return is_admin();
            case 'ajax' :
                return defined( 'DOING_AJAX' );
            case 'cron' :
                return defined( 'DOING_CRON' );
            case 'frontend' :
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
    }
}