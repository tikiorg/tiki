{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Menu{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="menu_$page" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
{$contentmenu}
{/tikimodule}
