<?php
add_action('init', 'stf_tweet_type');
function stf_tweet_type() {
	register_post_type('tweet', array(
			'label' => 'Tweet',
			'description' => 'Shows for Milt Rosenberg, turned into a Podcast feed too',
			'public' => true,
			'supports' => array('editor'),
			'has_archive' => 'tweet',
			'rewrite' => array('slug' => 'tweet', 'with_front' => true)
		)
	);
}
?>