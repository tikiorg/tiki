{title help="Cache"}{tr}External Pages Cache{/tr}{/title}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}The cache is used by:{/tr} <a href="tiki-admin.php?page=textarea">{tr}Cache external pages{/tr}</a>
{/remarksbox}

{include file='find.tpl'}

{cycle values="odd,even" print=false}
<table class="normal">
	<tr>
		<th>
			<a href="tiki-list_cache.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}URL{/tr}</a>
		</th>
		<th>
			<a href="tiki-list_cache.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'refresh_desc'}refresh_asc{else}refresh_desc{/if}">{tr}Last updated{/tr}</a>
		</th>
		<th>{tr}Action{/tr}</th>
	</tr>
	{section name=changes loop=$listpages}
		<tr class="{cycle}">
			<td class="text">
				<a class="link" href="{$listpages[changes].url}">{$listpages[changes].url}</a>
			</td>
			<td class="date">
				{$listpages[changes].refresh|tiki_short_datetime}
			</td>
			<td class="action">
				<a class="link" target="_blank" href="tiki-view_cache.php?cacheId={$listpages[changes].cacheId}" title="{tr}View{/tr}"><img src="pics/icons/magnifier.png" width="16" height="16" alt="{tr}View{/tr}" /></a>
				<a class="link" href="tiki-list_cache.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].cacheId}" title="{tr}Remove{/tr}"><img src="pics/icons/cross.png" height="16" width="16" alt="{tr}Remove{/tr}" /></a>
				<a class="link" href="tiki-list_cache.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;refresh={$listpages[changes].cacheId}" title="{tr}Refresh{/tr}"><img src="pics/icons/arrow_refresh.png" height="16" width="16" alt="{tr}Refresh{/tr}" /></a>
			</td>
		</tr>
	{sectionelse}
		{norecords _colspan=3}
	{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
