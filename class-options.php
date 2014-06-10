<?php

include_once('class-view.php');

class Options {

	private static $instance;

	private function __construct() {
		// Registrera inställningar
		$this->register_settings();
	}

	// Sätt förinställningar, kallas första gången pluginen aktiveras
	public static function set_default_options() {
		add_option('w4_instagram_location_options', array('distance' => '5000'));
		add_option('w4_instagram_display_options', array('template' => '1', 'count' => '20', 'cache' => '5'));
	}

	// Skapa en ny instans om det inte redan finns(singleton pattern)
	public static function get_instance() {
		if (null == self::$instance) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	// Skapa inställnings-sida
	public static function add_menu_page() {
		add_options_page(
			'W4 instagram',
			'W4 instagram',
			'administrator',
			'w4_instagram_options',
			array('Options', 'display_options_page')
		);
	}

	// Visa inställingar-sida
	public function display_options_page() {

		if( isset( $_GET[ 'tab' ] ) ) {
    		$active_tab = $_GET[ 'tab' ];
		}

		// Använd tabbar och visa innehåll beroende på vilken tabb som är satt
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'config_options';
		?>
			<div class="wrap">
				<h2>W4 instagram</h2>

				<?php View::tabs($active_tab); ?>

				<form method="post" action="options.php" enctype="multipart/form-data">
					<?php

					if($active_tab == 'config_options') {

						View::instructions();

						// Visa fält och sektioner
			            settings_fields('config_section');
						do_settings_sections('w4_instagram_config_options');
						submit_button();
						do_settings_sections('w4_instagram_auth_options');

			        } elseif($active_tab == 'hashtag_options') {

			        	View::is_authenticated();
			        	settings_fields('hashtag_section');
						do_settings_sections('w4_instagram_hashtag_options');

						View::hashtag_shortcode();

						submit_button();
			        } elseif($active_tab == 'user_options') {

			        	View::is_authenticated();
			        	settings_fields('user_section');
						do_settings_sections('w4_instagram_user_options');

						View::access_token();
						View::user_shortcode();

						submit_button();
			        } elseif($active_tab == 'location_options') {

			        	View::is_authenticated();
			        	settings_fields('location_section');
						do_settings_sections('w4_instagram_location_options');
						View::location_shortcode();
						submit_button();

						View::access_token();
						View::map_canvas();
			        } else {
			        	View::is_authenticated();
			        	settings_fields('display_section');
						do_settings_sections('w4_instagram_display_options');
						View::trigger();
						submit_button();
			        }

			        ?>
				</form>
			</div>
		<?php
	}

	/* -------------------------------------------------------------- */
	/* Sektioner
	/* -------------------------------------------------------------- */


	public function config_section_cb() {
	}

	public function hashtag_section_cb() {
	}

	public function auth_section_cb() {
	}

	public function user_section_cb() {
	}

	public function location_section_cb() {
		echo "<p>Search for a place, choose radius and click 'search photos'. Click 'Add location' and then save</p>";
	}

	public function display_section_cb() {
	}

	/* -------------------------------------------------------------- */
	/* Validering
	/* -------------------------------------------------------------- */

	public function validate($input) {

    	$output = array();
	    // Kör igenom inställningarna
	    foreach( $input as $key => $value ) {
	        // Om värde är satt så kör igenom
	        if( isset( $input[$key] ) ) {
	            // Ta bort taggar
	            $output[$key] = strip_tags( stripslashes( $input[ $key ] ) );
	        }
	    }
	    return apply_filters( 'sandbox_theme_validate_input_examples', $output, $input );
	}

	/* -------------------------------------------------------------- */
	/* Fields
	/* -------------------------------------------------------------- */
	
	// Client ID
	public function client_id_setting() {
		$config_options = Settings::config_options();
		echo "<input class='regular-text' name='w4_instagram_config_options[client_id]' type='text' value='{$config_options['client_id']}' />";
		echo "<p class='description'>Provide your application client id</p>";
	}

	// Client secret
	public function client_secret_setting() {
		$config_options = Settings::config_options();
		echo "<input class='regular-text' name='w4_instagram_config_options[client_secret]' type='text' value='{$config_options['client_secret']}' />";
		echo "<p class='description'>Provide your application client secret</p>";
	}

	// Hashtags
	public function hashtags_setting() {
		$hashtag_options = Settings::hashtag_options();
		echo "<input class='regular-text' name='w4_instagram_hashtag_options[hashtags]' type='text' value='{$hashtag_options['hashtags']}' />";
		echo "<p class='description'>Provide hashtags separated by commas</p>";
	}

	// Authorize link
	public function auth_setting() {

		$url = Settings::instagram_redirect_uri();
		$config_options = Settings::config_options();

		// Vid borttagning av användare
		if ($_GET['logout']) {
			update_option('w4_instagram_auth', array());
		}

		// Autentisering
		if ($_GET['code']) {

			// Posta data till instagram för att få tillbaka access token
			$args = array(
				'body' => array(
					'client_id' => $config_options['client_id'],
	  				'client_secret' => $config_options['client_secret'],
	  				'grant_type' => 'authorization_code',
	  				'redirect_uri' => $url,
	  				'code' => $_GET['code']
				)
			);
			$response = wp_remote_post('https://api.instagram.com/oauth/access_token', $args);

			if (is_wp_error( $response)) {
			   	$error_message = $response->get_error_message();
			   	echo "An error occured: $error_message";
			} else {
				$body = json_decode($response['body']);

				if ($body->code == '400') {
					echo $body->error_message;
				}

				// Om allt gick bra så spara användare och token
			   	update_option('w4_instagram_auth', array('access_token' => $body->access_token, 'username' => $body->user->username, 'user_id' => $body->user->id));
			}
		}

		// Visa användare om inloggad
		if (Settings::auth()) {
			$auth_options = Settings::auth_options();
			echo "<p><strong>Username:</strong> " . $auth_options['username'] . "</p>";
			echo "<p><strong>User ID:</strong> " . $auth_options['user_id'] . "</p>";
			echo "<p><a href='?page=w4_instagram_options&logout=true'>Logout</a></p>";
		} else {
			echo "<a href='https://instagram.com/oauth/authorize/?client_id={$config_options['client_id']}&redirect_uri={$url}&response_type=code'>Login</a>";
		}
	}

	// Username
	public function username_setting() {
		$user_options = Settings::user_options();
		echo "<input class='user-id-setting' name='w4_instagram_user_options[user_id]' type='hidden' value='{$user_options['user_id']}' />";
		echo "<div class='w4-instagram-error'></div>";
		echo "<div class='loader-field'><input class='username-setting regular-text' autocomplete='off' name='w4_instagram_user_options[username]' type='text' value='{$user_options['username']}' /></div>";
		echo "<div id='user-list'></div>";
		echo "<p class='description'>Search for a username and choose from the dropdown</p>";
	}

	// Location name
	public function location_name_setting() {
		$location_options = Settings::location_options();
		echo "<input class='regular-text location-name-setting' name='w4_instagram_location_options[location_name]' type='text' placeholder='optional' value='{$location_options['location_name']}' />";
	}

	// Location coords
	public function location_coords_setting() {
		$location_options = Settings::location_options();
		echo "<input class='regular-text location-coords-setting' name='w4_instagram_location_options[coords]' type='text' value='{$location_options['coords']}' />";
	}

	// Location distance
	public function location_distance_setting() {
		$location_options = Settings::location_options();
		echo "<input class='regular-text location-distance-setting' name='w4_instagram_location_options[distance]' type='text' value='{$location_options['distance']}' />";
	}

	// Template
	public function template_setting() {
		$display_options = Settings::display_options();
     
	    $html = '<fieldset><p><label><input type="radio" id="template_one" name="w4_instagram_display_options[template]" value="1"' . checked( 1, $display_options['template'], false ) . '/>Standard</label></p>';
	     
	    $html .= '<p><label><input type="radio" id="template_two" name="w4_instagram_display_options[template]" value="2"' . checked( 2, $display_options['template'], false ) . '/>Small</label></p></fieldset>';

	    $html .= '<p><label><input type="radio" id="template_two" name="w4_instagram_display_options[template]" value="3"' . checked( 3, $display_options['template'], false ) . '/>Custom <textarea class="w4-instagram-textarea" name="w4_instagram_display_options[custom]">' . esc_html($display_options['custom']) . '</textarea></label></p></fieldset>';
	     
	    echo $html;

	    echo "<p class='description'>Custom template: use regular html and insert the {{}} tag where you want it.<br>
			Supported tags are: {{image}}, {{thumbnail}}, {{caption}}, {{username}}, {{href}} and {{created}}
	    </p>";
	}

	// Count
	public function count_setting() {
		$display_options = Settings::display_options();
		echo "<input class='small-text count-setting' name='w4_instagram_display_options[count]' type='number' min='1' max='40' value='{$display_options['count']}' />";
		echo "<p class='description'>Specify how many images to fetch</p>";
	}

	// Cache
	public function cache_setting() {
		$display_options = Settings::display_options();
		echo "<input class='small-text cache-setting' name='w4_instagram_display_options[cache]' type='number' min='5' value='{$display_options['cache']}' />";
		echo "<p class='description'>Specify interval in minutes to fetch new images</p>";
	}

	/* -------------------------------------------------------------- */
	/* Settings
	/* -------------------------------------------------------------- */

	public function register_settings() {

		// Auth options

		add_settings_section(
			'auth_section',
			'Authorize',
			array($this, 'auth_section_cb'),
			'w4_instagram_auth_options'
		);

		add_settings_field(
			'auth',
			'Authorize',
			array($this, 'auth_setting'),
			'w4_instagram_auth_options',
			'auth_section'
		);

		// Hashtag options

		add_settings_section(
			'hashtag_section',
			'Hashtag options',
			array($this, 'hashtag_section_cb'),
			'w4_instagram_hashtag_options'
		);

		add_settings_field(
			'hashtags',
			'Hashtags',
			array($this, 'hashtags_setting'),
			'w4_instagram_hashtag_options',
			'hashtag_section'
		);

		register_setting(
			'hashtag_section',
			'w4_instagram_hashtag_options'
		);

		// Config options

		add_settings_section(
			'config_section',
			'Configuration options',
			array($this, 'config_section_cb'),
			'w4_instagram_config_options'
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

		register_setting(
			'config_section',
			'w4_instagram_config_options'
		);

		// User options

		add_settings_section(
			'user_section',
			'User options',
			array($this, 'user_section_cb'),
			'w4_instagram_user_options'
		);

		add_settings_field(
			'username',
			'Username',
			array($this, 'username_setting'),
			'w4_instagram_user_options',
			'user_section'
		);
		
		register_setting(
			'user_section',
			'w4_instagram_user_options',
			array($this, 'validate')
		);

		// Location settings

		add_settings_section(
			'location_section',
			'Location options',
			array($this, 'location_section_cb'),
			'w4_instagram_location_options'
		);

		add_settings_field(
			'location_name',
			'Location name',
			array($this, 'location_name_setting'),
			'w4_instagram_location_options',
			'location_section'
		);

		add_settings_field(
			'location_coords',
			'Location coords',
			array($this, 'location_coords_setting'),
			'w4_instagram_location_options',
			'location_section'
		);

		add_settings_field(
			'location_distance',
			'Location distance',
			array($this, 'location_distance_setting'),
			'w4_instagram_location_options',
			'location_section'
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

		add_settings_field(
			'cache',
			'Cache',
			array($this, 'cache_setting'),
			'w4_instagram_display_options',
			'display_section'
		);


	}
}










