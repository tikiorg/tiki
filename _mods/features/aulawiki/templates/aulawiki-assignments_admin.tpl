{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
<p class="pagetitle">{tr}Assignments Administration{/tr}</p>
<form name="assignmentSelection" method="post" action="{$ownurl}">
<input name="activeAssignment" type="hidden" id="activeAssignment" value=""> 
</form>
<div id="divFormRemoveAssignment" style="display:none;">
<form name="formRemoveAssignment" method="post" action="{$ownurl}">
<input name="removeAssignmentId" type="hidden" id="removeAssignmentId" value="">
  <table class="normal">
     <tr> 
      <td class="formcolor">
      {tr}are you sure to remove the assigment{/tr}?
      </td>
    </tr>
      <tr> 
      <td class="formcolor" colspan="2"><center><input type="submit" name="removeAssignment" value="Yes"><input type="button" name="nobutton" value="No" onclick="document.getElementById('divFormRemoveAssignment').style.display = 'display:none;';"></center></td>
    </tr>
  </table>
</form>
</div>

{if $showAssignmentPanel}
<div class="edubox" id="divFormCreateAssignment" style="display:block;">
{else}
<div class="edubox" id="divFormCreateAssignment" style="display:none;">
{/if}
<form name="formCreateAssignment" method="post" action="{$ownurl}">
  <input name="assignmentId" type="hidden" id="assignmentId" value="{$activeAssignment.assignmentId}"> 

  <table class="normal">
     <tr> 
      <td class="formcolor"><label for="name">{tr}Name{/tr}</label></td>
      <td class="formcolor"><input name="name" type="text" id="name" value="{$activeAssignment.name}" size="60" maxlength="100"/></td>
    </tr>
    <tr> 
      <td class="formcolor"><label for="description">{tr}Description{/tr}</label></td>
      <td class="formcolor"><textarea name="description" id="description" size="60" cols="60" rows="4">{$activeAssignment.description}</textarea></td>
    </tr>
    <tr> 
      <td class="formcolor"><label for="wikipage">{tr}Page{/tr}</label></td>
      <td class="formcolor"><input name="wikipage" id="wikipage" size="60"  maxlength="100" value="{$activeAssignment.wikiPage}"/></td>
    </tr>
    <tr> 
      <td class="formcolor"><label for="periodId">{tr}Period{/tr}</label></td>
      <td class="formcolor">
      <select name="periodId" id="periodId">
	      {foreach key=key item=period from=$periods}
	      	<option value="{$period.periodId}" {if $period.periodId==$activeAssignment.periodId}selected{/if}>
	      	{$period.name}</option>
	      {/foreach}
      </select>
    </tr>
 
     <tr> 
      <td class="formcolor"><label for="gradeWeight">{tr}Grade Weight{/tr}</label></td>
      <td class="formcolor"><input name="gradeWeight" id="gradeWeight" size="60"  maxlength="3" value="{$activeAssignment.gradeWeight}"/></td>
    </tr>
     <tr> 
      <td class="formcolor"><label>{tr}Created{/tr}</label></td>
      <td class="formcolor">
		  {$activeAssignment.creationDate|date_format:"%B %e, %Y %H:%M"}  
      </td>
    </tr>
    <tr> 
      <td class="formcolor"><label>{tr}Created by{/tr}</label></td>
      <td class="formcolor">
		  {$activeAssignment.createdby}  
      </td>
    </tr>
    <tr> 
      <td class="formcolor"><label for="startDate">{tr}Start Date{/tr}</label></td>
      <td class="formcolor">
		{if $feature_jscalendar eq 'y'}
		<input type="hidden" name="startDate" value="{$startDate}" id="startDate" />
		<span id="start_date_display" class="daterow">{$startDate|date_format:$daformat}</span>
		<script type="text/javascript">
		{literal}Calendar.setup( { {/literal}
		date        : "{$startDate|date_format:"%B %e, %Y %H:%M"}",
		inputField  : "startDate",
		ifFormat    : "%s",
		displayArea : "start_date_display",
		daFormat    : "{$daformat}",
		showsTime   : true,
		singleClick : true,
		align       : "bR",
		firstDay : {$firstDayofWeek},
		timeFormat : {$timeFormat12_24}
		{literal} } );{/literal}
		</script>
		{else}
		{if $start_freeform_error eq 'y'}<span class="attention">{tr}Syntax error{/tr}</span><br />{/if}
		<input type="text" name="start_freeform" value="{$start_freeform}">
		<a {popup text="{tr}Format: mm/dd/yyyy hh:mm<br />...{/tr} {tr}See strtotime php function{/tr}"}><img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}'></a>
		{tr}or{/tr}
		{html_select_date time=$startDate prefix="start_" end_year="+4" field_order=DMY}
		{html_select_time minute_interval=5 time=$startDate prefix="starth_" display_seconds=false use_24_hours=true}
		{/if}
		</td>
    </tr>    
    <tr> 
      <td class="formcolor"><label for="endDate">{tr}End Date{/tr}</label></td>
      <td class="formcolor">
	
		{if $feature_jscalendar eq 'y'}
		<input type="hidden" name="endDate" value="{$endDate}" id="endDate" />
		<span id="end_date_display" class="daterow">{$endDate|date_format:$daformat}</span>
		<script type="text/javascript">
		{literal}Calendar.setup( { {/literal}
		date        : "{$endDate|date_format:"%B %e, %Y %H:%M"}",
		inputField  : "endDate",
		ifFormat    : "%s",
		displayArea : "end_date_display",
		daFormat    : "{$daformat}",
		showsTime   : true,
		singleClick : true,
		align       : "bR",
		firstDay : {$firstDayofWeek},
		timeFormat : {$timeFormat12_24}
		{literal} } );{/literal}
		</script>
		{else}
		{if $end_freeform_error eq 'y'}<span class="attention">{tr}Syntax error{/tr}</span><br />{/if}
		<input type="text" name="end_freeform" value="{$end_freeform}">
		<a {popup text="{tr}Format: mm/dd/yyyy hh:mm<br />...{/tr} {tr}See strtotime php function{/tr}"}><img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}'></a>
		{tr}or{/tr}
		{html_select_date time=$endDate prefix="end_" end_year="+4" field_order=DMY}
		{html_select_time minute_interval=5 time=$endDate prefix="endh_" display_seconds=false use_24_hours=true}
		{/if}
		</td>
    </tr>    
  
     <tr> 
      <td class="formcolor"><label for="type">{tr}Type{/tr}</label></td>
      <td class="formcolor">
      
      <select name="type" id="type">
	      	<option value="1" {if $activeAssignment.type eq 1}selected{/if}>{tr}Exam{/tr}</option>
	      	<option value="2" {if $activeAssignment.type eq 2}selected{/if}>{tr}Exercise{/tr}</option>
      </select>

      </td>
    </tr>
 
     <tr> 
      <td class="formcolor" colspan="2"><center><input class="edubutton" type="submit" name="sendAssignment" value="Guardar"></center></td>
    </tr>
  </table>
</form>
</div>
<br/>

<div class="edubuttons">
{if $showAssignmentPanel}
<input class="edubutton" type="button" name="sendAssignment" value="{tr}New Assignment{/tr}" onclick="document.getElementById('activeAssignment').value='';document['assignmentSelection'].submit()"/>
{else}
<input class="edubutton" type="button" name="sendAssignment" value="{tr}New Assignment{/tr}" onclick="document.getElementById('divFormCreateAssignment').style.display = 'block';"/>
{/if}
</div>



<table class="normal">
<tr>
<td class="heading">&nbsp;</td>
<td class="heading">{tr}Name{/tr}</td>
<td class="heading">{tr}Weight{/tr}</td>
<td class="heading">{tr}Start{/tr}</td>
<td class="heading">{tr}End{/tr}</td>
<td class="heading">&nbsp;</td>
<td class="heading">&nbsp;</td>
</tr>
{foreach from=$assignments item=assignment}
{cycle values="odd,even" assign="parImpar"}
<tr>
<td class="{$parImpar}">
<img src="./images/aulawiki/edu_assignments.gif" align="middle">
</td>
<td class="{$parImpar}" width=100%>
<a class="categtree" href="tiki-workspaces_view_module.php?module=aulawiki_view_assignment&activeAssignment={$assignment.assignmentId}">{$assignment.name}</a>
</td>
<td class="{$parImpar}" >
{$assignment.gradeWeight}
</td>
<td class="{$parImpar}">
{$assignment.startDate|tiki_short_datetime}
</td>
<td class="{$parImpar}">
{$assignment.endDate|tiki_short_datetime}
</td>
<td class="{$parImpar}"> 
    <img src='img/icons/edit.gif' border='0' alt='Editar' title='Editar' onclick="document.getElementById('activeAssignment').value='{$assignment.assignmentId}';document['assignmentSelection'].submit()"/>
</td>
<td class="{$parImpar}" >
<img src='img/icons2/delete.gif' border='0' alt='Borrar' title='Borrar' onclick="document.getElementById('removeAssignmentId').value='{$assignment.assignmentId}';document.getElementById('divFormRemoveAssignment').style.display = 'block';"/>
</td>
</tr>
{/foreach} 
</table>