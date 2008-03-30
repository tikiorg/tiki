{* $Id$ *}
<h1><a class="pagetitle" href="tiki-list_banners.php">{tr}Banners{/tr}</a>
{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Banners" target="tikihelp" class="tikihelp" title="{tr}Admin Banners{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>{/if}
{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-list_banners.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Admin Banners tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}Edit Tpl{/tr}' /></a>{/if}</h1>

<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}To use a banner in a <a class="rbox-link" href="tiki-admin_modules.php">module</a> or a template, use {literal}{banner zone=ABC}{/literal}, where ABC is the name of the zone.{/tr}</div>
</div>
<br />

<div class="navbar">
<a class="linkbut" href="tiki-edit_banner.php">{tr}Create banner{/tr}</a>
</div>
{if $listpages}
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-list_banners.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
{/if}
<br />
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'bannerId_desc'}bannerId_asc{else}bannerId_desc{/if}">{tr}Id{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'client_desc'}client_asc{else}client_desc{/if}">{tr}Client{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}URL{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'zone_desc'}zone_asc{else}zone_desc{/if}">{tr}Zone{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'which_desc'}which_asc{else}which_desc{/if}">{tr}Method{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'useDates_desc'}useDates_asc{else}useDates_desc{/if}">{tr}Use Dates?{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'maxImpressions_desc'}maxImpressions_asc{else}maxImpressions_desc{/if}">{tr}Max Impressions{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'impressions_desc'}impressions_asc{else}impressions_desc{/if}">{tr}Impressions{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'clicks_desc'}clicks_asc{else}clicks_desc{/if}">{tr}Clicks{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=changes loop=$listpages}
<tr>
<td class="{cycle advance=false}">{$listpages[changes].bannerId}</td>
<td class="{cycle advance=false}">{$listpages[changes].client}</td>
<td class="{cycle advance=false}">{$listpages[changes].url}</td>
<td class="{cycle advance=false}">{$listpages[changes].zone}</td>
<td class="{cycle advance=false}">{$listpages[changes].created|tiki_short_date}</td>
<td class="{cycle advance=false}">{$listpages[changes].which}</td>
<td class="{cycle advance=false}">{$listpages[changes].useDates}</td>
<td class="{cycle advance=false}">{$listpages[changes].maxImpressions}</td>
<td class="{cycle advance=false}">{$listpages[changes].impressions}</td>
<td class="{cycle advance=false}">{$listpages[changes].clicks}</td>
<td class="{cycle}">
{if $tiki_p_admin_banners eq 'y'}
<a class="link" href="tiki-edit_banner.php?bannerId={$listpages[changes].bannerId}">{icon _id='page_edit'}</a>
<a class="link" href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].bannerId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
{/if}
<a class="link" href="tiki-view_banner.php?bannerId={$listpages[changes].bannerId}">{tr}Stats{/tr}</a>
</td>
</tr>
{sectionelse}
<tr><td class="odd" colspan="11">
{tr}No records found{/tr}
</td></tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-list_banners.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-list_banners.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-list_banners.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}

</div>
