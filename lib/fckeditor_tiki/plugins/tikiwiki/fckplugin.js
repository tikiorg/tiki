/*
 * FCKeditor - The text editor for Internet - http://www.fckeditor.net
 * Copyright (C) 2003-2007 Frederico Caldeira Knabben
 * Copyright (C) 2008-2009 Stéphane Casset for the Tikiwiki Team
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

// Rename the "Source" buttom to "Wikitext".
FCKToolbarItems.RegisterItem( 'Source', new FCKToolbarButton( 'Source', 'Wikitext', null, FCK_TOOLBARITEM_ICONTEXT, true, true, 1 ) ) ;

// Register our toolbar buttons.
//var tbButton = new FCKToolbarButton( 'MW_Template', 'Template',  FCKLang.wikiBtnTemplate || 'Insert/Edit Template') ;
//tbButton.IconPath = FCKConfig.PluginsPath + 'mediawiki/images/tb_icon_template.gif' ;
//FCKToolbarItems.RegisterItem( 'MW_Template', tbButton ) ;

FCKToolbarItems.OldGetItem = FCKToolbarItems.GetItem;

FCKToolbarItems.GetItem = function( itemName )
{
	var oItem = FCKToolbarItems.LoadedItems[ itemName ] ;

	if ( oItem )
		return oItem ;

	switch ( itemName )
	{
		case 'Bold'			: oItem = new FCKToolbarButton( 'Bold'          , FCKLang.Bold, null, null, true, true, 20 ) ; break ;
		case 'Italic'		: oItem = new FCKToolbarButton( 'Italic'        , FCKLang.Italic, null, null, true, true, 21 ) ; break ;
		case 'Underline'	: oItem = new FCKToolbarButton( 'Underline'     , FCKLang.Underline, null, null, true, true, 22 ) ; break ;
		case 'StrikeThrough': oItem = new FCKToolbarButton( 'StrikeThrough' , FCKLang.StrikeThrough, null, null, true, true, 23 ) ; break ;
		case 'Link'			: oItem = new FCKToolbarButton( 'Link'          , FCKLang.InsertLinkLbl, FCKLang.InsertLink, null, true, true, 34 ) ; break ;

		default:
			return FCKToolbarItems.OldGetItem( itemName );
	}

	FCKToolbarItems.LoadedItems[ itemName ] = oItem ;

	return oItem ;
}

FCKToolbarButton.prototype.Click = function() 
{
	var oToolbarButton = this._ToolbarButton || this ;
	
	// for some buttons, do something else instead...
	var CMode = false ;
	if ( oToolbarButton.SourceView && (FCK_EDITMODE_SOURCE == FCK.EditMode) )
	{
		if ( !window.parent.popup )
			var oDoc = window.parent;
		else
			var oDoc = window.parent.popup;

		switch (oToolbarButton.CommandName) 
		{
			case 'Bold' 		: oDoc.FCKeditorInsertTags ('__', '__', 'Bold text', document) ; CMode = true ; break ;
			case 'Italic' 		: oDoc.FCKeditorInsertTags ('\'\'', '\'\'', 'Italic text', document) ; CMode = true ; break ;
			case 'Underline' 	: oDoc.FCKeditorInsertTags ('===', '===', 'Underlined text', document) ; CMode = true ; break ;
			case 'StrikeThrough': oDoc.FCKeditorInsertTags ('--', '--', 'Strikethrough text', document) ; CMode = true ; break ;
			case 'Link' 		: oDoc.FCKeditorInsertTags ('((', '))', 'Internal link', document) ; CMode = true ; break ;
		}
	}
	
	if ( !CMode )
		FCK.ToolbarSet.CurrentInstance.Commands.GetCommand( oToolbarButton.CommandName ).Execute() ;
}

// TikiWiki Wikitext Data Processor implementation.
FCK.DataProcessor =
{
	_inPre : false,
	_inLSpace : false,	

	/*
	 * Returns a string representing the HTML format of "data". The returned
	 * value will be loaded in the editor.
	 * The HTML must be from <html> to </html>, eventually including
	 * the DOCTYPE.
	 *     @param {String} data The data to be converted in the
	 *            DataProcessor specific format.
	 */
	ConvertToHtml : function( data )
	{
		// Call the original code.
		return FCKDataProcessor.prototype.ConvertToHtml.call( this, data ) ;
	},

	/*
	 * Converts a DOM (sub-)tree to a string in the data format.
	 *     @param {Object} rootNode The node that contains the DOM tree to be
	 *            converted to the data format.
	 *     @param {Boolean} excludeRoot Indicates that the root node must not
	 *            be included in the conversion, only its children.
	 *     @param {Boolean} format Indicates that the data must be formatted
	 *            for human reading. Not all Data Processors may provide it.
	 */
	ConvertToDataFormat : function( rootNode, excludeRoot, ignoreIfEmptyParagraph, format )
	{
		// rootNode is <body>.

		// Normalize the document for text node processing (except IE - #1586).
		if ( !FCKBrowserInfo.IsIE )
			rootNode.normalize() ;

		var stringBuilder = new Array() ;
		this._AppendNode( rootNode, stringBuilder, '' ) ;
		return stringBuilder.join( '' ).RTrim().replace(/^\n*/, "") ;
	},

	/*
	 * Makes any necessary changes to a piece of HTML for insertion in the
	 * editor selection position.
	 *     @param {String} html The HTML to be fixed.
	 */
	FixHtml : function( html )
	{
		return html ;
	},

	// Collection of element definitions:
	//		0 : Prefix
	//		1 : Suffix
	//		2 : Ignore children
	_BasicElements : {
		body	: [ ],
		b		: [ "__", "__" ],
		strong	: [ "__", "__" ],
		i		: [ "''", "''" ],
		em		: [ "''", "''" ],
		u		: [ "===", "===" ],
		sub		: [ "{SUB()}", "{SUB}" ],
		sup		: [ "{SUP()}", "{SUP}" ],
		center		: [ "::", "::" ],
		pre		: [ "~pp~", "~/pp~" ],
		code		: [ "-+", "+-" ],
		strike	:	["--","--" ],
		del		:	["--","--" ],
		p			: [ '', '\n' ],
		h1		: [ '! ', '\n' ],
		h2		: [ '!! ', '\n' ],
		h3		: [ '!!! ', '\n' ],
		h4		: [ '!!!! ', '\n' ],
		h5		: [ '!!!!! ', '\n' ],
		h6		: [ '!!!!!! ', '\n' ],
		br		: [ '\n', null, true ],
		hr		: [ '---', null, true ]
	} ,

	// This function is based on FCKXHtml._AppendNode.
	_AppendNode : function( htmlNode, stringBuilder, prefix )
	{
		if ( !htmlNode )
			return ;

		switch ( htmlNode.nodeType )
		{
			// Element Node.
			case 1 :

				// Here we found an element that is not the real element, but a
				// fake one (like the Flash placeholder image), so we must get the real one.
				if ( htmlNode.getAttribute('_fckfakelement') && !htmlNode.getAttribute( '_fck_mw_math' ) )
					return this._AppendNode( FCK.GetRealElement( htmlNode ), stringBuilder ) ;

				// Mozilla insert custom nodes in the DOM.
				if ( FCKBrowserInfo.IsGecko && htmlNode.hasAttribute('_moz_editor_bogus_node') )
					return ;

				// This is for elements that are instrumental to FCKeditor and
				// must be removed from the final HTML.
				if ( htmlNode.getAttribute('_fcktemp') )
					return ;

				// Get the element name.
				var sNodeName = htmlNode.tagName.toLowerCase()  ;

				if ( FCKBrowserInfo.IsIE )
				{
					// IE doens't include the scope name in the nodeName. So, add the namespace.
					if ( htmlNode.scopeName && htmlNode.scopeName != 'HTML' && htmlNode.scopeName != 'FCK' )
						sNodeName = htmlNode.scopeName.toLowerCase() + ':' + sNodeName ;
				}
				else
				{
					if ( sNodeName.StartsWith( 'fck:' ) )
						sNodeName = sNodeName.Remove( 0,4 ) ;
				}

				// Check if the node name is valid, otherwise ignore this tag.
				// If the nodeName starts with a slash, it is a orphan closing tag.
				// On some strange cases, the nodeName is empty, even if the node exists.
				if ( !FCKRegexLib.ElementName.test( sNodeName ) )
					return ;

				if ( sNodeName == 'br' && ( this._inPre || this._inLSpace ) ) 
				{
					stringBuilder.push( "\n" ) ;
					if ( this._inLSpace )
						stringBuilder.push( "\n" ) ;
					return ;
				}
					
				// Remove the <br> if it is a bogus node.
				if ( sNodeName == 'br' && htmlNode.getAttribute( 'type', 2 ) == '_moz' )
					return ;

				// The already processed nodes must be marked to avoid then to be duplicated (bad formatted HTML).
				// So here, the "mark" is checked... if the element is Ok, then mark it.
				if ( htmlNode._fckxhtmljob && htmlNode._fckxhtmljob == FCKXHtml.CurrentJobNum )
					return ;

				var basicElement = this._BasicElements[ sNodeName ] ;
				if ( basicElement )
				{
					var basic0 = basicElement[0];
					var basic1 = basicElement[1];

/*
					if ( ( basicElement[0] == "''" || basicElement[0] == "'''" ) && stringBuilder.length > 2 )
					{
						var pr1 = stringBuilder[stringBuilder.length-1];
						var pr2 = stringBuilder[stringBuilder.length-2];

						if ( pr1 + pr2 == "'''''") {
							if ( basicElement[0] == "''")
							{
								basic0 = '<i>';
								basic1 = '</i>';
							}
							if ( basicElement[0] == "__")
							{
								basic0 = '<b>';
								basic1 = '</b>';
							}
						}
					}
*/

					if ( basic0 )
						stringBuilder.push( basic0 ) ;

					var len = stringBuilder.length ;
					
					if ( !basicElement[2] )
					{
						this._AppendChildNodes( htmlNode, stringBuilder, prefix ) ;
						// only empty element inside, remove it to avoid quotes
						if ( ( stringBuilder.length == len || (stringBuilder.length == len + 1 && !stringBuilder[len].length) ) 
							 )
						{
							if ( basic0 )
								stringBuilder.pop();
							stringBuilder.push( '\n' ) ;
							return;
						}
					}

					if ( basic1 )
						stringBuilder.push( basic1 ) ;
				}
				else
				{
					switch ( sNodeName )
					{
						case 'div':
							if (htmlNode.className == 'titlebar' ) {
								stringBuilder.push( '-=' ) ;
								this._AppendChildNodes( htmlNode, stringBuilder, prefix ) ;
								stringBuilder.push( '=-\n' ) ;
								break;
							}
							if (htmlNode.className == 'simplebox' ) {
								stringBuilder.push( '^' ) ;
								this._AppendChildNodes( htmlNode, stringBuilder, prefix ) ;
								stringBuilder.push( '^' ) ;
								break;
							}
						case 'ol' :
						case 'ul' :
							var isFirstLevel = !htmlNode.parentNode.nodeName.IEquals( 'ul', 'ol', 'li', 'dl', 'dt', 'dd' ) ;

							this._AppendChildNodes( htmlNode, stringBuilder, prefix ) ;

							if ( isFirstLevel && ! stringBuilder[ stringBuilder.length - 1 ].Trim().EndsWith("\n") ) {
								stringBuilder.push( '\n' ) ;
							}

							break ;

						case 'li' :

							if( stringBuilder.length > 1)
							{
								var sLastStr = stringBuilder[ stringBuilder.length - 1 ] ;
								if ( sLastStr != ";" && sLastStr != ":" && sLastStr != "#" && sLastStr != "*" && sLastStr != '\n' )
 									stringBuilder.push( '\n' + prefix ) ;
							}
							
							var parent = htmlNode.parentNode ;
							var listType = "#" ;
						if ( parent.nodeName.toLowerCase() == 'ul' ) {
									listType = "*" ;
								} else if ( parent.nodeName.toLowerCase() == 'ol' ) {
									listType = "#" ;
								}
	
							listPrefix = "";
							while ( parent )
							{
								if ( parent.nodeName.toLowerCase() == 'ul' || parent.nodeName.toLowerCase() == 'ol' ) {
									listPrefix = listPrefix + listType;
								} else if ( parent.nodeName.toLowerCase() != 'li' ) {
									break ;
								}
								parent = parent.parentNode ;
							}
							
							stringBuilder.push( listPrefix ) ;
							this._inList = true ;
							this._AppendChildNodes( htmlNode, stringBuilder, prefix) ;
							this._inList = false ;
							
							break ;

						case 'span':
							var plugin_content = htmlNode.getAttribute( '_plugin' , 2 ) ;
							if ( plugin_content ) {
								stringBuilder.push( urldecode(plugin_content) ) ;
								break;
							}
							if ( htmlNode.style.cssText.match(/color *: *([^; ]+)[ ;]?/) ) {
								color = htmlNode.style.cssText.replace(/color *: *([^; ]+)[ ;]?/,'$1');
								stringBuilder.push( '~~' + color + ':' );
								this._AppendChildNodes( htmlNode, stringBuilder, prefix ) ;
								stringBuilder.push( '~~' );
								break;
							}
							this._AppendChildNodes( htmlNode, stringBuilder, prefix ) ;
							break;
						case 'a' :

							href = htmlNode.getAttribute( 'href' , 2 ) || '' ;
							descr = htmlNode.innerHTML || '' ;

							if ( href.StartsWith('tiki-index.php') ) {
								if ( href.match(/.*\#(.+)$/) )
									anchor = href.replace(/.*\#(.+)$/,'$1');
								else
									anchor = '';
								page = href.replace(/tiki-index.php\?.*page=([^&#]+).*/,'$1');
								stringBuilder.push( '((' + page ) ;
								if ( anchor != '' ) {
									stringBuilder.push( '|#'+ anchor ) ;
								}
								if ( descr != '' && descr != page ) {
									stringBuilder.push( '|' );
									this._AppendChildNodes( htmlNode, stringBuilder, prefix ) ;
								}
								stringBuilder.push( '))' ) ;
							} else if ( href.StartsWith('http://') ) {
								stringBuilder.push( '[' + href ) ;
								rel = htmlNode.getAttribute( 'rel' , 2 ) || '' ;
								rel = rel.replace(/ *external */,'');
								if ( href != descr ) {
									stringBuilder.push( '|' ) ;
									this._AppendChildNodes( htmlNode, stringBuilder, prefix ) ;
								}
								if ( rel != '' )
									stringBuilder.push( '|' + rel ) ;
								stringBuilder.push( ']' ) ;
							}

							break ;
							
						case 'dl' :
						
							this._AppendChildNodes( htmlNode, stringBuilder, prefix ) ;
							var isFirstLevel = !htmlNode.parentNode.nodeName.IEquals( 'ul', 'ol', 'li', 'dl', 'dd', 'dt' ) ;
							if ( isFirstLevel && stringBuilder[ stringBuilder.length - 1 ] != "\n" )
								stringBuilder.push( '\n') ;
							
							break ;

						case 'dt' :
						
							if( stringBuilder.length > 1)
							{
								var sLastStr = stringBuilder[ stringBuilder.length - 1 ] ;
								if ( sLastStr != ";" && sLastStr != ":" && sLastStr != "#" && sLastStr != "*" )
 									stringBuilder.push( '\n' + prefix ) ;
							}
							stringBuilder.push( ';' ) ;
							this._AppendChildNodes( htmlNode, stringBuilder, prefix + ";") ;
							
							break ;

						case 'dd' :
						
							if( stringBuilder.length > 1)
							{
								var sLastStr = stringBuilder[ stringBuilder.length - 1 ] ;
/*
								if ( sLastStr != ";" && sLastStr != ":" && sLastStr != "#" && sLastStr != "*" )
 									stringBuilder.push( '\n' + prefix ) ;
*/
							}
							stringBuilder.push( ':' ) ;
							this._AppendChildNodes( htmlNode, stringBuilder, prefix + ":" ) ;
							
							break ;
							
						case 'table' :

							stringBuilder.push( '||' ) ;

							for ( var r = 0 ; r < htmlNode.rows.length ; r++ )
							{

								for ( var c = 0 ; c < htmlNode.rows[r].cells.length ; c++ )
								{
									this._IsInsideCell = true ;
									this._AppendChildNodes( htmlNode.rows[r].cells[c], stringBuilder, prefix ) ;
									this._IsInsideCell = false ;

									if (c < (htmlNode.rows[r].cells.length -1))
										stringBuilder.push( ' |' ) ;
								}
								if (r < (htmlNode.rows.length -1))
									stringBuilder.push( '\n' ) ;
							}

							stringBuilder.push( '||' ) ;

							break ;

						case 'img' :

							var formula = htmlNode.getAttribute( '_fck_mw_math' ) ;

							if ( formula && formula.length > 0 )
							{
								stringBuilder.push( '<math>' ) ;
								stringBuilder.push( formula ) ;
								stringBuilder.push( '</math>' ) ;
								return ;
							}

							var imgName		= htmlNode.getAttribute( '_fck_mw_filename' ) ;
							var imgCaption	= htmlNode.getAttribute( 'alt' ) || '' ;
							var imgType		= htmlNode.getAttribute( '_fck_mw_type' ) || '' ;
							var imgLocation	= htmlNode.getAttribute( '_fck_mw_location' ) || '' ;
							var imgWidth	= htmlNode.getAttribute( '_fck_mw_width' ) || '' ;
							var imgHeight	= htmlNode.getAttribute( '_fck_mw_height' ) || '' ;

							stringBuilder.push( '[[Image:' )
							stringBuilder.push( imgName )

							if ( imgType.length > 0 )
								stringBuilder.push( '|' + imgType ) ;

							if ( imgLocation.length > 0 )
								stringBuilder.push( '|' + imgLocation ) ;

							if ( imgWidth.length > 0 )
							{
								stringBuilder.push( '|' + imgWidth ) ;

								if ( imgHeight.length > 0 )
									stringBuilder.push( 'x' + imgHeight ) ;

								stringBuilder.push( 'px' ) ;
							}

							if ( imgCaption.length > 0 )
								stringBuilder.push( '|' + imgCaption ) ;

							stringBuilder.push( ']]' )

							break ;

						case 'span' :
							switch ( htmlNode.className )
							{
								case 'fck_mw_ref' :
									var refName = htmlNode.getAttribute( 'name' ) ;

									stringBuilder.push( '<ref' ) ;

									if ( refName && refName.length > 0 )
										stringBuilder.push( ' name="' + refName + '"' ) ;

									if ( htmlNode.innerHTML.length == 0 )
										stringBuilder.push( ' />' ) ;
									else
									{
										stringBuilder.push( '>' ) ;
										stringBuilder.push( htmlNode.innerHTML ) ;
										stringBuilder.push( '</ref>' ) ;
									}
									return ;

								case 'fck_mw_references' :
									stringBuilder.push( '<references />' ) ;
									return ;

								case 'fck_mw_template' :
									stringBuilder.push( FCKTools.HTMLDecode(htmlNode.innerHTML).replace(/fckLR/g,'\r\n') ) ;
									return ;
								
								case 'fck_mw_magic' :
									stringBuilder.push( htmlNode.innerHTML ) ;
									return ;

								case 'fck_mw_nowiki' :
									sNodeName = 'nowiki' ;
									break ;

								case 'fck_mw_html' :
									sNodeName = 'html' ;
									break ;

								case 'fck_mw_includeonly' :
									sNodeName = 'includeonly' ;
									break ;

								case 'fck_mw_noinclude' :
									sNodeName = 'noinclude' ;
									break ;

								case 'fck_mw_gallery' :
									sNodeName = 'gallery' ;
									break ;
									
								case 'fck_mw_onlyinclude' :
									sNodeName = 'onlyinclude' ;
									
									break ;
							}

							// Change the node name and fell in the "default" case.
							if ( htmlNode.getAttribute( '_fck_mw_customtag' ) )
								sNodeName = htmlNode.getAttribute( '_fck_mw_tagname' ) ;

						case 'pre' :
							var attribs = this._GetAttributesStr( htmlNode ) ;
							
							if ( htmlNode.className == "_fck_mw_lspace")
							{
								stringBuilder.push( "\n " ) ;
								this._inLSpace = true ;
								this._AppendChildNodes( htmlNode, stringBuilder, prefix ) ;
								this._inLSpace = false ;
								var len = stringBuilder.length ;
								if ( len>1 ) {
									var tail = stringBuilder[len-2] + stringBuilder[len-1];
									if ( len>2 ) {
										tail = stringBuilder[len-3] + tail ;
									}
									if (tail.EndsWith("\n ")) {
										stringBuilder[len-1] = stringBuilder[len-1].replace(/ $/, "");
									}
									else if ( !tail.EndsWith("\n") ) {
										stringBuilder.push( "\n" ) ;
									}
								}
							}
							else
							{
								stringBuilder.push( '<' ) ;
								stringBuilder.push( sNodeName ) ;

								if ( attribs.length > 0 )
									stringBuilder.push( attribs ) ;
								if(htmlNode.innerHTML == "")
									stringBuilder.push( ' />' ) ;
								else
								{
									stringBuilder.push( '>' ) ;
									this._inPre = true ;
									this._AppendChildNodes( htmlNode, stringBuilder, prefix ) ;
									this._inPre = false ;

									stringBuilder.push( '<\/' ) ;
									stringBuilder.push( sNodeName ) ;
									stringBuilder.push( '>' ) ;
								}
							}
						
							break ;
						default :
							var attribs = this._GetAttributesStr( htmlNode ) ;

							stringBuilder.push( '<' ) ;
							stringBuilder.push( sNodeName ) ;

							if ( attribs.length > 0 )
								stringBuilder.push( attribs ) ;

							stringBuilder.push( '>' ) ;
							this._AppendChildNodes( htmlNode, stringBuilder, prefix ) ;
							stringBuilder.push( '<\/' ) ;
							stringBuilder.push( sNodeName ) ;
							stringBuilder.push( '>' ) ;
							break ;
					}
				}

				htmlNode._fckxhtmljob = FCKXHtml.CurrentJobNum ;
				return ;

			// Text Node.
			case 3 :

				var parentIsSpecialTag = htmlNode.parentNode.getAttribute( '_fck_mw_customtag' ) ; 
				var textValue = htmlNode.nodeValue;
	
				if ( !parentIsSpecialTag ) 
				{
					if ( FCKBrowserInfo.IsIE && this._inLSpace ) {
						textValue = textValue.replace( /\r/g, "\r " ) ;
						if (textValue.EndsWith( "\r " )) {
							textValue = textValue.replace( /\r $/, "\r" );
						}
					}
					if ( !FCKBrowserInfo.IsIE && this._inLSpace ) {
						textValue = textValue.replace( /\n(?! )/g, "\n " ) ;
					}
					
					if (!this._inLSpace && !this._inPre) {
						textValue = textValue.replace( /[\n\t]/g, ' ' ) ; 
					}
	
					textValue = FCKTools.HTMLEncode( textValue ) ;
					textValue = textValue.replace( /\u00A0/g, '&nbsp;' ) ;

					if ( ( !htmlNode.previousSibling ||
					( stringBuilder.length > 0 && stringBuilder[ stringBuilder.length - 1 ].EndsWith( '\n' ) ) ) && !this._inLSpace && !this._inPre )
					{
						textValue = textValue.LTrim() ;
					}

					if ( !htmlNode.nextSibling && !this._inLSpace && !this._inPre && (!htmlNode.parentNode || !htmlNode.parentNode.nextSibling))
						textValue = textValue.RTrim() ;

					if (!this._inLSpace && !this._inPre)
						textValue = textValue.replace( / {2,}/g, ' ' ) ;

					if ( this._inLSpace && textValue.length == 1 && textValue.charCodeAt(0) == 13 )
						textValue = textValue + " " ;

					if ( !this._inLSpace && !this._inPre && textValue == " " ) {
						var len = stringBuilder.length;
						if (len > 1) {
							var tail = stringBuilder[len-2] + stringBuilder[len-1];
							if ( tail.toString().EndsWith( "\n" ) )
								textValue = "";
						}
					}

					if ( this._IsInsideCell )
						textValue = textValue.replace( /\|/g, '&#124;' ) ;
				}
				else 
				{
					textValue = FCKTools.HTMLDecode(textValue).replace(/fckLR/g,'\r\n');
				}
				
				stringBuilder.push( textValue ) ;
				return ;

			// Comment
			case 8 :
				// IE catches the <!DOTYPE ... > as a comment, but it has no
				// innerHTML, so we can catch it, and ignore it.
				if ( FCKBrowserInfo.IsIE && !htmlNode.innerHTML )
					return ;

				stringBuilder.push( "<!--"  ) ;

				try	{ stringBuilder.push( htmlNode.nodeValue ) ; }
				catch (e) { /* Do nothing... probably this is a wrong format comment. */ }

				stringBuilder.push( "-->" ) ;
				return ;
		}
	},

	_AppendChildNodes : function( htmlNode, stringBuilder, listPrefix )
	{
		var child = htmlNode.firstChild ;

		while ( child )
		{
			this._AppendNode( child, stringBuilder, listPrefix ) ;
			child = child.nextSibling ;
		}
	},

	_GetAttributesStr : function( htmlNode )
	{
		var attStr = '' ;
		var aAttributes = htmlNode.attributes ;

		for ( var n = 0 ; n < aAttributes.length ; n++ )
		{
			var oAttribute = aAttributes[n] ;

			if ( oAttribute.specified )
			{
				var sAttName = oAttribute.nodeName.toLowerCase() ;
				var sAttValue ;

				// Ignore any attribute starting with "_fck".
				if ( sAttName.StartsWith( '_fck' ) )
					continue ;
				// There is a bug in Mozilla that returns '_moz_xxx' attributes as specified.
				else if ( sAttName.indexOf( '_moz' ) == 0 )
					continue ;
				// For "class", nodeValue must be used.
				else if ( sAttName == 'class' )
				{
					// Get the class, removing any fckXXX we can have there.
					sAttValue = oAttribute.nodeValue.replace( /(^|\s*)fck\S+/, '' ).Trim() ;

					if ( sAttValue.length == 0 )
						continue ;
				}
				else if ( sAttName == 'style' && FCKBrowserInfo.IsIE ) {
					sAttValue = htmlNode.style.cssText.toLowerCase() ;
				}
				// XHTML doens't support attribute minimization like "CHECKED". It must be trasformed to cheched="checked".
				else if ( oAttribute.nodeValue === true )
					sAttValue = sAttName ;
				else
					sAttValue = htmlNode.getAttribute( sAttName, 2 ) ;	// We must use getAttribute to get it exactly as it is defined.

				// leave templates
				if ( sAttName.StartsWith( '{{' ) && sAttName.EndsWith( '}}' ) ) {
					attStr += ' ' + sAttName ;
				}
				else {
					attStr += ' ' + sAttName + '="' + String(sAttValue).replace( '"', '&quot;' ) + '"' ;
				}
			}
		}
		return attStr ;
	}
} ;

// Here we change the SwitchEditMode function to make the Ajax call when
// switching from Wikitext.
(function()
{
	if (window.parent.showFCKEditor & (2|4))				//if popup or toggle link
	{
		var original_ULF = FCK.UpdateLinkedField ;
		FCK.UpdateLinkedField = function(){
			if (window.parent.showFCKEditor & 1)			//only if editor is up-to-date
			{
				original_ULF.apply();
			}
		}
	}
	var original = FCK.SwitchEditMode ;
	FCK.SwitchEditMode = function()
	{
		var args = arguments ;
		var loadHTMLFromAjax = function( result )
		{
			FCK.EditingArea.Textarea.value = urldecode(result) ;
			original.apply( FCK, args ) ;
		}
		var edittools_markup = parent.document.getElementById ('editpage-specialchars') ;

		if ( FCK.EditMode == FCK_EDITMODE_SOURCE )
		{
			// Hide the textarea to avoid seeing the code change.
			FCK.EditingArea.Textarea.style.visibility = 'hidden' ;
			var loading = document.createElement( 'span' ) ;
			loading.innerHTML = '&nbsp;'+ (FCKLang.wikiLoadingWikitext || 'Loading Wikitext. Please wait...' )+'&nbsp;';
			loading.style.position = 'absolute' ;
			loading.style.left = '5px' ;
//			loading.style.backgroundColor = '#ff0000' ;
			FCK.EditingArea.Textarea.parentNode.appendChild( loading, FCK.EditingArea.Textarea ) ;
			// if we have a standard Edittools div, hide the one containing wikimarkup
			if (edittools_markup) {			
				edittools_markup.style.display = 'none' ;
			}	

			// Use Ajax to transform the Wikitext to HTML.
			//Sept needs proper function in javascript...
			if( window.parent.popup ){
				window.parent.popup.parent.FCK_sajax( 'wfSajaxWikiToHTML', [FCK.EditingArea.Textarea.value], loadHTMLFromAjax ) ;
			}
			else{
				window.parent.loadHTMLFromAjax = loadHTMLFromAjax;
				window.parent.xajax_WikiToHTML( FCK.EditingArea.Textarea.value ) ;
			}
		}
		else {
			original.apply( FCK, args ) ;
			if (edittools_markup) {
				edittools_markup.style.display = '' ;
			}
		}
	}
})() ;

function urldecode( str ) {
    // http://kevin.vanzonneveld.net
    // +   original by: Philip Peterson
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: AJ
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Brett Zamir
    // %          note: info on what encoding functions to use from: http://xkr.us/articles/javascript/encode-compare/
    // *     example 1: urldecode('Kevin+van+Zonneveld%21');
    // *     returns 1: 'Kevin van Zonneveld!'
    // *     example 2: urldecode('http%3A%2F%2Fkevin.vanzonneveld.net%2F');
    // *     returns 2: 'http://kevin.vanzonneveld.net/'
    // *     example 3: urldecode('http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a');
    // *     returns 3: 'http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a'
    
    var histogram = {};
    var ret = str.toString();
    
    var replacer = function(search, replace, str) {
        var tmp_arr = [];
        tmp_arr = str.split(search);
        return tmp_arr.join(replace);
    };
    
    // The histogram is identical to the one in urlencode.
    histogram["'"]   = '%27';
    histogram['(']   = '%28';
    histogram[')']   = '%29';
    histogram['*']   = '%2A';
    histogram['~']   = '%7E';
    histogram['!']   = '%21';
    histogram['%20'] = '+';
 
    for (replace in histogram) {
        search = histogram[replace]; // Switch order when decoding
        ret = replacer(search, replace, ret) // Custom replace. No regexing   
    }
    
    // End with decodeURIComponent, which most resembles PHP's encoding functions
    ret = decodeURIComponent(ret);
 
    return ret;
}

// Context menu for templates.
FCK.ContextMenu.RegisterListener({
	AddItems : function( contextMenu, tag, tagName )
	{
		if ( tagName == 'IMG' )
		{
			if ( tag.getAttribute( '_fck_mw_template' ) )
			{
				contextMenu.AddSeparator() ;
				contextMenu.AddItem( 'MW_Template', FCKLang.wikiMnuTemplate || 'Template Properties' ) ;
			}
			if ( tag.getAttribute( '_fck_mw_magic' ) )
			{
				contextMenu.AddSeparator() ;
				contextMenu.AddItem( 'MW_MagicWord', FCKLang.wikiMnuMagicWord || 'Modify Magic Word' ) ;
			}
			if ( tag.getAttribute( '_fck_mw_ref' ) )
			{
				contextMenu.AddSeparator() ;
				contextMenu.AddItem( 'MW_Ref', FCKLang.wikiMnuReference || 'Reference Properties' ) ;
			}
			if ( tag.getAttribute( '_fck_mw_html' ) )
			{
				contextMenu.AddSeparator() ;
				contextMenu.AddItem( 'MW_Special', 'Edit HTML code' ) ;
			}
			if ( tag.getAttribute( '_fck_mw_math' ) )
			{
				contextMenu.AddSeparator() ;
				contextMenu.AddItem( 'MW_Math', FCKLang.wikiMnuFormula || 'Edit Formula' ) ;
			}
			if ( tag.getAttribute( '_fck_mw_special' ) || tag.getAttribute( '_fck_mw_nowiki' ) || tag.getAttribute( '_fck_mw_includeonly' ) || tag.getAttribute( '_fck_mw_noinclude' ) || tag.getAttribute( '_fck_mw_onlyinclude' ) || tag.getAttribute( '_fck_mw_gallery' )) //YC
			{
				contextMenu.AddSeparator() ;
				contextMenu.AddItem( 'MW_Special', FCKLang.wikiMnuSpecial || 'Special Tag Properties' ) ;
			}
		}
	}
}) ;
