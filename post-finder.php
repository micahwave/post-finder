<?php

/**
 * Plugin Name: Post Finder
 * Author: Micah Ernst
 * Description: Adds a UI for currating and ordering posts
 * Version: 0.1
 */
 
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
		wp_enqueue_script( 'post-finder', plugins_url( '/js/post-finder.js', __FILE__ ), array( 'jquery', 'jquery-ui-draggable', 'jquery-ui-sortable' ), time(), true );
		wp_enqueue_style( 'post-finder', plugins_url( '/css/screen.css', __FILE__ ) );
	}
	
	/**
	 *
	 */
	function admin_footer() {
		wp_nonce_field( 'post-finder', 'pf-nonce' );
		?>
		<div id="pf-overlay"></div>
		<div id="pf-modal">
			<span class="close">x</span>
			<ul class="tabs">
				<li><a href="#" class="selected" data-tab="recent">Recent Posts</a></li>
				<li><a href="#" data-tab="search">Search</a></li>
			</ul>
			<div class="tab-content">
				<div class="panel recent-panel">
					<ul class="draggable">
						<?php
						
						$posts = get_posts( array(
							'posts_per_page' => 20
						));
						
						if( $posts ) {
							foreach( $posts as $post ) {
								echo $this->get_post_li( $post );
							}
						}
						
						?>
					</ul>
				</div>
				<div class="panel search-panel">
					<input type="text" class="query" placeholder="Enter Post Title"/>
					<input type="button" class="button-primary search-posts" value="Search"/>
					<img src="<?php echo admin_url(); ?>/images/wpspin_light.gif" class="spinner"/>
					<ul class="draggable search-results"></ul>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 *
	 *
	 * @param string Input name
	 * @param string Comma seperated post ids
	 */
	public static function create_finder( $input, $value ) {

		if( empty( $value ) )
			return;

		// sanitize value
		$post_ids = array_map( 'intval', explode( ',', $value ) );

		if( !count( $post_ids ) )
			return;

		$posts = get_posts( array(
			'posts_per_page' => 200,
			'post__in' => $post_ids,
			'orderby' => 'post__in'
		));

		$html = '<input type="text" name="' . esc_attr( $input ) . '" value="'. $value . '" class="pf-input"/>';

		$html .= $posts ? self::build_list( $posts ) : '<p>Sorry, no posts were found.</p>';

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
			$html .= self::get_post_li( $post );
		}

		$html .= '</ul>';

		return $html;
	}

	/**
	 *
	 */
	public static function get_post_li( $post ) {
		
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
	function ajax_get_posts() {
		
		check_ajax_referer( 'pf-nonce' );
		
		if( !isset( $_REQUEST['post_ids'] ) ) {
		
		}
		
	}
}
new Post_Finder();