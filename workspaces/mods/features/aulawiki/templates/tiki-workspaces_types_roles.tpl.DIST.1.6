{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
<p class="pagetitle">{tr}Workspace Type Roles{/tr}</p>
<a href="tiki-workspaces_types.php?edit={$wstype.id}">({$wstype.code}) {$wstype.name}</a>
<form name="form1" method="post" action="tiki-workspaces_types_roles.php">
  <input name="wstypeId" type="hidden" id="wstypeId" value="{$wstype.id}"> 
  <table class="normal">
    <tr> 
      <td class="formcolor"><label for="roleName">{tr}Role{/tr}</label></td>
      <td class="formcolor">
        <select name="roleName" id="roleName">
	      {foreach key=key item=role from=$rolesAll}
	      	{if !$role.selected}<option value="{$role.name}">{$role.name}</option>{/if}
	      {/foreach}
      	</select>
    </tr>
    <tr> 
      <td class="formcolor" ><label for="permGroup">{tr}Permission group{/tr}</label></td>
      <td class="formcolor">
           	<input name="permgroup" type="text" id="permgroup" value="" size="30" maxlength="100">
			{ws_help}Select a Tiki user group as a permissions templeate for that role. 
			If you left this filed blank, the permissions group asigned to the rol will be used. 
			The permissions that you assign to that group will be applied to de diferent resources (wiki page, blog, file gallery...) 
			of the workspaces that use that role.{/ws_help}

      </td>
    </tr>
 
     <tr> 
      <td class="formcolor" colspan="2"><center><input class="edubutton" type="submit" name="add" value="{tr}Add Role{/tr}"></center></td>
    </tr>
  </table>
</form>

<br/>

<table class="normal" width="100%">
    <tr> 
      <td class="heading" width="30%">{tr}Role name{/tr}</td>
      <td class="heading" width="40%">{tr}Role description{/tr}</td>
      <td class="heading" width="30%">{tr}Permission Group{/tr}</a></td>
      <td class="heading"> </td>
      <td class="heading"> </td>
    </tr>

{foreach key=key item=role from=$wstype.roles}
    {cycle values="odd,even" assign="parImpar"}
    <tr> 
      <td class="{$parImpar}">{$role.name}</td>
      <td class="{$parImpar}">{$role.description}</td>
      <td class="{$parImpar}">
      {if $role.wstypePermGroup==""}
      	{$role.permgroup}
      {else}
      	{$role.wstypePermGroup}
      {/if}
      </td>
      <td class="{$parImpar}"> <a class="link" href="tiki-assignpermission.php?group={if $role.wstypePermGroup==""}{$role.permgroup}{else}{$role.wstypePermGroup}{/if}">
           <img src='images/workspaces/edu_group.gif' border='0' alt='{tr}Premissions{/tr}' title='{tr}Premissions{/tr}' /></a></td>
        <td class="{$parImpar}"><a class="link" href="tiki-workspaces_types_roles.php?delete={$wstype.id}&wstypeId={$wstype.id}&roleName={$role.name}">
      	   <img src='img/icons2/delete.gif' border='0' alt='{tr}Delete{/tr}' title='{tr}Delete{/tr}' /></a>
      </td>
    </tr>
{/foreach}
</table>