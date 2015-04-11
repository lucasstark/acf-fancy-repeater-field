(function ($) {

	acf.fields.fancyrepeater = acf.field.extend({
		type: 'fancyrepeater',
		$el: null,
		$input: null,
		$field_list: null,
		$clone: null,
		actions: {
			'ready': 'initialize',
			'append': 'initialize',
			'show': 'show'
		},
		events: {
			'click .acf-fancyrepeater-add-row' : 'add',
			'click .acf-fancyrepeater-remove-row': 'remove',
		},
		focus: function () {
			
			this.$el = this.$field.find('.acf-fancyrepeater:first');
			this.$repeater = this.$field.find('.acf-fancyrepeater:first');
			this.$input = this.$field.find('input:first');
			
			this.$field_list = this.$repeater.find('div.acf-fancyrepeater-list:first');
			this.$clone = this.$field_list.children('div.acf-clone');
			
			this.o = acf.get_data(this.$repeater);
			
		},
		initialize: function () {
			
			this.$el = this.$field.find('.acf-fancyrepeater:first');
			this.$repeater = this.$field.find('.acf-fancyrepeater:first');
			this.$input = this.$field.find('input:first');
			
			this.$field_list = this.$repeater.find('div.acf-fancyrepeater-list:first');
			this.$clone = this.$field_list.children('div.acf-clone');
			
			this.o = acf.get_data(this.$repeater);
			
			
			
			this.$field.on('click', '.edit-field', function (e) {
				e.preventDefault();
				self.edit_field( $(this).closest('div.acf-fancyrepeater-object') );
			});
			
			var titlefieldkey = this.o.titlefieldkey;
			this.$field.on('keyup', 'div[data-key="' + titlefieldkey + '"] input', function( e ){
				self.update_child_title_label( $(this) );
			});
			

			// CSS fix
			/*
			 this.$tbody.on('mouseenter', 'tr.acf-row', function( e ){
			 
			 // vars
			 var $tr = $(this),
			 $td = $tr.children('.remove'),
			 $a = $td.find('.acf-childbuilder-add-row'),
			 margin = ( $td.height() / 2 ) + 9; // 9 = padding + border
			 
			 
			 // css
			 $a.css('margin-top', '-' + margin + 'px' );
			 
			 });
			 */

			// sortable
			if (this.o.max != 1) {

				// reference
				var self = this, $field_list = this.$field_list, $field = this.$field;


				//this.$el.one('mouseenter', 'td.order', function( e ){

				$field_list.unbind('sortable').sortable({
					handle: '.acf-icon',
					forceHelperSize: true,
					forcePlaceholderSize: true,
					scroll: true,
					start: function (event, ui) {

						// focus
						self.doFocus($field);

						acf.do_action('sortstart', ui.item, ui.placeholder);

					},
					stop: function (event, ui) {

						// render
						self.render();

						acf.do_action('sortstop', ui.item, ui.placeholder);

					},
					update: function (event, ui) {

						// trigger change
						self.$input.trigger('change');

					}

				});

				//});

			}


			// set column widths
			// no longer needed due to refresh action in acf.pro model
			//acf.pro.render_table( this.$el.children('table') );


			// disable clone inputs
			this.$clone.find('[name]').attr('disabled', 'disabled');


			// render
			this.render();

		},
		show: function () {

			this.$repeater.find('.acf-field:visible').each(function () {

				acf.do_action('show_field', $(this));

			});

		},
		count: function () {
			return this.$field_list.length - 1;

		},
		render: function () {

			// loop over fields
			this.$field_list.children().each(function (i) {
				// update icon number
				$(this).children('.handle').find('.acf-icon').html(i + 1);
			});


			// empty?
			if (this.count() == 0) {

				this.$repeater.addClass('empty');

			} else {

				this.$repeater.removeClass('empty');

			}


			// row limit reached
			if (this.o.max > 0 && this.count() >= this.o.max) {

				this.$repeater.addClass('disabled');
				this.$repeater.find('> .acf-hl .acf-button').addClass('disabled');

			} else {

				this.$repeater.removeClass('disabled');
				this.$repeater.find('> .acf-hl .acf-button').removeClass('disabled');

			}

		},
		add: function (e) {
			
			// validate
			if (this.o.max > 0 && this.count() >= this.o.max) {

				alert(acf._e('childbuilder', 'max').replace('{max}', this.o.max));
				return false;

			}
			
			
			// create and add the new field
			var new_id = acf.get_uniqid();
			var html = this.$clone.outerHTML();
			

			// replace acfcloneindex
			var html = html.replace(/(="[\w-\[\]]+?)(acfcloneindex)/g, '$1' + new_id);
			var $html = $(html);


			// remove clone class
			$html.removeClass('acf-clone');
			
			// enable inputs
			$html.find('[name]').removeAttr('disabled');
			
			// show
			$html.show();
			this.$clone.before($html);


			// trigger mouseenter on parent childbuilder to work out css margin on add-row button
			//this.$field.parents('.acf-row').trigger('mouseenter');


			// update order
			this.render();
			this.open_field( $html.first('div.acf-fancyrepeater-object') );

			// validation
			//acf.validation.remove_error(this.$field);


			// setup fields
			acf.do_action('append', $html);
			

			// return
			return $html;
		},
		remove : function( e ){
			
			// validate
			// reference
			var self = this;
			var $field = this.$field;
			
			
			if (this.count() <= this.o.min) {
				//TODO:  Add in min checking
				//alert(acf._e('childbuilder', 'min').replace('{min}', this.o.min));
				//return false;
			}
			
			// vars
			var $field_object = e.$el.closest('.acf-fancyrepeater-object');
			var $field_list	= $field_object.closest('.acf-fancyrepeater-list');
			
			// set layout
			$field_object.css({
				height		: $field_object.height(),
				width		: $field_object.width(),
				position	: 'absolute'
			});
			
			
			// wrap field
			$field_object.wrap( '<div class="temp-field-wrap" style="height:' + $field_object.height() + 'px"></div>' );
			
			
			// fade $el
			$field_object.animate({ opacity : 0 }, 250);
			
			
			// close field
			var end_height = 0,
			$show = false;
			
			
			if( $field_list.children('.acf-fancyrepeater-object').length == 1 ) {
				//TODO:  Add in no children message. 
				//$show = $field_list.children('.no-fields-message');
				//end_height = $show.outerHeight();
			}
			
			$field_object.parent('.temp-field-wrap').animate({ height : end_height }, 250, function(){
				
				// show another element
				if( $show ) {
				
					$show.show();
					
				}
				
				
				// action for 3rd party customization 
				acf.do_action('remove', $(this));
				
				
				// remove $el
				$(this).remove();
				
				
				// render fields becuase they have changed
				self.render();
				
			});
		},		
		/*
		 *  edit_field
		 *
		 *  This function is triggered when clicking on a field. It will open / close a fields settings
		 *
		 *  @type	function
		 *  @date	8/04/2014
		 *  @since	5.0.0
		 *
		 *  @param	$el
		 *  @return	n/a
		 */

		edit_field: function ($el) {

			if ($el.hasClass('open')) {

				this.close_field($el);

			} else {

				this.open_field($el);

			}
		},
		/*
		 *  open_field
		 *
		 *  This function will open a fields settings
		 *
		 *  @type	function
		 *  @date	8/04/2014
		 *  @since	5.0.0
		 *
		 *  @param	$el
		 *  @return	n/a
		 */

		open_field: function ($el) {

			// bail early if already open
			if ($el.hasClass('open')) {

				return false;

			}


			// add class
			$el.addClass('open');


			// action for 3rd party customization
			//acf.do_action('open_field', $el);


			// animate toggle
			$el.children('.settings').animate({'height': 'toggle'}, 250);
		},
		/*
		 *  close_field
		 *
		 *  This function will open a fields settings
		 *
		 *  @type	function
		 *  @date	8/04/2014
		 *  @since	5.0.0
		 *
		 *  @param	$el
		 *  @return	n/a
		 */

		close_field: function ($el) {

			// bail early if already closed
			if (!$el.hasClass('open')) {

				return false;

			}


			// remove class
			$el.removeClass('open');


			// action for 3rd party customization
			//acf.do_action('close_field', $el);


			// animate toggle
			$el.children('.settings').animate({'height': 'toggle'}, 250);		
		},
		update_child_title_label : function($input) {
			console.log('input');
			var $el =  $input.closest('div.acf-fancyrepeater-object');
			$el.find('.handle .li-fancyrepeater-label strong a').text( $input.val() );
		},
	});

})(jQuery);