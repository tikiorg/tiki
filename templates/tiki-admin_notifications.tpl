<a class="pagetitle" href="tiki-admin_notifications.php">{tr}Mail notifications{/tr}</a><br/><br/>
<h2>{tr}Add notification{/tr}</h2>
<table class="normal">
<form action="tiki-admin_notifications.php" method="post">
     <input type="hidden" name="find" value="{$find}" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
     <input type="hidden" name="offset" value="{$offset}" />
<tr><td class="formcolor">{tr}Event{/tr}:</td>
    <td class="formcolor">
    <select name="event">
      <option value="user_registers">{tr}A user registers{/tr}</option>
      <option value="article_submitted">{tr}A user submits an article{/tr}</option>
    </select>
    </td>
</tr> 
<tr><td class="formcolor">{tr}Email{/tr}:</td>        
    <td class="formcolor">
      <input type="text" id='femail' name="email" />
      <a href="#" onClick="javascript:document.getElementById('femail').value='{$admin_mail}'" class="link">{tr}use admin email{/tr}</a>
    </td>
</tr> 
<tr><td class="formcolor">&nbsp;</td>
    <td class="formcolor"><input type="submit" name="add" value="{tr}add{/tr}" /></td>
</tr>    
</form>
</table>
<br/><br/>

<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_notifications.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_notifications.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'event_desc'}event_asc{else}event_desc{/if}">{tr}event{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_notifications.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'object_desc'}object_asc{else}object_desc{/if}">{tr}object{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_notifications.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'email_desc'}email_asc{else}emails_desc{/if}">{tr}email{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$channels}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$channels[user].event}</td>
<td class="odd">{$channels[user].object}</td>
<td class="odd">{$channels[user].email}</td>
<td class="odd">
   <a class="link" href="tiki-admin_notifications.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;removeevent={$channels[user].event}&amp;object={$channels[user].object}&amp;email={$channels[user].email}">{tr}remove{/tr}</a>
</td>
</tr>
{else}
<tr>
<td class="even">{$channels[user].event}</td>
<td class="even">{$channels[user].object}</td>
<td class="even">{$channels[user].email}</td>
<td class="even">
   <a class="link" href="tiki-admin_notifications.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;removeevent={$channels[user].event}&amp;object={$channels[user].object}&amp;email={$channels[user].email}">{tr}remove{/tr}</a>
</td>
</tr>
{/if}
{/section}
</table>
<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_notifications.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_notifications.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>
</div>
