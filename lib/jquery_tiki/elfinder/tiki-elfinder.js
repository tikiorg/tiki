/**
 * Tiki wrapper for elFinder
 *
 * (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project

 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 *
 * $Id: $
 */


openElFinderDialog = function(element, options) {
	var $dialog = $('<div/>'), buttons = {};
	options = options ? options : {};
	$(this).append($dialog).data('elFinderDialog', $dialog);

	options = $.extend({
		title : tr("Browse Files"),
		minWidth : 500,
		height : 600,
		width: 800,
		zIndex : 9999,
		modal: true

	}, options);

	buttons[tr('Close')] = function () {
		$dialog
			.dialog('close')
			.dialog('destroy');
	};

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
			$elf.elfinder({
					url : 'tiki-file-finder'	// connector URL (TODO non-sefurl)
					// lang: 'ru'				// language (TODO)
			}).elfinder('instance');
		},
		close: function () {
			$(this).dialog('destroy');
		}
	});

	return false;
};
