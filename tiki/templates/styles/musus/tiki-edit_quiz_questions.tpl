<a class="pagetitle" href="tiki-edit_quiz_questions.php?quizId={$quizId}">{tr}Edit quiz questions{/tr}</a><br /><br />
<a class="linkbut" href="tiki-list_quizzes.php">{tr}list quizzes{/tr}</a>
<a class="linkbut" href="tiki-quiz_stats.php">{tr}quiz stats{/tr}</a>
<a class="linkbut" href="tiki-quiz_stats_quiz.php?quizId={$quizId}">{tr}this quiz stats{/tr}</a>
<a class="linkbut" href="tiki-edit_quiz.php?quizId={$quizId}">{tr}edit this quiz{/tr}</a>
<a class="linkbut" href="tiki-edit_quiz.php">{tr}admin quizzes{/tr}</a><br /><br />
<h2>{tr}Create/edit questions for quiz{/tr}: <a href="tiki-edit_quiz.php?quizId={$quiz_info.quizId}">{$quiz_info.name}</a></h2>
<form action="tiki-edit_quiz_questions.php" method="post">
<input type="hidden" name="quizId" value="{$quizId|escape}" />
<input type="hidden" name="questionId" value="{$questionId|escape}" />

<table>
<tr>
<td>{tr}Question{/tr}:</td>
{* change the size of the text field here * }
<td>
<textarea name="question" rows="5" cols="80">{$question|escape}</textarea>
</td>
</tr>
<tr><td>{tr}Position{/tr}:</td><td><select name="position">{html_options values=$positions output=$positions selected=$position}</select>
</td>
</tr>
<tr><td>&nbsp;</td><td><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<h2>{tr}Reuse question{/tr}</h2>
<form action="tiki-edit_quiz_questions.php" method="post">
<input type="hidden" name="quizId" value="{$quizId|escape}" />
<table>
<tr><td>{tr}Question{/tr}:</td>
<td>
<select name="usequestionid">
{section name=ix loop=$questions}
{* adding the juicy truncation bits here |truncate:110:"":true * }
<option value="{$questions[ix].questionId|escape}">{$questions[ix].question|truncate:110:"":true}</option>
{/section}
</select>
</td></tr>
<tr><td>{tr}Position{/tr}:</td><td><select name="position">{html_options values=$positions output=$positions selected=$position}</select></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" name="useQuestion" value="{tr}use{/tr}" /></td></tr>
</table>
</form>
<h2>{tr}Questions{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="tiki-edit_quiz_questions.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="quizId" value="{$quizId|escape}" />
   </form>
   </td>
</tr>
</table>
<table>
<tr>
<th><a href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'questionId_desc'}questionId_asc{else}questionId_desc{/if}">{tr}ID{/tr}</a></th>
<th><a href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'position_desc'}position_asc{else}position_desc{/if}">{tr}position{/tr}</a></th>
<th><a href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'question_desc'}question_asc{else}question_desc{/if}">{tr}question{/tr}</a></th>
<th>{tr}options{/tr}</th>
<th>{tr}maxScore{/tr}</th>
<th>{tr}action{/tr}</th>
</tr>
{section name=user loop=$channels}
{if $smarty.section.user.index % 2}
<tr class="odd">
<td>{$channels[user].questionId}</td>
<td>{$channels[user].position}</td>
<td>{$channels[user].question}</td>
<td>{$channels[user].options}</td>
<td>{$channels[user].maxPoints}</td>
<td>
   <a href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].questionId}">{tr}remove{/tr}</a>
   <a href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;questionId={$channels[user].questionId}">{tr}edit{/tr}</a>
   <a href="tiki-edit_question_options.php?quizId={$quizId}&amp;questionId={$channels[user].questionId}">{tr}options{/tr}</a>
</td>
</tr>
{else}
<tr class="even">
<td>{$channels[user].questionId}</td>
<td>{$channels[user].position}</td>
<td>{$channels[user].question}</td>
<td>{$channels[user].options}</td>
<td>{$channels[user].maxPoints}</td>
<td>
   <a href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].questionId}">{tr}remove{/tr}</a>
   <a href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;questionId={$channels[user].questionId}">{tr}edit{/tr}</a>
   <a href="tiki-edit_question_options.php?quizId={$quizId}&amp;questionId={$channels[user].questionId}">{tr}options{/tr}</a>
</td>
</tr>
{/if}
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
