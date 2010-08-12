/*
 * Licensed under the terms of the GNU Lesser General Public License:
 *   http://www.opensource.org/licenses/lgpl-license.php
 * 
 * File Name: plugin.js
 *   Plugin to post the editor's content to the server through AJAX 
 * 
 * File Authors:
 *   Mike Tonks (http://greenmap.sourceforge.net/fck_autosave.html)
 *              (adapted from ajaxPost by)
 *              Paul Moers (http://www.saulmade.nl, http://www.saulmade.nl/FCKeditor/FCKPlugins.php)
 *   
 *   sept_7 for tiki 4?/fckeditor ????
 *   
 *   jonnyb for Tiki 6/ckeditor aug 2010
 *   added (modified) contents of ajaxAutoSave.js to this file
 *  
 * */

CKEDITOR.plugins.add( 'ajaxAutoSave',
{
	init : function( editor )
	{
		this.button = {
				label : 'Auto Save',
				command : 'ajaxAutoSave',
				icon: this.path + 'images/ajaxAutoSaveClean.gif'

			};
		editor.ui.addButton( 'ajaxAutoSave', this.button );
		
		this.command = editor.addCommand( 'ajaxAutoSave', new CKEDITOR.command( editor ,
			{
				modes: { wysiwyg:1, source:1 },
				exec: function(elem, editor, data) {
					//if (!confirm("cancel=debug")){debugger;}
					
					editor.plugins.ajaxAutoSave.autoSaveManager.exec();
				},
				canUndo: false
			} ));

		this.autoSaveManager = new AutoSaveManager( editor, this );
	}
});

// AutoSaveManager constructor
var AutoSaveManager = function(an_editor, a_plugin) {
	// references
	this.editor = an_editor;
	this.plugin = a_plugin;
		
	// declare a global variable to hold the state
	this.ajaxAutoSaveIsDirty = false;
	
	// declare a counter to give us a few keystrokes leeway
	this.ajaxAutoSaveCounter = 0;
	
	// declare a status flag so we know if a draft has been saved
	this.ajaxAutoSaveDraftSaved = false;
	
	// preload toolbar loading image
	var tempNode = new Image();
	tempNode.src = this.plugin.path + "images/loadingSmall.gif";
	
	// instantiate ajaxAutoSave Object
	this.ajaxAutoSaveObject = new AxpObject(this.editor);
	
	this.editor.element.$.form.onsubmit = this.onSave;
	
	// Registering change on every document recreation.(#3844)
	this.editor.on('selectionChange', function(event) {
		
	});
	
	var plugs = this.plugin;
	this.editor.on( 'contentDom', function() {
		this.document.on('keydown', function(event) {
			// Do not capture CTRL hotkeys.
			if (!event.data.$.ctrlKey && !event.data.$.metaKey) {
				plugs.autoSaveManager.onSelectionChange(this);
			}
		});
	});

	this.editor.on('blur', function(event) {
		this.plugins.ajaxAutoSave.autoSaveManager.exec();
	});
};

AutoSaveManager.prototype = {

	init: function () {
		this.plugin.button = this.editor.getCommand("ajaxAutoSave").uiItems[0];
	},
	// what do we do when the button is clicked
	exec: function() {
		
		this.init();
		this.changeIcon( "loadingSmall.gif" );
		
		// save
		this.ajaxAutoSaveObject.post();
		
		// reset state
		this.ajaxAutoSaveIsDirty = false;
		this.ajaxAutoSaveCounter = 0;
		this.ajaxAutoSaveDraftSaved = true;
		return true;
	},
	
	onSave: function() {
		this.ajaxAutoSaveIsDirty = false;
		// added by jonnyb for tiki 4 - remove draft when page saved
		//var n = ajaxAutoSaveObject.editorInstance.Name;
		if (parent && typeof parent.xajax_remove_save === 'function') {
			parent.xajax_remove_save(this.editor.name, this.editor.config.autoSaveSelf);
		}
		return true;
		
	},
	
	// what to do when the fckeditor content is changed
	onSelectionChange: function() {
		
		var asm;
		this.init();
		
		if (!this.ajaxAutoSaveIsDirty) {
			this.changeIcon("ajaxAutoSaveDirty.gif");
			//this.plugin.button.label = FCKLang.ajaxAutoSaveButtonTitle;
			
			this.ajaxAutoSaveCounter++;
			
			if (this.ajaxAutoSaveCounter > CKEDITOR.config.ajaxAutoSaveSensitivity) {
				if (!asm) {
					asm = this;
					setTimeout(function() {
						asm.exec();
						asm = null;
					}, CKEDITOR.config.ajaxAutoSaveRefreshTime * 1000);
					
					this.ajaxAutoSaveIsDirty = true;
				}
			}
		}
		return true;
	},
	
	changeIcon: function( fileName ) {
		// use of jquery - must be a better ck-way of doing this
		var $img = jQuery("#" + this.plugin.button._.id + " span:first");
		$img.css( "background-image", $img.css( "background-image" ).replace(/[^\/\)]*\)$/, fileName + ")"));
	}
	
};

//
//// manage the plugins' button behavior
//AutoSaveManager.prototype.getState = function ()
//{
//	return FCK_TRISTATE_OFF;
//};


// previously was ajaxAutoSave.js separate file - now combined

/*
 * Licensed under the terms of the GNU Lesser General Public License:
 * http://www.opensource.org/licenses/lgpl-license.php
 * 
 * File Name: ajaxAutoSave.js
 * ajaxAutoSave Object.
 * 
 * File Authors:
 *  Mike Tonks (http://greenmap.sourceforge.net/fck_autosave.html)
 * 	(adapted from ajaxPost by)
 * 	Paul Moers (http://www.saulmade.nl, http://www.saulmade.nl/FCKeditor/FCKPlugins.php)
 * 
 *  Updated for ckeditor/tiki6 jonnyb 2010
*/


// AxpObject constructor
var AxpObject = function (editorInstance) {
	
	this.editorInstance	= editorInstance;
	this.isDirty = false;

//	if (editorInstance.EditorWindow) {
//		this.FCKLang	= editorInstance.EditorWindow.parent.FCKLang;
//	} else {
//		this.FCKLang	= "";
//	}

};


// initialize
AxpObject.prototype.initialize = function () {
	
	this.asManager = this.editorInstance.plugins.ajaxAutoSave.autoSaveManager;

	// create requestObject
	if (window.XMLHttpRequest) // Mozilla, Safari, IE7, ...
	{
		requestObject = new XMLHttpRequest();
	}
	else if (window.ActiveXObject) // IE
	{
		requestObject = new ActiveXObject('MsXml2.XmlHttp');
	}
	this.requestObject = requestObject;

	// set function to do on completion of the request
	this.requestObject.onreadystatechange = this.onReadyStateChange;
	this.requestObject.parentObject = this;
};


// post
AxpObject.prototype.post = function () {
	// set up the requestObject
	this.initialize();

	// make request
	this.requestObject.open('POST', CKEDITOR.config.ajaxAutoSaveTargetUrl, true);
	this.requestObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	this.requestObject.send('script=' + this.editorInstance.config.autoSaveSelf + '&editor_id=' + this.editorInstance.name + '&data=' + encodeURIComponent(this.editorInstance.getData()));

	// update AJAX preview if there

	if (parent.window.ajaxPreviewWindow && typeof parent.window.ajaxPreviewWindow.get_new_preview === 'function') {
		parent.window.ajaxPreviewWindow.get_new_preview();
	}
};


// the readystatechange event
AxpObject.prototype.onReadyStateChange = function( event ) {
	var requestObject = event.target;
	
	if (requestObject.readyState === 4) {
		if (requestObject.status === 200 && requestObject.responseXML) {
			// error node available?
			var errorNode = requestObject.responseXML.getElementsByTagName('error')[0];
			var resultNode = requestObject.responseXML.getElementsByTagName('result')[0];
			if (errorNode) {
				this.parentObject.feedback(errorNode.attributes.getNamedItem('errorNumber').value, errorNode.attributes.getNamedItem('errorData').value);
			}   // success
			else if (resultNode) {
				this.parentObject.feedback(0);
			} else {
				this.parentObject.feedback(104, 'ajaxAutoSaveRequestedURL: ' + CKEDITOR.config.ajaxAutoSaveTargetUrl + '<br />' + 'ajaxAutoSaveResponseText:<br />' + this.requestObject.responseText);
			}
		} else {
			this.parentObject.feedback(105, this.requestObject.statusText + ' (' + this.requestObject.status + ')');
		}
	}
};

// feedback
AxpObject.prototype.feedback = function (errorNumber, errorData)
{
// set toolbar icon and re-enable editor
	if (this.asManager)
	{
		if (parseInt(errorNumber, 10) > 0)
		{
			this.asManager.changeIcon( "cross_animated.gif" );
		}
		else
		{
			this.asManager.changeIcon( "tick_animated.gif" );
		}
	}

	switch (parseInt(errorNumber, 10))
	{
		case 0:
			var now = new Date();
			var hours = now.getHours();
			var mins = now.getMinutes();
			if (hours < 11) {
				hours = "0" + hours;
			}
			if (mins < 11) {
				mins = "0" + mins;
			}
			this.setMessage("ajaxAutoSaveSaveCompleted  " + hours + ":" + mins);
			break;
		case 101:
			this.setMessage("ajaxAutoSaveNoContentReceived", errorData);
			break;
		case 102:
			this.setMessage("ajaxAutoSaveDBConnectError", errorData);
			break;
		case 103:
			this.setMessage("ajaxAutoSaveQueryError", errorData);
			break;
		case 104:
			this.setMessage("ajaxAutoSaveErrMssgBadXMLResponse", errorData);
			break;
		case 105:
			this.setMessage("ajaxAutoSaveErrMssgXMLRequestError", errorData);
			break;
		default: 
			this.setMessage("ajaxAutoSaveErrMssgDefault" + ' ' + errorNumber, errorData);
			break;
	}

	var asm = this.asManager;
	var delay = (parseInt(errorNumber, 10) > 0) ? 8000 : 2000;
	setTimeout( function() {
		if (asm) {
			asm.ajaxAutoSaveObject.resetAjaxAutoSaveTool();
		}
	}, delay);
};

// set message
AxpObject.prototype.setMessage = function(errorMessage, errorData) {
	var message;
	
	message = errorMessage + (errorData ? ' ' + errorData : '');
	if (this.asManager) {
		this.asManager.plugin.button.label = message;
	} else {
		//alert(message);
		window.status = message;
	}
};

AxpObject.prototype.resetAjaxAutoSaveTool = function() {
	this.asManager.changeIcon( "ajaxAutoSaveClean.gif" );
	this.asManager.plugin.button.label = "Auto Save";
};
