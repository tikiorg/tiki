<h2>{tr}Search results{/tr}:</h2>
{tr}Search in{/tr}:
[
<a class="link" href="tiki-searchresults.php?words={$words}&amp;where=pages">{tr}Entire site{/tr}</a> |
{if $feature_wiki eq 'y'}
<a class="link" href="tiki-searchresults.php?words={$words}&amp;where=wikis">{tr}wiki pages{/tr}</a> |
{/if}
{if $feature_galleries eq 'y'}
<a class="link" href="tiki-searchresults.php?words={$words}&amp;where=galleries">{tr}galleries{/tr}</a> |
<a class="link" href="tiki-searchresults.php?words={$words}&amp;where=images">{tr}images{/tr}</a> |
{/if}
{if $feature_file_galleries eq 'y'}
<a class="link" href="tiki-searchresults.php?words={$words}&amp;where=files">{tr}files{/tr}</a> |
{/if}
{if $feature_forums eq 'y'}
<a class="link" href="tiki-searchresults.php?words={$words}&amp;where=forums">{tr}forums{/tr}</a> |
{/if}
{if $feature_faqs eq 'y'}
<a class="link" href="tiki-searchresults.php?words={$words}&amp;where=faqs">{tr}faqs{/tr}</a> |
{/if}
{if $feature_blogs eq 'y'}
<a class="link" href="tiki-searchresults.php?words={$words}&amp;where=blogs">{tr}blogs{/tr}</a> |
<a class="link" href="tiki-searchresults.php?words={$words}&amp;where=posts">{tr}blog posts{/tr}</a> |
{/if}
{if $feature_articles eq 'y'}
<a class="link" href="tiki-searchresults.php?words={$words}&amp;where=articles">{tr}articles{/tr}</a>
{/if}
]
<br/><br/>
{tr}Found{/tr} "{$words}" {tr}in{/tr} {$cant_results} {$where2}
<form class="forms" method="post" action="tiki-searchresults.php">
    {tr}Find{/tr}: <input id="fuser" name="words" size="14" type="text" accesskey="s" /> 
    <input type="hidden" name="where" value="{$where}" />
    <input type="submit" class="wikiaction" name="search" value="{tr}go{/tr}"/> 
</form>
<br/><br/>
{section  name=search loop=$results}
<a href="{$results[search].href}" class="wiki">{$results[search].pageName|strip_tags}</a> ({tr}Hits{/tr}: {$results[search].hits})
{if $feature_search_fulltext eq 'y'}
&nbsp;({tr}Relevance{/tr}: {$results[search].relevance})
{/if}
{if $results[search].type > ''}
&nbsp;({$results[search].type})
{/if}

<br/>
<div class="searchdesc">{$results[search].data|strip_tags}</div>
<div class="searchdate">{tr}Last modification date{/tr}: {$results[search].lastModif|tiki_long_datetime}</div><br/>
{sectionelse}
{tr}No pages matched the search criteria{/tr}
{/section}

<div align="center">
<div class="mini">
{if $prev_offset >= 0}
[<a class="link" href="tiki-searchresults.php?where={$where}&amp;words={$words}&amp;offset={$prev_offset}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="link" href="tiki-searchresults.php?where={$where}&amp;words={$words}&amp;offset={$next_offset}">{tr}next{/tr}</a>]
{/if}
</div>
</div>

