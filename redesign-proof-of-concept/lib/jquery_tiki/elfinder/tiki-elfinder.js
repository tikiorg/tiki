/**
 * Tiki wrapper for elFinder
 *
 * (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project

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
	$(this).append($dialog).data('elFinderDialog', $dialog);

	options = $.extend({
		title : tr("Browse Files"),
		minWidth : 500,
		height : 520,
		width: 800,
		zIndex : 9999,
		modal: true,
		eventOrigin: this
	}, options);

	buttons[tr('Close')] = function () {
		$dialog
			.dialog('close')
			.dialog('destroy');
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
		},
		close: function () {
			$("body").data("eventOrigin", "");
			$(this).dialog('destroy');
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

	if (options.defaultGalleryId > 0) {
		options.rememberLastDir = false;
		if (!options.deepGallerySearch) {
			//elfoptions.ui = ['toolbar', 'path', 'stat'];
		}
	}

	delete options.defaultGalleryId;		// moved into customData
	delete options.deepGallerySearch;


	// turn off most elfinder commands as at this stage it will be read-only in tiki (tiki 10)
	var remainingCommands = elFinder.prototype._options.commands, idx;
	var disabled = ['mkfile', 'edit', 'extract', 'archive', 'resize'];
	// done 'rm', 'duplicate', 'rename', 'mkdir', 'upload', 'copy', 'cut', 'paste',
	$.each(disabled, function (i, cmd) {
		(idx = $.inArray(cmd, remainingCommands)) !== -1 && remainingCommands.splice(idx, 1);
	});
	return options;
}

