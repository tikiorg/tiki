{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/user_module.tpl,v 1.7 2003-11-23 22:03:07 zaufi Exp $ *}

{tikimodule title=$user_title name=$user_module_name}
{* This will be nested 'box-data' div... *}
<div class="box-data" id="{$user_module_name}" {if $smarty.cookies.$user_module_name ne 'c'}style="display:block;"{else}style="display:none;"{/if}>
{eval var=$user_data}
</div>
{/tikimodule}
