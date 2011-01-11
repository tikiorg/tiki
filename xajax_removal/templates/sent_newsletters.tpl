
<div align="center">
	{include file='find.tpl'}
	
	<table class="normal">
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
			<th>{tr}Action{/tr}</th>
		</tr>
		{cycle values="odd,even" print=false}
		{section name=user loop=$channels}
			<tr class="{cycle}">
				<td class="text">{$channels[user].name|escape}</td>
				<td class="text">{$channels[user].subject|escape}</td>
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
					{if $url == "tiki-newsletter_archives.php"}
						<a class="link" href="{$url}?{if $nl_info}nlId={$channels[user].nlId}&amp;{/if}offset={$offset}&amp;sort_mode={$sort_mode}&amp;editionId={$channels[user].editionId}">{icon _id='magnifier' alt="{tr}View{/tr}"}</a>
					{/if}
					{if ($channels[user].tiki_p_send_newsletters eq 'y') or ($channels[user].tiki_p_admin_newsletters eq 'y') }
						<a class="link" href="tiki-send_newsletters.php?nlId={$channels[user].nlId}&amp;editionId={$channels[user].editionId}">{icon _id='email' alt="{tr}Send Newsletter{/tr}"}</a>
					{else}
						&nbsp;
					{/if}
					{if $channels[user].tiki_p_admin_newsletters eq 'y'}
						<a class="link" href="{$url}?nlId={$channels[user].nlId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].editionId}" title="{tr}Remove{/tr}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
					{else}
						&nbsp;
					{/if}
				</td>
			</tr>
		{/section}
	</table>

	<div class="mini">
		{if $prev_offset >= 0}
			[<a class="prevnext" href="{$url}?nlId={$nlId}&amp;{$cur}_offset={$prev_offset}&amp;{$bak}_offset={$offset_bak}&amp;{$cur}_sort_mode={$sort_mode}&amp;{$bak}_sort_mode={$sort_mode_bak}&amp;cookietab={$tab}">{tr}Prev{/tr}</a>]&nbsp;
			{/if}
		{tr}Page:{/tr} {$actual_page}/{$cant_pages}
		{if $next_offset >= 0}
			&nbsp;[<a class="prevnext" href="{$url}?nlId={$nlId}&amp;{$cur}_offset={$next_offset}&amp;{$bak}_offset={$offset_bak}&amp;{$cur}_sort_mode={$sort_mode}&amp;{$bak}_sort_mode={$sort_mode_bak}&amp;cookietab={$tab}">{tr}Next{/tr}</a>]
	{/if}
		{if $prefs.direct_pagination eq 'y'}
			<br />
			{section loop=$cant_pages name=foo}
				{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
					<a class="prevnext" href="{$url}?nlId={$nlId}&amp;{$cur}_offset={$selector_offset}&amp;{$bak}_offset={$offset_bak}&amp;{$cur}_sort_mode={$sort_mode}&amp;{$bak}_sort_mode={$sort_mode_bak}&amp;cookietab={$tab}">
				{$smarty.section.foo.index_next}</a>&nbsp;
			{/section}
		{/if}
	</div>
</div>
