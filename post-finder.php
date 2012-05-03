<?php

/**
 * Plugin Name: Post Finder
 * Author: Micah Ernst
 * Description: Adds a UI for currating and ordering posts
 * Version: 1.0
 */
 
class Post_Finder {
	
	function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_js' ) );
		add_action( 'admin_footer', array( $this, 'admin_footer' ) );
		
		add_action( 'wp_ajax_pf_get_posts', 'ajax_get_posts' );
	}
	
	function admin_js() {
		wp_enqueue_script( 'post-finder', plugins_url( '/js/post-finder.js', __FILE__ ), array( 'jquery', 'jquery-ui-draggable', 'jquery-ui-sortable' ) );
		wp_enqueue_style( 'post-finder', plugins_url( '/css/post-finder.css', __FILE__ ) );
	}
	
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
	
	function get_post_li( $post ) {
		
		return sprintf(
			'<li class="pf-item" data-id="%s"><a href="#" class="remove">-</a><a href="#" class="add">+</a>%s | <a href="%s" class="edit">edit</a></li>',
			intval( $post->ID ),
			get_the_title( $post->ID ),
			get_edit_post_link( $post->ID )
		);
	}
	
	function ajax_get_posts() {
		
		check_ajax_referer( 'pf-nonce' );
		
		if( !isset( $_REQUEST['post_ids'] ) ) {
		
		}
		
	}
}
global $post_finder;
$post_finder = new Post_Finder();