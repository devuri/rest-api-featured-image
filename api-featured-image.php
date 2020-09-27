<?php
/**
 * WP API Featured Media Source
 *
 * @package           WPFeaturedMediaSource
 * @author            Uriel Wilson
 * @copyright         2020 Uriel Wilson
 * @license           GPL-2.0
 *
 * @wordpress-plugin
 * Plugin Name:       WP REST API Featured Media Source
 * Plugin URI:        https://switchwebdev.com/wordpress-plugins/
 * Description:       This plugin will add the featured image src url field to the WordPress Rest API.
 * Version:           0.6.3
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
	define("APIFI_VERSION", '0.6.3');

 	# plugin directory
	define("APIFI_DIR", dirname(__FILE__));

 	# plugin url
	define("APIFI_URL", plugins_url( "/",__FILE__ ));

	/**
   * Load composer
   */
  require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

#  -----------------------------------------------------------------------------

  	/**
  	 * setup options on activation
  	 *
  	 * @var [type]
  	 */
	register_activation_hook( __FILE__, function () {
	   update_option( 'wpfms_post_types' , array() );
	});

#  -----------------------------------------------------------------------------

	/**
	 * setup the admin page
	 *
	 */
  	SimFeaturedMediaSrc\Admin\FeaturedMediaSrcAdmin::init();

	/**
	 * initialize Add_Featured_Image_Src
	 *
	 * TODO add option to change the image size for output ('thumbnail', 'medium', 'large' , 'full')
	 */
 	$src_field = new SimFeaturedMediaSrc\addFeaturedImageSrc(
		get_option('wpfms_post_types'),
		'large'
	);

	/**
	 * add the src url field
	 */
	$src_field->add_src_field();
