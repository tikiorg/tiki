<link rel="stylesheet" href="lib/sheet/style.css" type="text/css" />
<a href="tiki-sheets.php" class="pagetitle">{tr}{$title}{/tr}</a>

<div>
{$description}
</div>

{if $page_mode eq 'edit'}
	<div id="panel">
		<menu>
			<li><a href="#" onClick="insertRowClick()" class="linkbut">Insert Row</a></li>
			<li><a href="#" onClick="insertColumnClick()" class="linkbut">Insert Column</a></li>
			<li><a href="#" onClick="removeRowClick()" class="linkbut">Remove Row</a></li>
			<li><a href="#" onClick="removeColumnClick()" class="linkbut">Remove Column</a></li>
			<li><a href="#" onclick="mergeCellClick()" class="linkbut">Merge Cells</a></li>
			<li><a href="#" onclick="restoreCellClick()" class="linkbut">Restore Cells</a></li>
			<li><a href="#" onclick="copyCalculationClick()" class="linkbut">Copy Calculation</a></li>
		</menu>
		<div id="detail"></div>
	</div>
	<form method="post" action="tiki-view_sheets.php?mode=edit&sheetId={$sheetId}" id="Grid"></form>
	<div class='submit'><input type="submit" onclick='g.target.style.visibility = "hidden"; g.prepareSubmit(); g.target.submit();' value="Save" /></div>
	<script language="JavaScript" type="text/javascript" src="lib/sheet/grid.js"></script>
	<script language="JavaScript" type="text/javascript" src="lib/sheet/control.js"></script>
	<script language="JavaScript" type="text/javascript" src="lib/sheet/formula.js"></script>
	<script language="JavaScript">
	var g;
{$init_grid}

	controlInsertRowBefore = '<form name="insert" onSubmit="insertRowSubmit(this)"><input type="radio" name="pos" value="before" checked /> Before <input type="radio" name="pos" value="after" /> After <select name="row">';
	controlInsertRowAfter = '</select><input type="submit" name="submit" value="Insert Row" /></form>';
	
	controlInsertColumnBefore = '<form name="insert" onSubmit="insertColumnSubmit(this)"><input type="radio" name="pos" value="before" checked /> Before <input type="radio" name="pos" value="after" /> After <select name="column">';
	controlInsertColumnAfter = '</select><input type="submit" name="submit" value="Insert Column" /></form>';

	controlRemoveRowBefore = '<form name="remove" onSubmit="removeRowSubmit(this)"><select name="row">';
	controlRemoveRowAfter = '</select><input type="submit" name="submit" value="Remove Row" /></form>';

	controlRemoveColumnBefore = '<form name="remove" onSubmit="removeColumnSubmit(this)"><select name="column">';
	controlRemoveColumnAfter = '</select><input type="submit" name="submit" value="Remove Column" /></form>';
	controlCopyCalculation = '<form name="copy" onSubmit="copyCalculationSubmit(this)"><input type="submit" name="type" value="Left" onclick="document.copy.clicked.value = this.value;" /><input type="submit" name="type" value="Right" onclick="document.copy.clicked.value = this.value;" /><input type="submit" name="type" value="Up" onclick="document.copy.clicked.value = this.value;" /><input type="submit" name="type" value="Down" onclick="document.copy.clicked.value = this.value;" /><input type="hidden" name="clicked" /></form>';
	initGrid();
	</script>

{else}
{$grid_content}
<a href="tiki-view_sheets.php?sheetId={$sheetId}&mode=edit" class="linkbut">Edit</a>
{/if}
