{* $Id: mod-last_validated_faq_questions.tpl 33949 2011-04-14 05:13:23Z chealer $ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="last_validated_faq_questions" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modLastValidatedFaqQuestions nonums=$nonums}
	{section name=ix loop=$modLastValidatedFaqQuestions}
		<li>
			<a class="linkmodule" href="tiki-view_faq.php?faqId={$modLastValidatedFaqQuestions[ix].faqId}#q{$modLastValidatedFaqQuestions[ix].questionId}" title="{$modLastValidatedFaqQuestions[ix].question}">
				{$modLastValidatedFaqQuestions[ix].question|truncate:$trunc|escape}
			</a>
		</li>
	{/section}
{/modules_list}
{/tikimodule}
