<a class="pagetitle" href="tiki-list_banners.php">{tr}Banners{/tr}</a>

<!-- the help link info -->
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=Banners" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}admin Banners{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}

<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-list_banners.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin Banners tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='edit tpl' /></a>{/if}

<!-- begin -->

<br /><br />
<a class="linkbut" href="tiki-edit_banner.php">[{tr}Create banner{/tr}]</a>
<br /><br />
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Search{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-list_banners.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Go{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'bannerId_desc'}bannerId_asc{else}bannerId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'client_desc'}client_asc{else}client_desc{/if}">{tr}Client{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'zone_desc'}zone_asc{else}zone_desc{/if}">{tr}Zone{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'which_desc'}which_asc{else}which_desc{/if}">{tr}Method{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'useDates_desc'}useDates_asc{else}useDates_desc{/if}">{tr}Use Dates?{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'maxImpressions_desc'}maxImpressions_asc{else}maxImpressions_desc{/if}">{tr}Max Impressions{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'impressions_desc'}impressions_asc{else}impressions_desc{/if}">{tr}Impressions{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'clicks_desc'}clicks_asc{else}clicks_desc{/if}">{tr}Clicks{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</a></td>
</tr>
{section name=changes loop=$listpages}
<tr>
{if $smarty.section.changes.index % 2}
<td class="odd">&nbsp;{$listpages[changes].bannerId}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].client}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].zone}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].created|tiki_short_date}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].which}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].useDates}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].maxImpressions}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].impressions}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].clicks}&nbsp;</td>
<td class="odd">
{if $tiki_p_admin_banners eq 'y'}
<a class="link" href="tiki-edit_banner.php?bannerId={$listpages[changes].bannerId}">{tr}Edit{/tr}</a>
<a class="link" href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].bannerId}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this banner?{/tr}')">{tr}Remove{/tr}</a>
{/if}
<a class="link" href="tiki-view_banner.php?bannerId={$listpages[changes].bannerId}">{tr}Statistics{/tr}</a>
</td>
{else}
<td class="even">&nbsp;{$listpages[changes].bannerId}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].client}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].zone}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].created|tiki_short_date}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].which}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].useDates}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].maxImpressions}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].impressions}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].clicks}&nbsp;</td>
<td class="even">
{if $tiki_p_admin_banners eq 'y'}
<a class="link" href="tiki-edit_banner.php?bannerId={$listpages[changes].bannerId}">{tr}Edit{/tr}</a>
<a class="link" href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].bannerId}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this banner?{/tr}')">{tr}Delete{/tr}</a>
{/if}
<a class="link" href="tiki-view_banner.php?bannerId={$listpages[changes].bannerId}">{tr}Statistics{/tr}</a>
</td>
{/if}
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-list_banners.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-list_banners.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-list_banners.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}

</div>
</div>
