<h1><a class="pagetitle" href="tiki-list_surveys.php">{tr}Surveys{/tr}</a>

<! -- the help link info -->

      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=SurveysDoc" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}edit quiz questions{/tr}">
<img border='0' src='img/icons/help.gif' alt="{tr}help{/tr}" /></a>{/if}

<! -- link to tpl -->

     {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-list_surveys.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}edit quiz stats tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}edit tpl{/tr}' />
</a>
{/if}</h1>


<! -- link buttons -->

{if $tiki_p_view_survey_stats eq 'y'}
<span class="button2"><a class="linkbut" href="tiki-survey_stats.php">{tr}Survey stats{/tr}</a></span><br /><br />
{/if}
<table class="normal">
<tr>
<td class="heading">
<a class="tableheading" href="tiki-list_surveys.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_surveys.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}description{/tr}</a></td>
<td class="heading">{tr}questions{/tr}</td>
<td class="heading">{tr}actions{/tr}</td>
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
<img border='0' title='{tr}take survey{/tr}' alt='{tr}take survey{/tr}' width="16" height="16" hspace="6" vspace="0" src='img/icons/toright.gif' /></a>
{/if}

{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_view_survey_stats eq 'y') or ($channels[user].individual_tiki_p_view_survey_stats eq 'y')}
<a href="tiki-survey_stats_survey.php?surveyId={$channels[user].surveyId}">
<img border='0' title='{tr}stats{/tr}' alt='{tr}stats{/tr}' width="16" height="16" hspace="3" vspace="0" src='img/icons/zoom.gif' /></a>{/if}

{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_admin_surveys eq 'y') or ($channels[user].individual_tiki_p_admin_surveys eq 'y')}
<a href="tiki-admin_surveys.php?surveyId={$channels[user].surveyId}">
<img border='0' title='{tr}adm{/tr}' alt='{tr}adm{/tr}' width="16" height="16" hspace="3" vspace="0" src='img/icons/config.gif' /></a>{/if}
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
<div align="center" class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-list_surveys.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-list_surveys.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-list_surveys.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
