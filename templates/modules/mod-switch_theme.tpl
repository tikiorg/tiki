{tikimodule name="switch_theme"}
{tr}Style{/tr}: {$style}
<form method="get" action="tiki-switch_theme.php" target="_self">
<select name="theme" size="1" onchange="this.form.submit();">
{section name=ix loop=$styleslist}
<option value="{$styleslist[ix]}"{if $styleslist[ix] == $style} selected="selected"{/if}>{$styleslist[ix]}</option>
{/section}
</select>
</form>
{/tikimodule}
