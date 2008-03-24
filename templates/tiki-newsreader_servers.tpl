{*Smarty template*}
<h1><a class="pagetitle" href="tiki-newsreader_servers.php">{tr}Configure news servers{/tr}</a>

{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Newsreader" target="tikihelp" class="tikihelp" title="{tr}Configure Newsreader{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>
{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-newsreader_servers.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}configure newsreader server tpl{/tr}">
<img src="img/icons/info.gif" border="0" width="16" height="16" alt='{tr}Edit Template{/tr}' /></a>
{/if}</h1>



{if $prefs.feature_ajax ne 'y' && $prefs.feature_mootools ne 'y'}
{include file=tiki-mytiki_bar.tpl}
{/if}
<h2>{tr}Select a news server to browse{/tr}</h2>
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-newsreader_servers.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-newsreader_servers.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'server_desc'}server_asc{else}server_desc{/if}">{tr}server{/tr}</a></td>
<td  class="heading">&nbsp;</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">
	<a class="link" href="tiki-newsreader_groups.php?serverId={$channels[user].serverId}" title="{tr}Read the news{/tr}">{$channels[user].server}</a>
</td>
<td class="{cycle}">
	<a class="link" href="tiki-newsreader_servers.php?serverId={$channels[user].serverId}">{icon _id='page_edit'}</a>
	<a class="link" href="tiki-newsreader_servers.php?remove={$channels[user].serverId}">{icon _id='cross' alt='{tr}Del{/tr}' title='{tr}Delete{/tr}'}</a>
</td>
</tr>
{/section}
</table>
<div class="mini">
<div align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-newsreader_servers.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-newsreader_servers.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-newsreader_servers.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>


<h2>{tr}Add or edit a news server{/tr}</h2>
<form action="tiki-newsreader_servers.php" method="post">
<input type="hidden" name="serverId" value="{$serverId|escape}" />
<table class="normal">
<tr>
  <td class="formcolor">{tr}News server{/tr}</td>
  <td class="formcolor"><input type="text" name="server" value="{$info.server|escape}" />
  {tr}port{/tr}:<input type="text" size="4" name="port" value="{$info.port|escape}" /></td>
</tr>
<tr>
  <td class="formcolor">{tr}User{/tr}</td>
  <td class="formcolor"><input type="text" name="news_username" value="{$info.username|escape}" /></td>
</tr>
<tr>
  <td class="formcolor">{tr}Password{/tr}</td>
  <td class="formcolor"><input type="password" name="password" value="{$info.password|escape}" /></td>
</tr>
<tr>
  <td class="formcolor">&nbsp;</td>
  <td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td>
</tr>


</table>
</form>
