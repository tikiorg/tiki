{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/user_module.tpl,v 1.8 2004-01-01 15:12:20 mose Exp $ *}

{tikimodule title=$user_title name=$user_module_name}
{* This will be nested 'box-data' div... *}
<div id="{$user_module_name}" {if $smarty.cookies.$user_module_name ne 'c'}style="display:block;"{else}style="display:none;"{/if}>
{eval var=$user_data}
</div>
{/tikimodule}
