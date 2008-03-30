{* $Id$ *}
 
{* Copyright (c) 2004 *}
{* All Rights Reserved. See copyright.txt for details and a complete list of authors. *}
{* Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details. *}

<h1><a class="pagetitle" href="tiki-edit_quiz.php">{tr}Admin quizzes{/tr}</a>
{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Quiz" target="tikihelp" class="tikihelp" title="{tr}Quizzes{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>{/if}
{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=/tiki-edit_quiz.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Admin Quizzes tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}Edit Tpl{/tr}' /></a>{/if}</h1>

<div class="navbar">
<a class="linkbut" href="tiki-list_quizzes.php">{tr}List Quizzes{/tr}</a>
<a class="linkbut" href="tiki-quiz_stats.php">{tr}Quiz Stats{/tr}</a>
</div>

<h2>{tr}Create/edit quizzes{/tr}</h2>
{*check perms first *}
{if $individual eq 'y'}
<a class="link" href="tiki-objectpermissions.php?objectName={$name|escape:"url"}&amp;objectType=quiz&amp;permType=quizzes&amp;objectId={$quizId}">{tr}There are individual permissions set for this quiz{/tr}</a>
<br /><br />
{/if}
<! --- begin form to create/ edit quizzes -->
<form action="tiki-edit_quiz.php" method="post">
<input type="hidden" name="quizId" value="{$quizId|escape}" />
<table class="normal">
<tr>
<td class="formcolor">
<label for="quiz-name">{tr}Name{/tr}:</label>
</td>
<td class="formcolor">
	<input type="text" size ="80" name="name" id="quiz-name" value="{$name|escape}" />
</td>
</tr>
<tr>
<td class="formcolor">
<label for="quiz-desc">{tr}Description{/tr}:</label>
</td>
<td class="formcolor">
<textarea name="description" id="quiz-desc" rows="4" cols="75">{$description|escape}</textarea>
</td>
</tr>

<!-- here's the little gem that provides the categories -->
{include file=categorize.tpl}
<tr class="formcolor">

<!-- the publishing info does not work... don't trust it -->
<td>{tr}Publish Date{/tr}</td>
<td>
{html_select_date prefix="publish_" time=$publishDateSite start_year="-5" end_year="+10" field_order=$prefs.display_field_order} {tr}at{/tr} <span dir="ltr">{html_select_time prefix="publish_" time=$publishDateSite display_seconds=false}
&nbsp;{$siteTimeZone}
</span>
</td>
</tr>
<tr class="formcolor">
<td>{tr}Expiration Date{/tr}</td>
<td>
{html_select_date prefix="expire_" time=$expireDateSite start_year="-5" end_year="+10" field_order=$prefs.display_field_order} {tr}at{/tr} <span dir="ltr">{html_select_time prefix="expire_" time=$expireDateSite display_seconds=false}
&nbsp;{$siteTimeZone}
</span>
</td>
</tr>
<tr>
<td class="formcolor">
<label for="quiz-repeat">{tr}Quiz can be repeated{/tr}</td><td class="formcolor"><input type="checkbox" name="canRepeat" id="quiz-repeat" {if $canRepeat eq 'y'}checked="checked"{/if} /></td></tr>
<tr>
<td class="formcolor">
<label for="quiz-results">{tr}Store quiz results{/tr}</td>
<td class="formcolor">
<input type="checkbox" name="storeResults" id="quiz-results" {if $storeResults eq 'y'}checked="checked"{/if} /></td></tr>
<tr>
<!-- There is no immeidate feedback... the results come back as blank-->
<td class="formcolor"><label for="immediate-feedback">{tr}Immediate feedback{/tr}</td><td class="formcolor"><input type="checkbox" name="immediateFeedback" id="immediate-feedback" {if $immediateFeedback eq 'y'}checked="checked"{/if} /></td>
</tr>
<tr>
<td class="formcolor">
<label for="show-answers">{tr}Show correct answers{/tr}</td>
<td class="formcolor"><input type="checkbox" name="showAnswers" id="show-answers" {if $showAnswers eq 'y'}checked="checked"{/if} /></td></tr>
<tr>
<td class="formcolor">
<label for="shuffle-questions">{tr}Shuffle questions{/tr}</td><td class="formcolor">
<input type="checkbox" name="shuffleQuestions" id="shuffle-questions" {if $shuffleQuestions eq 'y'}checked="checked"{/if} />
</td>
</tr>
<tr>
<td class="formcolor">
<label for="shuffle-answers">{tr}Shuffle answers{/tr}</td><td class="formcolor">
<input type="checkbox" name="shuffleAnswers" id="shuffle-answers" {if $shuffleAnswers eq 'y'}checked="checked"{/if} /></td></tr>
<!--Why was this quoted out? Need to investigate
<tr><td class="formcolor"><label for="quiz-perpage">{tr}Questions per page{/tr}</td><td class="formcolor"><select name="questionsPerPage" id="quiz-perpage">{html_options values=$qpp selected=$questionsPerPage output=$qpp}</select></td></tr>-->
<tr>
<td class="formcolor">
<!-- quiz time limits do work-->
<label for="quiz-timelimit">{tr}Quiz is time limited{/tr}</label></td><td class="formcolor">
<input type="checkbox" name="timeLimited" id="quiz-timelimit" {if $timeLimited eq 'y'}checked="checked"{/if} />
</td>
</tr>
<tr>
<td class="formcolor">
<label for="quiz-maxtime">{tr}Maximum time{/tr}</label></td><td class="formcolor"><select name="timeLimit" id="quiz-maxtime">{html_options values=$mins selected=$timeLimit output=$mins}</select> {tr}minutes{/tr}</td></tr>

<tr>
<td class="formcolor">
<label for="quiz-passingperct">{tr}Passing Percentage{/tr}</label>
</td>
<td class="formcolor">
<input type="text" name="passingperct" id="quiz-passingperct" size=3 maxlength=3 value="{$passingperct}" />
{tr}%{/tr}</td></tr>

<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>



</table>
</form>

<!-- begin form for searching quizzes --->

<h2>{tr}Quizzes{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-edit_quiz.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>

<!-- begin table for displaying quiz data --->
<table class="normal">
<tr>
<td class="heading">
<a class="tableheading" href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'quizId_desc'}quizId_asc{else}quizId_desc{/if}">{tr}ID{/tr}</a>
</td>
<td class="heading">
<a class="tableheading" href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a>
</td>
<td class="heading">
<a class="tableheading" href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a>
</td>
<td class="heading">
<a class="tableheading" href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'canRepeat_desc'}canRepeat_asc{else}canRepeat_desc{/if}">{tr}canRepeat{/tr}</a>
</td>
<td class="heading">
<a class="tableheading" href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'timeLimit_desc'}timeLimit_asc{else}timeLimit_desc{/if}">{tr}timeLimit{/tr}</a>
</td>

<!-- I don't know why but these column head will not behave properly with sort -->
<td class="heading">{tr}Questions{/tr}</td>
<td class="heading">{tr}Results{/tr}</td>

{* still stuck on being able to sort by number of questions and results!
Results need to be sortable so as to give admin quick idea of user participation
<td><a class="tableheading" href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'results_desc'}results_asc{else}results_desc{/if}">{tr}Results{/tr}</a>
</td>
*}

<td class="heading">{tr}Action{/tr}</td>
</tr>
<!-- end header data -->
{section name=user loop=$channels}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$channels[user].quizId}</td>
<td class="odd">{$channels[user].name}</td>
<td class="odd">{$channels[user].description}</td>
<td class="odd">{$channels[user].canRepeat}</td>
<td class="odd">{$channels[user].timeLimited} {if $channels[user].timeLimited eq 'y'}({$channels[user].timeLimit} mins){/if}</td>
<td class="odd">{$channels[user].questions}</td>
<td class="odd">{$channels[user].results}</td>
<td class="odd">
   <a class="link" href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].quizId}">{tr}Remove{/tr}</a>
   <a class="link" href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;quizId={$channels[user].quizId}">{tr}Edit{/tr}</a>
   <a class="link" href="tiki-edit_quiz_questions.php?quizId={$channels[user].quizId}">{tr}Questions{/tr}</a>
   <a class="link" href="tiki-edit_quiz_results.php?quizId={$channels[user].quizId}">{tr}Results{/tr}</a>
   {if $channels[user].individual eq 'y'}({/if}<a class="link" href="tiki-objectpermissions.php?objectName={$channels[user].name|escape:"url"}&amp;objectType=quiz&amp;permType=quizzes&amp;objectId={$channels[user].quizId}">{tr}Perms{/tr}</a>{if $channels[user].individual eq 'y'}){/if}
</td>
</tr>
{else}
<tr>
<td class="even">{$channels[user].quizId}</td>
<td class="even">{$channels[user].name}</td>
<td class="even">{$channels[user].description}</td>
<td class="even">{$channels[user].canRepeat}</td>
<td class="even">{$channels[user].timeLimited} {if $channels[user].timeLimited eq 'y'}({$channels[user].timeLimit} mins){/if}</td>
<td class="even">{$channels[user].questions}</td>
<td class="even">{$channels[user].results}</td>
<td class="even">
   <a class="link" href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].quizId}">{tr}Remove{/tr}</a>
   <a class="link" href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;quizId={$channels[user].quizId}">{tr}Edit{/tr}</a>
   <a class="link" href="tiki-edit_quiz_questions.php?quizId={$channels[user].quizId}">{tr}Questions{/tr}</a>
   <a class="link" href="tiki-edit_quiz_results.php?quizId={$channels[user].quizId}">{tr}Results{/tr}</a>
   {if $channels[user].individual eq 'y'}({/if}<a class="link" href="tiki-objectpermissions.php?objectName={$channels[user].name|escape:"url"}&amp;objectType=quiz&amp;permType=quizzes&amp;objectId={$channels[user].quizId}">{tr}Perms{/tr}</a>{if $channels[user].individual eq 'y'}){/if}
</td>
</tr>
{/if}
{/section}
</table>

<! -- this is the page advance part -->

<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-edit_quiz.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-edit_quiz.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-edit_quiz.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

<!- tiki-edit_quiz.tpl end ->
