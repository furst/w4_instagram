<?php

include_once('class-instagramWebservice.php');

class Service {

	private $accessToken;
	private $media;
	private $nextUpdate;

	public function __construct() {
		$this->media = get_option('w4_instagram_media');
		$this->nextUpdate = get_option('w4_instagram_next_update');
		$this->accessToken = get_option('w4_instagram_access_token');
	}

	public function getUserMedia() {
		if (empty($this->media) || $this->nextUpdate < time()) {
			$webservice = new InstagramWebservice();

			$user = get_option('w4_instagram_user_options');
			$this->media = $webservice->getUser($user['user_id'], $this->accessToken);

			update_option('w4_instagram_media', $this->media);
			update_option('w4_instagram_next_update', time() + 300);
		}

		return $this->media;
	}

	public function getHashtagMedia() {
		if (empty($this->media) || $this->nextUpdate < time()) {
			$webservice = new InstagramWebservice();

			$hashtags = get_option('w4_instagram_hashtag_options');

			$hashtags = str_replace('#', '', $hashtags['hashtags']);
			$hashtags = str_replace(' ', '', $hashtags);
			$hashtags = explode(',', $hashtags);

			$mediaCollection = array();

			foreach ($hashtags as $hashtag) {
				$mediaCollection[] = $webservice->getTag($hashtag, $this->accessToken);
			}

			$n = 0;
			$this->media = '';

			foreach ($mediaCollection as $media) {
    			foreach ($mediaCollection[$n] as $data) {
                	$this->media[] = $data;
            	}
    			$n++;
    		}

			update_option('w4_instagram_media', $this->media);
			update_option('w4_instagram_next_update', time() + 300);
		}

		return $this->media;
	}

	public function aasort($array, $key) {
	    $sorter = array();
	    $ret = array();
	    reset($array);
	    foreach ($array as $ii => $va) {
	        $sorter[$ii]=$va[$key];
	    }
	    asort($sorter);
	    foreach ($sorter as $ii => $va) {
	        $ret[$ii]=$array[$ii];
	    }
	    $array = $ret;
	}
}

