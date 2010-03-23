{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="top_quizzes" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modTopQuizzes nonums=$nonums}
	{section name=ix loop=$modTopQuizzes}
		<li>
			<a class="linkmodule" href="tiki-take_quiz.php?quizId={$modTopQuizzes[ix].quizId}">
				{$modTopQuizzes[ix].quizName|escape}
			</a>
		</li>
	{/section}
{/modules_list}
{/tikimodule}

