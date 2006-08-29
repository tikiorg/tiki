{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-quick_edit.tpl,v 1.9 2006-08-29 20:19:14 sylvieg Exp $ *}

{if $tiki_p_edit eq 'y'}
{tikimodule title=$module_title name="quick_edit"  flip=$module_params.flip decorations=$module_params.decorations}
<form method="get" action="tiki-editpage.php">
{if $categId}<input type="hidden" name="categId" value="{$categId}" />{/if}
{if $templateId}<input type="hidden" name="templateId" value="{$templateId}" />{/if}
{if $mod_quickedit_heading}<div class="bod-data">{$mod_quickedit_heading}</div>{/if}
<input type="text" size="{$size}" name="page" />
<input type="submit" name="quickedit" value="{$submit}" />
</form>
{/tikimodule}
{else}
<!-- no perm -->
{/if}
