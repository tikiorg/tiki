{if $feature_forums eq 'y'}
<div class="box">
<div class="box-title">
{tr}Last forum topics{/tr}
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