{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_created_faqs.tpl,v 1.7 2003-11-24 01:33:46 zaufi Exp $ *}

{if $feature_faqs eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` Created FAQs{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last Created FAQs{/tr}" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="last_created_faqs"}
  <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modLastCreatedFaqs}
      <tr>
        {if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
        <td class="module">
          <a class="linkmodule" href="tiki-view_faq.php?faqId={$modLastCreatedFaqs[ix].faqId}">
            {$modLastCreatedFaqs[ix].title}
          </a>
        </td>
      </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
