{* $Id$ *}
{extends "layout_view.tpl"}

{block name="title"}
	{title help="Webmail"}{tr}Mail-in accounts{/tr}{/title}
{/block}

{block name="content"}
	{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
	{if $prefs.javascript_enabled !== 'y'}
		{$js = 'n'}
		{$libeg = '<li>'}
		{$liend = '</li>'}
	{else}
		{$js = 'y'}
		{$libeg = ''}
		{$liend = ''}
	{/if}
	<table class="table table-striped table-hover">
		<tr>
			<th>{tr}Account{/tr}</th>
			<th>{tr}Allow{/tr}</th>
			<th>{tr}Attach{/tr}</th>
			<th>{tr}HTML{/tr}</th>
			<th>{tr}Leave{/tr}</th>
			<th></th>
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
				<td>{if $account.attachments eq 'y'}{icon name="ok"}{/if}</td>
				<td>{if $account.save_html eq 'y'}{icon name="ok"}{/if}</td>
				<td>{if $account.leave_email eq 'y'}{icon name="ok"}{/if}</td>

				<td class="action">
					{capture name=mailin_actions}
						{strip}
							{$libeg}<a href="{bootstrap_modal controller=mailin action=replace_account accountId=$account.accountId}"
									onclick="$('[data-toggle=popover]').popover('hide');">
								{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
							</a>{$liend}
							{$libeg}<a href="{bootstrap_modal controller=mailin action=remove_account accountId=$account.accountId}"
									onclick="$('[data-toggle=popover]').popover('hide');">
								{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
							</a>{$liend}
						{/strip}
					{/capture}
					{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
					<a
						class="tips"
						title="{tr}Actions{/tr}"
						href="#"
						{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.mailin_actions|escape:"javascript"|escape:"html"}{/if}
						style="padding:0; margin:0; border:0"
					>
						{icon name='wrench'}
					</a>
					{if $js === 'n'}
						<ul class="dropdown-menu" role="menu">{$smarty.capture.mailin_actions}</ul></li></ul>
					{/if}
				</td>
			</tr>
		{/foreach}
	</table>
	<a href="{bootstrap_modal controller=mailin action=replace_account}" class="btn btn-default">{icon name="add"} {tr}Add Account{/tr}</a>
	{button _icon_name="cog" _text="{tr}Admin Mail-in Routes{/tr}" _type="link" href="tiki-admin_mailin_routes.php"}
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
