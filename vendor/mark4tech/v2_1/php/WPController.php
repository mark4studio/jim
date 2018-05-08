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

class WPController{
	public $plugin;
	public $layout;
	public function __construct($plugin){
		$this->plugin = $plugin;
	}
	public function view($path, $args=null, $layout=null){
		echo $this->getView($path, $args, $layout);
	}
	public function getView($path, $args=null, $layout=null){
		$layout = $layout ? $layout : $this->layout;
		$view = new WPView($this->plugin);
		return $view->render($path, $args, $layout);
	}
	public function redirect($path){
		wp_redirect($path);
		exit;
	}
}