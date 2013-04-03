<?php
/**
 * Description of st-tweet
 *
 * @author George Yates
 */
require_once 'lib/thmUtilities.php';

class st_tweet {
	private $wp_post;

	public $wp_id;
	public $is_retweet;
	public $is_response;
	public $content;
	public $time;
	public $time_str;

	function __construct($id = 0) {
		$this->wp_id = $id;

		$this->wp_post = get_post($this->id);

		$this->is_retweet = get_postmeta('', $this->wp_id);
		$this->is_response = get_postmeta('', $this->wp_id);

		$this->content = $this->wp_post->post_content;
		$this->time = $this->wp_post->post_date;


	}


}
?>
