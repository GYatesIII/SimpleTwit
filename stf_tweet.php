<?php
/**
 * Description of STF_Tweet
 *
 * @author George Yates
 */
require_once 'lib/tmhUtilities.php';

class STF_Tweet {
	private $wp_id;
	private $wp_post;
	private $raw_tweet;

	public $is_retweet;
	public $is_reply;
	public $content;
	public $time;
	public $time_gmt;
	public $time_str;

	/**
	 * Creates our STF_Tweet object which gives us much more useful information about a tweet than your standard WP_Post object
	 *
	 * @param integer The ID of the tweet post in the DB to be turned into a full blown tweet object
	 */
	function __construct($id = 0) {
		if ($id != 0) {
			$this->wp_id = $id;
			$this->wp_post = get_post($this->wp_id);
			if ($this->wp_post->post_type !== 'stf_tweet')
				throw new Exception("This post must be a Tweet. Post ID {$id} is a {$this->wp_post->post_type}");

			$this->raw_tweet = json_decode(json_encode( safe_unserialize( get_post_meta( $this->wp_id, 'raw_tweet', safe_serialize( array() ) ) ) ), false);

			$this->is_retweet = get_post_meta($this->wp_id, 'is_retweet', true) == 1 ? true : false;
			$this->is_reply= get_post_meta($this->wp_id, 'is_reply', true) == 1 ? true : false;

			$this->content = $this->wp_post->post_content;

			$this->time = $this->wp_post->post_date;
			$this->time_gmt = $this->wp_post->post_date_gmt;
			$this->time_str = $this->get_default_time_str();
		}
	}

	/**
	 * Display how long has elapsed since this Tweet in the same format as Twitter
	 *
	 * @param string The GMT time represented as a string, if no time is provided, it uses the GMT time of the tweet
	 * @return string The time since this tweet in the format that Twitter represents it on their site, ex: 5 minutes ago
	 */
	private function get_default_time_str($time_gmt = false) {
		if ($time_gmt === false)
			$time_gmt = $this->time_gmt;

		$tz_gmt = new DateTimeZone('GMT');

		$time = new DateTime("@" . strtotime($this->time_gmt) . "s", $tz_gmt);

		return parseTwitterDate($time->format('Y-m-d H:i:s'), date('Y-m-d H:i:s'));
	}

	/**
	 * Gets a simple set of information about the original tweet that this tweet is a retweet of
	 *
	 * @return stdClass|boolean A simple object of the retweeted tweet, or false if this tweet is not a retweet
	 */
	public function get_retweet_info() {
		if ($this->is_retweet) {
			$info = new stdClass();

			$retweet = $this->raw_tweet->retweeted_status;

			$info->username = $retweet->user->name;
			$info->screenname = $retweet->user->screen_name;
			$info->content = $retweet->text;
			$info->time_gmt = $retweet->created_at;
			$info->url = "http://twitter.com/" . $retweet->user->screen_name . "/status/" . $retweet->id;
			$info->user_url = "http://twitter.com/" . $info->screenname;

			$info->raw_retweet = $retweet;

			return $info;
		} else {
			return false;
		}
	}

	/**
	 * Gets a simple set of information about the tweet and user being replied to by this tweet
	 *
	 * @return stdClass|boolean A simple object of the tweet being replied to by this tweet
	 */
	public function get_reply_info() {
		if ($this->is_reply) {
			$info = new stdClass();

			$info->url = "http://twitter.com/" . $this->raw_tweet->in_reply_to_screen_name . "/status/" . $this->raw_tweet->in_reply_to_status_id;
			$info->in_reply_to_name = $this->raw_tweet->in_reply_to_screen_name;
			$info->in_reply_to_user_url = "http://twitter.com/" . $this->raw_tweet->in_reply_to_screen_name;

			return $info;
		} else {
			return false;
		}
	}

	/**
	 * Returns the device or method used for tweeting this tweet
	 *
	 * @return string The source of the tweet
	 */
	public function get_source() {
		return $this->raw_tweet->source;
	}

	public function get_raw_tweet() {
		return $this->raw_tweet;
	}

	/**
	 * Gets the direct link to this tweet on Twitter
	 *
	 * @return string The direct link on Twitter to this tweet
	 */
	public function get_link() {
		return 'https://twitter.com/' . $this->raw_tweet->user->screen_name . '/status/' . $this->raw_tweet->id_str;
	}

	/**
	 * Gets the raw author information from the raw tweet
	 *
	 * @return stdClass Author object
	 */
	public function get_author() {
		return $this->raw_tweet->user;
	}

	/**
	 * Gets the direct link to the author of this tweet on Twitter
	 *
	 * @return string The direct link to the author's page
	 */
	public function get_author_link() {
		return 'https://twitter.com/' . $this->raw_tweet->user->screen_name;
	}
}