// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

CKEDITOR.plugins.add('inlinecancel',
{
	init: function (editor) {

		var asplugin = this, command; // for closure references later
		this.editor = editor;

		editor.ui.addButton('inlinecancel', {
			label: 'Cancel',
			command: 'inlinecancel',
			icon: this.path + 'images/cross.png'

		});

		command = editor.addCommand('inlinecancel', new CKEDITOR.command(editor,
		{
			modes: { wysiwyg: 1, source: 1 },
			// button clicked or timer
			exec: function (elem, editor, data) {
				// Close the editable area and restore original contents
				var el = editor.element.$;
				el.blur();
				$(el).html($(el).data("inline_original"));
				editor.resetDirty();

				$(el).removeClass('unsavedChangesInEditor');
			}
		}));

	}
});
