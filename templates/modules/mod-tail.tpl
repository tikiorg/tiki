{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-tail.tpl,v 1.7 2005-05-18 11:03:31 mose Exp $ *}

{if $feature_tail eq 'y'}
{popup_init src="lib/overlib.js"}
{tikimodule title="$tailtitle" name="tail" flip=$module_params.flip decorations=$module_params.decorations}
{section name=ix loop=$tail}
<div class="module">{$tail[ix]}</div>
{/section}
{/tikimodule}
{/if}
