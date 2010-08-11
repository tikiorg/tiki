/*
 * FCKeditor - The text editor for Internet - http://www.fckeditor.net
 * Copyright (C) 2003-2007 Frederico Caldeira Knabben
 * Copyright (C) 2008-2009 Stéphane Casset for the Tikiwiki Team
 * Adapted for ckeditor by jonnyb for tiki
 *
 * == BEGIN LICENSE ==
 *
 * Licensed under the terms of any of the following licenses at your
 * choice:
 *
 *  - GNU General Public License Version 2 or later (the "GPL")
 *    http://www.gnu.org/licenses/gpl.html
 *
 *  - GNU Lesser General Public License Version 2.1 or later (the "LGPL")
 *    http://www.gnu.org/licenses/lgpl.html
 *
 *  - Mozilla Public License Version 1.1 or later (the "MPL")
 *    http://www.mozilla.org/MPL/MPL-1.1.html
 *
 * == END LICENSE ==
 *
 * Main TikiWiki integration plugin.
 * Based on work done by the MediaWiki Team, big thanx to them
 */


/* from: http://mediawiki.fckeditor.net/index.php/Talk:Main_Page#CKEditor
 * 
 * This plugin allows custom output for CKEditor
 * It could be used for any language type such as for a wiki or BBCode
 * @author Juan Valencia
 */
 
CKEDITOR.plugins.add('tikiwiki',{    
	init: function(editor)    {  
		//alert("It Loaded!" + CKEDITOR.timestamp);		
		editor.dataProcessor.toDataFormat = function (html, fixForBody) { return transform(html); };
//		editor.dataProcessor.toHtml = function (data, fixForBody) { return reform(data); };
		
		// button stuff goes here?
	}
 
});
 
/*
 * transform takes the html that is in the editor window and changes it 
 * to the output data format.
 * @param {String} html The html to convert to the new format
 * return the new output String.
 */
 
function transform(html) {
	debugger;
	return html;
}
 
/*
 * reform takes the data format that you are outputing to and reforms it back into
 * good html
 * @param {String} data The data string to convert back to html
 */
 
function reform(data) {
	debugger;
	return data;
}

