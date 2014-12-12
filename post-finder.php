<?php

/**
 * Plugin Name: Post Finder
 * Author: Micah Ernst
 * Description: Adds a UI for currating and ordering posts
 * Version: 0.2
 */

if( !class_exists( 'NS_Post_Finder' ) ) :

define( 'POST_FINDER_VERSION', '0.2' );
 
/**
 * Namespacing the class with "NS" to ensure uniqueness
 */
class NS_Post_Finder {
	
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
			plugins_url( 'js/main.js', __FILE__ ),
			array(
				'jquery',
				'jquery-ui-draggable',
				'jquery-ui-sortable',
				'underscore'
			),
			POST_FINDER_VERSION,
			true
		);

		wp_localize_script(
			'post-finder',
			'POST_FINDER_CONFIG',
			array(
				'adminurl'           => admin_url(),
				'nothing_found'      => __( 'Nothing Found', 'post_finder' ),
				'max_number_allowed' => __( 'Sorry, maximum number of items added.', 'post_finder' ),
				'already_added'      => __( 'Sorry, that item has already been added.', 'post_finder' )
			)
		);

		wp_enqueue_style( 'post-finder', plugins_url( 'css/screen.css', __FILE__ ) );
	}
	
	/**
	 * Make sure our nonce and JS templates are on all admin pages
	 *
	 * @return void
	 */
	function admin_footer() {
		wp_nonce_field( 'post_finder', 'post_finder_nonce' );

		$this->render_js_templates();
	}

	/**
	 * Outputs JS templates for use.
	 */
	private function render_js_templates() {
		$main_template = 
			'<li data-id="<%= id %>">
				<input type="text" size="3" maxlength="3" max="3" value="<%= pos %>">
				<span><%= title %></span>
				<nav>
					<a href="<%= edit_url %>" class="icon-pencil" target="_blank" title="Edit"></a>
					<a href="<%= permalink %>" class="icon-eye" target="_blank" title="View"></a>
					<a href="#" class="icon-remove" title="Remove"></a>
				</nav>
			</li>';

		$item_template = 
			'<li data-id="<%= ID %>" data-permalink="<%= permalink %>">
				<a href="#" class="add">Add</a>
				<span><%= post_title %></span>
			</li>';

		// allow for filtering / overriding of templates
		$main_template = apply_filters( 'post_finder_main_template', $main_template );
		$item_template = apply_filters( 'post_finder_item_template', $item_template );
		
		?>

		<script type="text/html" id="tmpl-post-finder-main">
		<?php echo $main_template; ?>
		</script>

		<script type="text/html" id="tmpl-post-finder-item">
		<?php echo $item_template; ?>
		</script>

		<?php
	}

	/**
	 * Builds an input that lets the user find and order posts
	 *
	 * @param string $name Name of input
	 * @param string $value Expecting comma seperated post ids
	 * @param array $options Field options
	 */
	public static function render( $name, $value, $options = array() ) {

		$options = wp_parse_args( $options, array(
			'show_numbers' => true, // display # next to post
			'limit' => 10,
			'include_script' => true, // Should the <script> tags to init post finder be included or not
		));
		$options = apply_filters( 'post_finder_render_options', $options );

		// check to see if we have query args
		$args = isset( $options['args'] ) ? $options['args'] : array();

		// setup some defaults
		$args = wp_parse_args( $args, array(
			'post_type'      => 'post',
			'posts_per_page' => 10,
			'post_status'    => 'publish'
		));

		// now that we have a post type, figure out the proper label
		if( is_array( $args['post_type'] ) ) {
			$singular         = 'Item';
			$plural           = 'Items';
			$singular_article = 'an';
		} elseif( $post_type = get_post_type_object( $args['post_type'] ) ) {
			$singular         = $post_type->labels->singular_name;
			$plural           = $post_type->labels->name;
			$singular_article = 'a';
		} else {
			$singular         = 'Post';
			$plural           = 'Posts';
			$singular_article = 'a';
		}
		
		// get current selected posts if we have a value
		if( !empty( $value ) && is_string( $value ) ) {

			$post_ids = array_map( 'intval', explode( ',', $value ) );

			$posts = get_posts( array(
				'post_type'      => $args['post_type'],
				'post_status'    => $args['post_status'],
				'post__in'       => $post_ids,
				'orderby'        => 'post__in',
				'posts_per_page' => count( $post_ids )
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
					$i = 1;
					foreach( $posts as $post ) {
						printf(
							'<li data-id="%s">' .
								'<input type="text" size="3" maxlength="3" max="3" value="%s">' .
								'<span>%s</span>' .
								'<nav>' .
									'<a href="%s" class="icon-pencil" target="_blank" title="Edit"></a>' .
									'<a href="%s" class="icon-eye" target="_blank" title="View"></a>' .
									'<a href="#" class="icon-remove" title="Remove"></a>' .
								'</nav>' .
							'</li>',
							intval( $post->ID ),
							intval( $i ),
							esc_html( apply_filters( 'post_finder_item_label', $post->post_title, $post ) ),
							get_edit_post_link( $post->ID ),
							get_permalink( $post->ID )
						);
						$i++;
					}
				} else {
					echo '<p class="notice">No ' . esc_html( $plural ) . ' added.</p>';
				}

				?>
			</ul>

			<?php if( $recent_posts ) : ?>
			<h4>Select a Recent <?php echo esc_html( $singular ); ?></h4>
			<select>
				<option value="0">Choose <?php echo esc_html( $singular_article ) . ' ' . esc_html( $singular ); ?></option>
				<?php foreach( $recent_posts as $post ) : ?>
				<option value="<?php echo intval( $post->ID ); ?>" data-permalink="<?php echo esc_attr( get_permalink( $post->ID ) ); ?>"><?php echo esc_html( apply_filters( 'post_finder_item_label', $post->post_title, $post ) ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php endif; ?>
		
			<div class="search">
				<h4>Search for <?php echo esc_html( $singular_article ) . ' ' . esc_html( $singular ); ?></h4>
				<input type="text" placeholder="Enter a term or phrase">
				<button class="button">Search</button>
				<ul class="results"></ul>
			</div>
		</div>
		<?php
		if ( $options['include_script'] ) {
			?>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					$('.post-finder').postFinder();
				});
			</script>
			<?php
		}
	}

	/**
	 * Ajax callback to get posts that we may want to ad
	 *
	 * @return void
	 */
	function search_posts() {
		
		check_ajax_referer( 'post_finder' );

		if( !current_user_can( 'edit_posts' ) )
			return;

		// possible vars we'll except
		$vars = array(
			's',
			'post_parent',
			'post_status',
		);

		$args = array();

		// clean the basic vars
		foreach( $vars as $var ) {
			if( isset( $_REQUEST[$var] ) ) {
				if( is_array( $_REQUEST[$var] ) ) {
					$args[$var] = array_map( 'sanitize_text_field', $_REQUEST[$var] );
				} else {
					$args[$var] = sanitize_text_field( $_REQUEST[$var] );
				}
			}
		}

		// this needs to be within a range
		if( isset( $_REQUEST['posts_per_page'] ) ) {

			$num = intval( $_REQUEST['posts_per_page'] );

			if( $num <= 0 ) {
				$num = 10;
			} elseif( $num > 100 ) {
				$num = 100;
			}

			$args['posts_per_page'] = $num;
		}

		// handle post type validation differently
		if( isset( $_REQUEST['post_type'] ) ) {

			$post_types = get_post_types( array( 'public' => true ) );

			if( is_array( $_REQUEST['post_type'] ) ) {

				foreach( $_REQUEST['post_type'] as $type ) {

					if( in_array( $type, $post_types ) ) {
						$args['post_type'][] = $type;
					}
				}

			} else {

				if( in_array( $_REQUEST['post_type'], $post_types ) )
					$args['post_type'] = $_REQUEST['post_type'];
			
			}
		}

		if ( isset( $_REQUEST['tax_query'] ) ) {
			foreach( $_REQUEST['tax_query'] as $current_tax_query ) {
				$args['tax_query'][] = array_map( 'sanitize_text_field', $current_tax_query );
			}
		}

		// allow search args to be filtered
		$posts = get_posts( apply_filters( 'post_finder_search_args', $args ) );

		// Get the permalink so that View/Edit links work
		foreach( $posts as $key => $post )
			$posts[$key]->permalink = get_permalink( $post->ID );

		$posts = apply_filters( 'post_finder_search_results', $posts );

		header("Content-type: text/json");
		die( json_encode( array( 'posts' => $posts ) ) );
	}
}
new NS_Post_Finder();

/**
 * Help function to render a post finder input
 */
function pf_render( $name, $value, $options = array() ) {
	NS_Post_Finder::render( $name, $value, $options );
}

endif;