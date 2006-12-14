<h1><a class="pagetitle" href="tiki-admin_notifications.php">{tr}EMail notifications{/tr}</a>

{if $feature_help eq 'y'}
<a href="{$helpurl}EmailNotificationsAdmin" target="tikihelp" class="tikihelp" title="{tr}admin Email Notifications{/tr}">
<img src="pics/icons/help.png" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_notifications.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin notifications template{/tr}">
<img src="pics/icons/shape_square_edit.png" border="0" width="16" height="16" alt='{tr}edit{/tr}' /></a>{/if}</h1>

{if empty($sender_email)}
<div class="highlight">{tr}You need to set <a href="tiki-admin.php?page=general">Sender Email</a>{/tr}</div>
{/if}

<h2>{tr}Add notification{/tr}</h2>
<table class="normal">
<form action="tiki-admin_notifications.php" method="post">
     <input type="hidden" name="find" value="{$find|escape}" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="offset" value="{$offset|escape}" />
<tr><td class="formcolor">{tr}Event{/tr}:</td>
    <td class="formcolor">
    <select name="event">
      <option value="user_registers">{tr}A user registers{/tr}</option>
      <option value="article_submitted">{tr}A user submits an article{/tr}</option>
      <option value="wiki_page_changes">{tr}Any wiki page is changed{/tr}</option>
      <option value="wiki_page_changes_incl_minor">{tr}Any wiki page is changed, even minor changes{/tr}</option>
	<option value="wiki_comment_changes">{tr}A comment in a wiki page is posted or edited{/tr}</option> 
	<option value="php_error">{tr}PHP error{/tr}</option>
    </select>
    </td>
</tr> 
<tr><td class="formcolor">{tr}Email{/tr}:</td>        
    <td class="formcolor">
      <input type="text" id='femail' name="email" />
      <a href="#" onclick="javascript:document.getElementById('femail').value='{$admin_mail}'" class="link">{tr}use admin email{/tr}</a>
    </td>
</tr> 
<tr><td class="formcolor">&nbsp;</td>
    <td class="formcolor"><input type="submit" name="add" value="{tr}add{/tr}" /></td>
</tr>    
</form>
</table>

<h2>{tr}EMail notifications{/tr}</h2>
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_notifications.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_notifications.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'event_desc'}event_asc{else}event_desc{/if}">{tr}event{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_notifications.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'object_desc'}object_asc{else}object_desc{/if}">{tr}object{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_notifications.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'email_desc'}email_asc{else}email_desc{/if}">{tr}email{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{cycle print=false values="even,odd"}
{section name=user loop=$channels}
<tr class="{cycle}">
<td>{$channels[user].event}</td>
<td>{$channels[user].object}</td>
<td>{$channels[user].email}</td>
<td>
   <a class="link" href="tiki-admin_notifications.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;removeevent={$channels[user].event}&amp;object={$channels[user].object}&amp;email={$channels[user].email}">{tr}remove{/tr}</a>
</td>
</tr>
{sectionelse}
<tr><td class="odd" colspan="4"><b>{tr}No records found.{/tr}</b></td></tr>
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
<a class="prevnext" href="tiki-admin_notifications.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
