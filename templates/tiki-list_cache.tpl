<h1><a href="tiki-list_cache.php" class="pagetitle">{tr}Cache{/tr}</a>
  
      {if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Cache" target="tikihelp" class="tikihelp" title="{tr}Admin Cache{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>{/if}

      {if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-list_cache.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Admin Cache tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}Edit Tpl{/tr}' /></a>{/if}</h1>

{remarksbox type="tip" title="{tr}Tip{/tr}"}
{tr}The cache is used by:{/tr} <a href="tiki-admin.php?page=general">{tr}Use cache for external pages{/tr}</a>
{/remarksbox}

{include file='find.tpl' _sort_mode='y'}

<div  align="center">
{cycle values="odd,even" print=false}
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-list_cache.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}URL{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_cache.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'refresh_desc'}refresh_asc{else}refresh_desc{/if}">{tr}Last updated{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{section name=changes loop=$listpages}
<tr>
<td class="{cycle advance=false}"><a class="link" href="{$listpages[changes].url}">{$listpages[changes].url}</a></td>
<td class="{cycle advance=false}">{$listpages[changes].refresh|tiki_short_datetime}</td>
<td class="{cycle}">
<a class="link" target="_blank" href="tiki-view_cache.php?cacheId={$listpages[changes].cacheId}" title="{tr}View{/tr}"><img src="pics/icons/magnifier.png" border="0" width="16" height="16" alt="{tr}View{/tr}" /></a>
<a class="link" href="tiki-list_cache.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].cacheId}" title="{tr}Remove{/tr}"><img src="pics/icons/cross.png" border="0" height="16" width="16" alt='{tr}Remove{/tr}' /></a>
<a class="link" href="tiki-list_cache.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;refresh={$listpages[changes].cacheId}" title="{tr}Refresh{/tr}"><img src="pics/icons/arrow_refresh.png" border="0" height="16" width="16" alt="{tr}Refresh{/tr}" /></a></td>
</tr>
{sectionelse}
<tr><td class="odd" colspan="3">
{tr}No records found{/tr}
</td></tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-list_cache.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-list_cache.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-list_cache.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
