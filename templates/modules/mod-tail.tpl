{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-tail.tpl,v 1.4 2003-11-23 04:01:52 gmuslera Exp $ *}

{if $feature_tail eq 'y'}
{popup_init src="lib/overlib.js"}
{tikimodule title="{$tailtitle}" name="tail"}
{section name=ix loop=$tail}
<div class="module">{$tail[ix]}</div>
{/section}
{/tikimodule}
{/if}
