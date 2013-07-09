<div class="wrap">
	<?php screen_icon(); ?>
	<h2>Twitter Feed</h2>
	<form method="post" action="options.php">
		<?php settings_fields('stf_options_group'); ?>
		<?php do_settings_sections('stf-twit-feed'); ?>
		<?php submit_button(); ?>
	</form>
</div>