=== SimpleTwit ===
Contributors: GYatesIII
Donate link:
Tags: twitter feed, developer, simple, oauth, caching, cache
Requires at least: 3.3
Tested up to: 3.5.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Everything a developer needs to pull in a Twitter feed, OAuth handling, caching, and more. All without predefined styles that can just get in the way.

== Description ==

A plugin for developers that sets up a WP_Cron to pull in and cache a user's stream. It's all that a developer needs to incorporate a Twitter feed on their site, the OAuth handling, caching to avoid rate limiting, and utilities to easily format tweets correctly without predefined styles to work around.

= Features =
* Define Username and Application Information
* Flags and provides extra information on retweets and replies
* Caches Tweets to prevent rate limiting problems
* Hooks into WP_Cron for easy installation
* Uses OAuth and the v1.1 REST API for the latest in Twitter compliance

= Usage =
The plugin provides a number of useful functions. First, we'll look at the STF_Tweet object:

**`STF_Tweet`**

This object replaces the WP_Post object and provides a number of useful methods when working with tweets. This object can be created by constructing a new `STF_Tweet` and passing it the WP Post ID of a Tweet. `$tweet = new STF_Tweet($id)` Generally though, this will be done for you with the `stf_get_tweets()` function. This object has the following accessible properties:

* `is_retweet` - This boolean will be `true` when the tweet is a retweet
* `is_reply` - This boolean will be `true` when the tweet is a reply to another tweet
* `content` - The content of the tweet, this will be automatically formatted to link other referenced Twitter users, hashtags, and inline links
* `time` - A timestamp of the tweet, timezoned to the WP install, in Y-m-d H:i:s format
* `time_gmt` - A timestamp of the tweet, timezoned to GMT, in Y-m-d H:i:s format
* `time_str` - The string that represents how long it's been since the tweet, in the way that Twitter usually dates its Tweets, good for an international audience since this isn't timezone specicific

The object has the following methods:

* `get_default_time_str($time)` - This method will return a Twitter formatted how long since `$time`, where `$time` is a string representing a GMT timezoned date time
* `get_source()` - This method returns the string representing the device used to Tweet this status
* `get_raw_tweet()` - This method returns the raw response from the API, this should only be rarely needed
* `get_retweet_info()` - This method will return the info on the original tweet of a retweet, or false if the tweet is not a retweet, the object returned contains:
* * `username` - The Twitter username of the original Tweet
* * `screenname` - The Twitter screenname of the original Tweet
* * `content` - The _unformatted_ content of the original Tweet
* * `time_gmt` - The GMT time of the retweet
* * `url` - A direct link to the original tweet
* * `user_url` - A direct link to the profile of the original Twitter user
* `get_reply_info()` - This method will return the info on the original status that this tweet is replying to, the info is as follows:
* * `url` - The direct link to the original status
* * `in_reply_to_name` - The screenname of the original Twitter user
* * `in_reply_to_user_url` - The direct link to the original Twitter user's profile

**`stf_get_tweets($args)`**

This will be the main function used to get Tweets from the DB. This function takes an array of parameters as follows:
* `$args['num']` - This tells us how many Tweets to get from the DB, defaults to 5
* `$args['offset']` - This tells us how many Tweets to skip over when selecting our Tweets, defaults to 0
* `$args['retweets']` - This tells us whether or not to get retweets, defaults to `true`
* `$args['replies']` - This tells us whether or not to get replies, defaults to `true`
This function returns an array of STF_Tweet objects, the use of these objects is described above

== Installation ==

1. Upload `SimpleTwit` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add in your OAuth Credentials through Settings >> Twitter Feed

== Frequently asked questions ==

= How can I get this to display Tweets? =

This plugin is intended entirely for developers and therefore there's no prebuilt Twitter feed box or widget. Instead, this plugin provides the `stf_get_tweets()` function that will return an array of Tweet objects that can be easily looped through and used to build a custom Twitter feed. Making it easy for anyone with familiarity with PHP, HTML and CSS to create the perfect custom Twitter feed they were looking for.

== Screenshots ==

1. A view of the admin area settings
2. A `print_r()` of the results of `stf_get_tweets()`

== Changelog ==

= 1.1.1 =
Bugfixes:

* Fixing styling errors on the readme.txt

= 1.1.0 =
New Features:

* Added readme.txt for Wordpress.org compatibility
* Added two screenshots

= 1.0.0 =
New Features:

* Option to offset tweets returned by `stf_get_tweets()`
* Added `get_tweet_link()` method to `STF_Tweet` object to return the direct link to status
* Documentation of the usage of the plugin in the README and documented the code

Feature Updates:

* Added in a check on the API call that verified the tweet was not already in the DB before adding
* Modified some of the DateTime code to make the plugin compatible with PHP 5.2
* Modified the `stf_get_tweets()` function so that it takes a single array of parameters argument rather than a list of arugments
* Namespaced the entire plugin to use `stf_` instead of no namespacing or `st_`
* Added in `function_exists()` checks to prevent fatal conflicts with other plugins
* Amended the Access Token and Secret labeling to mirror Twitter

Bugfixes:

* Fixed an issue with the default WP timezone returning an invalid timezone string for PHP
* Fixed an error that was entering a failed call to the Twitter API into the DB
* Fixed an error that was causing tweets to import twice
* Fixed an error with the Status IDs if the DB was running on a 32 bit machine
* Fixed an issue where a Notice was being thrown by the lack of apostrophes around a string
* Fixed the description of username entry to be more clear that only one user can be entered and to not include the @

= RC1 =
Initial release of the plugin

== Upgrade Notice ==

== Further Development ==

* Database Functions (Delete, Rescrape)
* Multiple Twitter Streams
* Reminder when oAuth credentials aren't set
* Check to make sure feed can be successfully scraped and throw errors if it's not
* Define the interval between feed scrapes
* Ability to retweet and reply with your own account right from site