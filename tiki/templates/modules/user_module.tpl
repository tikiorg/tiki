{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/user_module.tpl,v 1.6 2003-11-23 04:00:50 zaufi Exp $ *}

{tikimodule title=$user_title name=$module_name}
{* This will be nested 'box-data' div... *}
<div class="box-data" id="{$module_name}" {if $smarty.cookies.$module_name ne 'c'}style="display:block;"{else}style="display:none;"{/if}>
{eval var=$user_data}
</div>
{/tikimodule}
