//TODO: little wierd to have this outside the plugin but 
//I don't want the browser to have to do this join every 
//time the plugin is called on an element which happenes a lot.
var POSTFINDERTEMPLATE = [//Very specific name so as to not be overwritten accidentaly
	'<li data-id="<%= id %>">',
		'<span><%= title %></span>',
		'<nav>',
			'<a href="#" target="_blank">Edit</a>',
			'<a href="#" target="_blank">View</a>',
			'<a href="#" class="remove">Remove</a>',
		'</nav>',
	'</li>'
].join('');

(function($) {

	$.postFinder = function(element, options) {

		var defaults = {
			template : POSTFINDERTEMPLATE,
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
			console.log("init");
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
			console.log("select", plugin.$select);
			console.log("select", plugin.settings.selectSelector);
			plugin.$select.on('change click', function(e){
				add_item( $(this).val(), $('option:selected', this).text() );
			});

			// bind search button
			plugin.$search.find('.button').click(function(){
				search();
			});

			// bind list
			plugin.$list.sortable({
				placeholder: 'placeholder',
				update: function(ui, e) {
					serialize();
				}
			});

			// remove button
			console.log("list", plugin.$list);
			plugin.$list.on('click', '.remove', function(e){
				e.preventDefault();
				remove_item( $(this).closest('li').data('id') );
			});

			// add button
			plugin.$results.on('click', '.add', function(e){
				e.preventDefault();
				$li = $(this).closest('li');
				add_item( $li.data('id'), $li.find('span').text() );
			});
		}

		var add_item = function( id, title ) {//private method
			console.log("add item");
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
			plugin.$list.append(_.template(plugin.settings.template, { id: id, title: title }));

			// hide notice
			plugin.$list.find('.notice').hide();

			// remove from select if there
			plugin.$select.find('option[value="' + id + '"]').remove();

			// update the input
			serialize();
		}

		//Prv method to remove an item
		var remove_item = function( id ) {
			console.log("remove item");
			plugin.$list.find('li[data-id="' + id + '"]').remove();

			serialize();

			// show notice if no posts
			if( plugin.$list.find('li').length == 0 ) {
				plugin.$list.find('.notice').show();
			}
		}

		var search = function() {
			console.log("search");
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
								'<li data-id="<%= ID %>">',
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

		var serialize = function() {
			console.log("serialize");
			var ids = [];

			plugin.$list.find('li').each(function(){
				ids.push( $(this).data('id') );
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
