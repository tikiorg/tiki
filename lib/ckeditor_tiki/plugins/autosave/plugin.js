// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

CKEDITOR.plugins.add( 'autosave',
{
	// declare property to hold the state
	ajaxAutoSaveIsDirty: false,
	// declare a counter to give us a few keystrokes leeway
	ajaxAutoSaveCounter: 0,
	// declare a status flag so we know if a draft has been saved
	ajaxAutoSaveDraftSaved: false,
	// shortcut to my button
	button: null,
	editor: null,

	init: function( editor ) {

		var asplugin = this, command;	// for closure references later
		this.editor = editor;
	
		editor.ui.addButton( 'autosave', {
				label: 'Auto Save',
				command: 'autosave',
				icon: this.path + 'images/ajaxAutoSaveClean.gif'

			} );
		
		command = editor.addCommand( 'autosave', new CKEDITOR.command( editor ,
			{
				modes: { wysiwyg:1, source:1 },
				canUndo: false,
				// button clicked or timer
				exec: function(elem, editor, data) {
					asplugin.ajaxAutoSaveIsDirty = true;	// force
					return asplugin.doAjaxSave(editor);
				}
			} ));
		
		// preload toolbar loading image
		var tempNode = new Image();
		tempNode.src = this.path + "images/loadingSmall.gif";
		
		editor.element.$.form.onsubmit = function() {
			asplugin.onSave( editor );
		};
		
		//var plugs = this.plugin;
		editor.on( 'contentDom', function() {
			this.document.on('keydown', function(event) {
				// Do not capture CTRL hotkeys.
				if (!event.data.$.ctrlKey && !event.data.$.metaKey) {
					asplugin.onSelectionChange(editor);
				}
			});
			// Also chgeck for autosave changes after toolbar commands.
			editor.on( 'afterCommandExec', function(event) {asplugin.onSelectionChange(editor);} );

		});
	
		editor.on('blur', function(event) {
			asplugin.doAjaxSave(editor);
		});
	},	// end init

	doAjaxSave: function (editor) {
		editor.updateElement();			// workaround for a bug in Firefox where the textarea doesn't get updated properly
		var data = editor.getData();
		if (this.ajaxAutoSaveIsDirty && data != "ajax error") {
			this.changeIcon("loadingSmall.gif");
			
			var asplugin = this;
			jQuery.ajax({
				url: $.service("autosave", "save"),
				data: {
					referer: editor.config.autoSaveSelf,
					editor_id: editor.name,
					data: data
				},
				type: "POST",
				// good callback
				success: function(data) {
				
					// update AJAX preview if there
					if (parent.window.ajaxPreviewWindow && typeof parent.window.ajaxPreviewWindow.get_new_preview === 'function') {
						parent.window.ajaxPreviewWindow.get_new_preview();
					} else {
						ajax_preview( editor.name, editor.config.autoSaveSelf, true );
					}
					// reset state
					asplugin.ajaxAutoSaveIsDirty = false;
					asplugin.ajaxAutoSaveCounter = 0;
					asplugin.ajaxAutoSaveDraftSaved = true;
					// show
					asplugin.changeIcon("tick_animated.gif");
					// clear anim
					setTimeout(function() {
						asplugin.resetAjaxAutoSaveTool();
					}, 2000);
					
				},
				// bad callback - no good info in the params :(
				error: function(req, status, error) {
					asplugin.changeIcon("cross_animated.gif"); // just leave a cross there
				}
			});
		}
		return true;
	},
	
	onSave: function( editor ) {
		this.ajaxAutoSaveIsDirty = false;
		// remove draft when page saved
		if (parent && typeof parent.remove_save === 'function') {
			parent.remove_save(editor.name, editor.config.autoSaveSelf);
		}
		return true;
	},
	
	// what to do when the ckeditor content is changed
	onSelectionChange: function( editor ) {
		
		var asplugin;
		
		if (!this.ajaxAutoSaveIsDirty) {
			this.changeIcon("ajaxAutoSaveDirty.gif");
			
			this.ajaxAutoSaveCounter++;
			
			if (this.ajaxAutoSaveCounter > CKEDITOR.config.ajaxAutoSaveSensitivity) {
				if (!asplugin) {
					asplugin = this;
					setTimeout(function() {
						asplugin.doAjaxSave( editor );
						asplugin = null;
					}, CKEDITOR.config.ajaxAutoSaveRefreshTime * 1000);
					
					this.ajaxAutoSaveIsDirty = true;
				}
			}
		}
		return true;
	},
	
	getButton: function () {
		if (!this.button) {
			this.button = this.editor.getCommand("autosave").uiItems[0];
		}
		return this.button;
	},

	changeIcon: function( fileName ) {
		if (this.getButton()) {
			// use of jquery - must be a better "ck-way" of doing this
			var $img = jQuery("#" + this.button._.id + " span:first");
			$img.css("background-image", $img.css("background-image").replace(/[^\/]*\.gif/i, fileName));
		}
	},
	
	setMessage: function(errorMessage, errorData) {
		var message;
		
		message = errorMessage + (errorData ? ' ' + errorData : '');
		
		if (this.getButton()) {
			this.button.label = message; // doesn't seem to...
		}
	},
	
	resetAjaxAutoSaveTool: function() {
		this.changeIcon( "ajaxAutoSaveClean.gif" );
		if (this.getButton()) {
			this.button.label = "Auto Save";
		}
	}

	

});
