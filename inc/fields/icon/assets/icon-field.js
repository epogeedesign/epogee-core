(function($, undefined){
	var Field = acf.Field.extend({
		type: 'ep_icon',
		select2: false,
		wait: 'load',
		events: {
			'removeField': 'onRemove'
		},
		
		$input: function(){
			return this.$('select');
		},
		
		initialize: function(){
			// vars
			var $select = this.$input();
			
			// inherit data
			this.inherit($select);

			var sprite = this.$input().data('sprite');
			var render = function (item) {
				if (!item.id) {
					return $select.data('placeholder');
				}

				var html = '<div class="ep-icon-select2-row">';
				
				html += '<div class="svg-icon"><svg>'
					+ '<use xlink:href="' + sprite + '#' + item.id + '" stroke="#000" stroke-width="1" />'
					+ '</svg></div>';

				html += '<span>' + ((item.title) ? item.title : item.id) + '</span>';

				html += '</div>';

				return html;
			};

			// select2
			this.select2 = $select.select2({
				allowClear: $select.data('allow_null'),
				multiple: false,
				width: '100%',
				templateSelection: render,
				templateResult: render,
				escapeMarkup: function(markup) {
					return markup;
				}
			});

			this.select2.on('select2:clear', function () {
				$select.val('');
			});
		},

		templateRender: function (icon) {
			var sprite = this.$input.data('sprite');
			
			var html = '<svg><use xlink:href="' + sprite + '#' + icon.id + '" stroke="#000" stroke-width="1" /></svg>';

			return html;
		},
		
		onRemove: function(){
			// if( this.select2 ) {
			// 	this.select2.destroy();
			// }
		}
	});
	
	acf.registerFieldType(Field);
})(jQuery);
