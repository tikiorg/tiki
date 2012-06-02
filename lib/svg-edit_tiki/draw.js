$.fn.drawFullscreen = function() {
	var win = $(window);
	var me = $(this);
	me.trigger('saveDraw');
	
	var fullscreen = $('#svg-fullscreen');
	
	if (fullscreen.length == 0) {
		me.data('origParent', me.parent());
		
		var menuHeight = $('#drawMenu').height();
		$('body').addClass('full_screen_body');
		$('body,html').scrollTop(0);
		
		fullscreen = $('<div id="svg-fullscreen" />')
			.html(me)
			.prependTo('body');
		
		var fullscreenIframe = fullscreen.find('iframe');
		
		win
			.resize(function() {
				fullscreen
					.height(win.height())
					.width(win.width());
					
				fullscreenIframe.height((fullscreen.height() - menuHeight));
			})
			.resize() //we do it double here to make sure it is all resized right
			.resize();
			
	} else {
		me.data('origParent').append(me);
		win.unbind('resize');
		fullscreen.remove();
		$('body').removeClass('full_screen_body');
	}
	
	return this;
};

$.fn.replaceDraw = function(o) {
	var me = $(this);
	if (o.error) {
		alert('error ' + o.error);
	} else {
		$.modal(tr("Saving..."));
		$.post('tiki-edit_draw.php', {
			galleryId: o.galleryId,
			fileId: o.fileId,
			name: o.name,
			data: o.data
		}, function(fileId) {
			fileId = (fileId ? fileId : o.fileId);
			o.fileId = fileId;
			
			me.data('fileId', o.fileId);
			me.data('galleryId', o.galleryId);
			me.data('name', o.name);
			
			$.modal(tr("Saved file id") + o.fileId + '!');
			
			if ($.wikiTrackingDraw) {
				$.wikiTrackingDraw.params.id = o.fileId;
				$.modal(tr("Updating Wiki Page"));
				$.post('tiki-wikiplugin_edit.php', $.wikiTrackingDraw, function() {
					me.trigger('savedDraw', o);
					$.modal();
				});
			} else {
				me.trigger('savedDraw', o);
				$.modal();
			}
		});
	}
	
	return this;
};

$.fn.saveDraw = function() {
	var me = $(this);
	me.data('canvas').getSvgString()(function(data, error) {
		me.replaceDraw({
			data: data,
			error: error,
			fileId: me.data('fileId'),
			galleryId: me.data('galleryId'),
			name: me.data('name')
		})
	});

	try {
		me.data('window').svgCanvas.undoMgr.resetUndoStack();
	} catch(e) {}

	return this;
};

$.fn.saveAndBackDraw = function() {
	$(this)
		.saveDraw()
		.one('savedDraw', function() {
			window.history.back();
		});
};

$.fn.renameDraw = function() {
	var me = $(this);
	var name = me.data('name');
	var newName = prompt(tr("Enter new name"), name);
	if (newName) {
		if (newName != name) {
			name = newName;
			me.data('name', name);
			me.trigger('renamedDraw', name);
			
			me.saveDraw();
		}
	}
	
	return this;
};

$.drawInstance = 0;

$.fn.loadDraw = function(o) {
	var me = $(this);

	//prevent from happeneing over and over again
	if (me.data('drawLoaded')) return me;

	me.data('drawLoaded', true);

	var drawFrame = $('<iframe src="lib/svg-edit/svg-editor.html" id="svgedit"></iframe>')
		.appendTo(me)
		.load(function() {
			me
				.data('drawInstance', $.drawInstance)
				.data('fileId', (o.fileId ? o.fileId : 0))
				.data('galleryId', (o.galleryId ? o.galleryId : 0))
				.data('name', (o.name ? o.name : ''))
				.data('doc', $(drawFrame[0].contentDocument ? drawFrame[0].contentDocument : drawFrame[0].contentWindow.document))
				.data('canvas', new embedded_svg_edit(drawFrame[0]))
				.data('window', drawFrame[0].contentWindow);

			// Hide main button, as we will be controlling new/load/save etc from the host document
			var mainButton = me.data('doc').find('#main_button').hide();
			
			if (o.data) {
				me.data('canvas').setSvgString(o.data);
			}
			
			me.data('window').onbeforeunload = function() {};


			window.onbeforeunload = function() {
				try {
					if ( me.data('window').svgCanvas.undoMgr.getUndoStackSize() > 1 ) {
						return tr("There are unsaved changes, leave page?");
					}
				} catch (e) {}
			};
			
			drawFrame.height($(window).height() * 0.9);
			
			$.drawInstance++;
			
			me.trigger('loadedDraw');

			setTimeout(function() {
				if (o.removeButtons) {
					if (!$.isArray(o.removeButtons)) o.removeButtons = o.removeButtons.split(',');
					for(id in o.removeButtons) {
						me.data('doc').find('#' + o.removeButtons[id]).wrap('<div style="display:none;"/>');
					}
				}
			}, 1);
		});
	return me;
};

$.fn.drawOver = function(o) {
	var me = $(this);
	
	o = $.extend(o,{});
	
	var draw = $('<div class="drawOver" />')
		.insertAfter(me)
		.loadDraw(o)
		.bind('loadedDraw', function() {
			//we get the doc, because it is in an iframe, it is private and IT MUST be on the same domain
			var doc = draw.data('doc');
			
			var bg = doc.find('#canvasBackground')
				.fadeTo(0.01, 0.01);
			var root = doc.find('#svgroot');
			
			me
				.css('position', 'relative')
				.css('z-index', 0)
				//better size detection and positioning here
				.css('left', (parseInt(1920 / 2) - 320) + 'px')
				.css('top', (parseInt(1440 / 2) - 240) + 'px')
				.insertBefore(root);
		});
		
	return this;
};