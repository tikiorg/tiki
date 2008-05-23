<h1><a class="pagetitle" href="tiki-quiz_stats_quiz.php?quizId={$quizId}">{tr}Stats for quiz{/tr}:{$quiz_info.name}</a>


<! -- the help link info -->
  
      {if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Quiz" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Edit Quiz Questions{/tr}"><img border='0' src='img/icons/help.gif' alt="{tr}Help{/tr}" /></a>{/if}

<! -- link to tpl -->

     {if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=/tiki-quiz_stats_quiz.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Edit Quiz Stats Tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}Edit Tpl{/tr}' /></a>{/if}
<! -- linkbuttons, they'd be better if they had rollover info -->

</h1>
<a class="linkbut" href="tiki-list_quizzes.php">{tr}List Quizzes{/tr}</a>
<a class="linkbut" href="tiki-quiz_stats.php">{tr}Quiz Stats{/tr}</a>
<a class="linkbut" href="tiki-quiz_stats_quiz.php?quizId={$quizId}">{tr}this quiz stats{/tr}</a>
<a class="linkbut" href="tiki-edit_quiz.php?quizId={$quizId}">{tr}Edit this Quiz{/tr}</a>
{if $tiki_p_admin_quizzes eq 'y'}<a class="linkbut" href="tiki-quiz_stats_quiz.php?quizId={$quizId}&amp;clear={$quizId}">{tr}Clear Stats{/tr}</a>{/if}
<a class="linkbut" href="tiki-edit_quiz.php">{tr}Admin Quizzes{/tr}</a>
<br /><br />

<! -- end link buttons -- >

<h2>{tr}Quiz stats{/tr}</h2>
<div  align="center">

<! -- begin table for stats data -->

<table class="normal">
<tr>
<td class="heading">
<! -- sort user -->
<a class="tableheading" href="tiki-quiz_stats_quiz.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a>
</td>
{*
Set the names of the table headings to reflect the names of the db
*}
<! -- sort date -->
<td class="heading"><a class="tableheading" href="tiki-quiz_stats_quiz.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'timestamp_desc'}timestamp_asc{else}timestamp_desc{/if}">{tr}date{/tr}</a></td>
<! -- sort time taken -->
<td class="heading">
<a class="tableheading" href="tiki-quiz_stats_quiz.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'timeTaken_desc'}timeTaken_asc{else}timeTaken_desc{/if}">{tr}time taken{/tr}</a></td>
<! -- sort points-->
<td class="heading">
<a class="tableheading" href="tiki-quiz_stats_quiz.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'points_desc'}points_asc{else}points_desc{/if}">{tr}points{/tr}</a></td>
<td class="heading">
<! -- sort results -->
<a class="tableheading" href="tiki-quiz_stats_quiz.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'resultId_desc'}resultId_asc{else}resultId_desc{/if}">{tr}Result{/tr}</a></td>
<td class="heading">{tr}P/F{/tr}</td>
</tr>
{section name=user loop=$channels}
{if $smarty.section.user.index % 2}
<tr>
  <td class="odd">{$channels[user].user|userlink}</td>
  <td class="odd">{$channels[user].timestamp|tiki_short_datetime}</td>
  <td class="odd">{$channels[user].timeTaken} secs</td>
  <td class="odd">{$channels[user].points} ({$channels[user].avgavg|string_format:"%.2f"}%)</td>
  <td class="odd">
    {if $tiki_p_view_user_results eq 'y'}
      <a class="link" href="tiki-quiz_result_stats.php?quizId={$quizId}&amp;resultId={$channels[user].resultId}&amp;userResultId={$channels[user].userResultId}">{icon _id='application_form_magnify' alt='{tr}Results{/tr}' title='{tr}Results{/tr}'}</a>
      {if $channels[user].hasDetails eq 'y'}({tr}Details{/tr}){/if}
    {/if}
    
    {if $tiki_p_admin_quizzes eq 'y'}
      <a class="link" href="tiki-quiz_stats_quiz.php?quizId={$quizId}&amp;remove={$channels[user].userResultId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
    {/if}
  </td>
  <td class="odd">{if $channels[user].ispassing}{tr}Passed{/tr}{else}{tr}Failed{/tr}{/if}</td>
{else}
  <tr>
  <td class="even">{$channels[user].user|userlink}</td>
  <td class="even">{$channels[user].timestamp|tiki_short_datetime}</td>
  <td class="even">{$channels[user].timeTaken} secs</td>
  <td class="even">{$channels[user].points} ({$channels[user].avgavg|string_format:"%.2f"}%)</td>
  <td class="even">
    {if $tiki_p_view_user_results eq 'y'}
      <a class="link" href="tiki-quiz_result_stats.php?quizId={$quizId}&amp;resultId={$channels[user].resultId}&amp;userResultId={$channels[user].userResultId}">{icon _id='application_form_magnify' alt='{tr}Results{/tr}' title='{tr}Results{/tr}'}</a>
      {if $channels[user].hasDetails eq 'y'}({tr}Details{/tr}){/if}
    {/if}

    {if $tiki_p_admin_quizzes eq 'y'}
      <a class="link" href="tiki-quiz_stats_quiz.php?quizId={$quizId}&amp;remove={$channels[user].userResultId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>{/if}
  </td>
  <td class="odd">{if $channels[user].ispassing}{tr}Passed{/tr}{else}{tr}Failed{/tr}{/if}</td>
{/if}
</tr>
{/section}
</table>

<! -- this is the part for viewing the next 10 results -->
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-quiz_stats_quiz.php?quizId={$quizId}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-quiz_stats_quiz.php?quizId={$quizId}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<! -- really need to find how/ where to set the maxRecords to user control -->
<a class="prevnext" href="tiki-quiz_stats_quiz.php?quizId={$quizId}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

<! -- begin second table  -->
<h2>{tr}Stats for this quiz Questions {/tr}</h2>

{*first section beginning *}
{section name=ix loop=$questions} Question:
<a class="link" href="tiki-edit_quiz_questions.php?quizId={$quizId.questionId}">{$questions[ix].question}<br /></a>

<table class="normal">
<!-- begin header data for table-->

{* I'd like to have every table heading sorted for immediate analysis
<!-- sort options  -->
<td>
<a class="tableheading" href="tiki-quiz_stats_quiz.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'options_desc'}options_asc{else}options_desc{/if}">{tr}Options{/tr}</a>
</td>
*}

<tr>
	
	<td  class="heading">{tr}Option{/tr}</td>
        <td  class="heading">{tr}Votes{/tr}</td>
   	<td  class="heading">{tr}Average{/tr}</td>
</tr>
<!-- begin looping of data from data base-->
{*second section beginning *}
  {section name=jx loop=$questions[ix].options}
  <tr>
    <td class="odd">{$questions[ix].options[jx].optionText}</td>
    <td class="odd">{$questions[ix].options[jx].votes}</td>
    <td class="odd">{$questions[ix].options[jx].avg|string_format:"%.2f"}%</td>
  </tr>
{*second section end *}
  {/section}
</table>

<br />
{*first section end *}
{/section}
