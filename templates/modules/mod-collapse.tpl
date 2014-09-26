{* $Id$ *}
{tikimodule error=$module_error title=$tpl_module_title name=$tpl_module_name flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle type=$module_type}
	<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="{$module_params.target}"{if $module_params.parent} data-parent="{$module_params.parent}"{/if}>
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	</button>
{/tikimodule}
