<a class="pagetitle" href="tiki-quiz_stats_quiz.php?quizId={$quizId}">{tr}Stats for quiz{/tr}:{$quiz_info.name}</a><br /><br />
[<a class="link" href="tiki-list_quizzes.php">{tr}list quizzes{/tr}</a>
|<a class="link" href="tiki-quiz_stats.php">{tr}quiz stats{/tr}</a>
|<a class="link" href="tiki-quiz_stats_quiz.php?quizId={$quizId}">{tr}this quiz stats{/tr}</a>
|<a class="link" href="tiki-edit_quiz.php?quizId={$quizId}">{tr}edit this quiz{/tr}</a>
{if $tiki_p_admin_quizzes eq 'y'}|<a class="link" href="tiki-quiz_stats_quiz.php?quizId={$quizId}&amp;clear={$quizId}">{tr}clear stats{/tr}</a>{/if}
|<a class="link" href="tiki-edit_quiz.php">{tr}admin quizzes{/tr}</a>]<br /><br />
<h2>{tr}Quiz stats{/tr}</h2>
<div  align="center">
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-quiz_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-quiz_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'timestamp_desc'}timestamp_asc{else}timestamp_desc{/if}">{tr}date{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-quiz_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'timeTaken_desc'}timeTaken_asc{else}timeTaken_desc{/if}">{tr}time{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-quiz_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'points_desc'}points_asc{else}points_desc{/if}">{tr}score{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-quiz_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'resultId_desc'}resultId_asc{else}resultId_desc{/if}">{tr}result{/tr}</a></td>
</tr>
{section name=user loop=$channels}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$channels[user].user}</td>
<td class="odd">{$channels[user].timestamp|tiki_short_datetime}</td>
<td class="odd">{$channels[user].timeTaken} secs</td>
<td class="odd">{$channels[user].points} ({$channels[user].avgavg|string_format:"%.2f"}%)</td>
<td class="odd">{if $tiki_p_view_user_results eq 'y'}<a class="link" href="tiki-quiz_result_stats.php?quizId={$quizId}&amp;resultId={$channels[user].resultId}&amp;userResultId={$channels[user].userResultId}">{tr}result{/tr}</a>
{if $channels[user].hasDetails eq 'y'}({tr}details{/tr}){/if}{/if}
{if $tiki_p_admin_quizzes eq 'y'}<a class="link" href="tiki-quiz_stats_quiz.php?quizId={$quizId}&amp;remove={$channels[user].userResultId}">{tr}del{/tr}</a>{/if}
</td>
</tr>
{else}
<tr>
<td class="even">{$channels[user].user}</td>
<td class="even">{$channels[user].timestamp|tiki_short_datetime}</td>
<td class="even">{$channels[user].timeTaken} secs</td>
<td class="even">{$channels[user].points} ({$channels[user].avgavg|string_format:"%.2f"}%)</td>
<td class="even">{if $tiki_p_view_user_results eq 'y'}<a class="link" href="tiki-quiz_result_stats.php?quizId={$quizId}&amp;resultId={$channels[user].resultId}&amp;userResultId={$channels[user].userResultId}">{tr}result{/tr}</a>
{if $channels[user].hasDetails eq 'y'}({tr}details{/tr}){/if}{/if}
{if $tiki_p_admin_quizzes eq 'y'}<a class="link" href="tiki-quiz_stats_quiz.php?quizId={$quizId}&amp;remove={$channels[user].userResultId}">{tr}del{/tr}</a>{/if}
</td>
</tr>
{/if}
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-quiz_stats.php?quizId={$quizId}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-quiz_stats.php?quizId={$quizId}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-quiz_stats.php?quizId={$quizId}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

<h2>{tr}Stats for this quiz Questions {/tr}</h2>
{section name=ix loop=$questions}
  Q: {$questions[ix].question}<br />
  <table class="normal">
  <tr>
   <td width="70%" class="heading">{tr}Option{/tr}</td>
   <td width="30%" class="heading">{tr}Votes{/tr}</td>
   <td width="30%" class="heading">{tr}Average{/tr}</td>
  </tr>
  {section name=jx loop=$questions[ix].options}
  <tr>
    <td class="odd">{$questions[ix].options[jx].optionText}</td>
    <td class="odd">{$questions[ix].options[jx].votes}</td>
    <td class="odd">{$questions[ix].options[jx].avg|string_format:"%.2f"}%</td>
  </tr>
  {/section}
</table><br />
{/section}
