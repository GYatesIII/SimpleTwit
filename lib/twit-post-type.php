<?php
add_action('init', 'st_twit_type');
function st_twit_type() {
	register_post_type('twit', array(
			'label' => 'Twit',
			'description' => 'Twitter Users who we\'re following',
			'public' => true,
			'supports' => array('editor'),
			'has_archive' => 'false'
		)
	);
}
?>