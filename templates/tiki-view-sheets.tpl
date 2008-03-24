{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-view-sheets.tpl,v 1.17.2.3 2008-03-02 21:44:35 pkdille Exp $ *}
<link rel="stylesheet" href="lib/sheet/style.css" type="text/css" />{* this shouldn't be here; links to CSS only allowed in head html tag !!! (luci) *}
{* here is missing body tag when above link to CSS remains!!! (luci) *}
<h1><a href="tiki-sheets.php?sheetId={$sheetId}" class="pagetitle">{tr}{$title}{/tr}</a></h1>

<div>
{$description}
</div>

{if $page_mode eq 'edit'}
	{if $editconflict eq 'y'}
		<script type="text/javascript">
		<!-- //Hide Script
		alert("{tr}This page is being edited by{/tr} {$semUser}. {tr}Proceed at your own peril{/tr}.")
		//End Hide Script -->
		</script>
	{/if}
	<div id="panel">
		<menu>
			<li><a href="#" onclick="insertRowClick()" class="linkbut">{tr}Insert Row{/tr}</a></li>
			<li><a href="#" onclick="insertColumnClick()" class="linkbut">{tr}Insert Column{/tr}</a></li>
			<li><a href="#" onclick="removeRowClick()" class="linkbut">{tr}Remove Row{/tr}</a></li>
			<li><a href="#" onclick="removeColumnClick()" class="linkbut">{tr}Remove Column{/tr}</a></li>
			<li><a href="#" onclick="mergeCellClick()" class="linkbut">{tr}Merge Cells{/tr}</a></li>
			<li><a href="#" onclick="restoreCellClick()" class="linkbut">{tr}Restore Cells{/tr}</a></li>
			<li><a href="#" onclick="copyCalculationClick()" class="linkbut">{tr}Copy Calculation{/tr}</a></li>
			<li><a href="#" onclick="formatCellClick()" class="linkbut">{tr}Format Cell{/tr}</a></li>
		</menu>
		<div id="detail"></div>
	</div>
	<form method="post" action="tiki-view_sheets.php?mode=edit&sheetId={$sheetId}" id="Grid"></form>
	<div class='submit'>
		<input type="submit" onclick='g.target.style.visibility = "hidden"; g.prepareSubmit(); g.target.submit();' value="{tr}Save{/tr}" />
		<a class="linkbut" href="tiki-view_sheets.php?sheetId={$sheetId}">{tr}Cancel{/tr}</a>
	</div>
	<script type="text/javascript" src="lib/sheet/grid.js"></script>
	<script type="text/javascript" src="lib/sheet/control.js"></script>
	<script type="text/javascript" src="lib/sheet/formula.js"></script>
	<script type="text/javascript">
	var g;
{$init_grid}

	controlInsertRowBefore = '<form name="insert" onSubmit="insertRowSubmit(this)"><input type="radio" name="pos" value="before" checked="checked" id="sht_ins_row_before" /> <label for="sht_ins_row_before">{tr}Before{/tr}</label> <input type="radio" name="pos" value="after" id="sht_ins_row_after" /> <label for="sht_ins_row_after">{tr}After{/tr}</label> <select name="row">';
	controlInsertRowAfter = '</select><input type="text" name="qty" value="1" size="2" /><input type="submit" name="submit" value="{tr}Insert Row{/tr}" /></form>';
	
	controlInsertColumnBefore = '<form name="insert" onSubmit="insertColumnSubmit(this)"><input type="radio" name="pos" value="before" checked="checked" id="sht_ins_col_before" /> <label for="sht_ins_col_before">{tr}Before{/tr}</label> <input type="radio" name="pos" value="after" id="sht_ins_col_after" /> <label for="sht_ins_col_after">{tr}After{/tr}</label> <select name="column">';
	controlInsertColumnAfter = '</select><input type="text" name="qty" value="1" size="2" /><input type="submit" name="submit" value="{tr}Insert Column{/tr}" /></form>';

	controlRemoveRowBefore = '<form name="remove" onSubmit="removeRowSubmit(this)"><select name="row">';
	controlRemoveRowAfter = '</select><input type="submit" name="submit" value="{tr}Remove Row{/tr}" /></form>';

	controlRemoveColumnBefore = '<form name="remove" onSubmit="removeColumnSubmit(this)"><select name="column">';
	controlRemoveColumnAfter = '</select><input type="submit" name="submit" value="{tr}Remove Column{/tr}" /></form>';
	controlCopyCalculation = '<form name="copy" onSubmit="copyCalculationSubmit(this)"><input type="submit" name="type" value="Left" onclick="document.copy.clicked.value = this.value;" /><input type="submit" name="type" value="Right" onclick="document.copy.clicked.value = this.value;" /><input type="submit" name="type" value="Up" onclick="document.copy.clicked.value = this.value;" /><input type="submit" name="type" value="Down" onclick="document.copy.clicked.value = this.value;" /><input type="hidden" name="clicked" /></form>';
	initGrid();
	controlFormatCellBefore = '<form name="format" onSubmit="formatCellSubmit(this)"><select name="format"><option value="">None</option>';
	controlFormatCellAfter = '</select><input type="submit" name="submit" value="{tr}Format Cell{/tr}" /></form>';
	</script>

{else}
{$grid_content}
{if $tiki_p_view_sheet eq 'y' || $tiki_p_sheet_admin eq 'y' || $tiki_p_admin eq 'y'}
<a href="tiki-sheets.php" class="linkbut">{tr}List Sheets{/tr}</a>
{/if}
{if $tiki_p_edit_sheet eq 'y' || $tiki_p_sheet_admin eq 'y' || $tiki_p_admin eq 'y'}
{if $editconflict eq 'y'}
	<a href="tiki-view_sheets.php?sheetId={$sheetId}&readdate={$read_date}&mode=edit" class="linkbut" title="{$semUser}" ><span class="highlight">{tr}Edit{/tr}</span></a>
{else}
	<a href="tiki-view_sheets.php?sheetId={$sheetId}&readdate={$read_date}&mode=edit" class="linkbut">{tr}Edit{/tr}</a>
{/if}
{/if}
{if $tiki_p_view_sheet_history eq 'y' || $tiki_p_sheet_admin eq 'y' || $tiki_p_admin eq 'y'}
<a href="tiki-history_sheets.php?sheetId={$sheetId}" class="linkbut">{tr}History{/tr}</a>
{/if}
{if $tiki_p_view_sheet eq 'y' || $tiki_p_sheet_admin eq 'y' || $tiki_p_admin eq 'y'}
<a href="tiki-export_sheet.php?sheetId={$sheetId}" class="linkbut">{tr}Export{/tr}</a>
{/if}
{if $tiki_p_edit_sheet eq 'y' || $tiki_p_sheet_admin eq 'y' || $tiki_p_admin eq 'y'}
<a href="tiki-import_sheet.php?sheetId={$sheetId}" class="linkbut">{tr}import{/tr}</a>
{/if}
{if $chart_enabled eq 'y'}
<a href="tiki-graph_sheet.php?sheetId={$sheetId}" class="linkbut">{tr}Graph{/tr}</a>
{/if}
{/if}
