<h2>{tr}Assign permissions to page: {$page}{/tr}</h2>
<h3>{tr}Current permissions for this page{/tr}:</h3>
<table class="normal">
<tr><td class="heading">{tr}group{/tr}</td><td class="heading">{tr}permission{/tr}</td><td class="heading">{tr}action{/tr}</td></tr>
{section  name=pg loop=$page_perms}
<tr><td class="odd">{$page_perms[pg].groupName}</td><td class="odd">{$page_perms[pg].permName}</td><td class="odd">(<a class="link" href="tiki-objectpermissions.php?referer={$referer}&amp;action=remove&amp;objectName={$objectName}&amp;objectId={$objectId}&amp;objectType={$objectType}&amp;permType={$permType}&amp;page={$page}&perm={$page_perms[pg].permName}&group={$page_perms[pg].groupName}">remove</a>)</td></tr>
{sectionelse}
<tr><td>{tr}No indivual permissions global permissions apply{/tr}</td></tr>
{/section}
</table>
<h3>{tr}Assign permissions to this page{/tr}</h3>
<form method="post" action="tiki-pagepermissions.php">
<input type="hidden" name="page" value="{$page}" />
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
<h2>Send email notifications when this page changes to:</h2>
<form action="tiki-pagepermissions.php" method="post">
<input type="hidden" name="page" value="{$page}" />
add email: <input type="text" name="email" />
<input type="submit" name="addemail" value="add" />
</form>
<h3>Notifications:</h3>
{section name=ix loop=$emails}
{$emails[ix]} (<a class="link" href="tiki-pagepermissions.php?page={$page}&amp;removeemail={$emails[ix]}">x</a>)<br/>
{/section}

