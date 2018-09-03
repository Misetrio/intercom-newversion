=== Internet Speed Test ===
Contributors: vyskoczilova, meternet
Tags: connection speed, connection test, speed, speed test, bandwidth, bandwidth test
Requires at least: 4.6
Tested up to: 4.9
Stable tag: /trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The plugin allows you to embed speed test for your website via a shortcode.

See [live demo here](https://www.meter.net/web-plugin/).

== Description ==

=== Features ===
* Measure download, upload, ping and jitter.
* Autodetects users language preference (or you can choose one)
* Modern, responsive, full HTTPS support
* Available in 12 languages: Czech (cs), Deutsch (de), English (en), French (fr), Hungarian (hu), Italin (it), Japanese (ja), Polish (pl), Portuguese (pt), Rusian (ru), Slovak (sk), Spanish (es).

The [Meter.net](https://www.meter.net) project has been measuring Internet access around the world since 2014 (on former domain bandwidth-test.net since 2005, and main Czech version since year 2004). Our users carry out tens of thousands of tests every day.

=== How to ===
Place `[internet-speed-test]` shortcode whenever you want to display the speed test. You can use following parameters to modify the look:

* **layout** - dark (default), light
* **language** - auto (automatic detection based on browser, default), wordpress (based on wordpress locale), manually asign a language (cs, de, en, es, fr, hu, it, ja, pl, pt, ru, sk)
* **default-language** - manually assign a fallback language for languages based on wordpress locale if the locale language is not supported, otherwise automatic detection will be undreleased

==== Examples of use ====
* Dark theme, automatic detection `[internet-speed-test]`
* Light theme with wordpress based language `[internet-speed-test layout="light" language="wordpress"]`
* Dark theme with wordpress based language with fallback to Spanish  `[internet-speed-test language="wordpress" default-language="es"]`

== Installation ==

1. Upload the plugin to your web site or install via plugin management.
1. Check whether the WooCommerce plugin is installed and active.
1. Activate the plugin through the `Plugins` menu in WordPress administration
1. Add a shortcode where you want to display the speed tester.
1. Done!

== Changelog ==

= 1.0.0 (2018-08-06) =
* Initial release