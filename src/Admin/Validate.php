<?php

namespace APIFeaturedImage\Admin;

// @codingStandardsIgnoreFile.

class Validate
{
    public static function request( string $action, string $nonce ): bool
    {
        if ( ! self::verify_nonce( $action, $nonce ) ) {
            return false;
        }
        if ( ! self::admin_referer( $action, $nonce ) ) {
            return false;
        }

        return true;
    }

    public static function verify_nonce( string $action, string $nonce ): bool
    {
        $validate_nonce = isset( $_REQUEST[ $nonce ] ) && wp_verify_nonce( $_REQUEST[ $nonce ], $action );
        if ( $validate_nonce ) {
            return true;
        }

        return false;
    }

    public static function admin_referer( string $action, string $nonce ): bool
    {
        /**
         * check_admin_referer() will return int|false.
         * we will only return true for 1 (0-12 hours ago).
         *
         * 1 if the nonce is valid and generated between 0-12 hours ago,
         * 2 if the nonce is valid and generated between 12-24 hours ago.
         * False if the nonce is invalid.
         */
        $validate = check_admin_referer( $action, $nonce );
        if ( 1 === $validate ) {
            return true;
        }

        return false;
    }

    public static function user_cap( string $capability = 'manage_options' ): bool
    {
        if ( current_user_can( $capability ) ) {
            return true;
        }

        return false;
    }
}
