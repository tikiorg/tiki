/**
 * Tiki wrapper for elFinder
 *
 * (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project

 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 *
 * $Id$
 */

/**
 * Open a dialog with elFinder in it
 * @param element	unused?
 * @param options	object containing jquery-ui and elFinder dialog options
 * @return {Boolean}
 */

openElFinderDialog = function(element, options) {
	var $dialog = $('<div/>'), buttons = {};
	options = options ? options : {};
	$(document.body).append($dialog);
	$(window).data('elFinderDialog', $dialog);	// needed for select handler later

	options = $.extend({
		title : tr("Browse Files"),
		minWidth : 500,
		height : 520,
		width: 800,
		zIndex : 9999,
		modal: true,
		eventOrigin: this,
		uploadCallback: null
	}, options);

	buttons[tr('Close')] = function () {
		$dialog
			.dialog('close');
	};


	if (options.eventOrigin) {	// save it for later
		$("body").data("eventOrigin", options.eventOrigin);	// sadly adding data to the dialog kills elfinder :(
		delete options.eventOrigin;
	}

	var elfoptions = initElFinder(options);

	$dialog.dialog({
		title: options.title,
		minWidth: options.minWidth,
		height: options.height,
		width: options.width,
		buttons: buttons,
		modal: options.modal,
		zIndex: options.zIndex,
		open: function () {

			var $elf = $('<div class="elFinderDialog" />');
			$(this).append($elf);
			$elf.elfinder(elfoptions).elfinder('instance');
			if (options.uploadCallback) {
				$elf.elfinder('instance').bind("upload", options.uploadCallback);
			}
		},
		close: function () {
			$("body").data("eventOrigin", "");
			$(this).dialog('close')
				.dialog('destroy')
				.remove();
		}
	});

	return false;
};

/**
 * Set up elFinder for tiki use
 *
 * @param options {Object} Tiki ones: defaultGalleryId, deepGallerySearch & getFileCallback
 * 			also see https://github.com/Studio-42/elFinder/wiki/Client-configuration-options
 * @return {Object}
 */

function initElFinder(options) {

	options = $.extend({
		getFileCallback: null,
		defaultGalleryId: 0,
		deepGallerySearch: true,
		url: $.service('file_finder', 'finder'), // connector URL
		// lang: 'ru',								// language (TODO)
		customData:{
			defaultGalleryId:options.defaultGalleryId,
			deepGallerySearch:options.deepGallerySearch
		}
	}, options);

	var lang = jqueryTiki.language;
	if (lang && typeof elFinder.prototype.i18[lang] !== "undefined" && !options.lang) {
		if (lang == 'cn') {
			lang = 'zh_CN';
		} else if (lang == 'pt-br') {
			lang = 'pt_BR';
		}
		options.lang = lang;
	}

	if (options.defaultGalleryId > 0) {
		options.rememberLastDir = false;
		location.hash = "";					// seems to be a bug in elFinder as it sets the hash for the dir even if this is false FIXME upsteream...
		if (!options.deepGallerySearch) {
			//elfoptions.ui = ['toolbar', 'path', 'stat'];
		}
	}

	delete options.defaultGalleryId;		// moved into customData
	delete options.deepGallerySearch;


	// turn off some elfinder commands - not many left to do...
	var remainingCommands = elFinder.prototype._options.commands, idx;
	var disabled = ['mkfile', 'edit', 'archive', 'resize'];
	// done 'rm', 'duplicate', 'rename', 'mkdir', 'upload', 'copy', 'cut', 'paste', 'extract',
	$.each(disabled, function (i, cmd) {
		(idx = $.inArray(cmd, remainingCommands)) !== -1 && remainingCommands.splice(idx, 1);
	});
	return options;
}

/**
 * @class elFinder command "info" extended for tiki
 * based on https://github.com/Studio-42/elFinder/wiki/Adding-file-description-to-Properties-dialog
 * from commit 3867757aa36b4c351614e8931ab787580b25014c
 *
 * Display dialog with file properties.
 *
 * @author Dmitry (dio) Levashov, dio@std42.ru
 **/

elFinder.prototype.commands.info = function() {

	var m   = 'msg',
		fm  = this.fm,
		spclass = 'elfinder-info-spinner',
		msg = {
			calc     : fm.i18n('calc'),
			size     : fm.i18n('size'),
			unknown  : fm.i18n('unknown'),
			path     : fm.i18n('path'),
			aliasfor : fm.i18n('aliasfor'),
			modify   : fm.i18n('modify'),
			perms    : fm.i18n('perms'),
			locked   : fm.i18n('locked'),
			dim      : fm.i18n('dim'),
			kind     : fm.i18n('kind'),
			files    : fm.i18n('files'),
			folders  : fm.i18n('folders'),
			items    : fm.i18n('items'),
			yes      : fm.i18n('yes'),
			no       : fm.i18n('no'),
			link     : fm.i18n('link'),
			// start tiki
			fileId   : tr("fileId"),
			user     : tr("user"),
			desc     : tr("description"),
			hits     : tr("hits"),
			syntax   : tr("syntax")
			// end tiki
		};

	this.tpl = {
		main       : '<div class="ui-helper-clearfix elfinder-info-title"><span class="elfinder-cwd-icon {class} ui-corner-all"/>{title}</div><table class="elfinder-info-tb">{content}</table>',
		itemTitle  : '<strong>{name}</strong><span class="elfinder-info-kind">{kind}</span>',
		groupTitle : '<strong>{items}: {num}</strong>',
		row        : '<tr><td>{label} : </td><td>{value}</td></tr>',
		// start tiki
		spinner    : '<span>{text}</span> <span class="'+spclass+' '+spclass+'-'+'{id}"/>'
		// end tiki
	};

	this.alwaysEnabled = true;
	this.updateOnSelect = false;
	this.shortcuts = [{
		pattern     : 'ctrl+i'
	}];

	this.init = function() {
		$.each(msg, function(k, v) {
			msg[k] = fm.i18n(v);
		});
	};

	this.getstate = function() {
		return 0;
	};

	this.exec = function(hashes) {
		var files   = this.files(hashes);
		if (! files.length) {
			files   = this.files([ this.fm.cwd().hash ]);
		}
		var self    = this,
			fm      = this.fm,
			tpl     = this.tpl,
			row     = tpl.row,
			cnt     = files.length,
			content = [],
			view    = tpl.main,
			l       = '{label}',
			v       = '{value}',
			opts    = {
				title : this.title,
				width : 'auto',
				close : function() { $(this).elfinderdialog('destroy'); }
			},
			count = [],
			replSpinner = function(msg) { dialog.find('.'+spclass).parent().text(msg); },
			// start tiki
			replSpinnerById = function(msg, id) { dialog.find('.' + spclass + '-' + id).parent().html(msg); },
			// end tiki
			id = fm.namespace+'-info-'+$.map(files, function(f) { return f.hash }).join('-'),
			dialog = fm.getUI().find('#'+id),
			size, tmb, file, title, dcnt;

		if (!cnt) {
			return $.Deferred().reject();
		}

		if (dialog.length) {
			dialog.elfinderdialog('toTop');
			return $.Deferred().resolve();
		}

		if (cnt == 1) {
			file  = files[0];

			view  = view.replace('{class}', fm.mime2class(file.mime));
			title = tpl.itemTitle.replace('{name}', fm.escape(file.i18 || file.name)).replace('{kind}', fm.mime2kind(file));

			if (file.tmb) {
				tmb = fm.option('tmbUrl')+file.tmb;
			}

			if (!file.read) {
				size = msg.unknown;
			} else if (file.mime != 'directory' || file.alias) {
				size = fm.formatSize(file.size);
			} else {
				// start tiki
				/* adding spinner id to separate field updates */
				//size = tpl.spinner.replace('{text}', msg.calc);
				size = tpl.spinner.replace('{text}', msg.calc).replace('{id}', 'size');
				// end tiki
				count.push(file.hash);
			}

			content.push(row.replace(l, msg.size).replace(v, size));
			file.alias && content.push(row.replace(l, msg.aliasfor).replace(v, file.alias));
			content.push(row.replace(l, msg.path).replace(v, fm.escape(fm.path(file.hash, true))));
			// start tiki
			file.read && content.push(row.replace(l, msg.link).replace(v,  tpl.spinner.replace('{text}', msg.calc).replace('{id}', 'link')));
			// end tiki

			if (file.dim) { // old api
				content.push(row.replace(l, msg.dim).replace(v, file.dim));
			} else if (file.mime.indexOf('image') !== -1) {
				if (file.width && file.height) {
					content.push(row.replace(l, msg.dim).replace(v, file.width+'x'+file.height));
				} else {
					// start tiki
					content.push(row.replace(l, msg.dim).replace(v, tpl.spinner.replace('{text}', msg.calc).replace('{id}', 'dim')));
					// end tiki
					fm.request({
						data : {cmd : 'dim', target : file.hash},
						preventDefault : true
					})
					.fail(function() {
						// start tiki
						replSpinnerById(msg.unknown, 'dim');
						// end tiki
					})
					.done(function(data) {
						// start tiki
						replSpinnerById(data.dim || msg.unknown, 'dim');
						// end tiki
						if (data.dim) {
							var dim = data.dim.split('x');
							var rfile = fm.file(file.hash);
							rfile.width = dim[0];
							rfile.height = dim[1];
						}
					});
				}
			}


			content.push(row.replace(l, msg.modify).replace(v, fm.formatDate(file)));
			content.push(row.replace(l, msg.perms).replace(v, fm.formatPermissions(file)));
			content.push(row.replace(l, msg.locked).replace(v, file.locked ? msg.yes : msg.no));

			// start tiki
			var fileIdLabel = file.mime === 'directory' ? tr('Gallery ID') : tr('File ID');
			content.push(row.replace(l, fileIdLabel).replace(v, tpl.spinner.replace('{text}', msg.calc).replace('{id}', 'fileId')));
			content.push(row.replace(l, tr('User')).replace(v, tpl.spinner.replace('{text}', msg.calc).replace('{id}', 'user')));
			if (file.mime !== 'directory') {
				content.push(row.replace(l, tr('Hits')).replace(v , tpl.spinner.replace('{text}', msg.calc).replace('{id}', 'hits')));
				content.push(row.replace(l, tr('Wiki Syntax')).replace(v, tpl.spinner.replace('{text}', msg.calc).replace('{id}', 'syntax')));
			}
			var desc;
			if (file.write) {
				desc = '<textarea cols="40" rows="5" id="elfinder-fm-file-desc" class="ui-widget ui-widget-content" disabled="true" /><br><input type="button" id="elfinder-fm-file-desc-btn-save" value="' + fm.i18n('btnSave') + '" />';
			} else {
				desc = '<div id="elfinder-fm-file-desc"/>'
			}
			content.unshift(row.replace(l, tr('Description')).replace(v , desc));	// put desc at the top

			fm.request({
				data: {cmd: 'info', target: file.hash, content: ""},	// get all the info in one call
				preventDefault: true
			})
				.fail(function () {
					replSpinnerById(msg.unknown, 'info');
				})
				.done(function (data) {
					if (file.mime === 'directory') {
						replSpinnerById(data.info.galleryId || msg.unknown, 'fileId');
					} else {
						replSpinnerById(data.info.fileId || msg.unknown, 'fileId');
					}
					replSpinnerById(data.info.user || msg.unknown, 'user');
					replSpinnerById(data.info.link || msg.unknown, 'link');
					if (file.mime !== 'directory') {
						replSpinnerById(data.info.hits || msg.unknown, 'hits');
						replSpinnerById(data.info.wiki_syntax || msg.unknown, 'syntax');
					}
					var fieldDesc = dialog.find('#elfinder-fm-file-desc');
					fieldDesc
						.empty()
						.html(data.info.description || '')
						.prop("disabled", !file.write);
				});
			// end tiki
		} else {
			view  = view.replace('{class}', 'elfinder-cwd-icon-group');
			title = tpl.groupTitle.replace('{items}', msg.items).replace('{num}', cnt);
			dcnt  = $.map(files, function(f) { return f.mime == 'directory' ? 1 : null }).length;
			if (!dcnt) {
				size = 0;
				$.each(files, function(h, f) {
					var s = parseInt(f.size);

					if (s >= 0 && size >= 0) {
						size += s;
					} else {
						size = 'unknown';
					}
				});
				content.push(row.replace(l, msg.kind).replace(v, msg.files));
				content.push(row.replace(l, msg.size).replace(v, fm.formatSize(size)));
			} else {
				content.push(row.replace(l, msg.kind).replace(v, dcnt == cnt ? msg.folders : msg.folders+' '+dcnt+', '+msg.files+' '+(cnt-dcnt)));
				content.push(row.replace(l, msg.size).replace(v, tpl.spinner.replace('{text}', msg.calc)));
				count = $.map(files, function(f) { return f.hash });

			}
		}

		view = view.replace('{title}', title).replace('{content}', content.join(''));

		dialog = fm.dialog(view, opts);
		dialog.attr('id', id);

		// start tiki
		if (file.write) {
			var inputDesc = $('#elfinder-fm-file-desc', dialog);
			var btnSave = $('#elfinder-fm-file-desc-btn-save', dialog).button();

			inputDesc.keypress(function (e) {
				e.stopImmediatePropagation();
			}).keydown(function (e) {
				e.stopImmediatePropagation();
			});
			btnSave.click(function () {
				fm.lockfiles({files: [file.hash]});
				inputDesc.prop("disabled", true);
				fm.request({
					data: {cmd: 'info', target: file.hash, content: inputDesc.val()},
					notify: {type: 'desc', cnt: 1}
				})
				.always(function () {
						fm.unlockfiles({files: [file.hash]});
						inputDesc.prop("disabled", false);
				});
			});
		}
		// end tiki
		// load thumbnail
		if (tmb) {
			$('<img/>')
				.load(function() { dialog.find('.elfinder-cwd-icon').css('background', 'url("'+tmb+'") center center no-repeat'); })
				.attr('src', tmb);
		}

		// send request to count total size
		if (count.length) {
			fm.request({
					data : {cmd : 'size', targets : count},
					preventDefault : true
				})
				.fail(function() {
					replSpinner(msg.unknown);
				})
				.done(function(data) {
					var size = parseInt(data.size);
					replSpinner(size >= 0 ? fm.formatSize(size) : msg.unknown);
				});
		}

	}

};
