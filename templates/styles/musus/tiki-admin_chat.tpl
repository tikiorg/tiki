<h1><a class="pagetitle" href="tiki-admin_chat.php">{tr}Chat Administration{/tr}</a></h1>

<!-- the help link info -->
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=ChatAdmin" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}ChatAdmin{/tr}"><img border="0" alt="{tr}Help{/tr}" src="img/icons/help.gif" /></a>
{/if}

<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admin_chat.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}ChatAdmin tpl{/tr}"><img border="0"  alt="{tr}Edit template{/tr}" src="img/icons/info.gif" /></a>
{/if}

<!-- begin -->


<h2>{tr}Create/edit channel{/tr}</h2>
<form action="tiki-admin_chat.php" method="post">
<input type="hidden" name="channelId" value="{$channelId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="name" value="{$name|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><textarea name="description" rows="4" cols="40">{$description|escape}</textarea></td></tr>
<tr><td class="formcolor">{tr}Active{/tr}:</td><td class="formcolor"><input type="checkbox" name="active" {if $active eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Refresh rate{/tr}:</td><td class="formcolor">
<select name="refresh">
<option value="500" {if $refresh eq 500}selected="selected"{/if}>{tr}Half a second{/tr}</option>
<option value="1000" {if $refresh eq 1000}selected="selected"{/if}>1 {tr}second{/tr}</option>
<option value="2000" {if $refresh eq 2000}selected="selected"{/if}>2 {tr}seconds{/tr}</option>
<option value="3000" {if $refresh eq 3000}selected="selected"{/if}>3 {tr}seconds{/tr}</option>
<option value="4000" {if $refresh eq 4000}selected="selected"{/if}>4 {tr}seconds{/tr}</option>
<option value="5000" {if $refresh eq 5000}selected="selected"{/if}>5 {tr}seconds{/tr}</option>
<option value="6000" {if $refresh eq 6000}selected="selected"{/if}>6 {tr}seconds{/tr}</option>
<option value="7000" {if $refresh eq 7000}selected="selected"{/if}>7 {tr}seconds{/tr}</option>
<option value="8000" {if $refresh eq 8000}selected="selected"{/if}>8 {tr}seconds{/tr}</option>
<option value="9000" {if $refresh eq 9000}selected="selected"{/if}>9 {tr}seconds{/tr}</option>
<option value="10000" {if $refresh eq 10000}selected="selected"{/if}>10 {tr}seconds{/tr}</option>
</select>
</td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<h2>{tr}Chat channels{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_chat.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_chat.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}bane_asc{else}name_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_chat.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}description{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_chat.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'active_desc'}active_asc{else}active_desc{/if}">{tr}active{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_chat.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'refresh_desc'}refresh_asc{else}refresh_desc{/if}">{tr}refresh{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$channels}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$channels[user].name}</td>
<td class="odd">{$channels[user].description}</td>
<td class="odd">{$channels[user].active}</td>
<td class="odd">{$channels[user].refresh}</td>
<td class="odd">
   &nbsp;&nbsp;<a class="link" href="tiki-admin_chat.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].channelId}" 
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this channel?{/tr}')" 
title="{tr}Click here to delete this channel{/tr}"><img border="0" alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>&nbsp;&nbsp;
   <a class="link" href="tiki-admin_chat.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;channelId={$channels[user].channelId}"><img border="0" alt="{tr}Edit{/tr}" src="img/icons/edit.gif" /></a>
</td>
</tr>
{else}
<tr>
<td class="even">{$channels[user].name}</td>
<td class="even">{$channels[user].description}</td>
<td class="even">{$channels[user].active}</td>
<td class="even">{$channels[user].refresh}</td>
<td class="even">
   &nbsp;&nbsp;<a class="link" href="tiki-admin_chat.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].channelId}" 
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this channel?{/tr}')" 
title="{tr}Click here to delete this channel{/tr}"><img border="0" alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>&nbsp;&nbsp;
   <a class="link" href="tiki-admin_chat.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;channelId={$channels[user].channelId}"><img alt="{tr}Edit{/tr}" border="0" src="img/icons/edit.gif" /></a>
</td>
</tr>
{/if}
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_chat.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_chat.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>
</div>
