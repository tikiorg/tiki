{if $feature_page_title eq 'y'}<h1><a  href="tiki-backlinks.php?page={$page}" title="{tr}Backlinks to{/tr} {$page}" class="pagetitle">{$page}</a></h1>{/if}
<div class="wikitext">{$parsed}</div>
<p class="editdate">{tr}Last modification date{/tr}: {$lastModif|tiki_long_datetime} {tr}by{/tr} {$lastUser}</p>
