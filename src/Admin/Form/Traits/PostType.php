<?php

namespace APIFeaturedImage\Admin\Form\Traits;

use WP_Post_Type;

trait PostType
{
    /**
     * Get the post types.
     *
     * @return WP_Post_Type[]
     *
     * @psalm-return array<string, \WP_Post_Type>
     */
    public static function post_types(): array
    {
        $args = [
            'public' => true,
        ];

        return get_post_types( $args, 'objects' );
    }

    /**
     * @return null|array
     *
     * @psalm-return array<string, mixed>|null
     */
    public static function post_type_list(): ?array
    {
        $post_types = null;
        foreach ( self::post_types() as $key => $post_type ) {
            $post_types[ $key ] = $post_type->labels->singular_name;
        }

        return $post_types;
    }
}
