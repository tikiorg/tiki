
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

<div align="center">
	{include file='find.tpl'}

	<table class="table table-striped table-hover">
		<tr>
			<th>
				<a href="{$url}?nlId={$nlId}&amp;{$cur}_offset={$offset}&amp;{$bak}_offset={$offset_bak}&amp;{$cur}_sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}&amp;{$bak}_sort_mode={$sort_mode_bak}&amp;cookietab={$tab}">{tr}Newsletter{/tr}</a>
			</th>
			<th>
				<a href="{$url}?nlId={$nlId}&amp;{$cur}_offset={$offset}&amp;{$bak}_offset={$offset_bak}&amp;{$cur}_sort_mode={if $sort_mode eq 'subject_desc'}subject_asc{else}subject_desc{/if}&amp;{$bak}_sort_mode={$sort_mode_bak}&amp;cookietab={$tab}">{tr}Subject{/tr}</a>
			</th>
			{if $view_editions eq 'y'}
				<th>
					<a href="{$url}?nlId={$nlId}&amp;{$cur}_offset={$offset}&amp;{$bak}_offset={$offset_bak}&amp;{$cur}_sort_mode={if $sort_mode eq 'users_desc'}users_asc{else}users_desc{/if}&amp;{$bak}_sort_mode={$sort_mode_bak}&amp;cookietab={$tab}">{tr}Users{/tr}</a>
				</th>
				<th>
					<a href="{$url}?nlId={$nlId}&amp;{$cur}_offset={$offset}&amp;{$bak}_offset={$offset_bak}&amp;{$cur}_sort_mode={if $sort_mode eq 'sent_desc'}sent_asc{else}sent_desc{/if}&amp;{$bak}_sort_mode={$sort_mode_bak}&amp;cookietab={$tab}">{tr}Sent{/tr}</a>
				</th>
			{/if}
			<th>{tr}Errors{/tr}</th>
			<th></th>
		</tr>

		{section name=user loop=$channels}
			<tr>
				<td class="text">{$channels[user].name|escape}</td>
				<td class="text">
					{if $view_editions eq 'y'}
						<a class="link" href="{$url}?{if $nl_info}nlId={$channels[user].nlId}&amp;{/if}offset={$offset}&amp;sort_mode={$sort_mode}&amp;editionId={$channels[user].editionId}&amp;resend=1">{$channels[user].subject|escape}</a>
					{else}
						<a class="link" href="{$url}?{if $nl_info}nlId={$channels[user].nlId}&amp;{/if}offset={$offset}&amp;sort_mode={$sort_mode}&amp;editionId={$channels[user].editionId}">{$channels[user].subject|escape}</a>
					{/if}
				</td>
				{if $view_editions eq 'y'}
					<td>{$channels[user].users}</td>
					<td>{$channels[user].sent|tiki_short_datetime}</td>
				{/if}
				<td class="integer">
					{if $channels[user].nbErrors > 0}
						<a href="tiki-newsletter_archives.php?nlId={$channels[user].nlId}&amp;error={$channels[user].editionId}">{$channels[user].nbErrors}</a>
					{else}
						0
					{/if}
				</td>
				<td class="action">
					{capture name=sent_actions}
						{strip}
							{if $url == "tiki-newsletter_archives.php"}
								{$libeg}<a href="{$url}?{if $nl_info}nlId={$channels[user].nlId}&amp;{/if}offset={$offset}&amp;sort_mode={$sort_mode}&amp;editionId={$channels[user].editionId}">
									{icon name='view' _menu_text='y' _menu_icon='y' alt="{tr}View{/tr}"}
								</a>{$liend}
							{/if}
							{if ($channels[user].tiki_p_send_newsletters eq 'y') or ($channels[user].tiki_p_admin_newsletters eq 'y')}
								{if $view_editions eq 'y'}
									{$libeg}<a href="tiki-send_newsletters.php?nlId={$channels[user].nlId}&amp;editionId={$channels[user].editionId}&amp;resend=1">
										{icon name='repeat' _menu_text='y' _menu_icon='y' alt="{tr}Resend newsletter{/tr}"}
									</a>{$liend}
								{else}
									{$libeg}<a class="tips" title="{tr}Send Newsletter{/tr}" href="tiki-send_newsletters.php?nlId={$channels[user].nlId}&amp;editionId={$channels[user].editionId}">
										{icon name='envelope' _menu_text='y' _menu_icon='y' alt="{tr}Send newsletter{/tr}"}
									</a>{$liend}
								{/if}
							{else}
								&nbsp;
							{/if}
							{if $channels[user].tiki_p_admin_newsletters eq 'y'}
								{$libeg}<a class="link" href="{$url}?nlId={$channels[user].nlId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].editionId}" title="{tr}Remove{/tr}">
									{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
								</a>{$liend}
							{else}
								&nbsp;
							{/if}
						{/strip}
					{/capture}
					{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
					<a
						class="tips"
						title="{tr}Actions{/tr}"
						href="#"
						{if $js === 'y'}{popup center=true text=$smarty.capture.sent_actions|escape:"javascript"|escape:"html"}{/if}
						style="padding:0; margin:0; border:0"
					>
						{icon name='wrench'}
					</a>
					{if $js === 'n'}
						<ul class="dropdown-menu" role="menu">{$smarty.capture.sent_actions}</ul></li></ul>
					{/if}
				</td>
			</tr>
		{/section}
	</table>

	<div class="center-block">
		{if $prev_offset >= 0}
			[<a class="prevnext" href="{$url}?nlId={$nlId}&amp;{$cur}_offset={$prev_offset}&amp;{$bak}_offset={$offset_bak}&amp;{$cur}_sort_mode={$sort_mode}&amp;{$bak}_sort_mode={$sort_mode_bak}&amp;cookietab={$tab}">{tr}Prev{/tr}</a>]&nbsp;
			{/if}
		{tr}Page{/tr}: {$actual_page}/{$cant_pages}
		{if $next_offset >= 0}
			&nbsp;[<a class="prevnext" href="{$url}?nlId={$nlId}&amp;{$cur}_offset={$next_offset}&amp;{$bak}_offset={$offset_bak}&amp;{$cur}_sort_mode={$sort_mode}&amp;{$bak}_sort_mode={$sort_mode_bak}&amp;cookietab={$tab}">{tr}Next{/tr}</a>]
	{/if}
		{if $prefs.direct_pagination eq 'y'}
			<br>
			{section loop=$cant_pages name=foo}
				{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
					<a class="prevnext" href="{$url}?nlId={$nlId}&amp;{$cur}_offset={$selector_offset}&amp;{$bak}_offset={$offset_bak}&amp;{$cur}_sort_mode={$sort_mode}&amp;{$bak}_sort_mode={$sort_mode_bak}&amp;cookietab={$tab}">
				{$smarty.section.foo.index_next}</a>&nbsp;
			{/section}
		{/if}
	</div>
</div>
