{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-breadcrumb.tpl,v 1.4 2003-09-25 01:05:22 rlpowell Exp $ *}

{if $feature_featuredLinks eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Recently visited pages{/tr}" module_name="breadcrumb"}
</div>
<div class="box-data">
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$breadCrumb}
<tr><td class="module"><a class="linkmodule" href="tiki-index.php?page={$breadCrumb[ix]}">{$breadCrumb[ix]}</a></td></tr>
{sectionelse}
<tr><td class="module">&nbsp;</td></tr>
{/section}
</table>
</div>
</div>
{/if}