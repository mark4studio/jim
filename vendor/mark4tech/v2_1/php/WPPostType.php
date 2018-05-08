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

class WPPostType{
	public $plugin;
	public $postType;
	public function __construct($plugin){
		$this->plugin = $plugin;
		$this->validator = new WPValidator();
		if(method_exists($this, 'meta')){
			$this->validator->rules = $this->meta();
		}
		if(method_exists($this, 'metaLabels')){
			$this->validator->labels = $this->metaLabels();
		}
	}
	public function register(){
		$args = array();
		if(method_exists($this, 'metaBoxes')){
			$args['register_meta_box_cb'] = array($this, 'customMetaBoxes');
		}
		if(method_exists($this, 'labels')){
			$args['labels'] = $this->labels();
		}
		$args = array_merge($args, $this->attributes());
		register_post_type($this->postType, $args);
		if(method_exists($this, 'listTableColumnTitles')){
			add_filter('manage_'. $this->postType .'_posts_columns' , array($this, 'listTableColumnTitles'));
		}
		if(method_exists($this, 'listTableColumns')){
			add_action( 'manage_'. $this->postType .'_posts_custom_column' , array($this, 'listTableColumns'), 10, 2);
		}
	}
	public function customMetaBoxes($post){
		$boxes = $this->metaBoxes();
		foreach($boxes as $args){
			$show = true;
			if(isset($args['visibility_filter']) && is_callable($args['visibility_filter'])){
				$show = call_user_func($args['visibility_filter'], $post);
			}
			if($show===true){
				add_meta_box(@$args['id'], @$args['title'], array($this, 'renderMetaBox'), $this->postType, @$args['context'], @$args['priority'], array('view'=>@$args['view']));
			}
		}
	}
	public function renderMetaBox($post, $box){
		$view = new WPView($this->plugin);
		echo $view->render($box['args']['view'], array('post'=>$post));
	}
	public function getScripts(){
		if(method_exists($this, 'adminScripts')){
			$predefined = array(
					array(
	                    'as'=>'wppost-js',
	                    'type'=>'script',
	                    'src'=>$this->plugin->frameworkUrl('js/WPPost.js'),
	                    'variables'=>array('WPPost', array('postType'=>$this->postType, 'pluginName'=>@$this->plugin->config['title']))
	                ),
				);
			$admin_scripts = array_merge($this->adminScripts(), $predefined);
			return array($this->postType=>$admin_scripts);
		}
	}
	public function ajaxValidate(){
		$result = new \stdClass();
		$result->success = $this->validator->validate($_POST);
		$result->errors = $this->validator->errors;
		echo json_encode($result);
		wp_die();
	}
	public function getMetaFields(){

	}
	public function onSave($post, $is_new, $form=null){
		$form = $form ? $form : $_POST;
		if($this->validator->validate($form)){
			foreach($this->validator->rules as $fields=>$rule){
				$fields = explode(',', $fields);
				foreach($fields as $field){
					$field = trim($field);
					$this->plugin->debug->log('saving meta for post : '. $post->ID . ', field:'. $field . ', value:'. $form[$field]);
					if (!add_post_meta($post->ID, $field, $form[$field], true)) { 
					   update_post_meta($post->ID,  $field, $form[$field]);
					}
				}
			}
		}
	}
}