{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-categories.tpl,v 1.2 2007-02-18 11:21:16 mose Exp $ *}
{if !isset($tpl_module_title)}{eval assign=tpl_module_title var="{tr}Categories{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="categories" flip=$module_params.flip decorations=$module_params.decorations}
{$module_error}
{$tree}
{/tikimodule}

