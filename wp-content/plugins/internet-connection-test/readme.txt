=== Internet Connection Test ===
Contributors: aabweber
Tags: speed test, internet test, widget
Stable tag: trunk
Requires at least: 3.0.1
Tested up to: 3.8
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This plugin allows your users to test internet connection speed.

== Description ==

This plugin allows you to place a widget for internet connection speed test.
If your theme doesn't support widgets you can insert directly to your theme code something like that:

`<?php showSpeedTestHTML();?>`

or

`<?php echo getSpeedTestHTML();?>`

See installation section to learn how to change widget template.



== Installation ==

1. Upload `internet-connection-test` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Appearance -> Widgets in Admin Panel and you will see a new widget
4. Drag and drop it to appropriate section, save
5. If your theme doesn't support widgets - add `<?php showSpeedTestHTML();?>` in your theme templates
6. To change widget template copy spt-widget.php file from plugin directory to you theme directory, edit it as you want.

== Frequently Asked Questions ==


== Screenshots ==

== Changelog ==

= 0.3 =
Number of downloads limited to 5 then test will stops.

= 0.2 =
* Customizable labels
* Customizable template
* CSS bug fixes

= 0.1 =
* Initial release

== Upgrade Notice ==

== Arbitrary section ==


== A brief Markdown Example ==

