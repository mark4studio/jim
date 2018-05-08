<?php
namespace your\nmspace\Posts;
use mark4tech\v2_1\WPPostType;

class Programme extends WPPostType{
	public $postType = 'qp_programme';

	public function attributes(){
		return array(
    		'public'=>true,
            'show_in_nav_menus'=>false,
            'show_in_menu'=>false,
    		'query_var'=> true,
    		'rewrite' => array( 'slug' => 'programme' ),
    		'supports'=>array('title', 'editor'),
    	);
	}
    public function labels(){
        return array(
                'name'               => _x( 'Programmes', 'post type general name', 'qopolis' ),
                'singular_name'      => _x( 'Programme', 'post type singular name', 'qopolis' ),
                'menu_name'          => _x( 'Programmes', 'admin menu', 'qopolis' ),
                'name_admin_bar'     => _x( 'Programme', 'add new on admin bar', 'qopolis' ),
                'add_new'            => _x( 'Add New', 'Programme', 'qopolis' ),
                'add_new_item'       => __( 'Add New Programme', 'qopolis' ),
                'new_item'           => __( 'New Programme', 'qopolis' ),
                'edit_item'          => __( 'Edit Programme', 'qopolis' ),
                'view_item'          => __( 'View Programme', 'qopolis' ),
                'all_items'          => __( 'All Programmes', 'qopolis' ),
                'search_items'       => __( 'Search Programmes', 'qopolis' ),
                'parent_item_colon'  => __( 'Parent Programmes:', 'qopolis' ),
                'not_found'          => __( 'No Programmes found.', 'qopolis' ),
                'not_found_in_trash' => __( 'No Programmes found in Trash.', 'qopolis' )
            );
    }
	public function metaBoxes(){
		return array(
			array(
					'id'=>'courseAttributes',
					'title'=> __( 'Attributes', 'qopolis' ),
					'context' => 'side',
					'priority' =>'default',
					'view'=>'admin/programme/metaboxes/Attributes'
				)
		);
	}
}