<h2>{tr}Admin users{/tr}</h2>
<div  align="center">
<table border="1" cellpadding="0" cellspacing="0" width="80%">
<tr>
<td class="textbl" bgcolor="#bbbbbb"><a class="link" href="tiki-adminusers.php?offset={$offset}&sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}name{/tr}</a></td>
<td class="textbl" bgcolor="#bbbbbb"><a class="link" href="tiki-adminusers.php?offset={$offset}&sort_mode={if $sort_mode eq 'email_desc'}email_asc{else}email_desc{/if}">{tr}email{/tr}</a></td>
<td class="textbl" bgcolor="#bbbbbb"><a class="link" href="tiki-adminusers.php?offset={$offset}&sort_mode={if $sort_mode eq 'lastLogin_desc'}lastLogin_asc{else}lastLogin_desc{/if}">{tr}last_login{/tr}</a></td>
<td class="textbl" bgcolor="#bbbbbb"><a class="link" href="tiki-adminusers.php?offset={$offset}&sort_mode={if $sort_mode eq 'changed_desc'}changed_asc{else}changed_desc{/if}">{tr}changed{/tr}</a></td>
<td class="textbl" bgcolor="#bbbbbb"><a class="link" href="tiki-adminusers.php?offset={$offset}&sort_mode={if $sort_mode eq 'versions_desc'}versions_asc{else}versions_desc{/if}">{tr}versions{/tr}</a></td>
<td class="textbl" bgcolor="#bbbbbb">{tr}action{/tr}</td>
</tr>
{section name=user loop=$users}
{if $smarty.section.user.index % 2}
<tr>
<td bgcolor="#dddddd" class="text">{$users[user].user}</td>
<td bgcolor="#dddddd" class="text">{$users[user].email}</td>
<td bgcolor="#dddddd" class="text">{$users[user].lastLogin|date_format:"%A %d of %B, %Y [%H:%M:%S]"}</td>
<td bgcolor="#dddddd" class="text">{$users[user].lastChanged}</td>
{if $users[user].versions}
<td bgcolor="#dddddd" class="text"><a href="tiki-userversions.php?ruser={$users[user].user}">{$users[user].versions}</a></td>
{else}
<td bgcolor="#dddddd" class="text">{$users[user].versions}</td>
{/if}
<td bgcolor="#dddddd" class="text"><a href="tiki-adminusers.php?action=delete&user={$users[user].user}">{tr}delete{/tr}</a></td>
</tr>
{else}
<tr>
<td class="text">{$users[user].user}</td>
<td class="text">{$users[user].email}</td>
<td class="text">{$users[user].lastLogin|date_format:"%A %d of %B, %Y [%H:%M:%S]"}</td>
<td class="text">{$users[user].lastChanged}</td>
{if $users[user].versions}
<td class="text"><a href="tiki-userversions.php?ruser={$users[user].user}">{$users[user].versions}</a></td>
{else}
<td class="text">{$users[user].versions}</td>
{/if}
<td class="text"><a href="tiki-adminusers.php?action=delete&user={$users[user].user}">{tr}delete{/tr}</a></td>
</tr>
{/if}
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a href="tiki-adminusers.php?&offset={$prev_offset}&sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a href="tiki-adminusers.php?&offset={$next_offset}&sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>

</div>
<h3>{tr}Add a new user{/tr}</h3>
<form action="tiki-adminusers.php" method="post">
<table>
<tr><td>{tr}User{/tr}:</td><td><input type="text" name="name" /></td></tr>
<tr><td>{tr}Pass{/tr}:</td><td><input type="password" name="pass" /></td></tr>
<tr><td>{tr}Again{/tr}:</td><td><input type="password" name="pass2" /></td></tr>
<tr><td>{tr}Email{/tr}:</td><td><input type="text" name="email" /></td></tr>
<tr><td colspan="2"><input type="submit" name="newuser" value="{tr}Add{/tr}" /></td></tr>
</table>
</form>
