{* $Id$ *}

{title help="Spreadsheet"}{$title}{/title}

<div class="description">
	{$description|escape}
</div>

{foreach from=$grid_content item=thisGrid}
	<div class="tiki_sheet"
		{if !empty($tiki_sheet_div_style)} 
			style="{$tiki_sheet_div_style}"
		{/if}>{$thisGrid}</div>
{/foreach}
<div id="feedback" style="height: 1.5em; margin-left: .2em"><span></span></div>

<div class="navbar">
	{if $tiki_p_view_sheet eq 'y' || $tiki_p_admin eq 'y'}
		{button href="tiki-sheets.php" _text="{tr}List Sheets{/tr}"}
	{/if}

	{if $objectperms->edit_sheet}
		{if $editconflict eq 'y'}
			{assign var="uWarning" value="&lt;br /&gt;{tr}Already being edited by{/tr} $semUser"}
		{else}
			{assign var="uWarning" value=""}
		{/if}
		{if $editReload}
			{button _id="edit_button" _text="{tr}Edit{/tr}" _htmlelement="role_main" _template="tiki-view_sheets.tpl" sheetId="$sheetId" _class="" parse="edit" editSheet="y" _auto_args="*" _title="{tr}New jQuery.sheet based editing{/tr}"|cat:$uWarning}
		{else}
			{button _id="save_button" _text="{tr}Save{/tr}" _htmlelement="role_main" _template="tiki-view_sheets.tpl" sheetId="$sheetId" _class="" _title="{tr}Tiki Sheet{/tr} | {tr}Save current spreadsheet{/tr}"}
			{button _id="edit_button" _text="{tr}Edit{/tr}" _htmlelement="role_main" _template="tiki-view_sheets.tpl" sheetId="$sheetId" _class="" _title="{tr}New jQuery.sheet based editing{/tr}"|cat:$uWarning}
			{jq notonready=true}var editSheetButtonLabel2="{tr}Cancel{/tr}";{/jq}
			{if $prefs.feature_contribution eq 'y'}
				{include file='contribution.tpl'}
			{/if}
		{/if}
	{/if}
	
	{if $parseValues eq 'y'}
		{if $smarty.request.parse eq 'y'}
			{button parse="n" _text="{tr}No parse{/tr}"  _htmlelement="role_main" _template="tiki-view_sheets.tpl" sheetId="$sheetId" _auto_args="*"}
		{else}
			{button parse="y" _text="{tr}Parse{/tr}"  _htmlelement="role_main" _template="tiki-view_sheets.tpl" sheetId="$sheetId" _auto_args="*"}
		{/if}
	{/if}
	{if $smarty.request.simple eq 'y'}
		{button simple="n" _text="{tr}Spreadsheet{/tr}"  _htmlelement="role_main" _template="tiki-view_sheets.tpl" sheetId="$sheetId" _auto_args="*"}
	{else}
		{button simple="y" _text="{tr}Simple{/tr}"  _htmlelement="role_main" _template="tiki-view_sheets.tpl" sheetId="$sheetId" _auto_args="*"}
	{/if}

	{if $objectperms->view_sheet_history}
		{button href="tiki-history_sheets.php?sheetId=$sheetId" _text="{tr}History{/tr}"}
	{/if}

	{if  $objectperms->view_sheet}
		{button href="tiki-export_sheet.php?sheetId=$sheetId" _text="{tr}Export{/tr}"}
	{/if}

	{if  $objectperms->edit_sheet}
		{button href="tiki-import_sheet.php?sheetId=$sheetId" _text="{tr}Import{/tr}"}
	{/if}

	{if $chart_enabled eq 'y'}
		{button href="tiki-graph_sheet.php?sheetId=$sheetId" _text="{tr}Graph{/tr}"}
	{/if}

</div>
<div id="sheetTools" style="display: none;"><div style="text-align: left;">{toolbars area_id="jSheetControls_formula_0"}</div></div>
