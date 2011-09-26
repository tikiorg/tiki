$.fn.drawFullscreen = function() {
	var win = $(window);
	var me = $(this);
	
	me.trigger('saveDraw');
	
	var fullscreen = $('#fullscreen');
	var menuHeight = $('#svg-menu').height();
	
	if (fullscreen.length == 0) {
		$('body').addClass('full_screen_body');
		fullscreen = $('<div />').attr('id', 'fullscreen')
			.html(me.find('#tiki_draw_editor'))
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
		me.append(fullscreen.find('#tiki_draw_editor'));
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
		me.modal(tr("Saving..."));
		alert(o.fileId);
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
			
			me.modal(tr("Saved file id") + o.fileId + '!');
			
			if ($.wikiTrackingDraw) {
				$.wikiTrackingDraw['params[id]'] = o.fileId;
				
				me.modal(tr("Updating Wiki Page"));
				$.post('tiki-wikiplugin_edit.php', $.wikiTrackingDraw, function() {
					me.modal();
				});
			} else {
				me.modal();
			}
			
			me.trigger('savedDraw', o);
		});
	}
	
	return this;
};

$.fn.saveDraw = function() {
	var me = $(this);
	var I = me.data('drawInstance');
	
	$.svgCanvas[I].getSvgString()(function(data, error) {
		me.replaceDraw({
			data: data,
			error: error,
			fileId: me.data('fileId'),
			galleryId: me.data('galleryId'),
			name: me.data('name')
		})
	});
	
	$.svgWindow[I].svgCanvas.undoMgr.resetUndoStack();
	
	return this;
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
$.svgCanvas = [];
$.svgWindow = [];

$.fn.loadDraw = function(o) {
	var me = $(this);
	return me.load(function() {
		if (!me[0]) return;
		
		me.data('drawInstance', $.drawInstance);
		me.data('fileId', o.fileId);
		me.data('galleryId', o.galleryId);
		me.data('name', o.name);
		
		$.svgCanvas[$.drawInstance] = new embedded_svg_edit(me[0]);
		$.svgWindow[$.drawInstance] = me[0].contentWindow;
		
		// Hide main button, as we will be controlling new/load/save etc from the host document
		var mainButton = $(
			me[0].contentDocument ? 
				me[0].contentDocument : 
				me[0].contentWindow.document
		)
			.find('#main_button').hide();
		
		if (o.data) {
			$.svgCanvas[$.drawInstance].setSvgString(o.data);
		}
		
		$.svgWindow[$.drawInstance].onbeforeunload = function() {};
		
		window.onbeforeunload = function() {
			if ( $.svgWindow[$.drawInstance].svgCanvas.undoMgr.getUndoStackSize() > 1 ) {
				return tr("There are unsaved changes, leave page?");
			}
		};
		
		me.height($(window).height() * 0.9);
		
		$.drawInstance++;
		
		return this;
	});
};