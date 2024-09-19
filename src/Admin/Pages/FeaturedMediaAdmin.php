<?php

namespace APIFeaturedImage\Admin\Pages;

use APIFeaturedImage\Admin\PageInterface;

class FeaturedMediaAdmin implements PageInterface
{
    public static function render(): void
    {
        ?><h2>Featured Media Src API endpoint</h2>
			<p>Enhance your WordPress REST API by adding a featured image URL field directly to API responses, improving performance by eliminating extra requests.</p>

<p><strong>REST API Featured Image</strong> is a lightweight yet powerful plugin that simplifies how to retrieve featured images via the WordPress REST API. By introducing a top-level field called <code>featured_media_src_url</code>, this plugin embeds the direct URL of the featured image into your REST API responses. This eliminates the need for additional API calls to fetch featured images, resulting in faster load times and enhanced site performance.</p>
<p><strong>Key Features:</strong></p>
<p>– <strong>Direct Access to Featured Image URL:</strong> Adds <code>featured_media_src_url</code> to REST API responses, providing immediate access to the featured image URL.
– <strong>Performance Optimization:</strong> Reduces the number of API requests, improving the speed and efficiency of your applications.
– <strong>Custom Post Type Support:</strong> Fully supports custom post types, allowing you to enable or disable the featured image URL field for specific post types through the admin settings.
– <strong>User-Friendly Configuration:</strong> Easy to install and configure without any coding.</p>
<p><strong>Why Use REST API Featured Image?</strong></p>
<p>When developing applications or themes that rely on the WordPress REST API, accessing the featured image typically requires an additional request for each post. This can be time-consuming and may negatively impact site performance. <strong>REST API Featured Image</strong> addresses this issue by including the featured image URL directly in the REST API response, saving you time and resources.</p>
<p><strong>REST API Featured Image Field:</strong></p>
<p>– <code>featured_media_src_url</code></p>

        <?php
    }

    /**
     * @return null
     */
    public static function process( ?array $_post ): ?bool
    {
        return null;
    }
}
