<h2>{tr}backlinks to{/tr} <a href="tiki-index.php?page={$page}" class="wiki">{$page}</a>:</h2>
<ul>
{section name=back loop=$backlinks}
<li><a  href="tiki-index.php?page={$backlinks[back].fromPage|escape:"url"}" class="wiki">{$backlinks[back].fromPage}</a><br /></li>
{sectionelse}
{tr}No backlinks to this page{/tr}
{/section}
</ul>

{include file="tiki-page_bar.tpl"}
