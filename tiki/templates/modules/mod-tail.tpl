{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-tail.tpl,v 1.5 2005-01-22 22:56:27 mose Exp $ *}

{if $feature_tail eq 'y'}
{popup_init src="lib/overlib.js"}
{tikimodule title="$tailtitle" name="tail"}
{section name=ix loop=$tail}
<div class="module">{$tail[ix]}</div>
{/section}
{/tikimodule}
{/if}
