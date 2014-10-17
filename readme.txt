=== SimpleTwit ===
Contributors: GYatesIII
Donate link:
Tags: twitter feed, developer, designer, widget, simple, oauth, caching, cache, twitter, feed, template tag
Requires at least: 3.3
Tested up to: 3.5.2
Stable tag: 1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Everything a developer or designer needs to pull in a Twitter feed. All in a slim package that won't get in the way of your creativity.

== Description ==

A plugin for developers and designers that sets up a WP_Cron to pull in and cache a user's stream using OAuth and the Twitter v1.1 REST API. It's all that a developer needs to incorporate a Twitter feed on their site, the OAuth handling, caching to avoid rate limiting, and utilities to easily format Tweets correctly without predefined styles to work around. For designers, the plugin creates a widget that can be used to easily display and style Tweets in your theme.

= Features =
* Provides access to a user's Tweets for both designers and developers
* Creates a widget for easy display of the latest Tweets in your theme's sidebar
* Clearly designates Retweets and Replies to allow easy styling and manipulation
* Easily set Username and OAuth credentials
* Caches Tweets to prevent rate limiting problems
* Hooks into WP_Cron for easy installation and automatic API calls
* Uses OAuth and the v1.1 Twitter REST API

### Usage ###

There are two main ways to access the Tweets, one aimed at developers and one aimed at designers.

= For Designers =

This plugin creates a widget that allows display of the most recent Tweets in any sidebar in your theme. When adding the widget you can customize how many Tweets and what information is displayed, choosing from content, time, author, and source. The widget outputs HTML5 which with classes to style on every element, including flagging Tweets as Retweets and Replies. You will be able to style this widget to look exactly as you need it it to.

= For Developers =

For those with something special in mind and willing to get their hands dirty, this plugin provides powerful access to the DB of Tweets. Tweets can be grabbed through a template tag and are provided in a special object jam packed with features:

**`STF_Tweet`**

An array of these objects is returned by the template tag instead of the `WP_Post` object, or an individual can be constructed by passing the post ID of the Tweet to the object constructor.

The object provides a number of useful methods when working with Tweets. This object has the following accessible properties:

* `is_retweet` - This boolean will be `true` when the Tweet is a Retweet
* `is_reply` - This boolean will be `true` when the Tweet is a Reply to another Tweet
* `content` - The content of the Tweet, this will be automatically formatted to link other referenced Twitter users, hashtags, and inline links
* `time` - A timestamp of the Tweet, timezoned to the WP install, in Y-m-d H:i:s format
* `time_gmt` - A timestamp of the Tweet, timezoned to GMT, in Y-m-d H:i:s format
* `time_str` - The string that represents how long it's been since the Tweet, in the way that Twitter usually dates its Tweets, good for an international audience since this isn't timezone specicific

The object has the following methods:

* `get_source()` - Returns the string representing the device used to Tweet this status
* `get_raw_tweet()` - Returns the cached raw response from the API as an object, this should rarely be used as almost all information on the Tweet is accessible without loading this object
* `get_author_link()` - Returns a string that is the link to the Tweet's author's page on Twitter
* `get_retweet_info()` - Returns the info on the original Tweet of a Retweet, or false if the Tweet is not a Retweet, the object returned contains:
  * `username` - The Twitter username of the original Tweet
  * `screenname` - The Twitter screenname of the original Tweet
  * `content` - The _unformatted_ content of the original Tweet
  * `time_gmt` - The GMT time of the Retweet
  * `url` - A direct link to the original Tweet
  * `user_url` - A direct link to the profile of the original Twitter user
* `get_reply_info()` - Returns the info on the original status that this Tweet is replying to, the info is as follows:
  * `url` - The direct link to the original status
  * `in_reply_to_name` - The screenname of the original Twitter user
  * `in_reply_to_user_url` - The direct link to the original Twitter user's profile
* `get_author_info()` - Gets the raw object response from the Twitter API scrape, there are a lot of variables in the raw object, but here's the main attributes:
  * `id_str` - The id of the user
  * `name` - The nice name of the author account
  * `screen_name` - The username of the author account
  * `description` - The self-provided description of the author on Twitter
  * `created_at` - The creation date of the Twitter author account
  * `profile_image_url` - Link to the profile image of the Twitter author account
  * `profile_image_url_https` - Secure link to the profile image of the Twitter author account

**`stf_get_tweets($args)`**

This will be the main function used to get Tweets from the DB. This function takes an array of parameters as follows:

* `$args['num']` - This tells us how many Tweets to get from the DB, defaults to 5
* `$args['offset']` - This tells us how many Tweets to skip over when selecting our Tweets, defaults to 0
* `$args['retweets']` - This tells us whether or not to get Retweets, defaults to `true`
* `$args['replies']` - This tells us whether or not to get Replies, defaults to `true`
This function returns an array of STF_Tweet objects, the use of these objects is described above

== Installation ==

1. Upload `SimpleTwit` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Twitter's Developer Center](https://dev.twitter.com/) and setup your Twitter app to get your OAuth credentials
4. Add in your OAuth credentials through Settings >> Twitter Feed
5. The plugin will scrape the API right away and let you know if there were any problems with the information provided

== Frequently asked questions ==

= How can I get this to display Tweets? =

This plugin is intended entirely for developers and therefore there's no prebuilt Twitter feed box or widget. Instead, this plugin provides the `stf_get_tweets()` function that will return an array of Tweet objects that can be easily looped through and used to build a custom Twitter feed. Making it easy for anyone with familiarity with PHP, HTML and CSS to create the perfect custom Twitter feed they were looking for.

== Screenshots ==

1. A view of the admin area settings
2. A `print_r()` of the results of `stf_get_tweets()`
3. Options for the widget in the admin area

== Changelog ==

### 1.3 ###
#### Feature Updates ####
* Added in support for Twitter's $ (Cash) tag

### 1.2.2 ###
#### Bugfixes ####
* Fixed bug that was precenting Tweets from pulling through WP_Cron

### 1.2.1 ###
#### Bugfixes ####
* Fixed a bug with the time string not handling time zones correctly and displaying the wrong information

***

### 1.2.0 ###
#### New Features ####
* Added a widget that uses the DB information to display most recent Tweets
* Now when the username is changed, it deletes all the Tweets and rescrapes Twitter using the new username
* When the OAuth credentials are changed, it runs an import to check if the new credentials are valid
* Added admin notices for incomplete or erroneous OAuth credentials

#### Feature Updates ####
* Added in `STF_Tweet->get_author()` method to provide detailed information on the Tweet's author
* Changed `STF_Tweet->get_raw_tweet()` to return an object instead of associative array
* Added in `STF_Tweet->get_author_link()` to get the direct link to the Tweet author's page

#### Bugfixes ####
* Fixed a bug where the time string was not adjusting for timezone differences and causing funky time reporting

***

### 1.1.1 ###
#### Bugfixes ####
* Fixed styling errors in the readme.txt

***

### 1.1.0 ###
#### New Features ####
* Added readme.txt for Wordpress.org compatibility
* Added two screenshots

***

### 1.0.0 ###
#### New Features ####
* Added in option to offset the Tweets returned by `stf_get_tweets()`
* Added in `get_tweet_link()` method to `STF_Tweet` object to return the direct link to status
* Added in documentation of the usage of the plugin in the README and in the code

#### Feature Updates ####
* Added in a check on the API call that verified the Tweet was not already in the DB before adding
* Modified some of the DateTime code to make the plugin compatible with PHP 5.2
* Modified the `stf_get_tweets()` function so that it takes a single array of parameters argument rather than a list of arugments
* Namespaced the entire plugin to use `stf_` instead of no namespacing or `st_`
* Added in `function_exists()` checks to prevent fatal conflicts with other plugins
* Amended the Access Token and Secret labeling to mirror Twitter

#### Bugfixes ####
* Fixed an issue with the default WP timezone returning an invalid timezone string for PHP
* Fixed an error that was entering a failed call to the Twitter API into the DB
* Fixed an error that was causing Tweets to import twice
* Fixed an error with the Status IDs if the DB was running on a 32 bit machine
* Fixed an issue where a Notice was being thrown by the lack of apostrophes around a string
* Fixed the description of username entry to be more clear that only one user can be entered and to not include the @

***

### RC1 ###
* The initial release of the plugin.

== Contributors ==
* [George Yates III](https://github.com/GYatesIII)
* [alexsomeoddpilot](https://github.com/alexsomeoddpilot)

== Upgrade Notice ==

== Future Development ==

* Database Functions (Delete, Rescrape)
* Multiple Twitter Streams
* Define the interval between feed scrapes
* Allow users to Retweet and Reply with their own accounts right from your site
* Add in shortcode support for recent Tweet display