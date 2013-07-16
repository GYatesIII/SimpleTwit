<?php
/*
Plugin Name: Simple Twitter Feed
Plugin URI: http://www.golden-tech.com
Description: This is a simple plugin that enables you to pull in and cache a Twitter feed.
Version: 0.1
Author: George Yates
Author URI: http://www.georgeyatesiii.com
License: GPL
*/

require 'tmhOAuth.php';
require 'tmhUtilities.php';

if (!function_exists('get_api_tweets'))
{
	function get_api_tweets($args) {
		$defaults = array(
			'limit' => 20,
			'since' => 0
		);
		$args = wp_parse_args($args, $defaults);

		$stf_auth_creds = safe_unserialize(get_option('stf_auth_creds'));

		$config = array(
			'consumer_key' => $stf_auth_creds['consumer_key'],
			'consumer_secret' => $stf_auth_creds['consumer_secret'],
			'user_token' => $stf_auth_creds['user_token'],
			'user_secret' => $stf_auth_creds['user_secret']
		);

		$auth = new tmhOAuth($config);

		$method = 'GET';
		$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
		$params = array(
			'screen_name' => get_option('stf_twit'),
			'include_rts' => 'true',
			'exclude_replies' => 'false'
		);

		if ($args['limit'] != 0)
			$params['count'] = $args['limit'];
		else
			$params['count'] = 199;

		if ($args['since'] > 0)
			$params['since_id'] = $args['since'];

		$auth->request($method, $url, $params);

		if ($auth->response['info']['http_code'] == 200)
			return json_decode($auth->response['response'], true);
		else
			return false;
	}
}