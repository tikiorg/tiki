{if $contentmenu}
	{tikimodule error=$module_params.error title=$tpl_module_title name="menupage" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{if !empty($module_params.menu_id)}
		<div class="clearfix {$module_params.menu_class}" id="{$module_params.menu_id}">
			{$contentmenu}
		</div>
	{else}
		{$contentmenu}
	{/if}
	{/tikimodule}
{/if}
