{title help="Quiz"}{tr}Stats for quiz:{/tr} {$quiz_info.name|escape}{/title}

<div class="navbar">
	{button href="tiki-list_quizzes.php" _text="{tr}List Quizzes{/tr}"} 
	{button href="tiki-quiz_stats.php" _text="{tr}Quiz Stats{/tr}"}
	{button href="tiki-quiz_stats_quiz.php?quizId=$quizId" _text="{tr}This Quiz Stats{/tr}"}
	{button href="tiki-edit_quiz.php?quizId=$quizId" _text="{tr}Edit this Quiz{/tr}"} 
	{if $tiki_p_admin_quizzes eq 'y'}
		{button href="tiki-quiz_stats_quiz.php?quizId=$quizId&clear=$quizId" _text="{tr}Clear Stats{/tr}"}
	{/if}
	{button href="tiki-edit_quiz.php" _text="{tr}Admin Quizzes{/tr}"}
</div>

<!-- end link buttons -->

<h2>{tr}Quiz stats{/tr}</h2>

<!-- begin table for stats data -->

<table class="normal">
<tr>
<th>
<!-- sort user -->
<a href="tiki-quiz_stats_quiz.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a>
</th>
{*
Set the names of the table headings to reflect the names of the db
*}
<!-- sort date -->
<th><a href="tiki-quiz_stats_quiz.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'timestamp_desc'}timestamp_asc{else}timestamp_desc{/if}">{tr}Date{/tr}</a></th>
<!-- sort time taken -->
<th>
<a href="tiki-quiz_stats_quiz.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'timeTaken_desc'}timeTaken_asc{else}timeTaken_desc{/if}">{tr}time taken{/tr}</a></th>
<!-- sort points-->
<th>
<a href="tiki-quiz_stats_quiz.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'points_desc'}points_asc{else}points_desc{/if}">{tr}points{/tr}</a></th>
<th>
<!-- sort results -->
<a href="tiki-quiz_stats_quiz.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'resultId_desc'}resultId_asc{else}resultId_desc{/if}">{tr}Result{/tr}</a></th>
<th>{tr}P/F{/tr}</th>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr class="{cycle}">
  <td class="username">{$channels[user].user|userlink}</td>
  <td class="date">{$channels[user].timestamp|tiki_short_datetime}</td>
  <td class="date">{$channels[user].timeTaken} secs</td>
  <td class="integer">{$channels[user].points} ({$channels[user].avgavg|string_format:"%.2f"}%)</td>
  <td class="action">
    {if $tiki_p_view_user_results eq 'y'}
      <a class="link" href="tiki-quiz_result_stats.php?quizId={$quizId}&amp;resultId={$channels[user].resultId}&amp;userResultId={$channels[user].userResultId}">{icon _id='application_form_magnify' alt="{tr}Results{/tr}" title="{tr}Results{/tr}"}</a>
      {if $channels[user].hasDetails eq 'y'}({tr}Details{/tr}){/if}
    {/if}
    
    {if $tiki_p_admin_quizzes eq 'y'}
      <a class="link" href="tiki-quiz_stats_quiz.php?quizId={$quizId}&amp;remove={$channels[user].userResultId}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
    {/if}
  </td>
  <td>{if $channels[user].ispassing}{tr}Passed{/tr}{else}{tr}Failed{/tr}{/if}</td>
</tr>
{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}

<!-- begin second table -->
<h2>{tr}Stats for this quiz Questions {/tr}</h2>

{*first section beginning *}
{section name=ix loop=$questions}
{tr}Question:{/tr}
<a class="link" href="tiki-edit_quiz_questions.php?quizId={$quizId.questionId}">{$questions[ix].question|escape}<br /></a>

<table class="normal">
<!-- begin header data for table -->

{* I'd like to have every table heading sorted for immediate analysis
<!-- sort options -->
<th>
<a href="tiki-quiz_stats_quiz.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'options_desc'}options_asc{else}options_desc{/if}">{tr}Options{/tr}</a>
</th>
*}

<tr>
	
	<th>{tr}Option{/tr}</th>
        <th>{tr}Votes{/tr}</th>
   	<th>{tr}Average{/tr}</th>
</tr>
<!-- begin looping of data from data base -->
{*second section beginning *}
  {section name=jx loop=$questions[ix].options}
  <tr class="odd">
    <td class="text">{$questions[ix].options[jx].optionText|escape}</td>
    <td class="text">{$questions[ix].options[jx].votes}</td>
    <td class="integer">{$questions[ix].options[jx].avg|string_format:"%.2f"}%</td>
  </tr>
{*second section end *}
  {/section}
</table>

<br />
{*first section end *}
{/section}
