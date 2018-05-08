<?php
namespace your\nmspace;
use mark4tech\v2_1\WPPlugin;

class DemoPlugin extends WPPlugin{
    public function init(){
        
        /*add_filter('manage_qp_course_posts_columns' , $this->classLoader->load('Controllers/CourseController@filterPostTableColumns'));
        add_action( 'manage_qp_course_posts_custom_column' , $this->classLoader->load('Controllers/CourseController@managePostTableColumns'), 10, 2 );*/
        
    }
    public function adminPages(){
        return array(
                'manage_options'=>array(
                        array(
                            'title'=>'Qopolis',
                            'slug'=>'qopolis_dashboard',
                            'use'=>'Controllers/Admin/MainController@index',
                            'submenu_alias_title'=>'Dashboard',
                            'icon'=>$this->assetUrl('images/icons/qopolis-mono.png'),
                            'position'=>'2.1'
                        ),
                        array(
                            'title'=>'Programmes',
                            'parent'=>'qopolis_dashboard',
                            'slug'=>'edit.php?post_type=qp_programme'
                        ),
                        array(
                            'title'=>'Courses',
                            'parent'=>'qopolis_dashboard',
                            'slug'=>'edit.php?post_type=qp_course'
                        ),
                        array(
                            'title'=>'Lessons',
                            'parent'=>'qopolis_dashboard',
                            'slug'=>'edit.php?post_type=qp_lesson'
                        ),

                    )
            );
    }
    public function adminScripts(){
        return array(
                '*'=>array(
                    array(
                        'as'=>'adminwidgets-css',
                        'type'=>'style',
                        'src'=>$this->assetUrl('css/AdminWidgets.css')
                    ),
                )
            );
    }
}
return new DemoPlugin(__NAMESPACE__, $config);