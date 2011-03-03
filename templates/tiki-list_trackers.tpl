{title help="Trackers" admpage="trackers"}{tr}Trackers{/tr}{/title}

<div class="navbar">
	{if $tiki_p_admin_trackers eq 'y'}
		{button href="tiki-admin_trackers.php?show=mod#mod" _text="{tr}Create Tracker{/tr}"}
	{/if}
</div>

{if ($channels) or ($find)}
	{include file='find.tpl'}
	{if ($find) and ($channels)}
		<p>{tr}Found{/tr} {$channels|@count} {tr}trackers:{/tr}</p>
	{/if}
{/if}
<!-- beginning of table -->
<table class="normal">
	<tr>
		<th>{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}</th>
		<th>{self_link _sort_arg='sort_mode' _sort_field='description'}{tr}Description{/tr}{/self_link}</th>
		<th>{self_link _sort_arg='sort_mode' _sort_field='created'}{tr}Created{/tr}{/self_link}</th>
		<th>{self_link _sort_arg='sort_mode' _sort_field='lastModif'}{tr}Last Modif{/tr}{/self_link}</th>
		<th style="text-align:right;">{self_link _sort_arg='sort_mode' _sort_field='items'}{tr}Items{/tr}{/self_link}</th>
	</tr>
	{cycle values="odd,even" print=false}
	{section name=user loop=$channels}
		{if $channels[user].individual eq 'n' or $channels[user].individual_tiki_p_view_trackers eq 'y'}
			<tr class="{cycle}">
				<td class="text"><a class="tablename trackerLink" trackerId="{$channels[user].trackerId}" href="tiki-view_tracker.php?trackerId={$channels[user].trackerId}">{$channels[user].name|escape}</a></td>
				{if $channels[user].descriptionIsParsed eq 'y'}
					<td class="text">{wiki}{$channels[user].description}{/wiki}</td>
				{else}
					<td class="text">{$channels[user].description|escape|nl2br}</td>
				{/if}
				<td class="date">{$channels[user].created|tiki_short_datetime}</td>
				<td class="date">{$channels[user].lastModif|tiki_short_datetime}</td>
				<td class="integer">{$channels[user].items}</td>
			</tr>
		{/if}
	{sectionelse}
		{if $find}{norecords _colspan=5 _text="No records found with: $find"}{else}{norecords _colspan=5}{/if}
	{/section}
</table>

{pagination_links cant=$channels_cant step=$prefs.maxRecords offset=$offset}{/pagination_links}
