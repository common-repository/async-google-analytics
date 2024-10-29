=== Asynchronous Google Analytics for WordPress ===
Contributors: Bambang Sugiarto
Donate link: http://www.minilibra.com/donate/
Tags: analytics, async, asynchronous, google analytics, asynchronous google analytics, statistics, multisite, multi site
Requires at least: 2.7
Tested up to: 3.1
Stable tag: 3.0.7

SUPPORT MULTI SITE NOW!! The Asynchronous Google Analytics for WordPress plugin is Google Analytics plugin that support asynchronous tracking method.

== Description ==

The Asynchronous Google Analytics for WordPress plugin allow google analytics asynchronous tracking method. You can enable/disable tracking for specified categories, tags, pages, posts, URL, visitor with specified IP, MULTIPLE User Role as well as multiple by User ID, Username, and or User email.

If you want to exclude yourself from being tracking, then you can add your own IP in the plugin option.

Multisite FAQ: Please read the FAQ section and changelog for the plugin updates.

NEW START FROM VERSION 3.0.0 :

* Support WordPress Multisite
* Easy configuration for sub-domains tracking.
* All in one easy panel access and configuration in Main Site as well as flexible configuration and setup per site / sub-domains.
* IMPORTANT NOTE: Have been tested in multisite environment using sub-domains. And never been tested on multisite environment using sub-directory.

This plugin also automatically tracks and segments all outbound links from within posts, comment author links, links within comments, blogroll links and downloads, allows you to track AdSense clicks, add extra search engines, track image search queries and it will even work together with Urchin, and the important things that this plugin now using new asynchronous tracking method instead of traditional one.

In the options panel for the plugin, you can determine the prefixes to use for the different kinds of outbound links and downloads it tracks. You can also activated or deactivated tracking method (use new asynchronous method or use traditional method).

Plugin News and Updates:

* [Asynchronous Google Analytics for WordPress](http://www.minilibra.com/wordpress/plugins/analytics.html).

Follow my blogs at:

* [Professional Web Developer & WordPress Expert](http://www.minilibra.com/).
* [Web Design Jakarta - satublogs.com](http://www.satublogs.com/).

== Installation ==

This section describes how to install the plugin and get it working.

1. Delete any existing `gapp` or `google-analytics-for-wordpress` or `async-google-analytics` folder from the `/wp-content/plugins/` directory
1. Upload `async-google-analytics` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to the options panel under the 'Settings' menu and add your Analytics account number and set the settings you want.

== Changelog ==

= 3.0.7 =
* Compatibility updates to WP 3.1.

= 3.0.5 =
* add 'is_multisite' function detection to prevent plugin activation error on wordpress prior of version 3. Thanks to Mars.

= 3.0.4 =
* Fix bug: 1 JS error in IE8/Windows (Conflict JS with WP Carousel). Thanks for artur & jgravelle for the report.

= 3.0.3 =
* Fix bug: custom filter didn't working and always return FALSE when php disallow execution in the plugin path.
* IMPORTANT!! If you have been upgraded the plugin to version 3.0.x, please update to fix this bug.

= 3.0.2 =
* Fix bug in aga_tools when 'allow_url_fopen' setting was disable. Thanks for Den.

= 3.0.1 =
* Following couple of fixs (on WP mutisite environment only, while on non-multisite the plugin working as usual):
* Fix Global Configuration issues in Multisite Features (Thank you so much for Victor for his feedback).
* Support ga_debug.js for Google Analytics debugging purposed (for developer and advanced user only). Plese see the asyncgoogleanalytics.php file (in line 16-17).

= 3.0.0 =
* Support WordPress Multisite
* Easy configuration for sub-domains tracking.
* All in one easy panel access and configuration in Main Site as well as flexible configuration and setup per site / sub-domains.
* IMPORTANT NOTE: Have been tested in multisite environment using sub-domains. And never been tested on multisite environment using sub-directory.

= 2.0.3 =
* New Features Released.
* Enabled / disabled tracking for specified MULTIPLE User Role as well as multiple by User ID, Username, and or User email.
* Automatically detected all outbound links that found in any web pages sections; event for static links, such as links defined in your wordpress themes, widgets, etc.
* Smartest link detection, rules detection, and enhanced & optimized javascript code to dealing with Asynchronous Google Analytics javascript codes.

= 2.0.2 =
* Fix disabled credit link option in plugin panels. Thanks to Bradford for the correction.

= 2.0.1 =
* Fix little bug for link tracking in rss feeds.

= 2.0.0 =
* New Features Preparation for more filters tracking.

= 1.0.9 =
* Javascript Optimization.

= 1.0.8 =
* Fix the ampersand & with & amp; in the tracking URL.
* Round the javascript code with CDATA section for validating DOCTYPE XHTML strict.

= 1.0.7 =
* Optimize filter tracking for specified categories, tags, pages, posts, URL, or IP proceed on server side by async javascript call. Make the plugin compatible with any cache plugin (WPSuperCache, W3 Total Cache, etc).
* Fix _gaq instance from private function scope to public.
* Support wildcard IP in enable/disable tracking option. Allow dynamic IP to be excluded from tracking.

= 1.0.6 =
* Added new feature. Now you can enable/disable tracking for specified categories, tags, pages, posts, URL, or visitor with specified IP. If you want to exclude yourself from being tracking, then you can add your own IP in this option.

= 1.0.5 =
* Fix JavaScript bugs on Chrome.

= 1.0.4 =
* Fix warning on $_SERVER,$_GET,$_POST. I know a notice isn't critical but i'd like to keep it tidy.

= 1.0.3 =
* JavaScript improvement.

= 1.0.1 =
* Fix warning on "Latest news from plugin development" in config panel.
* Adding latest news from plugin support forum and development rodemap into config panel.

= 1.0.0 =
* First released.

== Frequently Asked Questions ==

= I've been using / upgrading the plugin in non-multisite features, does the latest version have any side effect? =
No it's not. The latest version was "adding" multisite support/capabilities while in non-multisite the plugin will working as usual before version 3 (plus some of bugs fixing, additional reach features, etc.. off course).

= How the plugin's work between non-multisite and multisite WP? =
The plugin will detected your WP environment automatically and will know did you have been using non-multisite or multisite WP, and will open their features based on the detected WP environment.
In non-multisite WP you just get 1 configuration panel while in multisite WP you will get 1 configuration panel (Global Configuration) plus additional number of configuration panels for each domain inside your WP network (Specific Domain Configuration) when you activated the plugin Side-Wide.
All configuration (either global and or specific domain configuration are manageable under your WP main site), as well as the Specific domain configuration is manageable under their domain's administrator dashboard.

= I've been installing the plugin in WP multisite, but looks like i just can change the UA and domain name option for Global configuration only, while for another domain (except the main domain) i can't change the UA and or domain name. Why? =
This is probably because the "Track Sub Domains And Merged into One Analytics Account ID" option (in Global Configuration section) has been activated.
If this option activated (checked) then it's mean that you have been choose to using 1 UA and Domain name for all your domains inside your multisite.
That's why the UA and or the domain name except the main domain can not set the UA and or domain name itself.
If you want each domains or one or more another domains inside your WP network have their own UA then you have to disabled this option (keep unchecked).

= I've been installing the plugin in WP multisite, but I just see one panel option. Why? =
1. Make sure you have been activated the plugin side-wide.
2. You didn't have any network yet. Create another network, and the plugin will show the specific configuration panel for this domain.

= How to Find My Google Analytics Tracking ID (UA)? =

1. Login to your Google Analytics at <a href="https://www.google.com/analytics/" target="_blank">https://www.google.com/analytics/</a>
2. After Login, Go to Google Analytics home by clicking the Google logo in the left top corner of the page
3. <a href="http://203.174.82.154/~millionb/_vti_mnl/uploads/2010-09-06_1158.png" target="_blank">Click here</a> to see and found which one is your Tracking ID

= How to exclude one or more external links from being tracking as outbound clicks? =

It is simple and easy. Just assign your links with 'aga_no' css classname.
Example: &lt;a href=&quot;http://external-domain.com&quot; class=&quot;aga_no&quot;&gt;external-domain&lt;/a&gt;

= This inflates my clicks, can I filter those out? =

Yes you can, create a new profile based on your original profile and name it something like 'domain - clean'. For each different outbound clicks or download prefix you have, create an exclude filter. You do this by:

1. choosing a name for the filter, something like 'Exclude Downloads';
2. selecting 'Custom filter' from the dropdown;
3. selecting 'Exclude';
4. selecting 'Request URI' in the Filter Field dropdown;
5. setting the Filter Pattern to '/downloads/(.*)$' for a prefix '/downloads/';
6. setting case sensitive to 'No'.

For some more info, see the screenshot under Screenshots.

= Can I run this plugin together with another Google Analytics plugin? =

No. You can not. It will break tracking. As example, you can not install this plugin together with "Google Analytics" plugin by joostdevalk. Since this plugin already covered all his plugin features, then you just need to install this plugin. In the next released, all features request will be updated into this plugin. Just drop your comments or feedbacks in the <a href="http://wordpress.org/tags/async-google-analytics?forum_id=10">plugin support forum</a> or in the <a href="http://www.minilibra.com/wordpress/plugins/analytics.html#utm_source=wordpress&utm_medium=plugin&utm_campaign=async-google-analytics">plugin homepage</a>.

== License ==

Good news, this plugin is free for everyone! Since it's released under the GPL, you can use it free of charge on your personal or commercial blog. But if you enjoy this plugin, you can thank me and leave a [small donation](http://www.minilibra.com/donate/ "Donate with PayPal") for the time I've spent writing and supporting this plugin. And I really don't want to know how many hours of my life this plugin has already eaten ;)

