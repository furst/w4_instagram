<?php

include_once('class-settings.php');

Class View {

	public static function tabs($active_tab) {
		?>
		<h2 class='nav-tab-wrapper'>
			<a href='?page=w4_instagram_options&tab=config_options' class='nav-tab <?php echo $active_tab == 'config_options' ? 'nav-tab-active' : ''; ?>'>Config Options</a>
    		<a href='?page=w4_instagram_options&tab=hashtag_options' class='nav-tab <?php echo $active_tab == 'hashtag_options' ? 'nav-tab-active' : ''; ?>'>Hashtag Options</a>
    		<a href='?page=w4_instagram_options&tab=user_options' class='nav-tab <?php echo $active_tab == 'user_options' ? 'nav-tab-active' : ''; ?>'>User Options</a>
    		<a href='?page=w4_instagram_options&tab=location_options' class='nav-tab <?php echo $active_tab == 'location_options' ? 'nav-tab-active' : ''; ?>'>Location Options</a>
    		<a href='?page=w4_instagram_options&tab=display_options' class='nav-tab <?php echo $active_tab == 'display_options' ? 'nav-tab-active' : ''; ?>'>Display Options</a>
		</h2>
		<?php
	}

	public static function instructions() {
		
		$website_url = Settings::website_url();
		$instagram_redirect_uri = Settings::instagram_redirect_uri();

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
			</ol>

			<p>
				<strong>Name:</strong> your-application-name<br>
				<strong>Website:</strong> $website_url<br>
				<strong>OAuth redirect_uri:</strong> $instagram_redirect_uri<br>
			</p>
		</div>
		";
	}

	public static function hashtag_shortcode() {
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
	}

	public static function user_shortcode() {
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
	}

	public static function location_shortcode() {
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
			        		<code>[w4_instagram location=true]</code>
			        	</td>
			       	</tr>
			    </tbody>
			</table>
		";
	}

	public static function trigger() {
		echo "
			<table class='form-table permalink-structure'>
			    <tbody>
			       	<tr>
			        	<th>
			        		<label>
			        			Javascript to run after W4 instagram content has loaded
			        		</label>
			        	</th>
			        	<td>
			        		Use <code>jQuery(document).on('w4ImagesLoaded', your-callback);</code>
			        	</td>
			       	</tr>
			    </tbody>
			</table>
		";
	}

	public static function map_canvas() {
		echo
		"
		<div class='w4-instagram-error'></div>
		<div class='maparea'>
			<div id='panel'>
				<input class='get-locations' type='button' value='Search photos'>
				<input class='save-location' type='button' value='Add location'>
				<select class='radius-select'>
					<option value='' disabled='disabled' selected='selected'>Search radius</option>
					<option value='5000'>5000m</option>
					<option value='3000'>3000m</option>
					<option value='1000'>1000m</option>
					<option value='500'>500m</option>
					<option value='300'>300m</option>
					<option value='100'>100m</option>
				</select>
			</div>
			<input id='pac-input' class='controls' type='text' placeholder='Search place'>
			<div id='map-canvas' style='height:550px; width:100%;'></div>
		</div>
		";
	}

	public static function is_authenticated() {
		if (!Settings::auth()) {
			echo
			"
			<div class='w4-instagram-not-auth'>
				<p>You are not authenticated, please go to the config-tab and authenticate</p>
			</div>
			";
		}
	}

	public static function error_message() {
		echo "<p>Media could not be loaded</p>";
	}

	public static function access_token() {
		$access_token = Settings::access_token();
		echo "<script type='text/javascript'>var accessToken = '{$access_token}'</script>";
	}
}