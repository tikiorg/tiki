{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-random_pages.tpl,v 1.6 2004-09-19 19:37:13 mose Exp $ *}

{if $feature_wiki eq 'y'}
{tikimodule title="{tr}Random Pages{/tr}" name="random_pages"}
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modRandomPages}
<tr><td   class="module"><a class="linkmodule" href="tiki-index.php?page={$modRandomPages[ix]|escape:'url'}">{$modRandomPages[ix]}</a></td></tr>
{/section}
</table>
{/tikimodule}
{/if}