{* $Id$ *}

{if $module_position eq 'topbar'}
	<nav class="navbar navbar-default" role="navigation">
		{* <div class="container"> *}
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse">
                {menu params=$module_params bootstrap=navbar}
            </div>
        {* </div> *}
	</nav>
{elseif $module_position eq 'left'}
	{menu params=$module_params bootstrap=basic}
{else}
{tikimodule error=$module_error title=$tpl_module_title name=$tpl_module_name flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle type=$module_type}
{if !empty($module_params.menu_id)}
	<div class="clearfix {$module_params.menu_class}" id="{$module_params.menu_id}">
		{menu params=$module_params}
	</div>
{else}
	{menu params=$module_params}
{/if}
{/tikimodule}
{/if}
