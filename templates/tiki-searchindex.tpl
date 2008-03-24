{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-searchindex.tpl,v 1.20.2.3 2007-12-15 01:28:04 luciash Exp $ *}
{if !( $searchNoResults ) }
<h1>{tr}Search results{/tr}:</h1>
{/if}

{if !( $searchStyle eq "menu" )}
  <div class="nohighlight">
    {tr}Search in{/tr}:
    <br /><br />
    {foreach item=name key=k from=$where_list}
      <a class="linkbut" {if $where eq $k}id="highlight"{/if} href="tiki-searchindex.php?highlight={$words}&amp;where={$k}">{tr}{$name}{/tr}</a>
    {/foreach}
  </div><!--nohighlight-->

  <br /><br /> 

{if $words neq ''}{tr}Found{/tr} "{$words}" {tr}in{/tr} {$cant_results} {$where2}{/if}
{/if}
<form class="forms" method="get" action="tiki-searchindex.php">
    {tr}Find{/tr} <input id="fuser" name="highlight" size="14" type="text" accesskey="s" value="{$words}"/>
{if ( $searchStyle eq "menu" )}
    {tr}in{/tr}
    <select name="where">
    {foreach item=name key=k from=$where_list}
    <option value="{$k}">{tr}{$name}{/tr}</option>
    {/foreach}
    </select>
{else}
    <input type="hidden" name="where" value="{$where|escape}" />
{/if}
    <input type="submit" class="wikiaction" name="search" value="{tr}Go{/tr}"/>
</form>

{if $words neq ''}
<div class="searchresults">
{if !($searchNoResults) }
<br /><br />
{section name=search loop=$results}
<b>{tr}{$results[search].location}{/tr}:&nbsp;<a href="{$results[search].href}&amp;highlight={$words}{$results[search].anchor}" class="wiki">{$results[search].pageName|strip_tags|escape}</a> ({tr}Hits{/tr}: {$results[search].hits})</b>
{if $prefs.feature_search_fulltext eq 'y'}
	{if $results[search].relevance <= 0}
		&nbsp;({tr}Simple search{/tr})
	{else}
		&nbsp;({tr}Relevance{/tr}: {$results[search].relevance})
	{/if}
{/if}
{if $results[search].type > ''}
&nbsp;({$results[search].type})
{/if}

<br />
<div class="searchdesc">{$results[search].data|strip_tags|truncate:250:'...'}</div>
<div class="searchdate">{tr}Last modification date{/tr}: {$results[search].lastModif|tiki_long_datetime}</div><br />
{sectionelse}
{tr}No pages matched the search criteria{/tr}
{/section}
</div>

{if $cant_pages gt 0}<div class="mini">
{if $prev_offset >= 0}
[<a class="link" href="tiki-searchindex.php?where={$where}&amp;highlight={$words}&amp;offset={$prev_offset}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="link" href="tiki-searchindex.php?where={$where}&amp;highlight={$words}&amp;offset={$next_offset}">{tr}Next{/tr}</a>]
{/if}
</div>{/if}
{/if}
{/if}
