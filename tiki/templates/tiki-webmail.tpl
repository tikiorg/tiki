{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-webmail.tpl,v 1.46.2.3 2007-11-15 21:19:55 sylvieg Exp $ *}

<h1><a href="tiki-webmail.php" class="pagetitle">{tr}Webmail{/tr}</a>
{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Webmail" target="tikihelp" class="tikihelp" title="{tr}Webmail Doc{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>
{/if}
{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-webmail.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}Webmail Doc template{/tr}">
<img border="0" src="img/icons/info.gif" alt="{tr}Edit{/tr}" /></a>
{/if}
{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=webmail" title="{tr}Admin Feature{/tr}">{html_image file='pics/icons/wrench.png' border='0'  alt="{tr}Admin Feature{/tr}"}</a>
{/if}
</h1>

{include file=tiki-mytiki_bar.tpl}
<br /><br />
<table>
<tr>
  <td>
    <a class="link" href="tiki-webmail.php?locSection=settings" title="{tr}settings{/tr}">
    <img border="0" src="img/webmail/settings.gif" alt="{tr}Settings{/tr}" /><br />
    {tr}settings{/tr}</a>
  </td>
  <td>
    <a class="link" href="tiki-webmail.php?locSection=mailbox" title="{tr}mailbox{/tr}">
    <img border="0" src="img/webmail/mailbox.gif" alt="{tr}Mailbox{/tr}" /><br />
    {tr}mailbox{/tr}</a>
  </td>
  <td>
    <a class="link" href="tiki-webmail.php?locSection=compose" title="{tr}compose{/tr}">
    <img border="0" src="img/webmail/compose.gif" alt="{tr}Compose{/tr}" /><br />
    {tr}compose{/tr}</a>
  </td>
  <td>
    <a class="link" href="tiki-webmail.php?locSection=contacts" title="{tr}contacts{/tr}">
    <img border="0" src="img/webmail/contact.gif" alt="{tr}Contacts{/tr}" /><br />
    {tr}contacts{/tr}</a>
  </td>
</tr>
</table>
<hr/>

{if $locSection eq 'settings'}
{if $accountId}
<h2>{tr}Edit mail account{/tr}</h2>
<a href="tiki-webmail.php?locSection=settings" class="linkbut">{tr}Add new mail account{/tr}</a><br /><br />
{else}
<h2>{tr}Add new mail account{/tr}</h2>
{/if}
<form action="tiki-webmail.php" method="post">
<input type="hidden" name="accountId" value="{$accountId|escape}" />
<input type="hidden" name="locSection" value="settings" />
<table class="normal">
<tr><td class="formcolor">{tr}Account name{/tr}</td><td colspan="3" class="formcolor"><input type="text" name="account" value="{$info.account|escape}" /></td></tr>
<tr><td class="formcolor">{tr}POP server{/tr}</td><td class="formcolor"><input type="text" name="pop" value="{$info.pop|escape}" /></td><td class="formcolor">{tr}Port{/tr}</td><td class="formcolor"><input type="text" name="port" size="7" value="{$info.port}" /></td></tr>
<tr><td class="formcolor">{tr}SMTP server{/tr}</td><td class="formcolor"><input type="text" name="smtp" value="{$info.smtp|escape}" /></td><td class="formcolor">{tr}Port{/tr}</td><td class="formcolor"><input type="text" name="smtpPort" size="7" value="{$info.smtpPort}" /></td></tr>
<tr><td class="formcolor">{tr}SMTP requires authentication{/tr}</td><td colspan="3" class="formcolor">{tr}Yes{/tr}<input type="radio" name="useAuth" value="y" {if $info.useAuth eq 'y'}checked="checked"{/if} /> {tr}No{/tr}<input type="radio" name="useAuth" value="n" {if $info.useAuth eq 'n'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Username{/tr}</td><td colspan="3" class="formcolor"><input type="text" name="username" value="{$info.username|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Password{/tr}</td><td colspan="3" class="formcolor"><input type="password" name="pass" value="{$info.pass|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Messages per page{/tr}</td><td colspan="3" class="formcolor"><input type="text" name="msgs" size="7" value="{$info.msgs|escape}" /></td></tr>
<tr><td class="formcolor">&nbsp;</td><td colspan="3" class="formcolor"><input type="submit" name="new_acc" value="{tr}Add{/tr}" /></td></tr>
</table>
</form>
<h2>{tr}User accounts{/tr}</h2>
<table class="normal">
<tr>
<td class="heading">{tr}account{/tr}</td>
<td class="heading">{tr}active{/tr}</td>
<td class="heading">{tr}pop{/tr}</td>
<td class="heading">{tr}User{/tr}</td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$accounts}
<tr>
<td class="{cycle advance=false}"><a href="tiki-webmail.php?locSection=settings&amp;current={$accounts[ix].accountId}" class="{if $accounts[ix].current eq 'y'}tablename{else}link{/if}" title="{if $accounts[ix].current ne 'y'}{tr}Activate{/tr}{/if}">{$accounts[ix].account}</a>
</td>
<td class="{cycle advance=false}">{if $accounts[ix].current eq 'y'}{tr}yes{/tr}{else}{tr}no{/tr}{/if}</td>
<td class="{cycle advance=false}">{$accounts[ix].pop} ({$accounts[ix].port})</td>
<td class="{cycle advance=false}">{$accounts[ix].username}</td>
<td class="{cycle}"><a href="tiki-webmail.php?locSection=settings&amp;remove={$accounts[ix].accountId}" class="link" 
title="{tr}Delete{/tr}"><img src="pics/icons/cross.png" border="0" height="16" width="16" alt='{tr}Delete{/tr}' /></a>
<a href="tiki-webmail.php?locSection=settings&amp;accountId={$accounts[ix].accountId}" class="tablename" title="{tr}Edit{/tr}">
<img src="pics/icons/page_edit.png" border="0" width="16" height="16" alt='{tr}Edit{/tr}' />
</a>
{if $accounts[ix].current ne 'y'}
<a href="tiki-webmail.php?locSection=settings&amp;current={$accounts[ix].accountId}" title="{tr}Activate{/tr}">
<img src="pics/icons/accept.png" alt="{tr}Activate{/tr}" width="16" height="16" border="0" />
</a>{/if}
</td></tr>
{/section}
</table>
{/if}

{if $locSection eq 'mailbox'}
<table >
<tr>
<td>
<a class="link" href="tiki-webmail.php?locSection=mailbox">{tr}View All{/tr}</a> | <a class="link" href="tiki-webmail.php?locSection=mailbox&amp;filter=unread">{tr}Unread{/tr}</a> | <a class="link" href="tiki-webmail.php?locSection=mailbox&amp;filter=flagged">{tr}Flagged{/tr}</a>
</td>
<td align="right">
{tr}Msg{/tr} {$showstart}-{$showend} {tr}of{/tr} {$total} 
{if $first}| <a class="link" href="tiki-webmail.php?locSection=mailbox&amp;start={$first}{if $filter}&amp;filter={$filter}{/if}">{tr}First{/tr}</a>{/if} 
{if $prevstart}| <a class="link" href="tiki-webmail.php?locSection=mailbox&amp;start={$prevstart}{if $filter}&amp;filter={$filter}{/if}">{tr}Prev{/tr}</a>{/if} 
{if $nextstart}| <a class="link" href="tiki-webmail.php?locSection=mailbox&start={$nextstart}{if $filter}&amp;filter={$filter}{/if}">{tr}Next{/tr}</a>{/if} 
{if $last}| <a class="link" href="tiki-webmail.php?locSection=mailbox&amp;start={$last}{if $filter}&amp;filter={$filter}{/if}">{tr}Last{/tr}</a>{/if}
</td>
</tr>
</table><br />
<form action="tiki-webmail.php" method="post">
<input type="hidden" name="locSection" value="mailbox" />
<input type="submit" name="delete" value="{tr}Delete{/tr}" />
<input type="hidden" name="start" value="{$start|escape}" />
<select name="action">
<option value="flag">{tr}Mark as flagged{/tr}</option>
<option value="unflag">{tr}Mark as unflagged{/tr}</option>
<option value="read">{tr}Mark as read{/tr}</option>
<option value="unread">{tr}Mark as unread{/tr}</option>
</select>
<input type="submit" name="operate" value="{tr}mark{/tr}" />
<br /><br />
<table class="normal">
<tr>
  <td class="heading">&nbsp;</td>
  <td class="heading">&nbsp;</td>
  <td class="heading">{tr}sender{/tr}</td>
  <td class="heading">{tr}subject{/tr}</td>
  <td class="heading">{tr}date{/tr}</td>
  <td class="heading">{tr}Size{/tr}</td>
</tr>
{section name=ix loop=$list}
{if $list[ix].isRead eq 'y'}
{assign var=class value="#CCCCCC"}
{else}
{assign var=class value="#FFFFFF"}
{/if}
<tr>
<td style="background:{$class};">
<input type="checkbox" name="msg[{$list[ix].msgid}]" />
<input type="hidden" name="realmsg[{$list[ix].msgid}]" value="{$list[ix].realmsgid|escape}" />
</td>
<td style="background:{$class};">
{if $list[ix].isFlagged eq 'y'}
<img src="img/webmail/flagged.gif" alt='{tr}flagged{/tr}'/>
{/if}
{if $list[ix].isReplied eq 'y'}
<img src="img/webmail/replied.gif" alt='{tr}replied{/tr}'/>
{/if}
</td>
<td style="background:{$class};">{$list[ix].sender.name}</td>
<td style="background:{$class};"><a class="link" href="tiki-webmail.php?locSection=read&amp;msgid={$list[ix].msgid}">{$list[ix].subject}</a>{if $list[ix].has_attachment}<img src="img/webmail/clip.gif" alt='{tr}clip{/tr}'/>{/if}</td>
<td style="background:{$class};">{$list[ix].timestamp|tiki_short_datetime}</td>
<td align="right" style="background:{$class};">{$list[ix].size|kbsize}</td>
</tr>
{/section}
</table>
</form>
{/if}

{if $locSection eq 'read'}
{if $prev}<a class="link" href="tiki-webmail.php?locSection=read&amp;msgid={$prev}">{tr}Prev{/tr}</a> |{/if}
{if $next}<a class="link" href="tiki-webmail.php?locSection=read&amp;msgid={$next}">{tr}Next{/tr}</a> |{/if}
 <a class="link" href="tiki-webmail.php?locSection=mailbox">{tr}back to mailbox{/tr}</a> |
{if $fullheaders eq 'n'} 
 <a class="link" href="tiki-webmail.php?locSection=read&amp;msgid={$msgid}&amp;fullheaders=1">{tr}full headers{/tr}</a>
{else}
 <a class="link" href="tiki-webmail.php?locSection=read&amp;msgid={$msgid}">{tr}normal headers{/tr}</a>
{/if} 
<table>
<tr>
  <td>
    <form method="post" action="tiki-webmail.php">
    <input type="submit" name="delete_one" value="{tr}Delete{/tr}" />
    {if $next}
    <input type="hidden" name="locSection" value="read" />
    <input type="hidden" name="msgid" value="{$next|escape}" />
    {else}
    <input type="hidden" name="locSection" value="mailbox" />
    {/if}
    <input type="hidden" name="msgdel" value="{$msgid|escape}" />
    </form>
  </td>
  <td>
    <form method="post" action="tiki-webmail.php">
    <input type="hidden" name="locSection" value="compose" />
    <input type="submit" name="reply" value="{tr}Reply{/tr}" />
    <input type="hidden" name="realmsgid" value="{$realmsgid|escape}" />
    <input type="hidden" name="to" value="{$headers.replyto|escape}" />
    <input type="hidden" name="subject" value="Re:{$headers.subject}" />
    <input type="hidden" name="body" value="{$allbodies|escape}" />
    </form>
  </td>
  <td>
    <form method="post" action="tiki-webmail.php">
    <input type="hidden" name="locSection" value="compose" />
    <input type="submit" name="replyall" value="{tr}reply all{/tr}" />
    <input type="hidden" name="to" value="{$headers.replyto|escape}" />
    <input type="hidden" name="realmsgid" value="{$realmsgid|escape}" />
    <input type="hidden" name="cc" value="{$headers.replycc|escape}" />
    <input type="hidden" name="subject" value="Re:{$headers.subject}" />
    <input type="hidden" name="body" value="{$allbodies|escape}" />
    </form>
  </td>
  <td>
    <form method="post" action="tiki-webmail.php">
    <input type="submit" name="reply" value="{tr}forward{/tr}" />
    <input type="hidden" name="locSection" value="compose" />
    <input type="hidden" name="to" value="" />
    <input type="hidden" name="cc" value="" />
    <input type="hidden" name="subject" value="Fw:{$headers.subject}" />
    <input type="hidden" name="body" value="{$allbodies|escape}" />
    </form>

  </td>
</tr>
</table>
<table >
{if $fullheaders eq 'n'}
<tr class="formcolor"><td>{tr}From{/tr}</td><td>{$headers.from|escape}</td></tr>
<tr class="formcolor"><td>{tr}To{/tr}</td><td>{$headers.to|escape}</td></tr>
{if $headers.cc}
<tr class="formcolor"><td>{tr}Cc{/tr}</td><td>{$headers.cc|escape}</td></tr>
{/if}
<tr class="formcolor"><td>{tr}Subject{/tr}</td><td>{$headers.subject|escape}</td></tr>
<tr class="formcolor"><td>{tr}Date{/tr}</td><td>{$headers.timestamp|tiki_short_datetime}</td></tr>
{/if}
{if $fullheaders eq 'y'}
{foreach key=key item=item from=$headers}
    <tr class="formcolor"><td>{$key}</td><td>
    {section name=ix loop=$item}
    {$item[ix]}<br /> 
    {sectionelse}
    {$item}
    {/section}
    </td></tr>
  {/foreach}
{/if}
</table>
<br />
{section name=ix loop=$bodies}
{$bodies[ix]|nl2br}
<hr />
{/section}

{section name=ix loop=$attachs}
<div class="simplebox">
<a class="link" href="tiki-webmail_download_attachment.php?locSection=read&amp;msgid={$msgid}&amp;getpart={$attachs[ix].part}">{$attachs[ix].name|iconify}{$attachs[ix].name}</a>
</div>
{/section}
{/if}

{if $locSection eq 'contacts'}
<h2>{tr}Create/edit contacts{/tr}</h2>
<form action="tiki-webmail.php" method="post">
<input type="hidden" name="locSection" value="contacts" />
<input type="hidden" name="contactId" value="{$contactId|escape}" />
<table class="normal">
<tr class="formcolor"><td>{tr}First Name{/tr}:</td><td><input type="text" maxlength="80" size="20" name="firstName" value="{$info.firstName|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Last Name{/tr}:</td><td><input type="text" maxlength="80" size="20" name="lastName" value="{$info.lastName|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Email{/tr}:</td><td><input type="text" maxlength="80" size="20" name="email" value="{$info.email|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Nickname{/tr}:</td><td><input type="text" maxlength="80" size="20" name="nickname" value="{$info.nickname|escape}" /></td></tr>
<tr class="formcolor"><td colspan="2"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<h2>{tr}Contacts{/tr}</h2>
<div align="center">
<table class="findtable">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="tiki-webmail.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="locSection" value="contacts" />
   </form>
   </td>
</tr>
</table>
<a class="link" href="tiki-webmail.php?locSection=contacts&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}">{tr}All{/tr}</a>
{section name=ix loop=$letters}
<a class="link" href="tiki-webmail.php?locSection=contacts&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;letter={$letters[ix]}">{$letters[ix]}</a>
{/section}
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-webmail.php?locSection=contacts&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'firstName_desc'}firstName_asc{else}firstName_desc{/if}">{tr}First Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-webmail.php?locSection=contacts&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastName_desc'}lastName_asc{else}lastName_desc{/if}">{tr}Last Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-webmail.php?locSection=contacts&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'email_desc'}email_asc{else}email_desc{/if}">{tr}Email{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-webmail.php?locSection=contacts&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'nickname_desc'}nickname_asc{else}nickname_desc{/if}">{tr}Nickname{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].firstName}</td>
<td class="{cycle advance=false}">{$channels[user].lastName}</td>
<td class="{cycle advance=false}"><a class="link" href="tiki-webmail.php?locSection=contacts&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;contactId={$channels[user].contactId}">{$channels[user].email|escape}</a>
[&nbsp;&nbsp;<a class="link" href="tiki-webmail.php?locSection=contacts&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;remove={$channels[user].contactId}" 
title="{tr}Delete{/tr}"><img src="pics/icons/cross.png" border="0" height="16" width="16" alt='{tr}Delete{/tr}' /></a>&nbsp;&nbsp;]
</td>
<td class="{cycle advance=false}">{$channels[user].nickname}</td>
</tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-webmail.php?locSection=contacts&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-webmail.php?locSection=contacts&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-webmail.php?locSection=contacts&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
{/if}

{if $locSection eq 'compose'}
{if $attaching eq 'n'}
  {if $sent eq 'n'}
    <form action="tiki-webmail.php" method="post">
    <input type="hidden" name="locSection" value="compose" />
    <input type="hidden" name="attach1" value="{$attach1|escape}" />
    <input type="hidden" name="attach2" value="{$attach2|escape}" />
    <input type="hidden" name="attach3" value="{$attach3|escape}" />
    <input type="hidden" name="attach1file" value="{$attach1file|escape}" />
    <input type="hidden" name="attach2file" value="{$attach2file|escape}" />
    <input type="hidden" name="attach3file" value="{$attach3file|escape}" />
    <input type="hidden" name="attach1type" value="{$attach1type|escape}" />
    <input type="hidden" name="attach2type" value="{$attach2type|escape}" />
    <input type="hidden" name="attach3type" value="{$attach3type|escape}" />
    <input type="submit" name="send" value="{tr}Send{/tr}" />
    <table >
    <tr class="formcolor"><td><a title="{tr}select from address book{/tr}" class="link" href="#" onclick="javascript:window.open('tiki-webmail_contacts.php?element=to','','menubar=no,width=452,height=550');">{tr}To{/tr}</a>:</td><td colspan="3"><input size="69" type="text" id="to" name="to" value="{$to|escape}" /></td></tr>
    <tr class="formcolor"><td>{tr}cc{/tr}</td><td><input id="cc" type="text" name="cc" value="{$cc|escape}" /></td><td>{tr}bcc{/tr}</td><td><input type="text" name="bcc" value="{$bcc}" id="bcc" /></td></tr>
    <tr class="formcolor"><td>{tr}Subject{/tr}</td><td colspan="3"><input size="69" type="text" name="subject" value="{$subject|escape}" /></td></tr>
    <tr class="formcolor"><td>{tr}Attachments{/tr}</td><td colspan="3">
    {if $attach1}
    ({$attach1})
    {/if}
    {if $attach2}
    ({$attach2})
    {/if}
    {if $attach3}
    ({$attach3})
    {/if}
    <input type="submit" name="attach" value="{tr}Add{/tr}" />
    </td></tr>
    <tr>
    <tr class="formcolor"><td>&nbsp;</td>
    <td colspan="3">
    <textarea name="body" cols="60" rows="30">{$body|escape}</textarea>
    <tr class="formcolor"><td>{tr}Use HTML mail{/tr}</td><td colspan="3"><input type="checkbox" name="useHTML" />
    </td></tr>
    </table>
    </form>
  {else}
    {$msg}<br /><br />
    {if $notcon eq 'y'}
      {tr}The following addresses are not in your address book{/tr}<br /><br />
      <form action="tiki-webmail.php" method="post">
      <table class="normal">
      <tr>
        <td class="heading">&nbsp;</td>
        <td class="heading">{tr}Email{/tr}</td>
        <td class="heading">{tr}First Name{/tr}</td>
        <td class="heading">{tr}Last Name{/tr}</td>
        <td class="heading">{tr}Nickname{/tr}</td>
      </tr>
      {section name=ix loop=$not_contacts}
      <tr><td><input type="checkbox" name="add[{$smarty.section.ix.index}]" /><input type="hidden" name="addemail[{$smarty.section.ix.index}]" value="{$not_contacts[ix]|escape}" /></td>
          <td>{$not_contacts[ix]}</td>
          <td><input type="text" name="addFirstName[{$smarty.section.ix.index}]" /></td>
          <td><input type="text" name="addLastName[{$smarty.section.ix.index}]" /></td>
          <td><input type="text" name="addNickname[{$smarty.section.ix.index}]" /></td>
      </tr>
      {/section}
      <tr><td>&nbsp;</td><td><input type="submit" name="add_contacts" value="{tr}Add Contacts{/tr}" /></td></tr>
      </table>
      </form>
    {/if}
  {/if}
{else}
  <form enctype="multipart/form-data" action="tiki-webmail.php" method="post">
    <input type="hidden" name="locSection" value="compose" />
    <input type="hidden" name="to" value="{$to|escape}" />
    <input type="hidden" name="cc" value="{$cc|escape}" />
    <input type="hidden" name="bcc" value="{$bcc|escape}" />
    <input type="hidden" name="subject" value="{$subject|escape}" />
    <input type="hidden" name="body" value="{$body|escape}" />
    <input type="hidden" name="attach1" value="{$attach1|escape}" />
    <input type="hidden" name="attach2" value="{$attach2|escape}" />
    <input type="hidden" name="attach3" value="{$attach3|escape}" />
    <input type="hidden" name="attach1file" value="{$attach1file|escape}" />
    <input type="hidden" name="attach2file" value="{$attach2file|escape}" />
    <input type="hidden" name="attach3file" value="{$attach3file|escape}" />
    <input type="hidden" name="attach1type" value="{$attach1type|escape}" />
    <input type="hidden" name="attach2type" value="{$attach2type|escape}" />
    <input type="hidden" name="attach3type" value="{$attach3type|escape}" />
    <table class="normal">
    {if $attach1}
    <tr class="formcolor"><td>{tr}Attachment 1{/tr}</td><td>{$attach1} <input type="submit" name="remove_attach1" value="{tr}Remove{/tr}" /></td></tr>
    {else}
    <tr class="formcolor"><td>{tr}Attachment 1{/tr}</td><td><input type="hidden" name="MAX_FILE_SIZE" value="1500000" /><input name="userfile1" type="file" /></td></tr>
    {/if}
    {if $attach2}
    <tr class="formcolor"><td>{tr}Attachment 2{/tr}</td><td>{$attach2} <input type="submit" name="remove_attach2" value="{tr}Remove{/tr}" /></td></tr>
    {else}
    <tr class="formcolor"><td>{tr}Attachment 2{/tr}</td><td><input type="hidden" name="MAX_FILE_SIZE" value="1500000" /><input name="userfile2" type="file" /></td></tr>
    {/if}
    {if $attach3}
    <tr class="formcolor"><td>{tr}Attachment 3{/tr}</td><td>{$attach3} <input type="submit" name="remove_attach3" value="{tr}Remove{/tr}" /></td></tr>
    {else}
    <tr class="formcolor"><td>{tr}Attachment 3{/tr}</td><td><input type="hidden" name="MAX_FILE_SIZE" value="1500000" /><input name="userfile3" type="file" /></td></tr>
    {/if}
    <tr class="formcolor"><td>&nbsp;</td><td><input type="submit" name="attached" value="{tr}done{/tr}" /></td></tr>
    </table>
    </form>
  
{/if}
{/if}
