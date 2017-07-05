<?php
/**
 * Useful helper functions
 *
 * @since 0.3.0
 */

if ( ! function_exists( 'pf_render' ) ) :
	/**
	 * Render a post finder input.
	 *
	 * @since 0.1.0
	 *
	 * @param string $name Name to use for the input.
	 * @param string $value Currently selected value(s).
	 * @param array $options Optional options for the field.
	 * @return void
	 */
	function pf_render( $name, $value, $options = array() ) {
		if ( class_exists( 'NS_Post_Finder' ) ) {
			NS_Post_Finder::render( $name, $value, $options );
		}
	}
endif;
