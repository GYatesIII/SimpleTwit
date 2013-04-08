<?php
/*
Plugin Name: Simple Twit
Plugin URI: http://www.golden-tech.com
Description: This is a simple plugin that enables you to pull in and cache a Twitter feed.
Version: 0.1
Author: George Yates
Author URI: http://www.georgeyatesiii.com
License: GPL
*/

require_once 'lib/tweet-post-type.php';
require_once 'lib/make_request.php';
require_once 'options-page.php';
require_once 'st_tweet.php';

function get_tweets($num = 5) {
	$args = array(
		'post_type' => 'tweet',
		'numberposts' => 5
	);
	$raw_tweets = get_posts($args);

	$tweets = array();

	foreach ($raw_tweets as $raw_tweet) {
		$tweet = new ST_Tweet($raw_tweet->ID);

		$tweets[] = $tweet;
	}

	return $tweets;
}

/**
 * Setting Up Import on Plugin Install
 */
add_filter( 'cron_schedules', 'cron_add_fifteen' );
function cron_add_fifteen( $schedules ) {
	// Adds once weekly to the existing schedules.
	$schedules['fifteen'] = array(
		'interval' => 60*15,
		'display' => 'Every Fifteen Minutes'
	);
	return $schedules;
}

add_action('tweet_import', 'import_tweets');
function import_tweets() {
	$raw_tweets = get_api_tweets(0, get_option('last_tweet', 0));
	if (!empty($raw_tweets))
		input_tweets($raw_tweets);
}

function input_tweets($tweets) {
	$tmhUtil = new tmhUtilities();
	
	foreach ($tweets as $tweet) {
		$post = array();

		$post['post_title'] = $tweet['id_str'];
		$post['post_content'] = $tmhUtil->entify($tweet);
		$post['post_date_gmt'] = date('Y-m-d H:i:s', strtotime($tweet['created_at']));
		$post['post_type'] = 'tweet';

		$post_date = new DateTime($post['post_date_gmt'], new DateTimeZone('GMT'));
		$timezone = get_option('timezone_string');
		if (!empty($timezone))
			$post_date->setTimezone(new DateTimeZone(get_option('timezone_string')));

		$post['post_date'] = $post_date->format('Y-m-d H:i:s');
		$post['post_status'] = 'publish';

		$id = wp_insert_post($post);
		update_post_meta($id, 'raw_tweet', safe_serialize($tweet));
		update_post_meta($id, 'is_retweet',  isset($tweet['retweeted_status']) && $tweet['retweeted_status'] !== NULL);
		update_post_meta($id, 'is_reply', isset($tweet['in_reply_to_status_id']) && $tweet['in_reply_to_status_id'] !== NULL);
	}

	if (isset($tweets[0]['id']))
		update_option('last_tweet', intval($tweets[0]['id']));
}

/**
 * Clearing the Cron on Plugin Deactivation
 */
register_deactivation_hook(__FILE__, 'stf_deactivation');
function stf_deactivation() {
	wp_clear_scheduled_hook('tweet_import');
}

/**
 * Adding in Cron on Plugin Activation, adding in plugin options and importing the first 50 tweets of each author
 */
register_activation_hook(__FILE__, 'stf_activation');
function stf_activation() {
	wp_schedule_event(time(), 'fifteen', 'tweet_import');

	// Adding Initial Options for Plugin
	$init_options = array (
			'consumer_key' => '',
			'consumer_secret' => '',
			'user_token' => '',
			'user_secret' => ''
		);
	add_option('st_auth_creds', safe_serialize($init_options));
	add_option('st_twit', 'GeorgeYatesIII');
	add_option('st_last_tweet', 0);
}

/**
 * Helper Functions
 */

/**
 * Parse a provided date into a string similar to how Twitter represents its dates
 *
 * @param DateTime $date The date of the tweet
 * @param DateTime $large_date [optional] The date to compare the tweet to, to generate the string, defaults to the current date
 * @return string on success or boolean on failure
 */
function parseTwitterDate($date, $large_date=false)
{
	if(!$large_date)
		$large_date = date('Y-m-d h:i:s');

	$n = strtotime($large_date) - strtotime($date);

	if($n <= 1) return 'less than 1 second ago';
	if($n < (60)) return $n . ' seconds ago';
	if($n < (60*60)) { $minutes = round($n/60); return 'about ' . $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago'; }
	if($n < (60*60*16)) { $hours = round($n/(60*60)); return 'about ' . $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago'; }
	if($n < (time() - strtotime('yesterday'))) return 'yesterday';
	if($n < (60*60*24)) { $hours = round($n/(60*60)); return 'about ' . $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago'; }
	if($n < (60*60*24*6.5)) return 'about ' . round($n/(60*60*24)) . ' days ago';
	if($n < (time() - strtotime('last week'))) return 'last week';
	if(round($n/(60*60*24*7))  == 1) return 'about a week ago';
	if($n < (60*60*24*7*3.5)) return 'about ' . round($n/(60*60*24*7)) . ' weeks ago';
	if($n < (time() - strtotime('last month'))) return 'last month';
	if(round($n/(60*60*24*7*4))  == 1) return 'about a month ago';
	if($n < (60*60*24*7*4*11.5)) return 'about ' . round($n/(60*60*24*7*4)) . ' months ago';
	if($n < (time() - strtotime('last year'))) return 'last year';
	if(round($n/(60*60*24*7*52)) == 1) return 'about a year ago';
	if($n >= (60*60*24*7*4*12)) return 'about ' . round($n/(60*60*24*7*52)) . ' years ago';
	return false;
}

/**
 * Serialize and Unserialize data safely to avoid offset errors
 */
if (!function_exists('safe_serialize')) {
	function safe_serialize($var) {
		return base64_encode(serialize($var));
	}
}

if ( !function_exists('safe_unserialize') ) {
	function safe_unserialize($var) {
		return unserialize(base64_decode($var));
	}
}
?>