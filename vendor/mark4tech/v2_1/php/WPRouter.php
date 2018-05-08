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


class WPRouter{
	private $actions = array();
	private $tagPattern = '/<(\w+)(:([^\/]+))?>/'; //<tag:format_reg_ex>
	private $plugin;
	public function __construct($plugin, $routes=null){
		$this->plugin = $plugin;
		if($routes){
			$this->loadRoutes($routes);
		}
	}
	public function loadRoutes($routes){
		add_rewrite_tag('%mark4tech_action_id%', '([^&]+)');
    	add_action('parse_request', array($this, 'handleRequest'));

		foreach($routes as $pattern=>$path){
			$this->actions[] = $path;
			$id = count($this->actions)-1;
			$redirect_url = 'index.php?mark4tech_action_id='.$id.'&';
			$matches = array();
			$pattern = str_replace('/', '\\/', trim($pattern, '/'));
			if(preg_match_all($this->tagPattern, $pattern, $matches) > 0){
				foreach ($matches[1] as $id => $param)
	            {
	                add_rewrite_tag('%' . $param . '%', '([^&]+)');
	                $redirect_url .= $param . '=$matches[' . ($id + 1) . ']&';
	            }
				$pattern = preg_replace($this->tagPattern, '($3)', $pattern);
			}
			$pattern = '^'. $pattern .'\/?';
			$redirect_url = trim($redirect_url, '&');
			add_rewrite_rule($pattern, $redirect_url, 'top');
    	}
	}
	public function handleRequest($query){
		if(isset($query->query_vars['mark4tech_action_id'])){
			$action_id = $query->query_vars['mark4tech_action_id'];
			$callback = $this->plugin->classLoader->load($this->actions[$action_id]);
			if($callback){
				echo call_user_func($callback, $query->query_vars);
				die();
			}
		}
	}
}