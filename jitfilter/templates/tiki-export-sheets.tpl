<link rel="stylesheet" href="lib/sheet/style.css" type="text/css" />

{title}{tr}{$title}{/tr}{/title}

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
{if $tiki_p_sheet_admin eq 'y' || $tiki_p_admin eq 'y'}
<span class="button2"><a href="tiki-import_sheet.php?sheetId={$sheetId}">{tr}import{/tr}</a></span>
{/if}
{if $chart_enabled eq 'y'}
<span class="button2"><a href="tiki-graph_sheet.php?sheetId={$sheetId}">{tr}Graph{/tr}</a></span>
{/if}

{if $page_mode eq 'submit'}
{$grid_content}

{else}
	<form method="post" action="tiki-export_sheet.php?mode=export&sheetId={$sheetId}" enctype="multipart/form-data">
		<h2>{tr}Export to file{/tr}</h2>
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
		<input type="submit" value="{tr}Export{/tr}" />
	</form>
{/if}
