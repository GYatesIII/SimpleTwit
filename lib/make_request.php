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

define('CONSUMER_KEY', 'nZmplnVJQS8iSU4SQjrA');
define('CONSUMER_SECRET', 'E4wtsKyLgckQa3i4ZBt8TrrGdNtFziO9d7i0APZzRI');
define('USER_TOKEN', '1284087823-nP1WlQazKCkXhPwr3Y4j3p18XCcbzbIYLDz0zEd');
define('USER_SECRET', 'origind7TeNCTWYSn6bR6p5Y9iEzdkQCEJ7UEZQ860');

define('SCREEN_NAME', 'Milt_Rosenberg');

function get_api_tweets($limit = 20, $since = 0) {

	$config = array(
		'consumer_key' => CONSUMER_KEY,
		'consumer_secret' => CONSUMER_SECRET,
		'user_token' => USER_TOKEN,
		'user_secret' => USER_SECRET
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