{title help="polls" admpage="polls"}{tr}Polls{/tr}{/title}

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
<div class="{if $js === 'y'}table-responsive{/if}"> {*the table-responsive class cuts off dropdown menus *}
<table class="table table-striped table-hover">
<tr>
<th>{self_link _sort_arg='sort_mode' _sort_field='title' title="{tr}Title{/tr}"}{tr}Title{/tr}{/self_link}</th>
<th>{self_link _sort_arg='sort_mode' _sort_field='publishDate' title="{tr}Published{/tr}"}{tr}Published{/tr}{/self_link}</th>
<th>{self_link _sort_arg='sort_mode' _sort_field='votes' title="{tr}Votes{/tr}"}{tr}Votes{/tr}{/self_link}</th>
<th></th>
</tr>

{section name=changes loop=$listpages}
<tr>
<td class="text">{$listpages[changes].title|escape}</td>
<td class="date">{$listpages[changes].publishDate|tiki_short_datetime}</td>
<td class="text">{$listpages[changes].votes}</td>
<td class="action">
	{capture name=old_poll_actions}
		{strip}
			{$libeg}<a href="tiki-poll_results.php?pollId={$listpages[changes].pollId}">
				{icon name='chart' _menu_text='y' _menu_icon='y' alt="{tr}Results{/tr}"}
			</a>{$liend}
			{if $tiki_p_vote_poll ne 'n'}
				{$libeg}<a href="tiki-poll_form.php?pollId={$listpages[changes].pollId}">
					{icon name='ok' _menu_text='y' _menu_icon='y' alt="{tr}Vote{/tr}"}
				</a>{$liend}
			{/if}
		{/strip}
	{/capture}
	{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
	<a
		class="tips"
		title="{tr}Actions{/tr}"
		href="#"
		{if $js === 'y'}{popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.old_poll_actions|escape:"javascript"|escape:"html"}{/if}
		style="padding:0; margin:0; border:0"
	>
		{icon name='wrench'}
	</a>
	{if $js === 'n'}
		<ul class="dropdown-menu" role="menu">{$smarty.capture.old_poll_actions}</ul></li></ul>
	{/if}
</td>
</tr>
{sectionelse}
	{norecords _colspan=4}
{/section}
</table>
</div>
{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
