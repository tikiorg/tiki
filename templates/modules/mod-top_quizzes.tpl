{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_quizzes.tpl,v 1.6 2003-11-23 04:01:52 gmuslera Exp $ *}

{if $feature_quizzes eq 'y'}
    {tikimodule title="{tr}Top Quizzes{/tr}" name="top_quizzes"}
    <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modTopQuizzes}
	<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
	<td class="module"><a class="linkmodule" href="tiki-take_quiz.php?quizId={$modTopQuizzes[ix].quizId}">{$modTopQuizzes[ix].quizName}</a></td></tr>
    {/section}
    </table>
    {/tikimodule}
{/if}