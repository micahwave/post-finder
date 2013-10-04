PostFinder = window.PostFinder || {};

(function($) {
	
	PostFinder = {
	
		$overlay: $('#pf-overlay'),
		$find: $('.find-posts'),
		$modal: $('#pf-modal'),
		$tabs: $('#pf-modal .tabs a'),
		$close: $('#pf-modal .close'),
		$add: $('.pf-item .add'),
		$remove: $('.pf-item .remove'),
		$nonce: $('#pf-nonce'),
		$target: null,
		template: [
			'<li class="pf-item" data-id="<%= ID %>">',
				'<%= post_title %>',
				'<nav>',
					'<a href="#" target="_blank" class="edit">Edit</a>',
					'<a href="#" class="remove">Remove</a>',
					'<a href="#" class="add">Add</a>',
				'</nav>',
			'</li>'
		].join(''),
			
		init : function() {
			
			var t = this;

			// make lists sortable
			$('.pf-list ul').sortable({
				placeholder: 'placeholder',
				update: function(e, ui) {
					var $self = $(this), $input = $self.parent().find('.pf-input');
					t.serialize( $self, $input );
				}
			});

			// open modal window
			$('.pf-add').click(function(){

				t.$target = $(this).parents('.pf-list');

				console.log( t.$target );

				t.open();

			});

			// append to body to get positioning right
			t.$modal.appendTo('body');

			// add item to list
			$('#pf-modal').on('click', '.add', function(){
				
				var $self = $(this), 
					$li = $self.parents('li');

				t.$target.find('ul').append( $li.clone() );

				$li.remove();

				t.serialize( t.$target );

			});
			
			// close overlay
			t.$overlay.click(function() {
				t.close();
			});
			
			// close the overlay
			t.$close.click(function() {
				t.close();
			});
			
			// remove an li
			$('.pf-list').on('click', '.pf-item .remove', function(e) {

				e.preventDefault();

				console.log('remove me');

				var $self = $(this),
					$el = $self.parents('.pf-list');

				$self.parents('li').remove();

				t.serialize( $el );
			});

			t.$modal.find('.search-posts').click(function() {
				t.search_posts();
			});
		},
		
		open : function() {

			var t = this;

			$('body').css('overflow', 'hidden');

			t.$overlay.show();
			t.$modal.show();

			t.get_posts();
			

		},
		
		close : function() {

			var t = this;

			t.$overlay.hide();
			t.$modal.hide();

			$('body').css('overflow', 'visible');
		},

		get_posts: function() {

			var t = this,
				ids = t.$target.find('.pf-input').val(),
				$ul = t.$modal.find('ul');

			t.$modal.addClass('loading');

			$ul.html('');

			$.post(
				ajaxurl,
				{
					action: 'pf_get_posts',
					ids: ids,
					_ajax_nonce: t.$nonce.val()
				},
				function( response ) {
					if( typeof response.posts != "undefined" ) {
						t.render_posts(response.posts);
					}
					t.$modal.removeClass('loading');
				}
			);
		},

		search_posts: function() {

			var t = this,
				ids = t.$target.find('.pf-input').val(),
				query = t.$modal.find('.query').val();

			t.$modal.addClass('loading');

			t.$modal.find('ul').html('');

			$.post(
				ajaxurl,
				{
					action: 'pf_search_posts',
					ids: ids,
					query: query,
					_ajax_nonce: t.$nonce.val()
				},
				function( response ) {
					if( typeof response.posts != "undefined" ) {
						t.render_posts(response.posts);
						t.$modal.removeClass('loading');
					}
				}
			);
		},

		render_posts: function( posts ) {

			var t = this,
				$ul = t.$modal.find('ul');

			for( var i in posts ) {
				$ul.append( _.template( t.template, posts[i] ) );
			}
		},
		
		serialize : function( $el ) {

			var ids = [],
				$input = $el.find('.pf-input');

			$('li', $el).each(function() {
				ids.push( $(this).attr('data-id') );
			});

			$input.val( ids.join(',') );
		}
		
	}

	PostFinder.init();

})(jQuery);