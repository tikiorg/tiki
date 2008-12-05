{$wiki_page_controls}
{if $likepages|@count ge '2'}
<ul>
{section name=back loop=$likepages}
{if $likepages[back] ne $pagegae}
<li><a href="tiki-index.php?page={$likepages[back]|escape:"url"}" class="wiki">{$likepages[back]}</a></li>
{/if}
{sectionelse}
{tr}No pages found{/tr}
{/section}
</ul>
{else}
{tr}No pages found{/tr}
{/if}
<br />

{include file=tiki-page_bar.tpl}

