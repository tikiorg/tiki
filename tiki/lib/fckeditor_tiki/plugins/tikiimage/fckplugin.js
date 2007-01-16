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
	'tikiimage',
	new FCKDialogCommand(
		'tikiimage',
		FCKConfig.tikiimageDlgTitle,
		FCKConfig.BasePath + 'dialog/fck_image.html',
		400,
		400
	)
);
 
var otikiimageItem = new FCKToolbarButton( 'tikiimage', FCKConfig.tikiimageBtn, null, null, false, true ); 
otikiimageItem.IconPath = _TikiRoot + 'pics/icons/page_white_picture.png'; 

FCKToolbarItems.RegisterItem( 'tikiimage', otikiimageItem );

var FCKTikiImages = new Object() ;

FCKTikiImages.add = function( sSrc, sHeight, sWidth, sLink, sAlign, sDesc, sImalign, sAlt, sUsemap, sClass ) {
	var oImg = FCK.CreateElement( 'IMG' ) ;
	this.SetupImage( oImg, sSrc, sHeight, sWidth, sLink, sAlign, sDesc, sImalign, sAlt, sUsemap, sClass ) ;
}

FCKTikiImages.SetupImage  = function( img, sSrc, sHeight, sWidth, sLink, sAlign, sDesc, sImalign, sAlt, sUsemap, sClass ) {
	img.contentEditable = 'false' ;
	if ( sClass ) img.className = sClass ;
	if ( sHeight ) img.height = sHeight ;
	if ( sWidth ) img.width = sWidth ;
	if ( sAlign ) img.align = sAlign ;
	if ( sAlt ) img.alt = sAlt ;
	img.src = sSrc ;
	img._tikiimage = true ;
	img.onresizestart = function() {
		FCK.EditorWindow.event.returnValue = false ;
		return false ;
	}
}

FCKTikiImages._SetupClickListener = function() {
	FCKTikiImages._ClickListener = function( e ) {
		if ( e.target.tagName == 'IMG' && e.target.src ) {
			FCKSelection.SelectNode( e.target ) ;
		}
	}
	FCK.EditorDocument.addEventListener( 'click', FCKTikiImages._ClickListener, true ) ;
}

FCKTikiImages.OnDoubleClick = function( img ) {
	if ( img.tagName == 'IMG' && img.src ) {
		FCKCommands.GetCommand( 'tikiimage' ).Execute() ;
	}
}

FCK.RegisterDoubleClickHandler( FCKTikiImages.OnDoubleClick, 'IMG' ) ;

FCKTikiImages.Exist = function( name ) {
	var aImg = FCK.EditorDocument.getElementsByTagName( 'IMG' ) ;
	for ( var i = 0 ; i < aImg.length ; i++ ) {
		if ( aImg[i]._tikiimage ) {
			return true ;
		}
	}
}

if ( FCKBrowserInfo.IsIE ) {
	FCKTikiImages.Redraw = function() {
		var aImgs = FCK.EditorDocument.body.innerText.match( /(\{img\s*[^\}]*\})/g ) ;
		if ( !aImgs ) {
			return ;
		}
		var oRange = FCK.EditorDocument.body.createTextRange() ;
		for ( var i = 0 ; i < aImgs.length ; i++ ) {
			if ( oRange.findText( aImgs[i] ) ) {
				var sImg = aImgs[i].match( /{img\s*([^\}]*)\}/ )[0].split(' ') ;
				var sSrc = '' ;
				var sClass  = '' ;
				var sHeight  = '' ;
				var sWidth  = '' ;
				var sAlign  = '' ;
				for ( var j = 0 ; j < sImg.length ; j++ ) {
					if ( sImg[j].indexOf( '=' ) != -1 ) {
						var lParam = sImg[j].split('=')[0] ;
						var lValue = sImg[j].split('=')[1] ;
						if ( lParam == 'src') {
							sSrc = lValue ;
						} else if ( lParam == 'height' ) {
							sHeight = lValue ;
						} else if ( lParam == 'width' ) {
							sWidth = lValue ;
						} else if ( lParam == 'align' ) {
							sAlign = lValue ;
						} else if ( lParam == 'class' ) {
							sClass = lValue ;
						}
					}
				}
				if ( sSrc ) {
					var extra = '' ;
					if ( sHeight ) extra = extra + ' height="' + sHeight + '"' ;
					if ( sWidth ) extra = extra + ' width="' + sWidth + '"' ;
					if ( sClass ) extra = extra + ' class="' + sClass + '"' ;
					if ( sAlign ) extra = extra + ' align="' + sAlign + '"' ;
					oRange.pasteHTML( '<img src="' + sSrc + '" ' + extra + 'contenteditable="false" _tikiimage="true" />' );
				}
			}
		}
	}
} else {
	FCKTikiImages.Redraw = function() {
		var oInteractor = FCK.EditorDocument.createTreeWalker( FCK.EditorDocument.body, NodeFilter.SHOW_TEXT, FCKTikiImages._AcceptNode, true ) ;
		var aNodes = new Array() ;
		while ( oNode = oInteractor.nextNode() ) {
			aNodes[ aNodes.length ] = oNode ;
		}
		for ( var n = 0 ; n < aNodes.length ; n++ ) {
			var aPieces = aNodes[n].nodeValue.split( /(\{img\s*[^\}]*\})/ );
			for ( var i = 0 ; i < aPieces.length ; i++ ) {
				if ( aPieces[i].length > 0 ) {
					if ( aPieces[i].indexOf( '{img ' ) == 0 ) {
						var sImg = aPieces[i].match( /\{img\s*([^\}]*)\}/ )[1].split(' ') ;
						var sSrc = '' ;
						var sHeight = '' ;
						var sWidth = '' ;
						var sLink = '' ;
						var sAlign = '' ;
						var sDesc = '' ;
						var sImalign = '' ;
						var sAlt = '' ;
						var sUsemap = '' ;
						var sClass = '' ;
						for ( var j = 0 ; j < sImg.length ; j++ ) {
							if ( sImg[j].indexOf( '=' ) != -1 ) {
							var lParam = sImg[j].split('=')[0] ;
							var lValue = sImg[j].split('=')[1] ;
							if ( lParam == 'src') {
								sSrc = lValue ;
							} else if ( lParam == 'height' ) {
								sHeight = lValue ;
							} else if ( lParam == 'width' ) {
								sWidth = lValue ;
							} else if ( lParam == 'align' ) {
								sAlign = lValue ;
							} else if ( lParam == 'alt' ) {
								sAlt = lValue ;
							} else if ( lParam == 'link' ) {
							} else if ( lParam == 'desc' ) {
							} else if ( lParam == 'imalign' ) {
								sAlign = lValue ;
							} else if ( lParam == 'usemap' ) {
							} else if ( lParam == 'class' ) {
							}
							}
						}
						var oImg = FCK.EditorDocument.createElement( 'img' ) ;
						FCKTikiImages.SetupImage( oImg, sSrc, sHeight, sWidth, sLink, sAlign, sDesc, sImalign, sAlt, sUsemap, sClass ) ;
						aNodes[n].parentNode.insertBefore( oImg, aNodes[n] ) ;
					} else {
						aNodes[n].parentNode.insertBefore( FCK.EditorDocument.createTextNode( aPieces[i] ) , aNodes[n] ) ;
					}
				}
			}
			aNodes[n].parentNode.removeChild( aNodes[n] ) ;
		}
		FCKTikiImages._SetupClickListener() ;
	}
	FCKTikiImages._AcceptNode = function( node ) {
		if ( /\{img\s/.test( node.nodeValue ) ) {
			return NodeFilter.FILTER_ACCEPT ;
		} else {
			return NodeFilter.FILTER_SKIP ;
		}
	}
}

FCK.Events.AttachEvent( 'OnAfterSetHTML', FCKTikiImages.Redraw ) ;

FCKXHtml.TagProcessors['img'] = function( node, htmlNode ) {
	if ( htmlNode.src ) {
		var extra = '' ;
		if ( htmlNode.height ) extra = extra + ' height=' + htmlNode.height ;
		if ( htmlNode.width ) extra = extra + ' width=' + htmlNode.width ;
		if ( htmlNode.align ) {
			if ( htmlNode.align == 'left' || htmlNode.align == 'right' ) {
				extra = extra + ' imalign=' + htmlNode.align ;
			} else {
				extra = extra + ' align=' + htmlNode.align ;
			}
		}
		if ( htmlNode.alt ) extra = extra + ' alt=' + htmlNode.alt ;
		node = FCKXHtml.XML.createTextNode( '{img src=' + htmlNode.src + ' ' + extra + '}' ) ;
	} else {
		FCKXHtml._AppendChildNodes( node, htmlNode, false ) ;
	}
	return node ;
}

