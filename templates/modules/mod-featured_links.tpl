{if $feature_featuredLinks eq 'y'}
<div class="box">
<div class="box-title">
{tr}Featured links{/tr}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$featuredLinks}
<tr><td class="module"><a class="linkmodule" href="tiki-featured_link.php?url={$featuredLinks[ix].url}">{$featuredLinks[ix].title}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}