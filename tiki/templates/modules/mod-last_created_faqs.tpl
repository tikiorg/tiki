{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_created_faqs.tpl,v 1.5 2003-11-20 23:49:04 mose Exp $ *}

{if $feature_faqs eq 'y'}
<div class="box">
<div class="box-title">
{include file="module-title.tpl" module_title="{tr}Last Created FAQs{/tr}" module_name="last_created_faqs"}
</div>
<div class="box-data">
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modLastCreatedFaqs}
<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="tiki-view_faq.php?faqId={$modLastCreatedFaqs[ix].faqId}">{$modLastCreatedFaqs[ix].title}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}