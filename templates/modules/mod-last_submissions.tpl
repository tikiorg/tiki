{if $feature_submissions eq 'y'}
<div class="box">
<div class="box-title">
{tr}Last submissions{/tr}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modLastSubmissions}
{if $tiki_p_edit_submission eq 'y'}
<tr><td  width="5%" class="module" valign="top">{$smarty.section.ix.index_next})</td><td class="module">&nbsp;<a class="linkmodule" href="tiki-edit_submission.php?subId={$modLastSubmissions[ix].subId}">{$modLastSubmissions[ix].title}</a></td></tr>
{else}
<tr><td  width="5%" class="module" valign="top">{$smarty.section.ix.index_next})</td><td class="module">&nbsp;{$modLastSubmissions[ix].title}</td></tr>
{/if}
{/section}
</table>
</div>
</div>
{/if}
