{* $Id: tiki-sheets.tpl 35450 2011-07-17 19:03:36Z changi67 $ *}
{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
{if $prefs.javascript_enabled !== 'y'}
	{$js = 'n'}
	{$libeg = '<li>'}
	{$liend = '</li>'}
{else}
	{$js = 'y'}
	{$libeg = ''}
	{$liend = ''}
{/if}
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
		{capture name='sheets_actions'}
			{strip}
				{if $chart_enabled eq 'y'}
					{$libeg}<a class="gallink" href="tiki-graph_sheet.php?sheetId={$sheet.sheetId}">
						{icon name='chart' _menu_text='y' _menu_icon='y' alt="{tr}Graph{/tr}"}
					</a>{$liend}
				{/if}
				{if $tiki_p_view_sheet_history eq 'y'}
					{$libeg}<a class="gallink" href="tiki-history_sheets.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;sheetId={$sheet.sheetId}">
						{icon name='history' _menu_text='y' _menu_icon='y' alt="{tr}History{/tr}"}
					</a>{$liend}
				{/if}
				{$libeg}<a class="gallink tips" title=":{tr}Export{/tr}" href="tiki-export_sheet.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;sheetId={$sheet.sheetId}">
					{icon name='export' _menu_text='y' _menu_icon='y' alt="{tr}Export{/tr}"}
				</a>{$liend}
				{if $sheet.tiki_p_edit_sheet eq 'y'}
					{$libeg}<a class="gallink tips" title=":{tr}Import{/tr}" href="tiki-import_sheet.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;sheetId={$sheet.sheetId}">
						{icon name='import' _menu_text='y' _menu_icon='y' alt="{tr}Imort{/tr}"}
					</a>{$liend}
				{/if}
				{if $tiki_p_admin_sheet eq 'y'}
					{$libeg}{permission_link mode='text' type=sheet id=$sheet.sheetId title=$sheet.title}{$liend}
				{/if}
				{if $sheet.tiki_p_edit_sheet eq 'y'}
					{$libeg}<a class="gallink" href="tiki-sheets.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;edit_mode=1&amp;sheetId={$sheet.sheetId}">
						{icon name='cog' _menu_text='y' _menu_icon='y' alt="{tr}Configure{/tr}"}
					</a>{$liend}
					{$libeg}<a class="gallink" href="tiki-sheets.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;removesheet=y&amp;sheetId={$sheet.sheetId}">
						{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Delete{/tr}"}
					</a>{$liend}
				{/if}
			{/strip}
		{/capture}
		{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
		<a
			class="tips"
			title="{tr}Actions{/tr}" href="#"
			{if $js === 'y'}{popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.sheets_actions|escape:"javascript"|escape:"html"}{/if}
			style="padding:0; margin:0; border:0"
		>
			{icon name='settings'}
		</a>
		{if $js === 'n'}
			<ul class="dropdown-menu" role="menu">{$smarty.capture.sheets_actions}</ul></li></ul>
		{/if}
	</td>
</tr>
