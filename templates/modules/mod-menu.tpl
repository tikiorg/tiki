{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name=$tpl_module_name flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle type=$module_type}
{if !empty($module_params.menu_id)}<div class="clearfix {$module_params.menu_class}" id="{$module_params.menu_id}">{/if}
{if !isset($module_params.css)}
	{if !isset($module_params.type)}
		{menu id=$module_params.id}
	{else}
		{menu id=$module_params.id type=$module_params.type}
	{/if}
{else}
	{if !isset($module_params.type)}
		{menu id=$module_params.id css=$module_params.css }
	{else}
		{menu id=$module_params.id css=$module_params.css type=$module_params.type}
	{/if}
{/if}
{if !empty($module_params.menu_id)}</div>{/if}
{/tikimodule}