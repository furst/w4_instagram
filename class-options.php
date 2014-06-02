<?php

/*
*Information here

* TODO: Set default options values
*/

class Options {

	public $config_options;
	public $hashtag_options;
	public $user_options;
	public $location_options;
	public $display_options;
	public $url;

	public function __construct() {
		$this->config_options = get_option('w4_instagram_config_options');
		$this->hashtag_options = get_option('w4_instagram_hashtag_options');
		$this->user_options = get_option('w4_instagram_user_options');
		$this->location_options = get_option('w4_instagram_location_options');
		$this->display_options = get_option('w4_instagram_display_options');
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
    				<a href="?page=w4_instagram_options&tab=display_options" class="nav-tab <?php echo $active_tab == 'display_options' ? 'nav-tab-active' : ''; ?>">Display Options</a>
				</h2>
				<form method="post" action="options.php" enctype="multipart/form-data">
					<?php

					if($active_tab == 'config_options') {

						$website = get_bloginfo('url');
						$redirect_uri = get_bloginfo('url') . '/wp-admin/options-general.php?page=w4_instagram_options';

						echo 
						"
						<h3>Instructions</h3>
						<a class='info-toggle' href='#'>Toogle instructions</a>
						<div class='hide'>
							<ol>
								<li>Create a developer account at <a target='_blank' href='http://instagram.com/developer/clients/manage/'>instagram</a></li>
								<li>Create a new client with the options listed below</li>
								<li>Use the information about the client to fill in the fields below, save</li>
								<li>Click Authorize</li>
								<li></li>
							</ol>

							<p>
								<strong>Name:</strong> your-application-name<br>
								<strong>Website:</strong> $website<br>
								<strong>OAuth redirect_uri:</strong> $redirect_uri<br>
							</p>
						</div>
						";

			            settings_fields('config_section');
						do_settings_sections('w4_instagram_config_options');
						submit_button();
						do_settings_sections('w4_instagram_auth_options');
			        } elseif($active_tab == 'hashtag_options') {
			        	settings_fields('hashtag_section');
						do_settings_sections('w4_instagram_hashtag_options');
						echo "
			        		<table class='form-table permalink-structure'>
			        			<tbody>
			        				<tr>
			        					<th>
			        					<label>
			        						Shortcode
			        					</label>
			        					</th>
			        					<td>
			        					<code>[w4_instagram hashtags=true]</code>
			        					</td>
			        				</tr>
			        			</tbody>
			        		</table>
			        	";
						submit_button();
			        } elseif($active_tab == 'user_options') {
			        	settings_fields('user_section');
						do_settings_sections('w4_instagram_user_options');
						echo "
			        		<table class='form-table permalink-structure'>
			        			<tbody>
			        				<tr>
			        					<th>
			        					<label>
			        						Shortcode
			        					</label>
			        					</th>
			        					<td>
			        					<code>[w4_instagram user=true]</code>
			        					</td>
			        				</tr>
			        			</tbody>
			        		</table>
			        	";
						submit_button();

						$access_token = get_option('w4_instagram_access_token');
						echo "<script type='text/javascript'>var accessToken = '{$access_token}'</script>";
			        } elseif($active_tab == 'location_options') {
			        	settings_fields('location_section');
						do_settings_sections('w4_instagram_location_options');
						submit_button();

						$access_token = get_option('w4_instagram_access_token');
						echo "<script type='text/javascript'>var accessToken = '{$access_token}'</script>";

						echo "<div class='get-locations'><a href='#'>Get locations</a></div>";

						echo "<input id='pac-input' class='controls' type='text' placeholder='Search place'>";
						echo "<div id='map-canvas' style='height:500px; width:100%;'></div>";
			        } else {
			        	settings_fields('display_section');
						do_settings_sections('w4_instagram_display_options');
						submit_button();
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
			'username',
			'Username',
			array($this, 'username_setting'),
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

		add_settings_field(
			'location_name',
			'Location name',
			array($this, 'location_name_setting'),
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

		// Display settings

		add_settings_section(
			'display_section',
			'Display options',
			array($this, 'display_section_cb'),
			'w4_instagram_display_options'
		);

		register_setting(
			'display_section',
			'w4_instagram_display_options'
		);

		add_settings_field(
			'template',
			'Template',
			array($this, 'template_setting'),
			'w4_instagram_display_options',
			'display_section'
		);

		add_settings_field(
			'count',
			'Count',
			array($this, 'count_setting'),
			'w4_instagram_display_options',
			'display_section'
		);
	}

	public function config_section_cb() {
	}

	public function hashtag_section_cb() {
	}

	public function auth_section_cb() {
	}

	public function user_section_cb() {
	}

	public function location_section_cb() {
		echo "<p>Search for a place, then click on the pin to add the place</p>";
	}

	public function display_section_cb() {
	}

	/* -------- Fields -------- */
	
	// Client ID
	public function client_id_setting() {
		echo "<input class='regular-text' name='w4_instagram_config_options[client_id]' type='text' value='{$this->config_options['client_id']}' />";
		echo "<p class='description'>Provide your application client id</p>";
	}

	// Client secret
	public function client_secret_setting() {
		echo "<input class='regular-text' name='w4_instagram_config_options[client_secret]' type='text' value='{$this->config_options['client_secret']}' />";
		echo "<p class='description'>Provide your application client secret</p>";
	}

	// Hashtags
	public function hashtags_setting() {
		echo "<input class='regular-text' name='w4_instagram_hashtag_options[hashtags]' type='text' value='{$this->hashtag_options['hashtags']}' />";
		echo "<p class='description'>Provide hashtags separated by commas</p>";
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

	// Username
	public function username_setting() {
		echo "<div class='loader-field'><input class='username-setting regular-text' autocomplete='off' name='w4_instagram_user_options[username]' type='text' value='{$this->user_options['username']}' /></div>";
		echo "<div id='user-list'></div>";
		echo "<p class='description'>Search for a username</p>";
	}

	// User ID
	public function user_id_setting() {
		echo "<input class='user-id-setting' name='w4_instagram_user_options[user_id]' type='hidden' value='{$this->user_options['user_id']}' />";
	}

	// Location name
	public function location_name_setting() {
		echo "<input class='regular-text location-name-setting' name='w4_instagram_location_options[location_name]' type='text' value='{$this->location_options['location_name']}' />";
	}

	// Location ID
	public function location_id_setting() {
		echo "<input class='regular-text location-id-setting' name='w4_instagram_location_options[location_id]' type='hidden' value='{$this->location_options['location_id']}' />";
	}

	// Template
	public function template_setting() {
		$options = get_option('w4_instagram_display_options');
     
	    $html = '<fieldset><p><label><input type="radio" id="template_one" name="w4_instagram_display_options[template]" value="1"' . checked( 1, $options['template'], false ) . '/>Standard</label></p>';
	     
	    $html .= '<p><label><input type="radio" id="template_two" name="w4_instagram_display_options[template]" value="2"' . checked( 2, $options['template'], false ) . '/>Small</label></p></fieldset>';

	    $html .= '<p><label><input type="radio" id="template_two" name="w4_instagram_display_options[template]" value="3"' . checked( 3, $options['template'], false ) . '/>Custom</label></p></fieldset>';
	     
	    echo $html;
	}

	// Count
	public function count_setting() {
		echo "<input class='small-text count-setting' name='w4_instagram_display_options[count]' type='number' value='{$this->display_options['count']}' />";
	}
}










