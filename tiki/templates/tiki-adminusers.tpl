<h2>{tr}Admin users{/tr}</h2>
<div align="center">
<table border="1" cellpadding="0" cellspacing="0" width="97%">
<tr><td>Find</td>
   <td>
   <form method="get" action="tiki-adminusers.php">
     <input type="text" name="find" />
     <input type="submit" value="find" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table border="1" cellpadding="0" cellspacing="0" width="97%">
<tr>
<td class="heading"><a class="link" href="tiki-adminusers.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'login_desc'}login_asc{else}login_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-adminusers.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'email_desc'}email_asc{else}email_desc{/if}">{tr}email{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-adminusers.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastLogin_desc'}lastLogin_asc{else}lastLogin_desc{/if}">{tr}last_login{/tr}</a></td>
<td class="heading">{tr}Groups{/tr}</td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$users}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$users[user].user}</td>
<td class="odd">{$users[user].email}</td>
<td class="odd">{$users[user].lastLogin|date_format:"%A %d of %B, %Y [%H:%M:%S]"}</td>
<td class="odd">
{section name=grs loop=$users[user].groups}
{$users[user].groups[grs]}{if $users[user].groups[grs] != "Anonymous"}(<a class="link" href="tiki-adminusers.php?ruser={$users[user].user}&amp;action=removegroup&amp;group={$users[user].groups[grs]}">x</a>){/if}&nbsp;
{/section}
</td>
<td class="odd"><a class="link" href="tiki-adminusers.php?action=delete&amp;user={$users[user].user}">{tr}delete{/tr}</a>
                                   <a class="link" href="tiki-assignuser.php?assign_user={$users[user].user}">{tr}assign group{/tr}</a></td>
</tr>
{else}
<tr>
<td class="even">{$users[user].user}</td>
<td class="even">{$users[user].email}</td>
<td class="even">{$users[user].lastLogin|date_format:"%A %d of %B, %Y [%H:%M:%S]"}</td>
<td class="even">
{section name=grs loop=$users[user].groups}
{$users[user].groups[grs]}{if $users[user].groups[grs] != "Anonymous"}(<a class="link" href="tiki-adminusers.php?ruser={$users[user].user}&amp;action=removegroup&amp;group={$users[user].groups[grs]}">x</a>){/if}&nbsp;
{/section}
</td>
<td class="even"><a class="link" href="tiki-adminusers.php?action=delete&amp;user={$users[user].user}">{tr}delete{/tr}</a>
                 <a class="link" href="tiki-assignuser.php?assign_user={$users[user].user}">{tr}assign group{/tr}</a></td>
</tr>
{/if}
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="link" href="tiki-adminusers.php?&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="link" href="tiki-adminusers.php?&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
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
