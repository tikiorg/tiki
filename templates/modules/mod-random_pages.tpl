{if $feature_wiki eq 'y'}
<div class="box">
<div class="box-title">
{tr}Random Pages{/tr}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modRandomPages}
<tr><td  width="5%" class="module"><a class="linkmodule" href="tiki-index.php?page={$modRandomPages[ix]}">{$modRandomPages[ix]}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}