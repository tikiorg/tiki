{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-tail.tpl,v 1.1 2004-01-07 04:31:24 musus Exp $ *}

{if $feature_tail eq 'y'}
{popup_init src="lib/overlib.js"}
{tikimodule title="{$tailtitle}" name="tail"}
{section name=ix loop=$tail}
<div class="module">{$tail[ix]}</div>
{/section}
{/tikimodule}
{/if}
