{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-simple_plugin.tpl,v 1.8 2004-03-13 03:42:23 aurel42 Exp $ *}

<div class="catlists">
<span class="cattitle">{$title}</span>
{foreach key=t item=i from=$listcat}
{tr}{$t}{/tr}:
{section name=o loop=$i}
<a href="{$i[o].href}" class="link" title="{tr}Created{/tr} {$i[o].created|tiki_long_date}">{$i[o].name}</a>
{/section}
{/foreach}
</div>
