{* $Header: /cvsroot/tikiwiki/_mods/templates/sdl_collection/templates/tiki-adminusers.tpl,v 1.1 2004-05-09 23:07:57 damosoft Exp $ *}

<a href="tiki-adminusers.php" class="pagetitle">{tr}Admin Users{/tr}</a>
<!-- the help link info -->
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=UserAdministrationScreen" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}admin users{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}

<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-adminusers.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin users tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='edit tpl' /></a>{/if}

<!-- begin -->

<br/><br/>
<h2>{tr}Add a new user{/tr}</h2>

<form action="tiki-adminusers.php" method="post" enctype="multipart/form-data">
<table class="normal">
<tr class="formcolor"><td>{tr}User{/tr}:</td><td><input type="text" name="name" /></td></tr>
<tr class="formcolor"><td>{tr}Password{/tr}:</td><td><input type="password" name="pass" /></td></tr>
<tr class="formcolor"><td>{tr}Again{/tr}:</td><td><input type="password" name="pass2" /></td></tr>
<tr class="formcolor"><td>{tr}Email{/tr}:</td><td><input type="text" name="email" size="30" /></td></tr>
<tr class="formcolor"><td>{tr}Batch upload (CSV file){/tr}:</td><td><input type="file" name="csvlist" /><br/>{tr}Overwrite{/tr}: <input type="checkbox" name="overwrite" checked></td></tr>
<tr class="formcolor"><td>&nbsp;</td><td><input type="submit" name="newuser" value="{tr}Add{/tr}" /></td></tr>
</table>
</form>
<br />
<table class="normal">
<tr class="formcolor"><td><a class="link" href="javascript:genPass('genepass','pass','pass2');">{tr}Generate a password{/tr}</a></td>
<td><input id='genepass' type="text" /></td></tr>
</table>
<br />
{if (($added != "") || ($discarded != "")) }
	<h2>{tr}Batch Upload Results{/tr}</h2>
	{tr}Added users{/tr}: {$added}
	{if ($discarded != "") }
		 - {tr}Rejected users{/tr}: {$discarded}<br/><br/>
		<table class="normal">
			<tr class="heading"><td>{tr}Username{/tr}</td><td>{tr}Reason{/tr}</td></tr>
			{section name=reject loop=$discardlist}
				<tr class="odd"><td>{$discardlist[reject].name}</td><td>{$discardlist[reject].reason}</td></tr>
			{/section}
		</table>
	{/if}
{/if}

<h2>{tr}Users{/tr}</h2>
<div align="center">
<table class="findtable">
<tr><td>{tr}Search{/tr}</td>
   <td>
   <form method="get" action="tiki-adminusers.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Go{/tr}" name="search" />
		 {tr}Number of displayed rows{/tr}
		 <input type="text" size="4" name="numrows" value="{$numrows|escape}">
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr >
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
(<a class="link" href="tiki-adminusers.php?offset={$offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}&amp;user={$users[user].user}&amp;action=removegroup&amp;group={$grs}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this group?{/tr}')">Delete</a>)
{/if}
{/foreach}
<td class="{cycle}" nowrap="nowrap">{if $users[user].user ne 'admin'}<a class="link" href="tiki-adminusers.php?offset={$offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}&amp;action=delete&amp;user={$users[user].user}"  title="{tr}Remove{/tr}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this user?{/tr}')"><img border="0" alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>{/if}
<a class="link" href="tiki-assignuser.php?assign_user={$users[user].user}" title="{tr}Assign Group{/tr}"><img border="0" alt="{tr}Assign Group{/tr}" src="img/icons/key.gif" /></a>
<a class="link" href="tiki-user_preferences.php?view_user={$users[user].user}" title="{tr}Configure/Options{/tr}"><img border="0" alt="{tr}Configure/Options{/tr}" src="img/icons/config.gif" /></a>
</td>
</tr>
{/section}
</table>
<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-adminusers.php?find={$find}&amp;offset={$prev_offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>] 
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
 [<a class="prevnext" href="tiki-adminusers.php?find={$find}&amp;offset={$next_offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-adminusers.php?find={$find}&amp;offset={$selector_offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>
{/section}
{/if}

</div>
</div>
