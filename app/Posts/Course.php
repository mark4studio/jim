<?php
namespace your\nmspace\Posts;
use mark4tech\v2_1\WPPostType;

class Course extends WPPostType{
	public $postType = 'qp_course';
    public function meta(){
        return array(
            'qp_programme'=>'required',
            'qp_prerequisites'=>'safe'
        );
    }
    public function metaLabels(){
        return array(
                'qp_programme'=>'Programme',
                'qp_prerequisites'=>'Prerequisites'
            );
    }
	public function attributes(){
		return array(
    		'public'=>true,
            'show_in_nav_menus'=>false,
            'show_in_menu'=>false,
    		'hierarchical' => true,
    		'query_var'=> true,
    		'rewrite' => array( 'slug' => 'course' ),
    		'supports'=>array('title', 'editor', 'page-attributes'),
    	);
	}
    public function labels(){
        return array(
                'name'               => _x( 'Courses', 'post type general name', 'qopolis' ),
                'singular_name'      => _x( 'Course', 'post type singular name', 'qopolis' ),
                'menu_name'          => _x( 'Courses', 'admin menu', 'qopolis' ),
                'name_admin_bar'     => _x( 'Course', 'add new on admin bar', 'qopolis' ),
                'add_new'            => _x( 'Add New', 'Course', 'qopolis' ),
                'add_new_item'       => __( 'Add New Course', 'qopolis' ),
                'new_item'           => __( 'New Course', 'qopolis' ),
                'edit_item'          => __( 'Edit Course', 'qopolis' ),
                'view_item'          => __( 'View Course', 'qopolis' ),
                'all_items'          => __( 'All Courses', 'qopolis' ),
                'search_items'       => __( 'Search Courses', 'qopolis' ),
                'parent_item_colon'  => __( 'Parent Courses:', 'qopolis' ),
                'not_found'          => __( 'No Courses found.', 'qopolis' ),
                'not_found_in_trash' => __( 'No Courses found in Trash.', 'qopolis' )
            );
    }
	public function metaBoxes(){
		return array(

			array(
					'id'=>'courseProgrammes',
					'title'=> __( 'Programmes', 'qopolis' ),
					'context' => 'side',
					'priority' =>'default',
					'view'=>'admin/course/metaboxes/Programmes'
				),
            array(
                    'id'=>'prerequisites',
                    'title'=> __( 'Prerequisites', 'qopolis' ),
                    'context' => 'side',
                    'priority' =>'default',
                    'view'=>'admin/course/metaboxes/Prerequisites'
                ),
            array(
                    'id'=>'lessons',
                    'title'=> __( 'Lessons', 'qopolis' ),
                    'context' => 'advanced',
                    'priority' =>'default',
                    'view'=>'admin/course/metaboxes/Lessons'
                ),
		);
	}
    public function listTableColumnTitles($columns){
        unset($columns['date']);
        return array_merge($columns, 
                array(
                'lessons' =>__('Lessons'),
                'date' => __('Date')
                )
            );
    }
    public function listTableColumns($column, $post_id){
        switch ( $column ) {
            case 'lessons' :
                echo '<a href="#">'. rand(1,15) .'</a>';
                break;
        }
    }
    public function adminScripts(){
        return array(
                array(
                    'as'=>'admin-widgets-css',
                    'src'=>$this->plugin->assetUrl('css/AdminWidgets.css')
                ),
            );
    }
    public function onSave($post, $is_new,  $form=null){
        $form = $_POST;
        $form['qp_programme'] = implode(',', $form['qp_programme']);
        $form['qp_prerequisites'] = implode(',', $form['qp_prerequisites']);
        parent::onSave($post, $is_new, $form);
    }
}