<?php

/*
*Information here

* TODO: Add prefixes to classes and names
*/

class Options {

	public $options;

	public function __construct() {
		$this->options = get_option('instagram_options');
		$this->register_settings();
	}

	public static function add_menu_page() {
		add_options_page('Instagram', 'Instagram', 'administrator', __FILE__, array('Options', 'display_options_page'));
	}

	public function display_options_page() {
		?>
			<div class="wrap">
				<h2>Instagram</h2>
				<form method="post" action="options.php" enctype="multipart/form-data">
					<?php settings_fields('instagram_options'); ?>
					<?php do_settings_sections(__FILE__); ?>
					<p class="submit">
						<input type="submit" name="submit" class="button-primary" value="Save changes">
					</p>
				</form>
			</div>
		<?php
	}

	public function register_settings() {
		register_setting('instagram_options', 'instagram_options');
		add_settings_section('main_section', 'Main Settings', array($this, 'main_section_cb'), __FILE__); // id, title, callback, page
		add_settings_field('testfield', 'Testfield', array($this, 'testfield_setting'), __FILE__, 'main_section');
	}

	public function main_section_cb() {

	}

	// Testfield
	public function testfield_setting() {
		echo "<input name='instagram_options[testfield]' type='text' value='{$this->options['testfield']}' />";
	}
}