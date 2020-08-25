<?php
/**
 * API Featured Image
 *
 * @package           APIFeaturedImage
 * @author            Uriel Wilson
 * @copyright         2020 Uriel Wilson
 * @license           GPL-2.0
 *
 * @wordpress-plugin
 * Plugin Name:       WP API Featured Media Source
 * Plugin URI:        https://switchwebdev.com/wordpress-plugins/
 * Description:       This plugin will add the featured image src url field to the WordPress Rest API.
 * Version:           0.4.0
 * Requires at least: 3.4
 * Requires PHP:      5.6
 * Author:            SwitchWebdev.com
 * Author URI:        https://switchwebdev.com
 * Text Domain:       api-featured-image-url
 * Domain Path:       languages
 * License:           GPLv2
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

 	# deny direct access
	if ( ! defined( 'WPINC' ) ) {
		die;
	}

 	# plugin directory
	define("APIFI_VERSION", '0.4.0');

 	# plugin directory
	define("APIFI_DIR", dirname(__FILE__));

 	# plugin url
	define("APIFI_URL", plugins_url( "/",__FILE__ ));

	/**
   * Load composer
   */
  require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

	/**
   * setup the admin page
   * @var [type]
   */
  SimFeaturedMediaSrc\Admin\FeaturedMediaSrcAdmin::init();

	/**
	 * initialize Add_Featured_Image_Src
	 *
	 * TODO add option to change the image size for output ('thumbnail', 'medium', 'large')
	 */
 	$src_field = new SwitchWebdev\Add_Featured_Image_Src( get_option('wpfeatured_media_src_post_types') , 'large'  );

	/**
	 * add the src url field
	 */
	$src_field->add_src_field();
