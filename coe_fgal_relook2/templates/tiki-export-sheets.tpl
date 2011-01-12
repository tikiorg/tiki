
{title}{tr}{$title}{/tr}{/title}

<div>
	{$description}
</div>

<div class="navbar">
	{if $tiki_p_view_sheet eq 'y' || $tiki_p_admin_sheet eq 'y' || $tiki_p_admin eq 'y'}
		{button href="tiki-sheets.php" _text="{tr}List Sheets{/tr}"}
	{/if}

	{if $tiki_p_view_sheet eq 'y' || $tiki_p_admin_sheet eq 'y' || $tiki_p_admin eq 'y'}
		{button href="tiki-view_sheets.php?sheetId=$sheetId" _text="{tr}View{/tr}"}
	{/if}

	{if $tiki_p_edit_sheet eq 'y' || $tiki_p_admin_sheet eq 'y' || $tiki_p_admin eq 'y'}
		{button href="tiki-view_sheets.php?sheetId=$sheetId&amp;readdate=$read_date&amp;parse=edit" _text="{tr}Edit{/tr}"}
	{/if}
	
	{if $tiki_p_view_sheet_history eq 'y' || $tiki_p_admin_sheet eq 'y' || $tiki_p_admin eq 'y'}
		{button href="tiki-history_sheets.php?sheetId=$sheetId" _text="{tr}History{/tr}"}
	{/if}

	{if $tiki_p_admin_sheet eq 'y' || $tiki_p_admin eq 'y'}
		{button href="tiki-import_sheet.php?sheetId=$sheetId" _text="{tr}Import{/tr}"}
	{/if}

	{if $chart_enabled eq 'y'}
		{button href="tiki-graph_sheet.php?sheetId=$sheetId" _text="{tr}Graph{/tr}"}
	{/if}
</div>

{if $page_mode eq 'submit'}
{$grid_content}

{else}
	<form method="post" action="tiki-export_sheet.php?mode=export&sheetId={$sheetId}" enctype="multipart/form-data">
		<h2>{tr}Export to file{/tr}</h2>
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
		<input type="submit" value="{tr}Export{/tr}" />
	</form>
{/if}
