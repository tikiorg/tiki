<a class="pagetitle" href="tiki-view_faq.php?faqId={$faqId}">{$faq_info.title}</a><br/><br/>
[<a class="link" href="tiki-list_faqs.php">{tr}List FAQs{/tr}</a>]<br/><br/>
<h2>{tr}FAQ Questions{/tr}</h2>
<div class="faqlistquestions">
<ol>
{section name=ix loop=$channels}
<li><a class="link" href="#q{$channels[ix].questionId}">{$channels[ix].question}</a></li>
{/section}
</ol>
</div>
<h2>{tr}FAQ Answers{/tr}</h2>
{section name=ix loop=$channels}
<a name="q{$channels[ix].questionId}"></a>
<div class="faqqa">
<div class="faqquestion">
Q: {$channels[ix].question}
</div>
<div class="faqanswer">
A: {$channels[ix].answer}
</div>
</div>
{/section}
{if $feature_faq_comments eq 'y'}
{include file=comments.tpl}
{/if}