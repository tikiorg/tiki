<a class="pagetitle" href="tiki-admin_surveys.php">{tr}Admin surveys{/tr}</a>

<!-- the help link info -->
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=Surveys" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Surveys{/tr}">
<img border="0" src="img/icons/help.gif" alt="{tr}help{/tr}" /></a>{/if}

<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admin_surveys.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}administer surveys template{/tr}">
<img border="0" src="img/icons/info.gif" alt="{tr}edit{/tr}" /></a>{/if}

<!-- beginning of next bit -->









<br /><br />
<span class="button2"><a class="linkbut" href="tiki-list_surveys.php">{tr}list surveys{/tr}</a></span>
<span class="button2"><a class="linkbut" href="tiki-survey_stats.php">{tr}survey stats{/tr}</a></span>
<br /><br />
{if $info.surveyId > 0}
<h2>{tr}Edit this Survey:{/tr} {$info.name}</h2>
<a href="tiki-admin_surveys.php">Create new survey</a>
{else}
<h2>{tr}Create New Survey{/tr}:</h2>
{/if}
{if $individual eq 'y'}
<a class="link" href="tiki-objectpermissions.php?objectName=Survey%20{$info.name}&amp;objectType=survey&amp;permType=surveys&amp;objectId={$info.surveyId}">{tr}There are individual permissions set for this survey{/tr}</a><br /><br />
{/if}
<form action="tiki-admin_surveys.php" method="post">
<input type="hidden" name="surveyId" value="{$info.surveyId|escape}" />
<table class="normal">
<tr class="formcolor"><td>{tr}Name{/tr}:</td><td><input type="text" name="name" value="{$info.name|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Description{/tr}:</td><td><textarea name="description" rows="4" cols="40">{$info.description|escape}</textarea></td></tr>
{include file=categorize.tpl}
<tr class="formcolor"><td>{tr}Status{/tr}</td><td>
<select name="status">
<option value="o" {if $info.status eq 'o'}selected='selected'{/if}>{tr}open{/tr}</option>
<option value="c" {if $info.status eq 'c'}selected='selected'{/if}>{tr}closed{/tr}</option>
</select>
</td></tr>
<tr class="formcolor"><td>&nbsp;</td><td><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<h2>{tr}Surveys{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td>{tr}Find{/tr}</td>
   <td>
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
<th><a class="tableheading" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'surveyId_desc'}surveyId_asc{else}surveyId_desc{/if}">{tr}ID{/tr}</a></th>
<th><a class="tableheading" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></th>
<th><a class="tableheading" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}description{/tr}</a></th>
<th><a class="tableheading" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'status_desc'}status_asc{else}status_desc{/if}">{tr}stat{/tr}</a></th>
<th>{tr}questions{/tr}</th>
<th>{tr}action{/tr}</th>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].surveyId}</td>
<td class="{cycle advance=false}">{$channels[user].name}</td>
<td class="{cycle advance=false}">{$channels[user].description}</td>
<td style="text-align:center;" class="{cycle advance=false}">
{if $channels[user].status eq 'o'}
	<img src="img/icons/ofo.gif" alt="{tr}open{/tr}" />
{else}
	<img src="img/icons/fo.gif" alt="{tr}closed{/tr}" />
{/if}
</td>
<td style="text-align:right;"  class="{cycle advance=false}">{$channels[user].questions}</td>
<td  class="{cycle}">
   <a title="{tr}edit{/tr}" class="link" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;surveyId={$channels[user].surveyId}"><img src="img/icons/config.gif" border="0" alt="{tr}edit{/tr}" /></a>
   <a title="{tr}question{/tr}" class="link" href="tiki-admin_survey_questions.php?surveyId={$channels[user].surveyId}"><img src="img/icons/question.gif" alt="{tr}question{/tr}" border="0" /></a>
   {if $channels[user].individual eq 'y'}({/if}<a title="{tr}permissions{/tr}" class="link" href="tiki-objectpermissions.php?objectName=Survey%20{$channels[user].name}&amp;objectType=survey&amp;permType=surveys&amp;objectId={$channels[user].surveyId}"><img src="img/icons/key.gif" alt="{tr}Permissions{/tr}" border="0" /></a>{if $channels[user].individual eq 'y'}){/if}
   <a title="{tr}delete{/tr}" class="link" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].surveyId}"><img src="img/icons2/delete.gif" alt="{tr}delete{/tr}" border="0" /></a>
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

