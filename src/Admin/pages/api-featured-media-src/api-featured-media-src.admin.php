<?php

  $dashicon_style = 'style="padding: 8px; vertical-align: middle;"';

	// Save Data
	if ( isset( $_POST['update_postype'] ) ) :

		// lets verify the nonce
		if ( ! $this->form()->verify_nonce()  ) {
			wp_die($this->form()->user_feedback('Verification Failed !!!' , 'error'));
		}

    /**
     * Make sure this is set if not load empty array
     * @var [type]
     */
    if ( ! isset( $_POST['postype_name'] ) ) {
      $wpfeaturedtypes = array();
      update_option('wpfeatured_media_src_post_types', $wpfeaturedtypes );
      echo $this->form()->user_feedback(' Updated <strong>No Post Types Have Been Set</strong> !!!', 'warning');
    } else {
      // sanitize
      foreach ($_POST['postype_name'] as $pkey => $post_type) {
        $wpfeaturedtypes[$pkey] = sanitize_text_field($post_type);
      }
      // update and provide feedback
  	  update_option('wpfeatured_media_src_post_types', $wpfeaturedtypes );
  		echo $this->form()->user_feedback('Post Types Have Been Updated !!!');
    }


	endif;


?><div id="frmwrap" >
  <h2><?php _e('Set Post Types to Add  Featured Media Source API field'); ?></h2>
  <hr/>
  <div class="description">
    <?php _e('Add the Featured Image src URL Field'); ?>
  </div>
<p/>
<form action="" method="POST"	enctype="multipart/form-data"><?php
  /**
   * get the post types
   * @var array
   */
  $args = array(
      'public'   => true,
  );
  $getpost_types = get_post_types( $args, 'objects' );

  /**
   * lets exclude these
   * @var array
   */
  $excludedtypes = array();
  $excludedtypes[] = 'elementor_library';
  $excludedtypes[] = 'elementor-hf';


  /**
   * Get The Public Post Types
   * @var [type]
   */
  foreach ( $getpost_types  as $post_type ) {


    /**
     *  checked or not
     * @var [type]
     */
  if ( in_array($post_type->name,get_option('wpfeatured_media_src_post_types') )) {
    $checkpostype   = 'checked';
    $featuredmedia  = 'featured_media_src_url';
    $ptype_status   = 'style=" background-color: #dff0d8;border-color: #d6e9c6;color: #4B8A3B; padding: 8px;border-bottom: solid thin;"';
  } else {
    $checkpostype   = '';
    $featuredmedia  = '';
    $ptype_status   = 'style=" background-color: #F5F5F5;color: #555555; padding: 8px;"';
  }

  /**
   * exclude
   * @var [type]
   */
  if (in_array($post_type->name,$excludedtypes)) {
    // code...
  } else {
    /**
     * build out the checkboxes
     * @var [type]
     */
    echo '<div '.$ptype_status.' id="ppt_wrap '.$post_type->name.'" >';
    echo '<span '.$dashicon_style.' class="wp-menu-image wll-small-admin-dashicons '.$post_type->menu_icon.'"></span>';
    echo '<input type="checkbox" name="postype_name['.$post_type->name.']" value="'.$post_type->name.'" '.$checkpostype.'>';
    echo '<label for="'.$post_type->name.'">';
    _e($post_type->labels->singular_name.' <span style="font-size: smaller;color: #a59b9b;"> '.$post_type->name.'</span> <span style="color: #4b8a3b;">'. $featuredmedia.' </span>');
    echo '</label>';
    echo '</div>';
  }


  } // end foreach
  echo '<p/>';

  // generate nonce_field
  $this->form()->nonce();

  // submit button
  echo $this->form()->submit_button('Save Post Types', 'primary large', 'update_postype');
    ?>
</form>
<br/>
</div><!--frmwrap-->
