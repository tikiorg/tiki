<h1><a  href="tiki-backlinks.php?page={$page}" title="{tr}backlinks to{/tr} {$page}" class="pagetitle">{$page}</a></h1>
<div class="wikitext">{$parsed}</div>
<p class="editdate">{tr}Last modification date{/tr}: {$lastModif|date_format:"%A %d of %B, %Y [%H:%M:%S]"} by {$lastUser}</p>
{if $feature_wiki_comments eq 'y'}
{include file=comments.tpl}
{/if}
