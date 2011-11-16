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
		single: function(option) {
			var obj = objs[option.objName] = $('<span class="reportBuilderObj" />')
				.addClass(option.objName);
			
			var select;
			
			build.dependancy(obj, option, {
				start: function() {
					select = $('<select class="reportBuilderInput" />')
						.attr('name', option.objName)
						.addClass(option.objName)
						.appendTo(obj);
					
					inputs[option.objName] = select;

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
						.attr('name', option.objName)
						.addClass(option.objName)
						.appendTo(obj);
				}
			});
			
			return obj;
		},
		multi: function(option) {
			var obj = objs[option.objName] = $('<span class="reportBuilderObj" />')
				.addClass(option.objName);
			
			build.dependancy(obj, option, {
				start: function() {
					$('<a href="#"> Toggle Checked </a>')
						.toggle(function() {
							$(inputs[option.objName]).find('.reportBuilderInput:checkbox').removeAttr('checked');
						}, function() {
							$(inputs[option.objName]).find('.reportBuilderInput:checkbox').attr('checked', 'true');
						})
						.appendTo(obj);
				},
				values: function() {
					var area = $('<div />').appendTo(obj);
					
					$('<input type="checkbox" class="reportBuilderInput" checked="true" />')
						.attr('name', option.objName + '[]')
						.addClass(option.objName)
						.attr('value', this.value || this + '')
						.appendTo(area);
					
					inputs[option.objName] = area.find('input');
					
					$('<span />')
						.text(this.label || this + '')
						.appendTo(area);
				}
			});
			
			return obj;
		},
		date: function(option) {
			var obj = objs[option.objName] = $('<span class="reportBuilderObj" />')
				.addClass(option.objName);
			
			var date = $('<input type="text" class="reportBuilderInput" />');
			date
				.attr('name', option.objName)
				.addClass(option.objName)
				.ready(function() {
					date.datepicker();
				})
				.appendTo(obj);
			
			return obj;
		},
		singleOneToOne: function(option) {
			var obj = objs[option.objName] = $('<span class="reportBuilderObj" />')
				.addClass(option.objName);
			var select;
			
			$(option.values).each(function(i) {
				build.dependancy(obj, option, {
					start: function() {
						select = $('<select class="reportBuilderInput" />')
							.attr('name', option.objName)
							.addClass(option.objName)
							.appendTo(obj);
						
						inputs[option.objName + ''] = select;
						
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
		joinInner: function(option, type) {
			var joinController = $('<div clas="joinController" style="padding-left: 25px;" />')
				.addClass('ui-widget-content');

			$('<div class="reportOption" />')
				.text(option.join.label + ' ' + option.join.on.label)
				.appendTo(joinController);
			
			var joinThis = $('<select class="joinThis" />')
				.appendTo(joinController)
				.change(function() {
					if (!$(this).val()) return;
					
					joinReport
						.html('')
						.reportBuilder(oCopy, 'joinIndex_' + $('.joinContainer').length + '_');
				});
			
			$(o.definition.values[option.join.on.values]).each(function() {
				$('<option />')
					.attr('value', this.value)
					.text(this.label)
					.appendTo(joinThis);
			});
			
			$('<span />')
				.text(option.join.relationLabel + ' ' + option.join.on.dependancyLabel)
				.appendTo(joinController);
			
			var joinOn = $('<select class="joinOn" />')
				.appendTo(joinController);
			
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
			
			var minus = $.uiIcon("minus")
				.appendTo(joinController)
				.click(function() {
					minus.hide();
					plus.show();
					joinReport.slideUp();
				});
			
			var plus = $.uiIcon("plus")
				.appendTo(joinController)
				.click(function() {
					minus.show();
					plus.hide();
					joinReport.slideDown();
				})
				.hide();
			
			var close = $.uiIcon("close")
				.appendTo(joinController)
				.click(function() {
					joinController.remove();
				});
			
			var joinReport = $("<div class='joinReport' />")
				.appendTo(joinController);
				
			joinThis.change();
			
			return joinController;
		},
		join: function(option, type) {
			if (prefix) return;
		
			var joinContainer = $("<div class='joinContainer' />")
				.insertAfter(objs[option.objName]);
			
			$('<span class="button joinButton"><a href="#">' + option.join.label + ' ' + option.join.on.label + '</a></span>')
				.click(function() {
					if (option.join.type == "inner") {
						joinContainer
							.append(build.joinInner(option, type));
					}
					return false;
				})
				.appendTo(joinContainer);
			
			
			$(inputs[option.objName])
				.change(function() {
					//joinContainer.html('');
				});
		},
		base: function(option) {
			if (!option.options) return;
			
			var parent = $('<span class="reportBuilderObj "/>');
			
			$(option.options).each(function() {
				this.objName = prefix  + (option.key ? option.key + '_' : '') + this.key;
				$('<div class="reportOption" />').text(this.label).appendTo(parent);
				
				var type = build[this.type](this);
				
				$('<span class="reportBuilder' + (option.type ? option.type : '') + '" />')
					.attr('id', this.objName)
					.append(type)
					.appendTo(parent);
				
				if (!prefix && this.join) {
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
	
	var base = build.base(o.definition);
	
	me
		.append(base)
		.trigger('reportReady')
		.trigger('reportBuilt');
	
	return this;
};