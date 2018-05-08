<?php
namespace your\nmspace\Controllers;
use mark4tech\v2_1\WPController;

class CourseController extends WPController{
	public function filterPostTableColumns($columns) {
	    unset($columns['date']);
	    return array_merge($columns, 
	              array('lessons' => __('Lessons')),
	              array('date' => __('Date'))
	        );
	}
	public function managePostTableColumns( $column, $post_id ) {
	    switch ( $column ) {
	        case 'lessons' :
	            echo '<a href="#">'. rand(1,15) .'</a>';
	            break;
	    }
	}
}
