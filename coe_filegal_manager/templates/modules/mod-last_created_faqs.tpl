{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="last_created_faqs" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$modLastCreatedFaqs}
      <li>
          <a class="linkmodule" href="tiki-view_faq.php?faqId={$modLastCreatedFaqs[ix].faqId}">
            {$modLastCreatedFaqs[ix].title|escape}
          </a>
        </li>
    {/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
