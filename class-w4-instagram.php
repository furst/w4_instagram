<?php

/*
*Information here

* TODO:
*/

include_once('class-options.php');
include_once('model/class-service.php');

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
		add_action('wp_footer', array($this, 'enqueue_scripts_and_styles'));

		add_action('admin_enqueue_scripts', array($this, 'admin_js'));

		add_shortcode('w4_instagram_hashtags', array($this, 'w4_instagram_hashtags_shortcode'));
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

	public function add_media_script($media, $template) {
		echo "
			<script type='text/javascript'>
				var w4media = {$media};
				var w4template = {$template};
			</script>
		";
	}

	public function enqueue_scripts_and_styles() {
		wp_enqueue_script('instafeed');
		wp_enqueue_script('w4-instagram');
	}

	public function w4_instagram_shortcode($atts = null, $content = null) {

		$service = new Service();
		
		$a = shortcode_atts(array(
			'hashtags' => 'false',
        	'user' => 'false',
        	'location' => 'false'
    	), $atts );

    	$str = '';

    	if ($a['hashtags'] == 'true') {

    		$media = $service->getHashtagMedia();
    		$template = get_option('w4_instagram_display_options');

    		$str .= "<div class='w4-instagram'></div>";

    		$this->add_media_script(json_encode($media), json_encode($template));
    	}

    	if ($a['user'] == 'true') {
    		$media = $service->getUserMedia();
    		$template = get_option('w4_instagram_display_options');

    		$str .= "<div class='w4-instagram'></div>";

    		$this->add_media_script(json_encode($media), json_encode($template));
    	}

    	if ($a['location'] == 'true') {
    		
    	}

		return $str;
	}

	public function admin_js(){

		if (!is_admin()) {
    		wp_enqueue_script('instafeed', plugins_url( '/js/instafeed.js', __FILE__ ));
    	}
    	
    	wp_enqueue_script('admin', plugins_url( '/js/admin.js', __FILE__ ), array('jquery'));
    	
    	wp_enqueue_script('googlemaps',	'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places');
    	

    	//fixa
    	wp_enqueue_style('admin', plugins_url( '/css/admin.css', __FILE__ ));
	}
}