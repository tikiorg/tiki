<h2>{tr}Search results{/tr}:</h2>
{section name=search loop=$results}
<a href="tiki-index.php?page={$results[search].pageName}" class="wiki">{$results[search].pageName}</a> ({$results[search].hits})<br/>
<div class="text">{$results[search].data}</div>
<div class="searchdate">{tr}Last modification date{/tr}: {$results[search].lastModif|date_format:"%A %d of %B, %Y [%H:%M:%S]"}</div><br/>
{sectionelse}
{tr}No pages matched the search criteria{/tr}
{/section}
