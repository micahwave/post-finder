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
	 * Setup hooks
	 *
	 * @return void
	 */
	function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'admin_footer', array( $this, 'admin_footer' ) );
		add_action( 'wp_ajax_pf_search_posts', array( $this, 'search_posts' ) );
	}

	/**
	 * Enable our scripts and stylesheets
	 *
	 * @return void
	 */
	function scripts() {

		wp_enqueue_script(
			'post-finder',
			plugins_url( '/js/main.js', __FILE__ ),
			array(
				'jquery',
				'jquery-ui-draggable',
				'jquery-ui-sortable',
				'underscore'
			),
			null,
			true
		);

		wp_enqueue_style( 'post-finder', plugins_url( '/css/screen.css', __FILE__ ) );
	}
	
	/**
	 * Make sure our nonce is on all admin pages
	 *
	 * @return void
	 */
	function admin_footer() {
		wp_nonce_field( 'post_finder', 'post_finder_nonce' );
	}

	/**
	 * Builds an input that lets the user find and order posts
	 *
	 * @param string Name of input
	 * @param string Expecting comma seperated post ids
	 * @param array Field options
	 */
	public static function render( $name, $value, $options = array() ) {

		global $wp_post_types;

		$options = wp_parse_args( $options, array(
			'show_numbers' => true, // display # next to post
			'limit' => 10
		));

		// check to see if we have query args
		$args = isset( $options['args'] ) ? $options['args'] : array();

		// setup some defaults
		$args = wp_parse_args( $args, array(
			'post_type' => 'post',
			'posts_per_page' => 10
		));

		// now that we have a post type, figure out the proper label
		if( isset( $wp_post_types[$args['post_type']] ) ) {
			$singular = $wp_post_types[$args['post_type']]->labels->singular_name;
			$plural = $wp_post_types[$args['post_type']]->labels->name;
		} else {
			$singular = 'Post';
			$plural = 'Posts';
		}
		
		// get current selected posts if we have a value
		if( !empty( $value ) && is_string( $value ) ) {

			$post_ids = array_map( 'intval', explode( ',', $value ) );

			$posts = get_posts( array(
				'post__in' => $post_ids,
				'orderby' => 'post__in',
			));
		}

		// if we have some ids already, make sure they arent included in the recent posts
		if( !empty( $post_ids ) ) {
			$args['post__not_in'] = $post_ids;
		} 

		// get recent posts
		$recent_posts = get_posts( apply_filters( 'post_finder_' . $name . '_recent_post_args', $args ) );

		$class = 'post-finder';

		if( !$options['show_numbers'] )
			$class .= ' no-numbers';
		
		?>
		<div class="<?php echo esc_attr( $class ); ?>" data-limit="<?php echo intval( $options['limit'] ); ?>" data-args='<?php echo json_encode( $args ); ?>'>
			<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>">
			<ul class="list">
				<?php

				if( !empty( $posts ) ) {
					foreach( $posts as $post ) {
						printf(
							'<li data-id="%s">' .
								'<span>%s</span>' .
								'<nav>' .
									'<a href="%s" target="_blank">Edit</a>' .
									'<a href="%s" target="_blank">View</a>' .
									'<a href="#" class="remove">Remove</a>' .
								'</nav>' .
							'</li>',
							intval( $post->ID ),
							esc_html( $post->post_title ),
							get_edit_post_link( $post->ID ),
							get_the_guid( $post->ID )
						);
					}
				} else {
					echo '<p class="notice">No ' . esc_html( $plural ) . ' added.</p>';
				}

				?>
			</ul>

			<?php if( $recent_posts ) : ?>
			<h4>Select a Recent <?php echo esc_html( $singular ); ?></h4>
			<select>
				<option value="0">Choose a <?php echo esc_html( $singular ); ?></option>
				<?php foreach( $recent_posts as $post ) : ?>
				<option value="<?php echo intval( $post->ID ); ?>"><?php echo esc_html( $post->post_title ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php endif; ?>
		
			<div class="search">
				<h4>Search for a <?php echo esc_html( $singular ); ?></h4>
				<input type="text" placeholder="Enter a term or phrase">
				<buttton class="button">Search</buttton>
				<ul class="results"></ul>
			</div>
		</div>
		<script>jQuery(document).ready(function($){$('.post-finder').postFinder()});</script>
		<?php
	}

	/**
	 * Ajax callback to get posts that we may want to ad
	 *
	 * @return void
	 */
	function search_posts() {
		
		check_ajax_referer( 'post_finder' );

		// possible vars we'll except
		$vars = array(
			'posts_per_page',
			'post_status',
			's',
			'post_type',
			'post_parent'
		);

		$args = array();

		foreach( $vars as $var ) {
			if( isset( $_REQUEST[$var] ) ) {
				$args[$var] = sanitize_text_field( $_REQUEST[$var] );
			}
		}

		$posts = get_posts( $args );

		if( $posts )
			header("Content-type: text/json");
			die( json_encode( array( 'posts' => $posts ) ) );
		
	}
}
new Post_Finder();

endif;