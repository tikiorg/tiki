<link rel="stylesheet" href="lib/sheet/style.css" type="text/css" />
<a href="tiki-sheets.php" class="pagetitle">{tr}{$title}{/tr}</a>

<div>
{$description}
</div>

{if $page_mode eq 'submit'}
{$grid_content}

{else}
	<form method="post" action="tiki-import_sheet.php?mode=import&sheetId={$sheetId}" enctype="multipart/form-data">
		<select name="handler">
{section name=key loop=$handlers}
			<option value="{$handlers[key].class}">{$handlers[key].name} V. {$handlers[key].version}</option>
{/section}
		</select>
		<input type="file" name="file" />
		<input type="submit" value="Import" />
	</form>
{/if}
