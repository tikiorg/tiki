{if $feature_directory eq 'y'}
<div class="box">
<div class="box-title">
{tr}Top Sites{/tr}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modTopdirSites}
<tr><td  width="5%" valign="top" class="module">{$smarty.section.ix.index_next})</td><td class="module">&nbsp;<a class="linkmodule" href="tiki-directory_redirect.php?siteId={$modTopdirSites[ix].siteId}" {if $directory_open_links eq 'n'}target="_new"{/if}>{$modTopdirSites[ix].name}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}