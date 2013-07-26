// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

CKEDITOR.plugins.add('inlinesave',
{
	// declare property to hold the state
	ajaxSaveIsDirty: false,
	// declare a counter to give us a few keystrokes leeway
	ajaxSaveCounter: 0,
	// declare a status flag so we know if a draft has been saved
	ajaxSaveDraftSaved: false,
	// shortcut to my button

	init: function (editor) {

		var myplugin = this; // for closure references later
		this.editor = editor;

		editor.ui.addButton('inlinesave', {
			label: 'Save',
			command: 'inlinesave',
			icon: this.path + 'images/ajaxSaveClean.gif'

		});

		var command = editor.addCommand('inlinesave', new CKEDITOR.command(editor,
			{
				modes: { wysiwyg: 1, source: 1 },
				canUndo: false,
				// button clicked or timer
				exec: function (elem, editor, data) {
					myplugin.ajaxSaveIsDirty = true; // force
					var ret = myplugin.doAjaxSave(editor);
					editor.element.$.blur();	// close editor
					return  ret;
				}
			}));

		editor.on('page-data', function () {
			this.document.on('keydown', function (event) {
				// Do not capture CTRL hotkeys.
				if (!event.data.$.ctrlKey && !event.data.$.metaKey) {
					myplugin.onSelectionChange(editor);
				}
			});
			// Also check for save changes after toolbar commands.
			editor.on('afterCommandExec', function (event) { myplugin.onSelectionChange(editor); });

		});
		/*
		editor.on('blur', function (event) {
			asplugin.doAjaxSave(editor);
		});
		*/
	}, // end init

	doAjaxSave: function (editor) {
		var data = "", editor2;		// for now send the whole page back for saving
		$("#page-data > .cke_editable:not(.icon_edit_section)").each(function () {
			var element = new CKEDITOR.dom.element($(this)[0]);
			editor2 = element.getEditor();
			if (editor !== editor2) {
				data += editor2.element.$.outerHTML;
			} else {
				data += editor.element.$.outerHTML;
			}
		});


		if (this.ajaxSaveIsDirty && data != "ajax error") {
			this.changeIcon("loadingSmall.gif", editor);

			var referrer = '';
			if (editor.config.saveSelf != null) {
				referrer = editor.config.saveSelf;
			}
			var myplugin = this;
			jQuery.ajax({
				url: CKEDITOR.config.ajaxSaveTargetUrl,
				data: 'command=inline_save&referer=' + referrer + '&editor_id=' + editor.name + '&data=' + tiki_encodeURIComponent(data),
				type: "POST",
				// good callback
				success: function (data) {

					// reset state
					myplugin.ajaxSaveIsDirty = false;
					myplugin.ajaxSaveCounter = 0;
					myplugin.ajaxSaveDraftSaved = true;
					// show
					myplugin.changeIcon("tick_animated.gif", editor);
					// clear anim
					setTimeout(function () {
						myplugin.changeIcon("ajaxSaveClean.gif", editor);
					}, 2000);

				},
				// bad callback - no good info in the params :(
				error: function (req, status, error) {
					myplugin.changeIcon("cross_animated.gif", editor); // just leave a cross there
				}
			});
		}
		return true;
	},

	onSave: function (editor) {
		this.ajaxSaveIsDirty = false;
		// remove draft when page saved
		/*
		if (parent && typeof parent.remove_save === 'function') {
			parent.remove_save(editor.name, editor.config.autoSaveSelf);
		}
		*/
		return true;
	},

	// what to do when the ckeditor content is changed
	onSelectionChange: function (editor) {

		var asplugin;

		if (!this.ajaxSaveIsDirty) {
			this.changeIcon("ajaxSaveDirty.gif");
			this.ajaxSaveIsDirty = true;
		}
		return true;
	},

	changeIcon: function (fileName, editor) {
		var button = editor.getCommand("inlinesave").uiItems[0];
		if (button) {
			// use of jquery - must be a better "ck-way" of doing this
			var $img = $("#" + button._.id + " span:first");
			$img.css("background-image", $img.css("background-image").replace(/[^\/]*\.gif/i, fileName));
		}
	}

});
