{* $Id: tiki-sheets.tpl 35450 2011-07-17 19:03:36Z changi67 $ *}

<tr>
	<td class="text">
		{if $sheet.parentSheetId}
			<span class="ui-icon ui-icon-grip-dotted-vertical" style="float: left;"></span>
			<span class="ui-icon ui-icon-grip-dotted-horizontal" style="float: left; margin-left: -9px;"></span>
		{/if}
		<a class="galname sheetLink" sheetId="{$sheet.sheetId}" href="tiki-view_sheets.php?sheetId={$sheet.sheetId}">{$sheet.title|escape}</a>
	</td>
	<td class="text">{$sheet.description|escape}</td>
	<td>{$sheet.created|tiki_short_date}</td>
	<td>{$sheet.lastModif|tiki_short_date}</td>
	<td class="username">{$sheet.author|escape}</td>
	<td class="action">
		{if $chart_enabled eq 'y'}
			<a class="gallink tips" title=":{tr}Graph{/tr}" href="tiki-graph_sheet.php?sheetId={$sheet.sheetId}">
				{icon name='chart'}
			</a>
		{/if}
		{if $tiki_p_view_sheet_history eq 'y'}
			<a class="gallink tips" title=":{tr}History{/tr}" href="tiki-history_sheets.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;sheetId={$sheet.sheetId}">
				{icon name='history'}
			</a>
		{/if}
		<a class="gallink tips" title=":{tr}Export{/tr}" href="tiki-export_sheet.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;sheetId={$sheet.sheetId}">
			{icon name='export'}
		</a>
		{if $sheet.tiki_p_edit_sheet eq 'y'}
			<a class="gallink tips" title=":{tr}Import{/tr}" href="tiki-import_sheet.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;sheetId={$sheet.sheetId}">
				{icon name='import'}
			</a>
		{/if}
		{if $tiki_p_admin_sheet eq 'y'}
			{permission_link type=sheet id=$sheet.sheetId title=$sheet.title}
		{/if}
		{if $sheet.tiki_p_edit_sheet eq 'y'}
			<a class="gallink tips" title=":{tr}Configure{/tr}"href="tiki-sheets.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;edit_mode=1&amp;sheetId={$sheet.sheetId}">
				{icon name='cog'}
			</a>
			<a class="gallink tips" title=":{tr}Delete{/tr}" href="tiki-sheets.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;removesheet=y&amp;sheetId={$sheet.sheetId}">
				{icon name='remove'}
			</a>
		{/if}
	</td>
</tr>
