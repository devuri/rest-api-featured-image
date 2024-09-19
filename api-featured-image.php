<?php

/**
 * Plugin Name:       REST API Featured Image
 * Plugin URI:        https://github.com/devuri/rest-api-featured-image
 * Description:       This plugin will add the featured image src url field to the WordPress Rest API.
 * Version:           0.9.0
 * Requires at least: 5.3.0
 * Requires PHP:      7.4
 * Author:            uriel
 * Author URI:        https://github.com/devuri
 * Text Domain:       rest-api-featured-image
 * License:           GPLv2
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Network: true.
 */

if ( ! \defined( 'ABSPATH' ) ) {
    exit;
}

\define( 'APIFI_VERSION', '0.9.0' );
\define( 'APIFI_DIR', \dirname( __FILE__ ) );
\define( 'APIFI_URL', plugins_url( '/', __FILE__ ) );
\define( 'APIFI_PT_OPTION', 'wpfms_post_types' );

require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

register_activation_hook(
    __FILE__, function (): void {
        update_option( APIFI_PT_OPTION, [] );
    }
);

APIFeaturedImage\Plugin::init( plugin_dir_path( __FILE__ ), plugin_dir_url( __FILE__ ) )
    ->registerAdmin()
    // TODO add option to change the image size for output ('thumbnail', 'medium', 'large' , 'full')
    ->setMediaSize( 'large' )
    ->setPostTypes( get_option( APIFI_PT_OPTION, [] ) )
    ->addSrcField();
