<a href="tiki-webmail.php" class="pagetitle">{tr}Webmail{/tr}</a><br /><br />
{include file=tiki-mytiki_bar.tpl}
<br /><br />
<table>
<tr>
  <td>
    <a class="link" href="tiki-webmail.php?section=settings">
    <img border="0" src="img/webmail/settings.gif"><br />
    {tr}settings{/tr}</a>
  </td>
  <td>
    <a class="link" href="tiki-webmail.php?section=mailbox">
    <img border="0" src="img/webmail/mailbox.gif"><br />
    {tr}mailbox{/tr}</a>
  </td>
  <td>
    <a class="link" href="tiki-webmail.php?section=compose">
    <img border="0" src="img/webmail/compose.gif"><br />
    {tr}compose{/tr}</a>
  </td>
  <td>
    <a class="link" href="tiki-webmail.php?section=contacts">
    <img border="0" src="img/webmail/contact.gif"><br />
    {tr}contacts{/tr}</a>
  </td>
</tr>
</table>
<hr/>

{if $section eq 'settings'}
<h3>{tr}Add new mail account{/tr}</h3>
<form action="tiki-webmail.php" method="post">
<input type="hidden" name="accountId" value="{$accountId|escape}" />
<input type="hidden" name="section" value="settings" />
<table class="normal">
<tr><td class="formcolor">{tr}Account name{/tr}</td><td colspan="3" class="formcolor"><input type="text" name="account" value="{$info.account|escape}" /></td></tr>
<tr><td class="formcolor">{tr}POP server{/tr}</td><td class="formcolor"><input type="text" name="pop" value="{$info.pop|escape}" /></td><td class="formcolor">{tr}Port{/tr}</td><td class="formcolor"><input type="text" name="port" size="7" value="{$info.port}" /></td></tr>
<tr><td class="formcolor">{tr}SMTP server{/tr}</td><td class="formcolor"><input type="text" name="smtp" value="{$info.smtp|escape}" /></td><td class="formcolor">{tr}Port{/tr}</td><td class="formcolor"><input type="text" name="smtpPort" size="7" value="{$info.smtpPort}" /></td></tr>
<tr><td class="formcolor">{tr}SMTP requires authentication{/tr}</td><td colspan="3" class="formcolor">{tr}Yes{/tr}<input type="radio" name="useAuth" value="yes" {if $info.useAuth eq 'y'}checked="checked"{/if} /> {tr}No{/tr}<input type="radio" name="useAuth" value="no" {if $info.useAuth eq 'n'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Username{/tr}</td><td colspan="3" class="formcolor"><input type="text" name="username" value="{$info.username|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Password{/tr}</td><td colspan="3" class="formcolor"><input type="text" name="pass" value="{$info.pass|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Messages per page{/tr}</td><td colspan="3" class="formcolor"><input type="text" name="msgs" size="7" value="{$info.msgs|escape}" /></td></tr>
<tr><td class="formcolor">&nbsp;</td><td colspan="3" class="formcolor"><input type="submit" name="new_acc" value="{tr}add{/tr}" /></td></tr>
</table>
</form>
<h3>{tr}User accounts{/tr}</h3>
<table class="normal">
<tr>
<td class="heading">{tr}account{/tr}</td>
<td class="heading">{tr}pop{/tr}</td>
<td class="heading">{tr}user{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$accounts}
<td class="{cycle advance=false}"><a href="tiki-webmail.php?section=settings&amp;current={$accounts[ix].accountId}" class="{if $accounts[ix].current eq 'y'}tablename{else}link{/if}">{$accounts[ix].account}</a>
[<a href="tiki-webmail.php?section=settings&amp;remove={$accounts[ix].accountId}" class="link">x</a>|<a href="tiki-webmail.php?section=settings&amp;accountId={$accounts[ix].accountId}" class="tablename">{tr}edit{/tr}</a>]
</td>
<td class="{cycle advance=false}">{$accounts[ix].pop} ({$accounts[ix].port})</td>
<td class="{cycle}">{$accounts[ix].user}</td>
</tr>
{/section}
</table>
{/if}

{if $section eq 'mailbox'}
<table width="100%">
<tr>
<td>
<a class="link" href="tiki-webmail.php?section=mailbox">{tr}View All{/tr}</a> | <a class="link" href="tiki-webmail.php?section=mailbox&amp;filter=unread">{tr}Unread{/tr}</a> | <a class="link" href="tiki-webmail.php?section=mailbox&amp;filter=flagged">{tr}Flagged{/tr}</a>
</td>
<td align="right">
{tr}Msg{/tr} {$showstart}-{$showend} {tr}of{/tr} {$total} 
{if $first}| <a class="link" href="tiki-webmail.php?section=mailbox&amp;start={$first}{if $filter}&amp;filter={$filter}{/if}">{tr}First{/tr}</a>{/if} 
{if $prevstart}| <a class="link" href="tiki-webmail.php?section=mailbox&amp;start={$prevstart}{if $filter}&amp;filter={$filter}{/if}">{tr}Prev{/tr}</a>{/if} 
{if $nextstart}| <a class="link" href="tiki-webmail.php?section=mailbox&start={$nextstart}{if $filter}&amp;filter={$filter}{/if}">{tr}Next{/tr}</a>{/if} 
{if $last}| <a class="link" href="tiki-webmail.php?section=mailbox&amp;start={$last}{if $filter}&amp;filter={$filter}{/if}">{tr}Last{/tr}</a>{/if}
</td>
</tr>
</table><br />
<form action="tiki-webmail.php" method="post">
<input type="hidden" name="section" value="mailbox" />
<input type="submit" name="delete" value="{tr}delete{/tr}" />
<input type="hidden" name="start" value="{$start|escape}" />
<select name="action">
<option value="flag">{tr}Mark as Flagged{/tr}</option>
<option value="unflag">{tr}Mark as unflagged{/tr}</option>
<option value="read">{tr}Mark as read{/tr}</option>
<option value="unread">{tr}Mark as unread{/tr}</option>
</select>
<input type="submit" name="operate" value="{tr}ok{/tr}" />
<br /><br />
<table class="normal">
<tr>
  <td width="2%" class="heading"></td>
  <td width="2%" class="heading"></td>
  <td width="20%" class="heading">{tr}sender{/tr}</td>
  <td width="30%" class="heading">{tr}subject{/tr}</td>
  <td width="12%" class="heading">{tr}date{/tr}</td>
  <td width="8%" align="right" class="heading">{tr}size{/tr}</td>
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
<img src="img/webmail/flagged.gif" />
{/if}
{if $list[ix].isReplied eq 'y'}
<img src="img/webmail/replied.gif" />
{/if}
</td>
<td style="background:{$class};">{$list[ix].sender.name}</td>
<td style="background:{$class};"><a class="link" href="tiki-webmail.php?section=read&amp;msgid={$list[ix].msgid}">{$list[ix].subject}</a>{if $list[ix].has_attachment}<img src="img/webmail/clip.gif" />{/if}</td>
<td style="background:{$class};">{$list[ix].date}</td>
<td align="right" style="background:{$class};">{$list[ix].size|kbsize}</td>
</tr>
{/section}
</table>
</form>
{/if}

{if $section eq 'read'}
{if $prev}<a class="link" href="tiki-webmail.php?section=read&amp;msgid={$prev}">{tr}Prev{/tr}</a> |{/if}
{if $next}<a class="link" href="tiki-webmail.php?section=read&amp;msgid={$next}">{tr}Next{/tr}</a> |{/if}
 <a class="link" href="tiki-webmail.php?section=mailbox">{tr}back to mailbox{/tr}</a> |
{if $fullheaders eq 'n'} 
 <a class="link" href="tiki-webmail.php?section=read&amp;msgid={$msgid}&amp;fullheaders=1">{tr}full headers{/tr}</a>
{else}
 <a class="link" href="tiki-webmail.php?section=read&amp;msgid={$msgid}">{tr}normal headers{/tr}</a>
{/if} 
<table>
<tr>
  <td>
    <form method="post" action="tiki-webmail.php">
    <input type="submit" name="delete_one" value="{tr}delete{/tr}" />
    {if $next}
    <input type="hidden" name="section" value="read" />
    <input type="hidden" name="msgid" value="{$next|escape}" />
    {else}
    <input type="hidden" name="section" value="mailbox" />
    {/if}
    <input type="hidden" name="msgdel" value="{$msgid|escape}" />
    </form>
  </td>
  <td>
    <form method="post" action="tiki-webmail.php">
    <input type="hidden" name="section" value="compose" />
    <input type="submit" name="reply" value="{tr}reply{/tr}" />
    <input type="hidden" name="realmsgid" value="{$realmsgid|escape}" />
    <input type="hidden" name="to" value="{$headers.replyto|escape}" />
    <input type="hidden" name="subject" value="Re:{$headers.subject}" />
    <input type="hidden" name="body" value="{$allbodies|escape}" />
    </form>
  </td>
  <td>
    <form method="post" action="tiki-webmail.php">
    <input type="hidden" name="section" value="compose" />
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
    <input type="hidden" name="section" value="compose" />
    <input type="hidden" name="to" value="" />
    <input type="hidden" name="cc" value="" />
    <input type="hidden" name="subject" value="Fw:{$headers.subject}" />
    <input type="hidden" name="body" value="{$allbodies|escape}" />
    </form>

  </td>
</tr>
</table>
<table width="100%">
{if $fullheaders eq 'n'}
<tr><td class="formcolor">{tr}From{/tr}</td><td class="formcolor">{$headers.from}</td></tr>
<tr><td class="formcolor">{tr}To{/tr}</td><td class="formcolor">{$headers.to}</td></tr>
{if $headers.cc}
<tr><td class="formcolor">{tr}Cc{/tr}</td><td class="formcolor">{$headers.cc}</td></tr>
{/if}
<tr><td class="formcolor">{tr}Subject{/tr}</td><td class="formcolor">{$headers.subject}</td></tr>
<tr><td class="formcolor">{tr}Date{/tr}</td><td class="formcolor">{$headers.date}</td></tr>
{/if}
{if $fullheaders eq 'y'}
{foreach key=key item=item from=$headers}
    <tr><td class="formcolor">{$key}</td><td class="formcolor">
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
<hr/>
{/section}
</div>
{section name=ix loop=$attachs}
<div class="simplebox">
<a class="link" href="tiki-webmail_download_attachment.php?section=read&amp;msgid={$msgid}&amp;getpart={$attachs[ix].part}">{$attachs[ix].name|iconify}{$attachs[ix].name}</a>
</div>
{/section}
{/if}

{if $section eq 'contacts'}
<h2>{tr}Create/edit contacts{/tr}</h2>
<form action="tiki-webmail.php" method="post">
<input type="hidden" name="section" value="contacts" />
<input type="hidden" name="contactId" value="{$contactId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}First Name{/tr}:</td><td class="formcolor"><input type="text" maxlength="80" size="20" name="firstName" value="{$info.firstName|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Last Name{/tr}:</td><td class="formcolor"><input type="text" maxlength="80" size="20" name="lastName" value="{$info.lastName|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Email{/tr}:</td><td class="formcolor"><input type="text" maxlength="80" size="20" name="email" value="{$info.email|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Nickname{/tr}:</td><td class="formcolor"><input type="text" maxlength="80" size="20" name="nickname" value="{$info.nickname|escape}" /></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<h2>{tr}Contacts{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-webmail.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="section" value="contacts" />
   </form>
   </td>
</tr>
</table>
<a class="link" href="tiki-webmail.php?section=contacts&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}">{tr}all{/tr}</a>
{section name=ix loop=$letters}
<a class="link" href="tiki-webmail.php?section=contacts&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;letter={$letters[ix]}">{$letters[ix]}</a>
{/section}
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-webmail.php?section=contacts&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'firstName_desc'}firstName_asc{else}firstName_desc{/if}">{tr}First Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-webmail.php?section=contacts&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastName_desc'}lastName_asc{else}lastName_desc{/if}">{tr}Last Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-webmail.php?section=contacts&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'email_desc'}email_asc{else}email_desc{/if}">{tr}Email{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-webmail.php?section=contacts&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'nickname_desc'}nickname_asc{else}nickname_desc{/if}">{tr}Nickname{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].firstName}</td>
<td class="{cycle advance=false}">{$channels[user].lastName}</td>
<td class="{cycle advance=false}"><a class="link" href="tiki-webmail.php?section=contacts&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;contactId={$channels[user].contactId}">{$channels[user].email}</a>
[<a class="link" href="tiki-webmail.php?section=contacts&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;remove={$channels[user].contactId}">x</a>]
</td>
<td class="{cycle advance=false}">{$channels[user].nickname}</td>
</tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-webmail.php?section=contacts&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-webmail.php?section=contacts&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-webmail.php?section=contacts&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
{/if}

{if $section eq 'compose'}
{if $attaching eq 'n'}
  {if $sent eq 'n'}
    <form action="tiki-webmail.php" method="post">
    <input type="hidden" name="section" value="compose" />
    <input type="hidden" name="attach1" value="{$attach1|escape}" />
    <input type="hidden" name="attach2" value="{$attach2|escape}" />
    <input type="hidden" name="attach3" value="{$attach3|escape}" />
    <input type="hidden" name="attach1file" value="{$attach1file|escape}" />
    <input type="hidden" name="attach2file" value="{$attach2file|escape}" />
    <input type="hidden" name="attach3file" value="{$attach3file|escape}" />
    <input type="hidden" name="attach1type" value="{$attach1type|escape}" />
    <input type="hidden" name="attach2type" value="{$attach2type|escape}" />
    <input type="hidden" name="attach3type" value="{$attach3type|escape}" />
    <input type="submit" name="send" value="{tr}send{/tr}" />
    <table width="100%">
    <tr><td class="formcolor"><a title="{tr}select from address book{/tr}" class="link" href="#" onClick="javascript:window.open('tiki-webmail_contacts.php?element=to','','menubar=no,width=452,height=550');">{tr}To{/tr}</a>:</td><td colspan="3" class="formcolor"><input size="69" type="text" id="to" name="to" value="{$to|escape}" /></td></tr>
    <tr><td class="formcolor">{tr}cc{/tr}</td><td class="formcolor"><input id="cc" type="text" name="cc" value="{$cc|escape}" /></td><td class="formcolor">{tr}bcc{/tr}</td><td class="formcolor"><input type="text" name="bcc" value="{$bcc}" id="bcc" /></td></tr>
    <tr><td class="formcolor">{tr}Subject{/tr}</td><td colspan="3" class="formcolor"><input size="69" type="text" name="subject" value="{$subject|escape}" /></td></tr>
    <tr><td class="formcolor">{tr}Attachments{/tr}</td><td colspan="3" class="formcolor">
    {if $attach1}
    ({$attach1})
    {/if}
    {if $attach2}
    ({$attach2})
    {/if}
    {if $attach3}
    ({$attach3})
    {/if}
    <input type="submit" name="attach" value="{tr}add{/tr}" />
    </td></tr>
    <tr>
    <tr><td class="formcolor">&nbsp;</td>
    <td class="formcolor" colspan="3">
    <textarea name="body" cols="60" rows="30">{$body|escape}</textarea>
    <tr><td class="formcolor">{tr}Use HTML mail{/tr}</td><td colspan="3" class="formcolor"><input type="checkbox" name="useHTML" /></td></tr>
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
        <td class="heading"></td>
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
      <tr><td>&nbsp;</td><td><input type="submit" name="add_contacts" value="{tr}add contacts{/tr}" /></td></tr>
      </table>
      </form>
    {/if}
  {/if}
{else}
  <form enctype="multipart/form-data" action="tiki-webmail.php" method="post">
    <input type="hidden" name="section" value="compose" />
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
    <tr><td class="formcolor">{tr}Attachment 1{/tr}</td><td class="formcolor">{$attach1} <input type="submit" name="remove_attach1" value="{tr}remove{/tr}" /></td></tr>
    {else}
    <tr><td class="formcolor">{tr}Attachment 1{/tr}</td><td class="formcolor"><input type="hidden" name="MAX_FILE_SIZE" value="1500000"><input name="userfile1" type="file"></td></tr>
    {/if}
    {if $attach2}
    <tr><td class="formcolor">{tr}Attachment 2{/tr}</td><td class="formcolor">{$attach2} <input type="submit" name="remove_attach2" value="{tr}remove{/tr}" /></td></tr>
    {else}
    <tr><td class="formcolor">{tr}Attachment 2{/tr}</td><td class="formcolor"><input type="hidden" name="MAX_FILE_SIZE" value="1500000"><input name="userfile2" type="file"></td></tr>
    {/if}
    {if $attach3}
    <tr><td class="formcolor">{tr}Attachment 3{/tr}</td><td class="formcolor">{$attach3} <input type="submit" name="remove_attach3" value="{tr}remove{/tr}" /></td></tr>
    {else}
    <tr><td class="formcolor">{tr}Attachment 3{/tr}</td><td class="formcolor"><input type="hidden" name="MAX_FILE_SIZE" value="1500000"><input name="userfile3" type="file"></td></tr>
    {/if}
    <tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="attached" value="{tr}done{/tr}" /></td></tr>
    </table>
    </form>
  
{/if}
{/if}