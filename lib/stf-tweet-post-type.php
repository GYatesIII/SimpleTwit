<?php
if (!function_exists('stf_tweet_type'))
{
	function stf_tweet_type() {
		register_post_type('stf_tweet', array(
				'label' => 'Tweet',
				'description' => 'Tweets pulled from Twitter',
				'public' => false,
				'supports' => array('editor'),
				'has_archive' => 'tweet',
				'rewrite' => array('slug' => 'tweet', 'with_front' => true)
			)
		);
	}
	add_action('init', 'stf_tweet_type');
}