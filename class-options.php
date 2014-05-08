<?php

/*
*Information here

* TODO:
*/

class Options {

	public $config_options;
	public $hashtag_options;
	public $user_options;
	public $location_options;
	public $url;

	public function __construct() {
		$this->config_options = get_option('w4_instagram_config_options');
		$this->hashtag_options = get_option('w4_instagram_hashtag_options');
		$this->user_options = get_option('w4_instagram_user_options');
		$this->location_options = get_option('w4_instagram_location_options');
		$this->url = get_bloginfo('url') . '/wp-admin/options-general.php?page=w4_instagram_options';
		$this->register_settings();
	}

	public static function add_menu_page() {
		add_options_page(
			'W4 instagram',
			'W4 instagram',
			'administrator',
			'w4_instagram_options',
			array('Options', 'display_options_page')
		);
	}

	public function display_options_page() {

		if( isset( $_GET[ 'tab' ] ) ) {
    		$active_tab = $_GET[ 'tab' ];
		}

		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'config_options';
		?>
			<div class="wrap">
				<h2>W4 instagram</h2>

				<h2 class="nav-tab-wrapper">
				    <a href="?page=w4_instagram_options&tab=config_options" class="nav-tab <?php echo $active_tab == 'config_options' ? 'nav-tab-active' : ''; ?>">Config Options</a>
    				<a href="?page=w4_instagram_options&tab=hashtag_options" class="nav-tab <?php echo $active_tab == 'hashtag_options' ? 'nav-tab-active' : ''; ?>">Hashtag Options</a>
    				<a href="?page=w4_instagram_options&tab=user_options" class="nav-tab <?php echo $active_tab == 'user_options' ? 'nav-tab-active' : ''; ?>">User Options</a>
    				<a href="?page=w4_instagram_options&tab=location_options" class="nav-tab <?php echo $active_tab == 'location_options' ? 'nav-tab-active' : ''; ?>">Location Options</a>
				</h2>
				<form method="post" action="options.php" enctype="multipart/form-data">
					<?php

					if($active_tab == 'config_options') {
			            settings_fields('config_section');
						do_settings_sections('w4_instagram_config_options');
						submit_button();
						do_settings_sections('w4_instagram_auth_options');
			        } elseif($active_tab == 'hashtag_options') {
			        	settings_fields('hashtag_section');
						do_settings_sections('w4_instagram_hashtag_options');
						submit_button();
			        } elseif($active_tab == 'user_options') {
			        	settings_fields('user_section');
						do_settings_sections('w4_instagram_user_options');
						submit_button();

						$access_token = get_option('w4_instagram_access_token');
						echo "<script type='text/javascript'>var accessToken = '{$access_token}'</script>";

						echo "<input placeholder='username' class='query' type='text'/>";
						echo "<button class='user-search'>Sök</button>";
						echo "<div id='user-con'></div>";
			        } else {
			        	settings_fields('location_section');
						do_settings_sections('w4_instagram_location_options');
						submit_button();

						$access_token = get_option('w4_instagram_access_token');
						echo "<script type='text/javascript'>var accessToken = '{$access_token}'</script>";

						echo "<input class='lat-query' placeholder='lat' type='text'/>";
						echo "<input class='lng-query' placeholder='lng' type='text'/>";
						echo "<button class='location-search'>Sök</button>";
						echo "<div id='location-con'></div>";
			        }

			        ?>
				</form>
			</div>
		<?php
	}

	public function register_settings() {

		add_settings_section(
			'hashtag_section',
			'Hashtag options',
			array($this, 'hashtag_section_cb'),
			'w4_instagram_hashtag_options'
		);

		add_settings_section(
			'config_section',
			'Configuration options',
			array($this, 'config_section_cb'),
			'w4_instagram_config_options'
		);

		add_settings_section(
			'auth_section',
			'Authorize',
			array($this, 'auth_section_cb'),
			'w4_instagram_auth_options'
		);

		add_settings_section(
			'user_section',
			'User options',
			array($this, 'user_section_cb'),
			'w4_instagram_user_options'
		);

		add_settings_section(
			'location_section',
			'Location options',
			array($this, 'location_section_cb'),
			'w4_instagram_location_options'
		);

		add_settings_field(
			'hashtags',
			'Hashtags',
			array($this, 'hashtags_setting'),
			'w4_instagram_hashtag_options',
			'hashtag_section'
		);

		add_settings_field(
			'client_id',
			'Client ID',
			array($this, 'client_id_setting'),
			'w4_instagram_config_options',
			'config_section'
		);

		add_settings_field(
			'client_secret',
			'Client secret',
			array($this, 'client_secret_setting'),
			'w4_instagram_config_options',
			'config_section'
		);

		add_settings_field(
			'auth',
			'Authorize',
			array($this, 'auth_setting'),
			'w4_instagram_auth_options',
			'auth_section'
		);

		add_settings_field(
			'user_id',
			'User ID',
			array($this, 'user_id_setting'),
			'w4_instagram_user_options',
			'user_section'
		);

		add_settings_field(
			'location_id',
			'Location ID',
			array($this, 'location_id_setting'),
			'w4_instagram_location_options',
			'location_section'
		);

		register_setting(
			'hashtag_section',
			'w4_instagram_hashtag_options'
		);

		register_setting(
			'config_section',
			'w4_instagram_config_options'
		);

		register_setting(
			'user_section',
			'w4_instagram_user_options'
		);

		register_setting(
			'location_section',
			'w4_instagram_location_options'
		);
	}

	public function config_section_cb() {
	}

	public function hashtag_section_cb() {
	}

	public function auth_section_cb() {
	}

	public function user_section_cb() {
		echo "<p>Search for a username, then choose the user you want to add, click save.</p>";
	}

	public function location_section_cb() {
		echo "<p>Search for lat and long, then choose the location you want to add, click save.</p>";
	}

	/* -------- Fields -------- */
	
	// Client ID
	public function client_id_setting() {
		echo "<input class='regular-text' name='w4_instagram_config_options[client_id]' type='text' value='{$this->config_options['client_id']}' />";
		echo "<p class='description'>Provide your application client id.</p>";
	}

	// Client secret
	public function client_secret_setting() {
		echo "<input class='regular-text' name='w4_instagram_config_options[client_secret]' type='text' value='{$this->config_options['client_secret']}' />";
	}

	// Hashtags
	public function hashtags_setting() {
		echo "<input class='regular-text' name='w4_instagram_hashtag_options[hashtags]' type='text' value='{$this->hashtag_options['hashtags']}' />";
		echo "<p class='description'>Provide hashtags separated by commas.</p>";
	}

	// Authorize link
	public function auth_setting() {

		if ($_GET['code']) {
			$args = array(
				'body' => array(
					'client_id' => $this->config_options['client_id'],
	  				'client_secret' => $this->config_options['client_secret'],
	  				'grant_type' => 'authorization_code',
	  				'redirect_uri' => $this->url,
	  				'code' => $_GET['code']
				)
			);
			$response = wp_remote_post('https://api.instagram.com/oauth/access_token', $args);

			if ( is_wp_error( $response ) ) {
			   	// $error_message = $response->get_error_message();
			   	// Felmeddelande
			   	echo 'Ett fel inträffade';
			} else {
				$body = json_decode($response['body']);

				if ($body->code == '400') {
					echo $body->error_message;
				}

				// Flytta till sektion om möjligt
			   	update_option('w4_instagram_access_token', $body->access_token);
			   	update_option('w4_instagram_username', $body->user->username);
			   	update_option('w4_instagram_user_id', $body->user->id);
			}
		}

		if (get_option('w4_instagram_access_token') != '') {
			echo "<p><strong>Username:</strong> " . get_option('w4_instagram_username') . "</p>";
			echo "<p><strong>User ID:</strong> " . get_option('w4_instagram_user_id') . "</p>";

		} else {
			echo "<a href='https://instagram.com/oauth/authorize/?client_id={$this->config_options['client_id']}&redirect_uri={$this->url}&response_type=code'>Login</a>";
		}
	}

	// User ID
	public function user_id_setting() {
		echo "<input class='regular-text user-id-setting' name='w4_instagram_user_options[user_id]' type='text' value='{$this->user_options['user_id']}' />";
	}

	// Location ID
	public function location_id_setting() {
		echo "<input class='regular-text location-id-setting' name='w4_instagram_location_options[location_id]' type='text' value='{$this->location_options['location_id']}' />";
	}
}










