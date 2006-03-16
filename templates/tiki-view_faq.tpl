<h1><a class="pagetitle" href="tiki-view_faq.php?faqId={$faqId}">{$faq_info.title}</a>

{if $feature_help eq 'y'}
<a href="{$helpurl}FAQs" target="tikihelp" class="tikihelp" title="{tr}view faq{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-view_faq.tpl" target="tikihelp" class="tikihelp" title="{tr}view faq tpl{/tr}: {tr}admin menus tpl{/tr}">
<img src="img/icons/info.gif" border="0" width="16" height="16" alt='{tr}edit template{/tr}' /></a>{/if}</h1>

<a class="linkbut" href="tiki-list_faqs.php">{tr}List FAQs{/tr}</a>
{if $tiki_p_admin_faqs eq 'y'}<a class="linkbut" href="tiki-list_faqs.php?faqId={$faqId}">{tr}Edit this FAQ{/tr}</a> {/if}
{if $tiki_p_admin_faqs eq 'y'}<a class="linkbut" href="tiki-faq_questions.php?faqId={$faqId}">{tr}new question{/tr}{/if}</a><br /><br />
<h2>{tr}Questions{/tr}</h2>
<div class="faqlistquestions">
<ol>
{section name=ix loop=$channels}
<li><a class="link" href="#q{$channels[ix].questionId}">{$channels[ix].question}</a></li>
{/section}
</ol>
</div>
<h2>{tr}Answers{/tr}</h2>
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
<a href="javascript:flip('faqsugg');" class="linkbut">
{if $suggested_cant == 0}
{tr}add suggestion{/tr}{elseif $suggested_cant == 1}
<span class="highlight">{tr}1 suggestion{/tr}</span>{else}
<span class="highlight">{$suggested_cant} {tr}suggestions{/tr}</span>{/if}</a>
{/if}
{if $feature_faq_comments == 'y'
&& (($tiki_p_read_comments  == 'y'
&& $comments_cant != 0)
||  $tiki_p_post_comments  == 'y'
||  $tiki_p_edit_comments  == 'y')}
    <a href="#comments" onclick="javascript:flip('comzone{if $comments_show eq 'y'}open{/if}');" class="linkbut">
{if $comments_cant == 0}
{tr}add comment{/tr}
{elseif $comments_cant == 1}
<span class="highlight">{tr}1 comment{/tr}</span>
{else}
<span class="highlight">{$comments_cant} {tr}comments{/tr}</span>
{/if}
</a>
{/if}
{if $faq_info.canSuggest eq 'y' and $tiki_p_suggest_faq eq 'y'}
<div class="faq_suggestions" id="faqsugg" style="display:none;">
<br />
<form action="tiki-view_faq.php" method="post">
<input type="hidden" name="faqId" value="{$faqId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Question{/tr}:</td><td class="formcolor"><textarea rows="2" cols="80" name="suggested_question"></textarea></td></tr>
<tr><td class="formcolor">{tr}Answer{/tr}:</td><td class="formcolor"><textarea rows="2" cols="80" name="suggested_answer"></textarea></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="sugg" value="{tr}Add{/tr}" /></td></tr>
</table>
</form>
{if count($suggested) != 0}
<br />
<table class="normal">
<tr><td class="heading">{tr}Suggested questions{/tr}</td></tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$suggested}
<tr><td class="{cycle}">{$suggested[ix].question}</td></tr>
{/section}
</table>
{/if}
</div>
{/if}
{if $feature_faq_comments == 'y'
&& (($tiki_p_read_comments  == 'y'
&& $comments_cant != 0)
||  $tiki_p_post_comments  == 'y'
||  $tiki_p_edit_comments  == 'y')}
{include file=comments.tpl}
{/if}
