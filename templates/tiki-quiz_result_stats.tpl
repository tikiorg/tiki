<h1><a class="pagetitle" href="tiki-quiz_result_stats.php?quizId={$quizId}&amp;resultId={$resultId}&amp;userResultId={$userResultId}">{tr}Quiz result stats{/tr}:</a>

<! -- the help link info -->

      {if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Quiz" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Edit Quiz Questions{/tr}"><img border='0' src='img/icons/help.gif' alt="{tr}Help{/tr}" /></a>{/if}

<! -- link to tpl -->

     {if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?=tiki-quiz_result_stats.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Edit Quiz Stats Tpl{/tr}"><img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}Edit Tpl{/tr}' /></a>
{/if}


</h1>
<! -- linkbuttons, they'd be better if they had rollover info -->

<a class="linkbut" href="tiki-list_quizzes.php">{tr}List Quizzes{/tr}</a>
<a class="linkbut" href="tiki-quiz_stats.php">{tr}Quiz Stats{/tr}</a>
<a class="linkbut" href="tiki-quiz_stats_quiz.php?quizId={$quizId}">{tr}this quiz stats{/tr}</a>
<a class="linkbut" href="tiki-edit_quiz.php?quizId={$quizId}">{tr}Edit this Quiz{/tr}</a>
<a class="linkbut" href="tiki-edit_quiz.php">{tr}Admin Quizzes{/tr}</a><br /><br />

<! -- begin table/ it has no internal linking... that needs fixing -->

<table class="normal">
<tr> 
  <td colspan="2" class="heading">{tr}Quiz stats{/tr}</td>
</tr>
<tr> 
  <td class="even">{tr}Quiz{/tr}</td>
  <td class="even">{$quiz_info.name}</td>
</tr>
<tr> 
  <td class="even">{tr}User{/tr} </td>
  <td class="even">{$ur_info.user|userlink}</td>
{* RFE
add userlink/ next step is to grab quiz results and include it in messaging so that all stakeholders will receive results
*}
</tr>
<tr> 
  <td class="even">{tr}Date{/tr}</td>
  <td class="even">{$ur_info.timestamp|tiki_short_datetime}</td>
</tr>
<tr> 
  <td class="even">{tr}Points{/tr}</td>
  <td class="even">{$ur_info.points} / {$ur_info.maxPoints}</td>
</tr>
<tr> 
  <td class="even">{tr}Time{/tr}</td>
  <td class="even">{$ur_info.timeTaken} secs</td>
</tr>
</table>

<!-- I'm not sure why this is here -->

<br /> 
Answer: <br /> 
{*
what is this supposed to be doing? isn't it already in the table below?
*}

<div class="quizanswer">{$result.answer}</div>
<br />

<h2>{tr}User answers{/tr}</h2>
<! -- table displaying user results -->
<p>sorting doesn't work here but should</p>
<table class="normal">
  <tr>
{*
   <td  class="heading">{tr}Question{/tr}</td>
*}
<td class="heading">
<a class="tableheading" href="tiki-quiz_result_stats.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'question_desc'}question_asc{else}question_desc{/if}">{tr}Questions{/tr}</a>
</td>

{*
   <td  class="heading">{tr}Answer{/tr}</td>
*}

<td class="heading">
<a class="tableheading" href="tiki-quiz_result_stats.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'optionText_desc'}optionText_asc{else}optionText_desc{/if}">{tr}Answer{/tr}</a>
</td>


{*
   <td  class="heading">{tr}Points{/tr}</td>
*}
<td class="heading">
<a class="tableheading" href="tiki-quiz_result_stats.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'points_desc'}points_asc{else}points_desc{/if}">{tr}Points{/tr}</a>
</td>
<td class="heading">
{tr}Upload{/tr}
</td>



  </tr>
{cycle print=false values="odd,even"}
{section name=ix loop=$questions}
  <tr>
    <td class="{cycle advance=false}">{$questions[ix].question}</td>
    <td class="{cycle advance=false}">{$questions[ix].options[0].optionText}</td>
    <td class="{cycle}">{$questions[ix].options[0].points}</td>
	{if $questions[ix].options[0].filename}
    <td class="{cycle}">
	<a href="tiki-quiz_download_answer.php?answerUploadId={$questions[ix].options[0].answerUploadId}">{$questions[ix].options[0].filename}</a>
	</td>
	{/if}
  </tr>
{/section}
</table><br />


