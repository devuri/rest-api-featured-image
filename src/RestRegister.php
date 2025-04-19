<?php

namespace APIFeaturedImage;

class RestRegister
{
    private ?array $postTypes;
    private ?string $imageSize;
    private ?string $fieldID = 'featured_media_src_url';

    public function __construct(array $postTypes = [], $size = 'large' )
    {
        $this->postTypes = $postTypes;

        // TODO add option to change the image size for output ('thumbnail', 'medium', 'large' , 'full')
        $this->imageSize = $size;
    }

    public function addEndpoint(string $postType): void
    {
        register_rest_field(
            $postType,
            $this->fieldID,
            [
                'get_callback'    => function ( $post_object ) use ($postType) {
                    return $this->getFeaturedMediaSrc( $post_object, $postType );
                },
                'update_callback' => null,
                'schema'          => null,
            ]
        );
    }

    /**
     * If the post type is not set (empty array()) just use post.
     */
    public function getPostTypes(): ?array
    {
        if ( empty( $this->postTypes ) ) {
            $this->postTypes = [ 'post' ];
        }

        $postTypes = apply_filters( APIFI_PT_OPTION, $this->postTypes );

        if ( ! \is_array( $postTypes ) ) {
            error_log( 'return value of postTypes must be array filter:' . APIFI_PT_OPTION );

            return null;
        }

        return $postTypes;
    }

    /**
     * Featured media src
     * check if there is featured_media and if not return null.
     *
     * @param object $post     the post data.
     * @param string $postType the post data.
     */
    protected function getFeaturedMediaSrc( $post = null, ?string $postType = null )
    {
        $media = \array_key_exists( 'featured_media', $post );

        if ( $media ) {
            $media_src = $this->getMedia( $post['featured_media'] );

            if ( isset($media_src[0]) ) {
                return $media_src[0];
            }

            return null;
        }

        return null;
    }

    /**
     * Get the featured image.
     *
     * @param int $id [description].
     *
     * @return (bool|int|string)[]|false [description]
     *
     * @see https://developer.wordpress.org/reference/functions/wp_get_attachment_image_src/
     *
     * @psalm-return array{0: string, 1: int, 2: int, 3: bool}|false
     */
    private function getMedia( $id = null )
    {
        return wp_get_attachment_image_src( $id, $this->imageSize );
    }
}
