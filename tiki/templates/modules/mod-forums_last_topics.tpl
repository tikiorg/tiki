{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-forums_last_topics.tpl,v 1.2 2003-08-07 20:56:53 zaufi Exp $ *}

{if $feature_forums eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Last forum topics{/tr}" module_name="forums_last_topics"}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modForumsLastTopics}
<tr><td  width="5%" valign="top" class="module">{$smarty.section.ix.index_next})</td><td class="module">&nbsp;<a class="linkmodule" href="{$modForumsLastTopics[ix].href}">{$modForumsLastTopics[ix].name}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}