<?php
/**
 * REST API Featured Image
 *
 * @package           APIFeaturedImage
 * @author            Uriel Wilson
 * @copyright         2020 Uriel Wilson
 * @license           GPL-2.0
 *
 * @wordpress-plugin
 * Plugin Name:       REST API Featured Image
 * Plugin URI:        https://switchwebdev.com/wordpress-plugins/
 * Description:       This plugin will add the featured image src url field to the WordPress Rest API.
 * Version:           0.6.8
 * Requires at least: 3.4
 * Requires PHP:      5.6
 * Author:            SwitchWebdev.com
 * Author URI:        https://switchwebdev.com
 * Text Domain:       api-featured-image
 * Domain Path:       languages
 * License:           GPLv2
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

 	// deny direct access.
	if ( ! defined( 'WPINC' ) ) {
		die;
	}

	// plugin directory.
	define( 'APIFI_VERSION', '0.6.8' );

	// plugin directory.
	define( 'APIFI_DIR', dirname( __FILE__ ) );

	// plugin url.
	define( 'APIFI_URL', plugins_url( '/', __FILE__ ) );

	/**
	 * Load composer
	 */
	require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

// -----------------------------------------------------------------------------

  	/**
  	 * Setup options on activation
  	 */
	register_activation_hook( __FILE__, function () {
			update_option( 'wpfms_post_types', array() );
		}
	);

// -----------------------------------------------------------------------------

	/**
	 * Setup the admin page
	 */
  	SimFeaturedMediaSrc\Admin\FeaturedMediaSrcAdmin::init();

	/**
	 * Initialize Add_Featured_Image_Src
	 *
	 * TODO add option to change the image size for output ('thumbnail', 'medium', 'large' , 'full')
	 */
 	$src_field = new SimFeaturedMediaSrc\addFeaturedImageSrc(
		get_option( 'wpfms_post_types' ),
		'large'
	);

	/**
	 * Add the src url field
	 */
	$src_field->add_src_field();
