{* $Id: $ *}

{* If is the user first time coming to Workspaces we need to set up... *}
{if $prefs.new_to_ws eq 'y'}
	{include file='tiki-admin-config-workspaces.tpl' warning=$warning}
{/if}
