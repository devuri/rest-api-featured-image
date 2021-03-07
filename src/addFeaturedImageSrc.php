<?php

namespace SimFeaturedMediaSrc;

class addFeaturedImageSrc // @codingStandardsIgnoreLine
{

	/**
	 * Set the featured image size
	 *
	 * 'thumbnail'  Thumbnail (Note: different to Post Thumbnail)
	 * 'medium'     Medium resolution
	 * 'large'      Large resolution
	 * 'full'       Original resolution
	 *
	 * @var string $image_size
	 */
	private $image_size;

	/**
	 * Set the post types
	 *
	 * @var array $post_types
	 */
	private $post_types = array();

	/**
	 * __construct setup and image size
	 *
	 * @param array  $types post types to add featured media.
	 * @param string $size size of the featured image.
	 */
	public function __construct( $types = array(), $size = 'thumbnail' ) {
		$this->post_types = $types;
		$this->image_size = $size;
	}

	/**
	 * If the post type is not set (empty array()) just use post
	 */
	private function get_post_types() {
		if ( empty( $this->post_types ) ) {
			$this->post_types = array( 'post' );
		}
		return $this->post_types;
	}

	/**
	 * Get the featured image
	 *
	 * @param  int $id [description].
	 * @return array [description]
	 * @link https://developer.wordpress.org/reference/functions/wp_get_attachment_image_src/
	 */
	private function get_media( $id = null ) {
		$featured_image = wp_get_attachment_image_src( $id, $this->image_size );
		return $featured_image;
	}

	/**
	 * Add the featured image url
	 */
	public function add_src_field() {

		/**
		 * get post types and add featured media to each.
		 */
		foreach ( $this->get_post_types() as $post_type ) {
			add_action( 'rest_api_init', function() use ( $post_type ) {
				register_rest_field( $post_type, 'featured_media_src_url', array(
					    'get_callback'    => function ( $post ) {
											return $this->featured_media_src( $post );
										},
						'update_callback' => null,
						'schema'          => null,
					) // @codingStandardsIgnoreLine
				);
				}, 99
			);
		}
	}

	/**
	 * Featured media src
	 * check if there is featured_media and if not return null
	 *
	 * @param object $post the post data.
	 */
	private function featured_media_src( $post = null ) {
		$media = array_key_exists( 'featured_media', $post );
		if ( $media ) {
			$media_src = $this->get_media( $post['featured_media'] );
			return $media_src[0];
		}
		return null;
	}
}
