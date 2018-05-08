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

class ClassLoader{

    public $namespace;
    public $classInstances;
    private $plugin;

    public function __construct($ns, $plugin){
        $this->namespace = $ns;
        $this->plugin = $plugin;
    }
    public function load($path){
        if(!$path) return;
        
        $pattern = '/^([\w\/]*)(@(\w+))?$/';
        $parts = array();
        
        if(1===preg_match($pattern, $path, $parts)){
            $path = $parts[1];
            $method = isset($parts[3]) ? $parts[3] : null;
            
        	if(isset($this->classInstances[$path])){
                if(!$method) return $this->classInstances[$path];
        		return array($this->classInstances[$path], $method);
        	}
            if(file_exists($this->plugin->config['app_path'] . '/' .$path.'.php')){
            	include($this->plugin->config['app_path'] . '/' .$path.'.php');
            	$class = $this->namespace . '\\' . str_replace('/', '\\', $path);
            	$this->classInstances[$path] = new $class($this->plugin);

                if(!$method) return $this->classInstances[$path];

                if($method && method_exists($this->classInstances[$path], $method)){
                    return array($this->classInstances[$path], $method);
                }
            }
        }
    }
}