{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-last_created_quizzes.tpl,v 1.12 2007/10/14 17:51:00 mose *}

{if $prefs.feature_quizzes eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` Created Quizzes{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last Created Quizzes{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="last_created_quizzes" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$modLastCreatedQuizzes}
      <li>
        <a class="linkmodule" href="tiki-take_quiz.php?quizId={$modLastCreatedQuizzes[ix].quizId}">
            {$modLastCreatedQuizzes[ix].name}
          </a>
        </li>
    {/section}
	{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
{/if}
