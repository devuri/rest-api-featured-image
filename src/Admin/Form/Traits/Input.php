<?php

namespace APIFeaturedImage\Admin\Form\Traits;

// @codingStandardsIgnoreFile.

trait Input
{
    /**
     * Generates an HTML input field with optional parameters and a submit button.
     *
     * This static function creates an HTML input field, allowing customization through
     * various parameters. It supports adding a label, custom classes, different input types,
     * and an optional submit button. It also includes accessibility features like `aria-describedby`.
     * The function adheres to WordPress coding standards, ensuring compatibility within WordPress projects.
     *
     * @param string $fieldtitle The name of the field, defaulting to 'item name'. It is sanitized and
     *                           used for both the input's name and its label. Two versions of sanitization
     *                           are performed: one for the field name (hyphenated) and one for the field ID (underscored).
     * @param string $val        The default value for the input field.
     * @param array  $args       Optional. An array of additional parameters to customize the input field. Possible keys include:
     *                           - 'required' (bool)   : If true, marks the input as required.
     *                           - 'class' (string)    : Additional CSS classes for the input element.
     *                           - 'type' (string)     : The type of the input (e.g., 'text', 'email').
     *                           - 'button' (string)   : If set, adds a submit button with the given label.
     *                           - 'hidden' (bool)     : If true, hides the input field.
     *                           - 'disabled' (bool)   : If true, disables the input field.
     *                           - 'info' (bool|string): Additional information or instructions for the input field.
     *                           - 'width' (string): The <td> width for the input field.
     *
     * @return string The HTML markup for the input field and optional button.
     */
    public static function input( $fieldtitle = 'item name', $val = '', array $args = [] ): string
    {
        $params = array_merge(
            [
                'name' => null,
                'required' => false,
                'class'    => 'uk-input form-control',
                'type'     => 'text',
                'button'   => null,
                'hidden'   => false,
                'disabled' => false,
                'info'     => false,
                'width'    => '200',
                'icon'    => null,
            ],
            $args,
        );

        // changed to item-name
        $field_title = self::sanitize( $fieldtitle, true );

        // dashicons
        $dashicon = $params['icon'] ?? null;

        // changed to item_name
        $field_id = self::sanitize( $fieldtitle );

        // field name ID.
        $field_name = $params['name'] ?? $field_id;

        // return built out the input
        return sprintf(
            '<!-- input field %s input -->
            <tr class="input-%s"><th>
				<span class="dashicons %s"></span>
                <label for="%s">%s</label>
            </th>
                <td width="%s">
                    <input type="%s" name="%s"
                    id="%s" aria-describedby="%s"
                    value="%s" class="%s" %s>
					<p class="description" id="%s">%s %s</p>
                </td>
				<td>%s<p class="description" style="visibility: hidden;">...</p></td>
            </tr>',
            // <!-- comment
            $field_title,
            // class
            $field_title,
            // dashicon
            $dashicon,
            // for label
            $field_id,
            // label
            ucwords( str_replace( '_', ' ', $field_id ) ),
            // width
            $params['width'],
            // type
            $params['type'],
            // name
            $field_name,
            // id
            $field_id,
            // describedby
            $field_title,
            // value
            $val,
            // input class
            esc_attr(  $params['class'] ),
            // disabled
            self::get_param('disabled', $params),
            // <p> id
            $field_title,
            // <p> content
            str_replace( '_', ' ', $field_id ),
            // <p> required text
            static::is_description( $params['info'] ),
            // submit button
            self::input_button( $params['button'] )
        );
    }

    /**
     * input_val.
     *
     * Get the input field $_POST data
     *
     * @param string $input_field input name
     *
     * @return null|string
     */
    public static function input_val( $input_field = null ): ?string
    {
        $input = sanitize_text_field( $_POST[ $input_field ] );
        if ( ! empty( $input ) ) {
            return $input;
        }

        return null;
    }

    /**
     * hidden Input Field.
     *
     * @param string $fieldtitle the name of the field
     * @param string $val
     *
     * @return string
     */
    public static function input_hidden( $fieldtitle = 'name', $val = '...' ): string
    {
        $fieldtitle = strtolower( $fieldtitle );

        // lets build out the input
        $input_hidden  = '<!-- input field ' . $fieldtitle . '_input -->';
        $input_hidden .= '<tr class="input">';
        $input_hidden .= '<th>';
        $input_hidden .= '</th>';
        $input_hidden .= '<td>';
        $input_hidden .= '<input type="hidden" name="' . self::sanitize( $fieldtitle ) . '" id="' . self::sanitize( $fieldtitle ) . '" value="' . $val . '" class="uk-input">';
        $input_hidden .= '</td>';
        $input_hidden .= '</tr>';
        $input_hidden .= '<!-- input field ' . $fieldtitle . '_input -->';

        return $input_hidden;
    }

    /**
     * Get the ID.
     *
     * @param string      $name
     * @param null|mixed $label
     * @param mixed      $description
     */
    public static function button( string $name='Send', $label = null, $description = '', ?string $bg_color = null ): string
    {
        $button_id = self::sanitize( $name );

        $button = '<a name="' . $button_id . '" class="button button-secondary button-large" id="' . $button_id . '" href="#">' . $name . '</a>';

        return self::info( $label, $button, $description, false, $bg_color);
    }

    /**
     * Info for the current item.
     *
     * @param string     $th_label    the heading label
     * @param string     $text        the text description.
     * @param bool       $hr          whether to add bottom border.
     * @param null|mixed $description
     * @param null|mixed $bg_color
     *
     * @return string table row.
     */
    public static function info(?string $th_label = null, $text = null, $description = null, $hr = false, $bg_color = null): string
    {
        $border = $hr ? 'border-bottom: solid thin #ccd0d4;' : '';

        if ( $bg_color ) {
            $background = "background-color: $bg_color;";
        } else {
            $background = '';
        }

        return sprintf(
            '<tr style="%s %s">
                <th>
				<span style="font-size: large; font-weight: 400;color: #646970;">%s<span>
				</th>
                <td>%s <p id="desc_%s" class="description">%s</p></td>
            </tr>',
            $border,
            $background,
            $th_label,
            $text,
            self::sanitize( $th_label ),
            $description,
        );
    }

    /**
     * Hidden input field for channel url.
     *
     * @param string $class additional class.
     *
     * @return void
     */
    public static function channel_url( string $class = '' ): void
    {
        ?><tr class="input-channel-url <?php echo esc_attr( $class ); ?>"><th>
				<label for="channel_url">Channel Url</label>
			</th>
				<td>
					<input type="text" name="channel_url" id="channel_url" aria-describedby="channel-url" value=" " class="hidden-cls">
				<p class="description" id="channel-url">channel url</p>
			</td>
		</tr>
		<?php
    }

    /**
     * Renders a table row containing an input field within an HTML form.
     *
     * This method generates a table row (`<tr>`) with a title (`<th>`) and a field (`<td>`).
     * The field can be a text input or a checkbox depending on the `$type` parameter.
     * An optional description can be displayed below the input field.
     *
     * @param string $title         The label displayed in the `<th>` element.
     * @param string $field_title   The name attribute for the input field.
     * @param string $value         Optional. The current value of the input field. Default empty string.
     * @param string $description   Optional. A description displayed below the input field. Default empty string.
     * @param string $type          Optional. The type of the input field (e.g., 'text', 'checkbox'). Default 'text'.
     * @param string $checked_value Optional. The value used to determine if the checkbox should be checked.
     *                              Only used if `$type` is 'checkbox'. Default '1'.
     */
    public static function inputRow( string $title, string $field_title, ?string $value = '', string $description = '', string $type = 'text', string $checked_value = '1' ): void
    {
        ?>
        <tr valign="top">
            <th scope="row">
                <?php echo esc_html( $title ); ?>
            </th>
            <td>
                <?php if ( 'checkbox' === $type ) { ?>
                    <input type="checkbox" name="<?php echo esc_attr( $field_title ); ?>" value="<?php echo esc_attr( $checked_value ); ?>"
                        <?php checked( $value, $checked_value ); ?> />
                <?php } else { ?>
                    <input type="<?php echo esc_attr( $type ); ?>"
                           name="<?php echo esc_attr( $field_title ); ?>"
                           value="<?php echo esc_attr( $value ); ?>" />
                <?php } ?>
                <?php if ( ! empty( $description ) ) { ?>
                    <p class="description">
                        <?php echo esc_html( $description ); ?>
                    </p>
                <?php } ?>
            </td>
        </tr>
        <?php
    }

    protected static function get_param( string $key, $params = [] )
    {
        return $params[$key] ?? null;
    }

    /**
     * Set input button for inline buttons.
     *
     * @param ?string $button
     *
     * @return null|string
     */
    protected static function input_button( ?string $button ): ?string
    {
        if ( ! \is_null( $button ) ) {
            $button_id = 'submit_' . self::sanitize( $button );

            return static::submit_button( ucwords($button) , 'primary large', $button_id );
        }

        return null;
    }
}
