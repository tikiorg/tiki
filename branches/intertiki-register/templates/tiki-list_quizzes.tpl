{* $Id$ *}
<h1><a class="pagetitle" href="tiki-list_quizzes.php">{tr}Quizzes{/tr}</a>
{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Quiz" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Quizzes{/tr}">
<img border='0' src='img/icons/help.gif' alt="{tr}Help{/tr}" />
</a>
{/if}
{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-list_quizzes.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}List Quizzes Tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}Edit Tpl{/tr}' /></a>{/if}</h1>

{if $tiki_p_view_quiz_stats eq 'y'}
<div class="navbar">
<a class="linkbut" href="tiki-quiz_stats.php">{tr}Quiz Stats{/tr}</a>
</div>
{if $channels or ($find ne '')}
<table class="findtable">
<tr>
<td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-list_quizzes.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="quizId" value="{$quizId|escape}" />
   </form>
   </td>
</tr>
</table>
{/if}
{/if}

<table class="normal">
<tr>
<td class="heading">
<a class="tableheading" href="tiki-list_quizzes.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a>
</td>
<td class="heading">
<a class="tableheading" href="tiki-list_quizzes.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a>
</td>
<td class="heading">
<a class="tableheading" href="tiki-list_quizzes.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'timeLimit_desc'}timeLimit_asc{else}timeLimit_desc{/if}">{tr}timeLimit{/tr}</a>
</td>

{* 
Why doesn't sort by questions work as well? I'm getting weird errors*
//error message: 
Warning: mysql error: Unknown column 'questionsLimit' in 'order clause' in query:
select * from `tiki_quizzes` order by `questionsLimit` desc
in /var/www/html/tikiwiki/lib/tikidblib.php on line 133

Fatal error: Call to a member function on a non-object in /var/www/html/tikiwiki/lib/tikidblib.php on line 151
// code
<td class="heading">
<a class="tableheading" href="tiki-list_quizzes.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'questions_desc'}questions_asc{else}questionsLimit_desc{/if}">{tr}Questions{/tr}</a>
*}
<!-- the question heading won't sort -->
<td class="heading">{tr}Questions{/tr}</td>

</tr>
{section name=user loop=$channels}
{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_take_quiz eq 'y') or ($channels[user].individual_tiki_p_take_quiz eq 'y')}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">
<a class="tablename" href="tiki-take_quiz.php?quizId={$channels[user].quizId}">{$channels[user].name}</a>
{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_admin_quizzes eq 'y') or ($channels[user].individual_tiki_p_admin_quizzes eq 'y')} (<a class="link" href="tiki-edit_quiz.php?quizId={$channels[user].quizId}">
<small>adm</small>
</a>){/if}
{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_view_quiz_stats eq 'y') or ($channels[user].individual_tiki_p_view_quiz_stats eq 'y')} (<a class="link" href="tiki-quiz_stats_quiz.php?quizId={$channels[user].quizId}">
<small>stats</small></a>){/if}
</td>
<td class="odd">{$channels[user].description}</td>
<td class="odd">{$channels[user].timeLimited} {if $channels[user].timeLimited eq 'y'}({$channels[user].timeLimit} mins){/if}</td>
<td class="odd">{$channels[user].questions}</td>
</tr>
{else}
<tr>
<td class="even"><a class="tablename" href="tiki-take_quiz.php?quizId={$channels[user].quizId}">{$channels[user].name}</a>
{*
here's the bit on the adm and stats, it would be nice if there was a direct link to edit here
*}
{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_admin_quizzes eq 'y') or ($channels[user].individual_tiki_p_admin_quizzes eq 'y')} (<a class="link" href="tiki-edit_quiz.php?quizId={$channels[user].quizId}"><small>adm</small></a>){/if}
{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_view_quiz_stats eq 'y') or ($channels[user].individual_tiki_p_view_quiz_stats eq 'y')} (<a class="link" href="tiki-quiz_stats_quiz.php?quizId={$channels[user].quizId}"><small>stats</small></a>){/if}
</td>
<td class="even">{$channels[user].description}</td>
<td class="even">{$channels[user].timeLimited} {if $channels[user].timeLimited eq 'y'}({$channels[user].timeLimit} mins){/if}</td>
<td class="even">{$channels[user].questions}</td>
</tr>
{/if}
{/if}
{sectionelse}
<tr><td class="odd" colspan="4">{tr}No records.{/tr}</td></tr>
{/section}
</table>

<!-- the next/ prev  -->
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-list_quizzes.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-list_quizzes.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-list_quizzes.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
