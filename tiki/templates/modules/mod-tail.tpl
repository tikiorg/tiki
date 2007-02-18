{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-tail.tpl,v 1.8 2007-02-18 11:21:19 mose Exp $ *}

{if $feature_tail eq 'y'}
{popup_init src="lib/overlib.js"}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}$tailtitle{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="tail" flip=$module_params.flip decorations=$module_params.decorations}
{section name=ix loop=$tail}
<div class="module">{$tail[ix]}</div>
{/section}
{/tikimodule}
{/if}
