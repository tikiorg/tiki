<a class="pagetitle" href="tiki-admin_surveys.php">{tr}Admin surveys{/tr}</a><br /><br />
[<a class="link" href="tiki-list_surveys.php">{tr}list surveys{/tr}</a>
|<a class="link" href="tiki-survey_stats.php">{tr}survey stats{/tr}</a>
]<br /><br />
{if $info.surveyId > 0}
<h2>{tr}Edit this Survey:{/tr} {$info.name}</h2>
<a href="tiki-admin_surveys.php">Create new survey</a>
{else}
<h2>{tr}Create New Survey:{/tr}</h2>
{/if}
{if $individual eq 'y'}
<a class="link" href="tiki-objectpermissions.php?objectName=Survey%20{$info.name}&amp;objectType=survey&amp;permType=surveys&amp;objectId={$info.surveyId}">{tr}There are individual permissions set for this survey{/tr}</a><br /><br />
{/if}
<form action="tiki-admin_surveys.php" method="post">
<input type="hidden" name="surveyId" value="{$info.surveyId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="name" value="{$info.name|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><textarea name="description" rows="4" cols="40">{$info.description|escape}</textarea></td></tr>
{include file=categorize.tpl}
<tr><td class="formcolor">{tr}Status{/tr}</td><td class="formcolor">
<select name="status">
<option value="o" {if $info.status eq 'o'}selected='selected'{/if}>{tr}open{/tr}</option>
<option value="c" {if $info.status eq 'c'}selected='selected'{/if}>{tr}closed{/tr}</option>
</select>
</td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<h2>{tr}Surveys{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_surveys.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td width="2%" class="heading"><a class="tableheading" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'surveyId_desc'}surveyId_asc{else}surveyId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}description{/tr}</a></td>
<td width="2%" class="heading"><a class="tableheading" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'status_desc'}status_asc{else}status_desc{/if}">{tr}stat{/tr}</a></td>
<td style="text-align:right;" width="3%" class="heading">{tr}questions{/tr}</td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].surveyId}</td>
<td class="{cycle advance=false}">{$channels[user].name}</td>
<td class="{cycle advance=false}">{$channels[user].description}</td>
<td style="text-align:center;" class="{cycle advance=false}">
{if $channels[user].status eq 'o'}
	<img src='img/icons/ofo.gif' alt='{tr}open{/tr}' />
{else}
	<img src='img/icons/fo.gif' alt='{tr}closed{/tr}' />
{/if}
</td>
<td style="text-align:right;" width="3%" class="{cycle advance=false}">{$channels[user].questions}</td>
<td width="16%" class="{cycle}">
   <a class="link" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;surveyId={$channels[user].surveyId}"><img src='img/icons/config.gif' border='0' alt='{tr}edit{/tr}' title='{tr}edit{/tr}' /></a>
   <a class="link" href="tiki-admin_survey_questions.php?surveyId={$channels[user].surveyId}"><img src='img/icons/question.gif' alt='{tr}question{/tr}' border='0' title='{tr}questions{/tr}' /></a>
   {if $channels[user].individual eq 'y'}({/if}<a class="link" href="tiki-objectpermissions.php?objectName=Survey%20{$channels[user].name}&amp;objectType=survey&amp;permType=surveys&amp;objectId={$channels[user].surveyId}"><img src='img/icons/key.gif' alt='{tr}perms{/tr}' border='0' title='{tr}perms{/tr}' /></a>{if $channels[user].individual eq 'y'}){/if}
   <a class="link" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].surveyId}"><img src='img/icons2/delete.gif' alt='{tr}remove{/tr}' title='{tr}remove{/tr}' border='0' /></a>
</td>
</tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_surveys.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_surveys.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admin_surveys.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

