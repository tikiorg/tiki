{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
<h1>{tr}Assign permissions to {/tr}{tr}{$objectType}{/tr} {$objectName}</h1>
<a href="{$referer}" class="linkbut">{tr}back{/tr}</a>
<div>
<h2>{tr}Current permissions for this object{/tr}:</h2>
<table class="normal">
<tr><td class="heading">{tr}group{/tr}</td><td class="heading">{tr}permission{/tr}</td><td class="heading">{tr}action{/tr}</td></tr>
{cycle values="odd,even" print=false}
{section  name=pg loop=$page_perms}
<tr><td class="{cycle advance=false}">{$page_perms[pg].groupName}</td>
<td class="{cycle advance=false}">{$page_perms[pg].permName}</td>
<td class="{cycle advance=true}"><a class="link" href="tiki-workspaces_objectpermissions.php?referer={$referer|escape:"url"}&amp;action=remove&amp;objectName={$objectName}&amp;resourceIdName={$resourceIdName}&amp;objectType={$objectType}&amp;permType={$permType}&amp;page={$page|escape:"url"}&amp;perm={$page_perms[pg].permName}&amp;group={$page_perms[pg].groupName}" title="{tr}Delete{/tr}"><img src="img/icons2/delete.gif" width="12" height="12" alt="{tr}delete{/tr}" border="0"></a></td></tr>
{sectionelse}
<tr><td colspan="3" class="odd">{tr}No individual permissions global permissions apply{/tr}</td></tr>
{/section}
</table>
<h2>{tr}Assign permissions to this object{/tr}</h2>
<form method="post" action="tiki-workspaces_objectpermissions.php">
<input type="hidden" name="page" value="{$page|escape}" />
<input type="hidden" name="referer" value="{$referer|escape}" />
<input type="hidden" name="objectName" value="{$objectName|escape}" />
<input type="hidden" name="objectType" value="{$objectType|escape}" />
<input type="hidden" name="resourceIdName" value="{$resourceIdName|escape}" />
<input type="hidden" name="permType" value="{$permType|escape}" />
<input type="submit" name="assign" value="{tr}assign{/tr}" />

<select name="perm">
{section name=prm loop=$perms}
<option value="{$perms[prm].permName|escape}">{$perms[prm].permName}</option>
{/section}
</select>
{tr}to group{/tr}:
<select name="group">
{section name=grp loop=$groups}
<option value="{$groups[grp].groupName|escape}" {if $groupName eq $groups[grp].groupName }selected="selected"{/if}>{$groups[grp].groupName}</option>
{/section}
</select>
</form>
</div>
