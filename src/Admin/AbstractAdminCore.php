<?php

namespace APIFeaturedImage\Admin;

use Exception;
use InvalidArgumentException;

abstract class AbstractAdminCore implements AdminCoreInterface
{
    public const ADMINVERSION = '9.1.6';

    protected ?array $menu_args;
    protected ?string $object_id;
    protected ?array $style_scripts;
    protected ?array $submenu_hooks;
    protected ?string $generic_icon;
    protected ?string $plugin_dir_url;
    protected ?string $asset_url;
    protected ?string $plugin_path;
    protected ?string $admin_views;
    protected ?string $page_title;
    protected ?string $menu_title;
    protected ?string $capability;
    protected ?string $menu_slug;
    protected ?string $function;
    protected ?string $icon_url;
    protected ?string $position;
    protected ?string $prefix;
    protected ?array $submenu_items;
    protected ?array $registered_submenus;
    protected ?string $mcolor;
    protected ?bool $network_admin;
    protected ?bool $_is_network_admin;
    protected ?array $_child_submenus;
    protected ?array $side_panel_links;
    protected ?array $_hidden_submenus;
    protected ?array $user_options;
    protected ?string $parent;
    protected ?bool $is_frontend;
    protected ?bool $backend_access;
    protected ?string $evp_frontend_url;
    protected ?string $page_current_screen;
    protected ?string $bgcolor;
    protected ?string $layout;

    public function __construct( array $menu_args, array $user_options = [] )
    {
        $this->user_options = $user_options;

        $this->_properties_init( $menu_args );

        $this->_is_network_admin = $this->is_network_admin();
        $this->generic_icon      = 'dashicons-admin-generic';
        $this->evp_frontend_url  = home_url( 'evp-admin/' );
    }

    public function __get( $name )
    {
        if ( ! property_exists( $this, $name ) ) {
            throw new Exception( "Property:$name does not exist." );
        }

        return $this->$name;
    }

    /**
     * @return static
     */
    public function set_submenus( ?array $submenus = null ): self
    {
        if ( ! empty( $submenus ) && \is_array( $submenus ) ) {
            if ( ! $this->is_frontend || is_admin() ) {
                $this->unset_frontend_keys( $submenus, [ 'home', 'logout', 'user-account' ] );
            }

            $this->submenu_items = apply_filters( '_evp_admin_submenu', $submenus );

            $this->_register_submenus();
        }

        return $this;
    }

    public function get_submenu_items(): array
    {
        if ( ! \is_array( $this->submenu_items ) || empty( $this->submenu_items ) ) {
            error_log( 'InvalidArgumentException: Submenu items need a valid array, use ::set_submenus()' );

            throw new InvalidArgumentException( 'Submenu items need a valid array.' );
        }

        return $this->submenu_items;
    }

    /**
     * @return static
     */
    public static function init( array $menu_args, array $submenu_items = [], array $user_options = [] ): AdminCoreInterface
    {
        $instance = static::class;

        $factory = new $instance( $menu_args, $user_options );

        return $factory->set_submenus( $submenu_items )->admin_enqueue()->create();
    }

    /**
     * @return static
     */
    public function admin_enqueue(): self
    {
        if ( $this->is_frontend ) {
            return $this;
        }

        add_action(
            'admin_enqueue_scripts',
            function ( $hook ): void {
                $page_info         = self::get_page_info();
                $page_parent       = $page_info['parent'] ?? null;
                $current_page_slug = $page_info['slug'] ?? null;

                $is_evp_page = strpos( $current_page_slug, (string) $this->get_prefix() );

                if ( false === $is_evp_page ) {
                    return;
                }
                $this->_enqueue();
            }
        );

        return $this;
    }

    /**
     * @param mixed $is_network
     *
     * @return static
     */
    public function create( $is_network = false ): AdminCoreInterface
    {
        $this->submenu_hooks = $this->set_submenu_hooks();

        add_action( 'admin_init', [ $this, 'register_user_options' ] );

        add_action(
            'init',
            function (): void {
                $this->add_dashboard_assets();
                $this->add_lity_scripts();
            }
        );

        add_action(
            'wp_enqueue_scripts',
            function (): void {
                wp_enqueue_style( $this->style_scripts['admin'] );
                wp_enqueue_style( $this->style_scripts['dashboard_admin'] );
                wp_enqueue_style( $this->style_scripts['litycss'] );
                wp_enqueue_script( $this->style_scripts['lityjs'] );

                if ( $this->is_frontend ) {
                    $custom_inline_style = '
                body {
                    background: #f9f9f9;
                    color: #3c434a;
                    font-family: "Plus Jakarta Sans", sans-serif !important;
                    font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
                    font-size: 13px;
                    line-height: 1.4em;
                    margin: 0px;
                }
                .dashicons, .dashicons-before:before {
                    line-height: unset;
                }';
                    wp_add_inline_style( $this->style_scripts['litycss'], $custom_inline_style );
                }
            }
        );

        if ( $this->_is_network_admin || $is_network ) {
            add_action( 'network_admin_menu', [ $this, 'build_menu' ] );
        } else {
            add_action( 'admin_menu', [ $this, 'build_menu' ] );
        }

        return $this;
    }

    public function register_user_options(): void
    {
    }

    /**
     * @return string
     */
    public function menu_title(): string
    {
        $menu_title  = '<h2 style="font-size:small;" class="wll-admin-dashicons-before ';
        $menu_title .= $this->icon_url;
        $menu_title .= '">';

        $menu_title .= '</h2>';

        return $menu_title;
    }

    public function header( array $submenu ): void
    {
        if ( ! is_user_logged_in() ) {
            wp_die();
        }

        if ( $this->is_frontend ) {
            $container = 'container';
            $slug      = null;
        } else {
            // $container = 'container-fluid';
            $container = $this->layout;
            $slug      = $submenu['slug'];
            do_action( '_evp_admin_init', $slug );
            do_action( '_evp_admin_head', $slug );
        }

        add_thickbox();

        $this->image_overlay_styles();

        $this->notice_section_banner(); ?><header class="wll-header" style="box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05); padding: 0px;">
            <div class="<?php echo esc_attr( $container ); ?>">
              <div class="row">
                <!-- <div class="col col-lg-2">

                </div> -->
                <div class="col">
                    <?php $this->dynamic_tabs(); ?>
                </div>
              </div>
            </div>
        </header>
        <div class="navbar-horizontal" style="border-bottom:1px solid #ebf1f6;">
            <div class="<?php echo esc_attr( $container ); ?>" style="font-size: small;     font-size: small; padding-top: 10px; padding-bottom: 20px;">
                <?php $this->render_child_submenu( $slug ); ?>
            </div>
        </div>
            <div class="wrapit container">
                <h2 style="color:#a7aaad;font-weight: 400;font-size: medium;">
					<?php echo $submenu['name']; ?>
				</h2>
                <?php static::admin_notice_section( $this ); ?>
            </div><!---wrap admin notices -->
        <div class="<?php echo esc_attr( $container ); ?>">
            <div class="row">
            <div class="col">
               <div class="wll-main" style="margin-top: 30px; box-shadow: rgba(145, 158, 171, 0.2) 0px 0px 2px 0px, rgba(145, 158, 171, 0.12) 0px 12px 24px -4px;">
        <?php

        $this->render_frontend_button( $slug );
    }

    public static function admin_notice_section( $instance ): void
    {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        do_action( 'evp_admin_notice', $instance );
    }

    public function notice_section_banner(): void
    {
        if ( $this->is_frontend ) {
            $panel_banner = 'frontend_banner';
        } else {
            $panel_banner = 'admin_banner';
        }
        ?>
        <div id="wll-important-notice" style="color:white; font-size: medium; padding:.5rem; background-color:<?php echo $this->mcolor; ?>;">
              <span class="wll-banner-notice">
                <?php do_action( "_evp_{$panel_banner}" ); ?>
              </span>
        </div>
        <?php
    }

    public function page_content( ?string $spage = null, $submenu_item = null, ?string $submenu_slug = null ): void
    {
        if ( $this->is_frontend ) {
            $admin_page_hook = 'frontend_' . str_replace( '-', '_', $this->page_name() );
        } else {
            $admin_page_hook = 'evp_' . str_replace( '-', '_', $this->page_name() );
        }

        $hooked_args = [
            'object'    => $this,
            'hook'      => $admin_page_hook,
            'page_name' => $this->page_name(),
            'slug'      => $submenu_slug,
            'item'      => $submenu_item,
            'ajax'      => $submenu_item['ajax'] ?? false,
            'screen'    => $this->page_current_screen,
            // 'items'     => $this->registered_submenus,
        ];

        // loads page via includes.
        $this->maybe_load_view_file( $spage );

        $_admin_page_hook = "_admin_page_{$admin_page_hook}";

        do_action( $_admin_page_hook, $hooked_args );

        if ( ! \defined( 'WP_DEBUG' ) || false === WP_DEBUG ) {
            return;
        }

        if ( ! has_action( $_admin_page_hook ) && current_user_can( 'manage_options' ) ) {
            trigger_error( __( 'The following hook is missing a callback: ', 'rest-api-featured-image' ) . "<strong>{$_admin_page_hook}</strong>", E_USER_WARNING );
        }
    }

    public function footer( $submenu_item ): void
    {
        $submenu_ajax_script = $submenu_item['script'] ?? null;

        if ( $submenu_ajax_script ) {
            // Enqueue JavaScript for form submission handling.
            wp_enqueue_script( $submenu_ajax_script );
        }

        $submenu_hook = str_replace( '-', '_', $this->page_name() );

        ?>
            </p></div><!--wll-main -->
        </div><!---col-md-8 -->

        <?php if ( $this->get_property( 'sidebar', $submenu_item ) ) { ?>
        <div class="col-md-3">
			<div class=" container" style="margin-top:30px;font-size: initial;line-height: 2em;">
				<?php $this->side_panel_settings( $submenu_hook ); ?>
				<?php do_action( "_evp_admin_sidebar_{$submenu_hook}", $this ); ?>
	            <?php do_action( '_evp_admin_sidebar', $this ); ?>
			</div>
        </div><!---col-md-4-->
    <?php } ?>
      </div><!---row-->
        <div style="padding-left: 20px; padding-right: 40px;color: #b9b9b9;font-weight: 300;" class="">
            <?php do_action( '_evp_admin_footer' ); ?>
            <footer class="py-3 my-4">
                <p class="text-center text-body-secondary">
                    <?php echo get_bloginfo( 'name' ); ?>
                </p>
            </footer>
        </div>
      </div><!---container-fluid -->
        <?php
    }

    public function sidebar(): string
    {
        return self::get_page_info( 'slug' );
    }

    public function striped_table_list( array $data_list = [] )
    {
        $output  = '<table class="wp-list-table widefat fixed striped table-view-list"><tbody>';
        $output .= self::striped_table_content( $data_list );
        $output .= '</tbody></table>';

        return $output;
    }

    public function striped_table_content( array $data_list = [] ): ?string
    {
        $output = null;
        foreach ( $data_list as $key => $value ) {
            $output .= sprintf(
                '<tr class="row-item"><th>%s</th><td>%s</td></tr>',
                esc_html( $key ),
                esc_html( $value )
            );
        }

        return $output;
    }

    /**
     * @param null|array $items
     */
    public function generate_admin_links( ?array $items )
    {
        $admin_url   = admin_url( 'admin.php?page=' );
        $html_links  = '';
        $line_height = 'style="line-height: 2 !important;"';

        foreach ( $items as $key => $item ) {
            if ( 0 === $key ) {
                $icon = '<span ' . $line_height . ' class="wlc-icons dashicons dashicons-menu"></span> ';
            } else {
                $icon = ! empty( $item['icon'] ) ? '<span ' . $line_height . ' class="wlc-icons dashicons ' . esc_attr( $item['icon'] ) . '"></span> ' : '';
            }
            $link        = $admin_url . $item['slug'];
            $html_links .= '<div>';
            $html_links .= '<a href="' . esc_url( $link ) . '">' . $icon . esc_html( $item['name'] ) . '</a>';
            $html_links .= '</div>';
        }

        return $html_links;
    }

    public function admin_gui_version(): ?string
    {
        if ( current_user_can( $this->access_capability() ) ) {
            return self::ADMINVERSION;
        }

        return null;
    }

    public function current_page_title(): ?string
    {
        if ( $this->is_frontend ) {
            return $this->page_current_screen;
        }

        $screen = self::get_screen();

        $id_page_name = explode( '_', $screen->id );
        $current_page = $id_page_name[2];

        return sanitize_text_field( $current_page );
    }

    public function set_current_page_screen( string $page_current_screen ): void
    {
        $this->page_current_screen = sanitize_key( $page_current_screen );
    }

    public function callback()
    {
        return null;
    }

    public function page_name(): string
    {
        return str_replace( $this->get_prefix(), '', $this->current_page_title() );
    }

    public function make_admin_url( $slug ): string
    {
        $slug = strtolower( $slug );

        if ( 'home' === $slug ) {
            return esc_url( home_url() );
        }

        if ( 'logout' === $slug ) {
            return esc_url( wp_logout_url( home_url() ) );
        }

        if ( $this->is_frontend ) {
            return esc_url( $this->evp_frontend_url . $slug );
        }

        if ( $this->_is_network_admin ) {
            return esc_url( network_admin_url( '/admin.php?page=' . $slug . '' ) );
        }

        return esc_url( admin_url( '/admin.php?page=' . $slug . '' ) );
    }

    public function menu_slug(): string
    {
        return str_replace( $this->get_prefix(), '', $this->menu_slug );
    }

    public function build_menu(): void
    {
        if ( $this->has_submenus() ) {
            $this->register_main_menu();
            $this->add_submenu_pages();
        } else {
            $this->register_main_menu( [ $this, 'callback' ] );
        }
    }

    public function admin_bar_links( $wp_admin_bar ): void
    {
        $admin_bar_id = $this->menu_slug . 'admin-bar';

        $args = [
            'id'    => $admin_bar_id,
            'title' => $this->menu_title,
            'meta'  => [ 'class' => 'evp-dropdown-class' ],
        ];

        $wp_admin_bar->add_node( $args );

        foreach ( $this->registered_submenus as $key => $submenu ) {
            $parent_slug    = $submenu['parent'];
            $submenu_access = $submenu['access'];
            $submenu_slug   = $submenu['slug'];

            if ( self::is_child( $submenu ) || self::is_hidden( $submenu ) ) {
                continue;
            }

            if ( 0 === $key || 'logout' === $key || ! current_user_can( $submenu_access ) ) {
                continue;
            }

            $args = [
                'id'     => $submenu_slug,
                'title'  => ucfirst( $this->get_property( 'name', $submenu ) ),
                'href'   => $this->evp_frontend_url . $submenu_slug,
                'parent' => $admin_bar_id,
                'meta'   => [ 'class' => 'evp-link' ],
            ];
            $wp_admin_bar->add_node( $args );
        }// end foreach
    }

    public function get_page_template( $spage, $submenu, $submenu_slug ): void
    {
        if ( empty( $submenu ) ) {
            return;
        }

        $this->exit_fail( $submenu_slug );

        $this->header( $submenu );
        $this->page_content( $spage, $submenu, $submenu_slug );
        $this->footer( $submenu );
    }

    public function get_property( $key, array $submenus )
    {
        $params = array_merge(
            [
                'name'      => $submenus['name'],
                'parent'    => $this->menu_slug,
                'views_dir' => null,
                'sidebar'   => true,
                'child'     => false,
                'premium'   => false,
            ],
            $submenus,
        );

        return $params[ $key ];
    }

    public function sub_capability( ?string $subkey = null ): ?string
    {
        if ( \is_null( $subkey ) ) {
            return $this->access_capability();
        }

        return $this->getAccessControl( $subkey ) ?? null;
    }

    public function get_child(): ?array
    {
        return $this->_child_submenus;
    }

    public function get_hidden(): ?array
    {
        return $this->_hidden_submenus;
    }

    /**
     * @return string
     */
    public function asset_url(): string
    {
        if ( empty( $this->plugin_dir_url ) ) {
            throw new \Exception( 'Empty plugin dir url' );
        }

        return $this->plugin_dir_url . 'asset/dist/';
    }

    public function is_network_managed(): bool
    {
        return is_network_admin();
    }

    /**
     * @param null|string $default
     *
     * @psalm-param 'menu_title' $key
     * @psalm-param 'Featured Media'|null $default
     */
    public function get_user_option( string $key, ?string $default = null )
    {
        if ( \array_key_exists( $key, $this->user_options ) && ! empty( $this->user_options[ $key ] ) ) {
            return $this->user_options[ $key ];
        }

        return $default;
    }

    public function get_asset_url(): ?string
    {
        if ( empty( $this->plugin_dir_url ) ) {
            wp_trigger_error( static::class . '::get_asset_url', 'asset url is null, you should make sure plugin_dir_url property is set' );

            return null;
        }

        return $this->asset_url;
    }

    public function add_dashboard_assets(): void
    {
        wp_register_style(
            $this->style_scripts['admin'],
            $this->get_asset_url() . 'css/admin.min.css',
            [],
            self::ADMINVERSION,
            'all'
        );

        wp_register_style(
            $this->style_scripts['dashboard_admin'],
            $this->get_asset_url() . 'css/evps-dashboard.min.css',
            [],
            self::ADMINVERSION,
            'all'
        );
    }

    public function add_lity_scripts(): void
    {
        wp_register_script(
            $this->style_scripts['lityjs'],
            $this->get_asset_url() . 'lity/lity.min.js',
            [ 'jquery' ],
            self::ADMINVERSION,
            'all'
        );

        wp_register_style(
            $this->style_scripts['litycss'],
            $this->get_asset_url() . 'lity/lity.min.css',
            [],
            self::ADMINVERSION,
            'all'
        );
    }

    public function get_style_scripts(): ?array
    {
        return $this->style_scripts;
    }

    public function get_menu_arg( ?string $key = null )
    {
        if ( \is_null( $key ) ) {
            return $this->menu_args;
        }

        return $this->menu_args[ $key ] ?? null;
    }

    protected function get_panel_link( string $link_id ): ?string
    {
        return $this->side_panel_links[ $link_id ] ?? null;
    }

    protected function side_panel_settings( string $submenu_hook ): void
    {
        if ( 'video_publisher' !== $submenu_hook ) {
            return;
        }

        // $button_class = 'button button-secondary';

        $this->add_postbox(
            'Review',
            'If you enjoy the plugin, please leave a review.',
            [
                'url'   => $this->get_panel_link( 'review' ),
                'title' => 'Leave a Review',
            ]
        );

        $this->add_postbox(
            'Documentation',
            'Explore our knowledge base full of helpful articles.',
            [
                'url'   => $this->get_panel_link( 'docs' ),
                'title' => 'Documentation',
            ]
        );

        $this->add_postbox(
            'Videos',
            'Check out our video tutorials for a step-by-step guides.',
            [
                'url'   => $this->get_panel_link( 'videos' ),
                'title' => 'Watch Videos',
            ]
        );

        $this->add_postbox(
            'Community',
            'Join our community users, share, ask questions, and help others.',
            [
                'url'   => $this->get_panel_link( 'community' ),
                'title' => 'Join our Community',
            ]
        );

        $this->add_postbox(
            'Support',
            'If you have a question, contact our support team for assistance.',
            [
                'url'   => $this->get_panel_link( 'support' ),
                'title' => 'Submit a Ticket',
            ]
        );

        echo $this->generate_admin_links( $this->registered_submenus );
    }

    protected static function is_youtube_admin( array $submenu_item ): bool
    {
        return isset( $submenu_item['name'] ) && 'YouTube' === $submenu_item['name'];
    }

    /**
     * @param (int|string) $key
     *
     * @psalm-param array-key $key
     */
    protected function set_submenu_slug( $key )
    {
        if ( 0 === $key ) {
            return $this->menu_slug;
        }

        return sanitize_title( $this->get_prefix() . $key );
    }

    /**
     * @param (int|string) $key
     *
     * @psalm-param array-key $key
     */
    protected function item_access( $key )
    {
        if ( 0 === $key ) {
            return $this->access_capability();
        }

        return $this->sub_capability( $key ) ?? $this->access_capability();
    }

    /**
     * Unset specified keys from the provided array if they exist.
     *
     * @param array $array The array from which keys should be removed.
     * @param array $keys  The keys to be removed from the array.
     *
     * @return void
     */
    protected function unset_frontend_keys( array &$array, array $keys ): void
    {
        foreach ( $keys as $key ) {
            if ( \array_key_exists( $key, $array ) ) {
                unset( $array[ $key ] );
            }
        }
    }

    protected function get_prefix()
    {
        return $this->prefix ? $this->prefix . '-' : null;
    }

    protected function dynamic_tabs(): void
    {
        echo '<span style="border: unset; padding-top: 0px;" class="wll-admin nav-tab-wrapper wp-clearfix">';

        foreach ( $this->registered_submenus as $key => $submenu ) {
            $parent_slug    = $submenu['parent'];
            $submenu_access = $submenu['access'];
            $submenu_slug   = $submenu['slug'];

            if ( 0 === $key ) {
                $submenu_slug = $this->menu_slug;
            } else {
                $submenu_slug = sanitize_title( $this->get_prefix() . $key );
            }

            if ( ! $this->user_can_access( $submenu_slug ) ) {
                continue;
            }

            if ( isset( $submenu['display'] ) && false === $submenu['display'] ) {
                continue;
            }

            if ( self::is_child( $submenu ) ) {
                $this->_child_submenus[] = [
                    'slug' => $submenu_slug,
                    'item' => $submenu,
                ];

                continue;
            }

            if ( self::is_hidden( $submenu ) ) {
                $this->_hidden_submenus[] = [
                    'slug' => $submenu_slug,
                    'item' => $submenu,
                ];

                continue;
            }

            echo $this->nav_item( $submenu_slug, $submenu );
        }// end foreach
        echo '</span>';
    }

    protected function nav_item( string $submenu_slug, ?array $submenu_item, bool $child = false )
    {
        if ( $submenu_slug === $this->current_page_title() ) {
            $tab_is_active = 'wll-admin-tab nav-tab-active';
            $color_style   = 'style="color:#787c82"';
        } else {
            $tab_is_active = 'wll-admin-tab';
            $color_style   = 'style="color:#0071A1"';
        }

        $tab_is_active = $child ? 'wll-child-tab' : $tab_is_active;
        $icocss        = $child ? 'wlc-icons' : 'wll-icons';

        $icon = $submenu_item['icon'] ?? 'dashicons-menu-alt2';

        $nav_link = '<a ' . $color_style . ' href="' . $this->make_admin_url( $submenu_slug ) . '">' . ucwords( $this->get_property( 'name', $submenu_item ) ) . '</a>';

        return '<span class="' . $tab_is_active . '"><span class="' . $icocss . ' dashicons ' . $icon . '"></span>' . $nav_link . '</span>';
    }

    protected function render_child_submenu( ?string $slug ): void
    {
        foreach ( $this->get_child() as $key => $submenu_item ) {
            echo $this->nav_item( $submenu_item['slug'], $submenu_item['item'], true );
        }
    }

    protected function maybe_load_view_file( ?string $spage ): void
    {
        if ( \is_null( $spage ) ) {
            $admin_path = $this->admin_views;
        } else {
            $admin_path = $spage;
        }

        // TODO verify $this->admin_views is not null

        if ( $this->is_frontend ) {
            $adminfile = $admin_path . $this->menu_slug() . '/' . $this->page_name() . '.frontend.php';
        } else {
            $adminfile = $admin_path . $this->menu_slug() . '/' . $this->page_name() . '.admin.php';
        }

        if ( file_exists( $adminfile ) ) {
            require_once $adminfile;
        }
    }

    protected function access_capability(): string
    {
        if ( $this->_is_network_admin ) {
            return 'manage_network';
        }

        return 'manage_options';
    }

    protected static function plugin_dir_path()
    {
        return plugin_dir_path( __FILE__ );
    }

    protected function add_submenu_pages(): void
    {
        foreach ( $this->registered_submenus as $key => $submenu ) {
            $parent_slug    = $submenu['parent'];
            $submenu_access = $submenu['access'];
            $submenu_slug   = $submenu['slug'];
            add_submenu_page(
                $parent_slug,
                ucfirst( $this->get_property( 'name', $submenu ) ),
                ucwords( $this->get_property( 'name', $submenu ) ),
                $submenu_access,
                $submenu_slug,
                function () use ( $submenu, $submenu_slug ): void {
                    $this->render_submenu_page( $submenu, $submenu_slug );
                }
            );
        }
    }

    private function getAccessControl( string $subkey, $default = null )
    {
        return null;
    }

    private function add_postbox( string $heading = 'Documentation', string $postbox_content = 'knowledge base and articles ', array $link = [] ): void
    {
        $abutton_class = $link['class'] ?? null;
        ?>
        <div class="postbox wll-shadow" style="border:none;">
			<div class="inside">
				<h2 style="font-size:medium; font-weight:bold;"><?php echo esc_html( $heading ); ?></h2>
					<p><?php echo $postbox_content; ?></p>
				<a class="<?php echo esc_attr( $abutton_class ); ?>" href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" class="sidebar-link">
					<?php echo esc_html( $link['title'] ); ?>
				</a>
			</div>
		</div>
        <?php
    }

    private function _enqueue(): void
    {
        wp_enqueue_style( $this->style_scripts['dashboard_admin'] );
        wp_enqueue_style( $this->style_scripts['litycss'] );
        wp_enqueue_script( $this->style_scripts['lityjs'] );

        $custom_inline_style = '
        body {
            background: #f9f9f9;
            line-height: inherit;
        }
        p {
            line-height: inherit;
        }';
        wp_add_inline_style( $this->style_scripts['dashboard_admin'], $custom_inline_style );
    }

    private function has_submenus(): bool
    {
        return ! empty( $this->submenu_items );
    }

    private function register_main_menu( ?callable $callback = null ): void
    {
        if ( $this->is_frontend ) {
            return;
        }

        add_menu_page(
            $this->page_title,
            $this->menu_title,
            $this->capability,
            $this->menu_slug,
            $callback,
            $this->icon_url,
            $this->position
        );
    }

    private function set_prefix_menu_slug(): void
    {
        $this->menu_slug = $this->get_prefix() . $this->menu_slug;
    }

    private function _register_submenus(): void
    {
        $this->registered_submenus = [];
        foreach ( $this->get_submenu_items() as $key => $submenus ) {
            $submenu = self::normalize( (int) $key, $submenus );

            $submenu_slug   = $this->set_submenu_slug( $key );
            $submenu_access = $this->item_access( $key );

            if ( ! $this->is_frontend && false === $this->backend_access ) {
                $submenu_access = $this->access_capability();
            }

            $parent_slug = $this->get_parent_slug( $submenu, $key );

            $this->registered_submenus[ $key ] = array_merge(
                [
                    'slug'    => $submenu_slug,
                    'access'  => $submenu_access,
                    'parent'  => $parent_slug,
                    'ajax'    => false,
                    'script'  => null,
                    'sidebar' => true,
                ],
                $submenu,
            );
        }// end foreach
    }

    private function get_registered_submenus(): ?array
    {
        return $this->registered_submenus;
    }

    /**
     * @param null|array   $submenu
     * @param (int|string) $key
     *
     * @psalm-param array-key $key
     */
    private function get_parent_slug( ?array $submenu, $key ): ?string
    {
        if ( self::is_child( $submenu ) || self::is_hidden( $submenu ) ) {
            return null;
        }

        return $this->get_property( 'parent', $submenu );
    }

    private function render_submenu_page( array $submenu, string $submenu_slug ): void
    {
        if ( empty( $submenu ) ) {
            return;
        }

        $this->exit_fail( $submenu_slug );
        $this->header( $submenu );
        $this->page_content( $this->get_property( 'views_dir', $submenu ), $submenu, $submenu_slug );
        $this->footer( $submenu );
    }

    private function render_frontend_button( ?string $slug = null ): void
    {
        if ( ! is_admin() || empty( $slug ) ) {
            return;
        }

        $slug_only = str_replace( $this->get_prefix(), null, $slug );

        $frontend_url = $this->evp_frontend_url . $slug_only;
    }

    private static function get_screen()
    {
        return get_current_screen();
    }

    private function exit_fail( string $submenu_slug ): void
    {
        $has_access = $this->user_can_access( $submenu_slug );

        if ( ! $has_access ) {
            wp_die( 'Access Permission Failed' );
        }
    }

    private function user_can_access( string $submenu_slug ): bool
    {
        if ( current_user_can( 'manage_options' ) || current_user_can( 'manage_network' ) ) {
            return true;
        }

        $access_capability = $this->sub_capability( $submenu_slug ) ?? $this->access_capability();

        return current_user_can( $access_capability );
    }

    /**
     * @return (mixed|string)[]
     *
     * @psalm-return array<mixed|string>
     */
    private function set_submenu_hooks(): array
    {
        foreach ( $this->registered_submenus as $key => $submenu ) {
            $named_hook_key = str_replace( '-', '_', sanitize_title( $submenu['name'] ) );

            $this->submenu_hooks[ $key ] = "_evp_admin_page_{$named_hook_key}";
        }

        return $this->submenu_hooks;
    }

    private function is_network_admin(): bool
    {
        if ( $this->network_admin && is_multisite() ) {
            return true;
        }

        return false;
    }

    /**
     * @param null|array $submenu_item
     */
    private static function is_child( ?array $submenu_item ): bool
    {
        if ( isset( $submenu_item['child'] )
        && true === $submenu_item['child'] ) {
            return true;
        }

        return false;
    }

    /**
     * @param null|array $submenu_item
     */
    private static function is_hidden( ?array $submenu_item ): bool
    {
        if ( isset( $submenu_item['hidden'] )
        && true === $submenu_item['hidden'] ) {
            return true;
        }

        return false;
    }

    private static function sanitize( string $fieldname, bool $use_dashes = false ): string
    {
        $field_id = sanitize_key(
            sanitize_file_name( $fieldname )
        );

        if ( $use_dashes ) {
            return $field_id;
        }

        return str_replace( '-', '_', $field_id );
    }

    private static function normalize( int $key, $submenus ): ?array
    {
        if ( \is_array( $submenus ) ) {
            return $submenus;
        }
        if ( \is_string( $submenus ) ) {
            return [ 'name' => $submenus ];
        }

        return null;
    }

    private function image_overlay_styles(): void
    {
        ?>
        <style>
            .col-image-container {
                position: relative;
            }

            img {
                /* width: 100%; */
                height: auto;
            }

            .col-image-overlay {
                position: absolute;
                bottom: 10px;
                background-color: rgba(0, 0, 0, 0.5);
                color: white;
                padding: 5px 10px;
                font-size: medium;
            }
            </style>
        <?php
    }

    private static function get_page_info( ?string $info = null )
    {
        if ( ! is_admin() ) {
            return false;
        }
        $get_slug  = explode( '_page_', $GLOBALS['hook_suffix'] );
        $page_info = [
            'title'  => get_admin_page_title(),
            'slug'   => $get_slug[1] ?? null,
            'suffix' => $GLOBALS['hook_suffix'],
            'screen' => get_current_screen(),
            'parent' => get_admin_page_parent(),
        ];

        if ( \is_null( $info ) ) {
            return $page_info;
        }

        return $page_info[ $info ];
    }

    private function _properties_init( array $menu_args ): void
    {
        $menu_title = $this->get_user_option( 'menu_title', 'Featured Media' );

        $this->menu_args           = $menu_args;
        $this->object_id           = $menu_args['object_id'] ?? 'evp_admin_id';
        $this->generic_icon        = $menu_args['generic_icon'] ?? null;
        $this->plugin_dir_url      = $menu_args['plugin_dir_url'] ?? null;
        $this->plugin_path         = $menu_args['plugin_path'] ?? static::plugin_dir_path();
        $this->admin_views         = $menu_args['admin_views'] ?? null;
        $this->page_title          = $menu_args['page_title'] ?? 'Page Title';
        $this->menu_title          = $menu_args['menu_title'] ?? $menu_title;
        $this->capability          = $menu_args['capability'] ?? $this->access_capability();
        $this->menu_slug           = $menu_args['menu_slug'] ?? null;
        $this->function            = $menu_args['function'] ?? null;
        $this->icon_url            = $menu_args['icon_url'] ?? null;
        $this->position            = $menu_args['position'] ?? null;
        $this->prefix              = $menu_args['prefix'] ?? null;
        $this->submenu_items       = $menu_args['submenu_items'] ?? null;
        $this->registered_submenus = $menu_args['registered_submenus'] ?? null;
        $this->network_admin       = $menu_args['network_admin'] ?? false;
        $this->_is_network_admin   = $menu_args['_is_network_admin'] ?? null;
        $this->user_options        = $menu_args['user_options'] ?? null;
        $this->parent              = $menu_args['parent'] ?? null;
        $this->is_frontend         = $menu_args['is_frontend'] ?? false;
        $this->backend_access      = $menu_args['backend_access'] ?? false;
        $this->evp_frontend_url    = $menu_args['evp_frontend_url'] ?? null;
        $this->page_current_screen = $menu_args['page_current_screen'] ?? null;
        $this->mcolor              = $menu_args['mcolor'] ?? '#0071A1';
        $this->bgcolor             = $menu_args['bgcolor'] ?? '#f7f6f6';
        $this->layout              = $menu_args['layout'] ?? 'container';
        // 'container-fluid'
        $this->side_panel_links = $menu_args['panel_links'] ?? [];
        // f9f9f9

        // core setups.
        $this->_hidden_submenus = [];
        $this->_child_submenus  = [];
        $this->submenu_hooks    = [];
        $this->asset_url        = $this->plugin_dir_url . 'asset/dist/';
        $this->style_scripts    = [
            'admin'           => 'evps-admin-styles-' . $this->object_id,
            'dashboard_admin' => 'evps-admin-dashboard-styles-' . $this->object_id,
            'litycss'         => 'evps-lity-styles-' . $this->object_id,
            'lityjs'          => 'evpjs-lity-script-' . $this->object_id,
        ];

        $this->set_prefix_menu_slug();
    }
}
