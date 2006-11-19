{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/user_module.tpl,v 1.11 2006-11-19 23:52:53 franck Exp $ *}

{tikimodule title=$user_title name=$user_module_name flip=$module_params.flip decorations=$module_params.decorations}
{* This will be nested 'box-data' div... *}
<div id="{$user_module_name}" {if (isset($smarty.cookies.$user_module_name) && $smarty.cookies.$user_module_name ne 'c') || !isset($smarty.cookies.$user_module_name)}style="display:block;"{else}style="display:none;"{/if}>
{eval var=$user_data}
</div>
{/tikimodule}
