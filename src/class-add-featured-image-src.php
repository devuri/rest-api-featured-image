<?php
namespace SwitchWebdev;

/**
 *
 */
class Add_Featured_Image_Src
{

	/**
	 * add src field
	 */
	public function add_src_field(){
		add_action( 'rest_api_init', array( $this, 'featured_image_src_field'), 99 );
	}

	/**
	 * setup the featured image src field
	 * @return [type] [description]
	 * @link https://developer.wordpress.org/reference/functions/register_rest_field/
	 */
	public function featured_image_src_field() {
	    register_rest_field( 'post', 'featured_image_src', array(
	          'get_callback' => function ( $post ) {
						 		$image_src_arr = wp_get_attachment_image_src( $post['featured_media'] , 'large' );
						 		return $image_src_arr[0];
						 	},
						'update_callback' => null,
						'schema' => null
	        )
	    );
	}
}
