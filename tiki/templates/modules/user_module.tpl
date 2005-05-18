{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/user_module.tpl,v 1.10 2005-05-18 11:03:32 mose Exp $ *}

{tikimodule title=$user_title name=$user_module_name flip=$module_params.flip decorations=$module_params.decorations}
{* This will be nested 'box-data' div... *}
<div id="{$user_module_name}" {if $smarty.cookies.$user_module_name ne 'c'}style="display:block;"{else}style="display:none;"{/if}>
{eval var=$user_data}
</div>
{/tikimodule}
