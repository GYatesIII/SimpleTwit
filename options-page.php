<?php
if (!function_exists('stf_submenu_page'))
{
	function stf_submenu_page() {
		add_options_page( 'Twitter Feed Options', 'Twitter Feed', 'manage_options', 'stf-twit-feed', 'stf_options_page');
	}
	add_action('admin_menu', 'stf_submenu_page');
}

if (!function_exists('stf_options_setup'))
{
	function stf_options_setup() {
		register_setting('stf_options_group', 'stf_twit', 'stf_edit_twit');
		register_setting('stf_options_group', 'stf_auth_creds', 'stf_edit_auth_creds');

		add_settings_section('stf_twits_section', 'Twitter Feeds', 'stf_twits_explain', 'stf-twit-feed');
		add_settings_section('stf_auth_creds_section', 'OAuth Credentials', 'stf_auth_creds_explain', 'stf-twit-feed');

		// Username Field
		add_settings_field('stf_twits_fields', 'Username', 'stf_twits_fields', 'stf-twit-feed', 'stf_twits_section');

		// OAuth Creds Fields
		add_settings_field('stf_consumer_key_field', 'Consumer Key', 'stf_auth_consumer_key', 'stf-twit-feed', 'stf_auth_creds_section');
		add_settings_field('stf_consumer_secret_field', 'Consumer Secet', 'stf_auth_consumer_secret', 'stf-twit-feed', 'stf_auth_creds_section');
		add_settings_field('stf_user_token_field', 'Access Token', 'stf_auth_user_token', 'stf-twit-feed', 'stf_auth_creds_section');
		add_settings_field('stf_user_secret_field', 'Access Secret', 'stf_auth_user_secret', 'stf-twit-feed', 'stf_auth_creds_section');
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
		echo 'The username pulled whose feed we\'re pulling in. Changing this may take a while as it rebuilds the database of tweets.';
	}
}
if (!function_exists('stf_twits_fields'))
{
	// Displays the username entry and deletion structure
	function stf_twits_fields() {
		echo '<input type="hidden" value="' . get_option('stf_twit') . '" name="stf_twit[old]">';
		echo '@<input type="text" value="' . get_option('stf_twit') . '" name="stf_twit[new]" class="regular-text" id="stf_twits_fields">';
	}
}
if (!function_exists('stf_edit_twit'))
{
	//Checks to see if the user has changed, if he has then we rebuild the tweet database
	function stf_edit_twit($twit) {
		if (is_string($twit)) return $twit; // Since this function gets called twice, the second time we don't want to run this callback

		if ($twit['old'] !== $twit['new'])
		{
			// First we delete the existing tweets and reset the last known tweet record
			$offset = 0;
			while (!isset($old_tweets) || !empty($old_tweets))
			{
				$args = array(
					'posts_per_page' => 50,
					'offset' => $offset,
					'post_type' => 'stf_tweet',
					'post_status' => 'any'
				);
				$old_tweets = get_posts($args);

				foreach($old_tweets as $old_tweet)
				{
					wp_delete_post( $old_tweet->ID, true );
				}

				$offset += 50;
			}
			update_option( 'stf_last_tweet', '0' );

			// Then we run an API pull with the current user
			stf_import_tweets(array('screen_name' => $twit['new']));
		}

		return $twit['new'];
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
		$stf_auth_creds = safe_unserialize(get_option('stf_auth_creds'));
		echo '<input type="text" value="'. $stf_auth_creds['consumer_key'] .'" name="stf_auth_creds[consumer_key]" class="regular-text code" id="stf_consumer_key_field">';
	}
}
if (!function_exists('stf_auth_consumer_secret'))
{
	function stf_auth_consumer_secret() {
		$stf_auth_creds = safe_unserialize(get_option('stf_auth_creds'));
		echo '<input type="text" value="'. $stf_auth_creds['consumer_secret'] .'" name="stf_auth_creds[consumer_secret]" class="regular-text code" id="stf_consumer_secret_field">';
	}
}
if (!function_exists('stf_auth_user_token'))
{
	function stf_auth_user_token() {
		$stf_auth_creds = safe_unserialize(get_option('stf_auth_creds'));
		echo '<input type="text" value="'. $stf_auth_creds['user_token'] .'" name="stf_auth_creds[user_token]" class="regular-text code" id="stf_user_token_field">';
	}
}
if (!function_exists('stf_auth_user_secret'))
{
	function stf_auth_user_secret() {
		$stf_auth_creds = safe_unserialize(get_option('stf_auth_creds'));
		echo '<input type="text" value="'. $stf_auth_creds['user_secret'] .'" name="stf_auth_creds[user_secret]" class="regular-text code" id="stf_user_secret_field">';
	}
}

if (!function_exists('stf_edit_auth_creds'))
{
	function stf_edit_auth_creds($input) {
		$old_creds = safe_unserialize(get_option('stf_auth_creds'));

		$defaults = array(
			'consumer_key' => '',
			'consumer_secret' => '',
			'user_token' => '',
			'user_secret' => ''
			);
		$creds = wp_parse_args($input, $defaults);

		if ($creds != $old_creds)
		{
			stf_import_tweets(array('auth_creds' => $creds));
		}

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