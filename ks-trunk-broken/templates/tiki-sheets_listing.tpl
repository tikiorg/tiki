{* $Id: tiki-sheets.tpl 35450 2011-07-17 19:03:36Z changi67 $ *}

<tr class="{cycle}">
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
			<a class="gallink" href="tiki-graph_sheet.php?sheetId={$sheet.sheetId}">
				<img src='img/icons/chart_curve.png' width='16' height='16' alt="{tr}Graph{/tr}" title="{tr}Graph{/tr}" />
			</a>
		{/if}
		{if $tiki_p_view_sheet_history eq 'y'}
			<a class="gallink" href="tiki-history_sheets.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;sheetId={$sheet.sheetId}">
				{icon _id='application_form_magnify' alt="{tr}History{/tr}"}
			</a>
		{/if}
		<a class="gallink" href="tiki-export_sheet.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;sheetId={$sheet.sheetId}">
			{icon _id='disk' alt="{tr}Export{/tr}"}
		</a>
		{if $sheet.tiki_p_edit_sheet eq 'y'}
			<a class="gallink" href="tiki-import_sheet.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;sheetId={$sheet.sheetId}">
				{icon _id='folder_add' alt="{tr}Import{/tr}"}
			</a>
		{/if}
		{if $tiki_p_admin_sheet eq 'y'}
			<a class="gallink" href="tiki-objectpermissions.php?objectName={$sheet.title|escape:"url"}&amp;objectType=sheet&amp;permType=sheet&amp;objectId={$sheet.sheetId}">
			{if $sheet.individual eq 'y'}
				{icon _id='key_active' alt="{tr}Active Perms{/tr}"}
			{else}
				{icon _id='key' alt="{tr}Perms{/tr}"}
			{/if}
			</a>
		{/if}
		{if $sheet.tiki_p_edit_sheet eq 'y'}
			<a class="gallink" href="tiki-sheets.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;edit_mode=1&amp;sheetId={$sheet.sheetId}">
				{icon _id='page_edit' alt="{tr}Configure{/tr}"}
			</a>
			<a class="gallink" href="tiki-sheets.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;removesheet=y&amp;sheetId={$sheet.sheetId}">
				{icon _id='cross' alt="{tr}Delete{/tr}"}
			</a>
		{/if}
	</td>
</tr>
