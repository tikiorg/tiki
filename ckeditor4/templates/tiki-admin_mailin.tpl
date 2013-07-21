{* $Id$ *}
{title help="Webmail"}{tr}Mail-in accounts{/tr}{/title}

<table class="normal" style="text-align:center">
  <tr>
    <th>
      {tr}Action{/tr}
    </th>
    <th>
      {tr}Account{/tr}
    </th>
    <th>{tr}Type{/tr}</th>
    <th>{tr}Active{/tr}</th>
    <th>{tr}Anonym{/tr}</th>
    <th>{tr}Admin{/tr}</th>
    <th>{tr}Attach{/tr}</th>
    <th>{tr}Route{/tr}</th>
    <th>{tr}Inline{/tr}</th>
    <th>{tr}HTML{/tr}</th>
    <th>{tr}Categ{/tr}</th>
    <th>{tr}Namespace{/tr}</th>
    <th>{tr}Email{/tr}</th>
    <th>{tr}Leave{/tr}</th>
  </tr>
  {cycle values="even,odd" print=false}
  {section name=ix loop=$accounts}
    <tr class="{cycle}">
      <td class="action">
        <a href="tiki-admin_mailin.php?accountId={$accounts[ix].accountId}#add">{icon _id='page_edit'}</a>
        <a href="tiki-admin_mailin.php?remove={$accounts[ix].accountId}" >{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
      </td>
      <td class="text">{$accounts[ix].account}</td>
      <td class="text">{tr}{$accounts[ix].type}{/tr}</td>
      <td class="text">{tr}{$accounts[ix].active}{/tr}</td>
      <td class="text">{tr}{$accounts[ix].anonymous}{/tr}</td>
      <td class="text">{tr}{$accounts[ix].admin}{/tr}</td>
      <td class="text">{tr}{$accounts[ix].attachments}{/tr}</td>
      <td class="text">{tr}{$accounts[ix].routing}{/tr}</td>
      <td class="text">{tr}{$accounts[ix].show_inlineImages}{/tr}</td>
      <td class="text">{tr}{$accounts[ix].save_html}{/tr}</td>
      <td class="text">{tr}{$accounts[ix].categoryId}{/tr}</td>
      <td class="text">{tr}{$accounts[ix].namespace}{/tr}</td>
      <td class="text">{tr}{$accounts[ix].respond_email}{/tr}</td>
      <td class="text">{tr}{$accounts[ix].leave_email}{/tr}</td>
	  
    </tr>
  {/section}
</table>
<br>
{button _icon="img/icons/large/messages.gif" _text="{tr}Admin Mail-in Routes{/tr}" href="tiki-admin_mailin_routes.php" _menu_text="y"}
<br>
{if $tikifeedback}
	{section name=n loop=$tikifeedback}<div class="simplebox {if $tikifeedback[n].num > 0} highlight{/if}">{$tikifeedback[n].mes}</div>{/section}
	<br>
{/if}
<a name="check" ></a><h2><a href="tiki-mailin.php#check">{tr}Check Mail-in accounts{/tr}</a></h2>
<form action="tiki-admin_mailin.php" method="post">
  <table class="formcolor">
    <tr>
      <td>{tr}Check automatically{/tr}</td>
      <td>
		&nbsp;&nbsp;
        {tr}Yes{/tr}
        <input type="radio" name="mailin_autocheck" {if $prefs.mailin_autocheck eq 'y'}checked="checked"{/if} value="y">
		&nbsp;&nbsp;
        {tr}No{/tr}
        <input type="radio" name="mailin_autocheck" {if $prefs.mailin_autocheck eq 'n'}checked="checked"{/if} value="n">
      </td>
    </tr>
    <tr>
      <td>{tr}Frequency{/tr}</td>
      <td><input type="text" name="mailin_autocheckFreq" size="6" value="{$prefs.mailin_autocheckFreq}">&nbsp;{tr}minutes{/tr}</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="set_auto" value="{tr}Set{/tr}"></td>
    </tr>
    <tr>
      <td>{tr}Check manually{/tr}</td>
      <td><a href="tiki-mailin.php">Check Now</a></td>
    </tr>
  </table>
</form>

<a name="add" ></a><h2>{if $accountId eq 0}{tr}Add new Mail-in account{/tr}{else}{tr}Edit Mail-in account:{/tr} <i>{$info.account}</i>{/if}</h2>
<form action="tiki-admin_mailin.php" method="post">
  <input type="hidden" name="accountId" value="{$accountId|escape}">
  <table class="formcolor">
    <tr>
      <td>{tr}Account name{/tr}</td>
      <td colspan="4"><input type="text" name="account" value="{$info.account|escape}"></td>
    </tr>
    <tr>
      <td>{tr}POP server{/tr}</td>
      <td><input type="text" name="pop" value="{$info.pop|escape}"></td>
      <td>{tr}Port{/tr}</td>
      <td><input type="text" name="port" size="7" value="{$info.port}"></td>
	  <td>&nbsp;</td>
    </tr>
    <tr>
      <td>{tr}SMTP server{/tr}</td>
      <td><input type="text" name="smtp" value="{$info.smtp|escape}"></td>
      <td>{tr}Port{/tr}</td>
      <td><input type="text" name="smtpPort" size="7" value="{$info.smtpPort}"></td>
	  <td>&nbsp;</td>
    </tr>
    <tr>
      <td>{tr}SMTP requires authentication{/tr}</td>
      <td colspan="4">
        {tr}Yes{/tr}
        <input type="radio" name="useAuth" value="y" {if $info.useAuth eq 'y'}checked="checked"{/if}>
        {tr}No{/tr}
        <input type="radio" name="useAuth" value="n" {if $info.useAuth eq 'n'}checked="checked"{/if}>
      </td>
    </tr>
    <tr>
      <td>{tr}Username{/tr}</td>
      <td colspan="4"><input type="text" name="username" value="{$info.username|escape}"></td>
    </tr>
    <tr>
      <td>{tr}Password{/tr}</td>
      <td colspan="4"><input type="password" name="pass" value="{$info.pass|escape}"></td>
    </tr>
    <tr>
      <td>{tr}Type{/tr}</td>
      <td colspan="3">
        <select name="type" id='mailin_type' onchange='javascript:chgMailinType();'>
		  {if $prefs.feature_articles eq 'y'}
			<option value="article-put" {if $info.type eq 'article-put'}selected="selected"{/if}>{tr}article-put{/tr}</option>
          {/if}
          <option value="wiki-get" {if $info.type eq 'wiki-get'}selected="selected"{/if}>{tr}wiki-get{/tr}</option>
          <option value="wiki-put" {if $info.type eq 'wiki-put'}selected="selected"{/if}>{tr}wiki-put{/tr}</option>
          <option value="wiki-prepend" {if $info.type eq 'wiki-prepend'}selected="selected"{/if}>{tr}wiki-prepend{/tr}</option>
          <option value="wiki-append" {if $info.type eq 'wiki-append'}selected="selected"{/if}>{tr}wiki-append{/tr}</option>
          <option value="wiki" {if $info.type eq 'wiki'}selected="selected"{/if}>{tr}wiki{/tr}</option>
        </select>
      </td>
	  <td>{tr}put wiki/article => create/update from email. get wiki => send page to user as email{/tr}</td>
    </tr>

{if $prefs.feature_articles eq 'y'}
<tr id='article_topic' {if $info.type ne 'article-put'}style="display:none;"{/if}><td>{tr}Article Topic{/tr}</td><td>
<select name="article_topicId">
{foreach $topics as $topicId=>$topic}
<option value="{$topicId|escape}" {if $info.article_topicId eq $topicId}selected="selected"{/if}>{$topic.name}</option>
{/foreach}
<option value="" {if $info.article_topicId eq 0}selected="selected"{/if}>{tr}None{/tr}</option>
</select>
{if $tiki_p_admin_cms eq 'y'}<a href="tiki-admin_topics.php" class="link">{tr}Admin Topics{/tr}</a>{/if}
</td><td></td><td></td>
<td>&nbsp;</td>
</tr>
<tr id='article_type' {if $info.type ne 'article-put'}style="display:none;"{/if}><td>{tr}Article Type{/tr}</td><td>
<select id='articletype' name='article_type'>
<option value="">-</option>
{section name=t loop=$types}
<option value="{$types[t].type|escape}" {if $info.article_type eq $types[t].type}selected="selected"{/if}>{tr}{$types[t].type}{/tr}</option>
{/section}
</select>
{if $tiki_p_admin_cms eq 'y'}<a href="tiki-article_types.php" class="link">{tr}Admin Types{/tr}</a>{/if}
</td><td></td><td></td>
 <td>&nbsp;</td>
</tr>
{/if}
    <tr>
      <td>{tr}Active{/tr}</td>
      <td colspan="3">
        {tr}Yes{/tr}
        <input type="radio" name="active" {if $info.active eq 'y'}checked="checked"{/if} value="y">
        {tr}No{/tr}
        <input type="radio" name="active" {if $info.active eq 'n'}checked="checked"{/if} value="n">
      </td>
	  <td>&nbsp;</td>
    </tr>
    <tr>
      <td>{tr}Allow anonymous access{/tr}</td>
      <td colspan="3">
        {tr}Yes{/tr}
        <input type="radio" name="anonymous" {if $info.anonymous eq 'y'}checked="checked"{/if} value="y">
        {tr}No{/tr}
        <input type="radio" name="anonymous" {if $info.anonymous eq 'n'}checked="checked"{/if} value="n">
      </td>
	  <td>{tr}Warning: Enabling anonymous access will disable all permission checking for mailed-in content{/tr}.</td>
    </tr>
    <tr>
      <td>{tr}Allow admin access{/tr}</td>
      <td colspan="3">
        {tr}Yes{/tr}
        <input type="radio" name="admin" {if $info.admin eq 'y'}checked="checked"{/if} value="y">
        {tr}No{/tr}
        <input type="radio" name="admin" {if $info.admin eq 'n'}checked="checked"{/if} value="n">
      </td>
	  <td>{tr}Administrators have full access to the system. Disabling admin mail-in is the safest option{/tr}.</td>
    </tr>
    <tr>
      <td>{tr}Allow attachments{/tr}</td>
      <td colspan="4">
	   {if $prefs.feature_wiki_attachments eq 'y'}
        {tr}Yes{/tr}
        <input type="radio" name="attachments" {if $info.attachments eq 'y'}checked="checked"{/if} value="y">
        {tr}No{/tr}
        <input type="radio" name="attachments" {if $info.attachments eq 'n'}checked="checked"{/if} value="n">
	    {else}
		<a href="tiki-admin.php?page=wiki&cookietab=2&highlight=feature_wiki_attachments">Activate attachments</a>
		{/if}
      </td>
    </tr>
    <tr>
      <td>{tr}Allow Routing{/tr}</td>
      <td colspan="3">
	   {if $prefs.feature_wiki eq 'y'}
        {tr}Yes{/tr}
        <input type="radio" name="routing" {if $info.routing eq 'y'}checked="checked"{/if} value="y">
        {tr}No{/tr}
        <input type="radio" name="routing" {if $info.routing eq 'n'}checked="checked"{/if} value="n">
	    {else}
		<a href="tiki-admin.php?page=wiki&cookietab=1&highlight=feature_wiki">Activate wiki</a>
		{/if}
      </td>
	  <td>{tr}Allow per user routing of incoming email to structures{/tr}.</td>
    </tr>
    <tr>
      <td>{tr}Show inline images{/tr}</td>
      <td colspan="3">
		{if $prefs.feature_wiki_attachments eq 'y'}
        {tr}Yes{/tr}
        <input type="radio" name="show_inlineImages" {if $info.show_inlineImages eq 'y'}checked="checked"{/if} value="y">
        {tr}No{/tr}
        <input type="radio" name="show_inlineImages" {if $info.show_inlineImages eq 'n' || $info.show_inlineImages eq '' }checked="checked"{/if} value="n">
	    {else}
		<a href="tiki-admin.php?page=wiki&cookietab=2&highlight=feature_wiki_attachments">Activate attachments</a>
		{/if}
      </td>
	  <td>{tr}For HTML email, attempt to create a WYSIWYG wiki-page{/tr}.</td>
    </tr>
    <tr>
		<td>{tr}Keep HTML format{/tr}</td>
		<td colspan="3">
		{tr}Yes{/tr}
		<input type="radio" name="save_html" {if $info.save_html eq 'y'}checked="checked"{/if} value="y">
		{tr}No{/tr}
		<input type="radio" name="save_html" {if $info.save_html neq 'y'}checked="checked"{/if} value="n">
		</td>
	  <td>{tr}Always save Email in HTML format as a wiki page in HTML format, regardless of editor availability or selection{/tr}.</td>
    </tr>
    <tr>
      <td>{tr}Discard to the end from{/tr}</td>
      <td colspan="4">
      <input type="text" name="discard_after" value="{$info.discard_after|escape}">
      </td>
   </tr>
    <tr>
      <td>{tr}Auto-assign categoryId{/tr}</td>
      <td colspan="3">
	   {if $prefs.feature_categories eq 'y'}
        <input type="text" size="10" name="categoryId" value="{$info.categoryId}" />
        {else}
		<a href="tiki-admin.php?page=features&highlight=feature_categories">Activate categories</a>
		{/if}
      </td>
      <td>{tr}Only affects wiki-put, when creating a new wiki page{/tr}</td>
    </tr>
    <tr>
      <td>{tr}Auto-assign namespace{/tr}</td>
      <td colspan="3">
	   {if $prefs.namespace_enabled eq 'y'}
        <input type="text" size="20" name="namespace" value="{$info.namespace}" />
        {else}
		<a href="tiki-admin.php?page=wiki&cookietab=2&highlight=namespace_enabled">Activate namespaces</a>
		{/if}
      </td>
      <td>{tr}Only affects wiki-put{/tr}</td>
    </tr>
    <tr>
      <td>{tr}Email response when no access{/tr}</td>
      <td colspan="4">
		&nbsp;&nbsp;
        {tr}Yes{/tr}
        <input type="radio" name="respond_email" {if $info.respond_email eq 'y'}checked="checked"{/if} value="y">
		&nbsp;&nbsp;
        {tr}No{/tr}
        <input type="radio" name="respond_email" {if $info.respond_email eq 'n'}checked="checked"{/if} value="n">
      </td>
    </tr>
    <tr>
      <td>{tr}Leave email on server on error{/tr}</td>
      <td colspan="3">
		&nbsp;&nbsp;
        {tr}Yes{/tr}
        <input type="radio" name="leave_email" {if $info.leave_email eq 'y'}checked="checked"{/if} value="y">
		&nbsp;&nbsp;
        {tr}No{/tr}
        <input type="radio" name="leave_email" {if $info.leave_email eq 'n'}checked="checked"{/if} value="n">
      </td>
	  <td>{tr}Leave the email on the mail server, when an error occurs and the content has not been integrated into Tiki.{/tr}.</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="4"><input type="submit" name="new_acc" value="{if $accountId eq 0}{tr}Add{/tr}{else}{tr}Save{/tr}{/if}"></td>
    </tr>
  </table>
</form>
