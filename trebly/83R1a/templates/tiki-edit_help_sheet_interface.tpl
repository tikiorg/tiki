{* $Id: tiki-edit_help_sheet_interface.tpl 36526 2011-08-26 17:01:52Z robertplummer $ *}
{* \brief Show spreadsheet help (for jQuery.sheet) 
{* included by toolbarslib *}

{add_help show='n' id="sheet_help_interface" title="{tr}Spreadsheet Interface{/tr}"}
	<h3>{tr}Spreadsheet{/tr}</h3>
	<div class="help_section">	
		<p>
			{tr}Tiki uses jQuery.sheet for displaying spreadsheets, below you will see a list of usable functions and how to enhance how you use it.{/tr}
		</p>
		
		<object id="sheet_help_interface" style="height: 300px; width: 100% !important" data="lib/jquery.sheet/sheets/enduser.documentation.html"></object>
	</div>
{/add_help}
