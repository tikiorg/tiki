// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

					// Clean the unsavedChangesInEditor markers before saving.
					// When page is saved, all editors will be committed and not just this one.
					if (typeof CKEDITOR === 'object') {
						for (var ed in CKEDITOR.instances) {
							if (CKEDITOR.instances.hasOwnProperty(ed)) {
								var edit = CKEDITOR.instances[ed];
								edit.resetDirty();
								var e = edit.element.$;
								$(e).removeClass('unsavedChangesInEditor');
							}
						}
					}

					var ret = myplugin.doAjaxSave(editor);
					setTimeout(function () { myplugin.closeEditor(editor); }, 1000);	// close editor

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

	closeEditor: function (editor) {
		var el = editor.element.$;
		$(el).blur();
	},


	doAjaxSave: function (editor) {

		var data = "", editor2, $el, i;					// for now send the whole page back for saving

		$("> *:not(.icon_edit_section):not(.editplugin)", "#page-data").each(function () {

			var removedAttrs = [], removedClasses = [];		// clean out & replace all the ckeditor attributes & classes
			var element = new CKEDITOR.dom.element($(this)[0]);
			editor2 = element.getEditor();
			if (editor !== editor2) {		// editor2 will be null for plugins etc
				$el = $(this);
			} else {
				$el = $(editor.element.$);
			}
			for (i = 0; i < $el[0].attributes.length; i++) {
				if (editor2 && $.inArray($el[0].attributes[i].name, ["class", "id", "rel"]) === -1) {
					removedAttrs.push($el[0].attributes[i]);
				}
			}
			for (i = 0; i < removedAttrs.length; i++) {
				$el.removeAttr(removedAttrs[i].name);
			}
			var classNames = ["cke_editable", "cke_editable_inline", "cke_contents_ui", "cke_show_borders", "cke_focus"];
			for (i = 0; i < classNames.length; i++) {
				if ($el.hasClass(classNames[i])) {
					$el.removeClass(classNames[i]);
					removedClasses.push(classNames[i]);
				}
			}

			var elData = $el[0].outerHTML.replace(/<a .*class=["']?editplugin["']?.*<\/a>/g, "");	// yuk, strip out the editplugin icons TODO better
			data += elData.replace("<p class=\"\"></p>", "");

			// put all the cke stuff back so the editors still work
			for (i = 0; i < removedAttrs.length; i++) {
				$el.attr(removedAttrs[i].name, removedAttrs[i].value);
			}
			for (i = 0; i < removedClasses.length; i++) {
				$el.addClass(removedClasses[i]);
			}
		});

		data = editor.dataProcessor.toDataFormat(data);

		if (this.ajaxSaveIsDirty && data != "ajax error") {
			this.changeIcon("loadingSmall.gif", editor);

			var referrer = editor.config.saveSelf;
			var myplugin = this;
			jQuery.ajax({
				url: $.service("edit", "inlinesave"),
				data: {
					referer: referrer,
					editor_id: editor.name,
					data: data,
					page: editor.config.autoSavePage
				},
				type: "POST",
				// good callback
				success: function (data) {

					// reset state
					myplugin.ajaxSaveIsDirty = false;
					myplugin.ajaxSaveCounter = 0;
					myplugin.ajaxSaveDraftSaved = true;

					for(var ed in CKEDITOR.instances ) {
						if (CKEDITOR.instances.hasOwnProperty(ed)) {
							CKEDITOR.instances[ed].resetDirty();
						}
					}
					// show
					myplugin.changeIcon("tick_animated.gif", editor);
					// clear anim
					setTimeout(function () {
						myplugin.changeIcon("ajaxSaveClean.gif", editor);
					}, 2000);

					return true;
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
