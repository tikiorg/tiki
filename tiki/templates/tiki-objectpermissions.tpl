<h2>{tr}Assign permissions to {$objectName}{/tr}</h2>
<a href="{$referer}" class="link">{tr}back{/tr}</a>
<div>
<h3>{tr}Current permissions for this object{/tr}:</h3>
<table class="normal">
<tr><td class="heading">{tr}group{/tr}</td><td class="heading">{tr}permission{/tr}</td><td class="heading">{tr}action{/tr}</td></tr>
{section  name=pg loop=$page_perms}
<tr><td class="odd">{$page_perms[pg].groupName}</td><td class="odd">{$page_perms[pg].permName}</td><td class="odd">(<a class="link" href="tiki-objectpermissions.php?referer={$referer}&amp;action=remove&amp;objectName={$objectName}&amp;objectId={$objectId}&amp;objectType={$objectType}&amp;permType={$permType}&amp;page={$page}&perm={$page_perms[pg].permName}&group={$page_perms[pg].groupName}">remove</a>)</td></tr>
{sectionelse}
<tr><td>{tr}No indivual permissions global permissions apply{/tr}</td></tr>
{/section}
</table>
<h3>{tr}Assign permissions to this object{/tr}</h3>
<form method="post" action="tiki-objectpermissions.php">
<input type="hidden" name="page" value="{$page}" />
<input type="hidden" name="referer" value="{$referer}" />
<input type="hidden" name="objectName" value="{$objectName}" />
<input type="hidden" name="objectType" value="{$objectType}" />
<input type="hidden" name="objectId" value="{$objectId}" />
<input type="hidden" name="permType" value="{$permType}" />
<input type="submit" name="assign" value="{tr}assign{/tr}" />

<select name="perm">
{section name=prm loop=$perms}
<option value="{$perms[prm].permName}">{$perms[prm].permName}</option>
{/section}
</select>
to group:
<select name="group">
{section name=grp loop=$groups}
<option value="{$groups[grp].groupName}">{$groups[grp].groupName}</option>
{/section}
</select>
</form>
</div>
