{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{tiki_workspaces_module title="{tr}UserGroups{/tr}" name="user_groups" flip=$module_params.flip decorations=$module_params.decorations style_title=$style_title style_data=$style_data}
{include file="tiki-workspaces_module_error.tpl" error=$error_msg}

<form name="groupSelection" method="post" action="{$ownurl}">
<input name="activeGroup" type="hidden" id="activeGroup" value=""/> 
<input name="activeParentGroup" type="hidden" id="activeParentGroup" value=""/> 
</form>
{if $edu_user_groups_error!=""}
<div class="eduerror">
<img src='images/workspaces/edu_stop.gif' align="middle"/>
{$edu_user_groups_error}
</div>
{/if}
<br/>
<div class="edubox" id="formCreateGroup" style="display:none;">
<form name="formCreateGroup" method="post" action="{$ownurl}">
<input name="createGroupActiveName" type="hidden" id="createGroupActiveName" value="{$activeGroup}">
  <table class="normal">
     <tr> 
      <td class="formcolor">
      	<label for="createGroupName">{tr}Group Name:{/tr}</label>
      	<br/>
      	{$workspaceGroupName}-<input name="createGroupName" type="text" id="createGroupName" value="" size="25" maxlength="100"></td>
    </tr>
    <tr> 
      <td class="formcolor">
      <label for="createGroupDesc">{tr}Description:{/tr}</label>
      <br/>
      <textarea name="createGroupDesc" id="createGroupDesc" size="20" cols="35" rows="3"> </textarea></td>
    </tr>
     <tr> 
      <td class="formcolor" colspan="2"><center><input class="edubutton" type="submit" name="createGroup" value="Create Group"> <input class="edubutton" type="button" name="cancel" value="Cancel" onclick="document.getElementById('formCreateGroup').style.display = 'none';"></center></td>
    </tr>
  </table>
</form>
</div>

<div id="formCreateUser" style="display:none;">
<form name="formCreateUser" method="post" action="{$ownurl}">
<input name="createUserActiveGrpName" type="hidden" id="createUserActiveGrpName" value="{$activeGroup}">
  <table class="normal">
     <tr> 
      <td class="formcolor">
      <label for="createUserName">{tr}User name:{/tr}</label>
      <br/><input name="createUserName" type="text" id="createUserName" value="" size="25" maxlength="100"></td>
    </tr>
     <tr> 
      <td class="formcolor" colspan="2"><center><input class="edubutton" type="submit" name="createUser" value="Add user"> <input class="edubutton" type="button" name="cancel" value="Cancel" onclick="document.getElementById('formCreateUser').style.display = 'none';"></center></td>
    </tr>
  </table>
</form>
</div>

<div id="formAddGroup" style="display:none;">
<form name="formAddGroup" method="post" action="{$ownurl}">
<input name="addGroupActiveName" type="hidden" id="addGroupActiveName" value="{$activeGroup}">
  <table class="normal">
     <tr> 
      <td class="formcolor">
      <label for="addGroupName">{tr}Group Name:{/tr}</label>
      <br/><input name="addGroupName" type="text" id="addGroupName" value="" size="25" maxlength="100"></td>
    </tr>
      <tr> 
      <td class="formcolor" colspan="2"><center><input class="edubutton" type="submit" name="addGroup" value="Add Group"><input class="edubutton" type="button" name="cancel" value="Cancel" onclick="document.getElementById('formAddGroup').style.display = 'none';"></center></td>
    </tr>
  </table>
</form>
</div>
<div id="formRemoveGroup" style="display:none;">
<form name="formRemoveGroup" method="post" action="{$ownurl}">
<input name="removeGroupActiveName" type="hidden" id="removeGroupActiveName" value="{$activeGroup}">
<input name="removeGroupActiveParentName" type="hidden" id="removeGroupActiveParentName" value="{$activeParentGroup}">
  <table class="normal">
     <tr> 
      <td class="formcolor">
      {tr}are you sure to remove group {/tr}{$activeGroup}?
      </td>
    </tr>
      <tr> 
      <td class="formcolor" colspan="2"><center><input class="edubutton" type="submit" name="removeGroup" value="Yes"> <input class="edubutton" type="button" name="nobutton" value="No" onclick="document.getElementById('formRemoveGroup').style.display = 'none';"></center></td>
    </tr>
  </table>
</form>
</div>
<div id="formRemoveUser" style="display:none;">
<form name="formRemoveUser" method="post" action="{$ownurl}">
<input name="removeUserGroupActiveName" type="hidden" id="removeUserGroupActiveName" value="{$activeGroup}">
<input name="removeUserName" type="hidden" id="removeUserName" value="">
  <table class="normal">
     <tr> 
      <td class="formcolor">
      {tr}are you sure to remove user from group {/tr}{$activeGroup}?
      </td>
    </tr>
      <tr> 
      <td class="formcolor" colspan="2"><center><input class="edubutton" type="submit" name="removeUser" value="Yes"> <input class="edubutton" type="button" name="nobutton" value="No" onclick="document.getElementById('formRemoveUser').style.display = 'none';"></center></td>
    </tr>
  </table>
</form>
</div>
<b>Active group: {$activeGroup}</b>
<div class="edubuttons">
<a class="edubutton" href="#" onclick="document.getElementById('formCreateGroup').style.display = 'block';">
<img border='0'src='images/workspaces/edu_group_new.gif'/> New group</a>
<a class="edubutton" href="#" onclick="document.getElementById('formAddGroup').style.display = 'block';">
<img border='0' src='images/workspaces/edu_group_add.gif'/> Add group</a>
<a class="edubutton" href="#" onclick="document.getElementById('formRemoveGroup').style.display = 'block';">
<img border='0' src='images/workspaces/edu_group_remove.gif'/>Remove group</a> 
<a class="edubutton" href="#" onclick="document.getElementById('formCreateUser').style.display = 'block';">
<img border='0' src='images/workspaces/edu_user_new.gif'/> Add user</a>
</div>
<div class="edubox">
{$groupsTree}
</div>

<table class="normal">
<tr>
<td class="heading">&nbsp;</td><td class="heading">{tr}User{/tr}</td><td class="heading">{tr}Name{/tr}</td><td class="heading">&nbsp;</td>
</tr>
{foreach name=u key=k item=user from=$groupusers}
<tr>
<td class="odd" width="48"><img src="./tiki-show_user_avatar.php?user={$user.login}" align='absmiddle' border=0></td>
<td class="odd"><a href="./tiki-user_information.php?view_user={$user.login}">{$user.login}</a></td>
<td class="odd" width=100%>	<a href="./tiki-user_information.php?view_user={$user.login}">{$user.name}</a></td>
<td class="odd" >
<img src='img/icons2/delete.gif' border='0' alt='Borrar' title='Borrar' onclick="document.getElementById('removeUserName').value='{$user.login}';document.getElementById('formRemoveUser').style.display = 'block';"/>
</td>
</tr>
{/foreach} 
</table>
{/tiki_workspaces_module}

