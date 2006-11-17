{tikimodule title="{tr}Style{/tr}: $style" name="switch_theme" flip=$module_params.flip decorations=$module_params.decorations}
{if $change_theme ne 'n' or $user eq ''}
<form method="get" action="tiki-switch_theme.php" target="_self">
<select name="theme" size="1" onchange="this.form.submit();">
{section name=ix loop=$styleslist}
{if count($available_styles) == 0 || in_array($styleslist[ix], $available_styles)}
<option value="{$styleslist[ix]|escape}" {if $style eq $styleslist[ix]}selected="selected"{/if}>{$styleslist[ix]|truncate:15}</option>
{/if}
{/section}
</select>
</form>
{else}
{tr}Permission denied{/tr}
{/if}
{/tikimodule}
