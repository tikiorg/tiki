<a class="pagetitle" href="tiki-quiz_result_stats.php?quizId={$quizId}&amp;resultId={$resultId}&amp;userResultId={$userResultId}">{tr}Quiz result stats{/tr}:</a><br/><br/>
[<a class="link" href="tiki-list_quizzes.php">{tr}list quizzes{/tr}</a>
|<a class="link" href="tiki-quiz_stats.php">{tr}quiz stats{/tr}</a>
|<a class="link" href="tiki-quiz_stats_quiz.php?quizId={$quizId}">{tr}this quiz stats{/tr}</a>
|<a class="link" href="tiki-edit_quiz.php?quizId={$quizId}">{tr}edit this quiz{/tr}</a>
|<a class="link" href="tiki-edit_quiz.php">{tr}admin quizzes{/tr}</a>]<br/><br/>
<table class="normal">
<tr> 
  <td colspan="2" class="heading">{tr}Quiz stats{/tr}</td>
</tr>
<tr> 
  <td class="even">{tr}Quiz{/tr}</td>
  <td class="even">{$quiz_info.name}</td>
</tr>
<tr> 
  <td class="even">{tr}User{/tr}</td>
  <td class="even">{$ur_info.user}</td>
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
<br/><br/>
Answer:<br/>
<div class="quizanswer">{$result.answer}</div>
<br/>
<h2>{tr}User answers{/tr}</h2>
  <table class="normal">
  <tr>
   <td width="70%" class="heading">{tr}Question{/tr}</td>
   <td width="30%" class="heading">{tr}Answer{/tr}</td>
   <td width="30%" class="heading">{tr}Points{/tr}</td>
  </tr>
{cycle print=false values="odd,even"}
{section name=ix loop=$questions}
  <tr>
    <td class="{cycle advance=false}">{$questions[ix].question}</td>
    <td class="{cycle advance=false}">{$questions[ix].options[0].optionText}</td>
    <td class="{cycle}">{$questions[ix].options[0].points}</td>
  </tr>
{/section}
</table><br/>


