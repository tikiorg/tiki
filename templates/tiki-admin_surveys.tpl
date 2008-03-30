{* $Id$ *}
<h1><a class="pagetitle" href="tiki-admin_surveys.php">{tr}Admin surveys{/tr}</a>
{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Surveys" target="tikihelp" class="tikihelp" title="{tr}Surveys{/tr}">
{icon _id='help'}</a>{/if}
{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_surveys.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}administer surveys template{/tr}">
{icon _id='shape_square_edit'}</a>{/if}</h1>

<div class="navbar">
<span class="button2"><a class="linkbut" href="tiki-list_surveys.php">{tr}List Surveys{/tr}</a></span>
<span class="button2"><a class="linkbut" href="tiki-survey_stats.php">{tr}Survey Stats{/tr}</a></span>
</div>

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
<option value="o" {if $info.status eq 'o'}selected='selected'{/if}>{tr}Open{/tr}</option>
<option value="c" {if $info.status eq 'c'}selected='selected'{/if}>{tr}closed{/tr}</option>
</select>
</td></tr>
<tr class="formcolor"><td>&nbsp;</td><td><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<!--  find in existing surveys -->
<h2>{tr}Surveys{/tr}</h2>

<table class="findtable">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="tiki-admin_surveys.php">
     <input type="text" name="find" size= "40" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
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
<td class="heading"><a class="tableheading" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<!--  table: sort by description-->
<td class="heading"><a class="tableheading" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a></td>
<!--  table: sort by stat: which appears rediculous becuase there is nothing to sort-->
<td class="heading"><a class="tableheading" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'status_desc'}status_asc{else}status_desc{/if}">{tr}Status{/tr}</a></td>
<!--  table: sort by question but it doesn't work and I don't know why-->
{*
<td class="heading"><a class="tableheading" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'question_desc'}question_asc{else}question_desc{/if}">{tr}Questions{/tr}</td>
*}
<td class="heading">{tr}Questions{/tr}</td>

<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].surveyId}</td>
<td class="{cycle advance=false}">{$channels[user].name}</td>
<td class="{cycle advance=false}">{$channels[user].description}</td>
<td style="text-align:center;" class="{cycle advance=false}">
{if $channels[user].status eq 'o'}
	<img src="img/icons/ofo.gif" alt="{tr}Open{/tr}" />
{else}
	<img src="img/icons/fo.gif" alt="{tr}closed{/tr}" />
{/if}
</td>
<td style="text-align:right;"  class="{cycle advance=false}">{$channels[user].questions}</td>
<td  class="{cycle}">
   <a class="link" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;surveyId={$channels[user].surveyId}">{icon _id='wrench' alt='{tr}Edit{/tr}'}</a>
   <a class="link" href="tiki-admin_survey_questions.php?surveyId={$channels[user].surveyId}">{icon _id='help' alt='{tr}question{/tr}' title='{tr}Questions{/tr}'}</a>
   <a class="link" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].surveyId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
   {if $channels[user].individual eq 'y'}
	<a class="link" href="tiki-objectpermissions.php?objectName={$channels[user].name|escape:"url"}&amp;objectType=survey&amp;permType=surveys&amp;objectId={$channels[user].surveyId}">{icon _id='key_active' alt='{tr}Active Perms{/tr}'}</a>
   {else}
	<a class="link" href="tiki-objectpermissions.php?objectName={$channels[user].name|escape:"url"}&amp;objectType=survey&amp;permType=surveys&amp;objectId={$channels[user].surveyId}">{icon _id='key' alt='{tr}Perms{/tr}'}</a>
   {/if}
</td>
</tr>
{/section}
</table>
<!--  the little page adjust... need to find the $prefs.maxRecords value so that we can set that to a user controlled value-->

<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_surveys.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_surveys.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-admin_surveys.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>

<!--end tiki-admin_surveys.tpl... last edited by dgd-->
