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
        <a class="btn btn-xs btn-default" data-toggle="modal" data-target="#bootstrap-modal" href="{service controller=mailin action=replace_account accountId=$account.accountId modal=1}">{glyph name=edit} {tr}Edit{/tr}</a>
        <a class="btn btn-xs btn-danger" data-toggle="modal" data-target="#bootstrap-modal" href="{service controller=mailin action=remove_account accountId=$account.accountId modal=1}" >{glyph name=remove} <span class="sr-only">{tr}Remove{/tr}</span></a>
      </td>
    </tr>
  {/foreach}
</table>
{button _icon="img/icons/large/messages.gif" _text="{tr}Admin Mail-in Routes{/tr}" href="tiki-admin_mailin_routes.php" _menu_text="y"}
<a data-toggle="modal" data-target="#bootstrap-modal" href="{service controller=mailin action=replace_account modal=1}" class="btn btn-default">{glyph name=plus} {tr}Add Account{/tr}</a>
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
{/block}
