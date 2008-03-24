{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-survey_stats.tpl,v 1.17.2.1 2007-10-17 20:36:11 niclone Exp $ *}
<h1><a class="pagetitle" href="tiki-survey_stats.php">{tr}Stats for surveys{/tr}</a></h1>

<div class="navbar">
<span class="button2"><a class="linkbut" href="tiki-list_surveys.php">{tr}List Surveys{/tr}</a></span>
<span class="button2"><a class="linkbut" href="tiki-survey_stats.php">{tr}Survey Stats{/tr}</a></span>
{if $tiki_p_admin_surveys eq 'y'}<span class="button2"><a class="linkbut" href="tiki-admin_surveys.php">{tr}Admin Surveys{/tr}</a></span>{/if}
</div>

<h2>{tr}Surveys{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-survey_stats.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="surveyId" value="{$surveyId|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-survey_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Survey{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-survey_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'taken_desc'}taken_asc{else}taken_desc{/if}">{tr}taken{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-survey_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-survey_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastTaken_desc'}lastTaken_asc{else}lastTaken_desc{/if}">{tr}Last taken{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_view_survey_stats eq 'y') or ($channels[user].individual_tiki_p_view_survey_stats eq 'y')}
<tr>
<td class="{cycle advance=false}"><a class="tablename" href="tiki-survey_stats_survey.php?surveyId={$channels[user].surveyId}">{$channels[user].name}</a></td>
<td class="{cycle advance=false}">{$channels[user].taken}</td>
<td class="{cycle advance=false}">{$channels[user].created|tiki_short_datetime}</td>
<td class="{cycle advance=false}">{$channels[user].lastTaken|tiki_short_datetime}</td>
</tr>
{/if}
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-survey_stats.php?surveyId={$surveyId}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-survey_stats.php?surveyId={$surveyId}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-survey_stats.php?surveyId={$surveyId}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

