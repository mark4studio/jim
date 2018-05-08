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

final class Utils{
	public static function resolvePath($dir){
		if(!file_exists($dir)){
			mkdir($dir, 0777, true);
		}
		return $dir;
	}
	public static function normalizeString($str){
		if(preg_match_all('/([A-Z]?[a-z0-9]*)([\s_\-\.]?)/', $str, $matches, PREG_PATTERN_ORDER)){
			$pieces = array_map('ucfirst', $matches[1]);
			return implode(' ', $pieces);
		}
		return $str;
	}
}