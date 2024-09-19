<?php

namespace APIFeaturedImage\Admin;

interface AdminCoreInterface
{
    public function set_submenus( ?array $submenus = null ): self;

    /**
     * Init factory.
     *
     * @param array $menu_args
     * @param array $submenu_items submenu items.
     *
     * @since 6.0
     */
    public static function init( array $menu_args, array $submenu_items = [] ): self;

    /**
     * Lets create the menu.
     *
     * We can Initialize as network only by setting `network_admin` or is_network to true.
     * else just setup regular website admin.
     *
     * @param bool $is_network set this to true if its a network admin.
     *
     * @see https://developer.wordpress.org/reference/hooks/network_admin_menu/
     * @see https://developer.wordpress.org/reference/hooks/admin_menu/
     */
    public function create( $is_network = false ): self;

    /**
     * Register a new setting for the field.
     */
    public function register_user_options();

    /**
     * Admin Page Title.
     *
     * @since 1.0
     *
     * @return string
     */
    public function menu_title(): string;

    /**
     * Load the admin page header.
     *
     * @return void
     */
    public function header( array $submenu ): void;

    /**
     * Display section for evp_admin_notice.
     *
     * @param object $instance The instance of the current class (AdminCore).
     *
     * @return void
     */
    public static function admin_notice_section( $instance ): void;

    /**
     * Display notice section.
     */
    public function notice_section_banner();

    /**
     * Renders the content for a specific admin page.
     *
     * This method is responsible for generating the content of an admin page
     * within the WordPress admin area. It utilizes WordPress action hooks to
     * manage the output. The content is determined based on the provided page
     * name and optional submenu item details.
     *
     * @param null|string $spage        Optional. The specific page to render. Default null.
     * @param mixed       $submenu_item Optional. Details of the submenu item, if applicable. Default null.
     * @param null|string $submenu_slug Optional. The slug of the submenu, if applicable. Default null.
     *
     * @return void This method does not return any value.
     *
     * @hook "_evp_admin_page_{$admin_page_hook}" Allows for custom actions to be executed during the rendering of the admin page. The dynamic part of the hook name is derived from the page name, converted to snake_case. The `$hooked_args` array is passed as a parameter, containing details such as the object instance, hook name, page name, slug, and submenu items.
     */
    public function page_content( ?string $spage = null, $submenu_item = null, ?string $submenu_slug = null ): void;


    /**
     * Load the admin page header.
     *
     * @param mixed $submenu_item
     *
     * @return void
     */
    public function footer( $submenu_item ): void;

    /**
     * Sidebar action hook.
     *
     * @return string the slug name.
     */
    public function sidebar(): string;

    /**
     * Get the Page.
     *
     * @return null|string
     *
     * @since 1.0
     */
    public function current_page_title(): ?string;

    /**
     * The main callback should be implemented by child class.
     *
     * @since 1.0
     */
    public function callback();

    /**
     * Get the page name.
     *
     * @return string
     */
    public function page_name(): string;

    /**
     * Build the admin url.
     *
     * @param string $slug .
     *
     * @return string .
     */
    public function make_admin_url( $slug ): string;

    /**
     * Menu_slug.
     *
     * Get the menu slug without the $prefix
     *
     * @return string
     */
    public function menu_slug(): string;

    /**
     * Build of the full Menu and sub menus.
     *
     * @see https://developer.wordpress.org/reference/functions/add_menu_page/
     * @since 1.0
     */
    public function build_menu(): void;

    /**
     * Return the correct submenu value.
     *
     * @param mixed $key
     * @param array $submenus
     *
     * @return string
     */
    public function get_property( $key, array $submenus );

    /**
     * Set submenu capability.
     *
     * @param null|string $subkey the submenu item key.
     */
    public function sub_capability( ?string $subkey = null ): ?string;

    /**
     * Get child submenu items.
     *
     * @return null|array
     */
    public function get_child(): ?array;

    /**
     * Asset url.
     */
    public function asset_url(): string;
}
