<h1><a class="pagetitle" href="tiki-admin_survey_questions.php?surveyId={$surveyId}">{tr}Edit survey questions{/tr}</a>: {$survey_info.name}</h1>
<span class="button2"><a class="linkbut" href="tiki-list_surveys.php">{tr}List surveys{/tr}</a></span>
<span class="button2"><a class="linkbut" href="tiki-survey_stats.php">{tr}Survey Stats{/tr}</a></span>
<span class="button2"><a class="linkbut" href="tiki-survey_stats_survey.php?surveyId={$surveyId}">{tr}this survey stats{/tr}</a></span>
<span class="button2"><a class="linkbut" href="tiki-admin_surveys.php?surveyId={$surveyId}">{tr}Edit this Survey{/tr}</a></span>
<span class="button2"><a class="linkbut" href="tiki-admin_surveys.php">{tr}Admin Surveys{/tr}</a></span><br /><br />
<h2>{tr}Create/edit questions for survey{/tr}: <a href="tiki-admin_surveys.php?surveyId={$survey_info.surveyId}">{$survey_info.name}</a></h2>
<form action="tiki-admin_survey_questions.php" method="post">
<input type="hidden" name="surveyId" value="{$surveyId|escape}" />
<input type="hidden" name="questionId" value="{$questionId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Question{/tr}:</td><td class="formcolor"><textarea name="question" rows="5" cols="40">{$info.question|escape}</textarea></td></tr>
<tr><td class="formcolor">{tr}Position{/tr}:</td><td class="formcolor"><select name="position">{html_options values=$positions output=$positions selected=$info.position}</select></td></tr>
<tr><td class="formcolor">{tr}Type{/tr}:</td><td class="formcolor">
<select name="type">
<option value='c' {if $info.type eq 'c'}selected=selected{/if}>{tr}One choice{/tr}</option>
<option value='m' {if $info.type eq 'm'}selected=selected{/if}>{tr}Multiple choices{/tr}</option>
<option value='t' {if $info.type eq 't'}selected=selected{/if}>{tr}Short text{/tr}</option>
<option value='x' {if $info.type eq 'x'}selected=selected{/if}>{tr}Wiki textaera{/tr}</option>
<option value='r' {if $info.type eq 'r'}selected=selected{/if}>{tr}Rate (1..5){/tr}</option>
<option value='s' {if $info.type eq 's'}selected=selected{/if}>{tr}Rate (1..10){/tr}</option>
<option value='r' {if $info.type eq 'r'}selected=selected{/if}>{tr}Rate{/tr}</option>
</select></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor">
{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}For a multiple answer question put the answers into the following field, separated by a comma. Example: one,two,many,lots{/tr}.<br />{tr}For a rate, you can give the maximum value.{/tr}{/remarksbox}
</td></tr>
<tr><td class="formcolor">{tr}Options (if apply){/tr}:</td><td class="formcolor"><input type="text" name="options" value="{$info.options|escape}" size="80" /></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<h2>{tr}Questions{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_survey_questions.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="surveyId" value="{$surveyId|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_survey_questions.php?surveyId={$surveyId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'questionId_desc'}questionId_asc{else}questionId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_survey_questions.php?surveyId={$surveyId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'position_desc'}position_asc{else}position_desc{/if}">{tr}Position{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_survey_questions.php?surveyId={$surveyId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'question_desc'}question_asc{else}question_desc{/if}">{tr}question{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_survey_questions.php?surveyId={$surveyId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}Type{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_survey_questions.php?surveyId={$surveyId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'options_desc'}options_asc{else}options_desc{/if}">{tr}Options{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle print=false values="odd,even"}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].questionId}</td>
<td class="{cycle advance=false}">{$channels[user].position}</td>
<td class="{cycle advance=false}">{$channels[user].question}</td>
<td class="{cycle advance=false}">{$channels[user].type}</td>
<td class="{cycle advance=false}">{$channels[user].options}</td>
<td class="odd">
   <a class="link" href="tiki-admin_survey_questions.php?surveyId={$surveyId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].questionId}">{tr}Delete{/tr}</a>
   <a class="link" href="tiki-admin_survey_questions.php?surveyId={$surveyId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;questionId={$channels[user].questionId}">{tr}Edit{/tr}</a>
</td>
</tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_survey_questions.php?surveyId={$surveyId}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_survey_questions.php?surveyId={$surveyId}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-admin_survey_questions.php?surveyId={$surveyId}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

