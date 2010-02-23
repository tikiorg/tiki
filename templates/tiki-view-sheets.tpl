{* $Id$ *}
<link rel="stylesheet" href="lib/sheet/style.css" type="text/css" />{* this shouldn't be here; links to CSS only allowed in head html tag !!! (luci) *}
{* here is missing body tag when above link to CSS remains!!! (luci) *}

{title help="Spreadsheet"}{$title}{/title}

<div>
	{$description}
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
	<form method="post" action="tiki-view_sheets.php?mode=edit&sheetId={$sheetId}" id="Grid"></form>
	<div class='submit'>
		<input type="submit" onclick='g.target.style.visibility = "hidden"; g.prepareSubmit(); g.target.submit();' value="{tr}Save{/tr}" />
		{button sheetId="$sheetId" _text="{tr}Cancel{/tr}" _ajax="n"}
	</div>
	<script type="text/javascript" src="lib/sheet/grid.js"></script>
	<script type="text/javascript" src="lib/sheet/control.js"></script>
	<script type="text/javascript" src="lib/sheet/formula.js"></script>
	<script type="text/javascript">
	<!--//--><![CDATA[//><!--
	var g;
{$init_grid}

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
	//--><!]]>
	</script>

{else}
	{$grid_content}
	<div class="navbar">
		{if $tiki_p_view_sheet eq 'y' || $tiki_p_admin_sheet eq 'y' || $tiki_p_admin eq 'y'}
			{button href="tiki-sheets.php" _text="{tr}List Sheets{/tr}"}
		{/if}
	
		{if $tiki_p_edit_sheet eq 'y' || $tiki_p_admin_sheet eq 'y' || $tiki_p_admin eq 'y'}
			{if $editconflict eq 'y'}
				{button sheetId="$sheetId" readdate="$read_date" mode="edit" _title="$semUser" _text="{tr}Edit{/tr}" _ajax="n"}
			{else}
				{button sheetId="$sheetId" readdate="$read_date" mode="edit" _text="{tr}Edit{/tr}" _ajax="n"}
			{/if}
		{/if}

		{if $tiki_p_view_sheet_history eq 'y' || $tiki_p_admin_sheet eq 'y' || $tiki_p_admin eq 'y'}
			{button href="tiki-history_sheets.php?sheetId=$sheetId" _text="{tr}History{/tr}"}
		{/if}

		{if $tiki_p_view_sheet eq 'y' || $tiki_p_admin_sheet eq 'y' || $tiki_p_admin eq 'y'}
			{button href="tiki-export_sheet.php?sheetId=$sheetId" _text="{tr}Export{/tr}"}
		{/if}

		{if $tiki_p_edit_sheet eq 'y' || $tiki_p_admin_sheet eq 'y' || $tiki_p_admin eq 'y'}
			{button href="tiki-import_sheet.php?sheetId=$sheetId" _text="{tr}Import{/tr}"}
		{/if}

		{if $chart_enabled eq 'y'}
			{button href="tiki-graph_sheet.php?sheetId=$sheetId" _text="{tr}Graph{/tr}"}
		{/if}
	</div>
{/if}
