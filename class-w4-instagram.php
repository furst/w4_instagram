<?php

/*
*Information here

* TODO:
*/

include_once('class-options.php');

class W4_instagram {

	private static $instance;

	private function __construct() {
		add_action('admin_menu', function() {
			Options::add_menu_page();
		});

		add_action('admin_init', function() {
			new Options();
		});
	}

	public static function get_instance() {
		if (null == self::$instance) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}