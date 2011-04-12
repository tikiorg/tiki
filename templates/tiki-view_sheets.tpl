{title help="Spreadsheet"}{$title|escape}{/title}

<div class="description">
	{$description|escape}
</div>
<div class="msg">
	{$msg}
</div>

{foreach from=$grid_content item=thisGrid}
	<div class="tiki_sheet"
		{if !empty($tiki_sheet_div_style)} 
			style="{$tiki_sheet_div_style}"
		{/if}>{$thisGrid}</div>
{/foreach}
<div id="feedback" style="height: 1.5em; margin-left: .2em"><span></span></div>

<div class="navbar">
	<span id="saveState">
		{button _id="save_button" _text="{tr}Save{/tr}" _htmlelement="role_main" _template="tiki-view_sheets.tpl" sheetId="$sheetId" _class="" _title="{tr}Tiki Sheet{/tr} | {tr}Save current spreadsheet{/tr}"}
		{button _id="cancel_button" _text="{tr}Cancel{/tr}" _htmlelement="role_main" _template="tiki-view_sheets.tpl" sheetId="$sheetId" _class="" _title="{tr}Tiki Sheet{/tr} | {tr}Cancel editing current spreadsheet{/tr}"}
	</span>
	<span id="editState">
		{if $sheetId}
			{button _id="edit_button" _text="{tr}Edit{/tr}" _htmlelement="role_main" _template="tiki-view_sheets.tpl" parse="edit" _auto_args="*"  _class="" _title="{tr}New jQuery.sheet based editing{/tr}"}
			
			{if $tiki_p_view_sheet eq 'y' || $tiki_p_admin eq 'y'}
				{button href="tiki-sheets.php" _text="{tr}List Sheets{/tr}"}
			{/if}
		
			{if $objectperms->edit_sheet}
				{if $editconflict eq 'y'}
					{assign var="uWarning" value="&lt;br /&gt;{tr}Already being edited by{/tr} $semUser"}
				{else}
					{assign var="uWarning" value=""}
				{/if}
		
				{jq notonready=true}var editSheetButtonLabel2="{tr}Cancel{/tr}";{/jq}
				
				{if $prefs.feature_contribution eq 'y'}
					{include file='contribution.tpl'}
				{/if}
			{/if}
			
			{if $parseValues eq 'y'}
				{if $parse eq 'y'}
					{button parse="n" _text="{tr}No parse{/tr}"  _htmlelement="role_main" _template="tiki-view_sheets.tpl" sheetId="$sheetId" _auto_args="*"}
				{else}
					{button parse="y" _text="{tr}Parse{/tr}"  _htmlelement="role_main" _template="tiki-view_sheets.tpl" sheetId="$sheetId" _auto_args="*"}
				{/if}
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
		{/if}
	</span>
</div>

<div id="sheetTools" style="display: none;">
	<div style="text-align: left;">{toolbars area_id="jSheetControls_formula_0"}</div>
</div>

<div class="switchSheet" style="display: none;" title="{tr}What would you like to add?{/tr}">
	<input class="newSpreadsheet" type="button" value="{tr}New spreadsheet{/tr}" style="width: 100%;" /><br />
	<input class="addSpreadsheet" type="button" value="{tr}Existing spreadsheet{/tr}" style="width: 100%;" /><br />
	<input class="addTracker" type="button" value="{tr}Tracker as a spreadsheet{/tr}" style="width: 100%;" /><br />
	<input class="addFile" type="button" value="{tr}Spreadsheet from file gallery{/tr}" style="width: 100%;" />
</div>
