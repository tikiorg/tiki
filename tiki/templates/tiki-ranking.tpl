<h2>{tr}Top{/tr} {if $limit>0}{$limit}{else}{tr}all{/tr}{/if} {tr}pages{/tr}.</h2>
<a href="tiki-ranking.php?limit=10" class="wiki">{tr}Top{/tr} 10 {tr}pages{/tr}</a>|
<a href="tiki-ranking.php?limit=20" class="wiki">{tr}Top{/tr} 20 {tr}pages{/tr}</a>|
<a href="tiki-ranking.php?limit=50" class="wiki">{tr}Top{/tr} 50 {tr}pages{/tr}</a>|
<a href="tiki-ranking.php?limit=-1" class="wiki">{tr}All pages{/tr}</a>|
<br/><br/>
{section name=rank loop=$ranking}
<a  href="tiki-index.php?page={$ranking[rank].pageName}" class="wiki">{$ranking[rank].pageName}</a>({$ranking[rank].hits})<br/>
{sectionelse}
{tr}No pages found{/tr}
{/section}

