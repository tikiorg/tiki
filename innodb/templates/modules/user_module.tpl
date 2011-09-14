{* $Id$ *}
{tikimodule error=$module_params.error title=$user_title name=$user_module_name flip=$module_params.flip decorations=$module_params.decorations overflow=$module_params.overflow nobox=$module_params.nobox notitle=$module_params.notitle type=$module_type}
{* This will be nested 'box-data' div... *}
<div id="{$user_module_name|stringfix:' ':'_'}" {if (isset($smarty.cookies.$user_module_name) && $smarty.cookies.$user_module_name ne 'c') || !isset($smarty.cookies.$user_module_name)}style="display:block;"{else}style="display:none;"{/if}>
{eval var=$user_data}
</div>
{/tikimodule}
