{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-random_pages.tpl,v 1.5 2003-11-23 03:53:04 zaufi Exp $ *}

{if $feature_wiki eq 'y'}
{tikimodule title="{tr}Random Pages{/tr}" name="random_pages"}
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modRandomPages}
<tr><td   class="module"><a class="linkmodule" href="tiki-index.php?page={$modRandomPages[ix]}">{$modRandomPages[ix]}</a></td></tr>
{/section}
</table>
{/tikimodule}
{/if}