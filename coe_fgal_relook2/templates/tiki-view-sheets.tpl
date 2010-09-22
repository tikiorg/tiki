{* $Id$ *}

{title help="Spreadsheet"}{$title}{/title}

<div class="description">
	{$description|escape}
</div>

{if $page_mode eq 'edit'}
	{if $editconflict eq 'y'}
		<script type="text/javascript">
		<!--//--><![CDATA[//><!--
		alert("{tr}This page is being edited by{/tr} {$semUser}. {tr}Proceed at your own peril{/tr}.")
		//--><!]]>
		</script>
	{/if}
	<div class="navbar">
			{button href="#" _onclick="insertRowClick();return false;" _text="{tr}Insert Row{/tr}"}
			{button href="#" _onclick="insertColumnClick();return false;" _text="{tr}Insert Column{/tr}"}
			{button href="#" _onclick="removeRowClick();return false;" _text="{tr}Remove Row{/tr}"}
			{button href="#" _onclick="removeColumnClick();return false;" _text="{tr}Remove Column{/tr}"}
			{button href="#" _onclick="mergeCellClick();return false;" _text="{tr}Merge Cells{/tr}"}
			{button href="#" _onclick="restoreCellClick();return false;" _text="{tr}Restore Cells{/tr}"}
			{button href="#" _onclick="copyCalculationClick();return false;" _text="{tr}Copy Calculation{/tr}"}
			{button href="#" _onclick="formatCellClick();return false;" _text="{tr}Format Cell{/tr}"}
		<div id="detail"></div>
	</div>
	<form method="post" action="tiki-view_sheets.php?sheetId={$sheetId}" id="Grid"></form>
	<div class='submit'>
		<input type="submit" onclick='g.target.style.visibility = "hidden"; g.prepareSubmit(); g.target.submit();' value="{tr}Save{/tr}" />
		{button sheetId="$sheetId" _text="{tr}Cancel{/tr}" _ajax="n"}
	</div>
	<script type="text/javascript" src="lib/sheet/grid.js"></script>
	<script type="text/javascript" src="lib/sheet/control.js"></script>
	<script type="text/javascript" src="lib/sheet/formula.js"></script>
	{jq}
	var g;
{{$init_grid}}

	controlInsertRowBefore = '<form name="insert" onsubmit="return insertRowSubmit(this)"><input type="radio" name="pos" value="before" checked="checked" id="sht_ins_row_before" /> <label for="sht_ins_row_before">{tr}Before{/tr}</label> <input type="radio" name="pos" value="after" id="sht_ins_row_after" /> <label for="sht_ins_row_after">{tr}After{/tr}</label> <select name="row">';
	controlInsertRowAfter = '</select><input type="text" name="qty" value="1" size="2" /><input type="submit" name="submit" value="{tr}Insert Row{/tr}" /></form>';
	
	controlInsertColumnBefore = '<form name="insert" onsubmit="return insertColumnSubmit(this)"><input type="radio" name="pos" value="before" checked="checked" id="sht_ins_col_before" /> <label for="sht_ins_col_before">{tr}Before{/tr}</label> <input type="radio" name="pos" value="after" id="sht_ins_col_after" /> <label for="sht_ins_col_after">{tr}After{/tr}</label> <select name="column">';
	controlInsertColumnAfter = '</select><input type="text" name="qty" value="1" size="2" /><input type="submit" name="submit" value="{tr}Insert Column{/tr}" /></form>';

	controlRemoveRowBefore = '<form name="remove" onsubmit="return removeRowSubmit(this)"><select name="row">';
	controlRemoveRowAfter = '</select><input type="submit" name="submit" value="{tr}Remove Row{/tr}" /></form>';

	controlRemoveColumnBefore = '<form name="remove" onsubmit="return removeColumnSubmit(this)"><select name="column">';
	controlRemoveColumnAfter = '</select><input type="submit" name="submit" value="{tr}Remove Column{/tr}" /></form>';
	controlCopyCalculation = '<form name="copy" onsubmit="copyCalculationSubmit(this)"><input type="submit" name="type" value="Left" onclick="document.copy.clicked.value = this.value;" /><input type="submit" name="type" value="Right" onclick="document.copy.clicked.value = this.value;" /><input type="submit" name="type" value="Up" onclick="document.copy.clicked.value = this.value;" /><input type="submit" name="type" value="Down" onclick="document.copy.clicked.value = this.value;" /><input type="hidden" name="clicked" /></form>';
	initGrid();
	controlFormatCellBefore = '<form name="format" onsubmit="return formatCellSubmit(this)"><select name="format"><option value="">None</option>';
	controlFormatCellAfter = '</select><input type="submit" name="submit" value="{tr}Format Cell{/tr}" /></form>';
	{/jq}

{else}
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
			{if $prefs.feature_jquery_sheet eq "y"}
				{if $editconflict eq 'y'}
					{assign var="uWarning" value="&lt;br /&gt;{tr}Already being edited by{/tr} $semUser"}
				{else}
					{assign var="uWarning" value=""}
				{/if}
				{if $editReload}
					{button _id="edit_button" _text="{tr}Edit{/tr}" _htmlelement="role_main" _template="tiki-view-sheets.tpl" sheetId="$sheetId" _class="" parse="edit" editSheet="y" _auto_args="*" _title="{tr}New jQuery.sheet based editing{/tr}"|cat:$uWarning}
				{else}
					{button _id="save_button" _text="{tr}Save{/tr}" _htmlelement="role_main" _template="tiki-view-sheets.tpl" sheetId="$sheetId" _class="" _title="{tr}Tiki Sheet{/tr} | {tr}Save current spreadsheet{/tr}"}
					{button _id="edit_button" _text="{tr}Edit{/tr}" _htmlelement="role_main" _template="tiki-view-sheets.tpl" sheetId="$sheetId" _class="" _title="{tr}New jQuery.sheet based editing{/tr}"|cat:$uWarning}
					{jq notonready=true}var editSheetButtonLabel2="{tr}Cancel{/tr}";{/jq}
					{if $prefs.feature_contribution eq 'y'}
						{include file='contribution.tpl'}
					{/if}
				{/if}
			{else}
				{if $editconflict eq 'y'}
					{button sheetId="$sheetId" readdate="$read_date" mode="edit" _title="$semUser" _text="{tr}Edit{/tr}" _ajax="n"}
				{else}
					{button sheetId="$sheetId" readdate="$read_date" mode="edit" _text="{tr}Edit{/tr}" _ajax="n"}
				{/if}
			{/if}
		{/if}
		
		{if $parseValues eq 'y'}
			{if $smarty.request.parse eq 'y'}
				{button parse="n" _text="{tr}No parse{/tr}"  _htmlelement="role_main" _template="tiki-view-sheets.tpl" sheetId="$sheetId" _auto_args="*"}
			{else}
				{button parse="y" _text="{tr}Parse{/tr}"  _htmlelement="role_main" _template="tiki-view-sheets.tpl" sheetId="$sheetId" _auto_args="*"}
			{/if}
		{/if}
		{if $smarty.request.simple eq 'y'}
			{button simple="n" _text="{tr}Spreadsheet{/tr}"  _htmlelement="role_main" _template="tiki-view-sheets.tpl" sheetId="$sheetId" _auto_args="*"}
		{else}
			{button simple="y" _text="{tr}Simple{/tr}"  _htmlelement="role_main" _template="tiki-view-sheets.tpl" sheetId="$sheetId" _auto_args="*"}
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

		{if $objectperms->edit_sheet}
			{if $prefs.feature_jquery_sheet eq "y"}{* temporary button to edit the previous way *}
				<br /><br /><br />
				{remarksbox type="note" icon="bricks" title="jQuery.sheet under development"}
					Temporary "edit the old way" during jQuery.sheet development<br />
					{if $editconflict eq 'y'}
						{button sheetId="$sheetId" readdate="$read_date" mode="edit" _title="$semUser" _text="{tr}Tiki-Sheet Edit{/tr}" _ajax="n"}
					{else}
						{button sheetId="$sheetId" readdate="$read_date" mode="edit" _text="{tr}Tiki-Sheet Edit{/tr}" _ajax="n"}
					{/if}
				{/remarksbox}
			{/if}
		{/if}

	</div>
	<div id="sheetTools" style="display: none;"><div style="text-align: left;">{toolbars area_id="jSheetControls_formula_0"}</div></div>
{/if}
