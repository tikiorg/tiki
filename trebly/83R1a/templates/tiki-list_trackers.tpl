{* $Id: tiki-list_trackers.tpl 38323 2011-10-17 12:47:00Z lphuberdeau $ *}

{title help="Trackers" admpage="trackers"}{tr}Trackers{/tr}{/title}

<div class="navbar">
	{include file="tracker_actions.tpl"}
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
		<th>{self_link _sort_arg='sort_mode' _sort_field='created'}{tr}Created{/tr}{/self_link}</th>
		<th>{self_link _sort_arg='sort_mode' _sort_field='lastModif'}{tr}Last Modif{/tr}{/self_link}</th>
		<th style="text-align:right;">{self_link _sort_arg='sort_mode' _sort_field='items'}{tr}Items{/tr}{/self_link}</th>
	</tr>
	{cycle values="odd,even" print=false}
	{section name=user loop=$channels}
		{if $channels[user].individual eq 'n' or $channels[user].individual_tiki_p_view_trackers eq 'y'}
			<tr class="{cycle}">
				<td class="text">
					<a class="tablename trackerLink" trackerId="{$channels[user].trackerId}" href="tiki-view_tracker.php?trackerId={$channels[user].trackerId}">{$channels[user].name|escape}</a>
					<div class="description">
						{if $channels[user].descriptionIsParsed eq 'y'}
							{wiki}{$channels[user].description}{/wiki}
						{else}
							{$channels[user].description|escape|nl2br}
						{/if}
					</div>
				</td>
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
