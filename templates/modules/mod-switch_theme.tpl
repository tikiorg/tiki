{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-switch_theme.tpl,v 1.18 2007-10-04 22:17:47 nyloth Exp $ *}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Style{/tr}: $user_style"}{/if}
{tikimodule title=$tpl_module_title name="switch_theme" flip=$module_params.flip decorations=$module_params.decorations}
	{if $prefs.change_theme ne 'n' or $user eq ''}
	<form method="get" action="tiki-switch_theme.php">
		<select name="theme" size="1" onchange="this.form.submit();">
			<option value="" style="font-style:italic;border-bottom:1px dashed #666;">{tr}Site default{/tr}</option>
		{section name=ix loop=$styleslist}
			{if count($prefs.available_styles) == 0 || in_array($styleslist[ix], $prefs.available_styles)}
			<option value="{$styleslist[ix]|escape}" {if $user_style eq $styleslist[ix]}selected="selected"{/if}>{$styleslist[ix]|truncate:15}</option>
			{/if}
		{/section}
		</select>
		<noscript>
			<button type="submit">{tr}Switch{/tr}</button>
		</noscript>
	</form>
	{else}
		{tr}Permission denied{/tr}
	{/if}
{/tikimodule}
