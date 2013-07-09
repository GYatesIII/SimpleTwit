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

	function __construct($id = 0) {
		if ($id != 0) {
			$this->wp_id = $id;
			$this->wp_post = get_post($this->wp_id);
			$this->raw_tweet = safe_unserialize(get_post_meta($this->wp_id, 'raw_tweet', safe_serialize(array())));

			$this->is_retweet = get_post_meta($this->wp_id, 'is_retweet', true) == 1 ? true : false;
			$this->is_reply= get_post_meta($this->wp_id, 'is_reply', true) == 1 ? true : false;

			$this->content = $this->wp_post->post_content;

			$this->time = $this->wp_post->post_date;
			$this->time_gmt = $this->wp_post->post_date_gmt;
			$this->time_str = $this->get_default_time_str();
		}
	}

	public function get_default_time_str($time_gmt = false) {
		if ($time_gmt === false) $time_gmt = $this->time_gmt;

		$tz_str = get_option('timezone_string');
		if (empty($tz_str)) $tz_str = 'GMT';

		$tz = new DateTimeZone($tz_str);
		$time = new DateTime("@" . strtotime($this->time_gmt) . "s");
		$time->setTimezone($tz);
		return parseTwitterDate($time->format('Y-m-d H:i:s'));
	}

	public function get_retweet_info() {
		if ($this->is_retweet) {
			$info = new stdClass();

			$retweet = $this->raw_tweet['retweeted_status'];

			$info->username = $retweet['user']['name'];
			$info->screenname = $retweet['user']['screen_name'];
			$info->text = $retweet['text'];
			$info->time_gmt = $retweet['created_at'];
			$info->url = "http://twitter.com/" . $retweet['user']['screen_name'] . "/status/" . $retweet['id'];
			$info->user_url = "http://twitter.com/" . $info->screenname;

			$info->raw_retweet = $retweet;

			return $info;
		} else {
			return false;
		}
	}

	public function get_reply_info() {
		if ($this->is_reply) {
			$info = new stdClass();

			$info->status_url = "http://twitter.com/" . $this->raw_tweet['in_reply_to_screen_name'] . "/status/" . $this->raw_tweet['in_reply_to_status_id'];
			$info->in_reply_to_name = $this->raw_tweet['in_reply_to_screen_name'];
			$info->in_reply_to_user_url = "http://twitter.com/" . $this->raw_tweet['in_reply_to_screen_name'];

			return $info;
		} else {
			return false;
		}
	}

	public function get_source() {
		return $this->raw_tweet->source;
	}

	public function get_raw_tweet() {
		return $this->raw_tweet;
	}

	public function get_link() {
		return 'https://twitter.com/' . $this->raw_tweet['user']['screen_name'] . '/status/' . $this->raw_tweet['id_str'];
	}
}