{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-freetag.tpl,v 1.6 2006-12-13 20:49:53 mose Exp $ *}

{if $feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y'}
{eval var="{tr}Folksonomy{/tr}" assign="tpl_module_title"}
{tikimodule title=$tpl_module_title name="folksonomy_tagging" flip=$module_params.flip decorations=$module_params.decorations}

{include file="freetag_list.tpl"}

<form name="addTags" method="post" action="{$smarty.server.REQUEST_URI}">
<input type="text" name="addtags" maxlength="40" />
<input type="submit" name="Add" value="Add" />
</form>
</div>
{/tikimodule}
{/if}
