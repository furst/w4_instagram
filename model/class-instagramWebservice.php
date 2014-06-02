<?php

class InstagramWebservice {

	private $count = 10;

	public function __construct() {
		add_action('http_request_args', array($this, 'no_ssl_http_request_args'), 10, 2);
		$this->count = get_option('w4_instagram_display_options')['count'];
	}

	public function getUser($userId, $accessToken) {

        $result = wp_remote_get("https://api.instagram.com/v1/users/$userId/media/recent/?access_token=$accessToken&count={$this->count}");

        return $this->getResults($result, 'user');
	}

	public function getTag($tagName, $accessToken) {

        $result = wp_remote_get("https://api.instagram.com/v1/tags/$tagName/media/recent/?access_token=$accessToken&count={$this->count}");

        return $this->getResults($result, 'hashtag', $tagName);
	}

	private function getResults($result, $sorting, $tagName = '') {

		$model = '';

		if ( is_wp_error( $result ) ) {
            $error_message = $result->get_error_message();
            $model = "Something went wrong: $error_message";
        } else {
            $result = json_decode($result['body']);
            $model = array();
            $n = 0;
 
            foreach ($result->data as $d) {
                $model[$n]['username'] = $d->user->username;
                $model[$n]['thumbnail'] = $d->images->thumbnail->url;
                $model[$n]['image'] = $d->images->standard_resolution->url;
                $model[$n]['caption'] = ($d->caption->text == 'null' ? 'true' : 'false');
				$model[$n]['created'] = $d->created_time;
				$model[$n]['link'] = $d->link;
				$model[$n]['sorting'] = $sorting;
				if (!empty($tagName)) {
					$model[$n]['tagName'] = $tagName;
				}
                $n++;
            }
        }
 
        return $model;
	}

	public function no_ssl_http_request_args($args, $url) {
		$args['sslverify'] = false;
        return $args;
	}
}