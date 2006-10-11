{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
<p class="pagetitle">{tr}Roles Administration{/tr}</p>
{include file="tiki-workspaces_module_error.tpl" error=$page_error_msg}
<form name="form1" method="post" action="tiki-workspaces_roles.php">
  <input name="uid" type="hidden" id="uid" value="{$role.uid}"> 
  <table class="normal">
     <tr> 
      <td class="formcolor"><label for="name">{tr}Name{/tr}</label></td>
      <td class="formcolor"><input name="name" type="text" id="name" value="{$role.name}" size="60" maxlength="100"/></td>
    </tr>
    <tr> 
      <td class="formcolor" ><label for="desc">{tr}Description{/tr}</label></td>
      <td class="formcolor"><textarea name="desc" size="60" cols="60" rows="4">{$role.description}</textarea></td>
    </tr>
    <tr>
    	<td class="formcolor" ><label for="permgroup">{tr}Permission group{/tr}</label></td>
      <td class="formcolor">
				<input name="permgroup" type="text" id="permgroup" value="{$role.permgroup}" size="30" maxlength="100"/>
				{ws_help}Select a Tiki user group as a permissions templeate for that role.<br/> If you left this filed blank, a default group will be created.<br/>The permissions that you assign to that group will be applied to de diferent resources (wiki page, blog, file gallery...) of the workspaces that use that role.{/ws_help}
      </td>
     </tr>
    <tr> 
      <td class="formcolor" colspan="2"><center><input class="edubutton" type="submit" name="send" value="{tr}Save{/tr}"/></center></td>
    </tr>
  </table>
</form>

<br/>
<table class="findtable">
<tr>
   <td>
   <form method="get" action="tiki-workspaces_roles.php">
   <label for="find">{tr}Find{/tr}</find>
     <input type="text" name="find" id="find" value="{$find|escape}" />
     <input class="edubutton" type="submit" value="{tr}find{/tr}" name="search" />
		 <label for="numrows">{tr}Number of displayed rows{/tr}</label>
		 <input type="text" size="4" name="numrows" id=="numrows" value="{$numrows|escape}">
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>


<table class="normal" width="100%">
    <tr> 
      <td class="heading" width="40%"><a class="tableheading" href="tiki-workspaces_roles.php?offset={$offset}&amp;numrows={$numrows}&amp;sort_mode={if $sort_mode eq 'nombre_desc'}nombre_asc{else}nombre_desc{/if}">{tr}Name{/tr}</a></td>
      <td class="heading" width="50%">{tr}Description{/tr}</td>
      <td class="heading"> </td>
      <td class="heading"> </td>
      <td class="heading"> </td>
    </tr>

{section name=i loop=$roles}
    {cycle values="odd,even" assign="parImpar"}
    <tr> 
     <td class="{$parImpar}">{$roles[i].name}</td>
      <td class="{$parImpar}">{$roles[i].description}</td>
      <td class="{$parImpar}"> <a class="link" href="tiki-workspaces_roles.php?edit={$roles[i].uid}">
           <img src='img/icons/edit.gif' border='0' alt='{tr}Edit{/tr}' title='{tr}Edit{/tr}' /></a></td>
      <td class="{$parImpar}"> <a class="link" href="tiki-assignpermission.php?group={$roles[i].permgroup}">
           <img src='images/workspaces/edu_group.gif' border='0' alt='{tr}Premissions{/tr}' title='{tr}Premissions{/tr}' /></a></td>
      <td class="{$parImpar}"><a class="link" href="tiki-workspaces_roles.php?delete={$roles[i].uid}">
      	   <img src='img/icons2/delete.gif' border='0' alt='{tr}Delete{/tr}' title='{tr}Delete{/tr}' /></a>
      </td>
    </tr>
{/section}
</table>


<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-workspaces_roles.php?find={$find}&amp;offset={$prev_offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>] 
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
 [<a class="prevnext" href="tiki-workspaces_roles.php?find={$find}&amp;offset={$next_offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-workspaces_roles.php?find={$find}&amp;offset={$selector_offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>
{/section}
{/if}

</div>
</div>