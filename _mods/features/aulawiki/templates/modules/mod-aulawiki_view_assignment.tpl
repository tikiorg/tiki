{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{tiki_workspaces_module title="{tr}Assignment details{/tr}" name="aulawiki_view_assignment" flip=$module_params.flip decorations=$module_params.decorations}
{include file="tiki-workspaces_module_error.tpl" error=$error_msg}
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
  <table class="normal">
     <tr> 
      <td class="formcolor"><label>{tr}Name{/tr}</label></td>
      <td class="formcolor">{$activeAssignment.name}</td>
    </tr>
    <tr> 
      <td class="formcolor"><label>{tr}Description{/tr}</label></td>
      <td class="formcolor">{$activeAssignment.description}</td>
    </tr>
    <tr> 
      <td class="formcolor"><label>{tr}Page{/tr}</label></td>
      <td class="formcolor"><a href="{$activeAssignment.wikiPage}">{$activeAssignment.wikiPage}</a></td>
    </tr>
    <tr> 
      <td class="formcolor"><label>{tr}Period{/tr}</label></td>
      <td class="formcolor">
	      {foreach key=key item=period from=$periods}
	      	{if $period.periodId==$activeAssignment.periodId}{$period.name}{/if}
	      {/foreach}
    </tr>
 
     <tr> 
      <td class="formcolor"><label>{tr}Grade Weight{/tr}</label></td>
      <td class="formcolor">{$activeAssignment.gradeWeight}</td>
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
		{$activeAssignment.startDate|date_format:"%B %e, %Y %H:%M"}
    	</td>
    </tr>    
    <tr> 
      <td class="formcolor"><label for="endDate">{tr}End Date{/tr}</label></td>
      <td class="formcolor">
		{$activeAssignment.endDate|date_format:"%B %e, %Y %H:%M"}
	</td>
    </tr>    
  
     <tr> 
      <td class="formcolor"><label for="type">{tr}Type{/tr}</label></td>
      <td class="formcolor">
		{if $activeAssignment.type eq 1}{tr}Exam{/tr}{/if}
	    {if $activeAssignment.type eq 2}{tr}Exercise{/tr}{/if}
      </td>
    </tr>
 
  </table>
</div>
<br/>

{if $isTeacher}
<form name="formSaveGrades" method="post" action="{$ownurl}">
<input name="activeAssignment" type="hidden" id="activeAssignment" value="{$activeAssignment.assignmentId}"> 

<table class="normal">
<tr>
<td class="heading">&nbsp;</td>
<td class="heading" width="40%">{tr}Name{/tr}</td>
<td class="heading">{tr}Grade{/tr}</td>
<td class="heading" width="60%">{tr}Comment{/tr}</td>

</tr>
{foreach from=$users item=user}
{cycle values="odd,even" assign="parImpar"}
<tr>
<td class="{$parImpar}">
<img src="./images/aulawiki/edu_assignments.gif" align="middle">
</td>
<td class="{$parImpar}" width=100%>
<a class="categtree" href="aulawiki-view_assignment.php?assignmentId={$assignment.assignmentId}">({$user.login}) {$user.name}</a>
</td>
<td class="{$parImpar}" >
<input name="grade-{$user.login}" type="text" id="grade-{$user.login}" value="{$grades[$user.login].grade}" size="4">
</td>
<td class="{$parImpar}">
<textarea name="comment-{$user.login}" id="comment-{$user.login}" size="30" cols="40" rows="2">{$grades[$user.login].comment}</textarea>
</td>
</tr>
{/foreach}
     <tr> 
      <td  colspan="4"><center><input class="edubutton" type="submit" name="saveGrades" value="{tr}Save Grades{/tr}"></center></td>
    </tr> 
</table>
{else}
<h2>{tr}Grade for{/tr} {$currentUser}</h2>
<label>Grade:</label>{$grades[$currentUser].grade}<br/>
<label>Comment:</label>{$grades[$currentUser].comment}
{/if}

{/tiki_workspaces_module}
