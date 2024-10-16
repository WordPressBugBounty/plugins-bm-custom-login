=== Custom Login ===
Contributors: teydeastudio, bartoszgadomski, BinaryMoon
Tags: custom login, login, login customizer, customize login, branding
Requires at least: 4.7
Tested up to: 6.6
Stable Tag: 2.4.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Customise the WordPress login box quickly and easily

== Description ==

A simple way to customise the login screen on your WordPress install. There are a number of advantages to using this plugin:

1. Using a plugin means the changes stay when you upgrade
2. It's an easy way to add a little polish to your freelance web design jobs

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the entire contents of the zip file to your plugin directory "/wp-content/plugins/"
2. Activate the plugin through the "Plugins" menu in WordPress
3. Create a background image and then upload that to your website as well
4. Go to the plugins options page in the WordPress admin ("Settings" -> "Custom Login") and enter the url for you login background image
5. Change the colors as you desire
6. Press save
7. Bask in the glory of your beautiful new login screen

== Changelog ==

= 2.4.0 (2024-10-16) =
* Create changelog.txt file and add all missing records
* Update readme.txt file, list of contributors, tags, and the plugin author data
* Remove promotional contents
* Drop csstidy entirely and use similar approach for css sanitization as in WordPress core
* Remove invalid plugin uri header
* Bump tested-up-to version to WordPress 6.6
* Fix security issues
* Load plugin stylesheet through `login_enqueue_scripts` hook

= 2.3.2 (2021-07-14) =
* Fix errant comma that caused a 500 error on some servers

= 2.3.1 (2021-07-13) =
* Fix PHP error for missing settings

= 2.3 (2021-03-09) =
* Display the "powered by" text underneath the login form to ensure it is visible. It can be targetted with CSS using `.cl-powered-by`

= 2.2.5 (2020-06-17) =
* Fix implode parameter order

= 2.2.4 (2020-03-05) =
* Update CSSTidy to latest version

= 2.2.3 (2019-08-23) =
* Switch to submit_button function for settings form

= 2.2.2 (2019-04-30) =
* Replace deprecated filter
* Update coding standards

= 2.2.1 (2017-12-01) =
* Remove text shadow on login button so that it's more consistently readable
* Make it clearer what the text link colour changes
* Change CSS label to match the core customizer label
* Remove CSS vendor prefixes that are no longer needed

= 2.2 (2017-11-30) =
* Fix default colour values so that the default button works properly
* Fix PHP 7 warnings in CSSTidy
* Sanitize more things for extra security
