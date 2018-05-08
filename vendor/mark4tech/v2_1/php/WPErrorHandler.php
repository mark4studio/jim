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

use \ErrorException;
use \Exception;

class WPErrorHandler{
	function __construct($plugin){
		$this->plugin = $plugin;
		register_shutdown_function( array($this, "check_for_fatal") );
		set_error_handler( array($this, "log_error") );
		set_exception_handler( array($this, "log_exception") );
		ini_set( "display_errors", "on" );
		error_reporting( E_ALL );
	}

	/**
	* Error handler, passes flow over the exception logger with new ErrorException.
	*/
	function log_error( $num, $str, $file, $line, $context = null )
	{
	    $this->log_exception(new \ErrorException($str, 0, $num, $file, $line));
	}

	/**
	* Uncaught exception handler.
	*/
	function log_exception( Exception $e )
	{

	    if ( true == @$this->plugin->config["debug"] && 'development' == @$this->plugin->config["environment"] && false !== @$this->plugin->config["logErrors"])
	    {
	        $message = "(Type: " . get_class( $e ) . "; Message: {$e->getMessage()}; File: {$e->getFile()}; Line: {$e->getLine()};)";
	        $this->plugin->debug->log($message, WPDebug::ERROR);
	    }
	    return false;
	}

	/**
	* Checks for a fatal error, work around for set_error_handler not working on fatal errors.
	*/
	function check_for_fatal()
	{
	    $error = error_get_last();
	    if ( E_ERROR == $error["type"] )
	        $this->log_error( $error["type"], $error["message"], $error["file"], $error["line"] );
	}
}