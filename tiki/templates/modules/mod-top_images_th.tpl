<div class="box">
<div class="box-title">
{tr}Top Images{/tr}
</div>
<div class="box-data">
{section name=ix loop=$modTopImages}
{if $smarty.section.ix.index < 5}
<div align="center" class="imagerank">
<table cellpadding="0" cellspacing="0">
<tr>
<td valign="middle"><span class="user-box-text">{$smarty.section.ix.index_next})</span></td>
<td>
<a class="linkbut" href="tiki-browse_image.php?imageId={$modTopImages[ix].imageId}">
<img alt="image" src="show_image.php?id={$modTopImages[ix].imageId}" height="50" width="90" />
</a>
</td></tr>
</table>
</div>
{/if}
{/section}
</div>
</div>
