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

function get_api_tweets($limit = 20, $since = 0) {
	$st_auth_creds = safe_unserialize(get_option('st_auth_creds'));

	$config = array(
		'consumer_key' => $st_auth_creds['consumer_key'],
		'consumer_secret' => $st_auth_creds['consumer_secret'],
		'user_token' => $st_auth_creds['user_token'],
		'user_secret' => $st_auth_creds['user_secret']
	);

	$auth = new tmhOAuth($config);

	$method = 'GET';
	$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
	$params = array(
		'screen_name' => get_option('st_twit'),
		'include_rts' => 'true',
		'exclude_replies' => 'false'
	);

	if ($limit != 0)
		$params['count'] = $limit;
	else
		$params['count'] = 199;

	if ($since != 0)
		$params['since_id'] = $since;
	else
		$params['sinde_id'] = 0;

	$auth->request($method, $url, $params);

	if ($auth->response['info']['http_code'] == 200)
		return json_decode($auth->response['response'], true);
	else
		return false;
}
?>