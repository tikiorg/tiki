<a href="tiki-admin_mailin.php" class="pagetitle">{tr}Mail-in accounts{/tr}</a>

  
<!-- the help link info -->
{if $feature_help eq 'y'}
  <a href="http://tikiwiki.org/tiki-index.php?page=Webmail" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}admin Webmail{/tr}">
  <img border='0' src='img/icons/help.gif' alt="{tr}help{/tr}" /></a>
{/if}

<!-- link to tpl -->
{if $feature_view_tpl eq 'y'}
  <a href="tiki-edit_templates.php?template=templates/tiki-admin_mailin.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}admin mailin template{/tr}">
  <img border='0' src='img/icons/info.gif' alt='{tr}edit{/tr}' /></a>
{/if}

<!-- begin -->
<br /><br />



<table class="normal">
  <tr>
    <td class="heading" width="25%">
      [<a class="tablename" href="tiki-admin_mailin.php">{tr}add new{/tr} {tr}Account{/tr}</a>]
    </td>
    <td class="heading" width="50%">
      {tr}Account{/tr}
    </td>
    <td class="heading" width="25%">{tr}type{/tr}</td>
  </tr>
  {cycle values="even,odd" print=false}
  {section name=ix loop=$accounts}
    <tr>
      <td class="{cycle advance=false}" align="right">
        [<a href="tiki-admin_mailin.php?accountId={$accounts[ix].accountId}" class="tablename">{tr}edit{/tr}</a> |
         <a href="tiki-admin_mailin.php?remove={$accounts[ix].accountId}" class="tablename">{tr}delete{/tr}</a>]
      </td>
      <td class="{cycle advance=false}">{$accounts[ix].account}</td>
      <td class="{cycle}">{$accounts[ix].type}</td>
    </tr>
  {/section}
</table>
<br /><br />

<h3>{tr}Edit{/tr}/{tr}Add new mail account{/tr}</h3>
<form action="tiki-admin_mailin.php" method="post">
  <input type="hidden" name="accountId" value="{$accountId|escape}" />
  <table class="normal">
    <tr class="formcolor">
      <td>{tr}Account name{/tr}</td>
      <td colspan="3"><input type="text" name="account" value="{$info.account|escape}" /></td>
    </tr>
    <tr class="formcolor">
      <td>{tr}POP server{/tr}</td>
      <td><input type="text" name="pop" value="{$info.pop|escape}" /></td>
      <td>{tr}Port{/tr}</td>
      <td><input type="text" name="port" size="7" value="{$info.port}" /></td>
    </tr>
    <tr class="formcolor">
      <td>{tr}SMTP server{/tr}</td>
      <td><input type="text" name="smtp" value="{$info.smtp|escape}" /></td>
      <td>{tr}Port{/tr}</td>
      <td><input type="text" name="smtpPort" size="7" value="{$info.smtpPort}" /></td>
    </tr>
    <tr class="formcolor">
      <td>{tr}SMTP requires authentication{/tr}</td>
      <td colspan="3">
        {tr}Yes{/tr}
        <input type="radio" name="useAuth" value="y" {if $info.useAuth eq 'y'}checked="checked"{/if} />
        {tr}No{/tr}
        <input type="radio" name="useAuth" value="n" {if $info.useAuth eq 'n'}checked="checked"{/if} />
      </td>
    </tr>
    <tr class="formcolor">
      <td>{tr}Username{/tr}</td>
      <td colspan="3"><input type="text" name="username" value="{$info.username|escape}" /></td>
    </tr>
    <tr class="formcolor">
      <td>{tr}Password{/tr}</td>
      <td colspan="3"><input type="text" name="pass" value="{$info.pass|escape}" /></td>
    </tr>
    <tr class="formcolor">
      <td>{tr}Type{/tr}</td>
      <td colspan="3">
        <select name="type">
          <option value="wiki-get" {if $info.type eq 'wiki-get'}selected="selected"{/if}>{tr}wiki-get{/tr}</option>
          <option value="wiki-put" {if $info.type eq 'wiki-put'}selected="selected"{/if}>{tr}wiki-put{/tr}</option>
          <option value="wiki-append" {if $info.type eq 'wiki-append'}selected="selected"{/if}>{tr}wiki-append{/tr}</option>
          <option value="wiki" {if $info.type eq 'wiki'}selected="selected"{/if}>{tr}wiki{/tr}</option>
        </select>
      </td>
    </tr>
    <tr class="formcolor">
      <td>{tr}Active{/tr}</td>
      <td colspan="3">
        {tr}Yes{/tr}
        <input type="radio" name="active" {if $info.active eq 'y'}checked="checked"{/if} value="y" />
        {tr}No{/tr}
        <input type="radio" name="active" {if $info.active eq 'n'}checked="checked"{/if} value="n" />
      </td>
    </tr>
    <tr class="formcolor">
      <td>{tr}Allow anonymous acces{/tr}</td>
      <td colspan="3">
        {tr}Yes{/tr}
        <input type="radio" name="anonymous" {if $info.anonymous eq 'y'}checked="checked"{/if} value="y" />
        {tr}No{/tr}
        <input type="radio" name="anonymous" {if $info.anonymous eq 'n'}checked="checked"{/if} value="n" />
      </td>
    </tr>
    <tr class="formcolor">
      <td>{tr}Allow attachments{/tr}</td>
      <td colspan="3">
        {tr}Yes{/tr}
        <input type="radio" name="attachments" {if $info.attachments eq 'y'}checked="checked"{/if} value="y" />
        {tr}No{/tr}
        <input type="radio" name="attachments" {if $info.attachments eq 'n'}checked="checked"{/if} value="n" />
      </td>
    </tr>
    <tr class="formcolor">
      <td>&nbsp;</td>
      <td colspan="3"><input type="submit" name="new_acc" value="{tr}add{/tr}" /></td>
    </tr>
  </table>
</form>


<br /><br /><br />
<b><a class="readlink" href="tiki-mailin.php">{tr}Check Mail-in accounts{/tr}</a></b>
<br /><br />
<form action="tiki-admin_mailin.php" method="post">
  <table class="normal">
    <tr class="formcolor">
      <td>{tr}Check automatically{/tr}</td>
      <td>
        {tr}Yes{/tr}
        <input type="radio" name="mailin_autocheck" {if $mailin_autocheck eq 'y'}checked="checked"{/if} value="y" />
        {tr}No{/tr}
        <input type="radio" name="mailin_autocheck" {if $mailin_autocheck eq 'n'}checked="checked"{/if} value="n" />
      </td>
    </tr>
    <tr class="formcolor">
      <td>{tr}Frequency{/tr}</td>
      <td><input type="text" name="mailin_autocheckFreq" size="6" value="{$mailin_autocheckFreq}" />&nbsp;mn</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="set_auto" value="{tr}set{/tr}" /></td>
    </tr>
  </table>
</form>
<br /><br />
