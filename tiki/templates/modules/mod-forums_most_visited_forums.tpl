{if $feature_forums eq 'y'}
<div class="box">
<div class="box-title">
{tr}Most visited forums{/tr}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modForumsMostVisitedForums}
<tr><td  width="5%" class="module" valign="top">{$smarty.section.ix.index_next})&nbsp;</td><td class="module"><a class="linkmodule" href="{$modForumsMostVisitedForums[ix].href}">{$modForumsMostVisitedForums[ix].name}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}