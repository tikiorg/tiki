/*
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * File Name: fckplugin.js
 * 	This is the Clean HTML plugin definition file.
 */
 
// Define the command.
var CleanHTML_loaded = false;
var FCKCleanHTMLCommand = function()
{
	this.Name = 'CleanHTML' ;
}

function CleanWord( html )
{
  bIgnoreFont = true;
  bRemoveStyles = true;

  html = html.replace(/<o:p>\s*<\/o:p>/g, '') ;
  html = html.replace(/<o:p>.*?<\/o:p>/g, '&nbsp;') ;

  // Remove mso-xxx styles.
  html = html.replace( /\s*mso-[^:]+:[^;"]+;?/gi, '' ) ;

  // Remove margin styles.
  html = html.replace( /\s*MARGIN: 0cm 0cm 0pt\s*;/gi, '' ) ;
  html = html.replace( /\s*MARGIN: 0cm 0cm 0pt\s*"/gi, "\"" ) ;

  html = html.replace( /\s*TEXT-INDENT: 0cm\s*;/gi, '' ) ;
  html = html.replace( /\s*TEXT-INDENT: 0cm\s*"/gi, "\"" ) ;

  html = html.replace( /\s*TEXT-ALIGN: [^\s;]+;?"/gi, "\"" ) ;

  html = html.replace( /\s*PAGE-BREAK-BEFORE: [^\s;]+;?"/gi, "\"" ) ;

  html = html.replace( /\s*FONT-VARIANT: [^\s;]+;?"/gi, "\"" ) ;

  html = html.replace( /\s*tab-stops:[^;"]*;?/gi, '' ) ;
  html = html.replace( /\s*tab-stops:[^"]*/gi, '' ) ;

  // Remove FONT face attributes.
  if ( bIgnoreFont )
  {
    html = html.replace( /\s*face="[^"]*"/gi, '' ) ;
    html = html.replace( /\s*face=[^ >]*/gi, '' ) ;

    html = html.replace( /\s*FONT-FAMILY:[^;"]*;?/gi, '' ) ;
  }

  // Remove Class attributes
  html = html.replace(/<(\w[^>]*) class=([^ |>]*)([^>]*)/gi, "<$1$3") ;

  // Remove styles.
  if ( bRemoveStyles )
    html = html.replace( /<(\w[^>]*) style="([^\"]*)"([^>]*)/gi, "<$1$3" ) ;

  // Remove empty styles.
  html =  html.replace( /\s*style="\s*"/gi, '' ) ;

  html = html.replace( /<SPAN\s*[^>]*>\s*&nbsp;\s*<\/SPAN>/gi, '&nbsp;' ) ;
  html = html.replace( /<SPAN\s*[^>]*><\/SPAN>/gi, '' ) ;

  // Remove Lang attributes
  html = html.replace(/<(\w[^>]*) lang=([^ |>]*)([^>]*)/gi, "<$1$3") ;

  html = html.replace( /<SPAN\s*>(.*?)<\/SPAN>/gi, '$1' ) ;

  html = html.replace( /<FONT\s*>(.*?)<\/FONT>/gi, '$1' ) ;

  // Remove XML elements and declarations
  html = html.replace(/<\\?\?xml[^>]*>/gi, '' ) ;

  // Remove Tags with XML namespace declarations: <o:p><\/o:p>
  html = html.replace(/<\/?\w+:[^>]*>/gi, '' ) ;

  // Remove comments [SF BUG-1481861].
  html = html.replace(/<\!--.*?-->/mg, '' ) ;

  html = html.replace( /<(U|I|STRIKE)>&nbsp;<\/\1>/g, '&nbsp;' ) ;

  html = html.replace( /<H\d>\s*<\/H\d>/gi, '' ) ;

  // Remove "display:none" tags.
  html = html.replace( /<(\w+)[^>]*\sstyle="[^"]*DISPLAY\s?:\s?none(.*?)<\/\1>/ig, '' ) ;

  // Remove language tags
  html = html.replace( /<(\w[^>]*) language=([^ |>]*)([^>]*)/gi, "<$1$3") ;

  // Remove onmouseover and onmouseout events (from MS Word comments effect)
  html = html.replace( /<(\w[^>]*) onmouseover="([^\"]*)"([^>]*)/gi, "<$1$3") ;
  html = html.replace( /<(\w[^>]*) onmouseout="([^\"]*)"([^>]*)/gi, "<$1$3") ;

  if ( FCKConfig.CleanWordKeepsStructure )
  {
    // The original <Hn> tag send from Word is something like this: <Hn style="margin-top:0px;margin-bottom:0px">
    html = html.replace( /<H(\d)([^>]*)>/gi, '<h$1>' ) ;

    // Word likes to insert extra <font> tags, when using MSIE. (Wierd).
    html = html.replace( /<(H\d)><FONT[^>]*>(.*?)<\/FONT><\/\1>/gi, '<$1>$2<\/$1>' );
    html = html.replace( /<(H\d)><EM>(.*?)<\/EM><\/\1>/gi, '<$1>$2<\/$1>' );
  }
  else
  {
    html = html.replace( /<H1([^>]*)>/gi, '<div$1><b><font size="6">' ) ;
    html = html.replace( /<H2([^>]*)>/gi, '<div$1><b><font size="5">' ) ;
    html = html.replace( /<H3([^>]*)>/gi, '<div$1><b><font size="4">' ) ;
    html = html.replace( /<H4([^>]*)>/gi, '<div$1><b><font size="3">' ) ;
    html = html.replace( /<H5([^>]*)>/gi, '<div$1><b><font size="2">' ) ;
    html = html.replace( /<H6([^>]*)>/gi, '<div$1><b><font size="1">' ) ;

    html = html.replace( /<\/H\d>/gi, '<\/font><\/b><\/div>' ) ;

    // Transform <P> to <DIV>
    var re = new RegExp( '(<P)([^>]*>.*?)(<\/P>)', 'gi' ) ; // Different because of a IE 5.0 error
    html = html.replace( re, '<div$2<\/div>' ) ;

    // Remove empty tags (three times, just to be sure).
    // This also removes any empty anchor
    html = html.replace( /<([^\s>]+)(\s[^>]*)?>\s*<\/\1>/g, '' ) ;
    html = html.replace( /<([^\s>]+)(\s[^>]*)?>\s*<\/\1>/g, '' ) ;
    html = html.replace( /<([^\s>]+)(\s[^>]*)?>\s*<\/\1>/g, '' ) ;
  }
  // Remove comments
  html = html.replace( /<\!--.*-->/mg, '' ) ;

  return html ;
}


FCKCleanHTMLCommand.prototype.Execute = function()
{
	var DOMDocument = FCK.EditorDocument ;
	// The are two diffent browser specific ways to get the text.
	// I also use a trick to count linebreaks (<br>/</p>) as one-stroke.
	if ( FCKBrowserInfo.IsIE ) {
		var HTMLText = DOMDocument.body.innerHTML ;
    DOMDocument.body.innerHTML = CleanWord (HTMLText);
	} else {
		var r = DOMDocument.createRange() ;
		r.selectNodeContents( DOMDocument.body ) ;
		var HTMLText = r.startContainer.innerHTML ;
    r.startContainer.innerHTML =  CleanWord (HTMLText);
	}
}

FCKCleanHTMLCommand.prototype.GetState = function()
{
	return FCK_TRISTATE_OFF ;
}

// Register the related command.
FCKCommands.RegisterCommand( 'CleanHTML', new FCKCleanHTMLCommand( ) ) ;

// Create the "CleanHTML" toolbar button.
var oCleanHTMLItem = new FCKToolbarButton( 'CleanHTML', 'CleanHTML') ;
oCleanHTMLItem.IconPath = FCKPlugins.Items['CleanHTML'].Path + 'html.png';
FCKToolbarItems.RegisterItem( 'CleanHTML', oCleanHTMLItem ) ;

// Define the event handler.
function CleanHTMLEventHandler()
{
FCKCommands.GetCommand( 'CleanHTML' ).Execute() ;
}
