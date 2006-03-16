{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-list_users.tpl,v 1.11 2006-03-16 13:43:12 sylvieg Exp $ *}
<h1><a class="pagetitle" href="tiki-list_users.php">{tr}User List{/tr}</a>

{if $feature_help eq 'y'}
<a href="{$helpurl}UserList" target="tikihelp" class="tikihelp" title="{tr}User List{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>{/if}</h1>
<br />
{$cant_users} {if !$find}{tr}users registered{/tr}{else} {tr}users{/tr} {tr}like{/tr} "{$find}"{/if}
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
<table bgcolor="#ffffff" class="normal">
<tr>
  <td class="heading"><a href="tiki-list_users.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'login_desc'}login_asc{else}login_desc{/if}" class="userlistheading" style="color: White;">{tr}User{/tr}</a>&nbsp;</td>
  <td class="heading"><a class="userlistheading" href="tiki-list_users.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'realName_desc'}realName_asc{else}realName_desc{/if}" style="color: White;">{tr}Real Name{/tr}</a>&nbsp;</td>
{if $feature_score eq 'y'}
  <td class="heading"><a class="userlistheading" href="tiki-list_users.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'score_desc'}score_asc{else}score_desc{/if}" style="color: White;">{tr}Score{/tr}</a>&nbsp;</td>
{/if}
	<td class="heading">{tr}Country{/tr}&nbsp;</td>
  <td class="heading">{tr}Distance (km){/tr}&nbsp;</td>
</tr>
{cycle values="odd,even" print=false}
{section name=changes loop=$listusers}
<tr>
  <td class="odd">&nbsp;{$listusers[changes].login|userlink}&nbsp;</td>
  <td class="odd">&nbsp;{$listusers[changes].realName}&nbsp;</td>
{if $feature_score eq 'y'}
  <td class="odd">&nbsp;{$listusers[changes].score}&nbsp;</td>
{/if}
	<td class="odd">
	{if $listuserscountry[changes] == "None" || $listuserscountry[changes] == "Other" || $listuserscountry[changes] == ""}
  {html_image file='img/flags/Other.gif' border='0' hspace='4' vspace='1' alt='{tr}flag{/tr}' title='{tr}flag{/tr}'}
  {else}
  {html_image file="img/flags/$listuserscountry[changes].gif" hspace='4' vspace='1' alt='{tr}flag{/tr}' title='{tr}flag{/tr}'}
  &nbsp;{tr}{$listuserscountry[changes]}{/tr}
  {/if}
	&nbsp;</td>
	<td class="odd">&nbsp;{$listdistance[changes]}&nbsp;</td>
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
