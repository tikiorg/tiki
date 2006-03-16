<h1><a class="pagetitle" href="tiki-admin_surveys.php">{tr}Admin surveys{/tr}</a>



      {if $feature_help eq 'y'}
<a href="{$helpurl}Surveys" target="tikihelp" class="tikihelp" title="{tr}Surveys{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>{/if}

<!-- link to tpl and help-->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_surveys.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}administer surveys template{/tr}">
<img border="0" src="img/icons/info.gif" alt="{tr}edit{/tr}" /></a>{/if}</h1>

<!-- the two link buttons -->

<span class="button2"><a class="linkbut" href="tiki-list_surveys.php">{tr}list surveys{/tr}</a></span>
<span class="button2"><a class="linkbut" href="tiki-survey_stats.php">{tr}survey stats{/tr}</a></span>

<!-- describe the new survey -->
<br /><br />

{if $info.surveyId > 0}
<h2>{tr}Edit this Survey:{/tr} {$info.name}</h2>
{else}
<h2>{tr}Create New Survey{/tr}</h2>
{/if}
{if $individual eq 'y'}
<a class="link" href="tiki-objectpermissions.php?objectName={$info.name|escape:"url"}&amp;objectType=survey&amp;permType=surveys&amp;objectId={$info.surveyId}">{tr}There are individual permissions set for this survey{/tr}</a><br /><br />
{/if}
<form action="tiki-admin_surveys.php" method="post">
<input type="hidden" name="surveyId" value="{$info.surveyId|escape}" />
<table class="normal">
<tr class="formcolor">
<!-- the tiny field for giving it a name-->
<td>{tr}Name{/tr}:</td>
<td>

<!-- % works here -->
<input type="text" name="name" size="80" value="{$info.name|escape}" /></td>



</tr>
<tr class="formcolor">
<td>{tr}Description{/tr}:</td>
<td>
<!-- % !work here hence the wonkiness in formatting... ugly as dirt -->
<textarea name="description" rows="4" cols="80">{$info.description|escape}</textarea></td></tr>
{include file=categorize.tpl}
<tr class="formcolor">
<td>{tr}Status{/tr}</td>
<td>
<select name="status">
<!-- the survey has an on and off button... could be applied to quizzes -->
<option value="o" {if $info.status eq 'o'}selected='selected'{/if}>{tr}open{/tr}</option>
<option value="c" {if $info.status eq 'c'}selected='selected'{/if}>{tr}closed{/tr}</option>
</select>
</td></tr>
<tr class="formcolor"><td>&nbsp;</td><td><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<!--  find in existing surveys -->
<h2>{tr}Surveys{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="tiki-admin_surveys.php">
     <input type="text" name="find" size= "40" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<!--  existing surveys -->

<table class="normal">
<tr>
<td class="heading">
<!--  table: sort by ID -->
<a class="tableheading" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'surveyId_desc'}surveyId_asc{else}surveyId_desc{/if}">{tr}ID{/tr}</a></td>
<!--  table: sort by name -->
<td class="heading"><a class="tableheading" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></td>
<!--  table: sort by description-->
<td class="heading"><a class="tableheading" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}description{/tr}</a></td>
<!--  table: sort by stat: which appears rediculous becuase there is nothing to sort-->
<td class="heading"><a class="tableheading" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'status_desc'}status_asc{else}status_desc{/if}">{tr}status{/tr}</a></td>
<!--  table: sort by question but it doesn't work and I don't know why-->
{*
<td class="heading"><a class="tableheading" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'question_desc'}question_asc{else}question_desc{/if}">{tr}questions{/tr}</td>
*}
<td class="heading">{tr}questions{/tr}</td>

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
	<img src="img/icons/ofo.gif" alt="{tr}open{/tr}" />
{else}
	<img src="img/icons/fo.gif" alt="{tr}closed{/tr}" />
{/if}
</td>
<td style="text-align:right;"  class="{cycle advance=false}">{$channels[user].questions}</td>
<td  class="{cycle}">
   <a class="link" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;surveyId={$channels[user].surveyId}"><img src="img/icons/config.gif" border="0" width="16" height="16" alt='{tr}edit{/tr}' title='{tr}edit{/tr}' /></a>
   <a class="link" href="tiki-admin_survey_questions.php?surveyId={$channels[user].surveyId}">{html_image file='img/icons/question.gif' alt='{tr}question{/tr}' border='0' title='{tr}questions{/tr}'}</a>
   <a class="link" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].surveyId}"><img src="img/icons2/delete.gif" border="0" width="16" height="16"  alt='{tr}remove{/tr}' title='{tr}remove{/tr}' /></a>
   {if $channels[user].individual eq 'y'}
	<a class="link" href="tiki-objectpermissions.php?objectName={$channels[user].name|escape:"url"}&amp;objectType=survey&amp;permType=surveys&amp;objectId={$channels[user].surveyId}"><img src='img/icons/key_active.gif' alt='{tr}active perms{/tr}' border='0' title='{tr}active perms{/tr}' /></a>
   {else}
	<a class="link" href="tiki-objectpermissions.php?objectName={$channels[user].name|escape:"url"}&amp;objectType=survey&amp;permType=surveys&amp;objectId={$channels[user].surveyId}"><img src="img/icons/key.gif" border="0" width="17" height="16" alt='{tr}perms{/tr}' title='{tr}perms{/tr}' /></a>
   {/if}
</td>
</tr>
{/section}
</table>
<!--  the little page adjust... need to find the $maxRecords value so that we can set that to a user controlled value-->

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

<!--end tiki-admin_surveys.tpl... last edited by dgd-->
