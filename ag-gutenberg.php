<?php
/*
* Plugin Name: AG Gutenberg
* Description: Create custom Gutenberg blocks
* Author: Andrea Grillo
* Version: 1.0.0
* Author URI: http://andreagrillo.it/
*/

! defined( 'AG_GUTENBERG_PLUGIN_URL' ) && define( 'AG_GUTENBERG_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
! defined( 'AG_GUTENBERG_PLUGIN_PATH' ) && define( 'AG_GUTENBERG_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Create Shortcode
 */
require_once( AG_GUTENBERG_PLUGIN_PATH . '/ag-shortcode.php' );

/* === Gutenberg Integration === */

add_action( 'init', 'ag_gutenberg_integration', 30 );

if( ! function_exists( 'ag_gutenberg_integration' ) ){
	function ag_gutenberg_integration(){
		/**
		 * Check if Gutenberg module is enabled
		 */
		if( class_exists( 'WP_Block_Type_Registry' ) ){
			//First Step: Register Block
			add_action( 'init', 'ag_gutenberg_register_blocks', 35 );

			// Second Step (Optional): Create a new category
			add_filter( 'block_categories', 'ag_gutenberg_block_category', 10, 2 );

			// Third Step: Enqueue Gutenberg Script
			add_action( 'enqueue_block_editor_assets','ag_gutenberg_enqueue_block_editor_assets' );
			
			//Forth Step: Make shortcode preview
			add_action( 'wp_ajax_ag_gutenberg_do_shortcode', 'ag_gutenberg_do_shortcode' );
		}
	}
}

if( ! function_exists( 'ag_gutenberg_register_blocks' ) ){
	/**
	 * Add blocks to gutenberg editor
	 *
	 * @return void
	 * @author Andrea Grillo <andrea.grillo@yithemes.com>
	 */
	function ag_gutenberg_register_blocks(){
		$block_name = 'socials-button';
		$block_style = array( 'style' => 'ag-gutenberg-style' );
		register_block_type( "ag/{$block_name}", $block_style );
	}
}

if( ! function_exists( 'ag_gutenberg_block_category' ) ){
	/**
	 * Add blocks to gutenberg editor
	 *
	 * @param $categories array Current Gutenberg Categories
	 * @param $post WP_Post the current post object
	 *
	 * @return array
	 * @author Andrea Grillo <andrea.grillo@yithemes.com>
	 */
	function ag_gutenberg_block_category( $categories, $post ){
		return array_merge(
			$categories,
			array(
				array(
					'slug' => 'ag-blocks',
					'title' => _x( 'AG Gutenberg Blocks', '[gutenberg]: Category Name', 'ag-gutenberg' ),
				),
			)
		);
	}
}

if( ! function_exists( 'ag_gutenberg_enqueue_block_editor_assets' ) ){
	/**
	 * Enqueue scripts for gutenberg
	 */
	function ag_gutenberg_enqueue_block_editor_assets() {
		$suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$deps     = apply_filters( 'ag_gutenberg_script_deps', array(
			'js-md5',
			'wp-blocks',
			'wp-element',
		) );
		wp_register_script( 'js-md5', AG_GUTENBERG_PLUGIN_URL . 'assets/js/javascript-md5/md5.min.js', array(), '2.10.0', true );
		wp_enqueue_script( 'ag-gutenberg-script', AG_GUTENBERG_PLUGIN_URL . 'assets/js/ag-gutenberg' . $suffix . '.js', $deps, '1.0.0', true );
	}
}

if( ! function_exists( 'ag_gutenberg_do_shortcode' ) ){
	/**
	 * Get a do_shortcode in ajax call to show block preview
	 */
	function ag_gutenberg_do_shortcode(){
		$current_action = current_action();
		$sc             = ! empty( $_POST['shortcode'] ) ? $_POST['shortcode'] : '';

		if( ! apply_filters( 'ag_gutenberg_skip_shortcode_sanitize', false ) ){
			$sc = sanitize_text_field( stripslashes( $sc ) );
		}

		do_action( 'ag_gutenberg_before_do_shortcode', $sc, $current_action );
		echo do_shortcode( apply_filters( 'ag_gutenberg_shortcode', $sc, $current_action ) );
		do_action( 'ag_gutenberg_after_do_shortcode', $sc, $current_action );

		if( ag_is_ajax() ){
			die();
		}
	}
}

if ( ! function_exists( 'ag_is_ajax' ) ) {

	/**
	 * Is_ajax - Returns true when the page is loaded via ajax.
	 *
	 * @return bool
	 */
	function ag_is_ajax() {
		return function_exists( 'wp_doing_ajax' ) ? wp_doing_ajax() : defined( 'DOING_AJAX' );
	}
}