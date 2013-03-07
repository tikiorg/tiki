{title}{tr}Search results{/tr}{/title}

{* The navigation bar *}
{include file='tiki-directory_bar.tpl'} <br />
<br />
<div align="center">
  <form action="tiki-directory_search.php" method="post">
    <input type="hidden" name="parent" value="{$parent|escape}" />
    {tr}Search:{/tr}
    <select name="how">
      <option value="or" {if $how eq 'or'}selected="selected"{/if}>{tr}any{/tr}</option>
      <option value="and" {if $how eq 'and'}selected="selected"{/if}>{tr}All{/tr}</option>
    </select>
    <input type="text" name="words" value="{$words|escape}" size="30" />
    <input type="hidden" name="where" value="all" />
    <input type="submit" value="search" />
  </form>
</div>
<div class="dirlistsites"> {if $items}
  <form method="post" action="tiki-directory_search.php">
    <input type="hidden" name="how" value="{$how|escape}" />
    <input type="hidden" name="words" value="{$words|escape}" />
    <input type="hidden" name="where" value="{$where|escape}" />
    {tr}Sort by:{/tr}
    <select name="sort_mode">
      <option value="name_desc" {if $sort_mode eq 'name_desc'}selected="selected"{/if}>{tr}Name (desc){/tr}</option>
      <option value="name_asc" {if $sort_mode eq 'name_asc'}selected="selected"{/if}>{tr}Name (asc){/tr}</option>
      <option value="hits_desc" {if $sort_mode eq 'hits_desc'}selected="selected"{/if}>{tr}Hits (desc){/tr}</option>
      <option value="hits_asc" {if $sort_mode eq 'hits_asc'}selected="selected"{/if}>{tr}Hits (asc){/tr}</option>
      <option value="created_desc" {if $sort_mode eq 'created_desc'}selected="selected"{/if}>{tr}Creation Date (desc){/tr}</option>
      <option value="created_asc" {if $sort_mode eq 'created_asc'}selected="selected"{/if}>{tr}Creation Date (asc){/tr}</option>
      <option value="lastModif_desc" {if $sort_mode eq 'lastModif_desc'}selected="selected"{/if}>{tr}Last updated (desc){/tr}</option>
      <option value="lastModif_asc" {if $sort_mode eq 'lastModif_asc'}selected="selected"{/if}>{tr}Last updated (asc){/tr}</option>
    </select>
    <input type="submit" name="xx" value="sort" />
  </form>
  <br />
  {/if}
  {section name=ix loop=$items}
  <div class="dirsite"> {if $prefs.directory_country_flag eq 'y'} <img alt="flag" src="img/flags/{$items[ix].country}.gif" /> {/if} <a class="dirsitelink" href="tiki-directory_redirect.php?siteId={$items[ix].siteId}" {if $prefs.directory_open_links eq 'n'}target='_blank'{/if}>{$items[ix].name}</a> {if $tiki_p_admin_directory_sites eq 'y'} [<a class="dirsitelink" href="tiki-directory_admin_sites.php?siteId={$items[ix].siteId}">edit</a>]{/if} <br />
    <span class="dirsitedesc">{$items[ix].description}</span><br />
    {assign var=fsfs value=1} <span class="dirsitecats"> {tr}Directory Categories:{/tr}
    {section name=ii loop=$items[ix].cats}
    {if $fsfs}{assign var=fsfs value=0}{else}, {/if} <a class="dirsublink" href="tiki-directory_browse.php?parent={$items[ix].cats[ii].categId}">{$items[ix].cats[ii].path}</a> {/section} </span><br />
    <span class="dirsitetrail">{tr}Added:{/tr} {$items[ix].created|tiki_short_date} {tr}Last updated:{/tr} {$items[ix].lastModif|tiki_short_date} {tr}Hits:{/tr} {$items[ix].hits}</span> </div>
  {sectionelse}{tr}No records found.{/tr}
  {/section} </div>
{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links} 
