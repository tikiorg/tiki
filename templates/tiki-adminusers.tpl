{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-adminusers.tpl,v 1.21 2003-08-01 10:31:09 redflo Exp $ *}

<a href="tiki-adminusers.php" class="pagetitle">{tr}Admin users{/tr}</a><br/><br/>
<h3>{tr}Add a new user{/tr}</h3>
<form action="tiki-adminusers.php" method="post" enctype="multipart/form-data">
<table class="normal">
<tr><td class="formcolor">{tr}User{/tr}:</td><td class="formcolor"><input type="text" name="name" /></td></tr>
<tr><td class="formcolor">{tr}Pass{/tr}:</td><td class="formcolor"><input type="password" name="pass" /></td></tr>
<tr><td class="formcolor">{tr}Again{/tr}:</td><td class="formcolor"><input type="password" name="pass2" /></td></tr>
<tr><td class="formcolor">{tr}Email{/tr}:</td><td class="formcolor"><input type="text" name="email" size="30" /></td></tr>
<tr><td class="formcolor">{tr}Batch upload (CSV file){/tr}:</td><td class="formcolor"><input type="file" name="csvlist" /><br>{tr}Overwrite{/tr}: <input type="checkbox" name="overwrite" checked></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="newuser" value="{tr}Add{/tr}" /></td></tr>
</table>
</form>
<br/>
<table class="normal">
<tr><td class="formcolor"><a class="link" href="javascript:genPass('genepass','pass','pass2');">{tr}Generate a password{/tr}</a></td>
<td class="formcolor"><input id='genepass' type="text" /></td></tr>
</table>
<br/>
{if (($added != "") || ($discarded != "")) }
	<h3>{tr}Batch Upload Results{/tr}</h3>
	{tr}Added users{/tr}: {$added}
	{if ($discarded != "") }
		 - {tr}Rejected users{/tr}: {$discarded}<br><br>
		<table class="normal">
			<tr><td class="heading">{tr}Username{/tr}</td><td class="heading">{tr}Reason{/tr}</td></tr>
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
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
		 {tr}Number of displayed rows{/tr}
		 <input type="text" size="4" name="numrows" value="{$numrows|escape}">
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-adminusers.php?offset={$offset}&amp;numrows={$numrows}&amp;sort_mode={if $sort_mode eq 'login_desc'}login_asc{else}login_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-adminusers.php?offset={$offset}&amp;numrows={$numrows}&amp;sort_mode={if $sort_mode eq 'email_desc'}email_asc{else}email_desc{/if}">{tr}Email{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-adminusers.php?offset={$offset}&amp;numrows={$numrows}&amp;sort_mode={if $sort_mode eq 'currentLogin_desc'}currentLogin_asc{else}currentLogin_desc{/if}">{tr}Last login{/tr}</a></td>
<td class="heading">{tr}Groups{/tr}</td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle print=false values="even,odd"}
{section name=user loop=$users}
<tr>
<td class="{cycle advance=false}">{$users[user].user}</td>
<td class="{cycle advance=false}">{$users[user].email}</td>
<td class="{cycle advance=false}">{if $users[user].currentLogin eq ''}{tr}Never{/tr}{else}{$users[user].currentLogin|dbg|tiki_long_datetime}{/if}</td>
<td class="{cycle advance=false}">
{foreach from=$users[user].groups item=grs}
{$grs}
{if $grs != "Anonymous"}
(<a class="link"href="tiki-adminusers.php?offset={$offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}&amp;user={$users[user].user}&amp;action=removegroup&amp;group={$grs}">x</a>)
{/if}&nbsp;
{/foreach}
<td class="{cycle}">{if $users[user].user ne 'admin'}<a class="link" href="tiki-adminusers.php?offset={$offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}&amp;action=delete&amp;user={$users[user].user}">{tr}delete{/tr}</a><br/>{/if}
                                   <a class="link" href="tiki-assignuser.php?assign_user={$users[user].user}">{tr}assign group{/tr}</a><br/>
                                   <a class="link" href="tiki-user_preferences.php?view_user={$users[user].user}">{tr}view info{/tr}</a>
                                   </td>
</tr>
{/section}
</table>
<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-adminusers.php?find={$find}&amp;offset={$prev_offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-adminusers.php?find={$find}&amp;offset={$next_offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-adminusers.php?find={$find}&amp;offset={$selector_offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}

</div>
</div>

