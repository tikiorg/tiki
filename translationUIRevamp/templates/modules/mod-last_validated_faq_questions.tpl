{* $Id: mod-last_created_faqs.tpl 21754 2009-09-25 20:26:38Z chealer $ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="last_validated_faq_questions" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$modLastValidatedFaqQuestions}
      <li>
          <a class="linkmodule" href="tiki-view_faq.php?faqId={$modLastValidatedFaqQuestions[ix].faqId}#q{$modLastValidatedFaqQuestions[ix].questionId}" title="{$modLastValidatedFaqQuestions[ix].question}">
            {$modLastValidatedFaqQuestions[ix].question|truncate:$trunc|escape}
          </a>
        </li>
    {/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
