<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>{tr}Tiki Link - Insert internal link{/tr}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta content="noindex, nofollow" name="robots">
{literal}	
<script type="text/javascript" src="fcktikilink.js"></script>
<script type="text/javascript">
<!--
var oEditor			= window.parent.InnerDialogLoaded(); 
var FCK					= oEditor.FCK; 
var FCKConfig		= oEditor.FCKConfig ;
var FCKTikiLinks = oEditor.FCKTikiLinks ;
 
// oLink: The actual selected link in the editor.
var oLink = FCK.Selection.MoveToAncestorNode( 'A' ) ;
if ( oLink ) {
	FCK.Selection.SelectNode( oLink ) ;
}

window.onload = function ()	{ 
	LoadSelected();							//See function below 
	window.parent.SetOkButton( true );		//Show the "Ok" button. 
	window.parent.SetAutoSize( true ) ;
} 
 
//If an anchor (A) object is currently selected, load the properties into the dialog 
function LoadSelected()	{
	var sSelected;
	var oLink = FCK.Selection.GetSelectedElement() ;
	if ( oEditor.FCKBrowserInfo.IsIE && oLink != null ) {
		document.getElementById( 'txtPage' ).value = oLink.getAttribute( '_wikilink' ) ;
		document.getElementById( 'txtTitle' ).value = oLink.getAttribute( 'innerHTML' ) ;
	} else {
		if ( oEditor.FCKBrowserInfo.IsGecko ) {
			sSelected = FCK.EditorWindow.getSelection();
		} else {
			sSelected = FCK.EditorDocument.selection.createRange().text;
		}
		if ( sSelected != "" ) {
			var listen = document.getElementById( 'txtTitle' );
			listen.value = sSelected;
			var listen1 = document.getElementById( 'txtPage' );
			if ( oLink != null ) { listen1.value = oLink.getAttribute( 'href' ) ; }
		}
	}
}

//Code that runs after the OK button is clicked 
function Ok() {
	var oDoc = document.getElementById( 'txtPage' );
	if(oDoc.value == "") {
		alert('Please select a page in order to create a link');
		return false;
	}
	var oLinkTitle = document.getElementById( 'txtTitle' );
	if (oLinkTitle.value == "") {
		alert('Please enter a Link Title');
		return false;
	}

	FCKTikiLinks.add ( oDoc.value, oLinkTitle.value );
	return true;
} 
{/literal}
//-->
</script>
</head>
			
	<body scroll="yes" style="overflow:hidden;">
		 <input type="hidden" id="PageURL" value="" />
		 <table height="100%" cellSpacing="0" cellPadding="0" width="100%" border="0"> 
		 	<tr> 
				<td>
					<table width="100%">
						<tr>
							<td colspan="2">{tr}Select a Wiki page to link to:{/tr}&nbsp;</td>
						</tr>
						<tr>
							<td colspan="2">
				
<table class="findtable">
<tr><td class="findtitle">{tr}Find{/tr}</td>
   <td class="findtitle">
   <form method="get" action="fck_tikilink.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" name="search" value="{tr}find{/tr}" />
   </form>
   </td>
</tr>
</table>

<div class="pageslist" style="padding:10px;border:1px solid #999;background-color:#fff;">
<div class="mini" style="padding:0 10px 5px 0px;border-bottom:1px dashed #999;font-size:90%;">
<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr><td>
{if $prev_offset >= 0}
[<a class="prevnext" href="fck_tikilink.php?find={$find}&amp;offset={$prev_offset}">{tr}prev{/tr}</a>]
{else}
<span style="color:#ccc;">[{tr}prev{/tr}]</span>
{/if}
</td>
<td align="center">
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
</td>
<td align="right">
{if $next_offset >= 0}
[<a class="prevnext" href="fck_tikilink.php?find={$find}&amp;offset={$next_offset}">{tr}next{/tr}</a>]
{else}
<span style="color:#ccc;">[{tr}next{/tr}]</span>
{/if}
</td></tr></table>
</div>

{if $prefs.direct_pagination eq 'y'}
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="fck_tikilink.php?find={$find}&amp;offset={$selector_offset}">{smarty.section.foo.index_next}</a>
{/section}
{/if}

<br />
<table cellpadding="0" cellspacing="0" border="0" width="100%">
{foreach item=page from=$listpages}
<tr><td><a href="#" onclick="javascript:document.getElementById('txtPage').value = '{$page.pageName|escape:'javascript'}';" 
title="{if $page.description}{$page.description}{else}{$page.pageName}{/if}" class="wikilink">{$page.pageName}</a>
</td><td style="color:#999;">
{$page.description}
</td></tr>
{/foreach}
</table>
</div>
							</td>
						</tr>
						<tr>
							<td nowrap>{tr}Link{/tr}&nbsp;</td>
							<td width="100%" style="align:right;"><input id="txtTitle" style="WIDTH: 98%" type="text" name="txtTitle"></td>
						</tr>
						<tr>
							<td nowrap>{tr}Page name{/tr}&nbsp;</td>
							<td width="100%" style="align:right;"><input id="txtPage" style="WIDTH: 98%" type="text" name="txtPage"></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		
	</body>
</html> 
