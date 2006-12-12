<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>{tr}Tiki Link - Insert internal link{/tr}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta content="noindex, nofollow" name="robots">
{literal}	
<script type="text/javascript" src="fcktikilink.js"></script>
<script type = "text/javascript">
function addInfo(myString) {
	var listen = document.getElementById("PageURL") ;
	var listen1 = document.getElementById("txtSelection") ;
	listen.value = myString;
	listen1.value = myString;
}
</script>
<script type="text/javascript">
<!--
var oEditor			= window.parent.InnerDialogLoaded(); 
var FCK					= oEditor.FCK; 
var FCKLang			= oEditor.FCKLang ;
var FCKConfig		= oEditor.FCKConfig ;

 
// oLink: The actual selected link in the editor.
var oLink = FCK.Selection.MoveToAncestorNode( 'A' ) ;
if ( oLink ) {
	FCK.Selection.SelectNode( oLink ) ;
}

window.onload = function ()	{ 
	LoadSelected();							//See function below 
	window.parent.SetOkButton( true );		//Show the "Ok" button. 
} 
 
//If an anchor (A) object is currently selected, load the properties into the dialog 
function LoadSelected()	{
	var sSelected;
	if ( oEditor.FCKBrowserInfo.IsGecko ) {
		sSelected = FCK.EditorWindow.getSelection();
	} else {
		sSelected = FCK.EditorDocument.selection.createRange().text;
	}
	if ( sSelected != "" ) {
		var listen = document.getElementById("txtTitle");
		listen.value = sSelected;
	}
}

//Code that runs after the OK button is clicked 
function Ok() {
	var oDoc = document.getElementById( 'PageURL' );
	if(oDoc.value == "") {
		alert('Please select a page in order to create a link');
		return false;
	}
	var oLinkTitle = document.getElementById( 'txtTitle' );
	if (oLinkTitle.value == "") {
		alert('Please enter a Link Title');
		return false;
	}
	var sURL = document.getElementById( 'PageURL' ) ; 
	var sTagOutput = '<a href="' + sURL.value + '" class="wiki">' + document.getElementById( "txtTitle").value + '</a>';
	oEditor.FCK.InsertHtml( sTagOutput );

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
				
<div class="pageslist">
{foreach item=page from=$listpages}
<a href="javascript:addInfo('tiki-index.php?page={$page.pageName|escape:'javascript'}');" 
title="{if $page.description}{$page.description}{else}{$page.pageName}{/if}" class="wikilink">{$page.pageName}</a><br />
{/foreach}
</div>
							</td>
						</tr>
						<tr>
							<td nowrap>{tr}Title{/tr}&nbsp;</td>
							<td width="100%" style="align:right;"><input id="txtTitle" style="WIDTH: 98%" type="text" name="txtTitle"></td>
						</tr>
						<tr>
							<td nowrap>{tr}Selection{/tr}&nbsp;</td>
							<td width="100%" style="align:right;"><input id="txtSelection" disabled="true" style="WIDTH: 98%" type="text" name="txtSelection"></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		
	</body>
</html> 
