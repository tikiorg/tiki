{if $feature_quizzes eq 'y'}
<div class="box">
<div class="box-title">
{tr}Last Created Quizzes{/tr}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modLastCreatedQuizzes}
<tr><td  width="5%" class="module" valign="top">{$smarty.section.ix.index_next})</td><td class="module">&nbsp;<a class="linkmodule" href="tiki-take_quiz.php?quizId={$modLastCreatedQuizzes[ix].quizId}">{$modLastCreatedQuizzes[ix].name}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}