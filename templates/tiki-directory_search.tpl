<h1><a class="pagetitle">{tr}Search results{/tr}</a></h1>
{* The navigation bar *}
{include file=tiki-directory_bar.tpl}
<br /><br />

<div align="center">
<form action="tiki-directory_search.php" method="post">
<input type="hidden" name="parent" value="{$parent|escape}" />
{tr}Search{/tr}: 
<select name="how">
<option value="or" {if $how eq 'or'}selected="selected"{/if}>{tr}any{/tr}</option>
<option value="and" {if $how eq 'and'}selected="selected"{/if}>{tr}All{/tr}</option>
</select>
<input type="text" name="words" value="{$words|escape}" size="30" />
<input type="hidden" name="where" value="all" />
<input type="submit" value="search" />
</form>
</div>


<div class="dirlistsites">
{if $items}
<form method="post" action="tiki-directory_search.php">
<input type="hidden" name="how" value="{$how|escape}" />
<input type="hidden" name="words" value="{$words|escape}" />
<input type="hidden" name="where" value="{$where|escape}" />
{tr}Sort by{/tr}: <select name="sort_mode">
<option value="name_desc" {if $sort_mode eq 'name_desc'}selected="selected"{/if}>{tr}name (desc){/tr}</option>
<option value="name_asc" {if $sort_mode eq 'name_asc'}selected="selected"{/if}>{tr}name (asc){/tr}</option>
<option value="hits_desc" {if $sort_mode eq 'hits_desc'}selected="selected"{/if}>{tr}hits (desc){/tr}</option>
<option value="hits_asc" {if $sort_mode eq 'hits_asc'}selected="selected"{/if}>{tr}hits (asc){/tr}</option>
<option value="created_desc" {if $sort_mode eq 'created_desc'}selected="selected"{/if}>{tr}creation date (desc){/tr}</option>
<option value="created_asc" {if $sort_mode eq 'created_asc'}selected="selected"{/if}>{tr}creation date (asc){/tr}</option>
<option value="lastModif_desc" {if $sort_mode eq 'lastModif_desc'}selected="selected"{/if}>{tr}last updated (desc){/tr}</option>
<option value="lastModif_asc" {if $sort_mode eq 'lastModif_asc'}selected="selected"{/if}>{tr}last updated (asc){/tr}</option>
</select>
<input type="submit" name="xx" value="sort" />
</form>
<br />
{/if}
{section name=ix loop=$items}
<div class="dirsite">
{if $prefs.directory_country_flag eq 'y'}
<img alt="flag" src="img/flags/{$items[ix].country}.gif" />
{/if}
<a class="dirsitelink" href="tiki-directory_redirect.php?siteId={$items[ix].siteId}" {if $prefs.directory_open_links eq 'n'}target='_blank'{/if}>{$items[ix].name}</a>
{if $tiki_p_admin_directory_sites eq 'y'} [<a class="dirsitelink" href="tiki-directory_admin_sites.php?siteId={$items[ix].siteId}">edit</a>]{/if} <br />
<span class="dirsitedesc">{$items[ix].description}</span><br />
{assign var=fsfs value=1}
<span class="dirsitecats">
{tr}Categories{/tr}:
{section name=ii loop=$items[ix].cats}
  {if $fsfs}{assign var=fsfs value=0}{else}, {/if}
  <a class="dirsublink" href="tiki-directory_browse.php?parent={$items[ix].cats[ii].categId}">{$items[ix].cats[ii].path}</a>
{/section}
</span><br />
<span class="dirsitetrail">{tr}Added{/tr}: {$items[ix].created|tiki_short_date} {tr}Last updated{/tr}: {$items[ix].lastModif|tiki_short_date} {tr}Hits{/tr}: {$items[ix].hits}</span>
</div>
{sectionelse}{tr}No records found.{/tr}
{/section}
</div>
<br />
{* next and prev links *}
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-directory_search.php?words={$words}&amp;how={$how}&amp;where={$where}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-directory_search.php?words={$words}&amp;how={$how}&amp;where={$where}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-directory_search.php?parent={$parent}&amp;words={$words}&amp;how={$how}&amp;where={$where}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>



