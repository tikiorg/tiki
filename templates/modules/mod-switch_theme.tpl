<div class="box">
<div class="box-title">
{tr}Style{/tr}: {$style}
</div>
<div class="box-data">
<form method="get" action="tiki-switch_theme.php" target="_self">
<select name="theme" size="1" onChange="this.form.submit();">
{section name=ix loop=$styleslist}
<option value="{$styleslist[ix]}"{if $styleslist[ix] == $style} selected{/if}>{$styleslist[ix]}</option>
{/section}
</select>
</form>
</div>
</div>
