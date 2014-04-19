<?php

/**
 * Add post specific header images
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class UH_Post_Header_Images {

	/**
	 * Class constructor
	 * 
	 * Adds methods to appropriate hooks
	 * 
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.0
	 */
	public function __construct() {

		// Add filter for post header image (uses increased priority to ensure that single post thumbnails aren't overridden by category images)
		add_filter( 'theme_mod_header_image', array( $this, 'header_image_filter' ), 20 );

	}

	/*
	 * Filter for modifying the output of get_header()
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.0
	 * @param string $url The header image URL
	 * @global $post Used for accessing the current post/page ID
	 * @return string
	 */
	public function header_image_filter( $url ) {

		// Bail out now if not in post or page
		if ( ! is_single() && ! is_page() )
			return $url;

		// Pick post type
		if ( is_single() ) {
			$slug = 'post';
		} else {
			$slug = 'page';
		}

		// Grab the post thumbnail ID
		$attachment_id = get_post_meta( get_the_ID(), 'kd_custom-header_' . $slug . '_id', true );

		if ( '' == $attachment_id ) {

			// If not set, then hunt for a legacy value (from when we used the Multiple Post Thumbnails plugins class)
			$attachment_id = get_post_meta( get_the_ID(), $slug . '_custom-header_thumbnail_id', true );

			// If attachment ID set here, then update the new ID
			if ( '' != $attachment_id ) {
				update_post_meta( get_the_ID(), 'kd_custom-header_post_id', $attachment_id );
			}
		}

		// If no post thumbnail ID set, then use default
		if ( '' == $attachment_id ) {
			return $url;
		}

		// Grab URL from WordPress
		$url = wp_get_attachment_image_src( $attachment_id, 'full' );
		$url = $url[0];

		return $url;
	}

}
