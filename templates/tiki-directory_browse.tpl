{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-directory_browse.tpl,v 1.26.2.2 2008-02-05 16:14:44 jyhem Exp $ *}

{* The heading and category path *}
{if $prefs.feature_siteidentity ne 'y' or $prefs.feature_breadcrumbs ne 'y'}
<h1><a class="pagetitle" href="tiki-directory_browse.php?parent={$parent}">{tr}Directory{/tr}</a></h1>
{else}
<div id="pageheader">
{breadcrumbs type="trail" loc="page" crumbs=$crumbs}{breadcrumbs type="pagetitle"
loc="page" crumbs=$crumbs}
{breadcrumbs type="desc" loc="page" crumbs=$crumbs}
</div>
{/if}
{* The navigation bar *}
{include file=tiki-directory_bar.tpl}

{* The category path *}
{if $prefs.feature_siteidentity ne 'y' or $prefs.feature_breadcrumbs ne 'y'}
<a class="dirlink" href="tiki-directory_browse.php?parent=0">Top</a>{if $parent > 0} >> {/if}{$path}
{/if}
<div class="description">{$parent_info.description}</div>
<br /><br />
{if count($items) > 0}
<div class="findtable">
<form action="tiki-directory_search.php" method="post">
<input type="hidden" name="parent" value="{$parent|escape}" />
{tr}Find{/tr}: 
<select name="how">
<option value="or">{tr}any{/tr}</option>
<option value="and">{tr}All{/tr}</option>
</select>
<input type="text" name="words" size="30" />
<select name="where">
<option value="all">{tr}in entire directory{/tr}</option>
<option value="cat">{tr}in current category{/tr}</option>
</select>
<input type="submit" value="{tr}Search{/tr}" />
</form>
</div>
{/if}
{if count($categs)}
<br /><br />
<b>{tr}Subcategories{/tr}</b><br />
{* The table with the subcategories *}
<div class="dircategs">
<table  >
    <tr>
    {section name=numloop loop=$categs}
        <td><a class="dirlink" href="tiki-directory_browse.php?parent={$categs[numloop].categId}">{$categs[numloop].name}</a>
        {if $categs[numloop].showCount eq 'y'}
        ({$categs[numloop].sites})
        {/if}
        <br />
        {* Now display subcats if any *}
        {section name=ix loop=$categs[numloop].subcats}
        {if $categs[numloop].childrenType ne 'd'}
	<a class="dirsublink" href="tiki-directory_browse.php?parent={$categs[numloop].subcats[ix].categId}">{$categs[numloop].subcats[ix].name}</a>
	{else}
	{$categs[numloop].subcats[ix].name}
	{/if}
        {if $categs[numloop].subcats[ix].showCount eq 'y'} 	        
        ({$categs[numloop].subcats[ix].sites})
        {/if}
        {/section}
        </td>
        {* see if we should go to the next row *}
        {if not ($smarty.section.numloop.rownum mod $cols)}
                {if not $smarty.section.numloop.last}
                        </tr><tr>
                {/if}
        {/if}
        {if $smarty.section.numloop.last}
                {* pad the cells not yet created *}
                {math equation = "n - a % n" n=$cols a=$data|@count assign="cells"}
                {if $cells ne $cols}
                {section name=pad loop=$cells}
                        <td>&nbsp;</td>
                {/section}
                {/if}
                </tr>
        {/if}
    {/section}
</table>
</div>
{/if}
<br />
{* The links *}
{if $categ_info.allowSites eq 'y' and count($items) > 0}
<b>{tr}Links{/tr}</b><br />
<div class="dirlistsites">
<form method="post" action="tiki-directory_browse.php">
<input type="hidden" name="parent" value="{$parent|escape}" />
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
<input type="submit" name="xx" value="{tr}sort{/tr}" />
</form>
<br />
{section name=ix loop=$items}
<div class="dirsite">
{if $prefs.directory_country_flag eq 'y'}
<img alt="flag" src="img/flags/{$items[ix].country}.gif" />
{/if}
<a class="dirsitelink" href="tiki-directory_redirect.php?siteId={$items[ix].siteId}" {if $prefs.directory_open_links eq 'n'}target='_blank'{/if}>{$items[ix].name}</a>
{if $tiki_p_admin_directory_sites eq 'y'} [<a class="dirsitelink" href="tiki-directory_admin_sites.php?parent={$parent}&amp;siteId={$items[ix].siteId}">{tr}Edit{/tr}</a>]{/if} 
{if $prefs.cachepages eq 'y'}(<a  class="dirsitelink" href="tiki-view_cache.php?url={$items[ix].url}" target="_blank">{tr}Cache{/tr}</a>){/if}
<br />
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
{/section}
</div>
<br />
{* next and prev links *}
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-directory_browse.php?parent={$parent}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-directory_browse.php?parent={$parent}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-directory_browse.php?parent={$parent}&amp;find={$find}&amp;offset={$selector_offset*2}&amp;sort_mode={$sort_mode}"> {*selector_offset is calculated wrong internal; temporary bug fix by w-o-g*}
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
{else}{tr}No records.{/tr}
{/if}

{if count($related)>0}
<div class="dirrelated">
{tr}Related categories{/tr}<br /><br />
{section name=ix loop=$related}
<a class="dirlink" href="tiki-directory_browse.php?parent={$related[ix].relatedTo}">{$related[ix].path}</a><br />
{/section}
</div>
{/if}

{include file=tiki-directory_footer.tpl}
