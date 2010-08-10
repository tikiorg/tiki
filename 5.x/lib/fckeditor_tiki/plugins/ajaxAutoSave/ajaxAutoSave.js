/*
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * File Name: ajaxAutoSave.js
 * 	ajaxAutoSave Object.
 * 
 * File Authors:
 *              Mike Tonks (http://greenmap.sourceforge.net/fck_autosave.html)
 * 		(adapted from ajaxPost by)
 * 		Paul Moers (http://www.saulmade.nl, http://www.saulmade.nl/FCKeditor/FCKPlugins.php)
*/


// AxpObject constructor
var AxpObject = function (editorInstance)
{
	this.editorInstance	= editorInstance;
	this.FCKConfig		= editorInstance.Config;
	this.FCKLang		= editorInstance.EditorWindow.parent.FCKLang;

	this.IsDirty 		= false;
};


// initialize
AxpObject.prototype.initialize = function ()
{
	parentObject = this;

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
	requestObject.onreadystatechange = this.onReadyStateChange;
};


// post
AxpObject.prototype.post = function ()
{
	// set up the requestObject
	this.initialize();

	// make request
	requestObject.open('POST', this.FCKConfig.ajaxAutoSaveTargetUrl, true);
	requestObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	requestObject.send('script=' + this.FCKConfig.autoSaveSelf + '&editor_id='+this.editorInstance.Name+'&data=' + encodeURIComponent(this.editorInstance.GetXHTML()));
	
	// update AJAX preview if there
	
	if (parent.window.ajaxPreviewWindow && typeof parent.window.ajaxPreviewWindow.get_new_preview == 'function') {
		parent.window.ajaxPreviewWindow.get_new_preview();
	}
};


// the readystatechange event
AxpObject.prototype.onReadyStateChange = function ()
{
	if (requestObject.readyState == 4)
	{
		if (requestObject.status == 200 && requestObject.responseXML)
		{
			// error node available?
			var errorNode = requestObject.responseXML.getElementsByTagName('error')[0];
			var resultNode = requestObject.responseXML.getElementsByTagName('result')[0];
			if (errorNode)
			{
				parentObject.feedback(errorNode.attributes.getNamedItem('errorNumber').value, errorNode.attributes.getNamedItem('errorData').value);
			}
			// success
			else if (resultNode)
			{
				parentObject.feedback(0);
			}
			else
			{
				parentObject.feedback(104, parentObject.FCKLang.ajaxAutoSaveRequestedURL + ': ' + parentObject.FCKConfig.ajaxAutoSaveTargetUrl + '<br />' + parentObject.FCKLang.ajaxAutoSaveResponseText + ':<br />' + requestObject.responseText);
			}
		}
		else
		{
			parentObject.feedback(105, requestObject.statusText + ' (' + requestObject.status + ')');
		}
	}
};


// feedback
AxpObject.prototype.feedback = function (errorNumber, errorData)
{
	// set toolbar icon and re-enable editor
	if (this.toolbarButtonIcon)
	{
		if (parseInt(errorNumber, 10) > 0)
		{
			this.toolbarButtonIcon.src = this.toolbarButtonIcon.src.replace(/[^\/]*$/, 'cross_animated.gif');
		}
		else
		{
			this.toolbarButtonIcon.src = this.toolbarButtonIcon.src.replace(/[^\/]*$/, 'tick_animated.gif');
		}
	}

	switch (parseInt(errorNumber, 10))
	{
		case 0 :		var now = new Date();
					var hours = now.getHours();
					var mins = now.getMinutes();
					if (hours < 11) { hours = "0" + hours; }
					if (mins < 11) { mins = "0" + mins; }
						this.setMessage(this.FCKLang.ajaxAutoSaveSaveCompleted + " " + hours + ":" + mins );
						this.ResetIsDirty();
						if (this.FCKConfig.showDialog)
						{
							this.getDialogCancelButton(parent.document.getElementById('btnOk').parentNode).value = this.FCKLang.DlgBtnOK;
						}
						break;
		case 101 :	this.setMessage(this.FCKLang.ajaxAutoSaveNoContentReceived, errorData);
						break;
		case 102 :	this.setMessage(this.FCKLang.ajaxAutoSaveDBConnectError, errorData);
						break;
		case 103 :	this.setMessage(this.FCKLang.ajaxAutoSaveQueryError, errorData);
						break;
		case 104 :	this.setMessage(this.FCKLang.ajaxAutoSaveErrMssgBadXMLResponse, errorData);
						break;
		case 105 :	this.setMessage(this.FCKLang.ajaxAutoSaveErrMssgXMLRequestError, errorData);
						break;
		default :	this.setMessage(this.FCKLang.ajaxAutoSaveErrMssgDefault + ' ' + errorNumber, errorData);
						break;
	}

	if (this.toolbarButtonIcon)
	{
		if (parseInt(errorNumber, 10) > 0)
		{
			setTimeout(this.resetToolbarButton, 12000);
		}
		else
		{
			setTimeout(this.resetToolbarButton, 5000);
		}
	}
};


// set message
AxpObject.prototype.setMessage = function (errorMessage, errorData)
{
	var message;

	message = errorMessage + (errorData ? ' ' + errorData : '');
	if (this.toolbarButtonIcon)
	{
		this.toolbarButtonIcon.title = this.toolbarButtonIcon.alt = message;
	}
	else
	{
		alert(message);
	}
};


// reset the toolbar button
AxpObject.prototype.resetToolbarButton = function ()
{
	// Check if form is dirty before doing reset

	if (!FCK_ajaxAutoSaveIsDirty) {

		this.toolbarButtonIcon.src = this.toolbarButtonIcon.src.replace(/[^\/]*$/, 'ajaxAutoSaveClean.gif');
		//this.toolbarButtonIcon.title = this.toolbarButtonIcon.alt = this.FCKLang.ajaxAutoSaveButtonTitle;
	}

	FCK_ajaxAutoSaveDraftSaved = true;
};

// get the cancel button
AxpObject.prototype.getDialogCancelButton = function (container)
{
	for (i = 0; i < container.childNodes.length; i++)
	{
		var childNode = container.childNodes[i];
		if (childNode.getAttribute && childNode.getAttribute("fcklang") == "DlgBtnCancel")
		{
			return childNode;
		}
	}
};

AxpObject.prototype.ResetIsDirty = function ()
{
	FCK_ajaxAutoSaveIsDirty = false;
};

AxpObject.prototype.setIsDirty = function ()
{
	if (!this.IsDirty) {
		this.IsDirty = true;

		this.toolbarButtonIcon.src = this.toolbarButtonIcon.src.replace(/[^\/]*$/, 'ajaxAutoSaveDirty.gif');
	}
};


