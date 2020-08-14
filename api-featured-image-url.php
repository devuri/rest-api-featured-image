<?php
/**
 * API Featured Image Url
 *
 * @package           APIFeaturedImageUrl
 * @author            Uriel Wilson
 * @copyright         2020 Uriel Wilson
 * @license           GPL-2.0
 *
 * @wordpress-plugin
 * Plugin Name:       API Featured Image Url
 * Plugin URI:        https://switchwebdev.com/wordpress-plugins/
 * Description:       This will add the featured image src url to the WP Rest API (needed to work with apps like zapier etc).
 * Version:           0.1.3
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
	 define("EVP_VERSION", '0.1.3');

 # plugin directory
	 define("EVP_APIFI", dirname(__FILE__));

 # plugin url
	 define("EVP_APIFI", plugins_url( "/",__FILE__ ));



add_action( 'rest_api_init', function () {
	 register_rest_field( 'post', 'featured_image_src', array(
			 'get_callback' => function ( $post_arr ) {
					 $image_src_arr = wp_get_attachment_image_src( $post_arr['featured_media'] , 'large' );

					 return $image_src_arr[0];
			 },
			 'update_callback' => null,
			 'schema' => null
	 ) );
} );
