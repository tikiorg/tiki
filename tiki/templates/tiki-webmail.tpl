<a href="tiki-webmail.php" class="pagetitle">{tr}Webmail{/tr}</a><br/><br/>
<table>
<tr>
  <td>
    <a class="link" href="tiki-webmail.php?section=settings">
    <img border="0" src="img/webmail/settings.gif"><br/>
    {tr}settings{/tr}</a>
  </td>
  <td>
    <a class="link" href="tiki-webmail.php?section=mailbox">
    <img border="0" src="img/webmail/mailbox.gif"><br/>
    {tr}mailbox{/tr}</a>
  </td>
  <td>
    <a class="link" href="tiki-webmail.php?section=compose">
    <img border="0" src="img/webmail/compose.gif"><br/>
    {tr}compose{/tr}</a>
  </td>
  <td>
    <a class="link" href="tiki-webmail.php?section=contacts">
    <img border="0" src="img/webmail/contact.gif"><br/>
    {tr}contacts{/tr}</a>
  </td>
</tr>
</table>
<hr/>

{if $section eq 'settings'}
<h3>{tr}Add new mail account{/tr}</h3>
<form action="tiki-webmail.php" method="post">
<input type="hidden" name="accountId" value="{$accountId}" />
<input type="hidden" name="section" value="settings" />
<table class="normal">
<tr><td class="formcolor">{tr}Account name{/tr}</td><td class="formcolor"><input type="text" name="account" value="{$info.account}" /></td></tr>
<tr><td class="formcolor">{tr}Pop server{/tr}</td><td class="formcolor"><input type="text" name="pop" value="{$info.pop}" /></td></tr>
<tr><td class="formcolor">{tr}Username{/tr}</td><td class="formcolor"><input type="text" name="username" value="{$info.username}" /></td></tr>
<tr><td class="formcolor">{tr}Password{/tr}</td><td class="formcolor"><input type="text" name="pass" value="{$info.pass}" /></td></tr>
<tr><td class="formcolor">{tr}Port{/tr}</td><td class="formcolor"><input type="text" name="port" size="7" value="{$info.port}" /></td></tr>
<tr><td class="formcolor">{tr}Messages per page{/tr}</td><td class="formcolor"><input type="text" name="msgs" size="7" value="{$info.msgs}" /></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="new_acc" value="add" /></td></tr>
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
[<a href="tiki-webmail.php?section=settings&amp;remove={$accounts[ix].accountId}" class="link">x</a>|<a href="tiki-webmail.php?section=settings&amp;accountId={$accounts[ix].accountId}" class="tablename">edit</a>]
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
</table><br/>
<form action="tiki-webmail.php" method="post">
<input type="hidden" name="section" value="mailbox" />
<input type="submit" name="delete" value="{tr}delete{/tr}" />
<input type="hidden" name="start" value="{$start}" />
<select name="action">
<option value="flag">{tr}Mark as Flagged{/tr}</option>
<option value="unflag">{tr}Mark as unflagged{/tr}</option>
<option value="read">{tr}Mark as read{/tr}</option>
<option value="unread">{tr}Mark as unread{/tr}</option>
</select>
<input type="submit" name="operate" value="{tr}ok{/tr}" />
<br/><br/>
<table class="normal">
<tr>
  <td class="heading"></td>
  <td class="heading"></td>
  <td class="heading">{tr}sender{/tr}</td>
  <td class="heading"></td>
  <td class="heading">{tr}subject{/tr}</td>
  <td class="heading">{tr}date{/tr}</td>
  <td class="heading">{tr}size{/tr}</td>
</tr>
{section name=ix loop=$list}
{if $list[ix].isRead eq 'y'}
{assign var=class value=even}
{else}
{assign var=class value=odd}
{/if}
<tr>
<td class="{$class}">
<input type="checkbox" name="msg[{$list[ix].msgid}]" />
<input type="hidden" name="realmsg[{$list[ix].msgid}]" value="{$list[ix].realmsgid}" />
</td>
<td class="{$class}">
{if $list[ix].isFlagged eq 'y'}
<img src="img/webmail/flagged.gif" />
{/if}
{if $list[ix].isReplied eq 'y'}
<img src="img/webmail/replied.gif" />
{/if}
</td>
<td class="{$class}">{$list[ix].sender.name}</td>
<td class="{$class}">{if $list[ix].has_attachment}<img src="img/webmail/clip.gif" />{/if}</td>
<td class="{$class}"><a class="link" href="tiki-webmail.php?section=read&amp;msgid={$list[ix].msgid}">{$list[ix].subject}</a></td>
<td class="{$class}">{$list[ix].date}</td>
<td class="{$class}">{$list[ix].size}</td>
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
    <input type="submit" name="delete" value="{tr}delete{/tr}" />
  </td>
  <td>
    <input type="submit" name="reply" value="{tr}reply{/tr}" />
  </td>
  <td>
    <input type="submit" name="replyall" value="{tr}reply all{/tr}" />
  </td>
  <td>
    <input type="submit" name="reply" value="{tr}forward{/tr}" />
    <select name="type">
      <option value="attach">{tr}As attachment{/tr}</option>
      <option value="inline">{tr}As inline text{/tr}</option>
    </select>
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
    {$item[ix]}<br/> 
    {sectionelse}
    {$item}
    {/section}
    </td></tr>
  {/foreach}
{/if}
</table>
<br/>
{section name=ix loop=$bodies}
{$bodies[ix]}
<hr/>
{/section}
</div>
{section name=ix loop=$attachs}
<div class="simplebox">
<a class="link" href="tiki-webmail_download_attachment.php?section=read&amp;msgid={$msgid}&amp;getpart={$attachs[ix].part}">{$attachs[ix].name|iconify}{$attachs[ix].name}</a>
</div>
{/section}
{/if}

{if $section eq 'compose'}
<table width="100%">
<tr><td class="formcolor">{tr}To{/tr}</td><td colspan="3" class="formcolor"><input size="69" type="text" name="to" value="{$to}" /></td></tr>
<tr><td class="formcolor">{tr}cc{/tr}</td><td class="formcolor"><input type="text" name="cc" value="{$cc}" /></td><td class="formcolor">{tr}bcc{/tr}</td><td class="formcolor"><input type="text" name="bcc" value="{$bcc}" /></td></tr>
<tr><td class="formcolor">{tr}Subject{/tr}</td><td colspan="3" class="formcolor"><input size="69" type="text" name="subject" /></td></tr>
<tr><td class="formcolor">{tr}Attachments{/tr}</td><td colspan="3" class="formcolor">

</td></tr>
<tr>
<tr><td class="formcolor">&nbsp;</td>
<td class="formcolor" colspan="3">
<textarea name="body" cols="60" rows="30"></textarea>
</td></tr>
</table>

{/if}