<?php

namespace SwitchWebdev;

/**
 *
 */
class Add_Featured_Image_Src
{

	/**
	 * set the featured image size
	 *
	 * @var string $image_size
	 */
	private $image_size;

	/**
	 * set the post types
	 *
	 * @var array $post_types
	 */
	private $post_types = [];

	/**
	 * __construct setup and image size
	 * @param array 	$types post types to add featured media
	 * @param string 	$size size of the featured image
	 */
	public function __construct( $types = array() , $size = 'thumbnail' ){
		$this->image_size = $size;
		$this->post_types = $types;
	}

	/**
	 * if the post type is not set (empty array()) just use post
	 */
	private function get_post_types(){
		if ( empty($this->post_types) ) {
			$this->post_types = array('post');
		}
		return $this->post_types;
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
	 * add the featured image url
	 */
	public function add_src_field(){

		foreach ( $this->get_post_types() as $post_type ) {
			add_action( 'rest_api_init', function() use ( $post_type ) {
			    register_rest_field( $post_type , 'featured_media_src_url', array(
			          'get_callback' => function ( $post ) {
										$image_src = $this->get_media( $post['featured_media'] );
								 		return $image_src[0];
								  	},
								'update_callback' => null,
								'schema' => null
			        )
			    );
			}, 99 );
		}
	}

	// /**
	//  * setup the featured image src field
	//  * @return [type] [description]
	//  * @link https://developer.wordpress.org/reference/functions/register_rest_field/
	//  */
	// public function featured_image_src_field() {
	//     register_rest_field( 'post', 'featured_media_src_url', array(
	//           'get_callback' => function ( $post ) {
	// 							$image_src = $this->get_media( $post['featured_media'] );
	// 					 		return $image_src[0];
	// 					  	},
	// 					'update_callback' => null,
	// 					'schema' => null
	//         )
	//     );
	// }
}
