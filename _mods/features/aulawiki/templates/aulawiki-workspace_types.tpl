<p class="pagetitle">{tr}Workspace Types Administration{/tr}</p>
<form name="form1" method="post" action="aulawiki-workspace_types.php">
  <input name="id" type="hidden" id="id" value="{$wstype.id}"> 
  <table class="normal">
    <tr> 
      <td class="formcolor"><label for="code">{tr}Code{/tr}</label></td>
      <td class="formcolor"><input name="code" type="text" id="code" value="{$wstype.code}" size="60" maxlength="100"></td>
    </tr>
     <tr> 
      <td class="formcolor"><label for="name">{tr}Name{/tr}</label></td>
      <td class="formcolor"><input name="name" type="text" id="name" value="{$wstype.name}" size="60" maxlength="100"></td>
    </tr>
    <tr> 
      <td class="formcolor" ><label for="desc">{tr}Description{/tr}</label></td>
      <td class="formcolor"><textarea name="desc" size="60" cols="60" rows="4">{$wstype.description}</textarea></td>
    </tr>
    <tr> 
      <td class="formcolor"><label for="menuid">{tr}MenuID{/tr}</label></td>
      <td class="formcolor"><input name="menuid" type="text" id="menuid" value="{$wstype.menuid}" size="8" maxlength="8"></td>
    </tr>
    <tr> 
      <td class="formcolor" ><label for="active">{tr}Active{/tr}</label></td>
      <td class="formcolor">
		  <input name="active" id="active" type="checkbox" value="y" {if $wstype.active eq 'y'}checked{/if}>    
      </td>
    <tr> 
      <td class="formcolor" ><label for="hide">{tr}Hide{/tr}</label></td>
      <td class="formcolor">
		  <input name="hide" id="hide" type="checkbox" value="y" {if $wstype.hide eq 'y'}checked{/if}>    
      </td>
    </tr>
    <tr> 
      <td class="formcolor" ><label for="anonymous">{tr}Allow Anonymous{/tr}</label></td>
      <td class="formcolor">
		  <input name="anonymous" id="anonymous" type="checkbox" value="y" {if $wstype.anonymous eq 'y'}checked{/if}>  (Anonymous users can access workspaces)   
      </td>
    </tr>
    <tr> 
      <td class="formcolor" ><label for="registered">{tr}Allow Registered{/tr}</label></td>
      <td class="formcolor">
		  <input name="registered" id="registered" type="checkbox" value="y" {if $wstype.registered eq 'y'}checked{/if}> (Registered users can access workspaces)    
      </td>
    </tr>
    <tr> 
      <td class="formcolor" ><label for="roles">{tr}Roles{/tr}</label></td>
      <td class="formcolor">
      
      <select name="roles[]" size="5" multiple id="roles">
	      {section name=i loop=$rolesAll}
	      	<option value="{$rolesAll[i].name}" {if $rolesAll[i].selected}selected{/if}>{$rolesAll[i].name}</option>
	      {/section}
      </select>

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

      </td>
    </tr>
	<tr>
    <td class="formcolor" ><label for="workspaceResources">{tr}Workspace resources{/tr}</label></td>
    <td class="formcolor">
		<table class="normal" width="100%">
		    <tr> 
		      <td class="heading" width="10%">ID</td>
		      <td class="heading" width="15%">{tr}Name{/tr}</td>
		      <td class="heading" width="65%">{tr}Description{/tr}</a></td>
		      <td class="heading" width="10%">{tr}type{/tr}</td>
		      <td class="heading"> </td>
		      <td class="heading"> </td>
		    </tr>
		{assign var=resources value=$wstype.resources}
		{section name=i loop=$resources}
			{assign var=index value=$smarty.section.i.index}
		    {cycle values="odd,even" assign="parImpar"}
		    <tr> 
		      <td class="{$parImpar}">{$index}</td>
		      <td class="{$parImpar}">{$wstype.resources[i].name}</td>
		      <td class="{$parImpar}">{$wstype.resources[i].desc}</td>
		      <td class="{$parImpar}">{$wstype.resources[i].type}</td>
		      <td class="{$parImpar}"> <a class="link" href="aulawiki-workspace_types_resources.php?edit={$index}&wstypeId={$wstype.id}">
		           <img src='img/icons/edit.gif' border='0' alt='Editar' title='Editar' /></a></td>
		       <td class="{$parImpar}"><a class="link" href="aulawiki-workspace_types_resources.php?delete={$index}&wstypeId={$wstype.id}">
		      	   <img src='img/icons2/delete.gif' border='0' alt='Borrar' title='Borrar' /></a>
		      </td>
		    </tr>
		{/section}
		</table>
	</td>
	</tr>
 
     <tr> 
      <td class="formcolor" colspan="2"><center><input class="edubutton" type="submit" name="send" value="Guardar"></center></td>
    </tr>
  </table>
</form>

<br/>
<table class="findtable">
<tr><td><label for="find">{tr}Find{/tr}</find></td>
   <td>
   <form method="get" action="aulawiki-workspace_types.php">
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
      <td class="heading" width="65%"><a class="tableheading" href="aulawiki-wstype.php?offset={$offset}&amp;numrows={$numrows}&amp;sort_mode={if $sort_mode eq 'nombre_desc'}nombre_asc{else}nombre_desc{/if}">{tr}Name{/tr}</a></td>
      <td class="heading" width="10%">{tr}Active{/tr}</td>
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
      <td class="{$parImpar}"> <a class="link" href="aulawiki-workspace_types.php?edit={$wstypes[i].id}">
           <img src='img/icons/edit.gif' border='0' alt='Editar' title='Editar' /></a></td>
      <td class="{$parImpar}"><a class="link" href="aulawiki-ws_assigned_modules.php?workspaceId={$wstypes[i].id}&wsmodtype=workspace type">
      	   <img src='img/icons/mo.png' border='0' alt='Assigned modules' title='Assigned modules' /></a>
      </td>
{*      <td class="{$parImpar}"><a class="link" href="tiki-admingroups.php?find=WSTYPEGRP{$wstypes[i].code}&search=find&numrows=10&sort_mode=groupName_asc">
      	   <img src='images/aulawiki/edu_group.gif' border='0' alt='User Groups' title='User Groups' /></a>
      </td>
      *}
      <td class="{$parImpar}"><a class="link" href="aulawiki-workspace_types_resources.php?wstypeId={$wstypes[i].id}">
      	   <img src='img/icons/change.gif' border='0' alt='Workspace type resources' title='Workspace type resources' /></a>
      </td>
      <td class="{$parImpar}"><a class="link" href="aulawiki-workspace_types.php?delete={$wstypes[i].id}">
      	   <img src='img/icons2/delete.gif' border='0' alt='Borrar' title='Borrar' /></a>
      </td>
    </tr>
{/section}
</table>


<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="aulawiki-workspace_types.php?find={$find}&amp;offset={$prev_offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>] 
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
 [<a class="prevnext" href="aulawiki-workspace_types.php?find={$find}&amp;offset={$next_offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="aulawiki-workspace_types.php?find={$find}&amp;offset={$selector_offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>
{/section}
{/if}

</div>
</div>