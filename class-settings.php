<?php

Class Settings {

	public static function website_url() {
		return get_bloginfo('url');
	}

	public static function instagram_redirect_uri() {
		return get_bloginfo('url') . '/wp-admin/options-general.php?page=w4_instagram_options';
	}

	public static function config_options() {
		return get_option('w4_instagram_config_options');
	}

	public static function hashtag_options() {
		return get_option('w4_instagram_hashtag_options');
	}

	public static function user_options() {
		return get_option('w4_instagram_user_options');
	}

	public static function location_options() {
		return get_option('w4_instagram_location_options');
	}

	public static function display_options() {
		return get_option('w4_instagram_display_options');
	}

	public static function auth_options() {
		return get_option('w4_instagram_auth');
	}

	public static function media() {
		return get_option('w4_instagram_media');
	}

	public static function next_update() {
		return get_option('w4_instagram_next_update');
	}

	public static function access_token() {
		$auth_options = self::auth_options();
		return $auth_options['access_token'];
	}

	public static function endpoint() {
		return "https://api.instagram.com/v1/";
	}

	// Kolla om admin är autentiserad
	public static function auth() {
		$auth_options = self::auth_options();
		if ($auth_options['access_token'] != '') {
			return true;
		}
		return false;
	}
}