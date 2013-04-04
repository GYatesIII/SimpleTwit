<?php
add_action('admin_menu', 'st_submenu_page');
function st_submenu_page() {
	add_options_page( 'Twitter Feed Options', 'Twitter Feed', 'manage_options', 'st-twit-feed', 'st_options_page');
}

add_action('admin_init', 'st_options_setup');
function st_options_setup() {
	register_setting('st_options_group', 'st_twits', 'st_edit_twits');
	register_setting('st_options_group', 'st_auth_creds', 'st_edit_auth_creds');

	add_settings_section('st_twits_section', 'Twitter Feeds', 'st_twits_explain', 'st-twit-feed');
	add_settings_section('st_auth_creds_section', 'OAuth Credentials', 'st_auth_creds_explain', 'st-twit-feed');

	// Username Field
	add_settings_field('st_twits_fields', 'Twitter Feeds Fields', 'st_twits_fields', 'st-twit-feed', 'st_twits_section');

	// OAuth Creds Fields
	add_settings_field('st_consumer_key_field', 'Consumer Key', 'st_auth_consumer_key', 'st-twit-feed', 'st_auth_creds_section');
	add_settings_field('st_consumer_secret_field', 'Consumer Secet', 'st_auth_consumer_secret', 'st-twit-feed', 'st_auth_creds_section');
	add_settings_field('st_user_token_field', 'User Token', 'st_auth_user_token', 'st-twit-feed', 'st_auth_creds_section');
	add_settings_field('st_user_secret_field', 'User Secret', 'st_auth_user_secret', 'st-twit-feed', 'st_auth_creds_section');
}
/**
 * All the functions associated with the Twitter Feeds selection
 */
// Displays the explanation text for the twitter username
function st_twits_explain() {
	echo 'These are the usernames that will be pulled into this feed. Ideally, this shouldn\'t be more than ten users.';
}
// Displays the fields for the twitter feeds
function st_auth_consumer_key() {
	echo "CONSUMER KEY";
}
function st_auth_consumer_secret() {
	echo "CONSUMER SECRET";
}
function st_auth_user_token() {
	echo "USER TOKEN";
}
function st_auth_user_secret() {
	echo "USER SECRET";
}

/**
 * All the functions associated with the OAuth Creds
 */
// Displays the explanation text for the authentication section
function st_auth_creds_explain() {
	echo 'These are the OAuth credentials for Twitter. <a href="https://dev.twitter.com/docs/auth/authorizing-request" target="_blank">How do I generate these?</a>';
}
// Displays the
function st_auth_creds_fields() {
	// @todo Displays the fields for the auth credentials
}

/**
 * The function that calls the options page
 */
function st_options_page() {
	include 'lib/options-template.php';
}
?>