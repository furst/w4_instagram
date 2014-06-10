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

// Om denna fil kallas direkt, avbryt
if(!defined('WPINC')) {
	die;
}

include_once('class-w4instagram.php');

// Kör plugin(singleton)
W4Instagram::get_instance();

// Körs när pluginen aktiveras
register_activation_hook( __FILE__, array( 'Options', 'set_default_options' ) );