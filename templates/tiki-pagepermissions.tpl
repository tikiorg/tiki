<h2>{tr}Assign permissions to page: {$page}{/tr}</h2>
<div>
<h3>{tr}Current permissions for this page{/tr}:</h3>
<table>
{section name=pg loop=$page_perms}
<tr><td>{tr}Group{/tr}: {$page_perms[pg].groupName} {tr}Perm{/tr}: {$page_perms[pg].permName} (<a href="tiki-pagepermissions.php?action=remove&page={$page}&perm={$page_perms[pg].permName}&group={$page_perms[pg].groupName}">remove</a>)</td></tr>
{sectionelse}
<tr><td>{tr}No indivual permissions global permissions to all pages apply{/tr}</td></tr>
{/section}
</table>
<h3>{tr}Assign permissions to thispage{/tr}</h3>
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
</div>
