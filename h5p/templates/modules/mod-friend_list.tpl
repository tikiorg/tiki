{if $user}
	{tikimodule error=$module_params.error title=$tpl_module_title name="friend_list" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
		{service_inline controller=social action=list_friends}
	{/tikimodule}
{/if}
