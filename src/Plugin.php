<?php

namespace APIFeaturedImage;

use APIFeaturedImage\Admin\Pages\FeaturedMediaAdmin;
use APIFeaturedImage\Admin\Pages\PostTypesAdmin;

class Plugin extends AbstractPlugin
{
    protected static $version = '0.9.0';
    protected static $prefix  = 'apfms';
    private ?string $image_size;
    private ?array $post_types;
    private ?array $admin_menu;
    private ?array $admin_submenus;

    /**
     * @param array $types
     *
     * @return static
     */
    public function setPostTypes( array $types = []): self
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

    public function registerEndpoint( string $size = 'large' ): void
    {
        $register = new RestRegister( $this->post_types, $size );

        foreach ( $register->getPostTypes() as $post_type ) {
            add_action( 'rest_api_init', function() use ( $post_type, $register ): void {
                $register->addEndpoint( $post_type );
            }, 99
            );
        }
    }

    /**
     * @return (string|string[])[]
     *
     * @psalm-return array{0: string, 'post-types': array{name: string, icon: 'dashicons-image-filter'}}
     */
    protected static function getSubmenus(): array
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
            'position'       => 5.6736,
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

        $this->admin_submenus = static::getSubmenus();
    }
}
