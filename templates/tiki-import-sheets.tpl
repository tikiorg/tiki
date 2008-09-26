<link rel="stylesheet" href="lib/sheet/style.css" type="text/css" />

{title help="Spreadsheet"}{$title}{/title}

<div>
  {$description}
</div>

{if $tiki_p_view_sheet eq 'y' || $tiki_p_sheet_admin eq 'y' || $tiki_p_admin eq 'y'}
  <span class="button2"><a href="tiki-sheets.php">{tr}List Sheets{/tr}</a></span>
{/if}

{if $tiki_p_view_sheet eq 'y' || $tiki_p_sheet_admin eq 'y' || $tiki_p_admin eq 'y'}
  <span class="button2"><a href="tiki-view_sheets.php?sheetId={$sheetId}">{tr}View{/tr}</a></span>
{/if}

{if $tiki_p_edit_sheet eq 'y' || $tiki_p_sheet_admin eq 'y' || $tiki_p_admin eq 'y'}
  <span class="button2"><a href="tiki-view_sheets.php?sheetId={$sheetId}&readdate={$read_date}&mode=edit">{tr}Edit{/tr}</a></span>
{/if}

{if $tiki_p_view_sheet_history eq 'y' || $tiki_p_sheet_admin eq 'y' || $tiki_p_admin eq 'y'}
  <span class="button2"><a href="tiki-history_sheets.php?sheetId={$sheetId}">{tr}History{/tr}</a></span>
{/if}

{if $tiki_p_view_sheet eq 'y' || $tiki_p_sheet_admin eq 'y' || $tiki_p_admin eq 'y'}
  <span class="button2"><a href="tiki-export_sheet.php?sheetId={$sheetId}">{tr}Export{/tr}</a></span>
{/if}

{if $chart_enabled eq 'y'}
  <span class="button2"><a href="tiki-graph_sheet.php?sheetId={$sheetId}">{tr}Graph{/tr}</a></span>
{/if}

{if $page_mode eq 'submit'}
{$grid_content}

{else}
	<form method="post" action="tiki-import_sheet.php?mode=import&sheetId={$sheetId}" enctype="multipart/form-data">
		<h2>{tr}Import From File{/tr}</h2>
		{tr}Format{/tr}:
		<select name="handler">
		{section name=key loop=$handlers}
			<option value="{$handlers[key].class}">{$handlers[key].name} V. {$handlers[key].version}</option>
		{/section}
		</select>
		{tr}Charset encoding{/tr}:
		<select name="encoding">
			<!--<option value="">{tr}Autodetect{/tr}</option>-->
		{section name=key loop=$charsets}
			<option value="{$charsets[key]}">{$charsets[key]}</option>
		{/section}
		</select>
    <br />
    <br />
		<input type="file" name="file" />
		<input type="submit" value="{tr}Import{/tr}" />
	</form>
	<form method="post" action="tiki-import_sheet.php?mode=import&sheetId={$sheetId}">
		<h2>{tr}Grab Wiki Tables{/tr}</h2>
		<input type="text" name="page"/>
		<input type="hidden" name="handler" value="TikiSheetWikiTableHandler"/>
		<input type="submit" value="Import"/>
	</form>
{/if}
