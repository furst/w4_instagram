<?php
/*

@wordpress-plugin
Plugin Name: W4 Instagram
Plugin URI: http://andreasfurst.se
Description: En instagram-plugin till W4
Version: 0.1
Author: Andreas Fürst
Author URI: http://andreasfurst.se
License: GPL-2.0+
License URI: www.google.se
*/

// If this file is called directly, abort
if(!defined('WPINC')) {
	die;
}

include_once('class-w4-instagram.php');

W4_instagram::get_instance();