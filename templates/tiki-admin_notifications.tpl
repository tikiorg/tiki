<h1><a class="pagetitle" href="tiki-admin_notifications.php">{tr}EMail notifications{/tr}</a>

{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Mail+Notifications" target="tikihelp" class="tikihelp" title="{tr}Admin Email Notifications{/tr}">
{icon _id='help'}</a>{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_notifications.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Admin Notifications Template{/tr}">
{icon _id='shape_square_edit'}</a>{/if}</h1>

{if empty($prefs.sender_email)}<br />
<div class="highlight simplebox">{icon _id=information.png style="vertical-align:middle"} {tr}You need to set <a href="tiki-admin.php?page=general">Sender Email</a> before creating email notifications.{/tr}</div>
<br />{/if}

<h2>{tr}Add notification{/tr}</h2>
{if !empty($tikifeedback)}
<div class="highlight simplebox">{section name=ix loop=$tikifeedback}{icon _id=delete.png alt="{tr}Alert{/tr}" style="vertical-align:middle"} {$tikifeedback[ix].mes}.{/section}</div>
<br />{/if}
<form action="tiki-admin_notifications.php" method="post">
     <input type="hidden" name="find" value="{$find|escape}" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     {if $offset}<input type="hidden" name="offset" value="{$offset|escape}" />{/if}
	 {if $numrows ne $prefs.maxRecords and $numrows}<input type="hidden" name="numrows" value="{$numrows|escape}" />{/if}
<table class="normal">
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
<tr>
  <td class="formcolor">{tr}User:{/tr}</td>
  <td class="formcolor">
    <input type="text" id="flogin" name="login" />
  </td>
</tr>
<tr>
  <td class="formcolor">{tr}Email:{/tr}</td>        
  <td class="formcolor">
    <input type="text" id='femail' name="email" />
      {if $admin_mail neq ''}
        <a href="#" onclick="javascript:document.getElementById('femail').value='{$admin_mail}';document.getElementById('flogin').value='admin'" class="link">{tr}Preload Admin Account{/tr}</a>
      {/if}
  </td>
</tr> 
<tr><td class="formcolor">&nbsp;</td>
    <td class="formcolor"><input type="submit" name="add" value="{tr}Add{/tr}" /></td>
</tr>    
</table>
</form>
<br />
<h2>{tr}EMail notifications{/tr}</h2>
{if $channels or ($find ne '')}
  {include file='find.tpl' _sort_mode='y' find_show_num_rows='y'}
{/if}
<form method="get" action="tiki-admin_notifications.php">
<table class="normal">
<tr>
<th class="heading">
{if $channels}<input name="switcher" title="{tr}Select All{/tr}" id="clickall" type="checkbox" onclick="switchCheckboxes(this.form,'checked[]',this.checked)" />{/if}
</th>
<th class="heading">{self_link _class="tableheading" _sort_arg="sort_mode" _sort_field="event"}{tr}Event{/tr}{/self_link}</th>
<th class="heading">{self_link _class="tableheading" _sort_arg="sort_mode" _sort_field="object"}{tr}Object{/tr}{/self_link}</th>
<th class="heading">{self_link _class="tableheading" _sort_arg="sort_mode" _sort_field="email"}{tr}eMail{/tr}{/self_link}</th>
<th class="heading">{self_link _class="tableheading" _sort_arg="sort_mode" _sort_field="user"}{tr}User{/tr}{/self_link}</th>
<th class="heading">{tr}Action{/tr}</th>
</tr>
{cycle print=false values="even,odd"}
{section name=user loop=$channels}
<tr class="{cycle}">
<td><input type="checkbox" name="checked[]" value="{$channels[user].watchId|escape}" {if $smarty.request.checked and in_array($channels[user].watchId,$smarty.request.checked)}checked="checked"{/if} /></td>
<td>{$channels[user].event}</td>
<td>{if $channels[user].url}<a href="{$channels[user].url}" title="{$channels[user].title|escape}">{$channels[user].object|escape}</a>{else}{$channels[user].object|escape}{/if}</td>
<td>{$channels[user].email}</td>
<td>{$channels[user].user}</td>
<td>
   <a class="link" href="tiki-admin_notifications.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;removeevent={$channels[user].watchId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
</td>
</tr>
{sectionelse}
<tr class="odd"><td colspan="6"><b>{tr}No records found.{/tr}</b></td></tr>
{/section}
</table>
{if $channels}
<br />{tr}Perform action with checked:{/tr}
<input type="image" name="delsel" src='pics/icons/cross.png' alt='{tr}Delete{/tr}' title='{tr}Delete{/tr}' />
{/if}
</form>

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
