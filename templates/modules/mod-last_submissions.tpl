{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_submissions.tpl,v 1.8 2003-11-20 23:49:04 mose Exp $ *}

{if $feature_submissions eq 'y'}
<div class="box">
<div class="box-title">
{include file="module-title.tpl" module_title="{tr}Last submissions{/tr}" module_name="last_submissions"}
</div>
<div class="box-data">
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modLastSubmissions}
{if $tiki_p_edit_submission eq 'y'}
<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="tiki-edit_submission.php?subId={$modLastSubmissions[ix].subId}">{$modLastSubmissions[ix].title}</a></td></tr>
{else}
<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
<td class="module">{$modLastSubmissions[ix].title}</td></tr>
{/if}
{/section}
</table>
</div>
</div>
{/if}
