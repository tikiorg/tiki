{* $Id$ *}

{tikimodule error=$module_error title=$tpl_module_title name=$tpl_module_name flip=$module_params.flip|default:null decorations=$module_params.decorations|default:null nobox=$module_params.nobox|default:null notitle=$module_params.notitle|default:null type=$module_type}
	{if $module_params.bootstrap|default:null neq 'n'}
		{if $module_params.type|default:null eq 'horiz' OR !empty($module_params.navbar_brand)}
			<nav class="{if !empty($module_params.navbar_class)}{$module_params.navbar_class}{else}navbar navbar-default{/if}" role="navigation">
				{if empty($module_params.navbar_toggle) or $module_params.navbar_toggle neq 'n'}
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#mod-menu{$module_position}{$module_ord} .navbar-collapse">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						{if $module_params.navbar_brand neq ''}
							<a class="navbar-brand" href="index.php">
								<img id="logo-header" src="{$module_params.navbar_brand}" alt="Logo">
							</a>
						{/if}
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
		<div class="clearfix {if !empty($module_params.menu_class)}{$module_params.menu_class}{/if}"{if !empty($module_params.menu_id)} id="{$module_params.menu_id}"{/if}>
			{menu params=$module_params}
		</div>
	{/if}
{/tikimodule}
