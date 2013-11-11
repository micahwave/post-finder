var POST_FINDER_TEMPLATE = [
	'<li data-id="<%= id %>">',
		'<input type="text" size="3" maxlength="3" max="3" value="<%= pos %>">',
		'<span><%= title %></span>',
		'<nav>',
			'<a href="<%= edit_url %>" target="_blank">Edit</a>',
			'<a href="<%= permalink %>" target="_blank">View</a>',
			'<a href="#" class="remove">Remove</a>',
		'</nav>',
	'</li>'
].join('');

(function($) {

	$.postFinder = function(element, options) {

		var defaults = {
			template : POST_FINDER_TEMPLATE,
			fieldSelector : 'input[type=hidden]',
			selectSelector : 'select',
			listSelector : '.list',
			searchSelector : '.search',
			resultsSelector : '.results',
			querySelector : 'input[type=text]',
			nonceSelector : '#post_finder_nonce'
		}

		var plugin = this;

		plugin.settings = {}//empty object to store extended settings

		var $element = $(element),//store jquery object of el
			element = element;//store html el		

		plugin.init = function() {
		
			plugin.settings = $.extend({}, defaults, options);//over write defaults with passed options
			//all jquery objects are fetched once and stored in the plugin object
			plugin.$field = $element.find(plugin.settings.fieldSelector),
			plugin.$select = $element.find(plugin.settings.selectSelector),
			plugin.$list = $element.find(plugin.settings.listSelector),
			plugin.$search = $element.find(plugin.settings.searchSelector),
			plugin.$results = plugin.$search.find(plugin.settings.resultsSelector),
			plugin.$query = plugin.$search.find(plugin.settings.querySelector),
			plugin.nonce = $(plugin.settings.nonceSelector).val();

			// bind select
	
			plugin.$select.on('change', function(e){
				plugin.add_item( $(this).val(), $('option:selected', this).text(), $('option:selected', this).data('permalink') );
			});

			// bind search button
			plugin.$search.find('.button').click(function(){
				plugin.search();
			});

			// bind list
			plugin.$list.sortable({
				placeholder: 'placeholder',
				update: function(ui, e) {
					plugin.serialize();
				}
			});

			// remove button
			plugin.$list.on('click', '.remove', function(e){
				e.preventDefault();
				plugin.remove_item( $(this).closest('li').data('id') );
			});

			// add button
			plugin.$results.on('click', '.add', function(e){
				e.preventDefault();
				$li = $(this).closest('li');
				plugin.add_item( $li.data('id'), $li.find('span').text(), $li.data('permalink') );
			});
		}

		plugin.add_item = function( id, title, permalink ) {//private method
	
			// make sure we have an id
			if( id == 0 )
				return;

			if( plugin.$list.find('li').length >= $element.data('limit') ) {
				alert('Sorry, maximum number of items added.');
				return;
			}

			// see if item already exists
			if( plugin.$list.find('li[data-id="' + id + '"]').length ) {
				alert('Sorry, that item has already been added.');
				return;
			}

			// add item
			plugin.$list.append(_.template(plugin.settings.template, { 
				id: id, 
				title: title,
				edit_url: POST_FINDER_CONFIG.adminurl + 'post.php?post=' + id + '&action=edit',
				permalink: permalink,
				pos: plugin.$list.length + 1
			}));

			// hide notice
			plugin.$list.find('.notice').hide();

			// remove from select if there
			plugin.$select.find('option[value="' + id + '"]').remove();

			// update the input
			plugin.serialize();
		}

		//Prv method to remove an item
		plugin.remove_item = function( id ) {
			
			plugin.$list.find('li[data-id="' + id + '"]').remove();

			plugin.serialize();

			// show notice if no posts
			if( plugin.$list.find('li').length == 0 ) {
				plugin.$list.find('.notice').show();
			}
		}

		plugin.search = function() {
		
			var html = '',
				args = $element.data('args'),
				data = {
					action: 'pf_search_posts',
					s: plugin.$query.val(),
					_ajax_nonce: plugin.nonce
				};

			// merge the default args in
			data = $.extend(data, $element.data('args'));
			
			// display loading
			plugin.$search.addClass('loading');

			$.getJSON(
				ajaxurl,
				data,
				function(response) {
					if( typeof response.posts != "undefined" ) {
						for( var i in response.posts ) {
							html += _.template([
								'<li data-id="<%= ID %>" data-permalink="<%= permalink %>">',
									'<a href="#" class="add">Add</a>',
									'<span><%= post_title %></span>',
								'</li>'
							].join(''), response.posts[i]);
						}
						plugin.$results.html(html);
					}
				}
			);
		}

		plugin.serialize = function() {
			
			var ids = [], i = 1;

			plugin.$list.find('li').each(function(){
				$(this).find('input').val(i);
				ids.push( $(this).data('id') );
				i++;
			});

			plugin.$field.val( ids.join(',') );
		}

		plugin.init();

	}

	$.fn.postFinder = function(options) {

		return this.each(function() {
			if (undefined == $(this).data('postFinder')) {
				var plugin = new $.postFinder(this, options);
				$(this).data('postFinder', plugin);
			}
		});

	}

})(jQuery);
