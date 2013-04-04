<?php
add_action('admin_menu', 'st_submenu_page');
function st_submenu_page() {
	add_options_page( 'Twitter Feed Options', 'Twitter Feed', 'manage_options', 'st-twit-feed', 'st_options_page');
}

add_action('admin_init', 'st_options_setup');
function st_options_setup() {
	register_setting('st_options_group', 'st_auth_creds', 'st_edit_auth_creds');
	register_setting('st_options_group', 'st_twits', 'st_edit_twits');

	add_settings_section('st_auth_creds_section', 'OAuth Credentials', 'st_auth_creds_explain', 'st-twit-feed');
	add_settings_section('st_twits_section', 'Twitter Feeds', 'st_twits_explain', 'st-twit-feed');

	add_settings_field('st_auth_creds_fields', 'OAuth Credential Fields', 'st_auth_creds_fields', 'st-twit-feed');
	add_settings_field('st_twits_fields', 'Twitter Feeds Fields', 'st_twits_fields', 'st-twit-feed');
}

/**
 * All the functions associated with the OAuth Creds
 */
function st_auth_creds_explain() {
	// @todo Displays the explanation text for the authentication section
}

function st_edit_auth_creds($input) {
	// @todo Sanitizes the authentication credentials
}

function st_auth_creds_fields() {
	// @todo Displays the fields for the auth credentials
}

/**
 * All the functions associated with the Twitter Feeds selection
 */
function st_edit_twits($input) {
	// @todo Sanitizes the usernames added
}

function st_twits_fields() {
	// @todo Displays the fields for the twitter feeds
}

function st_twits_explain() {
	// @todo Displays the explanation text for the twitter username
}

/**
 * The function that calls the options page
 */
function st_options_page() {
	include 'lib/options-template.php';
}
?>