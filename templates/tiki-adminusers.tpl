<a href="tiki-adminusers.php" class="pagetitle">{tr}Admin users{/tr}</a><br/><br/>
<h3>{tr}Add a new user{/tr}</h3>
<form action="tiki-adminusers.php" method="post" enctype="multipart/form-data">
<table class="normal">
<tr><td class="formcolor">{tr}User{/tr}:</td><td class="formcolor"><input type="text" name="name" /></td></tr>
<tr><td class="formcolor">{tr}Pass{/tr}:</td><td class="formcolor"><input type="password" name="pass" /></td></tr>
<tr><td class="formcolor">{tr}Again{/tr}:</td><td class="formcolor"><input type="password" name="pass2" /></td></tr>
<tr><td class="formcolor">{tr}Email{/tr}:</td><td class="formcolor"><input type="text" name="email" size="30" /></td></tr>
<tr><td class="formcolor">{tr}Batch upload (CSV file){/tr}:</td><td class="formcolor"><input type="file" name="csvlist" /><br>Overwrite: <input type="checkbox" name="overwrite" checked></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="newuser" value="{tr}Add{/tr}" /></td></tr>
</table>
</form>
<br/>
<table class="normal">
<tr><td class="formcolor"><a class="link" href="javascript:genPass('genepass','pass1','pass2');">{tr}Generate a password{/tr}</a></td>
<td class="formcolor"><input id='genepass' type="text" /></td></tr>
</table>
<br/>
{if (($added != "") || ($discarded != "")) }
	<h3>{tr}Batch Upload Results{/tr}</h3>
	Added users: {$added}
	{if ($discarded != "") }
		 - Rejected users: {$discarded}<br><br>
		<table class="normal">
			<tr><td class="heading">Username</td><td class="heading">Reason</td></tr>
			{section name=reject loop=$discardlist}
				<tr><td class="odd">{$discardlist[reject].name}</td><td class="odd">{$discardlist[reject].reason}</td></tr>
			{/section}
		</table>
	{/if}
{/if}

<h3>
<h3>{tr}Users{/tr}</h3>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-adminusers.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-adminusers.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'login_desc'}login_asc{else}login_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-adminusers.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'email_desc'}email_asc{else}email_desc{/if}">{tr}email{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-adminusers.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'currentLogin_desc'}currentLogin_asc{else}currentLogin_desc{/if}">{tr}last_login{/tr}</a></td>
<td class="heading">{tr}Groups{/tr}</td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$users}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$users[user].user}</td>
<td class="odd">{$users[user].email}</td>
<td class="odd">{$users[user].currentLogin|tiki_long_datetime}</td>
<td class="odd">
{foreach from=$users[user].groups item=grs}
{$grs}
{if $grs != "Anonymous"}
(<a class="link"href="tiki-adminusers.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;user={$users[user].user}&amp;action=removegroup&amp;group={$grs}">x</a>)
{/if}&nbsp;
{/foreach}
<td class="odd"><a class="link" href="tiki-adminusers.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;action=delete&amp;user={$users[user].user}">{tr}delete{/tr}</a><br/>
                                   <a class="link" href="tiki-assignuser.php?assign_user={$users[user].user}">{tr}assign group{/tr}</a><br/>
                                   <a class="link" href="tiki-user_preferences.php?view_user={$users[user].user}">{tr}view info{/tr}</a>
                                   </td>
</tr>
{else}
<tr>
<td class="even">{$users[user].user}</td>
<td class="even">{$users[user].email}</td>
<td class="even">{$users[user].currentLogin|tiki_long_datetime}</td>
<td class="even">
{foreach from=$users[user].groups item=grs}
{$grs}
{if $grs != "Anonymous"}
(<a class="link"href="tiki-adminusers.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;user={$users[user].user}&amp;action=removegroup&amp;group={$grs}">x</a>)
{/if}&nbsp;
{/foreach}
<td class="even"><a class="link" href="tiki-adminusers.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;action=delete&amp;user={$users[user].user}">{tr}delete{/tr}</a><br/>
                 <a class="link" href="tiki-assignuser.php?assign_user={$users[user].user}">{tr}assign group{/tr}</a><br/>
                 <a class="link" href="tiki-user_preferences.php?view_user={$users[user].user}">{tr}view info{/tr}</a></td>
</tr>
{/if}
{/section}
</table>
<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-adminusers.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-adminusers.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-adminusers.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}

</div>
</div>

