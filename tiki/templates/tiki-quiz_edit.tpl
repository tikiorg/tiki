{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-quiz_edit.tpl,v 1.1 2004-05-04 18:40:20 ggeller Exp $ *}

{* Copyright (c) 2004 *}
{* All Rights Reserved. See copyright.txt for details and a complete list of authors. *}
{* Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details. *}

<!- tiki-quiz_edit.tpl start ->

<a class="pagetitle" href="tiki-quiz_edit.php?quizId={$quizId}">{tr}Edit quiz{/tr}: {$quiz_info.name}</a><br /><br />
<a class="linkbut" href="tiki-list_quizzes.php">{tr}list quizzes{/tr}</a>
<a class="linkbut" href="tiki-quiz_stats.php">{tr}quiz stats{/tr}</a>
<a class="linkbut" href="tiki-quiz_stats_quiz.php?quizId={$quizId}">{tr}this quiz stats{/tr}</a>
<a class="linkbut" href="tiki-edit_quiz.php?quizId={$quizId}">{tr}edit this quiz{/tr}</a>
<a class="linkbut" href="tiki-edit_quiz.php">{tr}admin quizzes{/tr}</a><br /><br />

<br /><br />
<a class="linkbut" href="tiki-list_quizzes.php">list quizzes</a>
<a class="linkbut" href="tiki-quiz_stats.php">quiz stats</a>
<a class="linkbut" href="tiki-edit_quiz.php">admin quizzes</a><br /><br />

<h2>Create/edit quizzes</h2>
<form action="tiki-edit_quiz.php" method="post">
<input type="hidden" name="quizId" value="0" />
<table class="normal">
<tr><td class="formcolor"><label for="quiz-name">Name:</label></td><td class="formcolor"><input type="text" name="name" id="quiz-name" value="" /></td></tr>
<tr><td class="formcolor"><label for="quiz-desc">Description:</label></td><td class="formcolor"><textarea name="description" id="quiz-desc" rows="4" cols="40"></textarea></td></tr>

<tr class="formcolor">

<tr class="formcolor">
 <td>Categorize</td>
 <td>
  [ <a class="link" href="javascript:show('categorizator');">show categories</a>

  | <a class="link" href="javascript:hide('categorizator');">hide categories</a> ]
  <div id="categorizator" style="display:none;">
     <select name="cat_categories[]" multiple="multiple" size="5">
       <option value="1" >English</option>
       <option value="2" >English::Mark Twain</option>
       <option value="3" >English::Mark Twain::Tom Sawyer</option>

      </select><br />
   <label for="cat-check">categorize this object:</label>
    <input type="checkbox" name="cat_categorize" id="cat-check" /><br />
        <a href="tiki-admin_categories.php" class="link">Admin categories</a>
    </div>
  </td>
</tr>

<h2>{tr}Import questions from text{/tr}</h2>
<form enctype="multipart/form-data" method="post" action="tiki-quiz_edit.php?quizId={$quiz_info.quizId}">
  <table class="normal">
    <tr>
      <td class="formcolor" colspan=2>{tr}Instructions: Type, or paste, your multiple choice questions below.  One line for the question, then start answer choices on subsequent lines.  Seperate additional questions with a blank line.  Indicatate correct answers by starting them a "*" (without the quotes) character.{/tr}</td>
    </tr>

    <tr>
      <td class="formcolor">
        {tr}Input{/tr}
      </td>
      <td class="formcolor">
        <textarea class="wikiedit" name="input_data" rows="30" cols="80" id='subheading' wrap="virtual" ></textarea>
      </td>
    </tr>
  </table>
  <div align="center">
    <input type="submit" class="wikiaction" name="import" value="Import" />
  </div>
</form>

<h2>{tr}Questions{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-edit_quiz_questions.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="quizId" value="{$quizId|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-quiz_edit.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'questionId_desc'}questionId_asc{else}questionId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-quiz_edit.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'position_desc'}position_asc{else}position_desc{/if}">{tr}position{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-quiz_edit.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'question_desc'}question_asc{else}question_desc{/if}">{tr}question{/tr}</a></td>
<td class="heading">{tr}options{/tr}</td>
<td class="heading">{tr}maxScore{/tr}</td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$channels}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$channels[user].questionId}</td>
<td class="odd">{$channels[user].position}</td>
<td class="odd">{$channels[user].question}</td>
<td class="odd">{$channels[user].options}</td>
<td class="odd">{$channels[user].maxPoints}</td>
<td class="odd">
   <a class="link" href="tiki-quiz_edit.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].questionId}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-quiz_edit.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;questionId={$channels[user].questionId}">{tr}edit{/tr}</a>
   <a class="link" href="tiki-quiz_edit.php?quizId={$quizId}&amp;questionId={$channels[user].questionId}">{tr}options{/tr}</a>
</td>
</tr>
{else}
<tr>
<td class="even">{$channels[user].questionId}</td>
<td class="even">{$channels[user].position}</td>
<td class="even">{$channels[user].question}</td>
<td class="even">{$channels[user].options}</td>
<td class="even">{$channels[user].maxPoints}</td>
<td class="even">
   <a class="link" href="tiki-quiz_edit.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].questionId}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-quiz_edit.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;questionId={$channels[user].questionId}">{tr}edit{/tr}</a>
   <a class="link" href="tiki-edit_question_options.php?quizId={$quizId}&amp;questionId={$channels[user].questionId}">{tr}options{/tr}</a>
</td>
</tr>
{/if}
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-quiz_edit.php?quizId={$quizId}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-quiz_edit.php?quizId={$quizId}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-quiz_edit.php?quizId={$quizId}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

<!- tiki-quiz_edit end ->
