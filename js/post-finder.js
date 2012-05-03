var PostFinder = window.PostFinder || [];

jQuery(document).ready(function($) {
	
	PostFinder = {
	
		$overlay : $('#pf-overlay'),
		$find : $('.find-posts'),
		$modal : $('#pf-modal'),
		$tabs : $('#pf-modal .tabs a'),
		$close : $('#pf-modal .close'),
		$add : $('.pf-item .add'),
		$remove : $('.pf-item .remove'),
		$nonce : $('#pf-nonce'),
		$target : null,
			
		init : function() {
			
			var t = this;
	
			t.setup();
			
			t.$find.live('click', function() {
			
				// show a popup with 2 tabs, recent posts and then a search tab
				// prob reuse the same search, just have a different target depending on which button you click
				t.$target = $(this).parent().find('.pf-posts');
				
				t.open();
				
			});
			
			t.$overlay.click(function() {
				t.close();
			});
			
			t.$tabs.click(function() {
				var tab = $(this).attr('data-tab');
				t.$tabs.removeClass('selected');
				$(this).addClass('selected');
				$('#pf-modal .panel').hide();
				$('#pf-modal .' + tab + '-panel').show();
			});
			
			t.$close.click(function() {
				t.close();
			});
			
			t.$add.live('click',function(e) {
				e.preventDefault();
				var li = $(this).parent();
				t.$target.append( li );
				t.serialize( t.$target.parent() );
			});
			
			t.$remove.live('click', function(e) {
				e.preventDefault();
				$(this).remove();
				t.serialize( t.$target.parent() );
			});
		},
		
		open : function() {
			var t = this;
			t.$overlay.show();
			t.$modal.show();
		},
		
		close : function() {
			var t = this;
			t.$overlay.hide();
			t.$modal.hide();
		},
		
		setup : function() {
			
			var t = this;
			
			var cnt = 1;
			
			$('#widgets-right .pf').each(function() {
				
				var html = '';
				html += '<div class="pf-wrap" id="pf-' + cnt + '" data-pf="' + cnt + '">';
				html += '<ul class="pf-posts"></ul>';
				html += '<input type="button" class="find-posts button" value="Add Posts"/>';
				html += '</div>';
				
				$(this).before( html ).addClass('pf-input-'+cnt).hide();
		
				$('#pf-' + cnt + ' .pf-posts').sortable({
					placeholder : 'placeholder',
					update : function(e, ui) {
						t.serialize( ui.item.parent().parent() );
					}
				});
			
				var data = {
					post_ids : $(this).val(),
					action : 'pf_get_posts',
					_ajax_nonce : t.$nonce.val()
				}
				
				$.ajax({
					url : ajaxurl,
					data : data,
					dataType : 'json',
					success : function( json ) {
						if( json.html ) {
							$('#pf-' + cnt + ' .pf-posts').html( json.html );
						}
					}
				});
				
				cnt++;
				
			});
		},
		
		serialize : function( obj ) {
			var ids = [];
			$('.pf-item', obj).each(function() {
				ids.push( $(this).attr('data-id') );
			});
			$('.pf-input-'+obj.attr('data-pf')).val( ids.join(',') );
		}
		
	}

	PostFinder.init();
});