{title help="Cache"}{tr}External Pages Cache{/tr}{/title}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}The cache is used by:{/tr} <a href="tiki-admin.php?page=textarea">{tr}Cache external pages{/tr}</a>
{/remarksbox}

{include file='find.tpl'}


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
<div class="{if $js === 'y'}table-responsive{/if}"> {* table-responsive class cuts off css drop-down menus *}
	<table class="table">
		<tr>
			<th>
				<a href="tiki-list_cache.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}URL{/tr}</a>
			</th>
			<th>
				<a href="tiki-list_cache.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'refresh_desc'}refresh_asc{else}refresh_desc{/if}">{tr}Last updated{/tr}</a>
			</th>
			<th></th>
		</tr>
		{section name=changes loop=$listpages}
			<tr>
				<td class="text">
					<a class="link" href="{$listpages[changes].url}">{$listpages[changes].url}</a>
				</td>
				<td class="date">
					{$listpages[changes].refresh|tiki_short_datetime}
				</td>
				<td class="action">
					{capture name=cache_actions}
						{strip}
							{$libeg}<a target="_blank" href="tiki-view_cache.php?cacheId={$listpages[changes].cacheId}">
								{icon name="view" _menu_text='y' _menu_icon='y' alt="{tr}View{/tr}"}
							</a>{$liend}
							{$libeg}<a href="tiki-list_cache.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;refresh={$listpages[changes].cacheId}"">
								{icon name="refresh" _menu_text='y' _menu_icon='y' alt="{tr}Refresh{/tr}"}
							</a>{$liend}
							{$libeg}<a href="tiki-list_cache.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].cacheId}">
								{icon name="remove" _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
							</a>{$liend}
						{/strip}
					{/capture}
					{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
					<a
						class="tips"
						title="{tr}Actions{/tr}"
						href="#"
						{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.cache_actions|escape:"javascript"|escape:"html"}{/if}
						style="padding:0; margin:0; border:0"
					>
						{icon name='wrench'}
					</a>
					{if $js === 'n'}
						<ul class="dropdown-menu" role="menu">{$smarty.capture.cache_actions}</ul></li></ul>
					{/if}
				</td>
			</tr>
		{sectionelse}
			{norecords _colspan=3}
		{/section}
	</table>
</div>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
