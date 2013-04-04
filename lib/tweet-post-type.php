<?php
add_action('init', 'st_tweet_type');
function st_tweet_type() {
	register_post_type('tweet', array(
			'label' => 'Tweet',
			'description' => 'Tweets pulled from Twitter',
			'public' => true,
			'supports' => array('editor'),
			'has_archive' => 'tweet',
			'rewrite' => array('slug' => 'tweet', 'with_front' => true)
		)
	);
}
?>