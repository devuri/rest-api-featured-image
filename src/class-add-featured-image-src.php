<?php

namespace SwitchWebdev;

/**
 *
 */
class Add_Featured_Image_Src
{

	/**
	 * set the featured image size
	 * @var string $image_size
	 */
	private $image_size;

	/**
	 * __construct setup and image size
	 * @param string $size [description]
	 */
	public function __construct( $size = 'thumbnail'){
		$this->image_size = $size;
	}

	/**
	 * add the featured image url
	 */
	public function add_src_field(){
		add_action( 'rest_api_init', array( $this, 'featured_image_src_field'), 99 );
	}

	/**
	 * get the featured image
	 * @param  int $attachment_id [description]
	 * @param  string $size          [description]
	 * @return array                [description]
	 * @link https://developer.wordpress.org/reference/functions/wp_get_attachment_image_src/
	 */
	private function get_media( $id = null ){
		$featured_image = wp_get_attachment_image_src( $id , $this->image_size );
		return $featured_image;
	}

	/**
	 * setup the featured image src field
	 * @return [type] [description]
	 * @link https://developer.wordpress.org/reference/functions/register_rest_field/
	 */
	public function featured_image_src_field() {
	    register_rest_field( 'post', 'featured_media_src_url', array(
	          'get_callback' => function ( $post ) {
								$image_src = $this->get_media( $post['featured_media'] );
						 		return $image_src[0];
						  	},
						'update_callback' => null,
						'schema' => null
	        )
	    );
	}
}
