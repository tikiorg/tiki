{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="top_visited_faqs" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modTopVisitedFaqs nonums=$nonums}
	{section name=ix loop=$modTopVisitedFaqs}
		<li>
			<a class="linkmodule" href="tiki-view_faq.php?faqId={$modTopVisitedFaqs[ix].faqId}">
				{$modTopVisitedFaqs[ix].title|escape}
			</a>
		</li>
	{/section}
{/modules_list}
{/tikimodule}
