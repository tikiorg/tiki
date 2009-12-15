{* $Id$ *}
{if empty($group_style)}
{if !isset($tpl_module_title)}
	{capture assign=tpl_module_title}{tr}Theme{/tr}: <em>{$prefs.style|truncate:15|replace:'.css':''|ucwords}</em>{/capture}
{/if}
{tikimodule error=$module_params.error title=$tpl_module_title name="switch_theme" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{if $prefs.change_theme ne 'n'}
	<form method="get" action="tiki-switch_theme.php">
		<select name="theme" size="1" onchange="this.form.submit();">
			<option value="" style="font-style:italic;border-bottom:1px dashed #666;">{tr}Site default{/tr}</option>
		{section name=ix loop=$styleslist}
			{if count($prefs.available_styles) == 0 || empty($prefs.available_styles[0]) || in_array($styleslist[ix], $prefs.available_styles)}
			<option value="{$styleslist[ix]|escape}" {if $prefs.style eq $styleslist[ix]}selected="selected"{/if}>{$styleslist[ix]|truncate:15|replace:'.css':''|ucwords}</option>
			{/if}
		{/section}
		</select>{if $style_options}
		<select name="theme-option" id="general-theme-options" onchange="this.form.submit();">
			{*<option value="" style="font-style:italic;border-bottom:1px dashed #666;">{tr}Site default{/tr}</option>*}
		{section name=ix loop=$style_options}
			<option value="{$style_options[ix]|escape}"{if $prefs.style_option eq $style_options[ix]} selected="selected"{/if}>{$style_options[ix]|truncate:15|replace:'.css':''|ucwords}</option>
		{/section}
		</select>{/if}
		<noscript>
			<button type="submit">{tr}Switch{/tr}</button>
		</noscript>
	</form>
	{else}
		{tr}This feature is disabled{/tr}
	{/if}
{/tikimodule}
{/if}
