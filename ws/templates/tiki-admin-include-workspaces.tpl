{* $Id: $ *}

{*If this is the first time a user comes to Workspaces we need to install some things*}
{if $prefs.new_to_ws eq 'y'}	
	{include file='tiki-admin-config-workspaces.tpl'}
{/if}
