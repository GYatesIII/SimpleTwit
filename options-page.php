<?php
if (!function_exists('stf_submenu_page'))
{
	function stf_submenu_page() {
		add_options_page( 'Twitter Feed Options', 'Twitter Feed', 'manage_options', 'st-twit-feed', 'stf_options_page');
	}
	add_action('admin_menu', 'stf_submenu_page');
}

if (!function_exists('stf_options_setup'))
{
	function stf_options_setup() {
		register_setting('st_options_group', 'st_twit');
		register_setting('st_options_group', 'st_auth_creds', 'stf_edit_auth_creds');

		add_settings_section('st_twits_section', 'Twitter Feeds', 'stf_twits_explain', 'st-twit-feed');
		add_settings_section('st_auth_creds_section', 'OAuth Credentials', 'stf_auth_creds_explain', 'st-twit-feed');

		// Username Field
		add_settings_field('stf_twits_fields', 'Username', 'stf_twits_fields', 'st-twit-feed', 'st_twits_section');

		// OAuth Creds Fields
		add_settings_field('st_consumer_key_field', 'Consumer Key', 'stf_auth_consumer_key', 'st-twit-feed', 'st_auth_creds_section');
		add_settings_field('st_consumer_secret_field', 'Consumer Secet', 'stf_auth_consumer_secret', 'st-twit-feed', 'st_auth_creds_section');
		add_settings_field('st_user_token_field', 'User Token', 'stf_auth_user_token', 'st-twit-feed', 'st_auth_creds_section');
		add_settings_field('st_user_secret_field', 'User Secret', 'stf_auth_user_secret', 'st-twit-feed', 'st_auth_creds_section');
	}
	add_action('admin_init', 'stf_options_setup');
}

/**
 * All the functions associated with the Twitter Feeds selection
 */
if (!function_exists('stf_twits_explain'))
{
	// Displays the explanation text for the twitter username
	function stf_twits_explain() {
		echo 'These are the usernames that will be pulled into this feed. Ideally, this shouldn\'t be more than ten users.';
	}
}
if (!function_exists('stf_twits_fields'))
{
	// Displays the username entry and deletion structure
	function stf_twits_fields() {
		echo '<input type="text" value="' . get_option('st_twit') . '" name="st_twit" class="regular-text" id="stf_twits_fields">';
	}
}

/**
 * All the functions associated with the OAuth Creds
 */
if (!function_exists('stf_auth_creds_explain'))
{
	// Displays the explanation text for the authentication section
	function stf_auth_creds_explain() {
		echo 'These are the OAuth credentials for Twitter. <a href="https://dev.twitter.com/docs/auth/authorizing-request" target="_blank">How do I generate these?</a>';
	}
}
if (!function_exists('stf_auth_consumer_key'))
{
	// Displays the fields for the twitter feeds
	function stf_auth_consumer_key() {
		$st_auth_creds = safe_unserialize(get_option('st_auth_creds'));
		echo '<input type="text" value="'. $st_auth_creds['consumer_key'] .'" name="st_auth_creds[consumer_key]" class="regular-text code" id="st_consumer_key_field">';
	}
}
if (!function_exists('stf_auth_consumer_secret'))
{
	function stf_auth_consumer_secret() {
		$st_auth_creds = safe_unserialize(get_option('st_auth_creds'));
		echo '<input type="text" value="'. $st_auth_creds['consumer_secret'] .'" name="st_auth_creds[consumer_secret]" class="regular-text code" id="st_consumer_secret_field">';
	}
}
if (!function_exists('stf_auth_user_token'))
{
	function stf_auth_user_token() {
		$st_auth_creds = safe_unserialize(get_option('st_auth_creds'));
		echo '<input type="text" value="'. $st_auth_creds['user_token'] .'" name="st_auth_creds[user_token]" class="regular-text code" id="st_user_token_field">';
	}
}
if (!function_exists('stf_auth_user_secret'))
{
	function stf_auth_user_secret() {
		$st_auth_creds = safe_unserialize(get_option('st_auth_creds'));
		echo '<input type="text" value="'. $st_auth_creds['user_secret'] .'" name="st_auth_creds[user_secret]" class="regular-text code" id="st_user_secret_field">';
	}
}

if (!function_exists('stf_edit_auth_creds'))
{
	function stf_edit_auth_creds($input) {
		$defaults = array(
			'consumer_key' => '',
			'consumer_secret' => '',
			'user_token' => '',
			'user_secret' => ''
			);
		$creds = wp_parse_args($input, $defaults);

		return safe_serialize($creds);
	}
}

/**
 * The function that calls the options page
 */
if (!function_exists('stf_options_page'))
{
	function stf_options_page() {
		include 'views/options-template.php';
	}
}
?>