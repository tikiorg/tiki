<link rel="stylesheet" href="lib/sheet/style.css" type="text/css" />
<a href="tiki-sheets.php" class="pagetitle">{tr}{$title}{/tr}</a>

<div>
{$description}
</div>

{if $page_mode eq 'edit'}
	<div id="panels"></div>
	<form method="post" action="tiki-view_sheets.php?mode=edit&sheetId={$sheetId}" id="Grid"></form>
	<div class='submit'><a href='#' onclick='g.target.style.visibility = "hidden"; g.prepareSubmit(); g.target.submit();'>Save</a></div>
	<script language="JavaScript" type="text/javascript" src="lib/sheet/panel.js"></script>
	<script language="JavaScript" type="text/javascript" src="lib/sheet/grid.js"></script>
	<script language="JavaScript" type="text/javascript" src="lib/sheet/control.js"></script>
	<script language="JavaScript" type="text/javascript" src="lib/sheet/formula.js"></script>
	<script language="JavaScript">
	var g;
{$init_grid}	
	drawPanels( document.getElementById( 'panels' ) );
	initGrid();
	</script>
	<menu>
		<li><a href="#" onClick="insertRowClick()">Insert Row</a></li>
		<li><a href="#" onClick="insertColumnClick()">Insert Column</a></li>
		<li><a href="#" onClick="removeRowClick()">Remove Row</a></li>
		<li><a href="#" onClick="removeColumnClick()">Remove Column</a></li>
		<li><a href="#" onclick="mergeCellClick()">Merge Cells</a></li>
		<li><a href="#" onclick="restoreCellClick()">Restore Cells</a></li>
	</menu>
	<div id="detail"></div>

{else}
{$grid_content}
<a href="tiki-view_sheets.php?sheetId={$sheetId}&mode=edit">Edit</a>
{/if}
