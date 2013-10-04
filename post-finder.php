<?php

/**
 * Plugin Name: Post Finder
 * Author: Micah Ernst
 * Description: Adds a UI for currating and ordering posts
 * Version: 0.1
 */

if( !class_exists( 'Post_Finder' ) ) :
 
class Post_Finder {
	
	/**
	 *
	 */
	function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_js' ) );
		add_action( 'admin_footer', array( $this, 'admin_footer' ) );
		
		add_action( 'wp_ajax_pf_get_posts', 'ajax_get_posts' );
	}
	
	/**
	 *
	 */
	function admin_js() {
		wp_enqueue_script(
			'post-finder',
			plugins_url( '/js/post-finder.js', __FILE__ ),
			array(
				'jquery',
				'jquery-ui-draggable',
				'jquery-ui-sortable'
			),
			time(),
			true
		);
		wp_enqueue_style( 'post-finder', plugins_url( '/css/screen.css', __FILE__ ) );
	}
	
	/**
	 *
	 */
	function admin_footer() {
		wp_nonce_field( 'post-finder', 'pf-nonce' );
		?>
		<div style="background:red;padding:4em">
			<?php Post_Finder::render( 'test_finder', null, array(
				'limit' => 5,
				'post_type' => 'post'
			));
			?>
		</div>
		<?php
	}

	/**
	 *
	 *
	 * @param string Input name
	 * @param string Comma seperated post ids
	 */
	public static function render( $name, $value, $args ) {

		if( empty( $value ) )
			//return;

		// sanitize value
		$post_ids = array_map( 'intval', explode( ',', $value ) );

		if( !count( $post_ids ) )
			//return;

		$posts = get_posts( array(
			'posts_per_page' => 10,
			'post__in' => $post_ids,
			'orderby' => 'post__in'
		));

		$html = '<input type="text" name="' . esc_attr( $input ) . '" value="'. $value . '" class="pf-input"/>';

		$html .= $posts ? self::build_list( $posts ) : '<p>Sorry, no posts were found.</p>';

		$html .= '<input type="button" name="" value="Add Posts" class="pf-add button">';

		echo '<div class="pf-list">' . $html . '</div>';
	}

	/**
	 *
	 */
	public static function build_list( $posts = array() ) {

		$html = '';

		// must have posts to build something
		if( !count( $posts ) )
			return $html;

		$html .= '<ul>';

		foreach( $posts as $post ) {
			$html .= self::get_input_li( $post );
		}

		$html .= '</ul>';

		return $html;
	}

	/**
	 *
	 */
	public static function get_input_li( $post ) {
		
		return sprintf(
			'<li class="pf-item" data-id="%s">%s<nav><a href="%s" target="_blank" class="edit">Edit</a> | <a href="#" class="remove">Remove</a></nav></li>',
			intval( $post->ID ),
			get_the_title( $post->ID ),
			get_edit_post_link( $post->ID )
		);
	}

	/**
	 *
	 */
	function get_search_li( $post ) {
		return sprintf(
			'<li data-id="%d">%s</li>',
			intval( $post->ID ),
			get_the_title( $post->ID )
		);
	}
	
	/**
	 *
	 */
	function ajax_get_posts() {
		
		check_ajax_referer( 'pf-nonce' );
		
		if( !isset( $_REQUEST['post_ids'] ) ) {
		
		}
		
	}
}
new Post_Finder();

endif;