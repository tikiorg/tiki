{* $Id$ *}

{title help="Trackers" admpage="trackers"}{tr}Trackers{/tr}{/title}

<div class="navbar">
  {if $tiki_p_admin_trackers eq 'y'}
    <span class="button2"><a href="tiki-admin_trackers.php?show=mod#mod" class="linkbut">{tr}Create Trackers{/tr}</a></span>
  {/if}
</div>

{if ($channels) or ($find)}
  {include file='find.tpl' _sort_mode='y'}
  {if ($find) and ($channels)}
    <p>{tr}Found{/tr} {$channels|@count} {tr}trackers{/tr}:</p>
  {/if}
{/if}
<!-- beginning of table -->
<table class="normal">
<tr>
<td class="heading">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}</td>
<td class="heading">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='description'}{tr}Description{/tr}{/self_link}</td>
<td class="heading">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='created'}{tr}Created{/tr}{/self_link}</td>
<td class="heading">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='lastModif'}{tr}Last Modif{/tr}{/self_link}</td>
<td class="heading" style="text-align:right;">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='items'}{tr}Items{/tr}{/self_link}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
{if $channels[user].individual eq 'n' or $channels[user].individual_tiki_p_view_trackers eq 'y'}
<tr class="{cycle}">
	<td><a class="tablename" href="tiki-view_tracker.php?trackerId={$channels[user].trackerId}">{$channels[user].name}</a></td>
	{if $channels[user].descriptionIsParsed eq 'y' }
		<td>{wiki}{$channels[user].description}{/wiki}</td>
	{else}
		<td>{$channels[user].description|escape|nl2br}</td>
	{/if}
<td>{$channels[user].created|tiki_short_datetime}</td>
<td>{$channels[user].lastModif|tiki_short_datetime}</td>
<td style="text-align:right;">{$channels[user].items}</td>
{/if}
</tr>
{sectionelse}
<tr><td colspan="5" class="odd">{tr}No records found{/tr}{if $find} {tr}with{/tr}: {$find}{/if}.</td></tr>
{/section}
</table>
<!-- Beginning of the prev/next advance buttons found at bottom of page -->
{pagination_links cant=$channels_cant step=$prefs.maxRecords offset=$offset}{/pagination_links}
