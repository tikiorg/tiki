{if $feature_featuredLinks eq 'y'}
<div class="box">
<div class="box-title">
{tr}Recently visited pages{/tr}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$breadCrumb}
<tr><td class="module"><a class="linkmodule" href="tiki-index.php?page={$breadCrumb[ix]}">{$breadCrumb[ix]}</a></td></tr>
{sectionelse}
<tr><td class="module">&nbsp;</td></tr>
{/section}
</table>
</div>
</div>
{/if}