{* $Id$ *}

{tikimodule error=$module_error title=$tpl_module_title name=$tpl_module_name flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle type=$module_type}
	{if $module_params.bootstrap neq 'n'}
		{if $module_params.type eq 'horiz'}
			<nav class="{if !empty($module_params.navbar_class)}{$module_params.navbar_class}{else}navbar navbar-default{/if}" role="navigation">
				{if $module_params.navbar_toggle neq 'n'}
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#mod-menu{$module_position}{$module_ord} .navbar-collapse">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
					</div>
					<div class="collapse navbar-collapse">
						{menu params=$module_params bootstrap=navbar}
					</div>
				{else}
					<div>
						{menu params=$module_params bootstrap=navbar}
					</div>
				{/if}
			</nav>
		{else}
			{menu params=$module_params bootstrap=basic}
		{/if}
	{else}{* non bootstrap legacy menus *}
		<div class="clearfix {$module_params.menu_class}"{if !empty($module_params.menu_id)} id="{$module_params.menu_id}"{/if}>
			{menu params=$module_params}
		</div>
	{/if}
{/tikimodule}
