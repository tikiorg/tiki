{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="logged_users" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
  <span class="user-box-text">{tr}We have{/tr} {$logged_users} 
	{if $logged_users > 1}
		{tr}online users.{/tr}
	{else}
		{tr}online user.{/tr}
	{/if}</span>
{/tikimodule}

