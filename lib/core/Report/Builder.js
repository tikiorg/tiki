$.fn.reportBuilder = function(o, prefix) {
	prefix = (prefix ? prefix : '');
	var oCopy = $.extend({}, o);
	var me = $(this);
	
	o = $.extend({
		definition: {
			options: [],
			values: []
		}
	}, o);
	
	if (!o.definition.options) return this;
	
	var objs = {};
	var inputs = {};
	
	var build = {
		single: function(option, name) {
			var obj = objs[name] = $('<span class="reportBuilderObj" />')
				.addClass(name);
			
			var select;
			
			build.dependancy(obj, option, {
				start: function() {
					select = $('<select class="reportBuilderInput" />')
						.attr('name', name)
						.addClass(name)
						.appendTo(obj);
					
					inputs[name] = select;

					me.one('reportBuilt', function() {
						select.change()
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
						.attr('name', name)
						.addClass(name)
						.appendTo(obj);
				}
			});
			
			return obj;
		},
		multi: function(option, name) {
			var obj = objs[name] = $('<span class="reportBuilderObj" />')
				.addClass(name);
			
			build.dependancy(obj, option, {
				start: function() {
					$.uiIconButton("check")
						.toggle(function() {
							obj.find(':checkbox').removeAttr('checked');
						}, function() {
							obj.find(':checkbox').attr('checked', true);
						})
						.appendTo(obj);
				},
				values: function() {
					var area = $('<div />').appendTo(obj);
					
					$('<input type="checkbox" class="reportBuilderInput" checked="true" />')
						.attr('name', name + '[]')
						.addClass(name)
						.attr('value', this.value || this + '')
						.appendTo(area);
					
					inputs[name] = area.find('input');
					
					$('<span />')
						.text(this.label || this + '')
						.appendTo(area);
				}
			});
			
			return obj;
		},
		date: function(option, name) {
			var obj = objs[name] = $('<span class="reportBuilderObj" />')
				.addClass(name);
			
			var date = $('<input type="text" class="reportBuilderInput" />');
			date
				.attr('name', name)
				.addClass(name)
				.ready(function() {
					date.datepicker();
				})
				.appendTo(obj);
			
			return obj;
		},
		singleOneToOne: function(option, name) {
			var obj = objs[name] = $('<span class="reportBuilderObj" />')
				.addClass(name);
			var select;
			
			$(option.values).each(function(i) {
				build.dependancy(obj, option, {
					start: function() {
						select = $('<select class="reportBuilderInput" />')
							.attr('name', name)
							.addClass(name)
							.appendTo(obj);
						
						inputs[name + ''] = select;
						
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
							.attr('name', name)
							.addClass(name)
							.appendTo(obj);
					}
				});
			});
			
			return obj;
		},
		dependancy: function(obj, option, fns) {
			var values = o.definition.values[option.values];
			if (values) {
				if (option.dependancy) {
					me.one('reportReady', function() {
						$(inputs[prefix + option.dependancy]).change(function() {
							obj.html('');
							
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
		joinInner: function(option, type, name) {
			var reportId = name + '_join_' + $('.joinContainer').length + '_';
			
			var joinController = $('<div clas="joinController" />')
				.addClass('ui-widget-content ui-state-default');
			
			var joinHeader = $('<div class="joinHeader ui-widget-header" />')
				.appendTo(joinController);
				
			$('<span />')
				.text(' ' + option.join.label + ' ' + option.join.on.label + ' ')
				.appendTo(joinHeader);
			
			var joinThis = $('<select class="joinThis" />')
				.appendTo(joinHeader)
				.change(function() {
					if (!$(this).val()) return;
					
					joinReport
						.html('')
						.one('reportBuilt', function() {
							joinReport.find('#' + reportId + option.key)
								.parent().children()
								.hide()
								.last().show();	
						})
						.reportBuilder(oCopy, reportId);
				});
			
			$(o.definition.values[option.join.on.values]).each(function() {
				$('<option />')
					.attr('value', this.value)
					.text(this.label)
					.appendTo(joinThis);
			});
			
			$('<span />')
				.text(' ' + option.join.relationLabel + ' ' + option.join.on.dependancyLabel + ' ')
				.appendTo(joinHeader);
			
			var joinOn = $('<select class="joinOn" />')
				.appendTo(joinHeader)
				.attr('name', reportId + 'on');
			
			$(':input.' + option.join.on.keyDependancy)
				.change(function() {
					
					joinOn.html('');
					
					$(':input.' + option.join.on.keyDependancy + ':checked').each(function() {
						var i = $(this).val();
						$(o.definition.values[option.join.on.dependancyValues]).each(function() {
							if (this.value == i) {
								$('<option />')
									.attr('value', this.value)
									.text(this.label)
									.appendTo(joinOn);
							}
						});
					});
					
				})
				.change();
			
			var close = $.uiIconButton("close")
				.appendTo(joinHeader)
				.css('float', 'right')
				.click(function() {
					joinController.remove();
				});
			
			var minus = $.uiIconButton("minus")
				.appendTo(joinHeader)
				.css('float', 'right')
				.click(function() {
					minus.hide();
					plus.show();
					joinReport.slideUp();
				});
			
			var plus = $.uiIconButton("plus")
				.appendTo(joinHeader)
				.css('float', 'right')
				.click(function() {
					minus.show();
					plus.hide();
					joinReport.slideDown();
				})
				.hide();
			
			var joinReport = $('<div class="joinReport" />')
				.appendTo(joinController);
				
			joinThis
				.change(function() {
					joinReport.find('#' + reportId + option.key + ' :input')
						.val(joinThis.val())
						.change();
				})
				.change();
			
			return joinController;
		},
		join: function(option, type, name) {
			if (prefix) return;
		
			var joinContainer = $("<div class='joinContainer' style='padding: 10px;' />")
				.insertAfter(objs[name]);
			
			$('<span class="button joinButton"><a href="#">' + option.join.label + ' ' + option.join.on.label + '</a></span>')
				.click(function() {
					if (option.join.type == "inner") {
						joinContainer
							.append(build.joinInner(option, type, name));
					}
					return false;
				})
				.insertAfter(inputs[name]);
			
			
			$(inputs[name])
				.change(function() {
					joinContainer.html('');
				});
		},
		base: function(option) {
			if (!option.options) return;
			
			var parent = $('<span class="reportBuilderObj "/>');
			
			$(option.options).each(function() {
				var name = prefix  + (option.key ? option.key + '_' : '') + this.key;
				var reportOption = $('<div class="reportOption" />')
					.appendTo(parent);
				
				var reportOptionLabel = $('<div class="reportOptionLabel" />')
					.appendTo(reportOption)
					.text(this.label);
				
				var type = build[this.type](this, name);
				
				$('<span class="reportBuilder' + (option.type ? option.type : '') + '" />')
					.attr('id', name)
					.append(type)
					.appendTo(reportOption);
				
				if (!prefix && this.join) {
					build.join(this, type, name);
				}
				
				$('<br />').appendTo(reportOption);
				
				if (this.options) {
					reportOption.append(build.base(this));
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