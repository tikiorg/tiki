<link rel="stylesheet" href="lib/sheet/style.css" type="text/css" />
<a href="tiki-sheets.php" class="pagetitle">{tr}{$title}{/tr}</a>

<div>
{$description}
</div>

{if $page_mode eq 'edit'}
	<div id="panel">
		<menu>
			<li><a href="#" onClick="insertRowClick()" class="linkbut">{tr}Insert Row{/tr}</a></li>
			<li><a href="#" onClick="insertColumnClick()" class="linkbut">{tr}Insert Column{/tr}</a></li>
			<li><a href="#" onClick="removeRowClick()" class="linkbut">{tr}Remove Row{/tr}</a></li>
			<li><a href="#" onClick="removeColumnClick()" class="linkbut">{tr}Remove Column{/tr}</a></li>
			<li><a href="#" onclick="mergeCellClick()" class="linkbut">{tr}Merge Cells{/tr}</a></li>
			<li><a href="#" onclick="restoreCellClick()" class="linkbut">{tr}Restore Cells{/tr}</a></li>
			<li><a href="#" onclick="copyCalculationClick()" class="linkbut">{tr}Copy Calculation{/tr}</a></li>
			<li><a href="#" onclick="formatCellClick()" class="linkbut">{tr}Format Cell{/tr}</a></li>
		</menu>
		<div id="detail"></div>
	</div>
	<form method="post" action="tiki-view_sheets.php?mode=edit&sheetId={$sheetId}" id="Grid"></form>
	<div class='submit'><input type="submit" onclick='g.target.style.visibility = "hidden"; g.prepareSubmit(); g.target.submit();' value="{tr}Save{/tr}" /></div>
	<script language="JavaScript" type="text/javascript" src="lib/sheet/grid.js"></script>
	<script language="JavaScript" type="text/javascript" src="lib/sheet/control.js"></script>
	<script language="JavaScript" type="text/javascript" src="lib/sheet/formula.js"></script>
	<script language="JavaScript">
	var g;
{$init_grid}

	controlInsertRowBefore = '<form name="insert" onSubmit="insertRowSubmit(this)"><input type="radio" name="pos" value="before" checked /> {tr}Before{/tr} <input type="radio" name="pos" value="after" /> {tr}After{/tr} <select name="row">';
	controlInsertRowAfter = '</select><input type="text" name="qty" value="1" size="2" /><input type="submit" name="submit" value="{tr}Insert Row{/tr}" /></form>';
	
	controlInsertColumnBefore = '<form name="insert" onSubmit="insertColumnSubmit(this)"><input type="radio" name="pos" value="before" checked /> {tr}Before{/tr} <input type="radio" name="pos" value="after" /> {tr}After{/tr} <select name="column">';
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
<a href="tiki-view_sheets.php?sheetId={$sheetId}&readdate={$read_date}&mode=edit" class="linkbut">{tr}Edit{/tr}</a>
{/if}
