<a href="tiki-admin_mailin.php" class="pagetitle">{tr}Mailin accounts{/tr}</a><br/><br/>
<a class="link" href="tiki-mailin.php">Mailin</a><br/><br/>
<h3>{tr}Add new mail account{/tr}</h3>
<form action="tiki-admin_mailin.php" method="post">
<input type="hidden" name="accountId" value="{$accountId}" />
<table class="normal">
<tr><td class="formcolor">{tr}Account name{/tr}</td><td colspan="3" class="formcolor"><input type="text" name="account" value="{$info.account}" /></td></tr>
<tr><td class="formcolor">{tr}POP server{/tr}</td><td class="formcolor"><input type="text" name="pop" value="{$info.pop}" /></td><td class="formcolor">{tr}Port{/tr}</td><td class="formcolor"><input type="text" name="port" size="7" value="{$info.port}" /></td></tr>
<tr><td class="formcolor">{tr}SMTP server{/tr}</td><td class="formcolor"><input type="text" name="smtp" value="{$info.smtp}" /></td><td class="formcolor">{tr}Port{/tr}</td><td class="formcolor"><input type="text" name="smtpPort" size="7" value="{$info.smtpPort}" /></td></tr>
<tr><td class="formcolor">{tr}SMTP requires authentication{/tr}</td><td  colspan="3" class="formcolor">{tr}Yes{/tr}<input type="radio" name="useAuth" value="yes" {if $info.useAuth eq 'y'}checked="checked"{/if} /> {tr}No{/tr}<input type="radio" name="useAuth" value="no" {if $info.useAuth eq 'n'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Username{/tr}</td><td  colspan="3" class="formcolor"><input type="text" name="username" value="{$info.username}" /></td></tr>
<tr><td class="formcolor">{tr}Password{/tr}</td><td  colspan="3" class="formcolor"><input type="text" name="pass" value="{$info.pass}" /></td></tr>
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
<tr><td class="formcolor">&nbsp;</td><td colspan="3" class="formcolor"><input type="submit" name="new_acc" value="add" /></td></tr>
</table>
</form>
<h3>{tr}User accounts{/tr}</h3>
<table class="normal">
<tr>
<td class="heading">{tr}account{/tr}</td>
<td class="heading">{tr}type{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$accounts}
<td class="{cycle advance=false}"><a href="tiki-admin_mailin.php}" class="{if $accounts[ix].current eq 'y'}tablename{else}link{/if}">{$accounts[ix].account}</a>
[<a href="tiki-admin_mailin.php?remove={$accounts[ix].accountId}" class="link">x</a>|<a href="tiki-admin_mailin.php?accountId={$accounts[ix].accountId}" class="tablename">edit</a>]
</td>
<td class="{cycle}">{$accounts[ix].type}</td>
</tr>
{/section}
</table>
<br/><br/>