{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-forums_best_voted_topics.tpl,v 1.5 2003-11-20 23:49:04 mose Exp $ *}

{if $feature_forums eq 'y'}
<div class="box">
<div class="box-title">
{include file="module-title.tpl" module_title="{tr}Top topics{/tr}" module_name="forums_best_voted_topics"}
</div>
<div class="box-data">
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modForumsTopTopics}
<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="{$modForumsTopTopics[ix].href}">{$modForumsTopTopics[ix].name}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}