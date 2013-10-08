(function($){
	
	$.fn.postFinder = function(options) {

		return this.each(function(){

			var self = this, 
				$self = $(this),
				$field = $self.find('input[type=hidden]'),
				$select = $self.find('select'),
				$list = $self.find('.list'),
				$search = $self.find('.search'),
				$results = $search.find('.results'),
				$query = $search.find('input[type=text]'),
				nonce = $('#post_finder_nonce').val(),
				template = [
					'<li data-id="<%= id %>">',
						'<span><%= title %></span>',
						'<nav>',
							'<a href="#" target="_blank">Edit</a>',
							'<a href="#" target="_blank">View</a>',
							'<a href="#" class="remove">Remove</a>',
						'</nav>',
					'</li>'
				].join('');

			/**
			 *
			 */
			function init() {

				// bind select
				$select.change(function(){
					add_item( $(this).val(), $('option:selected', this).text() );
				});

				// bind search button
				$search.find('.button').click(function(){
					search();
				});

				// bind list
				$list.sortable({
					placeholder: 'placeholder',
					update: function(ui, e) {
						serialize();
					}
				});

				// remove button
				$list.on('click', '.remove', function(e){
					e.preventDefault();
					remove_item( $(this).closest('li').data('id') );
				});

				// add button
				$results.on('click', '.add', function(e){
					e.preventDefault();
					$li = $(this).closest('li');
					add_item( $li.data('id'), $li.find('span').text() );
				});
			}

			/**
			 *
			 */
			function add_item( id, title ) {

				// make sure we have an id
				if( id == 0 )
					return;

				// see if item already exists
				if( $list.find('li[data-id="' + id + '"]').length ) {
					alert('Sorry, that item has already been added.');
					return;
				}

				// add item
				$list.append(_.template(template, { id: id, title: title }));

				// hide notice
				$list.find('.notice').hide();

				// remove from select if there
				$select.find('option[value="' + id + '"]').remove();

				// update the input
				serialize();
			}

			/**
			 *
			 */
			function remove_item( id ) {
				
				$list.find('li[data-id="' + id + '"]').remove();

				serialize();

				// show notice if no posts
				if( $list.find('li').length == 0 ) {
					$list.find('.notice').show();
				}
			}

			/**
			 *
			 */
			function search() {

				var html = '',
					data = {
					action: 'pf_search_posts',
					s: $query.val(),
					_ajax_nonce: nonce
				}

				// display loading
				$search.addClass('loading');

				$.getJSON(
					ajaxurl,
					data,
					function(response) {
						if( typeof response.posts != "undefined" ) {
							for( var i in response.posts ) {
								html += _.template([
									'<li data-id="<%= ID %>">',
										'<a href="#" class="add">Add</a>',
										'<span><%= post_title %></span>',
									'</li>'
								].join(''), response.posts[i]);
							}
							$results.html(html);
						}
					}
				);
			}

			/**
			 *
			 */
			function serialize() {

				console.log('serialize for ' + $field.attr('name'));

				var ids = [];

				$list.find('li').each(function(){
					ids.push( $(this).data('id') );
				});

				$field.val( ids.join(',') );
			}

			init();

		});
	}

})(jQuery);