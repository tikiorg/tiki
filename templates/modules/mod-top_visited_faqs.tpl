{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_visited_faqs.tpl,v 1.6 2003-11-23 04:01:52 gmuslera Exp $ *}

{if $feature_faqs eq 'y'}
    {tikimodule title="{tr}Top Visited FAQs{/tr}" name="top_visited_faqs"}
    <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modTopVisitedFaqs}
	<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
        <td class="module"><a class="linkmodule" href="tiki-view_faq.php?faqId={$modTopVisitedFaqs[ix].faqId}">{$modTopVisitedFaqs[ix].title}</a></td></tr>
    {/section}
    </table>
    {/tikimodule}
{/if}