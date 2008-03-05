{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-edit_quiz_questions.tpl,v 1.30.2.2 2008-03-05 23:33:05 marclaporte Exp $ *}

{* Copyright (c) 2004 *}
{* All Rights Reserved. See copyright.txt for details and a complete list of authors. *}
{* Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details. *}

<!- tiki-edit_quiz_questions.tpl start ->

<h1><a class="pagetitle" href="tiki-edit_quiz_questions.php?quizId={$quizId}">{tr}Edit quiz questions{/tr}</a>
<! -- the help link info -->
  
      {if $prefs.feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=QuizzesDoc#id141161" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Edit Quiz Questions{/tr}"><img border='0' src='img/icons/help.gif' alt="{tr}Help{/tr}" /></a>{/if}

<! -- link to tpl -->

     {if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=/tiki-edit_quiz_questions.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Edit Quiz Questions Tpl{/tr}"><img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}Edit Tpl{/tr}' /></a>
{/if}



</h1>

<! -- beginning of link buttons -->
<a class="linkbut" href="tiki-list_quizzes.php">{tr}List Quizzes{/tr}</a>
<a class="linkbut" href="tiki-quiz_stats.php">{tr}Quiz Stats{/tr}</a>
<a class="linkbut" href="tiki-quiz_stats_quiz.php?quizId={$quizId}">{tr}this quiz stats{/tr}</a>
<a class="linkbut" href="tiki-edit_quiz.php?quizId={$quizId}">{tr}Edit this Quiz{/tr}</a>
<a class="linkbut" href="tiki-edit_quiz.php">{tr}Admin Quizzes{/tr}</a>
<br /><br />

<h2>{tr}Create/edit questions for quiz{/tr}: <a href="tiki-edit_quiz.php?quizId={$quiz_info.quizId}" >{$quiz_info.name}</a></h2>
<form action="tiki-edit_quiz_questions.php" method="post">
<input type="hidden" name="quizId" value="{$quizId|escape}" />
<input type="hidden" name="questionId" value="{$questionId|escape}" />

<table class="normal">
<tr>
<td class="formcolor">{tr}Question{/tr}:</td>
{* change the size of the text field here *}
<td class="formcolor">
<textarea name="question" rows="5" cols="80">{$question|escape}</textarea>
</td>
</tr>
<tr><td class="formcolor">{tr}Position{/tr}:</td><td class="formcolor"><select name="position">{html_options values=$positions output=$positions selected=$position}</select>
</td>
</tr>

<tr><td class="formcolor">{tr}Question Type{/tr}:</td><td class="formcolor"><select name="questionType">{html_options options=$questionTypes selected=$type}</select>
</td>
</tr>


<tr><td  class="formcolor">&nbsp;</td>
<td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<! -- begin area to import questions en masse -- >
<h2>{tr}Import questions from text{/tr}
 {if $prefs.feature_help eq 'y'}
<! -- help link data -- >
  <a href="{$prefs.helpurl}Quiz+Question+Import" target="tikihelp" class="tikihelp">
  <img border="0" src="img/icons/help.gif" alt="{tr}Help{/tr}" /></a>
 {/if}
</h2>

<! -- begin form area for importing questions  -- >
<form enctype="multipart/form-data" method="post" action="tiki-edit_quiz_questions.php?quizId={$quiz_info.quizId}">
  <table class="normal">
    <tr>
      <td class="formcolor" colspan=2>{tr}Instructions: Type, or paste, your multiple choice questions below.  One line for the question, then start answer choices on subsequent lines.  Separate additional questions with a blank line.  Indicate correct answers by starting them with a "*" (without the quotes) character.{/tr}</td>
    </tr>

    <tr>
      <td class="formcolor">
        {tr}Input{/tr}
      </td>
      <td class="formcolor">
        <textarea class="wikiedit" name="input_data" rows="30" cols="80" id='subheading' wrap="virtual" >
</textarea>
      </td>
    </tr>
  </table>
  <div align="center">
    <input type="submit" class="wikiaction" name="import" value="Import" />
  </div>
</form>

<! -- begin form for searching questions --->
<h2>{tr}Questions{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-edit_quiz_questions.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="quizId" value="{$quizId|escape}" />
   </form>
   </td>
</tr>
</table>


<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'questionId_desc'}questionId_asc{else}questionId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'position_desc'}position_asc{else}position_desc{/if}">{tr}Position{/tr}</a></td>
<td class="heading">

{* how come these questions are sortable and the others are not? *}
<a class="tableheading" href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'question_desc'}question_asc{else}question_desc{/if}">{tr}question{/tr}</a>
</td>

{* how come the options are not sortable? 
<td class="heading"><a class="tableheading" href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'options_desc'}options_asc{else}options_desc{/if}">{tr}Options{/tr}</a>
</td>
*}

<td class="heading">{tr}Options{/tr}</td>

{* these need to be sortable so as to do quick cks for bogus scores... TAs and teachers make mistakes
<td class="heading"><a class="tableheading" href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'maxScore_desc'}maxScore_asc{else}maxScore_desc{/if}">{tr}maxScore{/tr}</a>
</td>
*}

<td class="heading">{tr}maxScore{/tr}</td>


<td class="heading">{tr}Action{/tr}</td>
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
   <a class="link" href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].questionId}">{tr}Remove{/tr}</a>
   <a class="link" href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;questionId={$channels[user].questionId}">{tr}Edit{/tr}</a>
   <a class="link" href="tiki-edit_question_options.php?quizId={$quizId}&amp;questionId={$channels[user].questionId}">{tr}Options{/tr}</a>
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
   <a class="link" href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].questionId}">{tr}Remove{/tr}</a>
   <a class="link" href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;questionId={$channels[user].questionId}">{tr}Edit{/tr}</a>
   <a class="link" href="tiki-edit_question_options.php?quizId={$quizId}&amp;questionId={$channels[user].questionId}">{tr}Options{/tr}</a>
</td>
</tr>
{/if}
{/section}
</table>

<! -- this is the page advance part -->
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

<!- tiki-edit_quiz_questions.tpl end ->
