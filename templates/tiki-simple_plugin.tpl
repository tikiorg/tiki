{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-simple_plugin.tpl,v 1.7 2003-12-29 15:12:16 mose Exp $ *}

<div class="catlists">
<span class="cattitle">{$title}</span>
{foreach key=t item=i from=$listcat}
{$t}:
{section name=o loop=$i}
<a href="{$i[o].href}" class="link" title="{tr}Created{/tr} {$i[o].created|tiki_long_date}">{$i[o].name}</a>
{/section}
{/foreach}
</div>
