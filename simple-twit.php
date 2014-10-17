<?php
/*
Plugin Name: SimpleTwit
Plugin URI: https://github.com/GYatesIII/SimpleTwit
Description: A plugin for developers and designers that sets up a WP_Cron to pull in and cache a user's stream. It's all that a developer needs to incorporate a Twitter feed on their site, the OAuth handling, caching to avoid rate limiting, and utilities to easily format Tweets correctly without predefined styles to work around. For designers, the plugin creates a widget that can be used to easily display and style Tweets in your theme.
Version: 1.3
Author: George Yates III
Author URI: http://www.georgeyatesiii.com
License: GPL2

Copyright 2013 George Yates (email : me@georgeyatesiii.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once 'lib/stf-tweet-post-type.php';
require_once 'lib/make_request.php';
require_once 'options-page.php';
require_once 'stf_tweet.php';
require_once 'widget.php';

if (!function_exists('stf_get_tweets'))
{
	/**
	 * The main function used to retrieve the raw tweets
	 *
	 * @param array An array of arguments 'num', 'offset', 'retweets', and 'replies'
	 * @return array An array of tweets
	 */
	function stf_get_tweets($args = array()) {
		$defaults = array(
			'num' => 5, // The number of tweets to get
			'offset' => 0, // The number of tweets to offset
			'retweets' => true, // Whether or not to get retweets
			'replies' => true  // Whether or not to get replies
		);
		$args = wp_parse_args($args, $defaults);

		$meta_query = array();
		if (!$args['retweets']) {
			$meta_query[] = array(
				'key' => 'is_retweet',
				'value' => 0
				);
		}
		if (!$args['replies']) {
			$meta_query[] = array(
				'key' => 'is_reply',
				'value' => 0
				);
		}
		$post_args = array(
			'post_type' => 'stf_tweet',
			'numberposts' => $args['num'],
			'offset' => $args['offset'],
			'meta_query' => $meta_query
		);
		$raw_tweets = get_posts($post_args);

		$tweets = array();
		foreach ($raw_tweets as $raw_tweet) {
			$tweets[] = new STF_Tweet($raw_tweet->ID);
		}

		return $tweets;
	}
}

if (!function_exists('stf_cron_add_fifteen'))
{
	/**
	 * Setting Up Import on Plugin Install
	 */
	function stf_cron_add_fifteen( $schedules ) {
		// Adds once weekly to the existing schedules.
		$schedules['fifteen'] = array(
			'interval' => 60*15,
			'display' => 'Every Fifteen Minutes'
		);
		return $schedules;
	}
	add_filter( 'cron_schedules', 'stf_cron_add_fifteen' );
}

if (!function_exists('stf_import_tweets'))
{
	/**
	 * Runs every 15 minutes and makes the API call and then passes the response to the function that enters the tweets into the DB
	 */
	function stf_import_tweets($args = array())
	{
		$defaults = array(
			'limit' => 0,
			'since' => get_option('stf_last_tweet', '0')
		);
		$args = wp_parse_args($args, $defaults);

		$raw_tweets = stf_get_api_tweets($args);
		if ($raw_tweets === false)
		{
			update_option( 'stf_creds_info', 'error' );
		}
		else
		{
			update_option( 'stf_creds_info', 'valid' );
		}

		if (!empty($raw_tweets))
		{
			stf_input_tweets($raw_tweets);
		}
	}
	add_action('stf_tweet_import', 'stf_import_tweets');
}

if (!function_exists('stf_input_tweets'))
{
	/**
	 * Takes the raw response from the Twitter API and processes it into the WP DB
	 *
	 * @param array $tweets
	 */
	function stf_input_tweets($tweets) {
		$tmhUtil = new tmhUtilities();

		foreach ($tweets as $tweet) {
			$args = array(
				'post_type' => 'stf_tweet',
				'meta_key' => 'tweet_id',
				'meta_value' => $tweet['id_str']
			);
			$tweet_test = get_posts($args);

			if (empty($tweet_test)) {
				$post = array();

				$post['post_title'] = $tweet['id_str'];
				$post['post_content'] = $tmhUtil->entify($tweet);
				$post['post_date_gmt'] = date('Y-m-d H:i:s', strtotime($tweet['created_at']));
				$post['post_type'] = 'stf_tweet';

				$post_date = new DateTime($post['post_date_gmt'], new DateTimeZone('GMT'));
				$timezone = get_option('timezone_string');
				if (!empty($timezone))
					$post_date->setTimezone(new DateTimeZone(get_option('timezone_string')));

				$post['post_date'] = $post_date->format('Y-m-d H:i:s');
				$post['post_status'] = 'publish';

				$id = wp_insert_post($post);
				update_post_meta($id, 'raw_tweet', safe_serialize($tweet));
				update_post_meta($id, 'tweet_id', $tweet['id_str']);
				update_post_meta($id, 'is_retweet',  isset($tweet['retweeted_status']) && $tweet['retweeted_status'] !== NULL ? 1 : 0);
				update_post_meta($id, 'is_reply', isset($tweet['in_reply_to_status_id']) && $tweet['in_reply_to_status_id'] !== NULL ? 1 : 0);
			}
		}

		if (isset($tweets[0]['id']))
			update_option('stf_last_tweet', $tweets[0]['id_str']);
	}
}

if ( !function_exists('stf_admin_notices') )
{
	/**
	 * Checks the DB to see if there's any problem with the provided OAuth creds and throws admin notices if there are
	 */
	function stf_admin_notices()
	{
		switch ( get_option('stf_creds_info') )
		{
			case 'empty' :
				?>
				<div class="updated">
					<p>Please <a href="<?php echo admin_url( 'options-general.php?page=stf-twit-feed' ); ?>">provide your OAuth credentials</a> for SimpleTwit to function properly.</p>
				</div>
				<?php
				break;
			case 'error' :
				?>
				<div class="error">
					<p>There was a problem with your OAuth credentials for SimpleTwit. Please <a href="<?php echo admin_url( 'options-general.php?page=stf-twit-feed' ); ?>">check and ensure they are correct</a>.</p>
				</div>
				<?php
				break;
		}
	}
	add_action( 'admin_notices', 'stf_admin_notices' );
}

if ( !function_exists('stf_deactivation') )
{
	/**
	 * Clearing the Cron on Plugin Deactivation
	 */
	register_deactivation_hook(__FILE__, 'stf_deactivation');
	function stf_deactivation() {
		wp_clear_scheduled_hook('stf_tweet_import');
	}
}

if (!function_exists('stf_activation'))
{
	/**
	 * Adding in Cron on Plugin Activation, adding in plugin options and importing the first 50 tweets of each author
	 */
	function stf_activation() {
		wp_schedule_event(time(), 'fifteen', 'stf_tweet_import');

		// Adding Initial Options for Plugin
		$init_options = array (
				'consumer_key' => '',
				'consumer_secret' => '',
				'user_token' => '',
				'user_secret' => ''
			);
		add_option('stf_auth_creds', safe_serialize($init_options));
		add_option('stf_twit', 'GeorgeYatesIII');
		add_option('stf_last_tweet', 0);
		add_option('stf_creds_info', 'empty');
	}
	register_activation_hook( __FILE__, 'stf_activation' );
}

/**
 * Helper Functions
 */

if (!function_exists('parseTwitterDate'))
{
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
}

/**
 * Helper functions used to serialize and unserialize data safely to avoid offset errors
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
