{* $Id$ *}
<h1><a class="pagetitle" href="tiki-list_trackers.php">{tr}Trackers{/tr}</a>
{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Trackers" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Trackers{/tr}">
<img border='0' src='img/icons/help.gif' alt="{tr}Help{/tr}" /></a>
{/if}
{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-list_trackers.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}List Trackers Tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}Edit Tpl{/tr}' /></a>
{/if}
{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=trackers" title="{tr}Admin Feature{/tr}">{icon _id='wrench' alt="{tr}Admin Feature{/tr}"}</a>
{/if}
</h1>

<div class="navbar">
{if $tiki_p_admin_trackers eq 'y'}
<span class="button2"><a href="tiki-admin_trackers.php?show=mod#mod" class="linkbut">{tr}Create Trackers{/tr}</a></span>
{/if}
</div>
{if ($channels) or ($find)}
<form method="get" action="tiki-list_trackers.php">
<table class="findtable"><tr>
<td>{tr}Find{/tr}</td>
<td><input type="text" name="find" value="{$find|escape}" /></td>
<td><input type="submit" value="{tr}Find{/tr}" name="search" /></td>
<td><input type="hidden" name="sort_mode" value="{$sort_mode|escape}" /></td>
</tr></table>
{if ($find) and ($channels)}
<p>{tr}Found{/tr} {$channels|@count} {tr}trackers{/tr}:</p>
{/if}
</form>
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
