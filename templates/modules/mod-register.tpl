{if $smarty.server.SCRIPT_NAME|stristr:"tiki-register.php" eq false}
	{tikimodule error=$module_params.error title=$tpl_module_title name="register" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
		{if empty($user)}
			{include file='tiki-register.tpl'}
		{else}
			{* can't just include mod-loginbox.tpl as you end up with nested module decorations *}'
			<div>{tr}Logged in as:{/tr} <span style="white-space: nowrap">{$user|userlink}</span></div>
			<div style="text-align: center;">
				{button href="tiki-logout.php" _text="{tr}Log out{/tr}"}
			</div>
		{/if}
	{/tikimodule}
{/if}
