{* $Header: /cvsroot/tikiwiki/_mods/modules/tail/templates/mod-tail.tpl,v 1.1 2007-12-04 22:46:36 mose Exp $ *}

{if $feature_tail eq 'y'}
{popup_init src="lib/overlib.js"}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}$tailtitle{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="tail" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
{section name=ix loop=$tail}
<div class="module">{$tail[ix]}</div>
{/section}
{/tikimodule}
{/if}
