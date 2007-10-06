<link rel="stylesheet" href="lib/sheet/style.css" type="text/css" />
<h1><a href="tiki-sheets.php?sheetId={$sheetId}" class="pagetitle">{tr}{$title}{/tr}</a></h1>

<div>
{$description}
</div>

<ul>
{section name=revision_date loop=$history}
	<li><a href="tiki-view_sheets.php?sheetId={$sheetId}&readdate={$history[revision_date].stamp}">{$history[revision_date].string}</a> / {$history[revision_date].user}</li>
{/section}
</ul>
