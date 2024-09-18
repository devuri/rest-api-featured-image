<?php

namespace APIFeaturedImage\Admin;

use APIFeaturedImage\WPAdminPage\AdminPage;

final class PluginAdmin extends AdminPage
{
    /**
     * [public description].
     *
     * @var
     */
    public static $plugin_path;

    /**
     * Init.
     *
     * @return PluginAdmin
     */
    public static function init()
    {
        return new self( self::menu(), self::submenu() );
    }

    /**
     * Menu.
     *
     * @return array
     */
    private static function menu()
    {
        $menu = [];
        $menu['pro']         = false;
        $menu['page_title']  = 'API Featured Media';
        $menu['menu_title']  = 'Featured Media';
        $menu['capability']  = 'manage_options';
        $menu['menu_slug']   = 'api-featured-media-src';
        $menu['function']    = 'api_featured_media';
        $menu['icon_url']    = 'dashicons-format-image';
        $menu['position']    = null;
        $menu['prefix']      = 'afms';
        $menu['plugin_path'] = plugin_dir_path( __FILE__ );

        return $menu;
    }

    /**
     * Dir path.
     *
     * @return string
     */
    private static function set_plugin_dir()
    {
        return plugin_dir_path( __FILE__ );
    }

    /**
     * Submenu items.
     *
     * @return array
     */
    private static function submenu()
    {
        $menu = [];
        $menu[] = 'Post Types';

        // $menu[] = 'Featured Image Size'; // get_intermediate_image_sizes() @codingStandardsIgnoreLine
        return $menu;
    }
}
