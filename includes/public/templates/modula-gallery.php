<?php
/** -------------------------------------------------------------------- *\
 * 	edobees modification to address <style> inside <div>.
 *	Feb. 27, 2019 - <edo_mod> is used to mark changes.
 * 	see 'class-modula-shortcode.php' for more information.
\** -------------------------------------------------------------------- */
?>
<div id="<?php echo esc_attr($data->gallery_id) ?>" class="modula modula-gallery" data-config="<?php echo esc_attr( json_encode( $data->js_config ) ) ?>">

	<?php do_action( 'modula_shortcode_before_items', $data->settings ); 
	
	// <edo_mod> begin: make inline-style attributes from settings
	$style = "";
	$g_settings = $data->settings;
	if ( 'custom-grid' != $g_settings['type'] ) {
		$style .= ' style="width:' . esc_attr($g_settings['width']) .
		'; height:' . absint( $g_settings['height'] ) . 'px;"';
	}
	// <edo_mod> end: but don't forget to echo $style after class...
	?>
	<div class='items' <?php echo $style;?>>
		<?php

		foreach ( $data->images as $image ) {

			$image_object = get_post( $image['id'] );
			if ( is_wp_error( $image_object ) || get_post_type( $image_object ) != 'attachment' ) {
				continue;
			}

			// Create array with data in order to send it to image template
			$item_data = array(
				/* Item Elements */
				'title'            => Modula_Helper::get_title( $image, $data->settings['wp_field_title'] ),
				'description'      => Modula_Helper::get_description( $image, $data->settings['wp_field_caption'] ),
				'lightbox'         => $data->settings['lightbox'],

				/* What to show from elements */
				'hide_title'       => boolval( $data->settings['hide_title'] ) ? true : false,
				'hide_description' => boolval( $data->settings['hide_description'] ) ? true : false,
				'hide_socials'     => false,
				"enableTwitter"    => boolval( $data->settings['enableTwitter'] ),
				"enableFacebook"   => boolval( $data->settings['enableFacebook'] ),
				"enablePinterest"  => boolval( $data->settings['enablePinterest'] ),
				"enableGplus"      => boolval( $data->settings['enableGplus'] ),

				/* Item container attributes & classes */
				'item_classes'     => array( 'item' ),
				'item_attributes'  => array(),

				/* Item link attributes & classes */
				'link_classes'     => array( 'tile-inner' ),
				'link_attributes'  => array(),

				/* Item img attributes & classes */
				'img_classes'      => array( 'pic' ),
				'img_attributes'   => array(
					'data-valign' => esc_attr( $image['valign'] ),
					'data-halign' => esc_attr( $image['halign'] ),
					'alt'         => esc_attr( $image['alt'] ),
				),
			);
			
			// <edo_mod>: add gallery 'settings' to $item_data
			$item_data['g_settings'] = $g_settings;

			/**
			 * Hook: modula_shortcode_item_data.
			 *
			 * @hooked modula_generate_image_links - 10
			 * @hooked modula_check_lightboxes_and_links - 15
			 * @hooked modula_check_hover_effect - 20
			 * @hooked modula_check_custom_grid - 25
			 */
			$item_data = apply_filters( 'modula_shortcode_item_data', $item_data, $image, $data->settings );

			do_action( 'modula_shortcode_before_item', $data->settings, $item_data );
			$data->loader->set_template_data( $item_data );
			$data->loader->get_template_part( 'items/item', $data->settings['effect'] );
			do_action( 'modula_shortcode_after_item', $data->settings, $item_data );
		}

		?>
	</div>

	<?php do_action( 'modula_shortcode_after_items', $data->settings ) ?>

</div>