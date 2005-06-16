<link rel="stylesheet" href="lib/sheet/style.css" type="text/css" />
<h1><a href="tiki-sheets.php" class="pagetitle">{tr}{$title}{/tr}</a></h1>

<div>
{$description}
</div>

{if $page_mode eq 'submit'}
{$grid_content}

{else}
	<form method="post" action="tiki-import_sheet.php?mode=import&sheetId={$sheetId}" enctype="multipart/form-data">
		<h2>{tr}Import From File{/tr}</h2>
		<select name="handler">
{section name=key loop=$handlers}
			<option value="{$handlers[key].class}">{$handlers[key].name} V. {$handlers[key].version}</option>
{/section}
		</select>
		<input type="file" name="file" />
		<input type="submit" value="Import" />
	</form>
	<form method="post" action="tiki-import_sheet.php?mode=import&sheetId={$sheetId}">
		<h2>{tr}Grab Wiki Tables{/tr}</h2>
		<input type="text" name="page"/>
		<input type="hidden" name="handler" value="TikiSheetWikiTableHandler"/>
		<input type="submit" value="Import"/>
	</form>
{/if}
