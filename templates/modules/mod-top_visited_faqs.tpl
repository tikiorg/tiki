{* $Id$ *}

{if $prefs.feature_faqs eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Top `$module_rows` Visited FAQs{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Top Visited FAQs{/tr}" assign="tpl_module_title"}
{/if}
{/if}

    {tikimodule title=$tpl_module_title name="top_visited_faqs" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
	{if $nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$modTopVisitedFaqs}
	<li><a class="linkmodule" href="tiki-view_faq.php?faqId={$modTopVisitedFaqs[ix].faqId}">{$modTopVisitedFaqs[ix].title}</a></li>
    {/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
    {/tikimodule}
{/if}
