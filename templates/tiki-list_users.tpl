{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-list_users.tpl,v 1.8 2005-05-18 11:03:18 mose Exp $ *}
<h1><a class="pagetitle" href="tiki-list_users.php">{tr}User List{/tr}</a>

{if $feature_help eq 'y'}
<a href="{$helpurl}UserList" target="tikihelp" class="tikihelp" title="{tr}User List{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}'></a>{/if}</h1>
<br /><br />
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-list_users.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<br />
<table class="userlist">
<tr>
  <td class="userlistheading"><a class="userlistheading" href="tiki-list_users.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'login_desc'}login_asc{else}login_desc{/if}">{tr}User{/tr}</a>&nbsp;</td>
  <td class="userlistheading"><a class="userlistheading" href="tiki-list_users.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'realName_desc'}realName_asc{else}realName_desc{/if}">{tr}Real Name{/tr}</a>&nbsp;</td>
{if $feature_score eq 'y'}
  <td class="userlistheading"><a class="userlistheading" href="tiki-list_users.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'score_desc'}score_asc{else}score_desc{/if}">{tr}Score{/tr}</a>&nbsp;</td>
{/if}
	<td class="userlistheading"><a class="userlistheading" href="tiki-list_users.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'country_desc'}country_asc{else}country_desc{/if}">{tr}Country{/tr}</a>&nbsp;</td>
  <td class="userlistheading">{tr}Distance (km){/tr}&nbsp;</td>
</tr>
{cycle values="odd,even" print=false}
{section name=changes loop=$listusers}
<tr>
  <td class="userlist{cycle advance=false}">&nbsp;{$listusers[changes].login|userlink}&nbsp;</td>
  <td class="userlist{cycle advance=false}">&nbsp;{$listusers[changes].realName}&nbsp;</td>
{if $feature_score eq 'y'}
  <td class="userlist{cycle advance=false}">&nbsp;{$listusers[changes].score}&nbsp;</td>
{/if}
	<td class="userlist{cycle advance=false}">&nbsp;
	{if $listuserscountry[changes] == "None" || $listuserscountry[changes] == "Other" || $listuserscountry[changes] == ""}
  <img src="img/flags/Other.gif" border="0" width="20" height="13" alt='' />
  {else}
  <img src="img/flags/{$listuserscountry[changes]}.gif" alt='' />
  &nbsp;{tr}{$listuserscountry[changes]}{/tr}
  {/if}
	&nbsp;</td>
	<td class="userlist{cycle advance=true}">&nbsp;{$listdistance[changes]}&nbsp;</td>
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br />
<div class="mini" align="center">
{if $prev_offset >= 0}
[<a class="userprevnext" href="tiki-list_users.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="userprevnext" href="tiki-list_users.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-list_users.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
