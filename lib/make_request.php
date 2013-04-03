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

define('CONSUMER_KEY', 'demo');
define('CONSUMER_SECRET', 'demo');
define('USER_TOKEN', 'demo');
define('USER_SECRET', 'demo');

define('SCREEN_NAME', 'GeorgeYatesIII');

function get_api_tweets($limit = 20, $since = 0) {

	$config = array(
		'consumer_key' => get_option('st_consumer_key'),
		'consumer_secret' => get_option('st_consumer_secret'),
		'user_token' => get_option('st_user_token'),
		'user_secret' => get_option('st_user_secret')
	);
	$auth = new tmhOAuth($config);

	$method = 'GET';
	$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
	$params = array(
		'screen_name' => SCREEN_NAME,
		'include_rts' => false,
		'exclude_replies' => true
	);

	if ($limit != false)
		$params['count'] = $limit;

	if ($since != 0)
		$params['since_id'] = $since;

	$auth->request($method, $url, $params);
	return json_decode($auth->response['response'], true);
}
?>