{if $feature_galleries eq 'y'}
<div class="box">
<div class="box-title">
{tr}Last submissions{/tr}
</div>
<div class="box-data">
{section name=ix loop=$modLastSubmissions}
{if $tiki_p_edit_submission eq 'y'}
<div class="button">{$smarty.section.ix.index_next})&nbsp;<a class="linkbut" href="tiki-edit_submission.php?subId={$modLastSubmissions[ix].subId}">{$modLastSubmissions[ix].title}</a></div>
{else}
<div class="button">{$smarty.section.ix.index_next})&nbsp;{$modLastSubmissions[ix].title}</div>
{/if}
{/section}
</div>
</div>
{/if}