{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/user_module.tpl,v 1.2 2004-01-16 18:38:29 musus Exp $ *}

{tikimodule title=$user_title name=$user_module_name}
{* This will be nested 'module-content' div... *}
<div class="module-content" id="{$user_module_name}" {if $smarty.cookies.$user_module_name ne 'c'}style="display:block;"{else}style="display:none;"{/if}>
{eval var=$user_data}
</div>
{/tikimodule}
