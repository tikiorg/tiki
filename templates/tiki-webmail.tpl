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
<form action="tiki-webmail.php" method="post">
<input type="hidden" name="section" value="mailbox" />
<input type="submit" name="delete" value="{tr}delete{/tr}" />
<br/><br/>
<table class="normal">
<tr>
  <td class="heading"></td>
  <td class="heading">{tr}sender{/tr}</td>
  <td class="heading"></td>
  <td class="heading">{tr}subject{/tr}</td>
  <td class="heading">{tr}date{/tr}</td>
  <td class="heading">{tr}size{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$list}
<tr>
<td class="{cycle advance=false}">
<input type="checkbox" name="msg[{$list[ix].msgid}]" />
</td>
<td class="{cycle advance=false}">{$list[ix].sender.name}</td>
<td class="{cycle advance=false}">{if $list[ix].has_attachment}<img src="img/webmail/clip.gif" />{/if}</td>
<td class="{cycle advance=false}"><a class="link" href="tiki-webmail.php?section=read&amp;msgid={$list[ix].msgid}">{$list[ix].subject}</a></td>
<td class="{cycle advance=false}">{$list[ix].date}</td>
<td class="{cycle}">{$list[ix].size}</td>
</tr>
{/section}
</table>
</form>
{/if}

{if $section eq 'read'}
<table>
<tr><td>{tr}From{/tr}</td><td>{$headers.from}</td></tr>
<tr><td>{tr}To{/tr}</td><td>{$headers.to}</td></tr>
{if $headers.cc}
<tr><td>{tr}Cc{/tr}</td><td>{$headers.cc}</td></tr>
{/if}
<tr><td>{tr}Subject{/tr}</td><td>{$headers.subject}</td></tr>
<tr><td>{tr}Date{/tr}</td><td>{$headers.date}</td></tr>
</table>
<hr/>
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

