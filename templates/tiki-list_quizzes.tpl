<a class="pagetitle" href="tiki-list_quizzes.php">{tr}Quizzes{/tr}</a><br /><br />
{if $tiki_p_view_quiz_stats eq 'y'}
<a class="linkbut" href="tiki-quiz_stats.php">{tr}quiz stats{/tr}</a><br /><br />
{/if}
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-list_quizzes.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_quizzes.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}description{/tr}</a></td>
<td class="heading">{tr}timeLimit{/tr}</td>
<td class="heading">{tr}questions{/tr}</td>
</tr>
{section name=user loop=$channels}
{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_take_quiz eq 'y') or ($channels[user].individual_tiki_p_take_quiz eq 'y')}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd"><a class="tablename" href="tiki-take_quiz.php?quizId={$channels[user].quizId}">{$channels[user].name}</a>
{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_admin_quizzes eq 'y') or ($channels[user].individual_tiki_p_admin_quizzes eq 'y')} (<a class="link" href="tiki-edit_quiz.php?quizId={$channels[user].quizId}"><small>adm</small></a>){/if}
{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_view_quiz_stats eq 'y') or ($channels[user].individual_tiki_p_view_quiz_stats eq 'y')} (<a class="link" href="tiki-quiz_stats_quiz.php?quizId={$channels[user].quizId}"><small>stats</small></a>){/if}
</td>
<td class="odd">{$channels[user].description}</td>
<td class="odd">{$channels[user].timeLimited} {if $channels[user].timeLimited eq 'y'}({$channels[user].timeLimit} mins){/if}</td>
<td class="odd">{$channels[user].questions}</td>
</tr>
{else}
<tr>
<td class="even"><a class="tablename" href="tiki-take_quiz.php?quizId={$channels[user].quizId}">{$channels[user].name}</a>
{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_admin_quizzes eq 'y') or ($channels[user].individual_tiki_p_admin_quizzes eq 'y')} (<a class="link" href="tiki-edit_quiz.php?quidIz={$channels[user].quizId}"><small>adm</small></a>){/if}
{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_view_quiz_stats eq 'y') or ($channels[user].individual_tiki_p_view_quiz_stats eq 'y')} (<a class="link" href="tiki-quiz_stats_quiz.php?quizId={$channels[user].quizId}"><small>stats</small></a>){/if}
</td>
<td class="even">{$channels[user].description}</td>
<td class="even">{$channels[user].timeLimited} {if $channels[user].timeLimited eq 'y'}({$channels[user].timeLimit} mins){/if}</td>
<td class="even">{$channels[user].questions}</td>
</tr>
{/if}
{/if}
{/section}
</table>
<br />
<div align="center">
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-list_quizzes.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-list_quizzes.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-list_quizzes.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
