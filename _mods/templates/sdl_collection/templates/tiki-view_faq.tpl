<a class="pagetitle" href="tiki-view_faq.php?faqId={$faqId}">{$faq_info.title}</a>

{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=FAQ" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}view faq{/tr}">
<img border='0' src='img/icons/help.gif' alt='{tr}help{/tr}' /></a>{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-view_faq.tpl" target="tikihelp" class="tikihelp" title="{tr}view faq tpl{/tr}: {tr}admin menus tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='{tr}edit template{/tr}' /></a>{/if}



<br /><br />
<a class="linkbut" href="tiki-list_faqs.php">{tr}List FAQs{/tr}</a><br /><br />
<h2>{tr}Frequently Asked Questions{/tr}</h2>
<div class="faqlistquestions">
<ol>
{section name=ix loop=$channels}
<li><a class="link" href="#q{$channels[ix].questionId}">{$channels[ix].question}</a></li>
{/section}
</ol>
</div>
<h2>{tr}Frequently Given Answers{/tr}</h2>
{section name=ix loop=$channels}
<a name="q{$channels[ix].questionId}"></a>
<div class="faqqa">
<div class="faqquestion">
{tr}Q{/tr}: {$channels[ix].question}
</div>
<div class="faqanswer">
{tr}A{/tr}: {$channels[ix].parsed}
</div>
</div>
{/section}
{if $faq_info.canSuggest eq 'y' and $tiki_p_suggest_faq eq 'y'}
[&nbsp;<a href="javascript:show('faqsugg');" class="opencomlink">{tr}Show suggested questions/suggest a question{/tr}</a>&nbsp;|&nbsp;
<a href="javascript:hide('faqsugg');" class="opencomlink">{tr}Hide suggested questions{/tr}</a>&nbsp;]<br /><br />
<div class="faq_suggestions" id="faqsugg" style="display:none;">
<form action="tiki-view_faq.php" method="post">
<input type="hidden" name="faqId" value="{$faqId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Question{/tr}:</td><td class="formcolor"><textarea rows="2" cols="80" name="suggested_question"></textarea></td></tr>
<tr><td class="formcolor">{tr}Answer{/tr}:</td><td class="formcolor"><textarea rows="2" cols="80" name="suggested_answer"></textarea></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="sugg" value="{tr}Add{/tr}" /></td></tr>
</table>
</form>
<br />
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
{if $tiki_p_read_comments eq 'y'}
<div id="page-bar">
<table>
<tr><td>
<div class="button2">
<a href="javascript:flip('comzone{if $comments_show eq 'y'}open{/if}');" class="linkbut">{if $comments_cant eq 0}{tr}comment{/tr}{elseif $comments_cant eq 1}1 {tr}comment{/tr}{else}{$comments_cant} {tr}comments{/tr}{/if}</a>
</div>
</td></tr></table>
</div>
{include file=comments.tpl}
{/if}
{/if}
