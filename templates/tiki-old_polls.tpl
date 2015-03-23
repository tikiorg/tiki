{title help="polls" admpage="polls"}{tr}Polls{/tr}{/title}

{include file='find.tpl'}
<div class="table-responsive">
<table class="table normal table-striped table-hover">
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
			<a href="tiki-poll_results.php?pollId={$listpages[changes].pollId}">
				{icon name='chart' _menu_text='y' _menu_icon='y' alt="{tr}Results{/tr}"}
			</a>
			{if $tiki_p_vote_poll ne 'n'}
				<a href="tiki-poll_form.php?pollId={$listpages[changes].pollId}">
					{icon name='ok' _menu_text='y' _menu_icon='y' alt="{tr}Vote{/tr}"}
				</a>
			{/if}
		{/strip}
	{/capture}
	<a class="tips"
	   title="{tr}Actions{/tr}"
	   href="#" {popup trigger="click" fullhtml="1" center=true text=$smarty.capture.old_poll_actions|escape:"javascript"|escape:"html"}
	   style="padding:0; margin:0; border:0"
			>
		{icon name='wrench'}
	</a>
</td>
</tr>
{sectionelse}
	{norecords _colspan=4}
{/section}
</table>
</div>
{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
