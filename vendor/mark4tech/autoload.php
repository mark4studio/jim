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

if(class_exists('mark4tech\\v2_1\\Jim')) return;

final class Jim{
	public static function loadPlugin($plugin_dir){
		$config = require($plugin_dir . '/config/main.php');
		if(is_null($config)) return;
		$plugin_file = $plugin_dir.'/plugin.php';
		$config['path'] = $plugin_dir;
		$config['app_path'] = $plugin_dir . '/app';
		
		$config['url'] = plugin_dir_url($plugin_file);
		$config['framework_url'] = $config['url'] . 'vendor/'.  str_replace("\\", '/', __NAMESPACE__) .'/';
		$pluginClass = __NAMESPACE__ . '\\WPPlugin';
		if(!class_exists($pluginClass)){
			$framework_dir = str_replace("\\", DIRECTORY_SEPARATOR, __NAMESPACE__);
			require($plugin_dir . '/vendor/'. $framework_dir .'/php/WPPlugin.php');
		}
		if(class_exists($pluginClass)){
			$plugin = require_once($config['app_path'] . '/bootstrap.php');
			$plugin->basePath = $plugin_dir;
		
			return $plugin;
		}
	}
}