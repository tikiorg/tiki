{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-categories.tpl,v 1.3 2007-10-14 17:51:00 mose Exp $ *}
{if !isset($tpl_module_title)}{eval assign=tpl_module_title var="{tr}Categories{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="categories" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
{$module_error}
{$tree}
{/tikimodule}

