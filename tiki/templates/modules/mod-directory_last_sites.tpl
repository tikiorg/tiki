{if $feature_directory eq 'y'}
<div class="box">
<div class="box-title">
{tr}Last Sites{/tr}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modLastdirSites}
<tr><td  width="5%" valign="top" class="module">{$smarty.section.ix.index_next})</td><td class="module">&nbsp;<a class="linkmodule" href="tiki-directory_redirect.php?siteId={$modLastdirSites[ix].siteId}" {if $directory_open_links eq 'n'}target="_new"{/if}>{$modLastdirSites[ix].name}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}