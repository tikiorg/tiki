<a href="tiki-admin_mailin.php" class="pagetitle">{tr}WebMail Accounts{/tr}</a>

<!-- the help link info -->
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=Webmail" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}admin Webmail{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}
<!-- There are definite disconnects between the tpl and php and what people call this-->
<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admin_mailin.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin mailin tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='edit tpl' /></a>{/if}

<!-- begin -->




<br /><br />
[<a class="linkbut" href="tiki-mailin.php">Mailin</a>]
<!-- What is this link? What does it do? -->


<br /><br />
<h3>{tr}Add new mail account{/tr}</h3>
<form action="tiki-admin_mailin.php" method="post">
<input type="hidden" name="accountId" value="{$accountId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Account name{/tr}</td><td colspan="3" class="formcolor"><input type="text" name="account" value="{$info.account|escape}" /></td></tr>
<tr><td class="formcolor">{tr}POP server{/tr}</td><td class="formcolor"><input type="text" name="pop" value="{$info.pop|escape}" /></td><td class="formcolor">{tr}Port{/tr}</td><td class="formcolor"><input type="text" name="port" size="7" value="{$info.port}" /></td></tr>
<tr><td class="formcolor">{tr}SMTP server{/tr}</td><td class="formcolor"><input type="text" name="smtp" value="{$info.smtp|escape}" /></td><td class="formcolor">{tr}Port{/tr}</td><td class="formcolor"><input type="text" name="smtpPort" size="7" value="{$info.smtpPort}" /></td></tr>
<tr><td class="formcolor">{tr}SMTP requires authentication{/tr}</td><td  colspan="3" class="formcolor">{tr}Yes{/tr}<input type="radio" name="useAuth" value="yes" {if $info.useAuth eq 'y'}checked="checked"{/if} /> {tr}No{/tr}<input type="radio" name="useAuth" value="no" {if $info.useAuth eq 'n'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Username{/tr}</td><td  colspan="3" class="formcolor"><input type="text" name="username" value="{$info.username|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Password{/tr}</td><td  colspan="3" class="formcolor"><input type="text" name="pass" value="{$info.pass|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Type{/tr}</td><td colspan="3" class="formcolor">
<select name="type">
<option value="wiki-get" {if $info.type eq 'wiki-get'}selected="selected"{/if}>{tr}wiki-get{/tr}</option>
<option value="wiki-put" {if $info.type eq 'wiki-put'}selected="selected"{/if}>{tr}wiki-put{/tr}</option>
<option value="wiki-append" {if $info.type eq 'wiki-append'}selected="selected"{/if}>{tr}wiki-append{/tr}</option>
<option value="wiki" {if $info.type eq 'wiki'}selected="selected"{/if}>{tr}wiki{/tr}</option>
</select>
</td></tr>
<tr><td class="formcolor">{tr}Active{/tr}</td><td colspan="3" class="formcolor">
{tr}Yes{/tr}<input type="radio" name="active" {if $info.active eq 'y'}checked="checked"{/if} value="y" /> <input type="radio" {if $info.active eq 'n'}checked="checked"{/if} name="active" value="n" />{tr}No{/tr}
</td></tr>
<tr><td class="formcolor">&nbsp;</td><td colspan="3" class="formcolor"><input type="submit" name="new_acc" value="{tr}Add{/tr}" /></td></tr>
</table>
</form>
<h3>{tr}User accounts{/tr}</h3>
<table class="normal">
<tr>
<td class="heading">{tr}Account{/tr}</td>
<td class="heading">{tr}Type{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$accounts}
<tr><td class="{cycle advance=false}"><a href="tiki-admin_mailin.php" class="{if $accounts[ix].current eq 'y'}tablename{else}link{/if}">{$accounts[ix].account}</a>
[<a href="tiki-admin_mailin.php?remove={$accounts[ix].accountId}" class="link" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this account?{/tr}')" >Delete</a>|<a class="link" href="tiki-admin_mailin.php?accountId={$accounts[ix].accountId}" class="tablename">Edit</a>]
</td>
<td class="{cycle}">{$accounts[ix].type}</td>
</tr>
{/section}
</table>
<br /><br />
