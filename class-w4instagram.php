<?php

include_once('class-options.php');
include_once('model/class-service.php');

class W4Instagram {

	private static $instance;

	private function __construct() {

		add_action('admin_init', function() {
			Options::get_instance();
		});

		// Lägg till meny
		add_action('admin_menu', function() {
			Options::add_menu_page();
		});

		add_action('http_request_args', array($this, 'no_ssl_http_request_args'), 10, 2);

		// Lägg till skript
		add_action('admin_enqueue_scripts', array($this, 'register_scripts_and_styles_admin'));
		add_action('wp_enqueue_scripts', array($this, 'register_scripts_and_styles_frontend'));

		// Lägg till shortcode
		add_shortcode('w4_instagram', array($this, 'w4_instagram_shortcode'));
	}

	// Skapa en ny instans om det inte redan finns(singleton pattern)
	public static function get_instance() {
		if (null == self::$instance) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	// fixa ssl problem
	public function no_ssl_http_request_args($args, $url) {
	    $args['sslverify'] = false;
	    return $args;
	}

	public function register_scripts_and_styles_admin() {
		// Skript
		wp_enqueue_script('admin', plugins_url( '/js/admin.js', __FILE__ ), array('jquery'));
    	wp_enqueue_script('googlemaps', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places');
    	
    	// CSS
    	wp_enqueue_style('admin', plugins_url( '/css/admin.css', __FILE__ ));
	}

	public function register_scripts_and_styles_frontend() {
		// Skript
		wp_enqueue_script('instafeed', plugins_url('/js/instafeed.js', __FILE__), array('jquery'));
	}

	// Lägg till javascript för att kunna användas på klienten
	public function add_media_script($media, $template) {
		echo "
			<script type='text/javascript'>
				var w4media = {$media};
				var w4template = {$template};
			</script>
		";
	}

	// Shortcode med attribut
	public function w4_instagram_shortcode($atts = null, $content = null) {

		$service = new Service();
		
		$a = shortcode_atts(array(
			'hashtags' => 'false',
        	'user' => 'false',
        	'location' => 'false'
    	), $atts );

    	if ($a['hashtags'] == 'true') {

    		// Hämta hashtagdata
    		try {
    			$media = $service->get_hashtag_media();
    		} catch(Exception $e) {
    			View::error_message();
    		}
    		
    		$template = get_option('w4_instagram_display_options');

    		// Lägg till javascript för att kunna användas på klienten
    		$this->add_media_script(json_encode($media), json_encode($template));
    	}

    	if ($a['user'] == 'true') {
    		// Hämta användardata
    		try {
    			$media = $service->get_user_media();
    		} catch(Exception $e) {
    			View::error_message();
    		}
    		
    		$template = get_option('w4_instagram_display_options');

    		// Lägg till javascript för att kunna användas på klienten
    		$this->add_media_script(json_encode($media), json_encode($template));
    	}

    	if ($a['location'] == 'true') {
    		// Hämta platsdata
    		try {
    			$media = $service->get_location_media();
    		} catch(Exception $e) {
    			View::error_message();
    		}
    		
    		$template = get_option('w4_instagram_display_options');

    		// Lägg till javascript för att kunna användas på klienten
    		$this->add_media_script(json_encode($media), json_encode($template));
    	}

    	$str = "<div class='w4-instagram'></div>";

		return $str;
	}
}