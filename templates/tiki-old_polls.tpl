{title help="polls" admpage="polls"}{tr}Polls{/tr}{/title}

{include file='find.tpl'}

<table class="normal">
<tr>
<th>{self_link _sort_arg='sort_mode' _sort_field='title' title="{tr}Title{/tr}"}{tr}Title{/tr}{/self_link}</th>
<th>{self_link _sort_arg='sort_mode' _sort_field='publishDate' title="{tr}Published{/tr}"}{tr}Published{/tr}{/self_link}</th>
<th>{self_link _sort_arg='sort_mode' _sort_field='votes' title="{tr}Votes{/tr}"}{tr}Votes{/tr}{/self_link}</th>
<th>{tr}Action{/tr}</th>
</tr>
{cycle values="odd,even" print=false}
{section name=changes loop=$listpages}
<tr class="{cycle}">
<td>{$listpages[changes].title|escape}</td>
<td>{$listpages[changes].publishDate|tiki_short_datetime}</td>
<td>{$listpages[changes].votes}</td>
<td>
<a class="link" href="tiki-poll_results.php?pollId={$listpages[changes].pollId}">{icon _id="chart_curve" alt="{tr}Results{/tr}"}</a>
{if $tiki_p_vote_poll ne 'n'}
	<a class="link" href="tiki-poll_form.php?pollId={$listpages[changes].pollId}">{tr}Vote{/tr}</a>
{/if}
</td>
</tr>
{sectionelse}
	{norecords _colspan=4}
{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
