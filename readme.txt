=== Bid13 Auction Feed ===
Contributors: deweydb
Tags: wordpress, plugin, template
Requires at least: 3.9
Tested up to: 5.7.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin uses the bid13.com API to pull a feed of auctions and display them.

== Description ==

This plugin uses the bid13.com API to pull a feed of auctions. These auctions can be printed out in a number of customizable ways on your site using a short code: [auction_feed]

== Installation ==

Installing "Bid13 Auction Feed" can be done either by searching for "Bid13 Auction Feed" via the "Plugins > Add New" screen in your WordPress dashboard, or by using the following steps:

1. Download the plugin via WordPress.org
2. Upload the ZIP file through the 'Plugins > Add New > Upload' screen in your WordPress dashboard
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Contact auctions@bid13.com to get your API key.
5. Under Settings->Bid13 Settings, enter your API key & click save.
6. Check the facilities you would like to display auctions from & click save again.
7. (Optional) Edit the settings on the Customization Tab
8. (Optional) Edit the Auction specific settings on the auction specific tabs.
9. Create a new page or post, and add the shortcode: [auction_feed]

== Screenshots ==

1. Screenshot of settings page

== Frequently Asked Questions ==

= How do I start using this plugin? =

You will need an API key, please contact our support department at auctions@bid13.com and they will help you get setup.

== Changelog ==
= 1.26 =
* 2021-05-04
* Update: Compress assets

= 1.25 =
* 2021-05-04
* Update: Color picker was not saving values correctly
* Update: Restrict what areas of the admin engage the plugin

= 1.24 =
* 2021-02-18
* Update: Security audit.

= 1.23 =
* 2021-02-18
* Update: Update to be compatible with Wordpress 5.6+

= 1.22 =
* 2020-07-22
* Bufix: Cancelled auctions were still displaying as active auctions in city/state sorted view

= 1.21 =
* 2020-06-24
* Bufix: Transition to V2 API broke loading of expired auctions

= 1.20 =
* 2020-02-19
* Feature: Upgraded to V2 API, loads auctions much faster now
* Feature: Minor change to french translations

= 1.19 =
* 2020-02-19
* Bugfix: Bugfix for V1.18, and minor display issue fix.

= 1.18 =
* 2020-02-19
* Bugfix: Fix vimeo video player issue

= 1.17 =
* 2020-01-14
* Feature: Option to disable no results newsletter link.
* Bugfix: Development mode now authenticates with staging server using http authentication.

= 1.16 =
* 2020-01-10
* Bugfix: Minor french translation fixes.

= 1.15 =
* 2020-01-09
* Bugfix: Fix missing translations for countdown timers.

= 1.14 =
* 2020-01-08
* Feature: Add french translation of plugin output.

= 1.13 =
* 2020-01-07
* Bugfix: add curl timeout

= 1.12 =
* 2020-01-07
* Feature: add new sorted view for bigger chains
* Feature: Added caching to facility API to reduce loadtime.

= 1.11 =
* 2020-01-02
* Feature: add development mode

= 1.10 =
* 2018-06-28
* Bug Fix: Fix warnings

= 1.06 =
* 2017-08-17
* Bug Fix: More bugfixes for php < 5.3.x

= 1.05 =
* 2017-08-17
* Bug Fix: Add support for php < 5.3.x

= 1.04 =
* 2017-08-16
* Bug Fix: Some users experiancing improper video embeds in auction feed
* New Feature: Sort by date ASC/DESC option

= 1.03 =
* 2017-03-20
* Update plugin to support new bid13 API

= 1.02 =
* 2016-08-05
* Bug fix: fixed Jquery no conflic issue in frontend.js
* Bug fix: curl close issue.
* New feature: Disable youtube related content at end of video

= 1.01 =
* 2016-08-04
* Bug fixes
* New feature: Videos instead of images option
* New feature: Ability to edit descriptions
* New feature: Ability to upload custom photos
* New feature: Links open in new window

= 1.0 =
* 2012-12-13
* Initial release

== Upgrade Notice ==

= 1.0 =
* 2012-12-13
* Initial release