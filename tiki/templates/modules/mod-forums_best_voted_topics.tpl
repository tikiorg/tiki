{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-forums_best_voted_topics.tpl,v 1.3 2003-09-25 01:05:22 rlpowell Exp $ *}

{if $feature_forums eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Top topics{/tr}" module_name="forums_best_voted_topics"}
</div>
<div class="box-data">
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modForumsTopTopics}
<tr><td   class="module" valign="top">{$smarty.section.ix.index_next})</td><td class="module">&nbsp;<a class="linkmodule" href="{$modForumsTopTopics[ix].href}">{$modForumsTopTopics[ix].name}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}