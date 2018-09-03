=== Plugins Speed Test ===
Contributors: d3wp
Donate link: http://www.wpspeedster.com
Tags: plugin,plugin speed test
Requires at least: 3.0.1
Tested up to: 4.1.1
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin shows impact of installed plugins on your blogs' speed.

== Description ==
This used, shows impact of installed plugins (only for those located in the official WordPress plugin repository) on your blogs'
speed.  In other words, with this plugin, you can easily determine which plugins from the WordPress
plugin repository are less resource intensive, and which ones can slow down your WordPress blog when used.

The plugin shows four results (see screenshot):

* Impact of the installed plugin on Google PageSpeed score for blogs' Home page
* Impact of the installed plugin on Google PageSpeed score for blogs' sample Post page
* Resources added to post page after plugin has been installed (in kB)
* Number of DB tables created by the plugin.

More than 25.000 plugins were tested lately.

Every plugin was tested on a fresh WordPress installed instance populated with a sample content.
No additional plugins were installed to avoid any unwanted impact on the speed test.

NOTE:
This plugin calls [wpspeedster.com](http://www.wpspeedster.com) API to obtain necessary information about Speed Impact details.
Only plugin slugs are send to the API, no additional or personal information is send.

== Installation ==

1. Upload `plugins-speed-test` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to ‘Plugins -> Installed Plugins’ to view "Speed Impact" column in plugins list table.

== Frequently Asked Questions ==

= How many plugins were tested? =

More than 20.000 plugins were tested.

== Screenshots ==

1. "Speed Impact" column in plugins list table.

== Changelog ==

= 1.0 =
* Initial version.

== Upgrade Notice ==

= 1.1 =
* Fixed bug when the test results didn't show up.

= 1.0 =
* Initial version.
