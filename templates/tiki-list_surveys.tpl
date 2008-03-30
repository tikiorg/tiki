{* $Id$ *}
<h1><a class="pagetitle" href="tiki-list_surveys.php">{tr}Surveys{/tr}</a>
{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Surveys" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Edit Quiz Questions{/tr}">
<img border='0' src='img/icons/help.gif' alt="{tr}Help{/tr}" /></a>{/if}
{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-list_surveys.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Edit Quiz Stats Tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}Edit Tpl{/tr}' />
</a>
{/if}</h1>

{if $tiki_p_view_survey_stats eq 'y'}
<div class="navbar"><span class="button2"><a class="linkbut" href="tiki-survey_stats.php">{tr}Survey stats{/tr}</a></span></div>
{/if}

<table class="normal">
<tr>
<td class="heading">
<a class="tableheading" href="tiki-list_surveys.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_surveys.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a></td>
<td class="heading">{tr}Questions{/tr}</td>
<td class="heading">{tr}Actions{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_take_survey eq 'y') or ($channels[user].individual_tiki_p_take_survey eq 'y')}
<tr>
<td class="{cycle advance=false}">
{if ($tiki_p_admin_surveys eq 'y') or ($channels[user].status eq 'o' and $channels[user].taken_survey eq 'n')}
<a class="tablename" href="tiki-take_survey.php?surveyId={$channels[user].surveyId}">
{else}
<a class="link" href="tiki-survey_stats_survey.php?surveyId={$channels[user].surveyId}">
{/if}
{$channels[user].name}</a>
</td>
<td class="{cycle advance=false}">{$channels[user].description}</td>
<td style="text-align:right;"  class="{cycle advance=false}">{$channels[user].questions}</td>

<td style="text-align:right;"  class="{cycle}">
{if ($tiki_p_admin_surveys eq 'y') or ($channels[user].status eq 'o' and $channels[user].taken_survey eq 'n')}
<a href="tiki-take_survey.php?surveyId={$channels[user].surveyId}">
<img border='0' title='{tr}Take Survey{/tr}' alt='{tr}Take Survey{/tr}' width="16" height="16" hspace="6" vspace="0" src='img/icons/toright.gif' /></a>
{/if}

{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_view_survey_stats eq 'y') or ($channels[user].individual_tiki_p_view_survey_stats eq 'y')}
<a href="tiki-survey_stats_survey.php?surveyId={$channels[user].surveyId}">
<img border='0' title='{tr}Stats{/tr}' alt='{tr}Stats{/tr}' width="16" height="16" hspace="3" vspace="0" src='img/icons/zoom.gif' /></a>{/if}

{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_admin_surveys eq 'y') or ($channels[user].individual_tiki_p_admin_surveys eq 'y')}
<a href="tiki-admin_surveys.php?surveyId={$channels[user].surveyId}">
<img border='0' title='{tr}Admin{/tr}' alt='{tr}Admin{/tr}' width="16" height="16" hspace="3" vspace="0" src='img/icons/config.gif' /></a>{/if}
</td>
</tr>
{/if}
{sectionelse}
<tr><td class="odd" colspan="4">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<! -- the page advance... it's set to ten by default in maxRecords... -->

<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-list_surveys.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-list_surveys.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-list_surveys.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
