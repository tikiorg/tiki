{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-directory_last_sites.tpl,v 1.4 2003-10-20 01:13:16 zaufi Exp $ *}

{if $feature_directory eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Last Sites{/tr}" module_name="directory_last_sites"}
</div>
<div class="box-data">
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modLastdirSites}
<tr>{if $nonums != 'y'}<td valign="top" class="module">{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="tiki-directory_redirect.php?siteId={$modLastdirSites[ix].siteId}" {if $directory_open_links eq 'n'}target="_new"{/if}>{$modLastdirSites[ix].name}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}