<?php
namespace your\nmspace\Posts;
use mark4tech\v2_1\WPPostType;

class Lesson extends WPPostType{
	public $postType = 'qp_lesson';
     public function meta(){
        return array(
            'qp_course'=>'required',
        );
    }
    public function metaLabels(){
        return array(
                'qp_course'=>'Course'
            );
    }
	public function attributes(){
		return array(
    		'public'=>true,
            'show_in_nav_menus'=>false,
            'show_in_menu'=>false,
    		'query_var'=> true,
    		'rewrite' => array( 'slug' => 'lesson' ),
    		'supports'=>array('title', 'editor'),
    	);
	}
    public function labels(){
        return array(
                'name'               => _x( 'Lessons', 'post type general name', 'qopolis' ),
                'singular_name'      => _x( 'Lesson', 'post type singular name', 'qopolis' ),
                'menu_name'          => _x( 'Lessons', 'admin menu', 'qopolis' ),
                'name_admin_bar'     => _x( 'Lesson', 'add new on admin bar', 'qopolis' ),
                'add_new'            => _x( 'Add New', 'Lesson', 'qopolis' ),
                'add_new_item'       => __( 'Add New Lesson', 'qopolis' ),
                'new_item'           => __( 'New Lesson', 'qopolis' ),
                'edit_item'          => __( 'Edit Lesson', 'qopolis' ),
                'view_item'          => __( 'View Lesson', 'qopolis' ),
                'all_items'          => __( 'All Lessons', 'qopolis' ),
                'search_items'       => __( 'Search Lessons', 'qopolis' ),
                'parent_item_colon'  => __( 'Parent Lessons:', 'qopolis' ),
                'not_found'          => __( 'No Lessons found.', 'qopolis' ),
                'not_found_in_trash' => __( 'No Lessons found in Trash.', 'qopolis' )
            );
    }
	public function metaBoxes(){
		return array(
			array(
					'id'=>'lessonCourses',
					'title'=> __( 'Courses', 'qopolis' ),
					'context' => 'side',
					'priority' =>'default',
					'view'=>'admin/lesson/metaboxes/Courses'
				)
		);
	}
    public function onSave($post, $is_new,  $form=null){
        $form = $_POST;
        if(isset($form['qp_course'])){
            $form['qp_course'] = implode(',', $form['qp_course']);
        }
        parent::onSave($post, $is_new, $form);
    }
}