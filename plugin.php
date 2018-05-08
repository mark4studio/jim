<?php
/**
 * @wordpress-plugin
 * Plugin Name:       JIM Framework example
 * Description:       Demo plugin for JIM Frmework
 * Version:           1.0.0
 * Author:            Naveen Ram
 * Author URI:        http://fiverr.com/naveenram
 * License:           GNU General Public License
 */
require('vendor/mark4tech/autoload.php');
use mark4tech\v2_1\Jim;

Jim::loadPlugin(dirname(__FILE__));