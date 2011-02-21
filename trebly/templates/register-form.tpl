{include file="register-login.tpl"}
{include file="register-passcode.tpl"}
{include file="register-pass.tpl"}
{include file="register-pass2.tpl"}
{include file="register-email.tpl"}
{* Custom fields *}
{section name=ir loop=$customfields}
	{if $customfields[ir].show}
		<tr>
			<td><label for="{$customfields[ir].prefName}">{tr}{$customfields[ir].label}:{/tr}</label></td>
			<td><input type="{$customfields[ir].type}" name="{$customfields[ir].prefName}" value="{$customfields[ir].value}" size="{$customfields[ir].size}" id="{$customfields[ir].prefName}" /></td>
		</tr>
	{/if}
{/section}
{include file="register-groupchoice.tpl"}
