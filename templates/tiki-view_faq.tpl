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
{if $faq_info.canSuggest eq 'y' and $tiki_p_suggest_faq eq 'y'}
[<a href="javascript:show('faqsugg');" class="opencomlink">{tr}Show suggested questions/suggest a question{/tr}</a>|
<a href="javascript:hide('faqsugg');" class="opencomlink">{tr}Hide suggested questions{/tr}</a>]<br/><br/>
<div class="faq_suggestions" id="faqsugg" style="display:none;">
<form action="tiki-view_faq.php" method="post">
<input type="hidden" name="faqId" value="{$faqId}" />
<table class="normal">
<tr><td class="formcolor">{tr}Question{/tr}:</td><td class="formcolor"><textarea rows="2" cols="80" name="suggested_question"></textarea></td></tr>
<tr><td class="formcolor">{tr}Answer{/tr}:</td><td class="formcolor"><textarea rows="2" cols="80" name="suggested_answer"></textarea></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="sugg" value="{tr}Add{/tr}" /></td></tr>
</table>
</form>
<br/>
<table class="normal">
<tr><td class="heading">{tr}Suggested questions{/tr}</td></tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$suggested}
<tr><td class="{cycle}">{$suggested[ix].question}</td></tr>
{/section}
</table>
</div>
{/if}
{if $feature_faq_comments eq 'y'}
{include file=comments.tpl}
{/if}