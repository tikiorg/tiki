{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-simple_plugin.tpl,v 1.10 2004-09-15 10:34:54 mose Exp $ *}

<div class="catlists">
{if $title}<div class="cbox-title">{$title}</div>{/if}
<div class="cbox-data">
{foreach key=t item=i from=$listcat}
<b>{$t}:</b>
{section name=o loop=$i}
<a href="{$i[o].href}" class="link">{$i[o].name}</a>
{if $smarty.section.o.index ne $smarty.section.o.total - 1} &middot; {/if}
{/section}
<br />
{/foreach}
</div>
</div>
