{* $Header: /cvsroot/tikiwiki/tiki/templates/categobjects.tpl,v 1.6 2006-03-16 13:43:12 sylvieg Exp $ *}

<div class="catblock">
<div class="cattitle">
{foreach name=for key=id item=title from=$titles}
<a href="tiki-browse_categories.php?parentId={$id}">{$title}</a>
{if !$smarty.foreach.for.last} &amp; {/if}
{/foreach}
</div>
<div class="catlists">
{foreach key=t item=i from=$listcat}
<b>{tr}{$t}{/tr}:</b>
{if $one eq 'y'}<br />{/if}
{section name=o loop=$i}
<a href="{$i[o].href}" class="link">{$i[o].name}</a>
{if $one eq 'y'}<br />{else !$smarty.section.o.last} &middot; {/if}
{/section}<br />
{/foreach}
</div>
</div>
