{* $Id$ *}
{include file="register-login.tpl"}
{include file="register-email.tpl"}
{include file="register-passcode.tpl"}
{include file="register-pass.tpl"}
{include file="register-pass2.tpl"}
{* Custom fields *}
{if isset($customfields)}
	{section name=ir loop=$customfields}
		{if $customfields[ir].show}
			<div class="form-group">
				<label class="col-sm-4 control-label" for="{$customfields[ir].prefName}">{tr}{$customfields[ir].label}:{/tr}</label>
				<div class="col-sm-8">
					<input type="{$customfields[ir].type}" name="{$customfields[ir].prefName}" value="{$customfields[ir].value}" size="{$customfields[ir].size}" id="{$customfields[ir].prefName}">
				</div>
			</div>
		{/if}
	{/section}
{/if}
{include file="register-groupchoice.tpl"}
