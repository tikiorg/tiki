{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-directory_last_sites.tpl,v 1.2 2003-08-07 20:56:53 zaufi Exp $ *}

{if $feature_directory eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Last Sites{/tr}" module_name="directory_last_sites"}
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