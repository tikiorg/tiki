<h2>{tr}Pages like{/tr}:

{if $page_exists eq 'n'}
'{$page}'
{else}
<a href="tiki-index.php?page={$page|escape:"url"}" class="wiki">{$page}</a>
{/if}</h2>

{section name=back loop=$likepages}
<a  href="tiki-index.php?page={$likepages[back]|escape:"url"}" class="wiki">{$likepages[back]}</a><br />
{sectionelse}
{tr}No pages found{/tr}
{/section}
<br />

{include file=tiki-page_bar.tpl}

