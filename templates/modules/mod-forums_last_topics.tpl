{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-forums_last_topics.tpl,v 1.5 2003-11-20 23:49:04 mose Exp $ *}

{if $feature_forums eq 'y'}
<div class="box">
<div class="box-title">
{include file="module-title.tpl" module_title="{tr}Last forum topics{/tr}" module_name="forums_last_topics"}
</div>
<div class="box-data">
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modForumsLastTopics}
<tr>{if $nonums != 'y'}<td valign="top" class="module">{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="{$modForumsLastTopics[ix].href}">{$modForumsLastTopics[ix].name}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}