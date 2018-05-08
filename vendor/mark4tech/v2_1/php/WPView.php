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

class WPView{
	public function __construct($plugin){
		$this->plugin = $plugin;
	}
	public function render($path, $args=null, $layout=null){
		$this->content = $this->renderPartial($path, $args);
		if($args) extract($args);
		if($layout){
			$_layout = $this->plugin->config['app_path'] . '/Views/'.$layout.'.php';
			if(file_exists($_layout)){
				ob_start();
				include_once($_layout);
			    echo ob_get_clean();
			    return;
			}
		}
		return $this->content;
	}
	public function renderPartial($path, $args=null){
		$path = $this->plugin->config['app_path'] . '/Views/'.$path.'.php';
		$this->content = '';
		if($args) extract($args);
		if(file_exists($path)){
			ob_start();
			include_once($path);
		    return ob_get_clean();
		}
	}
	public function assetUrl($path){
		return $this->plugin->assetUrl($path);
	}
	public function json($object){
		wp_send_json($object);
	}
}