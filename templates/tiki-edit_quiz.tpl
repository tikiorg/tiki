<a class="pagetitle" href="tiki-edit_quiz.php">{tr}Admin quizzes{/tr}</a>

<!-- the help link info -->
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=Quizzes" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Quizzes{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}

<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-edit_quiz.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin quizzes tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='edit tpl' /></a>{/if}

<!-- beginning of next bit -->




<br /><br />
<a class="linkbut" href="tiki-list_quizzes.php">{tr}list quizzes{/tr}</a>
<a class="linkbut" href="tiki-quiz_stats.php">{tr}quiz stats{/tr}</a>
<a class="linkbut" href="tiki-edit_quiz.php">{tr}admin quizzes{/tr}</a><br /><br />
<h2>{tr}Create/edit quizzes{/tr}</h2>
{if $individual eq 'y'}
<a class="link" href="tiki-objectpermissions.php?objectName=quiz%20{$name}&amp;objectType=quiz&amp;permType=quizzes&amp;objectId={$quizId}">{tr}There are individual permissions set for this quiz{/tr}</a><br /><br />
{/if}
<form action="tiki-edit_quiz.php" method="post">
<input type="hidden" name="quizId" value="{$quizId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="name" value="{$name|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><textarea name="description" rows="4" cols="40">{$description|escape}</textarea></td></tr>
{include file=categorize.tpl}
<tr><td class="formcolor">{tr}Quiz can be repeated{/tr}</td><td class="formcolor"><input type="checkbox" name="canRepeat" {if $canRepeat eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Store quiz results{/tr}</td><td class="formcolor"><input type="checkbox" name="storeResults" {if $storeResults eq 'y'}checked="checked"{/if} /></td></tr>
<!--<tr><td class="formcolor">{tr}Questions per page{/tr}</td><td class="formcolor"><select name="questionsPerPage">{html_options values=$qpp selected=$questionsPerPage output=$qpp}</select></td></tr>-->
<tr><td class="formcolor">{tr}Quiz is time limited{/tr}</td><td class="formcolor"><input type="checkbox" name="timeLimited" {if $timeLimited eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Maximum time{/tr}</td><td class="formcolor"><select name="timeLimit">{html_options values=$mins selected=$timeLimit output=$mins}</select> {tr}minutes{/tr}</td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<h2>{tr}quizzes{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-edit_quiz.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'quizId_desc'}quizId_asc{else}quizId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}description{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'canRepeat_desc'}canRepeat_asc{else}canRepeat_desc{/if}">{tr}canRepeat{/tr}</a></td>
<td class="heading">{tr}timeLimit{/tr}</td>
<td class="heading">{tr}questions{/tr}</td>
<td class="heading">{tr}results{/tr}</td>
<td class="heading">{tr}action{/tr}</td>
</tr>
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
   <a class="link" href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].quizId}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;quizId={$channels[user].quizId}">{tr}edit{/tr}</a>
   <a class="link" href="tiki-edit_quiz_questions.php?quizId={$channels[user].quizId}">{tr}questions{/tr}</a>
   <a class="link" href="tiki-edit_quiz_results.php?quizId={$channels[user].quizId}">{tr}results{/tr}</a>
   {if $channels[user].individual eq 'y'}({/if}<a class="link" href="tiki-objectpermissions.php?objectName=Quiz%20{$channels[user].name}&amp;objectType=quiz&amp;permType=quizzes&amp;objectId={$channels[user].quizId}">{tr}perms{/tr}</a>{if $channels[user].individual eq 'y'}){/if}
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
   <a class="link" href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].quizId}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;quizId={$channels[user].quizId}">{tr}edit{/tr}</a>
   <a class="link" href="tiki-edit_quiz_questions.php?quizId={$channels[user].quizId}">{tr}questions{/tr}</a>
   <a class="link" href="tiki-edit_quiz_results.php?quizId={$channels[user].quizId}">{tr}results{/tr}</a>
   {if $channels[user].individual eq 'y'}({/if}<a class="link" href="tiki-objectpermissions.php?objectName=Quiz%20{$channels[user].name}&amp;objectType=quiz&amp;permType=quizzes&amp;objectId={$channels[user].quizId}">{tr}perms{/tr}</a>{if $channels[user].individual eq 'y'}){/if}
</td>
</tr>
{/if}
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-edit_quiz.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-edit_quiz.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-edit_quiz.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

