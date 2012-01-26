/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license

External #1 for Tiki 6+ TikiLink
*/

CKEDITOR.plugins.add( 'tikilink',
{
	requires : [ 'dialog' ],
	init : function( editor )
	{
		//var command = editor.addCommand( 'tikilink', new CKEDITOR.dialogCommand( 'tikilink' ) );
		var command = editor.addCommand( 'tikilink', new CKEDITOR.command( editor , {
			modes: { wysiwyg:1, source:1 },
			exec: function(elem, editor, data) {if (!confirm("cancel=debug")){debugger;}},
			canUndo: false
		} ));

		editor.ui.addButton( 'tikilink',
			{
				label : 'TikiLink',
				command : 'tikilink',
				icon: editor.config._TikiRoot + 'pics/icons/page_link.png'
			});

		// Add the CSS styles for anchor placeholders.
		editor.addCss(
			'img.cke_tikilink {' +
				'background-image: url("' + editor.config._TikiRoot + 'pics/icons/page_link.png");' +
				'background-position: center center;' +
				'background-repeat: no-repeat;' +
				'border: 1px solid #a9a9a9;' +
				'width: 18px !important;' +
				'height: 18px !important;' +
			'}\n' +
			'a.cke_tikilink {' +
				'background-image: url("' + editor.config._TikiRoot + 'pics/icons/page_link.png");' +
				'background-position: 0 center;' +
				'background-repeat: no-repeat;' +
				'border: 1px solid #a9a9a9;' +
				'padding-left: 18px;' +
			'}'
	   	);

		CKEDITOR.dialog.add( 'tikilink', this.path + 'dialogs/tikilink.js' );
	}
});
