{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-forums_most_read_topics.tpl,v 1.4 2003-10-20 01:13:16 zaufi Exp $ *}

{if $feature_forums eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Most read topics{/tr}" module_name="forums_most_read_topics"}
</div>
<div class="box-data">
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modForumsMostReadTopics}
<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="{$modForumsMostReadTopics[ix].href}">{$modForumsMostReadTopics[ix].name}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}