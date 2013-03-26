<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of st-tweet
 *
 * @author gyates
 */
class st_tweet {
	private $wp_post;

	public $wp_id = $id;
	public $is_retweet;

	function __construct($id = 0) {
		$wp_id = $id;
		$wp_post = get_post($id);

		$is_retweet = get_postmeta('', $wp_id);
		$is_favorite = get_postmeta('', $wp_id);
	}

	
}
?>
