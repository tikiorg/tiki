{* $Header: /cvsroot/tikiwiki/tiki/templates/categobjects.tpl,v 1.9.2.1 2007-12-06 19:03:23 sylvieg Exp $ *}

<div class="catblock">
<div class="cattitle">
{foreach name=for key=id item=title from=$titles}
<a href="tiki-browse_categories.php?parentId={$id}">{$title|tr_if}</a>
{if !$smarty.foreach.for.last} &amp; {/if}
{/foreach}
</div>
<div class="catlists">
{foreach key=t item=i from=$listcat}
<b>{tr}{$t}{/tr}:</b>
{if $one}<br />{/if}
{section name=o loop=$i}
<a href="{$i[o].href}" class="link">{$i[o].name}</a>
{if $one}<br />{else !$smarty.section.o.last} &middot; {/if}
{/section}<br />
{/foreach}
</div>
</div>
