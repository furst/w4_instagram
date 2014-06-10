<?php

include_once('class-instagramWebservice.php');

class Service {

	private $access_token;
	private $media;
	private $next_update;

	public function __construct() {
		$this->media = Settings::media();
		$display_options = Settings::display_options();
		$this->cache = $display_options['cache'] * 60;
		$this->next_update = Settings::next_update();
		$this->access_token = Settings::access_token();
	}

	// Hämta media, om cachetid har passerat så hämta ny data från api
	public function get_user_media() {
		if (empty($this->media) || $this->next_update > time()) {
			$webservice = new InstagramWebservice();

			$user = Settings::user_options();
			try {
				$this->media = $webservice->get_user($user['user_id'], $this->access_token);
			} catch(Exception $e) {
				throw new InvalidArgumentException('Media could not be loaded');
			}
			
			update_option('w4_instagram_media', $this->media);
			update_option('w4_instagram_next_update', time() + $this->cache);
		}

		return $this->media;
	}

	// Hämta media, om cachetid har passerat så hämta ny data från api
	public function get_hashtag_media() {
		if (empty($this->media) || $this->next_update > time()) {
			$webservice = new InstagramWebservice();

			$hashtags = Settings::hashtag_options();

			// Separera hashtags
			$hashtags = str_replace('#', '', $hashtags['hashtags']);
			$hashtags = str_replace(' ', '', $hashtags);
			$hashtags = explode(',', $hashtags);

			$media_collection = array();

			// Hämta media på alla hashtags
			foreach ($hashtags as $hashtag) {
				try {
					$media_collection[] = $webservice->get_tag($hashtag, $this->access_token);
				} catch(Exception $e) {
					throw new InvalidArgumentException('Media could not be loaded');
				}
			}

			$n = 0;
			// Töm media
			$this->media = '';

			foreach ($media_collection as $media) {
    			foreach ($media_collection[$n] as $data) {
                	$this->media[] = $data;
            	}
    			$n++;
    		}

			update_option('w4_instagram_media', $this->media);
			update_option('w4_instagram_next_update', time() + $this->cache);
		}

		return $this->media;
	}

	// Hämta media, om cachetid har passerat så hämta ny data från api
	public function get_location_media() {
		if (empty($this->media) || $this->next_update > time()) {
			$webservice = new InstagramWebservice();

			$location = Settings::location_options();
			try {
				$this->media = $webservice->get_location($location, $this->access_token);
			} catch(Exception $e) {
				throw new InvalidArgumentException('Media could not be loaded');
			}

			update_option('w4_instagram_media', $this->media);
			update_option('w4_instagram_next_update', time() + $this->cache);

		}

		return $this->media;
	}
}