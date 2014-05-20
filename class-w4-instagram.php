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

		add_action('http_request_args', array($this, 'no_ssl_http_request_args'), 10, 2);

		add_action('init', array($this, 'register_scripts_and_styles'));
		add_action('wp_head', array($this, 'add_scripts_and_styles'));
		add_action('wp_footer', array($this, 'enqueue_scripts_and_styles'));

		add_action('admin_enqueue_scripts', array($this, 'admin_js'));

		add_shortcode('w4_instagram', array($this, 'w4_instagram_shortcode'));
	}

	public static function get_instance() {
		if (null == self::$instance) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	// fix SSL request error
	function no_ssl_http_request_args($args, $url) {
	    $args['sslverify'] = false;
	    return $args;
	}

	function register_scripts_and_styles() {
		if (!is_admin()) {
			wp_register_script('instafeed', plugins_url('w4_instagram/js/instafeed.js', dirname(__FILE__)));
			wp_register_script('w4-instagram', plugins_url('w4_instagram/js/w4-instagram.js', dirname(__FILE__)));
		}
	}

	public function add_scripts_and_styles() {
		$access_token = get_option('w4_instagram_access_token');

		echo "<script type='text/javascript'>var accessToken = '{$access_token}'</script>";
	}

	public function enqueue_scripts_and_styles() {
		wp_enqueue_script('instafeed');
		wp_enqueue_script('w4-instagram');
	}

	public function w4_instagram_shortcode($atts = null, $content = null) {
		$str = '';
		$access_token = get_option('w4_instagram_access_token');
		$options = get_option('w4_instagram_hashtag_options');

		//test
		$optionstest = get_option('w4_instagram_user_options');

		var_dump($optionstest);

		$hashtags = str_replace('#', '', $options['hashtags']);
		$hashtags = str_replace(' ', '', $hashtags);
		$hashtags = explode(',', $hashtags);

		$hashtag = $hashtags[0];

		$str .= "<ul>";

		foreach ($hashtags as $hashtag) {
    		$str .=
    		"<li>
				<a class='hashtag' href='#'>$hashtag</a>
			</li>";
		}

		$str .= "</ul><div id='instagram'></div>";

		return $str;
	}

	public function admin_js(){
    	wp_enqueue_script('admin', plugins_url( '/js/admin.js', __FILE__ ), array('jquery'));
    	wp_enqueue_script('instafeed', plugins_url( '/js/instafeed.js', __FILE__ ));
    	wp_enqueue_script('googlemaps',	'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places');
    	

    	//fixa
    	wp_enqueue_style('admin', plugins_url( '/css/admin.css', __FILE__ ));
	}
}