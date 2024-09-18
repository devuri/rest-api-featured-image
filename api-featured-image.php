<?php

/**
 * Plugin Name:       REST API Featured Image
 * Plugin URI:        https://github.com/devuri/rest-api-featured-image
 * Description:       This plugin will add the featured image src url field to the WordPress Rest API.
 * Version:           0.8.4
 * Requires at least: 5.3.0
 * Requires PHP:      7.3.5
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

\define( 'APIFI_VERSION', '0.8.4' );
\define( 'APIFI_DIR', \dirname( __FILE__ ) );
\define( 'APIFI_URL', plugins_url( '/', __FILE__ ) );

require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

/**
 * Setup options on activation.
 */
register_activation_hook( __FILE__, function (): void {
    update_option( 'wpfms_post_types', [] );
}
);


APIFeaturedImage\Admin\PluginAdmin::init();

/**
 * Initialize Add_Featured_Image_Src.
 *
 * TODO add option to change the image size for output ('thumbnail', 'medium', 'large' , 'full')
 */
(new APIFeaturedImage\Plugin(
    get_option( 'wpfms_post_types', [] ),
    'large'
))->add_src_field();
