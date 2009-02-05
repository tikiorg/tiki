{title help="Quiz"}{tr}Quiz result stats{/tr}{/title}

<! -- linkbuttons, they would be better if they had rollover info -->

<div class="navbar">
	{button href="tiki-list_quizzes.php" _text="{tr}List Quizzes{/tr}"} 
	{button href="tiki-quiz_stats.php" _text="{tr}Quiz Stats{/tr}"}
	{button href="tiki-quiz_stats_quiz.php?quizId=$quizId" _text="{tr}This Quiz Stats{/tr}"}
	{button href="tiki-edit_quiz.php?quizId=$quizId" _text="{tr}Edit this Quiz{/tr}"} 
	{button href="tiki-edit_quiz.php" _text="{tr}Admin Quizzes{/tr}"}
</div>
<! -- begin table/ it has no internal linking... that needs fixing -->

<table class="normal">
<tr> 
  <th colspan="2">{tr}Quiz stats{/tr}</th>
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
   <th>{tr}Question{/tr}</th>
*}
<th>
<a href="tiki-quiz_result_stats.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'question_desc'}question_asc{else}question_desc{/if}">{tr}Questions{/tr}</a>
</th>

{*
   <th>{tr}Answer{/tr}</th>
*}

<th>
<a href="tiki-quiz_result_stats.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'optionText_desc'}optionText_asc{else}optionText_desc{/if}">{tr}Answer{/tr}</a>
</th>


{*
   <th>{tr}Points{/tr}</th>
*}
<th>
<a href="tiki-quiz_result_stats.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'points_desc'}points_asc{else}points_desc{/if}">{tr}Points{/tr}</a>
</th>
<th>
{tr}Upload{/tr}
</th>



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


