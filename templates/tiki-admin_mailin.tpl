{* $Id$ *}
{extends "layout_view.tpl"}

{block name="title"}
{title help="Webmail"}{tr}Mail-in accounts{/tr}{/title}
{/block}

{block name="content"}
<table class="table">
  <tr>
    <th>{tr}Account{/tr}</th>
    <th>{tr}Allow{/tr}</th>
    <th>{tr}Attach{/tr}</th>
    <th>{tr}HTML{/tr}</th>
    <th>{tr}Leave{/tr}</th>

    <th>{tr}Action{/tr}</th>
  </tr>

  {foreach $accounts as $account}
    <tr>
      <td>
        <strong>{$account.account|escape}</strong>
        <div>{$mailin_types[$account.type].name|escape}</div>
        {if $account.active neq 'y'}
            <span class="label label-warning">{tr}Disabled{/tr}</span>
        {/if}
        {if $account.categoryId}
            <div class="text-muted">
                {tr}Auto-category:{/tr}
                {object_link type=category id=$account.categoryId}
            </div>
        {/if}
        {if $account.namespace}
            <div class="text-muted">
                {tr}Auto-namespace:{/tr}
                {object_link type="wiki page" id=$account.namespace}
            </div>
        {/if}
      </td>
      <td>
        {if $account.anonymous eq 'y'}<span class="label label-info">{tr}Anonymous{/tr}</span>{/if}
        {if $account.admin eq 'y'}<span class="label label-warning">{tr}Administrator{/tr}</span>{/if}
      </td>
      <td>{if $account.attachments eq 'y'}{glyph name="ok"}{/if}</td>
      <td>{if $account.save_html eq 'y'}{glyph name="ok"}{/if}</td>
      <td>{if $account.leave_email eq 'y'}{glyph name="ok"}{/if}</td>

      <td class="action">
        <a href="tiki-admin_mailin.php?accountId={$account.accountId}#add">{icon _id='page_edit'}</a>
        <a href="tiki-admin_mailin.php?remove={$account.accountId}" >{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
      </td>
    </tr>
  {/foreach}
</table>
{button _icon="img/icons/large/messages.gif" _text="{tr}Admin Mail-in Routes{/tr}" href="tiki-admin_mailin_routes.php" _menu_text="y"}
{if $tikifeedback}
	{section name=n loop=$tikifeedback}<div class="alert {if $tikifeedback[n].num > 0} alert-warning{/if}">{$tikifeedback[n].mes}</div>{/section}
{/if}
<h2>{tr}Check Mail-in accounts{/tr}</h2>
<form class="form-horizontal" action="tiki-admin_mailin.php" method="post">
  <div class="form-group">
    <div class="col-md-offset-3 col-md-9">
      <div class="checkbox">
        <label>
          <input type="checkbox" name="mailin_autocheck" value="y" {if $prefs.mailin_autocheck eq 'y'}checked{/if}>
          {tr}Check automatically{/tr}
        </label>
      </div>
    </div>
  </div>
  <div class="form-group">
    <label for="mailin_autocheckFreq" class="control-label col-md-3">{tr}Frequency{/tr}</label>
    <div class="col-md-3">
      <input type="text" name="mailin_autocheckFreq" value="{$prefs.mailin_autocheckFreq|escape}" class="form-control">
      <div class="help-block">
        {tr}minutes{/tr}
      </div>
    </div>
  </div>
  <div class="submit col-md-offset-3 col-md-9">
    <input type="submit" name="set_auto" value="{tr}Set{/tr}" class="btn btn-primary">
    <a class="btn btn-link" href="tiki-mailin.php">{tr}Check Manually Now{/tr}</a>
  </div>
</form>

<a name="add" ></a><h2>{if $accountId eq 0}{tr}Add new Mail-in account{/tr}{else}{tr}Edit Mail-in account:{/tr} <i>{$info.account}</i>{/if}</h2>
<form class="form-horizontal" action="tiki-admin_mailin.php" method="post">
  <input type="hidden" name="accountId" value="{$accountId|escape}">
  <div class="form-group">
    <div class="col-md-offset-3 col-md-9">
      <div class="checkbox">
        <label>
          <input type="checkbox" name="active" value="y" {if $info.active eq 'y'}checked{/if}>
          {tr}Active{/tr}
        </label>
      </div>
    </div>
  </div>
  <div class="form-group">
    <label for="account" class="control-label col-md-3">{tr}Account name{/tr}</label>
    <div class="col-md-9">
      <input type="text" name="account" value="{$info.account|escape}" class="form-control">
    </div>
  </div>
  <div class="form-group">
    <label for="type" class="control-label col-md-3">{tr}Type{/tr}</label>
    <div class="col-md-9">
      <select name="type" class="form-control">
        {foreach $mailin_types as $intype => $detail}
          <option value="{$intype|escape}" {if $intype eq $info.type}selected{/if}>{$detail.name|escape}</option>
        {/foreach}
      </select>
      <div class="help-block">
        {tr}Wiki (multiple action) allows to prefix the subject with GET:, PREPEND: or APPEND:{/tr}
      </div>
    </div>
  </div>
  <div class="form-group">
    <label for="pop" class="control-label col-md-3">{tr}POP server{/tr} / {tr}Port{/tr}</label>
    <div class="col-md-4">
      <input type="text" name="pop" value="{$info.pop|escape}" class="form-control" placeholder="{tr}Hostname{/tr}">
    </div>
    <div class="col-md-2">
      <input type="text" name="port" value="{$info.port|escape}" class="form-control" placeholder="{tr}Port{/tr}">
    </div>
  </div>
  <div class="form-group">
    <label for="username" class="control-label col-md-3">{tr}Username{/tr}</label>
    <div class="col-md-4">
      <input type="text" name="username" value="{$info.username|escape}" class="form-control">
    </div>
  </div>
  <div class="form-group">
    <label for="pass" class="control-label col-md-3">{tr}Password{/tr}</label>
    <div class="col-md-4">
      <input type="password" name="pass" value="{$info.password|escape}" class="form-control">
    </div>
  </div>
  <div class="form-group">
    <label for="smtp" class="control-label col-md-3">{tr}SMTP server{/tr} / {tr}Port{/tr}</label>
    <div class="col-md-4">
      <input type="text" name="smtp" value="{$info.smtp|escape}" class="form-control" placeholder="{tr}Hostname{/tr}">
    </div>
    <div class="col-md-2">
      <input type="text" name="smtpPort" value="{$info.smtpPort|escape}" class="form-control" placeholder="{tr}Port{/tr}">
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-3 col-md-9">
      <div class="checkbox">
        <label>
          <input type="checkbox" name="useAuth" value="y" {if $info.useAuth eq 'y'}checked{/if}>
          {tr}SMTP requires authentication{/tr}
        </label>
      </div>
    </div>
  </div>
  {if $prefs.feature_articles eq 'y'}
  <div class="form-group">
    <label for="article_topicId" class="control-label col-md-3">{tr}Article Topic{/tr}</label>
    <div class="col-md-9">
      <select name="article_topicId" class="form-control">
        {foreach $topics as $topicId=>$topic}
          <option value="{$topicId|escape}" {if $info.article_topicId eq $topicId}selected="selected"{/if}>{$topic.name|escape}</option>
          {/foreach}
          <option value="" {if $info.article_topicId eq 0}selected="selected"{/if}>{tr}None{/tr}</option>
      </select>
      {if $tiki_p_admin_cms eq 'y'}
        <div class="help-block">
          <a href="tiki-admin_topics.php" class="link">{tr}Admin Topics{/tr}</a>
        </div>
      {/if}
    </div>
  </div>
  <div class="form-group">
    <label for="article_type" class="control-label col-md-3">{tr}Article Topic{/tr}</label>
    <div class="col-md-9">
      <select name="article_type" class="form-control">
        <option value="">{tr}None{/tr}</option>
        {foreach $types as $type}
          <option value="{$type.type|escape}" {if $info.article_type eq $type.type}selected="selected"{/if}>{$type.type|escape}</option>
          {/foreach}
      </select>
      {if $tiki_p_admin_cms eq 'y'}
        <div class="help-block">
          <a href="tiki-admin_types.php" class="link">{tr}Admin Types{/tr}</a>
        </div>
      {/if}
    </div>
  </div>
  {/if}
  <div class="form-group">
    <div class="col-md-offset-3 col-md-9">
      <div class="checkbox">
        <label>
          <input type="checkbox" name="anonymous" value="y" {if $info.anonymous eq 'y'}checked{/if}>
          {tr}Allow anonymous access{/tr}
        </label>
        <div class="help-block">
          {tr}Warning: Enabling anonymous access will disable all permission checking for mailed-in content.{/tr}
        </div>
      </div>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-3 col-md-9">
      <div class="checkbox">
        <label>
          <input type="checkbox" name="admin" value="y" {if $info.admin eq 'y'}checked{/if}>
          {tr}Allow admin access{/tr}
        </label>
        <div class="help-block">
          {tr}Administrators have full access to the system. Disabling admin mail-in is the safest option.{/tr}
        </div>
      </div>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-3 col-md-9">
      {if $prefs.feature_wiki_attachments eq 'y'}
      <div class="checkbox">
        <label>
          <input type="checkbox" name="attachments" value="y" {if $info.attachments eq 'y'}checked{/if}>
          {tr}Allow attachments{/tr}
        </label>
      </div>
      {else}
        <a href="tiki-admin.php?page=wiki&cookietab=2&highlight=feature_wiki_attachments">Activate attachments</a>
      {/if}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-3 col-md-9">
      {if $prefs.feature_wiki eq 'y'}
      <div class="checkbox">
        <label>
          <input type="checkbox" name="routing" value="y" {if $info.routing eq 'y'}checked{/if}>
          {tr}Allow routing{/tr}
        </label>
        <div class="help-block">
          {tr}Allow per user routing of incoming email to structures.{/tr}
        </div>
      </div>
      {else}
        <a href="tiki-admin.php?page=wiki&cookietab=1&highlight=feature_wiki">Activate wiki</a>
      {/if}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-3 col-md-9">
      {if $prefs.feature_wiki_attachments eq 'y'}
      <div class="checkbox">
        <label>
          <input type="checkbox" name="show_inlineImages" value="y" {if $info.show_inlineImages eq 'y'}checked{/if}>
          {tr}Show inline images{/tr}
        </label>
	      <div class="help-block">
          {tr}For HTML email, attempt to create a WYSIWYG wiki-page.{/tr}
        </div>
      </div>
      {else}
        <a href="tiki-admin.php?page=wiki&cookietab=2&highlight=feature_wiki_attachments">Activate attachments</a>
      {/if}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-3 col-md-9">
      <div class="checkbox">
        <label>
          <input type="checkbox" name="save_html" value="y" {if $info.save_html eq 'y'}checked{/if}>
          {tr}Keep HTML format{/tr}
        </label>
        <div class="help-block">
	        {tr}Always save Email in HTML format as a wiki page in HTML format, regardless of editor availability or selection.{/tr}
        </div>
      </div>
    </div>
  </div>
  <div class="form-group">
    <label for="discard_after" class="control-label col-md-3">{tr}Discard to the end from{/tr}</label>
    <div class="col-md-9">
      <input type="text" name="discard_after" value="{$info.discard_after|escape}" class="form-control">
    </div>
  </div>
  <div class="form-group">
    <label for="cartegoryId" class="control-label col-md-3">{tr}Auto-assign category{/tr}</label>
    <div class="col-md-6">
	    {if $prefs.feature_categories eq 'y'}
        <input type="text" name="categoryId" value="{$info.categoryId|escape}" class="form-control" placeholder="{tr}Category ID{/tr}">
        <div class="help-block">{tr}Only affects wiki-put, when creating a new wiki page{/tr}</div>
      {else}
        <a href="tiki-admin.php?page=features&highlight=feature_categories">Activate categories</a>
      {/if}
    </div>
  </div>
  <div class="form-group">
    <label for="namespace" class="control-label col-md-3">{tr}Auto-assign namespace{/tr}</label>
    <div class="col-md-6">
	    {if $prefs.namespace_enabled eq 'y'}
        <input type="text" name="namespace" value="{$info.namespace|escape}" class="form-control">
        <div class="help-block">{tr}Only affects wiki-put, when creating a new wiki page{/tr}</div>
      {else}
        <a href="tiki-admin.php?page=wiki&cookietab=2&highlight=namespace_enabled">Activate namespaces</a>
      {/if}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-3 col-md-9">
      <div class="checkbox">
        <label>
          <input type="checkbox" name="respond_email" value="y" {if $info.respond_email eq 'y'}checked{/if}>
          {tr}Email response when no access{/tr}
        </label>
      </div>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-3 col-md-9">
      <div class="checkbox">
        <label>
          <input type="checkbox" name="leave_email" value="y" {if $info.leave_email eq 'y'}checked{/if}>
          {tr}Leave email on server on error{/tr}
        </label>
        <div class="help-block">
          {tr}Leave the email on the mail server, when an error occurs and the content has not been integrated into Tiki.{/tr}
        </div>
      </div>
    </div>
  </div>
  <div class="submit col-md-offset-3 col-md-9">
    <input type="submit" name="new_acc" value="{if $accountId eq 0}{tr}Add Account{/tr}{else}{tr}Save{/tr}{/if}" class="btn btn-primary">
  </div>
</form>
{/block}
