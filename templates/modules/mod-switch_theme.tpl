{tikimodule title="{tr}Style{/tr}: $styleName" name="switch_theme" flip=$module_params.flip decorations=$module_params.decorations}
{if $change_theme ne 'n' or $user eq ''}
<form method="get" action="tiki-switch_theme.php" target="_self">
<select name="theme" size="1" onchange="this.form.submit();">
{section name=ix loop=$styleslist}
<option value="{$styleslist[ix]}"{if $styleslist[ix] == $styleName} selected="selected"{/if}>{$styleslist[ix]|truncate:15}</option>
{/section}
</select>
</form>
{else}
{tr}Permission denied{/tr}
{/if}
{/tikimodule}
