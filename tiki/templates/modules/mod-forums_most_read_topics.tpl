{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-forums_most_read_topics.tpl,v 1.2 2003-08-07 20:56:53 zaufi Exp $ *}

{if $feature_forums eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Most read topics{/tr}" module_name="forums_most_read_topics"}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modForumsMostReadTopics}
<tr><td  width="5%" class="module" valign="top">{$smarty.section.ix.index_next})</td><td class="module">&nbsp;<a class="linkmodule" href="{$modForumsMostReadTopics[ix].href}">{$modForumsMostReadTopics[ix].name}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}