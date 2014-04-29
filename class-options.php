<?php

/*
*Information here

* TODO:
*/

class Options {

	public $options;

	public function __construct() {
		$this->options = get_option('w4_instagram_options');
		$this->register_settings();
	}

	public static function add_menu_page() {
		add_options_page('W4 instagram', 'W4 instagram', 'administrator', __FILE__, array('Options', 'display_options_page'));
	}

	public function display_options_page() {
		?>
			<div class="wrap">
				<h2>W4 instagram</h2>
				<form method="post" action="options.php" enctype="multipart/form-data">
					<?php settings_fields('w4_instagram_options'); ?>
					<?php do_settings_sections(__FILE__); ?>
					<p class="submit">
						<input type="submit" name="submit" class="button-primary" value="Save changes">
					</p>
				</form>
			</div>
		<?php
	}

	public function register_settings() {
		register_setting('w4_instagram_options', 'w4_instagram_options');
		add_settings_section('main_section', 'Main Settings', array($this, 'main_section_cb'), __FILE__); // id, title, callback, page
		add_settings_field('client_id', 'Client ID', array($this, 'client_id_setting'), __FILE__, 'main_section');
	}

	public function main_section_cb() {

	}

	/* -------- Fields -------- */
	
	// Client ID
	public function client_id_setting() {
		echo "<input name='w4_instagram_options[client_id]' type='text' value='{$this->options['client_id']}' />";
	}
}










