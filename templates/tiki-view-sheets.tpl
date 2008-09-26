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
	<div id="panel">
		<menu>
			<li><span class="button2"><a href="#" onclick="insertRowClick()">{tr}Insert Row{/tr}</a></span></li>
			<li><span class="button2"><a href="#" onclick="insertColumnClick()">{tr}Insert Column{/tr}</a></span></li>
			<li><span class="button2"><a href="#" onclick="removeRowClick()">{tr}Remove Row{/tr}</a></span></li>
			<li><span class="button2"><a href="#" onclick="removeColumnClick()">{tr}Remove Column{/tr}</a></span></li>
			<li><span class="button2"><a href="#" onclick="mergeCellClick()">{tr}Merge Cells{/tr}</a></span></li>
			<li><span class="button2"><a href="#" onclick="restoreCellClick()">{tr}Restore Cells{/tr}</a></span></li>
			<li><span class="button2"><a href="#" onclick="copyCalculationClick()">{tr}Copy Calculation{/tr}</a></span></li>
			<li><span class="button2"><a href="#" onclick="formatCellClick()">{tr}Format Cell{/tr}</a></span></li>
		</menu>
		<div id="detail"></div>
	</div>
	<form method="post" action="tiki-view_sheets.php?mode=edit&sheetId={$sheetId}" id="Grid"></form>
	<div class='submit'>
		<input type="submit" onclick='g.target.style.visibility = "hidden"; g.prepareSubmit(); g.target.submit();' value="{tr}Save{/tr}" />
		<span class="button2"><a href="tiki-view_sheets.php?sheetId={$sheetId}">{tr}Cancel{/tr}</a></span>
	</div>
	<script type="text/javascript" src="lib/sheet/grid.js"></script>
	<script type="text/javascript" src="lib/sheet/control.js"></script>
	<script type="text/javascript" src="lib/sheet/formula.js"></script>
	<script type="text/javascript">
	<!--//--><![CDATA[//><!--
	var g;
{$init_grid}

	controlInsertRowBefore = '<form name="insert" onsubmit="insertRowSubmit(this)"><input type="radio" name="pos" value="before" checked="checked" id="sht_ins_row_before" /> <label for="sht_ins_row_before">{tr}Before{/tr}</label> <input type="radio" name="pos" value="after" id="sht_ins_row_after" /> <label for="sht_ins_row_after">{tr}After{/tr}</label> <select name="row">';
	controlInsertRowAfter = '</select><input type="text" name="qty" value="1" size="2" /><input type="submit" name="submit" value="{tr}Insert Row{/tr}" /></form>';
	
	controlInsertColumnBefore = '<form name="insert" onsubmit="insertColumnSubmit(this)"><input type="radio" name="pos" value="before" checked="checked" id="sht_ins_col_before" /> <label for="sht_ins_col_before">{tr}Before{/tr}</label> <input type="radio" name="pos" value="after" id="sht_ins_col_after" /> <label for="sht_ins_col_after">{tr}After{/tr}</label> <select name="column">';
	controlInsertColumnAfter = '</select><input type="text" name="qty" value="1" size="2" /><input type="submit" name="submit" value="{tr}Insert Column{/tr}" /></form>';

	controlRemoveRowBefore = '<form name="remove" onsubmit="removeRowSubmit(this)"><select name="row">';
	controlRemoveRowAfter = '</select><input type="submit" name="submit" value="{tr}Remove Row{/tr}" /></form>';

	controlRemoveColumnBefore = '<form name="remove" onsubmit="removeColumnSubmit(this)"><select name="column">';
	controlRemoveColumnAfter = '</select><input type="submit" name="submit" value="{tr}Remove Column{/tr}" /></form>';
	controlCopyCalculation = '<form name="copy" onsubmit="copyCalculationSubmit(this)"><input type="submit" name="type" value="Left" onclick="document.copy.clicked.value = this.value;" /><input type="submit" name="type" value="Right" onclick="document.copy.clicked.value = this.value;" /><input type="submit" name="type" value="Up" onclick="document.copy.clicked.value = this.value;" /><input type="submit" name="type" value="Down" onclick="document.copy.clicked.value = this.value;" /><input type="hidden" name="clicked" /></form>';
	initGrid();
	controlFormatCellBefore = '<form name="format" onsubmit="formatCellSubmit(this)"><select name="format"><option value="">None</option>';
	controlFormatCellAfter = '</select><input type="submit" name="submit" value="{tr}Format Cell{/tr}" /></form>';
	//--><!]]>
	</script>

{else}
{$grid_content}
{if $tiki_p_view_sheet eq 'y' || $tiki_p_sheet_admin eq 'y' || $tiki_p_admin eq 'y'}
<span class="button2"><a href="tiki-sheets.php">{tr}List Sheets{/tr}</a></span>
{/if}
{if $tiki_p_edit_sheet eq 'y' || $tiki_p_sheet_admin eq 'y' || $tiki_p_admin eq 'y'}
{if $editconflict eq 'y'}
	<span class="button2 highlight"><a href="tiki-view_sheets.php?sheetId={$sheetId}&readdate={$read_date}&mode=edit" title="{$semUser}">{tr}Edit{/tr}</a></span>
{else}
	<span class="button2"><a href="tiki-view_sheets.php?sheetId={$sheetId}&readdate={$read_date}&mode=edit">{tr}Edit{/tr}</a></span>
{/if}
{/if}
{if $tiki_p_view_sheet_history eq 'y' || $tiki_p_sheet_admin eq 'y' || $tiki_p_admin eq 'y'}
<span class="button2"><a href="tiki-history_sheets.php?sheetId={$sheetId}">{tr}History{/tr}</a></span>
{/if}
{if $tiki_p_view_sheet eq 'y' || $tiki_p_sheet_admin eq 'y' || $tiki_p_admin eq 'y'}
<span class="button2"><a href="tiki-export_sheet.php?sheetId={$sheetId}">{tr}Export{/tr}</a></span>
{/if}
{if $tiki_p_edit_sheet eq 'y' || $tiki_p_sheet_admin eq 'y' || $tiki_p_admin eq 'y'}
<span class="button2"><a href="tiki-import_sheet.php?sheetId={$sheetId}">{tr}import{/tr}</a></span>
{/if}
{if $chart_enabled eq 'y'}
<span class="button2"><a href="tiki-graph_sheet.php?sheetId={$sheetId}">{tr}Graph{/tr}</a></span>
{/if}
{/if}
