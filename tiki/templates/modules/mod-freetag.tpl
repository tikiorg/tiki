{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-freetag.tpl,v 1.3 2006-11-20 00:37:51 mose Exp $ *}

{if $feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y'}
{eval var="{tr}Folksonomy{/tr}" assign="tpl_module_title"}
{tikimodule title=$tpl_module_title name="folksonomy_tagging" flip=$module_params.flip decorations=$module_params.decorations}

<div class="freetaglist">{tr}Tags{/tr}:

{if isset($freetags.data[0])}
{foreach from=$freetags.data item=taginfo}
<a class="freetag" href="tiki-browse_freetags.php?tag={$taginfo.tag}">{$taginfo.tag}</a> .
{/foreach}
</div>
{/if}

<form name="addTags" method="post" action="{$smarty.request.php_self}">
<input type="text" name="addtags" maxlength="40" />
<input type="submit" name="Add" value="Add" />
</form>
{/tikimodule}
{/if}
