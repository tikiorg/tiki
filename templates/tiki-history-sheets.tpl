<link rel="stylesheet" href="lib/sheet/style.css" type="text/css" />
<a href="tiki-sheets.php" class="pagetitle">{tr}{$title}{/tr}</a>

<div>
{$description}
</div>

<ul>
{section name=revision_date loop=$history}
	<li><a href="tiki-view_sheets.php?sheetId={$sheetId}&readdate={$history[revision_date].stamp}">{$history[revision_date].string}</a></li>
{/section}
</ul>
