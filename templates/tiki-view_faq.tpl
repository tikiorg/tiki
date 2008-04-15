<h1><a class="pagetitle" href="tiki-view_faq.php?faqId={$faqId}">{$faq_info.title}</a>

{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}FAQs" target="tikihelp" class="tikihelp" title="{tr}View FAQ{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-view_faq.tpl" target="tikihelp" class="tikihelp" title="{tr}View FAQ Tpl{/tr}: {tr}Admin Menus tpl{/tr}">
<img src="img/icons/info.gif" border="0" width="16" height="16" alt='{tr}Edit Template{/tr}' /></a>{/if}</h1>

<a class="linkbut" href="tiki-list_faqs.php">{tr}List FAQs{/tr}</a>
{if $tiki_p_admin_faqs eq 'y'}<a class="linkbut" href="tiki-list_faqs.php?faqId={$faqId}">{tr}Edit this FAQ{/tr}</a> {/if}
{if $tiki_p_admin_faqs eq 'y'}<a class="linkbut" href="tiki-faq_questions.php?faqId={$faqId}">{tr}New Question{/tr}{/if}</a><br /><br />
<h2>{tr}Questions{/tr}</h2>
{if !$channels}
{tr}There are no questions in this FAQ.{/tr}
{else}
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
<span class="faq_question_prefix">{tr}Q{/tr}: </span>{$channels[ix].question}
</div>
<div class="faqanswer">
<span class="faq_answer_prefix">{tr}A{/tr}: </span>{$channels[ix].parsed}
</div>
</div>
{/section}
{/if}
{if $faq_info.canSuggest eq 'y' and $tiki_p_suggest_faq eq 'y'}
<a href="javascript:flip('faqsugg');" class="linkbut">
{if $suggested_cant == 0}
{tr}Add Suggestion{/tr}{elseif $suggested_cant == 1}
<span class="highlight">{tr}1 suggestion{/tr}</span>{else}
<span class="highlight">{$suggested_cant} {tr}suggestions{/tr}</span>{/if}</a>
{/if}
{if $prefs.feature_faq_comments == 'y'
&& (($tiki_p_read_comments  == 'y'
&& $comments_cant != 0)
||  $tiki_p_post_comments  == 'y'
||  $tiki_p_edit_comments  == 'y')}
<a href="#comment" onclick="javascript:flip('comzone');flip('comzone_close','inline');return false;" class="linkbut">
{if $comments_cant == 0 or ($tiki_p_read_comments  == 'n' and $tiki_p_post_comments  == 'y')}
{tr}Add Comment{/tr}
{elseif $comments_cant == 1}
<span class="highlight">{tr}1 comment{/tr}</span>
{else}
<span class="highlight">{$comments_cant} {tr}comments{/tr}</span>
{/if}
<span id="comzone_close" style="display:{if isset($smarty.session.tiki_cookie_jar.show_comzone) and $smarty.session.tiki_cookie_jar.show_comzone eq 'y'}inline{else}none{/if};">({tr}close{/tr})</span>
</a>
{/if}
{if $faq_info.canSuggest eq 'y' and $tiki_p_suggest_faq eq 'y'}
<div class="faq_suggestions" id="faqsugg" style="display:{if !empty($error)}block{else}none{/if};">
{if !empty($error)}
<br />
 <div class="simplebox highlight">
  {$error}
 </div>
{/if}
<br />
<form action="tiki-view_faq.php" method="post">
<input type="hidden" name="faqId" value="{$faqId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Question{/tr}:</td><td class="formcolor"><textarea rows="2" cols="80" name="suggested_question">{if $pendingquestion}{$pendingquestion}{/if}</textarea></td></tr>
<tr><td class="formcolor">{tr}Answer{/tr}:</td><td class="formcolor"><textarea rows="2" cols="80" name="suggested_answer">{if $pendinganswer}{$pendinganswer}{/if}</textarea></td></tr>
{if $prefs.feature_antibot eq 'y' && $user eq ''}
{include file="antibot.tpl"}
{/if}
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

{if $prefs.faqs_feature_copyrights  eq 'y' and $prefs.wikiLicensePage}
  {if $prefs.wikiLicensePage == $page}
    {if $tiki_p_edit_copyrights eq 'y'}
      <p class="editdate">{tr}To edit the copyright notices{/tr} <a href="copyrights.php?page={$copyrightpage}">{tr}Click Here{/tr}</a>.</p>
    {/if}
  {else}
    <p class="editdate">{tr}The content on this page is licensed under the terms of the{/tr} <a href="tiki-index.php?page={$prefs.wikiLicensePage}&amp;copyrightpage={$page|escape:"url"}">{$prefs.wikiLicensePage}</a>.</p>
  {/if}
{/if}
 


{if $prefs.feature_faq_comments == 'y'
&& (($tiki_p_read_comments  == 'y'
&& $comments_cant != 0)
||  $tiki_p_post_comments  == 'y'
||  $tiki_p_edit_comments  == 'y')}
{include file=comments.tpl}
{/if}
