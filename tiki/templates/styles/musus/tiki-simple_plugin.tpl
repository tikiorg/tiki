{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/tiki-simple_plugin.tpl,v 1.4 2004-02-01 07:49:56 musus Exp $ *}

<div class="catlists">
<span class="cattitle">{$title}</span>
{foreach key=t item=i from=$listcat}
{$t}:
{section name=o loop=$i}
<a href="{$i[o].href}" class="link" title="{tr}Created{/tr} {$i[o].created|tiki_long_date}">{$i[o].name}</a>
{/section}
{/foreach}
</div>
