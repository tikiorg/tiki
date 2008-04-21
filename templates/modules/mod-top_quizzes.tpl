{* $Id$ *}

{if $prefs.feature_quizzes eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Top `$module_rows` Quizzes{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Top Quizzes{/tr}" assign="tpl_module_title"}
{/if}
{/if}

    {tikimodule title=$tpl_module_title name="top_quizzes" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$modTopQuizzes}
	<li><a class="linkmodule" href="tiki-take_quiz.php?quizId={$modTopQuizzes[ix].quizId}">{$modTopQuizzes[ix].quizName}</a></li>
    {/section}
	{if $nonums != 'y'}</ol>{else}</ul>{/if}
    {/tikimodule}
{/if}
