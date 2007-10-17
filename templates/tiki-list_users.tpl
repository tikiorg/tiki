{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-list_users.tpl,v 1.22.2.1 2007-10-17 20:36:10 niclone Exp $ *}
<h1><a class="pagetitle" href="tiki-list_users.php">{tr}User List{/tr}</a>

{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}UserList" target="tikihelp" class="tikihelp" title="{tr}User List{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>{/if}</h1>
<br />
{$cant_users} {if !$find}{tr}users registered{/tr}{else} {tr}Users{/tr} {tr}like{/tr} "{$find}"{/if}
<br /><br />
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-list_users.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table bgcolor="#ffffff" class="normal">
<tr>
  <td class="heading"><a href="tiki-list_users.php?offset={$offset}&amp;find={$find}&amp;sort_mode={if $sort_mode eq 'login_desc'}login_asc{else}login_desc{/if}" class="userlistheading" style="color: White;">{tr}User{/tr}</a>&nbsp;</td>
{if $prefs.feature_community_list_name eq 'y' and $prefs.user_show_realnames neq 'y'}
  <td class="heading"><a class="userlistheading" href="tiki-list_users.php?offset={$offset}&amp;find={$find}&amp;sort_mode={if $sort_mode eq 'pref:realName_desc'}pref:realName_asc{else}pref:realName_desc{/if}" style="color: White;">{tr}Real Name{/tr}</a>&nbsp;</td>
{/if}
{if $prefs.feature_score eq 'y'}{if $prefs.feature_community_list_score eq 'y'}
  <td class="heading"><a class="userlistheading" href="tiki-list_users.php?offset={$offset}&amp;find={$find}&amp;sort_mode={if $sort_mode eq 'score_desc'}score_asc{else}score_desc{/if}" style="color: White;">{tr}Score{/tr}</a>&nbsp;</td>
{/if}{/if}
{if $prefs.feature_community_list_country eq 'y'}
  <td class="heading"><a class="userlistheading" href="tiki-list_users.php?offset={$offset}&amp;find={$find}&amp;sort_mode={if $sort_mode eq 'pref:country_desc'}pref:country_asc{else}pref:country_desc{/if}" style="color: White;">{tr}Country{/tr}</a>&nbsp;</td>
{/if}
{if $prefs.feature_community_list_distance eq 'y'}<td class="heading">{tr}Distance (km){/tr}&nbsp;</td>{/if}
</tr>
{cycle values="odd,even" print=false}
{section name=changes loop=$listusers}
<tr>
  <td class="odd">&nbsp;{$listusers[changes].login|userlink}&nbsp;</td>
{if $prefs.feature_community_list_name eq 'y' and $prefs.user_show_realnames neq 'y'}
  <td class="odd">&nbsp;{$listusers[changes].realName}&nbsp;</td>
{/if}
{if $prefs.feature_score eq 'y'}{if $prefs.feature_community_list_score eq 'y'}
  <td class="odd">&nbsp;{$listusers[changes].score}&nbsp;</td>
{/if}{/if}
{if $prefs.feature_community_list_country eq 'y'}
	<td class="odd">
	{if $listuserscountry[changes] == "None" || $listuserscountry[changes] == "Other" || $listuserscountry[changes] == ""}
  {html_image file='img/flags/Other.gif' border='0' hspace='4' vspace='1' alt='{tr}Flag{/tr}' title='{tr}Flag{/tr}'}
  {else}
  {html_image file="img/flags/$listuserscountry[changes].gif" hspace='4' vspace='1' alt='{tr}Flag{/tr}' title='{tr}Flag{/tr}'}
  &nbsp;{tr}{$listuserscountry[changes]}{/tr}
  {/if}
	&nbsp;</td>
{/if}
{if $prefs.feature_community_list_distance eq 'y'}
	<td class="odd">&nbsp;{$listdistance[changes]}&nbsp;</td>
{/if}
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="userprevnext" href="tiki-list_users.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="userprevnext" href="tiki-list_users.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-list_users.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
