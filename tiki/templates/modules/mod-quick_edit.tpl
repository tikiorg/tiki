{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-quick_edit.tpl,v 1.6 2004-10-08 10:00:13 damosoft Exp $ *}

{if $tiki_p_edit eq 'y'}
{tikimodule title=$module_title name="quick_edit"}
<form method="get" action="tiki-editpage.php">
{if $categId}<input type="hidden" name="categId" value="{$categId}" />{/if}
{if $templateId}<input type="hidden" name="templateId" value="{$templateId}" />{/if}
{if $heading}<div class="bod-data">{$heading}</div>{/if}
<input type="text" size="{$size}" name="page" />
<input type="submit" name="quickedit" value="{$submit}" />
</form>
{/tikimodule}
{else}
<!-- no perm -->
{/if}
