<?php

namespace APIFeaturedImage;

use APIFeaturedImage\Admin\AbstractAdminCore;

class Admin extends AbstractAdminCore
{
    public static function version(): ?string
    {
        if ( current_user_can( 'manage_options' ) ) {
            return sprintf(
                '<span style="color:silver;font-size: small;"> v%1$s</span>',
                '4.0.21',
            );
        }

        return null;
    }

    // required.
    public function register_user_options(): void
    {
    }
}
