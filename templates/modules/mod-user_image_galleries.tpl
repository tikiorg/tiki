{if $user}
{if $feature_galleries eq 'y'}
<div class="box">
<div class="box-title">
{tr}My galleries{/tr}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modUserG}
<tr><td  width="5%" class="module" valign="top">{$smarty.section.ix.index_next})</td><td class="module">&nbsp;<a class="linkmodule" href="tiki-browse_gallery.php?galleryId={$modUserG[ix].galleryId}">{$modUserG[ix].name}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}
{/if}