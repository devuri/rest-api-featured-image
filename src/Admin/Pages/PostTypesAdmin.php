<?php

namespace APIFeaturedImage\Admin\Pages;

use APIFeaturedImage\Admin\Form\Form;
use APIFeaturedImage\Admin\PageInterface;
use APIFeaturedImage\Admin\Validate;

class PostTypesAdmin implements PageInterface
{
    public static function render(): void
    {
        static::process( $_POST );
        ?>
		  <h2>Set Post Types</h2>
		    <p>
		      The <strong>Post Types</strong> setup allows you to easily add featured media src endpoint field to any post type.
		    </p>
		<div id="post-posttype-form">
				<form action="" method="POST"	enctype="multipart/form-data">
		        <?php

                $getpost_types = Form::post_types();

        echo Form::table( 'open' );

        foreach ( $getpost_types  as $post_type ) {
            if ( \in_array( $post_type->name, get_option( APIFI_PT_OPTION, [] ), true ) ) {
                $is_checked   = 'checked';
                $featuredmedia  = ' API endpoint set: featured_media_src_url';
            } else {
                $is_checked   = null;
                $featuredmedia  = null;
            }

            echo self::renderCheckbox($post_type, $is_checked, $featuredmedia);
        }

        echo Form::table( 'close' );

        wp_nonce_field( 'update-posttype', '_update_posttype_nonce' );

        echo Form::submit_button( 'Save Post Types', 'primary large', 'submit_update_posttype' );

        ?>
		        </form>
				<p/>
				The WordPress REST API
				<a target="_blank" href="<?php echo esc_url( 'https://developer.wordpress.org/rest-api/reference/#rest-api-developer-endpoint-reference' ); ?>">
					Developer Endpoint Reference
				</a>
		</div><!--frmwrap-->
        <?php
    }

    public static function process( ?array $_post ): ?bool
    {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die();
        }

        if ( \is_null( $_post ) || empty( $_post ) ) {
            return null;
        }

        if ( isset( $_post['submit_update_posttype'] ) ) {
            if ( ! Validate::request( 'update-posttype', '_update_posttype_nonce' ) ) {
                wp_die();
            }

            if ( ! Validate::user_cap() ) {
                wp_die();
            }

			$post_type_names = [];

            if ( isset( $_post['post_type_names'] ) ) {
                foreach ( $_post['post_type_names'] as $key => $post_type ) {
                    $post_type_names[ $key ] = sanitize_text_field( $post_type );
                }

                update_option( APIFI_PT_OPTION, $post_type_names );
                echo Form::user_feedback( 'Post Types Have Been Updated !!!' );

                return true;
            }

			update_option( APIFI_PT_OPTION, $post_type_names );

            echo Form::user_feedback( 'Nothing to Update <strong>No Post Types Have Been Set</strong> !!!', 'warning' );

            return false;
        }

        return null;
    }

    /**
     * @param null|string $is_checked
     * @param null|string $featuredmedia
     *
     * @psalm-param 'checked'|null $is_checked
     * @psalm-param ' API endpoint set: featured_media_src_url'|null $featuredmedia
     */
    public static function renderCheckbox(\WP_Post_Type $post_type, ?string $is_checked, ?string $featuredmedia): ?string
    {
        if (\in_array($post_type->name, self::excluded(), true)) {
            return null;
        }

        return 	Form::input(
            $post_type->labels->singular_name,
            esc_attr($post_type->name),
            [
                'name' => "post_type_names[$post_type->name]",
                'icon' => $post_type->menu_icon,
                'type' => 'checkbox',
                'disabled' => esc_attr($is_checked),
                'info' => esc_html($featuredmedia),
            ]
        );
    }
    /**
     * @return string[]
     *
     * @psalm-return list{'elementor_library', 'elementor-hf', 'attachment'}
     */
    private static function excluded(): array
    {
        return [
            'elementor_library',
            'elementor-hf',
            'attachment',
        ];
    }
}
