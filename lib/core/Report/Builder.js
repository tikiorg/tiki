$.fn.reportBuilder = function(o) {
	var me = $(this);
	
	var I = window.reportBuilderI = (window.reportBuilderI ? window.reportBuilderI + 1 : 0);
	
	o = $.extend({
		definition: {
			options: [],
			values: []
		}
	}, o);
	
	if (!o.definition.options) return this;
	
	var build = {
		single: function(option) {
			option.obj = $('<span />');
			var select;
			
			build.dependancy(option, {
				start: function() {
					select = $('<select class="reportBuilderInput" />')
						.attr('name', option.objName)
						.appendTo(option.obj);
					
					me.one('reportBuilt', function() {
						select.change();
					});
				},
				values: function() {
					$('<option />')
						.attr('value', this.value)
						.text(this.name)
						.appendTo(select);
				},
				empty: function() {
					$('<input type="text" class="reportBuilderInput" />')
						.attr('name', option.objName)
						.appendTo(option.obj);
				}
			});
			
			return option.obj;
		},
		multi: function(option) {
			option.obj = $('<span />');
			
			build.dependancy(option, {
				start: function() {
					$('<a href="#"> Toggle Checked </a>')
						.toggle(function() {
							option.obj.find('.reportBuilderInput:checkbox').removeAttr('checked');
						}, function() {
							option.obj.find('.reportBuilderInput:checkbox').attr('checked', 'true');
						})
						.appendTo(option.obj);
				},
				values: function() {
					var area = $('<div />').appendTo(option.obj);
					
					$('<input type="checkbox" class="reportBuilderInput" checked="true" />')
						.attr('name', option.objName + '[]')
						.attr('value', this.value || this + '')
						.appendTo(area);
					$('<span />')
						.text(this.name || this + '')
						.appendTo(area);
				}
			});
			
			return option.obj;
		},
		date: function(option) {
			option.obj = $('<span />');
			
			var date = $('<input type="text" class="reportBuilderInput" />');
			date
				.attr('name', option.objName)
				.ready(function() {
					date.datepicker();
				})
				.appendTo(option.obj);
			
			return option.obj;
		},
		singleOneToOne: function(option) {
			option.obj = $('<span />');
			var select;
			
			$(option.values).each(function(i) {
				build.dependancy(option, {
					start: function() {
						select = $('<select class="reportBuilderInput" />')
							.attr('name', option.objName)
							.appendTo(option.obj);
						
						me.one('reportBuilt', function() {
							select.change();
						});
					},
					values: function() {
						$('<option />')
							.attr('value', this.value)
							.text(this.name)
							.appendTo(select);
					},
					end: function() {
						if (option.relationLabel) option.obj.append(option.relationLabel);
						$('<input type="text" class="reportBuilderInput" />')
							.attr('name', option.objName)
							.appendTo(option.obj);
					}
				});
			});
			
			return option.obj;
		},
		dependancy: function(option, fns) {
			var values = o.definition.values[option.values];
			if (values) {
				if (option.dependancy) {
					me.bind('reportReady', function() {
						me.find('#' + option.dependancy + ' .reportBuilderInput:first').change(function() {
							option.obj.html('');
							
							if (fns.start) fns.start();
							
							var newValues = [];
							
							var val = $(this).val();
							$(values).each(function(i) {
								if (this.dependancy == val) newValues.push(values[i]);
							});
							
							$(newValues).each(fns.values);
							
							if (fns.end) fns.end();
						});
					});
				} else {
					if (fns.start) fns.start();
					$(values).each(fns.values);
				}
			} else {
				if (fns.empty)
					fns.empty();
			}
		},
		base: function(option, name) {
			if (!option.options) return;
			
			var parent = $('<span class="reportBuilderObj "/>');
			
			$(option.options).each(function() {
				this.objName = (option.name ? option.name + '_' : '') + this.name;
				$('<span style="font-weight: bold; padding-right: 5px;" />').text(this.label).appendTo(parent);
				$('<span class="reportBuilder' + (option.type ? option.type : '') + '" />')
					.attr('id', this.objName)
					.append(
						build[this.type](this)
					)
					.appendTo(parent);
				
				$('<br />').appendTo(parent);
				
				if (this.options) {
					parent.append(build.base(this));
				}
			});	
			
			return parent;
		}
	};
	
	var base = build.base(o.definition);
	
	me
		.append(base)
		.trigger('reportReady')
		.trigger('reportBuilt');
	
	return this;
};