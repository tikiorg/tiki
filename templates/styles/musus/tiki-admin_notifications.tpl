<a class="pagetitle" href="tiki-admin_notifications.php">{tr}EMail notifications{/tr}</a>

<!-- the help link info -->
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=EmailNotificationsAdmin" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}admin Email Notifications{/tr}"><img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}

<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admin_notifications.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin notifications tpl{/tr}"><img border='0' src='img/icons/info.gif' alt='edit tpl' /></a>{/if}

<!-- begin -->

<br /><br />
<h2>{tr}Add notification{/tr}</h2>
<table>
<form action="tiki-admin_notifications.php" method="post">
     <input type="hidden" name="find" value="{$find|escape}" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="offset" value="{$offset|escape}" />
<tr><td>{tr}Event{/tr}:</td>
    <td>
    <select name="event">
      <option value="user_registers">{tr}A user registers{/tr}</option>
      <option value="article_submitted">{tr}A user submits an article{/tr}</option>
      <option value="wiki_page_changes">{tr}Any wiki page is changed{/tr}</option>
    </select>
    </td>
</tr> 
<tr><td>{tr}Email{/tr}:</td>        
    <td>
      <input type="text" id='femail' name="email" />
      <a href="#" onClick="javascript:document.getElementById('femail').value='{$admin_mail}'" class="link">{tr}use admin email{/tr}</a>
    </td>
</tr> 
<tr><td>&nbsp;</td>
    <td><input type="submit" name="add" value="{tr}add{/tr}" /></td>
</tr>    
</form>
</table>
<br /><br />

<table class="findtable">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="tiki-admin_notifications.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table>
<tr>
<td class="heading"><a href="tiki-admin_notifications.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'event_desc'}event_asc{else}event_desc{/if}">{tr}event{/tr}</a></td>
<td class="heading"><a href="tiki-admin_notifications.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'object_desc'}object_asc{else}object_desc{/if}">{tr}object{/tr}</a></td>
<td class="heading"><a href="tiki-admin_notifications.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'email_desc'}email_asc{else}emails_desc{/if}">{tr}email{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$channels}
{if $smarty.section.user.index % 2}
<tr class="odd">
<td>{$channels[user].event}</td>
<td>{$channels[user].object}</td>
<td>{$channels[user].email}</td>
<td><a href="tiki-admin_notifications.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;removeevent={$channels[user].event}&amp;object={$channels[user].object}&amp;email={$channels[user].email}">{tr}remove{/tr}</a></td>
</tr>
{else}
<tr class="even">
<td>{$channels[user].event}</td>
<td>{$channels[user].object}</td>
<td>{$channels[user].email}</td>
<td>
   <a href="tiki-admin_notifications.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;removeevent={$channels[user].event}&amp;object={$channels[user].object}&amp;email={$channels[user].email}">{tr}remove{/tr}</a>
</td>
</tr>
{/if}
{/section}
</table>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_notifications.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_notifications.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admin_notifications.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>