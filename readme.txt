=== REST API Featured Image ===
Contributors: icelayer
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6D6W2KXB88NKE
Tags: rest api, featured image, wordpress rest api, featured image url, api performance, json, post thumbnail
Requires at least: 4.7.0
Tested up to: 6.6
Stable tag: 0.9.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Enhance your WordPress REST API by adding a featured image URL field directly to API responses, improving performance by eliminating extra requests.

== Description ==

**REST API Featured Image** is a lightweight yet powerful plugin that simplifies how to retrieve featured images via the WordPress REST API. By introducing a top-level field called `featured_media_src_url`, this plugin embeds the direct URL of the featured image into your REST API responses. This eliminates the need for additional API calls to fetch featured images, resulting in faster load times and enhanced site performance.

**Key Features:**

– **Direct Access to Featured Image URL:** Adds `featured_media_src_url` to REST API responses, providing immediate access to the featured image URL.
– **Performance Optimization:** Reduces the number of API requests, improving the speed and efficiency of your applications.
– **Custom Post Type Support:** Fully supports custom post types, allowing you to enable or disable the featured image URL field for specific post types through the admin settings.
– **User-Friendly Configuration:** Easy to install and configure without any coding.

**Why Use REST API Featured Image?**

When developing applications or themes that rely on the WordPress REST API, accessing the featured image typically requires an additional request for each post. This can be time-consuming and may negatively impact site performance. **REST API Featured Image** addresses this issue by including the featured image URL directly in the REST API response, saving you time and resources.

**REST API Featured Image Field:**

– `featured_media_src_url`

== Installation ==

1. **Upload the Plugin Files:**
   – Upload the `rest-api-featured-image` folder to the `/wp-content/plugins/` directory.
   – Or install the plugin directly through the WordPress plugins screen.

2. **Activate the Plugin:**
   – Activate the plugin through the 'Plugins' screen in WordPress.

3. **Configure Plugin Settings:**
   – Navigate to the plugin settings page in the WordPress admin area.
   – Select the post types for which you want to enable or disable the featured image URL field.

== Frequently Asked Questions ==

= How do I access the featured image URL from the REST API? =

After installing and activating the plugin, the REST API responses for your selected post types will include a new field named `featured_media_src_url`. This field contains the direct URL to the featured image.

= Does this plugin support custom post types? =

Yes, the plugin fully supports custom post types. You can enable or disable the featured image URL field for any post type via the plugin's settings in the admin area.

= Will this plugin improve my site's performance? =

Absolutely. By eliminating the need for additional API requests to fetch featured images, your site's performance and load times will improve.

= Do I need any coding knowledge to use this plugin? =

Not at all. The plugin is user-friendly and requires no coding skills. Simply install, activate, and configure your settings.

== Screenshots ==

1. **Plugin Settings Page:** Easily select which post types to include or exclude from the featured image URL field.
