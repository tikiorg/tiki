{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-searchindex.tpl,v 1.20 2007-10-04 22:17:42 nyloth Exp $ *}
{if !( $searchNoResults ) }
<h1>{tr}Search results{/tr}:</h1>
{/if}

{if !( $searchStyle eq "menu" )}
<div class="nohighlight">
{tr}Search in{/tr}:<br />
 <a class="linkbut" href="tiki-searchindex.php?highlight={$words}&amp;where=pages">{tr}All{/tr}</a>
{if $prefs.feature_wiki eq 'y'}
 <a class="linkbut" href="tiki-searchindex.php?highlight={$words}&amp;where=wikis">{tr}Wiki{/tr}</a>
{/if}
{if $prefs.feature_galleries eq 'y'}
 <a class="linkbut" href="tiki-searchindex.php?highlight={$words}&amp;where=galleries">{tr}Galleries{/tr}</a>
 <a class="linkbut" href="tiki-searchindex.php?highlight={$words}&amp;where=images">{tr}Images{/tr}</a>
{/if}
{if $prefs.feature_file_galleries eq 'y'}
 <a class="linkbut" href="tiki-searchindex.php?highlight={$words}&amp;where=files">{tr}Files{/tr}</a>
{/if}
{if $prefs.feature_forums eq 'y'}
 <a class="linkbut" href="tiki-searchindex.php?highlight={$words}&amp;where=forums">{tr}Forums{/tr}</a>
{/if}
{if $prefs.feature_faqs eq 'y'}
 <a class="linkbut" href="tiki-searchindex.php?highlight={$words}&amp;where=faqs">{tr}FAQs{/tr}</a>
{/if}
{if $prefs.feature_blogs eq 'y'}
 <a class="linkbut" href="tiki-searchindex.php?highlight={$words}&amp;where=blogs">{tr}Blogs{/tr}</a>
 <a class="linkbut" href="tiki-searchindex.php?highlight={$words}&amp;where=posts">{tr}Blog Posts{/tr}</a>
{/if}
{if $prefs.feature_directory eq 'y'}
 <a class="linkbut" href="tiki-searchindex.php?highlight={$words}&amp;where=directory">{tr}Directory{/tr}</a>
{/if}

{if $prefs.feature_articles eq 'y'}
 <a class="linkbut" href="tiki-searchindex.php?highlight={$words}&amp;where=articles">{tr}Articles{/tr}</a>
{/if}
{if $prefs.feature_trackers eq 'y'}
 <a class="linkbut" href="tiki-searchindex.php?highlight={$words}&amp;where=trackers">{tr}Trackers{/tr}</a>
{/if}
</div><!--nohighlight-->
<br /><br /> 
{if $words neq ''}{tr}Found{/tr} "{$words}" {tr}in{/tr} {$cant_results} {$where2}{/if}
{/if}
<form class="forms" method="get" action="tiki-searchindex.php">
    {tr}Find{/tr} <input id="fuser" name="highlight" size="14" type="text" accesskey="s" value="{$words}"/>
{if ( $searchStyle eq "menu" )}
    {tr}in{/tr}
    <select name="where">
    <option value="pages">{tr}entire site{/tr}</option>
    {if $prefs.feature_wiki eq 'y'}
       <option value="wikis">{tr}Wiki Pages{/tr}</option>
    {/if}
    {if $prefs.feature_galleries eq 'y'}
       <option value="galleries">{tr}Galleries{/tr}</option>
       <option value="images">{tr}Images{/tr}</option>
    {/if}
    {if $prefs.feature_file_galleries eq 'y'}
       <option value="files">{tr}Files{/tr}</option>
    {/if}
    {if $prefs.feature_forums eq 'y'}
       <option value="forums">{tr}Forums{/tr}</option>
    {/if}
    {if $prefs.feature_faqs eq 'y'}
       <option value="faqs">{tr}FAQs{/tr}</option>
    {/if}
    {if $prefs.feature_blogs eq 'y'}
       <option value="blogs">{tr}Blogs{/tr}</option>
       <option value="posts">{tr}Blog Posts{/tr}</option>
    {/if}
    {if $prefs.feature_directory eq 'y'}
       <option value="directory">{tr}Directory{/tr}</option>
    {/if}
    {if $prefs.feature_articles eq 'y'}
       <option value="articles">{tr}Articles{/tr}</option>
    {/if}
    {if $prefs.feature_trackers eq 'y'}
       <option value="trackers">{tr}Trackers{/tr}</option>
    {/if}
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
{tr}{$results[search].location}{/tr}:&nbsp;<a href="{$results[search].href}&amp;highlight={$words}{$results[search].anchor}" class="wiki">{$results[search].pageName|strip_tags|escape}</a> ({tr}Hits{/tr}: {$results[search].hits})
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
<div class="searchdesc">{$results[search].data|strip_tags|escape}</div>
<div class="searchdate">{tr}Last modification date{/tr}: {$results[search].lastModif|tiki_long_datetime}</div><br />
{sectionelse}
{tr}No pages matched the search criteria{/tr}
{/section}
</div>

<div class="mini">
{if $prev_offset >= 0}
[<a class="link" href="tiki-searchindex.php?where={$where}&amp;highlight={$words}&amp;offset={$prev_offset}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="link" href="tiki-searchindex.php?where={$where}&amp;highlight={$words}&amp;offset={$next_offset}">{tr}Next{/tr}</a>]
{/if}
</div>
{/if}
{/if}
