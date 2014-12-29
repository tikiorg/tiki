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
	var repeats = {};
	
	function label(val) {
		return (val.label != undefined ? val.label : val + '');
	}
	
	function value(val) {
		return (val.value != undefined ? val.value : val + '');
	}
	
	var brkts = /\[\]/g;
	
	var build = {
		single: function(option, key) {
			var select;
			
			return build.dependancy(option, key, {
				start: function(key) {
					return inputs[key] = select = $('<select class="reportBuilderInput" />')
						.attr('name', key)
						.addClass(key.replace(brkts, ''))
						.appendTo(this);
				},
				values: function() {
					$('<option />')
						.attr('value', value(this))
						.text(label(this))
						.appendTo(select);
				},
				empty: function(key) {
					return inputs[key] = $('<input type="text" class="reportBuilderInput" />')
						.attr('name', key)
						.addClass(key.replace(brkts, ''))
						.appendTo(this);
				}
			});
		},
		multi: function(option, key) {
			var obj;
			return build.dependancy(option, key, {
				start: function() {
					obj = $(this);
					$.uiIconButton("check")
						.toggle(function() {
							obj.find(':checkbox').removeAttr('checked');
						}, function() {
							obj.find(':checkbox').prop('checked', true);
						})
						.appendTo(this);
				},
				values: function() {
					var area = $('<div />')
						.appendTo(obj);
					
					$('<input type="checkbox" class="reportBuilderInput" checked="true" />')
						.attr('name', key + '[]')
						.addClass(key.replace(brkts, ''))
						.attr('value', value(this))
						.appendTo(area);
					
					inputs[key] = area.find('input');

					$('<span />')
						.text(label(this))
						.appendTo(area);
				}
			});
		},
		date: function(option, key) {
			return build.dependancy(option, key, {empty: function() {
				var date = $('<input type="text" class="reportBuilderInput" />');
				date
					.attr('name', key)
					.addClass(key)
					.ready(function() {
						date.datepicker();
					})
					.appendTo(this);
			}});
		},
		singleOneToOne: function(option, key) {
			var select;
			
			return build.dependancy(option, key, {
				start: function(key) {
					return inputs[key] = select = $('<select class="reportBuilderInput" />')
						.attr('name', key)
						.addClass(key.replace(brkts, ''))
						.appendTo(this);
				},
				values: function() {
					$('<option />')
						.attr('value', value(this))
						.text(label(this))
						.appendTo(select);
				},
				end: function(key) {
					if (option.relationLabel) $(this).append(option.relationLabel);
					return inputs[key] = $('<input type="text" class="reportBuilderInput" />')
						.attr('name', key)
						.addClass(key.replace(brkts, ''))
						.appendTo(this);
				}
			});
		},
		dependancy: function(option, key, fns) {
			var obj = $('<span class="reportDependancy" />')
				.addClass(key.replace(brkts, ''));
			
			objs[key] = obj;
			
			var repeat = function() {
				if (option.repeats) {
					repeat = function() {};
					$.uiIconButton("plus")
						.insertAfter(obj)
						.click(function() {
							repeats[key] = (repeats[key] ? repeats[key] : []);
							
							var repeat = $('<div class="reportOption" />')
								.append('<div class="reportOptionLabel">' + option.label + '</div>')
								.append(build[option.type](option, key))
								.append($.uiIconButton("minus").click(function() {
									repeat.remove();
								}))
								.insertAfter(obj.parent());
							
							repeats[key].push(repeat);
							
							me
								.trigger('reportBase');
						});
				}
			};
			
			var init = function() {
				if (option.dependancy) {
					var dependancy = $(inputs[prefix + option.dependancy])
						.change(function() {
							obj.html('');
							
							if (fns.start) fns.start.apply(obj, [key + (option.repeats ? '[]' : '')]);
							
							var newValues = [];
							
							var val = $(this).val();
							$.each(values, function() {
								if (this.dependancy == val) newValues.push(this);
							});
							
							$.each(newValues, fns.values);
							
							if (fns.end) fns.end.apply(obj, [key + (option.repeats ? '[]' : '')]);
						});
					
					me.one('reportBase', function() {
						dependancy.change();
					});
				} else {
					if (fns.start) fns.start.apply(obj, [key + (option.repeats ? '[]' : '')]);
					$.each(values, fns.values);
				}
			};
			
			var values = o.definition.values[option.values];
			if (values) {
				init();
			} else if ($.isArray(option.values)) {
				values = option.values;
				init();
			} else {
				if (fns.empty) {
					fns.empty.apply(obj, [key + (option.repeats ? '[]' : '')]);
				}
			}
			
			me.one('reportBase', function() {
				repeat();
			});
			
			return obj;
		},
		joinInner: function(option, type, key) {
			var reportKey = key + '_join[]_';
			
			var joinController = $('<div class="joinController" />')
				.addClass('ui-widget-content ui-state-default');
			
			var joinHeader = $('<div class="joinHeader ui-widget-header" />')
				.appendTo(joinController);
				
			$('<span />')
				.text(option.join.label)
				.appendTo(joinHeader);
			
			//Join Object, base object to be joined to parent
			var joinObject = $('<select class="joinObject reportBuilderInput" />')
				.appendTo(joinHeader)
				.change(function() {
					if (!$(this).val()) return;
					
					joinReport
						.html('')
						.reportBuilder(oCopy, reportKey);
					
					joinReport.find('.reportBuilderInput.' + reportKey.replace(brkts, '') + option.key)
						.val(joinObject.val())
						.change()
						.attr('disabled', 'true');
				});
			
			$.each(o.definition.values[option.join.values], function() {
				$('<option />')
					.attr('value', value(this))
					.text(label(this))
					.appendTo(joinObject);
			});
			
			$('<span />')
				.text(option.join.relationLabel)
				.appendTo(joinHeader);
				
			//End Join Object
			
			//Join Settings, left first
			function joinSetting(type) {
				var joinSetting = $('<select class="joinSetting reportBuilderInput" />')
					.addClass((reportKey + type).replace(brkts, ''))
					.appendTo(joinHeader)
					.attr('name', reportKey + type);
				
				if (option.join[type].keyDependancy) {
					$(':input.' + option.join[type].keyDependancy)
						.change(function() {
							
							joinSetting.html('');
							
							$(':input.' + option.join[type].keyDependancy + ':checked').each(function() {
								var i = $(this).val();
								$.each(o.definition.values[option.join[type].values], function() {
									if (value(this) == i) {
										$('<option />')
											.attr('value', value(this))
											.text(label(this))
											.appendTo(joinSetting);
									}
								});o.definition.values[option.join[type].values]
							});
							
						})
						.change();
				} else {
					if ($.isArray(option.join[type].values)) {
						$(option.join[type].values).each(function() {
							$('<option />')
								.attr('value', value(this))
								.text(label(this))
								.appendTo(joinSetting);
						});
					} else {
						$.each(o.definition.values[option.join[type].values], function() {
							$('<option />')
								.attr('value', value(this))
								.text(label(this))
								.appendTo(joinSetting);
						});
					}
				}
			}
			
			joinSetting('left');
			$('<span />')
				.text(option.join.settingsLabel)
				.appendTo(joinHeader);
			joinSetting('right');
			//End Join Settings
			
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
			
			return joinController;
		},
		join: function(option, type, key) {
			if (prefix) return;
		
			var joinContainer = $("<div class='joinContainer' style='padding: 10px;' />")
				.addClass(key.replace(brkts, ''))
				.insertAfter(objs[key]);
			
			$('<span><a href="#">' + option.join.label + '</a></span>')
				.addClass("button joinButton " + key.replace(brkts, ''))
				.click(function() {
					joinContainer
						.append(build[option.join.type](option, type, key));
					
					return false;
				})
				.insertAfter(inputs[key]);
			
			
			$(inputs[key])
				.change(function() {
					joinContainer.html('');
				});
		},
		base: function(option) {
			if (!option.options) return;
			
			var reportBase = $('<span class="reportBase "/>');
			
			$.each(option.options, function() {
				var key = prefix  + (option.key ? option.key + '_' : '') + this.key;
				var reportOption = $('<div class="reportOption" />')
					.addClass(key.replace(brkts, ''))
					.appendTo(reportBase);
				
				var reportOptionLabel = $('<div class="reportOptionLabel" />')
					.appendTo(reportOption)
					.text(label(this));
				
				var type = build[this.type](this, key);
				
				$('<span class="reportBuilder' + (option.type ? option.type : '') + '" />')
					.append(type)
					.appendTo(reportOption);
				
				if (!prefix && this.join) {
					build.join(this, type, key);
				}
				
				$('<br />').appendTo(reportOption);
				
				if (this.options) {
					reportOption.append(build.base(this));
				}
			});	
			
			return reportBase;
		}
	};
	
	var base = build.base(o.definition);
	
	me
		.append(base)
		.trigger('reportBase')
		.trigger('reportDependency')
		.trigger('reportReady');
	
	return this;
};

$.fn.reportBuilderImport = function(data, o) {
	var me = $(this);
	
	if (!o) o = "";
	
	me.find(':checkbox').removeAttr('checked');
	var i = 0, type;
	//TODO: needs further application, but the idea is to apply the name of the array to the object on the page ;)
	var importData = function(dataItem, trail, I) {
		for(type in dataItem) {
			if (type == 'value') {
				var input = me.find('.reportBuilderInput.' + trail);
				
				if (input.is(':checkbox')) {
					input
						.each(function() {
							if ($(this).val() == dataItem[type]) {
								$(this).prop('checked', true);
							}
						})
						.change();
				} else {
					//text areas are set 1 at a time
					input = input.not('.imported').first();
					input
						.val(dataItem[type])
						.change()
						.addClass('imported');
				}
				
				console.log([input.val(), dataItem[type], trail, '.reportBuilderInput.' + trail]);
			} else if (type == 'join') {
				var thisTrail = (trail + "");
				var importTrail = $.cleanArray((trail + '_' + (isNaN(type) ? type : '')).split('_')).join("_");
				var thisData = dataItem[type];
				
				me.one('importReady', function() {
					importData(thisData, importTrail);
					me.find('.joinObject').change();
				});
				
				var button = me.find('.joinButton.' + thisTrail)
					.click();
			} else {
				i++;
				if (i < 1000)
				importData(dataItem[type], $.cleanArray((trail + '_' + (isNaN(type) ? type : '')).split('_')).join("_"), (!isNaN(type) ? type : ''));
			}
		}
	};
		
	importData(data, o);
	
	me
		.trigger('importBase')
		.trigger('importDependency')
		.trigger('importReady');
		
	console.log(i);
};

$.cleanArray = function(arr) {
	$.each(arr, function() {});
	for (var i = 0; i < arr.length; i++) {
		if (!arr[i]) {
    		arr.splice(i, 1);
			i--;
		}
	}
	return arr;
};

$.reportInit = function() {
	$('#reportType')
		.change(function() {
			$('#reportEditor').html('');
			if ($(this).val()) {
				$('#reportButtons').show();
				$.getJSON('tiki-ajax_services.php',{
					controller: 'report',
					action: 'load',
					type: $(this).val()
				}, function(data) {
					$('#reportEditor').reportBuilder({
						definition: data
					});
				});
			} else {
				$('#reportButtons').hide();
			}
		})
		.change();
	
	$('#reportPreview').click(function() {
		$('#report').tikiModal(tr('Loading...'));
		$.post('tiki-ajax_services.php', {
			controller: 'report',
			action: 'preview',
			value: $('#reportEditor').serializeArray(),
			type: $('#reportType').val()
		}, function(o) {
			var jS = $('#reportSheetPreview').getSheet();
			if (jS) {
				jS.openSheet(o);
			} else {
				$('#reportSheetPreview')
					.html($(o).attr('title', tr('Preview')))
					.sheet({
						buildSheet: true,
						editable: false
					});
			}
			
			$('#report').tikiModal();
		});
		
		return false;
	});
	
	$('#reportExportCSV').click(function() {
		$.download('tiki-ajax_services.php', {
			controller: 'report',
			action: 'exportcsv', 
			value: JSON.stringify($('#reportEditor').serializeArray()),
			type: $('#reportType').val()
		}, 'post');
		
		return false;
	});
	
	$('#reportWikiData').click(function() {
		$.post('tiki-ajax_services.php', {
			controller: 'report',
			action: 'wikidata',
			value: $('#reportEditor').serializeArray(),
			type: $('#reportType').val()
		}, function(o) {
			$('<pre />')
				.html('{REPORT()}\n' + o + '{REPORT}')
				.dialog({
					modal: true,
					title: tr('Wiki Data Output For REPORT Plugin') 
				})
				.flexibleSyntaxHighlighter({
					readOnly: true
				});
		});

		return false;
	});

	$('#reportEditor').submit(function() {
		$.post('tiki-ajax_services.php', {
			controller: 'report',
			action: 'wikidata',
			value: $('#reportEditor').serializeArray(),
			type: $('#reportType').val()
		}, function(o) {
			var index = $('#reportEditor').data('index');
			
			$.tikiModal(tr("Loading..."));
			
			$('.reportWikiPlugin[data-index="' + index + '"] [name="content"]').val(o);
			
			$('.reportWikiPlugin[data-index="' + index + '"]')
				.submit();
			
							
			$('#reports')
				.parent()
				.dialog('close');
		});
		return false;
	});
};
