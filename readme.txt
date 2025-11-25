=== Simple Socials ===
Contributors: headwalluk
Donate link: https://power-plugins.com/plugins/simple-socials/
Tags: widget, socials
Requires at least: 5.0
Tested up to: 5.7.2
Stable tag: 1.0.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple social media widget and buttons.

== Description ==

Configure your social networks once, and use them everywhere.

* Pulls your social network settings from Yoast.
* Easy to extend and customise.
* Use FontAwesome icons, or bring your own.
* Very lightweight.

== Installation ==

In the WordPress Admin area...

1. Plugins
2. Add New
3. Upload Plugin
4. Select the latest `simple-socials.zip` file and upload it.
5. Activate the plugin.

After the plugin has been activated, you will be prompted for the license key.
The plugin will work fine without the activation key, but you will not receive
any updates unless the activation key has been entered. You can get your
activation key from your account area on Poewr Plugins:

[My Power Plugins](https://power-plugins.com/my-account/my-plugins/)

== Changelog ==

= 1.1.0 ==
*Released 6th November 2021*

* Add built-in support for showing a phone number.
* Small adjustment to the CSS to better v-center FA large icons.

= 1.0.7 ==
*Released 11th July 2021*

* Add built-in support for showing default RSS2 feed icon.
* Add a the social_is_enabled_$network action to easily disable a social
  with __return_false.

= 1.0.6 ==
*Released 10th July 2021*

* Made the require_once statements more robust in the plugin main file.
  Only really affects people who use wp-cli to interact with WordPress.

= 1.0.5 ==
*Released 29th June 2021*

* Switch between FontAwesome 4 and FontAwesome 5 with a filter.
  *Important* FontAwesome doesn't ship with this plugin. It is assumed that your
  site already has FA4 or FA5 available.

= 1.0.4 ==
*Released 21st June 2021*

* Pick and choose which socials to show in individual widgets.

= 1.0.3 ==
*Released 20th June 2021*

* Added smps_get_social_url() to functions.php so you can grab social network
* URLs from your own code.
* Minor tweaks and revisions to support the documentation on Power Plugins.

= 1.0.0 =
*Released 19th June 2021*

* Initial release.
