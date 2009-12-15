/* 
 *  FCKPlugin.js
 *  ------------
 *  This is a generic file which is needed for plugins that are developed
 *  for FCKEditor. With the below statements that toolbar is created and
 *  several options are being activated.
 *
 *  See the online documentation for more information:
 *  http://wiki.fckeditor.net/
 */

// Register the related commands.
FCKCommands.RegisterCommand(
	'tikilink',
	new FCKDialogCommand(
		'tikilink',
		FCKConfig.tikilinkDlgTitle,
		FCKPlugins.Items['tikilink'].Path + 'fck_tikilink.php',
		400,
		400
	)
);
 
var otikilinkItem = new FCKToolbarButton( 'tikilink', FCKConfig.tikilinkBtn, null, null, false, true ); 
otikilinkItem.IconPath = _TikiRoot + 'pics/icons/page_white_link.png'; 

FCKToolbarItems.RegisterItem( 'tikilink', otikilinkItem );

var FCKTikiLinks = new Object() ;

FCKTikiLinks.add = function( page, name ) {
	var oA = FCK.CreateElement( 'A' ) ;
	this.SetupLink( oA, page, name ) ;
}

FCKTikiLinks.SetupLink  = function( a, page, name ) {
	a.innerHTML = name ;
	a.contentEditable = 'false' ;
	a.className = 'wiki' ;
	a.href = page ;
	a.title = 'wiki : ' + page ;
	a._wikilink = page ;
	a.onresizestart = function() {
		FCK.EditorWindow.event.returnValue = false ;
		return false ;
	}
}

FCKTikiLinks._SetupClickListener = function() {
	FCKTikiLinks._ClickListener = function( e ) {
		if ( e.target.tagName == 'A' && e.target._wikilink ) {
			FCKSelection.SelectNode( e.target ) ;
		}
	}
	FCK.EditorDocument.addEventListener( 'click', FCKTikiLinks._ClickListener, true ) ;
}

FCKTikiLinks.OnDoubleClick = function( a ) {
	if ( a.tagName == 'A' && a._wikilink ) {
		FCKCommands.GetCommand( 'tikilink' ).Execute() ;
	}
}

FCK.RegisterDoubleClickHandler( FCKTikiLinks.OnDoubleClick, 'A' ) ;

FCKTikiLinks.Exist = function( name ) {
	var aA = FCK.EditorDocument.getElementsByTagName( 'A' ) ;
	for ( var i = 0 ; i < aA.length ; i++ ) {
		if ( aA[i]._wikilink == name ) {
			return true ;
		}
	}
}

if ( FCKBrowserInfo.IsIE ) {
	FCKTikiLinks.Redraw = function() {
		var aWikiLinks = FCK.EditorDocument.body.innerText.match( /\(\(((?:[^\n|\(\)])(?:(?!(\)\)|\||\n)).)*?)(\|([^\)]*))?\)\)/g ) ;
		if ( !aWikiLinks )
			return ;
		var oRange = FCK.EditorDocument.body.createTextRange() ;
		for ( var i = 0 ; i < aWikiLinks.length ; i++ ) {
			if ( oRange.findText( aWikiLinks[i] ) ) {
				var sMatch = aWikiLinks[i].match( /\(\(((?:[^\n|\(\)])(?:(?!(\)\)|\||\n)).)*?)(\|([^\)]*))?\)\)/ );
				var sPage = sMatch[1] ;
				var sName = sMatch[4] ;
				if (! sName ) {
					sName = sPage ;
				}
				oRange.pasteHTML( '<a href="tiki-index.php?page=' + sPage + '" class="wiki" contenteditable="false" _wikilink="' + sPage + '">' + sName + '</a>' );
			}
		}
	}
} else {
	FCKTikiLinks.Redraw = function() {
		var oInteractor = FCK.EditorDocument.createTreeWalker( FCK.EditorDocument.body, NodeFilter.SHOW_TEXT, FCKTikiLinks._AcceptNode, true ) ;
		var aNodes = new Array() ;
		while ( oNode = oInteractor.nextNode() ) {
			aNodes[ aNodes.length ] = oNode ;
		}
		for ( var n = 0 ; n < aNodes.length ; n++ ) {
			var aPieces = aNodes[n].nodeValue.split( /(\(\((?:(?:[^\n|\(\)])(?:(?!(\)\)|\||\n)).)*?)(?:\|(?:[^\)]*))?\)\))/ );
			for ( var i = 0 ; i < aPieces.length ; i++ ) {
				if ( aPieces[i].length > 0 ) {
					if ( aPieces[i].indexOf( '((' ) == 0 ) {
						var sMatch = aPieces[i].match( /\(\(((?:[^\n|\(\)])(?:(?!(\)\)|\||\n)).)*?)(\|([^\)]*))?\)\)/ );
						var sPage = sMatch[1] ;
						var sName = sMatch[4] ;
						if ( !sName ) {
							sName = sPage ;
						}
						var oA = FCK.EditorDocument.createElement( 'a' ) ;
						FCKTikiLinks.SetupLink( oA, sPage, sName ) ;
						aNodes[n].parentNode.insertBefore( oA, aNodes[n] ) ;
					} else {
						aNodes[n].parentNode.insertBefore( FCK.EditorDocument.createTextNode( aPieces[i] ) , aNodes[n] ) ;
					}
				}
			}
			aNodes[n].parentNode.removeChild( aNodes[n] ) ;
		}
		FCKTikiLinks._SetupClickListener() ;
	}
	FCKTikiLinks._AcceptNode = function( node ) {
		if ( /\(\((([^\n|\(\)])((?!(\)\)|\||\n)).)*?)(\|([^\)]*))?\)\)/.test( node.nodeValue ) ) {
			return NodeFilter.FILTER_ACCEPT ;
		} else {
			return NodeFilter.FILTER_SKIP ;
		}
	}
}

FCK.Events.AttachEvent( 'OnAfterSetHTML', FCKTikiLinks.Redraw ) ;

FCKXHtml.TagProcessors['a'] = function( node, htmlNode ) {
	if ( htmlNode._wikilink ) {
		if ( htmlNode.innerHTML && htmlNode.innerHTML != htmlNode._wikilink) {
			node = FCKXHtml.XML.createTextNode( '((' + htmlNode._wikilink + '|' + htmlNode.innerHTML + '))' ) ;
		} else {
			node = FCKXHtml.XML.createTextNode( '((' + htmlNode._wikilink + '))' ) ;
		}
	} else {
		FCKXHtml._AppendChildNodes( node, htmlNode, false ) ;
	}
	return node ;
}

