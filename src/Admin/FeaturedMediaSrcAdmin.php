<?php

namespace SimFeaturedMediaSrc\Admin;

use SimFeaturedMediaSrc\WPAdminPage\AdminPage;

final class FeaturedMediaSrcAdmin extends AdminPage {

	/**
	 * [public description]
	 *
	 * @var $plugin_path
	 */
	public static $plugin_path;

	/**
	 * Menu
	 *
	 * @return array
	 */
	private static function menu() {
	    $menu = array();
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
   	 * Dir path
   	 *
   	 * @return string
   	 */
  	private static function set_plugin_dir() {
    	return plugin_dir_path( __FILE__ );
  	}

	/**
	 * Submenu items
	 *
	 * @return array
	 */
	private static function submenu() {
	  	$menu = array();
	  	$menu[] = 'Post Types';
	  	// $menu[] = 'Featured Image Size'; // @codingStandardsIgnoreLine
	  	return $menu;
	}

	/**
	 * Init
	 *
	 * @return FeaturedMediaSrcAdmin
	 */
	public static function init() {
	    return new FeaturedMediaSrcAdmin( self::menu(), self::submenu() );
	}
}
