<h2>{tr}backlinks to{/tr} <a href="tiki-index.php?page={$page}" class="wiki">{$page}</a>:</h2>
{section name=back loop=$backlinks}
<a  href="tiki-index.php?page={$backlinks[back].fromPage}" class="wiki">{$backlinks[back].fromPage}</a><br/>
{sectionelse}
{tr}No backlinks to this page{/tr}
{/section}
