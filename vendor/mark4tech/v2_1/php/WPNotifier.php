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

final class WPNotifier{
    protected static $notices = array();
	const NOTIFY_ERROR = 'error';
	const NOTIFY_WARNING = 'update-nag';
	const NOTIFY_SUCCESS = 'updated';

    public static function init(){
        //add_action('admin_notices', array(__CLASS__, 'sendNotices'));
    }
    public static function notify($message, $class=self::NOTIFY_SUCCESS){
        static::$notices[] = array('message'=>$message, 'class'=>$class);
	}
    public static function sendNotices(){
    	foreach (static::$notices as $notice)
        {
            echo "<div class=\"{$notice['class']}\"><p>{$notice['message']}</p></div>";
        }
        static::$notices = array();
    }
}