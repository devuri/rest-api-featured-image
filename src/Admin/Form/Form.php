<?php

namespace APIFeaturedImage\Admin\Form;

use APIFeaturedImage\Admin\Form\Traits\CategoryList;
use APIFeaturedImage\Admin\Form\Traits\DataList;
use APIFeaturedImage\Admin\Form\Traits\Input;
use APIFeaturedImage\Admin\Form\Traits\Nonce;
use APIFeaturedImage\Admin\Form\Traits\PostType;
use APIFeaturedImage\Admin\Form\Traits\Select;
use APIFeaturedImage\Admin\Form\Traits\TextArea;

class Form
{
    use CategoryList;
    use DataList;
    use Input;
    use Nonce;
    use PostType;
    use Select;
    use TextArea;

    public const ADMINVERSION = '1.3.2';

    public $processing = false;

    /**
     * @psalm-param 'Nothing to Update <strong>No Post Types Have Been Set</strong> !!!'|'Options updated'|'Post Types Have Been Updated !!!' $message
     * @psalm-param 'success'|'warning' $class
     *
     * @param mixed $element_id
     */
    public static function user_feedback(
        string $message = 'Options updated',
        string $class = 'success',
        $element_id = 'user-feedback'
    ): string {
        return sprintf(
            __(
                '<div style="font-size: small; margin: 20px; text-transform: capitalize; "
			id="%1$s" class="notice notice-%3$s is-dismissible">
			<p>%2$s</p></div>'
            ),
            $element_id,
            $message,
            $class,
        );
    }

    /**
     * thickbox builder.
     *
     * @param string $linktext
     * @param string $id
     *
     * @return string .
     */
    public static function thickbox_link( $linktext = 'click here', $id = '' ): string
    {
        return sprintf(
            '<a href="#TB_inline?width=auto&inlineId=%s" class="thickbox">%s</a>',
            $id,
            $linktext,
        );
    }

    /**
     * Retrieves and escapes an option value for HTML attribute use.
     *
     * Fetches an option from the WordPress database using `get_option` and
     * sanitizes it with `esc_attr` for safe HTML attribute inclusion.
     *
     * @param string $option Name of the option to retrieve.
     *
     * @return string Escaped option value, or an empty string if the option does not exist.
     */
    public static function get_option( $option )
    {
        return esc_attr( get_option( $option ) );
    }

    /**
     * is_description.
     *
     * set field as required, defaults to false.
     * Also used for input description since we pass back the value as output.
     *
     * @param mixed $description_info
     *
     * @return null|string
     */
    public static function is_description( $description_info = false ): ?string
    {
        if ( $description_info ) {
            return ' <span style="font-size: unset; color: #939698;" class="description">' . esc_html( $description_info ) . '</span>';
        }

        return null;
    }

    public static function upload( $fieldname = 'upload_image_button', $val = 'Upload Image', $required = false, $type = 'button' ): string
    {
        $fieldname      = strtolower( $fieldname );
        $upload_button  = '<tr class="input">';
        $upload_button .= '<th>';
        $upload_button .= '<label for="' . str_replace( ' ', '_', $fieldname ) . '">';
        $upload_button .= ucwords( str_replace( '_', ' ', $fieldname ) );
        $upload_button .= '</label>';
        $upload_button .= '</th>';
        $upload_button .= '<td>';
        $upload_button .= '<!-- upload field ' . $fieldname . '_input -->';
        $upload_button .= '<input id="' . str_replace( ' ', '_', $fieldname ) . '"';
        $upload_button .= 'type="' . $type . '" class="button"';
        $upload_button .= 'value="' . $val . '" />';
        $upload_button .= '<p class="description" id="' . str_replace( ' ', '-', $fieldname ) . '-description">';
        $upload_button .= strtolower( str_replace( '_', ' ', $fieldname ) );
        $upload_button .= '</p>';
        $upload_button .= '</td>';
        $upload_button .= '</tr>';
        $upload_button .= '<!-- input field ' . $fieldname . '_input -->';

        return $upload_button;
    }

    /**
     * page_list building our own $pages array.
     *
     * @param array $arg [description]
     *
     * @see https://developer.wordpress.org/reference/functions/get_pages/
     *
     * @return array
     */
    public static function page_list( $arg = [] ): array
    {
        $arg = [
            'sort_column' => 'post_date',
            'sort_order'  => 'desc',
        ];
        // get the pages
        $pages     = get_pages( $arg );
        $page_list = [];
        foreach ( $pages as $pkey => $page ) {
            $page_list[ $page->ID ] = $page->post_title;
        }

        return $page_list;
    }

    public static function tr( $html = null, $hr = '<hr>' ): string
    {
        return '<tr style="border-bottom: solid thin #e4e5e6;">' . $html . '</tr>';
    }

    /**
     * Make Table.
     *
     * Use this to create a table for the form
     *
     * @param string $tag     decide to open or close table
     * @param string $tbclass ad css class
     *
     * @return null|string
     */
    public static function table( $tag = 'close', $tbclass = '' ): ?string
    {
        if ( 'open' === $tag ) {
            return '<table class="form-table ' . $tbclass . '" role="presentation"><tbody>';
        }

        if ( 'close' === $tag ) {
            return '</tbody></table>';
        }

        return null;
    }

    /**
     * [submit_button description].
     *
     * @param string $text The text of the button. Default 'Save Changes'.
     * @param string $type The type and CSS class(es) of the button. Core values include 'primary', 'small', and 'large'.
     * @param string $name name of the submit button
     * @param string $wrap True if the output button should be wrapped in a paragraph tag, false otherwise.
     *
     * @return string the button html.
     *
     * @see https://developer.wordpress.org/reference/functions/get_submit_button/
     */
    public static function submit_button( $text = 'Save Changes', $type = 'primary large', $name = 'submit', $wrap = '' ): string
    {
        return get_submit_button( $text, $type, $name, $wrap );
    }

    public static function loader( string $load ): void
    {
        if ( $load ) {
            self::loading();
        }
    }

    /**
     * Define user access level for the admin form, who can acces and use the form.
     *
     * @param string $role .
     *
     * @return string
     */
    public static function access_level( $role = 'admin' ): string
    {
        $access = [
            'admin'       => 'manage_options',
            'editor'      => 'delete_others_pages',
            'author'      => 'publish_posts',
            'contributor' => 'edit_posts',
            'subscriber'  => 'read',
        ];

        return $access[ $role ];
    }

    /**
     * Allow the user to add a custom Title,
     * Instead of using the title from oEmbed.
     *
     * @param string $fieldname .
     *
     * @return string
     */
    public static function custom_title( $fieldname = 'Title' ): string
    {
        $fieldname  = strtolower( $fieldname );
        $get_title  = '<tr class="input-custom-title hidden"><th>';
        $get_title .= '<label for="' . str_replace( ' ', '_', $fieldname ) . '">' . ucwords( str_replace( '_', ' ', $fieldname ) ) . '</label>';
        $get_title .= '</th>';
        $get_title .= '<td><input type="text" name="' . str_replace( ' ', '_', $fieldname ) . '" id="' . str_replace( ' ', '_', $fieldname ) . '" aria-describedby="' . str_replace( ' ', '-', $fieldname ) . '-description" value=" " class="uk-input">';
        $get_title .= '<p class="description" id="' . str_replace( ' ', '-', $fieldname ) . '-description">' . $fieldname . '<strong>.</strong>';
        $get_title .= '</p></td></tr>';

        return $get_title;
    }

    public static function jquery_for_create_category(): void
    {
        ?>
        <script type="text/javascript">
            jQuery( document ).ready( function( $ ) {

                jQuery('#custom_category').on('click', function(){
                    $(".input-create-category").fadeOut(0.1);
                    $(".input-create-category").addClass('hidden');
                    $(".input-select-category").removeClass('hidden');
                    if ($('#custom_category').is(":checked")) {
                        $(".input-create-category").fadeIn(1);
                        $(".input-create-category").removeClass('hidden');
                        $(".input-select-category").addClass('hidden');
                    }
                });
            });
        </script>
        <?php
    }

    public static function create_custom_category(): void
    {
        ?>
        <td>
			<input type="checkbox" id="custom_category" name="custom_category">
	        <label for="custom_category">
				Create Category
			</label><br>
	        <small>
				create a category for this item ( will override category select )
			</small>
		</td>
        <?php
    }

    public static function loading( $name = 'update-loader', $css = [] ): void
    {
        $prong = '<div class="prong"><div class="inner"></div></div>';
        echo '<div class="loading ' . $name . ' hidden">';
        echo '<div class="loader">';
        for ( $i = 0; $i <= 12; $i++ ) {
            echo $prong;
        }
        echo '</div>';
        echo '</div>';
    }

    public static function css_style_loading( $css = [] ): void
    {
        ?>
		<style media="screen">
		.loading {
			padding: <?php echo $css['padding']; ?>;
			padding-bottom: <?php echo $css['padding-bottom']; ?>;
		}
		.loader {
		width:<?php echo $css['size']; ?>;
		height: <?php echo $css['size']; ?>;
		border-radius: 200px;
		position: relative;
		animation: rotate 0.8s steps(12, end) infinite;
		}
		.loader .prong {
		position: absolute;
		height: 50%;
		width: 16px;
		left: calc(50% - 8px);
		transform-origin: bottom;
		}
		.loader .prong .inner {
		background: #34657f;
		border-radius: 12px;
		position: absolute;
		width: 100%;
		top: 0;
		height: 50%;
		}
		.loader .prong:nth-of-type(1) {
		opacity: 0.08;
		transform: rotate(30deg);
		}
		.loader .prong:nth-of-type(2) {
		opacity: 0.16;
		transform: rotate(60deg);
		}
		.loader .prong:nth-of-type(3) {
		opacity: 0.24;
		transform: rotate(90deg);
		}
		.loader .prong:nth-of-type(4) {
		opacity: 0.32;
		transform: rotate(120deg);
		}
		.loader .prong:nth-of-type(5) {
		opacity: 0.4;
		transform: rotate(150deg);
		}
		.loader .prong:nth-of-type(6) {
		opacity: 0.48;
		transform: rotate(180deg);
		}
		.loader .prong:nth-of-type(7) {
		opacity: 0.56;
		transform: rotate(210deg);
		}
		.loader .prong:nth-of-type(8) {
		opacity: 0.64;
		transform: rotate(240deg);
		}
		.loader .prong:nth-of-type(9) {
		opacity: 0.72;
		transform: rotate(270deg);
		}
		.loader .prong:nth-of-type(10) {
		opacity: 0.8;
		transform: rotate(300deg);
		}
		.loader .prong:nth-of-type(11) {
		opacity: 0.88;
		transform: rotate(330deg);
		}
		.loader .prong:nth-of-type(12) {
		opacity: 0.96;
		transform: rotate(360deg);
		}

		@keyframes rotate {
		from {
			transform: rotate(0deg);
		}
		to {
			transform: rotate(360deg);
		}
		}
		</style>
		<?php
    }

    /**
     * Outputs an HTML spinner with accompanying CSS for a rotating animation.
     *
     * @param array $size An optional array to customize the spinner's size.
     *                    The first element is used for both width and height if only one value is provided.
     *                    If two values are provided, the first is the width and the second is the height.
     *
     * @return void
     */
    public function spinner( array $size = [] ): void
    {
        // Assign default size values
        $width  = $size[0] ?? '60px';
        $height = $size[1] ?? $width;
        // Use the same value for height if only one size is provided

        ?>
	    <div class="spinner"></div>

	    <style>
	        .spinner {
	            width: <?php echo htmlspecialchars( $width ); ?>;
	            height: <?php echo htmlspecialchars( $height ); ?>;
	            border: 6px solid #000; /* Black border */
	            border-top-color: #ff0000; /* Red top border */
	            border-radius: 50%;
	            animation: spin 0.8s linear infinite;
	            margin: 100px auto;
	        }

	        @keyframes spin {
	            to {
	                transform: rotate(360deg);
	            }
	        }
	    </style>
	    <?php
    }

    /**
     * Sanitizes a given field name to ensure its safety for use as a key or file name.
     *
     * This method employs two WordPress-sanitization functions. Firstly, `sanitize_file_name`
     * is applied to the field name to remove special characters and ensure valid file name characters.
     * Secondly, `sanitize_key` is used to sanitize the string for use as a key, which involves
     * lowercasing and removing characters that are not alphanumeric or dashes.s
     *
     * @param string $fieldname The field name to be sanitized.
     *
     * @return string Returns the sanitized version of the field name suitable for use as a key or file name.
     */
    protected static function sanitize( string $fieldname, bool $use_dashes = false ): string
    {
        $field_id = sanitize_key(
            sanitize_file_name( $fieldname )
        );

        if ( $use_dashes ) {
            return $field_id;
        }

        return str_replace( '-', '_', $field_id );
    }
}
