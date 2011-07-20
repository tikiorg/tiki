
{title help="Spreadsheet"}{$title}{/title}

<div>
  {$description|escape}
</div>

<div class="navbar">
	{if $tiki_p_view_sheet eq 'y' || $tiki_p_admin_sheet eq 'y' || $tiki_p_admin eq 'y'}
		{button href="tiki-sheets.php" _text="{tr}List Sheets{/tr}"}
	{/if}

	{if $tiki_p_view_sheet eq 'y' || $tiki_p_admin_sheet eq 'y' || $tiki_p_admin eq 'y'}
		{button href="tiki-view_sheets.php?sheetId=$sheetId" _text="{tr}View{/tr}"}
	{/if}

	{if $tiki_p_edit_sheet eq 'y' || $tiki_p_admin_sheet eq 'y' || $tiki_p_admin eq 'y'}
		{button href="tiki-view_sheets.php?sheetId=$sheetId&amp;readdate=$read_date&amp;mode=edit" _text="{tr}Edit{/tr}"}
	{/if}

	{if $tiki_p_view_sheet_history eq 'y' || $tiki_p_admin_sheet eq 'y' || $tiki_p_admin eq 'y'}
		{button href="tiki-history_sheets.php?sheetId=$sheetId" _text="{tr}History{/tr}"}
	{/if}

	{if $tiki_p_view_sheet eq 'y' || $tiki_p_admin_sheet eq 'y' || $tiki_p_admin eq 'y'}
		{button href="tiki-export_sheet.php?sheetId=$sheetId" _text="{tr}Export{/tr}"}
	{/if}

	{if $chart_enabled eq 'y'}
		{button href="tiki-graph_sheet.php?sheetId=$sheetId" _text="{tr}Graph{/tr}"}
	{/if}
</div>

{if $page_mode eq 'submit'}
{$grid_content}

{else}
	<form method="post" action="tiki-import_sheet.php?mode=import&sheetId={$sheetId}" enctype="multipart/form-data">
		<h2>{tr}Import From File{/tr}</h2>
		{tr}Format:{/tr}
		<select name="handler">
		{section name=key loop=$handlers}
			<option value="{$handlers[key].class}">{$handlers[key].name} V. {$handlers[key].version}</option>
		{/section}
		</select>
		{tr}Charset encoding:{/tr}
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
