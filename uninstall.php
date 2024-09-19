<?php


// deny direct access.
if ( ! \defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

delete_option( 'wpfms_post_types' );
wp_cache_flush();
