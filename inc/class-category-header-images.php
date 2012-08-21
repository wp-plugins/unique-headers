<?php

/**
 * Add category specific header images
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryan@pixopoint.com>
 * @since 1.0
 */
class Category_Header_Images {

	/**
	 * Class constructor
	 * 
	 * Adds methods to appropriate hooks
	 * 
	 * @author Ryan Hellyer <ryan@pixopoint.com>
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init'     ) );
	}

	/**
	 * Print styles to admin page
	 *
	 * @author Ryan Hellyer <ryan@pixopoint.com>
	 * @since 1.0
	 */
	public function init() {

		// Bail out now if taxonomy meta data plugin not installed
		if ( !class_exists( 'Taxonomy_Metadata' ) )
			return;

		// Add actions
		add_action( 'admin_print_scripts',            array( $this, 'print_styles'     ) );
		add_action( 'admin_print_scripts',            array( $this, 'external_scripts' ) );
		add_action( 'admin_head',                     array( $this, 'inline_scripts'   ) );
		$taxonomy = 'category';
		add_action( $taxonomy . '_edit_form_fields',  array( $this, 'extra_fields'), 1 );
		add_action( 'edit_category',                  array( $this, 'storing_taxonomy_data' ) );

		// Add filters
		add_filter( 'theme_mod_header_image',         array( $this, 'header_image_filter' ) );

	}

	/**
	 * Print styles to admin page
	 *
	 * @author Ryan Hellyer <ryan@pixopoint.com>
	 * @since 1.0
	 */
	public function print_styles() {

		// If on category page, then bail out
		if ( !isset( $_GET['taxonomy'] ) )
			return;

		wp_enqueue_style( 'thickbox' );
	}

	/**
	 * Print external scripts to admin page
	 *
	 * @author Ryan Hellyer <ryan@pixopoint.com>
	 * @since 1.0
	 */
	public function external_scripts() {

		// If on category page, then bail out
		if ( !isset( $_GET['taxonomy'] ) )
			return;

		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );
	}

	/**
	 * Print inline scripts to admin page
	 *
	 * @author Ryan Hellyer <ryan@pixopoint.com>
	 * @since 1.0
	 */
	public function inline_scripts() {

		// If on category page, then bail out
		if ( !isset( $_GET['taxonomy'] ) )
			return;

		echo "
		<script>
		jQuery(document).ready(function() {
			jQuery('#upload_image_button').click(function() {
				formfield = jQuery('#upload_image').attr('name');
				tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
				return false;
			});
			window.send_to_editor = function(html) {
				imgurl = jQuery('img',html).attr('src');
				jQuery('#upload_image').val(imgurl);
				tb_remove();
			}
		});
		</script>";
	}

	/*
	 * Filter for modifying the output of get_header()
	 *
	 * @author Ryan Hellyer <ryan@pixopoint.com>
	 * @since 1.0
	 */
	public function header_image_filter( $url ) {

		// Bail out now if not in category
		if ( ! is_category() )
			return $url;

		// Grab current category ID
		$tag_ID = get_query_var( 'cat' );

		// Grab stored taxonomy header
		$new_url = get_term_meta( $tag_ID, 'taxonomy-header-image', true );

		// If no URL set, then bail out now
		if ( '' == $new_url )
			return $url;

		return $url;
	}

	/**
	 * Storing the taxonomy header image selection
	 * 
	 * @author Ryan Hellyer <ryan@pixopoint.com>
	 * @since 1.0
	 */
	public function storing_taxonomy_data() {

		// Sanitize inputs
		$tag_ID = (int) $_POST['tag_ID'];
		$url = $_POST['taxonomy-header-image'];
		$url = esc_url( $url );

		update_term_meta( $tag_ID, 'taxonomy-header-image', $url );
	}

	/**
	 * Extra fields
	 * 
	 * @author Ryan Hellyer <ryan@pixopoint.com>
	 * @since 1.0
	 */
	public function extra_fields() {
		$tag_ID = $_GET['tag_ID'];
		$url = get_term_meta( $tag_ID, 'taxonomy-header-image', true );

		echo '
		<tr valign="top">
			<th scope="row">' . __( 'Upload header image', 'hyper_headers' ) . '</th>
			<td>
				<label for="upload_image">
					<input id="upload_image" type="text" size="36" name="taxonomy-header-image" value="' . $url . '" />
					<input id="upload_image_button" type="button" value="Upload Image" />
					<br />' . __( 'Enter an URL or upload an image for the banner.', 'hyper_headers' ) . '
					<br />
					<img style="width:500px;height:auto;" src="' . $url . '" alt="" />
				</label>
			</td>
		</tr>';

	}

}
