<a href="tiki-admin_mailin.php" class="pagetitle">{tr}WebMail accounts{/tr}</a>

<!-- the help link info -->
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=Webmail" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}admin Webmail{/tr}"><img src='img/icons/help.gif' alt='help' /></a>{/if}

<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admin_mailin.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin mailin tpl{/tr}"><img src='img/icons/info.gif' alt='edit tpl' /></a>{/if}

<!-- begin -->

<br /><br />
<a class="linkbut" href="tiki-mailin.php">Mailin</a>

<br /><br />
<h3>{tr}Add new mail account{/tr}</h3>
<form action="tiki-admin_mailin.php" method="post">
<input type="hidden" name="accountId" value="{$accountId|escape}" />
<table>
<tr><td>{tr}Account name{/tr}</td><td colspan="3"><input type="text" name="account" value="{$info.account|escape}" /></td></tr>
<tr><td>{tr}POP server{/tr}</td><td><input type="text" name="pop" value="{$info.pop|escape}" /></td><td>{tr}Port{/tr}</td><td><input type="text" name="port" size="7" value="{$info.port}" /></td></tr>
<tr><td>{tr}SMTP server{/tr}</td><td><input type="text" name="smtp" value="{$info.smtp|escape}" /></td><td>{tr}Port{/tr}</td><td><input type="text" name="smtpPort" size="7" value="{$info.smtpPort}" /></td></tr>
<tr><td>{tr}SMTP requires authentication{/tr}</td><td  colspan="3">{tr}Yes{/tr}<input type="radio" name="useAuth" value="yes" {if $info.useAuth eq 'y'}checked="checked"{/if} /> {tr}No{/tr}<input type="radio" name="useAuth" value="no" {if $info.useAuth eq 'n'}checked="checked"{/if} /></td></tr>
<tr><td>{tr}Username{/tr}</td><td  colspan="3"><input type="text" name="username" value="{$info.username|escape}" /></td></tr>
<tr><td>{tr}Password{/tr}</td><td  colspan="3"><input type="text" name="pass" value="{$info.pass|escape}" /></td></tr>
<tr><td>{tr}Type{/tr}</td><td colspan="3">
<select name="type">
<option value="wiki-get" {if $info.type eq 'wiki-get'}selected="selected"{/if}>{tr}wiki-get{/tr}</option>
<option value="wiki-put" {if $info.type eq 'wiki-put'}selected="selected"{/if}>{tr}wiki-put{/tr}</option>
<option value="wiki-append" {if $info.type eq 'wiki-append'}selected="selected"{/if}>{tr}wiki-append{/tr}</option>
<option value="wiki" {if $info.type eq 'wiki'}selected="selected"{/if}>{tr}wiki{/tr}</option>
</select>
</td></tr>
<tr><td>{tr}Active{/tr}</td><td colspan="3">
{tr}Yes{/tr}<input type="radio" name="active" {if $info.active eq 'y'}checked="checked"{/if} value="y" /> <input type="radio" {if $info.active eq 'n'}checked="checked"{/if} name="active" value="n" />{tr}No{/tr}
</td></tr>
<tr><td>&nbsp;</td><td colspan="3"><input type="submit" name="new_acc" value="{tr}add{/tr}" /></td></tr>
</table>
</form>
<h3>{tr}User accounts{/tr}</h3>
<table>
<tr>
<th>{tr}account{/tr}</th>
<th>{tr}type{/tr}</th>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$accounts}
<tr><td class="{cycle advance=false}"><a href="tiki-admin_mailin.php">{$accounts[ix].account}</a>
[<a href="tiki-admin_mailin.php?remove={$accounts[ix].accountId}">x</a>|<a href="tiki-admin_mailin.php?accountId={$accounts[ix].accountId}">edit</a>]
</td>
<td class="{cycle}">{$accounts[ix].type}</td>
</tr>
{/section}
</table>
<br /><br />