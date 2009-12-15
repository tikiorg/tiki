{* $Id$ *}
{if !isset($tpl_module_title)}{eval assign=tpl_module_title var="{tr}Categories{/tr}"}{/if}
{tikimodule error=$module_params.error title=$tpl_module_title name="categories" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle error=$module_params.error}
{$tree}
{/tikimodule}
