{* $Header: /cvsroot/tikiwiki/_mods/templates/sdl_collection/templates/tiki-pagepermissions.tpl,v 1.1 2004-05-09 23:09:15 damosoft Exp $ *}

<h2>{tr}Assign Permissions To Page{/tr}: {$page}</h2>
<h3>{tr}Current permissions for this page{/tr}:</h3>
<table class="normal">
<tr><td class="heading">{tr}Group{/tr}</td><td class="heading">{tr}Permission{/tr}</td><td class="heading">{tr}Action{/tr}</td></tr>
{cycle print=false values="even,odd"}
{section  name=pg loop=$page_perms}
<tr>
  <td class="{cycle advance=false}">{$page_perms[pg].groupName}</td>
  <td class="{cycle advance=false}">{$page_perms[pg].permName}</td>
  <td class="{cycle}">
    (<a class="link" href="tiki-pagepermissions.php?referer={$referer}&amp;action=remove&amp;objectName={$objectName}&amp;objectId={$objectId}&amp;objectType={$objectType}&amp;permType={$permType}&amp;page={$page}&amp;perm={$page_perms[pg].permName}&amp;group={$page_perms[pg].groupName}">{tr}Remove from page{/tr}</a>)
    (<a class="link" href="tiki-pagepermissions.php?referer={$referer}&amp;action=removestructure&amp;objectName={$objectName}&amp;objectId={$objectId}&amp;objectType={$objectType}&amp;permType={$permType}&amp;page={$page}&amp;perm={$page_perms[pg].permName}&amp;group={$page_perms[pg].groupName}">{tr}Remove from structure{/tr}</a>)
  </td></tr>
{sectionelse}
<tr><td>{tr}No individual permissions global permissions apply{/tr}</td></tr>
{/section}
</table>
<h3>{tr}Assign Permissions{/tr}</h3>
<form method="post" action="tiki-pagepermissions.php">
{tr}Assign{/tr}
<input type="hidden" name="page" value="{$page|escape}" />
<select name="perm">
{section name=prm loop=$perms}
<option value="{$perms[prm].permName|escape}">{$perms[prm].permName}</option>
{/section}
</select>
{tr}to group{/tr}
<select name="group">
{section name=grp loop=$groups}
<option value="{$groups[grp].groupName|escape}">{$groups[grp].groupName}</option>
{/section}
</select>
{tr}for{/tr}
<input type="submit" name="assign" value="{tr}this page{/tr}" />
{* CURRENT STRUCTURES CANNOT BE ASSIGNED PERMISSIONS
<input type="submit" name="assignstructure" value="{tr}this structure{/tr}" />
*}
</form>
<h2>{tr}Send email notifications when this page changes to{/tr}:</h2>
<form action="tiki-pagepermissions.php" method="post">
<input type="hidden" name="page" value="{$page|escape}" />
{tr}Add Email{/tr}: <input type="text" name="email" />
<input type="submit" name="addemail" value="{tr}Add{/tr}" />
</form>
<h3>{tr}Notifications{/tr}:</h3>
{section name=ix loop=$emails}
{$emails[ix]} (<a class="link" href="tiki-pagepermissions.php?page={$page}&amp;removeemail={$emails[ix]}">Delete</a>)<br/>
{/section}
