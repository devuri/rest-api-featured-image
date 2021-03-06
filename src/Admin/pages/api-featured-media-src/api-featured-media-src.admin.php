<?php

	// @codingStandardsIgnoreFile

	$dashicon_style = 'style="padding: 8px; vertical-align: middle;"';

	// Save Data.
	if ( isset( $_POST['update_postype'] ) ) :

		// lets verify the nonce.
		if ( ! $this->form()->verify_nonce()  ) {
			wp_die($this->form()->user_feedback( 'Verification Failed !!!', 'error' ) );
		}

	    /**
	     * Make sure this is set if not load empty array
	     */
	    if ( ! isset( $_POST['postype_name'] ) ) {
	      	$wpfeaturedtypes = array();
	      	update_option( 'wpfms_post_types', $wpfeaturedtypes );
	      	echo $this->form()->user_feedback( ' Updated <strong>No Post Types Have Been Set</strong> !!!', 'warning' );
	    } else {
	      	// sanitize.
	      	foreach ( $_POST['postype_name'] as $pkey => $post_type ) {
	        $wpfeaturedtypes[ $pkey ] = sanitize_text_field( $post_type );
	    }
	      	// update and provide feedback.
	  	  	update_option( 'wpfms_post_types', $wpfeaturedtypes );
	  		echo $this->form()->user_feedback( 'Post Types Have Been Updated !!!' );
	    }

	endif;


?>
<div id="frmwrap" >
	<h2>
		<?php _e( 'Set Post Types to Add  Featured Media Source API field' ); ?></h2>
	<hr/>
	  	<div class="description">
	    	<?php _e( 'Add the Featured Image src URL Field' ); ?>
	 	</div>
<p/>
<form action="" method="POST"	enctype="multipart/form-data">
	<?php

	/**
	 * Get the post types
	 */
	$args = array(
	    'public' => true,
	);
	$getpost_types = get_post_types( $args, 'objects' );

	/**
	 * Lets exclude these
	 */
	$excludedtypes = array();
	$excludedtypes[] = 'elementor_library';
	$excludedtypes[] = 'elementor-hf';
	$excludedtypes[] = 'attachment';

	/**
	 * Get The Public Post Types
	 */
	foreach ( $getpost_types  as $post_type ) {

    /**
     *  Checked or not
     */
	if ( in_array( $post_type->name, get_option( 'wpfms_post_types', array() ), true ) ) {
		$checkpostype   = 'checked';
		$featuredmedia  = 'featured_media_src_url';
		$ptype_status   = 'style=" background-color: #dff0d8;border-color: #d6e9c6;color: #4B8A3B; padding: 8px;border-bottom: solid thin;"';
	} else {
		$checkpostype   = '';
		$featuredmedia  = '';
		$ptype_status   = 'style=" background-color: #F5F5F5;color: #555555; padding: 8px;"';
	}

	/**
	 * Exclude
	 */
	if ( in_array( $post_type->name, $excludedtypes, true ) ) {
	    echo '';
		} else {

		    /**
		     * Build out the checkboxes
		     */
		    echo '<div ' . esc_attr( $ptype_status ) . ' id="ppt_wrap ' . esc_attr( $post_type->name ) . '" >';
		    echo '<span ' . esc_attr( $dashicon_style ) . ' class="wp-menu-image wll-small-admin-dashicons ' . esc_attr( $post_type->menu_icon ) . '"></span>';
		    echo ' <input type="checkbox" name="postype_name[' . esc_attr( $post_type->name ) . ']" value="' . esc_attr( $post_type->name ) . '" ' . esc_attr( $checkpostype ) . '>';
		    echo '<label for="' . esc_attr( $post_type->name ) . '">';
		    echo $post_type->labels->singular_name . ' <span style="font-size: smaller;color: #a59b9b;"> ' . esc_attr( $post_type->name ) . '</span> <span style="color: #4b8a3b;">' . esc_attr( $featuredmedia ) . ' </span>';
		    echo '</label>';
		    echo '</div>';
		}
	} // end foreach
	echo '<p/>';

	// generate nonce_field.
	$this->form()->nonce();

	// submit button.
	echo $this->form()->submit_button( 'Save Post Types', 'primary large', 'update_postype' );
    ?>
</form>
<hr/>
The WordPress REST API
<a target="_blank" href="<?php echo esc_url( 'https://developer.wordpress.org/rest-api/reference/#rest-api-developer-endpoint-reference' ); ?>">
	Developer Endpoint Reference
</a>
</div><!--frmwrap-->
