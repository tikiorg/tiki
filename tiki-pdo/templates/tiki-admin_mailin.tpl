<h1><a href="tiki-admin_mailin.php" class="pagetitle">{tr}Mail-in accounts{/tr}</a>

{if $prefs.feature_help eq 'y'}
  <a href="{$prefs.helpurl}Webmail" target="tikihelp" class="tikihelp" title="{tr}Admin Webmail{/tr}">
  {icon _id='help'}</a>
{/if}

{if $prefs.feature_view_tpl eq 'y'}
  <a href="tiki-edit_templates.php?template=tiki-admin_mailin.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}Admin Mailing Template{/tr}">
  {icon _id='shape_square_edit'}</a>
{/if}</h1>

<table class="normal">
  <tr>
    <td class="heading">
      {tr}Action{/tr}
    </td>
    <td class="heading">
      {tr}Account{/tr}
    </td>
    <td class="heading">{tr}Type{/tr}</td>
  </tr>
  {cycle values="even,odd" print=false}
  {section name=ix loop=$accounts}
    <tr>
      <td class="{cycle advance=false}" align="middle">
        <a href="tiki-admin_mailin.php?accountId={$accounts[ix].accountId}#add">{icon _id='page_edit'}</a> &nbsp;
        <a href="tiki-admin_mailin.php?remove={$accounts[ix].accountId}" >{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
      </td>
      <td class="{cycle advance=false}">{$accounts[ix].account}</td>
      <td class="{cycle}">{tr}{$accounts[ix].type}{/tr}</td>
    </tr>
  {/section}
</table>
<br />
{if $tikifeedback}
{section name=n loop=$tikifeedback}<div class="simplebox {if $tikifeedback[n].num > 0} highlight{/if}">{$tikifeedback[n].mes}</div>{/section}
{/if}
<br />

<a name="add" ></a><h2>{if $accountId eq 0}{tr}Add new Mail-in account{/tr}{else}{tr}Edit Mail-in account{/tr}: <i>{$info.account}</i>{/if}</h2>
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
      <td colspan="3"><input type="password" name="pass" value="{$info.pass|escape}" /></td>
    </tr>
    <tr class="formcolor">
      <td>{tr}Type{/tr}</td>
      <td colspan="3">
        <select name="type" id='mailin_type' onchange='javascript:chgMailinType();'>
          <option value="article-put" {if $info.type eq 'article-put'}selected="selected"{/if}>{tr}article-put{/tr}</option>
          <option value="wiki-get" {if $info.type eq 'wiki-get'}selected="selected"{/if}>{tr}wiki-get{/tr}</option>
          <option value="wiki-put" {if $info.type eq 'wiki-put'}selected="selected"{/if}>{tr}wiki-put{/tr}</option>
          <option value="wiki-append" {if $info.type eq 'wiki-append'}selected="selected"{/if}>{tr}wiki-append{/tr}</option>
          <option value="wiki" {if $info.type eq 'wiki'}selected="selected"{/if}>{tr}wiki{/tr}</option>
        </select>
      </td>
    </tr>

<tr id='article_topic' class="formcolor" {if $info.type ne 'article-put'}style="display:none;"{/if}><td>{tr}Article Topic{/tr}</td><td>
<select name="article_topicId">
{section name=t loop=$topics}
<option value="{$topics[t].topicId|escape}" {if $info.article_topicId eq $topics[t].topicId}selected="selected"{/if}>{$topics[t].name}</option>
{/section}
<option value="" {if $info.article_topicId eq 0}selected="selected"{/if}>{tr}None{/tr}</option>
</select>
{if $tiki_p_admin_cms eq 'y'}<a href="tiki-admin_topics.php" class="link">{tr}Admin topics{/tr}</a>{/if}
</td><td></td><td></td></tr>
<tr id='article_type' class="formcolor" {if $info.type ne 'article-put'}style="display:none;"{/if}><td>{tr}Article Type{/tr}</td><td>
<select id='articletype' name='article_type'>
<option value="">-</option>
{section name=t loop=$types}
<option value="{$types[t].type|escape}" {if $info.article_type eq $types[t].type}selected="selected"{/if}>{tr}{$types[t].type}{/tr}</option>
{/section}
</select>
{if $tiki_p_admin_cms eq 'y'}<a href="tiki-article_types.php" class="link">{tr}Admin types{/tr}</a>{/if}
</td><td></td><td></td></tr>

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
      <td>{tr}Discard to the end from{/tr}</td>
      <td colspan="3">
      <input type="text" name="discard_after" value="{$info.discard_after|escape}" />
      </td>
   </tr>
    <tr class="formcolor">
      <td>&nbsp;</td>
      <td colspan="3"><input type="submit" name="new_acc" value="{if $accountId eq 0}{tr}Add{/tr}{else}{tr}Edit{/tr}{/if}" /></td>
    </tr>
  </table>
</form>


<a name="check" ></a><h2><a href="tiki-mailin.php#check">{tr}Check Mail-in accounts{/tr}</a></h2>
<form action="tiki-admin_mailin.php" method="post">
  <table class="normal">
    <tr class="formcolor">
      <td>{tr}Check automatically{/tr}</td>
      <td>
        {tr}Yes{/tr}
        <input type="radio" name="mailin_autocheck" {if $prefs.mailin_autocheck eq 'y'}checked="checked"{/if} value="y" />
        {tr}No{/tr}
        <input type="radio" name="mailin_autocheck" {if $prefs.mailin_autocheck eq 'n'}checked="checked"{/if} value="n" />
      </td>
    </tr>
    <tr class="formcolor">
      <td>{tr}Frequency{/tr}</td>
      <td><input type="text" name="mailin_autocheckFreq" size="6" value="{$prefs.mailin_autocheckFreq}" />&nbsp;{tr}mn{/tr}</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="set_auto" value="{tr}Set{/tr}" /></td>
    </tr>
  </table>
</form>
<br /><br />
