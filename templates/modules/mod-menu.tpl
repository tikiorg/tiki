{* $Id$ *}

{tikimodule error=$module_error title=$tpl_module_title name=$tpl_module_name flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle type=$module_type}
	{if $module_params.bootstrap neq 'n'}
		{if $module_params.type eq 'horiz'}
			{* One should enclose this in: <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation"> *}
			{menu params=$module_params bootstrap=navbar class=noclearfix}
		{else}
			{menu params=$module_params bootstrap=basic}
		{/if}
	{else}{* non bootstrap legacy menus *}
		<div class="clearfix {$module_params.menu_class}"{if !empty($module_params.menu_id)} id="{$module_params.menu_id}"{/if}>
			{menu params=$module_params}
		</div>
	{/if}
{/tikimodule}
