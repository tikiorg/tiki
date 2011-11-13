$.fn.reportBuilder = function(o, joinIndex) {
	var me = $(this);
	
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
						.addClass(option.objName)
						.appendTo(option.obj);
					
					me.one('reportBuilt', function() {
						select.change();
					});
				},
				values: function() {
					$('<option />')
						.attr('value', this.value)
						.text(this.label)
						.appendTo(select);
				},
				empty: function() {
					$('<input type="text" class="reportBuilderInput" />')
						.attr('name', option.objName)
						.addClass(option.objName)
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
						.addClass(option.objName)
						.attr('value', this.value || this + '')
						.appendTo(area);
					$('<span />')
						.text(this.label || this + '')
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
				.addClass(option.objName)
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
							.addClass(option.objName)
							.appendTo(option.obj);
						
						me.one('reportBuilt', function() {
							select.change();
						});
					},
					values: function() {
						$('<option />')
							.attr('value', this.value)
							.text(this.label)
							.appendTo(select);
					},
					end: function() {
						if (option.relationLabel) option.obj.append(option.relationLabel);
						$('<input type="text" class="reportBuilderInput" />')
							.attr('name', option.objName)
							.addClass(option.objName)
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
		join: function(option, type) {
			var joinContainer = type.data("joinContainer");
			
			if (joinContainer) joinContainer.remove();
			
			var joinContainer = $("<div class='reportJoin' />")
				.insertAfter(type);
			
			type.data("joinContainer", joinContainer);

			if (option.join.type == "inner") {
				type.find(':input:first').change(function() {
					var meJoiner = $("<div />");
					
					joinContainer.html('');
					
					$('<div class="reportOption" />')
						.text(option.join.label + ' ' + option.join.on.label)
						.appendTo(joinContainer);
					
					var getJoiner = $('<select />')
						.change(function() {
							if (!$(this).val()) return;
							
							meJoiner
								.addClass('ui-state-active joinedReport')
								.insertAfter(type)
								.reportBuilder(o, $('.reportJoin').length)
								.bind('reportBuilt', function() {
									
									
								});
						})
						.appendTo(joinContainer);
					
					$(o.definition.values[option.join.on.values]).each(function() {
						$('<option />')
							.attr('value', this.value)
							.text(this.label)
							.appendTo(getJoiner);
					});
					
					$('<span />')
						.text(option.join.relationLabel + ' ' + option.join.on.dependancyLabel)
						.appendTo(joinContainer);
					
					var setJoiner = $('<select />')
						.appendTo(joinContainer);
					
					$(':input.' + option.join.on.keyDependancy + ':checked').each(function() {
						var i = $(this).val();
						$(o.definition.values[option.join.on.dependancyValues]).each(function() {
							if (this.value == i) {
								$('<option />')
									.attr('value', this.value)
									.text(this.label)
									.appendTo(setJoiner);
							}
						});
					});
				});
			}
				
			me.bind('reportBuilt', function() {
				type.find(':input:first').change();
			});
		},
		base: function(option, checkJoins) {
			if (!option.options) return;
			
			var parent = $('<span class="reportBuilderObj "/>');
			
			$(option.options).each(function() {
				this.objName = (joinIndex ? "joinIndex_" + joinIndex + "_" : '') + (option.key ? option.key + '_' : '') + this.key;
				$('<div class="reportOption" />').text(this.label).appendTo(parent);
				
				var type = build[this.type](this);
				
				$('<span class="reportBuilder' + (option.type ? option.type : '') + '" />')
					.attr('id', this.objName)
					.append(type)
					.appendTo(parent);
				
				if (checkJoins && this.join) { //only supported on initial option
					build.join(this, type);
				}
				
				$('<br />').appendTo(parent);
				
				if (this.options) {
					parent.append(build.base(this));
				}
			});	
			
			return parent;
		}
	};
	
	var base = build.base(o.definition, true);
	
	me
		.append(base)
		.trigger('reportReady')
		.trigger('reportBuilt');
	
	return this;
};