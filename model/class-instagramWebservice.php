<?php

class InstagramWebservice {

	private $count = 10;

	public function __construct() {
		add_action('http_request_args', array($this, 'no_ssl_http_request_args'), 10, 2);
		$display_options = Settings::display_options();
		$this->count = $display_options['count'];
	}

	// Konstruera user endpoint
	public function get_user($user_id, $access_token) {

        $result = wp_remote_get(Settings::endpoint() . "users/$user_id/media/recent/?access_token=$access_token&count={$this->count}");

        return $this->get_results($result, 'user');
	}

	// Konstruera tag endpoint
	public function get_tag($tag_name, $access_token) {

        $result = wp_remote_get(Settings::endpoint() . "tags/$tag_name/media/recent/?access_token=$access_token&count={$this->count}");

        return $this->get_results($result, 'hashtag', $tag_name);
	}

	// Konstruera location endpoint
	public function get_location($location, $access_token) {

		$distance = $location['distance'];
		$coords = $location['coords'];

		// Separera kordinater
		$coords = str_replace(' ', '', $coords);
		$coords = explode(',', $coords);

		$lat = $coords[0];
		$lng = $coords[1];
		
        $result = wp_remote_get(Settings::endpoint() . "media/search/?access_token=$access_token&count={$this->count}&distance={$distance}&lat={$lat}&lng={$lng}");

        return $this->get_results($result, 'location');
	}

	private function get_results($result, $sorting, $tag_name = '') {

		$model = '';

		if ( is_wp_error( $result ) ) {
            $error_message = $result->get_error_message();
            $model = "Something went wrong: $error_message";
        } else {
            $result = json_decode($result['body']);
            $model = array();
            $n = 0;
 			
 			// Sätt ihop array med data
 			if ($result->data) {
 				foreach ($result->data as $d) {

	                $model[$n]['username'] = $d->user->username;
	                $model[$n]['thumbnail'] = $d->images->thumbnail->url;
	                $model[$n]['image'] = $d->images->standard_resolution->url;
	                if ($d->caption->text === NULL) {
	            		$model[$n]['caption'] = '';
	            	} else {
	            		$model[$n]['caption'] = $this->remove_emoji($d->caption->text);
	            	}
					$model[$n]['created'] = $d->created_time;
					$model[$n]['link'] = $d->link;
					$model[$n]['sorting'] = $sorting;
					if (!empty($tag_name)) {
						$model[$n]['tagName'] = $tag_name;
					}
	                $n++;
	            }
 			} else {
		    	throw new InvalidArgumentException();
			}
            
        }
 
        return $model;
	}

	// Ta bort emoticons etc.
	private function remove_emoji($text) {

	    $clean_text = "";

	    $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
	    $clean_text = preg_replace($regexEmoticons, '', $text);

	    $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
	    $clean_text = preg_replace($regexSymbols, '', $clean_text);

	    $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
	    $clean_text = preg_replace($regexTransport, '', $clean_text);

	    return $clean_text;
	}

	// fixa ssl problem
	public function no_ssl_http_request_args($args, $url) {
		$args['sslverify'] = false;
        return $args;
	}
}