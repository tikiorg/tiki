{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/user_module.tpl,v 1.9 2005-03-12 16:51:00 mose Exp $ *}

{tikimodule title=$user_title name=$user_module_name flip=$module_params.flip decorations=$module_params.decorations}
{* This will be nested 'box-data' div... *}
<div id="{$user_module_name}" {if $smarty.cookies.$user_module_name ne 'c'}style="display:block;"{else}style="display:none;"{/if}>
{eval var=$user_data}
</div>
{/tikimodule}
