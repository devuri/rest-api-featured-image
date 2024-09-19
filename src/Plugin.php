<?php

namespace APIFeaturedImage;

use APIFeaturedImage\Admin\Pages\FeaturedMediaAdmin;
use APIFeaturedImage\Admin\Pages\PostTypesAdmin;

class Plugin extends AbstractPlugin
{
    protected static $version = '4.0.21';
    protected static $prefix  = 'apfms';
    private $image_size;

    /**
     * @var array
     */
    private $post_types = [];
    private $admin_menu     = [];
    private $admin_submenus = [];

    /**
     * @param mixed $size
     *
     * @return static
     */
    public function setMediaSize( $size = 'thumbnail' ): self
    {
        $this->image_size = $size;

        return $this;
    }

    /**
     * @param mixed $types
     *
     * @return static
     */
    public function setPostTypes( $types = []): self
    {
        $this->post_types = $types;

        return $this;
    }

    /**
     * @return static
     */
    public function registerAdmin(): self
    {
        $this->_defineAdmin();

        $admin_pages = Admin::init(
            $this->admin_menu,
            $this->admin_submenus
        );

        add_action(
            '_admin_page_evp_post_types',
            [ PostTypesAdmin::class, 'render' ]
        );

        add_action(
            '_admin_page_evp_api_featured_media',
            [ FeaturedMediaAdmin::class, 'render' ]
        );

        return $this;
    }

    public function addSrcField(): void
    {
        foreach ( $this->getPostTypes() as $post_type ) {
            add_action( 'rest_api_init', function() use ( $post_type ): void {
                register_rest_field( $post_type, 'featured_media_src_url', [
                    'get_callback'    => function ( $post ) {
                        return $this->featured_media_src( $post );
                    },
                    'update_callback' => null,
                    'schema'          => null,
                ]
                );
            }, 99
            );
        }
    }

    /**
     * @return (string|string[])[]
     *
     * @psalm-return array{0: string, 'post-types': array{name: string, icon: 'dashicons-image-filter'}}
     */
    protected static function get_submenus(): array
    {
        return [
            esc_html__( 'Settings', 'rest-api-featured-image' ),
            'post-types'    => [
                'name'   => esc_html__( 'Post Types', 'rest-api-featured-image' ),
                'icon'   => 'dashicons-image-filter',
            ],
        ];
    }

    private function _defineAdmin(): void
    {
        $this->admin_menu = [
            'mcolor'      => '#50575e',
            'layout'         => 'container',
            'menu_title'	 => esc_html__( 'Featured Media ', 'rest-api-featured-image' ),
            'page_title'     => esc_html__( 'API Featured Media ', 'rest-api-featured-image' ),
            'capability'     => 'manage_options',
            'menu_slug'      => 'api-featured-media',
            'icon_url'       => 'dashicons-format-gallery',
            'position'       => 4.8736,
            'prefix'         => self::$prefix,
            'admin_views'    => self::$plugin_dir_path . 'src/inc/pages/',
            'plugin_dir_url' => self::$plugin_dir_url,
            'object_id'      => 700729,
            'panel_links'    => [
                'review'    => '#',
                'docs'      => '#',
                'videos'    => '#',
                'community' => '#',
                'support'   => '#',
            ],
        ];

        $this->admin_submenus = static::get_submenus();
    }

    /**
     * If the post type is not set (empty array()) just use post.
     */
    private function getPostTypes()
    {
        if ( empty( $this->post_types ) ) {
            $this->post_types = [ 'post' ];
        }

        return apply_filters( APIFI_PT_OPTION, $this->post_types);
    }

    /**
     * Get the featured image.
     *
     * @param int $id [description].
     *
     * @return (bool|int|string)[]|false [description]
     *
     * @see https://developer.wordpress.org/reference/functions/wp_get_attachment_image_src/
     *
     * @psalm-return array{0: string, 1: int, 2: int, 3: bool}|false
     */
    private function get_media( $id = null )
    {
        return wp_get_attachment_image_src( $id, $this->image_size );
    }

    /**
     * Featured media src
     * check if there is featured_media and if not return null.
     *
     * @param object $post the post data.
     */
    private function featured_media_src( $post = null )
    {
        $media = \array_key_exists( 'featured_media', $post );
        if ( $media ) {
            $media_src = $this->get_media( $post['featured_media'] );

            return $media_src[0];
        }

        return null;
    }
}
