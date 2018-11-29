<?php

/* === Create Socials Shortcode === */

add_action( 'init', 'ag_register_scripts' );
add_action( 'init', 'ag_add_custom_shortcodes', 15 );
add_action( 'wp_enqueue_scripts', 'ag_enqueue_scripts' );

if( ! function_exists( 'ag_add_custom_shortcodes' ) ){
	function ag_add_custom_shortcodes(){
		add_shortcode( 'ag_social_buttons', 'ag_social_buttons' );
	}
}

if( ! function_exists( 'ag_social_buttons' ) ){
	function ag_social_buttons( $atts ){
		$sc_content = $sc_description = $sc_styles = '';
		$wrapper_id = uniqid ( 'ag-socials-wrapper-' );
		$sc_wrapper = '<div id="%s" class="ag-social-buttons-wrappers">%s<ul class="ag-social-buttons">%s</ul><style>%s</style></div>';

		if( ! empty( $atts ) && ! empty( $atts['buttons'] ) ){
			$sc_description = ! empty( $atts['description'] ) ? sprintf( '<span class="ag-social-buttons-description">%s</span>' , $atts['description'] ) : '';
			$buttons = explode( ',', $atts['buttons'] );
			$size = ! empty( $atts['size'] ) ? $atts['size'] : '2';
			foreach( $buttons as $button ){
				$button = trim( $button );
				$key = str_replace( '-', '_', $button );
				$url_key = "{$key}_url";
				$icon = sprintf( '<i class="fab fa-%1$s fa-%2$sx"></i>', $button, $size );
				$url = ! empty( $atts[ $url_key ] ) ? sprintf( '<a class="ag-social-buttons-url" href="%s", target="_blank">%s</a>', $atts[ $url_key ], $icon ) : $icon;
				$sc_content .= sprintf( '<li class="ag-shortcode-button %1$s">%2$s</li>', $button, $url );
			}

			if( ! empty( $atts['color'] ) ){
				$sc_styles .= "#{$wrapper_id} a.ag-social-buttons-url{color: {$atts['color']};}";
			}

			if( ! empty( $atts['color_hover'] ) ){
				$sc_styles .= "#{$wrapper_id} a.ag-social-buttons-url:hover{color: {$atts['color_hover']};}";
			}
		}

		return sprintf( $sc_wrapper, $wrapper_id, $sc_description, $sc_content, $sc_styles );
	}
}

if( ! function_exists( 'ag_enqueue_scripts' ) ){
	function ag_enqueue_scripts(){
		if( ag_post_content_has_shortcode( 'ag_social_buttons' ) ){
			wp_enqueue_style( 'ag-gutenberg-style' );
		}
	}
}

if( ! function_exists( 'ag_register_scripts' ) ){
	function ag_register_scripts(){
		wp_register_style( 'font-awesome', AG_GUTENBERG_PLUGIN_URL . 'assets/css/font-awesome/css/fontawesome-all.min.css', array(), '5.0.9' );
		wp_register_style( 'ag-gutenberg-style', AG_GUTENBERG_PLUGIN_URL . 'assets/css/ag-gutenberg.css', array( 'font-awesome' ), '1.0.0' );
	}
}

if( ! function_exists( 'ag_post_content_has_shortcode' ) ){
	function ag_post_content_has_shortcode( $tag = '' ) {
		global $post;
		return is_singular() && is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, $tag );
	}
}