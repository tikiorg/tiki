<h1><a class="wiki" href="tiki-list_banners.php">{tr}Banners{/tr}</a></h1>
<a class="link" href="tiki-edit_banner.php">Create banner</a>
<br/><br/>
<div align="center">
<table border="1" cellpadding="0" cellspacing="0" width="97%">
<tr><td>Find</td>
   <td>
   <form method="get" action="tiki-list_articles.php">
     <input type="text" name="find" />
     <input type="submit" value="find" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table  border="1" width="97%" cellpadding="0" cellspacing="0">
<tr>
<td class="heading"><a class="link" href="tiki-list_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'bannerId_desc'}bannerId_asc{else}bannerId_desc{/if}">{tr}Id{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'client_desc'}client_asc{else}client_desc{/if}">{tr}Client{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'zone_desc'}zone_asc{else}zone_desc{/if}">{tr}Zone{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'which_desc'}which_asc{else}which_desc{/if}">{tr}Method{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'useDates_desc'}useDates_asc{else}useDates_desc{/if}">{tr}Use Dates?{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'maxImpressions_desc'}maxImpressions_asc{else}maxImpressions_desc{/if}">{tr}Max Impressions{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'impressions_desc'}impressions_asc{else}impressions_desc{/if}">{tr}Impressions{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'clicks_desc'}clicks_asc{else}clicks_desc{/if}">{tr}Clicks{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</a></td>
</tr>
{section name=changes loop=$listpages}
<tr>
{if $smarty.section.changes.index % 2}
<td class="odd">&nbsp;{$listpages[changes].bannerId}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].client}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].zone}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].created|date_format:"%d of %b, %Y"}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].which}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].useDates}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].maxImpressions}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].impressions}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].clicks}&nbsp;</td>
<td class="odd">
{if $tiki_p_admin_banners eq 'y'}
<a class="link" href="tiki-edit_banner.php?bannerId={$listpages[changes].bannerId}">{tr}Edit{/tr}</a>
<a class="link" href="tiki-list_banners.php?remove={$listpages[changes].bannerId}">{tr}Remove{/tr}</a>
{/if}
<a class="link" href="tiki-view_banner.php?bannerId={$listpages[changes].bannerId}">{tr}Stats{/tr}</a>
</td>
{else}
<td class="even">&nbsp;{$listpages[changes].bannerId}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].client}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].zone}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].created|date_format:"%d of %b, %Y"}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].which}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].useDates}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].maxImpressions}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].impressions}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].clicks}&nbsp;</td>
<td class="even">
{if $tiki_p_admin_banners eq 'y'}
<a class="link" href="tiki-edit_banner.php?bannerId={$listpages[changes].bannerId}">{tr}Edit{/tr}</a>
<a class="link" href="tiki-list_banners.php?remove={$listpages[changes].bannerId}">{tr}Remove{/tr}</a>
{/if}
<a class="link" href="tiki-view_banner.php?bannerId={$listpages[changes].bannerId}">{tr}Stats{/tr}</a>
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
[<a href="tiki-list_articles.php?&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a href="tiki-list_articles.php?&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>
</div>
