{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-random_pages.tpl,v 1.4 2003-11-20 23:49:04 mose Exp $ *}

{if $feature_wiki eq 'y'}
<div class="box">
<div class="box-title">
{include file="module-title.tpl" module_title="{tr}Random Pages{/tr}" module_name="random_pages"}
</div>
<div class="box-data">
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modRandomPages}
<tr><td   class="module"><a class="linkmodule" href="tiki-index.php?page={$modRandomPages[ix]}">{$modRandomPages[ix]}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}