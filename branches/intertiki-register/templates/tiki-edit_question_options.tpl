<h1><a class="pagetitle" href="tiki-edit_question_options.php?questionId={$questionId}">{tr}Edit question options{/tr}</a></h1>
<a class="linkbut" href="tiki-list_quizzes.php">{tr}List Quizzes{/tr}</a>
<a class="linkbut" href="tiki-quiz_stats.php">{tr}Quiz Stats{/tr}</a>
<a class="linkbut" href="tiki-quiz_stats_quiz.php?quizId={$quizId}">{tr}this quiz stats{/tr}</a>
<a class="linkbut" href="tiki-edit_quiz.php?quizId={$quizId}">{tr}Edit this Quiz{/tr}</a>
<a class="linkbut" href="tiki-edit_quiz.php">{tr}Admin Quizzes{/tr}</a><br /><br />
<h2>{tr}Create/edit options for question{/tr}: <a  href="tiki-edit_quiz_questions.php?quizId={$question_info.quizId}&amp;questionId={$question_info.questionId}">{$question_info.question}</a></h2>
<form action="tiki-edit_question_options.php" method="post">
<input type="hidden" name="optionId" value="{$optionId|escape}" />
<input type="hidden" name="questionId" value="{$questionId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Option{/tr}:</td><td class="formcolor"><textarea name="optionText" rows="5" cols="40">{$optionText|escape}</textarea></td></tr>
<tr><td class="formcolor">{tr}Points{/tr}:</td><td class="formcolor"><input type="text" name="points" value="{$points|escape}" /></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<h2>Options</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-edit_question_options.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="questionId" value="{$questionId|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-edit_question_options.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'optionId_desc'}optionId_asc{else}optionId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-edit_question_options.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'optionText_desc'}optionText_asc{else}optionText_desc{/if}">{tr}text{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-edit_question_options.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'points_desc'}points_asc{else}points_desc{/if}">{tr}points{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{section name=user loop=$channels}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$channels[user].optionId}</td>
<td class="odd">{$channels[user].optionText}</td>
<td class="odd">{$channels[user].points}</td>
<td class="odd">
   <a class="link" href="tiki-edit_question_options.php?questionId={$questionId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].optionId}">{tr}Remove{/tr}</a>
   <a class="link" href="tiki-edit_question_options.php?questionId={$questionId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;optionId={$channels[user].optionId}">{tr}Edit{/tr}</a>
</td>
</tr>
{else}
<tr>
<td class="even">{$channels[user].optionId}</td>
<td class="even">{$channels[user].optionText}</td>
<td class="even">{$channels[user].points}</td>
<td class="even">
   <a class="link" href="tiki-edit_question_options.php?questionId={$questionId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].optionId}">{tr}Remove{/tr}</a>
   <a class="link" href="tiki-edit_question_options.php?questionId={$questionId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;optionId={$channels[user].optionId}">{tr}Edit{/tr}</a>
</td>
</tr>
{/if}
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-edit_question_options.php?questionId={$questionId}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-edit_question_options.php?questionId={$questionId}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-edit_question_options.php?questionId={$questionId}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

