{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
<p class="pagetitle">{tr}Workspace Types Administration{/tr}</p>
<form name="form1" method="post" action="tiki-workspaces_types.php">
  <input name="id" type="hidden" id="id" value="{$wstype.id}"> 
  <table class="normal">
    <tr> 
      <td class="formcolor"><label for="code">{tr}Code{/tr}</label></td>
      <td class="formcolor">
      	<input name="code" type="text" id="code" value="{$wstype.code}" size="60" maxlength="100"/>
      	{ws_help}A sort unique identifier for the workspace type{/ws_help}
      </td>
    </tr>
     <tr> 
      <td class="formcolor"><label for="name">{tr}Name{/tr}</label></td>
      <td class="formcolor">
      	<input name="name" type="text" id="name" value="{$wstype.name}" size="60" maxlength="100"/>
      	{ws_help}A sort description for the workspace type{/ws_help}
      </td>
    </tr>
    <tr> 
      <td class="formcolor" ><label for="desc">{tr}Description{/tr}</label></td>
      <td class="formcolor">
      	<textarea name="desc" size="60" cols="60" rows="4">{$wstype.description}</textarea>
      	{ws_help}A long description for workspace type{/ws_help}
      </td>
    </tr>
    <tr> 
      <td class="formcolor"><label for="menuid">{tr}MenuID{/tr}</label></td>
      <td class="formcolor">
      	<input name="menuid" type="text" id="menuid" value="{$wstype.menuid}" size="8" maxlength="8"/>
      	{ws_help}Select an existing Tiki menu id. <br/>This menu will be showed on My workspaces module for the workspaces of this type.{/ws_help}
      </td>
    </tr>
    <tr> 
      <td class="formcolor" ><label for="active">{tr}Active{/tr}</label></td>
      <td class="formcolor">
		  	<input name="active" id="active" type="checkbox" value="y" {if $wstype.active eq 'y'}checked{/if}/>    
		  	{ws_help}Check it to allow the creation of new workspaces of that type.{/ws_help}
      </td>
    <tr> 
      <td class="formcolor" ><label for="hide">{tr}Hide{/tr}</label></td>
      <td class="formcolor">
		  	<input name="hide" id="hide" type="checkbox" value="y" {if $wstype.hide eq 'y'}checked{/if}/>
		  	{ws_help}If checked, the workspaces of that type will not be listed on the My workspaces module.{/ws_help}
      </td>
    </tr>

    <tr> 
      <td class="formcolor" ><label for="userws">{tr}Allow private user zone{/tr}</label></td>
      <td class="formcolor">
      
      <select name="userws" id="userws">
	      <option value="" {if $activeTypes[i].id eq ""}selected{/if}>Not allowed</option>
	      {section name=i loop=$activeTypes}
	      	<option value="{$activeTypes[i].id}" {if $activeTypes[i].id eq $wstype.userwstype}selected{/if}>{$activeTypes[i].name}</option>
	      {/section}
      </select>
			{ws_help}A workspace can have a private subworkspace for each user of the main workspace. You can select the workspace type for that private zones.{/ws_help}
      </td>
    </tr>
     <tr> 
      <td class="formcolor" colspan="2"><center><input class="edubutton" type="submit" name="send" value="{tr}Save{/tr}"></center></td>
    </tr>
  </table>
</form>

<br/>
<table class="findtable">
<tr><td><label for="find">{tr}Find{/tr}</find></td>
   <td>
   <form method="get" action="tiki-workspaces_types.php">
     <input type="text" name="find" id=="find" value="{$find|escape}" />
     <input class="edubutton" type="submit" value="{tr}find{/tr}" name="search" />
		 <label for="numrows">{tr}Number of displayed rows{/tr}</label>
		 <input type="text" size="4" name="numrows" id="numrows" value="{$numrows|escape}">
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>


<table class="normal" width="100%">
    <tr> 
      <td class="heading" width="10%">ID</td>
      <td class="heading" width="15%">{tr}Code{/tr}</td>
      <td class="heading" width="65%"><a class="tableheading" href="tiki-workspaces_types.php?offset={$offset}&amp;numrows={$numrows}&amp;sort_mode={if $sort_mode eq 'nombre_desc'}nombre_asc{else}nombre_desc{/if}">{tr}Name{/tr}</a></td>
      <td class="heading" width="10%">{tr}Active{/tr}</td>
      <td class="heading"> </td>
      <td class="heading"> </td>
      <td class="heading"> </td>
      <td class="heading"> </td>
      <td class="heading"> </td>
    </tr>

{section name=i loop=$wstypes}
    {cycle values="odd,even" assign="parImpar"}
    <tr> 
      <td class="{$parImpar}">{$wstypes[i].id}</td>
      <td class="{$parImpar}">{$wstypes[i].code}</td>
      <td class="{$parImpar}">{$wstypes[i].name}</td>
      <td class="{$parImpar}">{$wstypes[i].active}</td>
      <td class="{$parImpar}"> <a class="link" href="tiki-workspaces_types.php?edit={$wstypes[i].id}">
           <img src='img/icons/edit.gif' border='0' alt='{tr}Edit{/tr}' title='{tr}Edit{/tr}' /></a></td>
      <td class="{$parImpar}"><a class="link" href="tiki-workspaces_assigned_modules.php?wstypeId={$wstypes[i].id}&wsmodtype=workspace type">
      	   <img src='img/icons/mo.png' border='0' alt='Assigned modules' title='Assigned modules' /></a>
      </td>
      <td class="{$parImpar}"><a class="link" href="tiki-workspaces_types_roles.php?wstypeId={$wstypes[i].id}">
      	   <img src='images/workspaces/edu_role.gif' border='0' alt='{tr}Workspace type roles{/tr}' title='{tr}Workspace type roles{/tr}' /></a>
      </td>
      <td class="{$parImpar}"><a class="link" href="tiki-workspaces_types_resources.php?wstypeId={$wstypes[i].id}">
      	   <img src='img/icons/change.gif' border='0' alt='{tr}Workspace type resources{/tr}' title='{tr}Workspace type resources{/tr}' /></a>
      </td>
      <td class="{$parImpar}"><a class="link" href="tiki-workspaces_types.php?delete={$wstypes[i].id}">
      	   <img src='img/icons2/delete.gif' border='0' alt='{tr}Delete{/tr}' title='{tr}Delete{/tr}' /></a>
      </td>
    </tr>
{/section}
</table>


<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-workspaces_types.php?find={$find}&amp;offset={$prev_offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>] 
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
 [<a class="prevnext" href="tiki-workspaces_types.php?find={$find}&amp;offset={$next_offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-workspaces_types.php?find={$find}&amp;offset={$selector_offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>
{/section}
{/if}

</div>
</div>