{*Smarty template*}
<a class="pagetitle" href="tiki-newsreader_servers.php">{tr}Configure news servers{/tr}</a><br/><br/>
{include file=tiki-mytiki_bar.tpl}
<h3>{tr}Select a news server to browse{/tr}</h3>
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-newsreader_servers.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading" width="80%"><a class="tableheading" href="tiki-newsreader_servers.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'server_desc'}server_asc{else}server_desc{/if}">{tr}server{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle}"><a class="link" href="tiki-newsreader_groups.php?serverId={$channels[user].serverId}">{$channels[user].server}</a>
 [<a class="link" href="tiki-newsreader_servers.php?serverId={$channels[user].serverId}">edit</a>|<a class="link" href="tiki-newsreader_servers.php?remove={$channels[user].serverId}">del</a>]
</td>
</tr>
{/section}
</table>
<div class="mini">
<div align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-newsreader_servers.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-newsreader_servers.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-newsreader_servers.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>


<h3>{tr}Add or edit a news server{/tr}</h3>
<form action="tiki-newsreader_servers.php" method="post">
<input type="hidden" name="serverId" value="{$serverId}" />
<table class="normal">
<tr>
  <td class="formcolor">{tr}News server{/tr}</td>
  <td class="formcolor"><input type="text" name="server" value="{$info.server}" />
  {tr}port{/tr}:<input type="text" size="4" name="port" value="{$info.port}" /></td>
  </td>
</tr>
<tr>
  <td class="formcolor">{tr}User{/tr}</td>
  <td class="formcolor"><input type="text" name="username" value="{$info.username}" /></td>
</tr>
<tr>
  <td class="formcolor">{tr}Password{/tr}</td>
  <td class="formcolor"><input type="text" name="password" value="{$info.password}" /></td>
</tr>
<tr>
  <td class="formcolor">&nbsp;</td>
  <td class="formcolor"><input type="submit" name="save" value="{tr}save{/tr}" /></td>
</tr>


</table>
</form>