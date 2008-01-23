{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-list_trackers.tpl,v 1.28.2.2 2008-01-23 14:18:50 nyloth Exp $ *}
<h1><a class="pagetitle" href="tiki-list_trackers.php">{tr}Trackers{/tr}</a>
{if $prefs.feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=Trackers#id187996" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Trackers{/tr}">
<img border='0' src='img/icons/help.gif' alt="{tr}Help{/tr}" /></a>
{/if}
{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-list_trackers.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}List Trackers Tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}Edit Tpl{/tr}' /></a>
{/if}
{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=trackers" title="{tr}Admin Feature{/tr}">{html_image file='pics/icons/wrench.png' border='0'  alt="{tr}Admin Feature{/tr}"}</a>
{/if}
</h1>

<div class="navbar">
{if $tiki_p_admin_trackers eq 'y'}
<span class="button2"><a href="tiki-admin_trackers.php?show=mod#mod" class="linkbut">{tr}Create trackers{/tr}</a></span>
{/if}
</div>

<form method="get" action="tiki-list_trackers.php">
<table class="findtable"><tr>
<td>{tr}Find{/tr}</td>
<td><input type="text" name="find" value="{$find|escape}" /></td>
<td><input type="submit" value="{tr}Find{/tr}" name="search" /></td>
<td><input type="hidden" name="sort_mode" value="{$sort_mode|escape}" /></td>
</tr></table>
</form>

<!-- beginning of table -->
<table class="normal">
<tr>
<td class="heading">
<a class="tableheading" href="tiki-list_trackers.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a>
</td>
<td class="heading">
<a class="tableheading" href="tiki-list_trackers.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a>
</td>
<td class="heading">
<a class="tableheading" href="tiki-list_trackers.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a>
</td>
<td class="heading"><a class="tableheading" href="tiki-list_trackers.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}Last Modif{/tr}</a></td>
<td style="text-align:right;" class="heading"><a class="tableheading" href="tiki-list_trackers.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'items_desc'}items_asc{else}items_desc{/if}">{tr}Items{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
{if $channels[user].individual eq 'n' or $channels[user].individual_tiki_p_view_trackers eq 'y'}
<tr class="{cycle}">
<td><a class="tablename" href="tiki-view_tracker.php?trackerId={$channels[user].trackerId}">{$channels[user].name}</a></td>
<td>{$channels[user].description}</td>
<td>{$channels[user].created|tiki_short_datetime}</td>
<td>{$channels[user].lastModif|tiki_short_datetime}</td>
<td style="text-align:right;">{$channels[user].items}</td>
{/if}
</tr>
{sectionelse}
<tr><td colspan="5" class="odd">{tr}No records found{/tr}</td></tr>
{/section}
</table>
<! --- Beginning of the prev/next advance buttons found at bottom of page -->
{pagination_links cant=$channels_cant step=$prefs.maxRecords offset=$offset}{/pagination_links}
