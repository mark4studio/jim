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

use mark4tech\v2_1\Utils;

class WPDebug{
	const ERROR = 'ERROR';
	const WARNING = 'WARNING';
    const MESSAGE = 'MESSAGE';
    public function __construct($plugin){
        $this->logPath = rtrim($plugin->config['path'], '/') . '/log';
        Utils::resolvePath($this->logPath);
    }
    public function log($message, $type=WPDebug::MESSAGE){
        $logfile = fopen($this->logPath . "/log.". date('Y-m-d') .".txt", "a");
        $message = date('Y-m-d H:i:s') . " $type : " . rtrim($message, "\r\n") . "\r\n";
        fwrite($logfile, $message);
        fclose($logfile);
    }
}