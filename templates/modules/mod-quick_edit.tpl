{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-quick_edit.tpl,v 1.12 2007-10-14 17:51:01 mose Exp $ *}

{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}$module_title{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="quick_edit" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
<form method="get" action="tiki-editpage.php">
{if $categId}<input type="hidden" name="categId" value="{$categId}" />{/if}
{if $templateId}<input type="hidden" name="templateId" value="{$templateId}" />{/if}
{if $mod_quickedit_heading}<div class="bod-data">{$mod_quickedit_heading}</div>{/if}
<input type="text" size="{$size}" name="page" />
<input type="submit" name="quickedit" value="{$submit}" />
</form>
{/tikimodule}
