{* $Id: *}
{* \brief Show spreadsheet help (for jQuery.sheet) 
 * included by toolbarslib *}

{add_help show='n' id="sheet_help" title="{tr}Spreadsheet Help{/tr}"}

{jq}
setTimeout(function(){	// have to wait otherwise this one also gets id=0
	$('#jQuerySheet_help').sheet({
		title: 'Enduser Documentation',
		editable: false,
		urlGet: 'lib/jquery/jquery.sheet/sheets/enduser.documentation.html',
		resizable: false
	});
}, 1000);
{/jq}

<h3>{tr}Spreadsheet{/tr}</h3>
<div class="help_section">
<p>{tr}jQuery.sheet spreadsheets{/tr}</p>
<p>{tr}What is jQuery.sheet?{/tr}</p>
<p>{tr}jQuery.sheet gives you all sorts of possibilities when it comes to giving your web application a spreadsheet style interface with MS Excel style calculations.{/tr}</p>

<p>{tr}jQuery.sheet manages the sheet creation, viewing, and editing processes.  It can even be used like a datagrid, without calculations.
For a complete list of all the MS Excel style functions that jQuery.sheet supports, see below.{/tr}</p>

<div id="jQuerySheet_help" style="height: 450px !important; width: 400px !important"></div>
<!-- (ugly) <iframe id="jQuerySheet_help" style="height: 450px; width: 100% !important" src="lib/jquery/jquery.sheet/sheets/enduser.documentation.html"></iframe>-->

</div>

{/add_help}
