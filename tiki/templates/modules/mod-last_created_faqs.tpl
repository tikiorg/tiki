{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_created_faqs.tpl,v 1.3 2003-09-25 01:05:22 rlpowell Exp $ *}

{if $feature_faqs eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Last Created FAQs{/tr}" module_name="last_created_faqs"}
</div>
<div class="box-data">
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modLastCreatedFaqs}
<tr><td   class="module" valign="top">{$smarty.section.ix.index_next})</td><td class="module">&nbsp;<a class="linkmodule" href="tiki-view_faq.php?faqId={$modLastCreatedFaqs[ix].faqId}">{$modLastCreatedFaqs[ix].title}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}