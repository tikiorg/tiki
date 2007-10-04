{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_quizzes.tpl,v 1.11 2007-10-04 22:17:47 nyloth Exp $ *}

{if $prefs.feature_quizzes eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Top `$module_rows` Quizzes{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Top Quizzes{/tr}" assign="tpl_module_title"}
{/if}
{/if}

    {tikimodule title=$tpl_module_title name="top_quizzes" flip=$module_params.flip decorations=$module_params.decorations}
    <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modTopQuizzes}
	<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
	<td class="module"><a class="linkmodule" href="tiki-take_quiz.php?quizId={$modTopQuizzes[ix].quizId}">{$modTopQuizzes[ix].quizName}</a></td></tr>
    {/section}
    </table>
    {/tikimodule}
{/if}
