{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-freetag.tpl,v 1.8 2007-02-17 11:16:41 mose Exp $ *}

{if $feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y'}
{if !$tpl_module_title}{assign value="{tr}Folksonomy{/tr}" var="tpl_module_title"}{/if}
{tikimodule title=$tpl_module_title name="folksonomy_tagging" flip=$module_params.flip decorations=$module_params.decorations}

{include file="freetag_list.tpl"}

<form name="addTags" method="post" action="{$smarty.server.REQUEST_URI}">
<input type="text" name="addtags" maxlength="40" />
<input type="submit" name="Add" value="Add" />
</form>
</div>
{/tikimodule}
{/if}
