<h2>{tr}Search results{/tr}:</h2>
{tr}Search in{/tr}:
[
{if $feature_wiki eq 'y'}
<a class="link" href="tiki-searchresults.php?words={$words}&amp;where=pages">{tr}pages{/tr}</a> |
{/if}
{if $feature_galleries eq 'y'}
<a class="link" href="tiki-searchresults.php?words={$words}&amp;where=galleries">{tr}galleries{/tr}</a> |
<a class="link" href="tiki-searchresults.php?words={$words}&amp;where=images">{tr}images{/tr}</a> |
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
{tr}Found{/tr} "{$words}" {tr}in{/tr} {$cant_results} {$where}
<br/><br/>
{section  name=search loop=$results}
<a href="{$results[search].href}" class="wiki">{$results[search].pageName}</a> ({$results[search].hits})<br/>
<div class="text">{$results[search].data}</div>
<div class="searchdate">{tr}Last modification date{/tr}: {$results[search].lastModif|date_format:"%A %d of %B, %Y [%H:%M:%S]"}</div><br/>
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

