=== Chunks ===
Contributors: kovshenin
Donate link: http://kovshenin.com/wordpress/plugins/chunks/
Tags: chunks, theme, utility
Requires at least: 2.8
Tested up to: 3.1.3
Stable tag: 1.1

Chunks is about managing tiny bits of content on your WordPress site.

== Description ==

Chunks is for theme developers that have their themes filled with footer notes, copyright notices, block titles and descriptions, slogans, etc, which are sometimes hard-coded into the theme, sometimes localized (can be changed in po and mo files) and sometimes taken out to the theme options.

Chunks will do the job for you. A "chunk" is a piece of HTML code that could be inserted anywhere in your theme and edited from the Theme Chunks page under Appearance in your admin panel. Use register_chunks() in your functions.php to register chunks for your theme and use the chunk() to get the chunk value anywhere in your template files.

It'll take you 5 minutes to implement Chunks in your theme: [Getting Started with Chunks](http://kovshenin.com/wordpress/plugins/chunks/ "Getting Started with Chunks")

== Installation ==

1. Upload archive contents to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Modify your theme to [implement chunks](http://kovshenin.com/wordpress/plugins/chunks/ "implement chunks").
1. Look for the 'Theme Chunks' option under your 'Appearance' menu.

== Frequently Asked Questions ==

= Can I use chunks in my posts or pages? =
Of course, try out the chunk shortcode!

= Can I use HTML in my Chunks? =
Yes.

= Can I use Javascript in my Chunks? =
Yes, but be careful.

== Screenshots ==

1. The Theme Chunks screen

== Change log ==

= 1.1 =
* Chunk shortcode implemented
* Cleanup and coding style

= 1.0 =
* Chunks went from personal project to public!
