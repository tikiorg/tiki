{* $Header: /cvsroot/tikiwiki/tiki/templates/categobjects.tpl,v 1.3 2005-03-12 16:50:08 mose Exp $ *}

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
{section name=o loop=$i}
<a href="{$i[o].href}" class="link">
{$i[o].name}
</a>
{if !$smarty.section.o.last} &middot; {/if}
{/section}<br />
{/foreach}
</div>
</div>
