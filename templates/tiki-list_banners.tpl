{* $Id$ *}

{title help="Banners" admpage=ads}{tr}Banners{/tr}{/title}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}To use a banner in a <a class="rbox-link" href="tiki-admin_modules.php">module</a> or a template, use {literal}{banner zone=ABC}{/literal}, where ABC is the name of the zone.{/tr}{/remarksbox}

<div class="navbar">
	{button href="tiki-edit_banner.php" _text="{tr}Create banner{/tr}"}
</div>

{if $listpages or ($find ne '')}
  {include file='find.tpl' _sort_mode='y'}
{/if}
<table class="normal">
<tr>
<th><a href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'bannerId_desc'}bannerId_asc{else}bannerId_desc{/if}">{tr}Id{/tr}</a></th>
<th><a href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'client_desc'}client_asc{else}client_desc{/if}">{tr}Client{/tr}</a></th>
<th><a href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}URL{/tr}</a></th>
<th><a href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'zone_desc'}zone_asc{else}zone_desc{/if}">{tr}Zone{/tr}</a></th>
<th><a href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></th>
<th><a href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'which_desc'}which_asc{else}which_desc{/if}">{tr}Method{/tr}</a></th>
<th><a href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'useDates_desc'}useDates_asc{else}useDates_desc{/if}">{tr}Use Dates?{/tr}</a></th>
<th><a href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'maxImpressions_desc'}maxImpressions_asc{else}maxImpressions_desc{/if}">{tr}Max Impressions{/tr}</a></th>
<th><a href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'impressions_desc'}impressions_asc{else}impressions_desc{/if}">{tr}Impressions{/tr}</a></th>
<th><a href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'maxClicks_desc'}maxImpressions_asc{else}maxClicks_desc{/if}">{tr}Max Clicks{/tr}</a></th>
<th><a href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'clicks_desc'}clicks_asc{else}clicks_desc{/if}">{tr}Clicks{/tr}</a></th>
<th>{tr}Action{/tr}</th>
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
<td class="{cycle advance=false}">{$listpages[changes].maxClicks}</td>
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
