{* $Id$ *}
{tikimodule error=$module_params.error title=$tpl_module_title name="domain_password" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{literal}
	<script type="text/javascript">
		function submitform{/literal}{$dompwdCount}{literal}(doEdit)
		{
			document.frmDomainPassword{/literal}{$dompwdCount}{literal}.edit_form{/literal}{$dompwdCount}{literal}.value = doEdit ? "y" : "n";
			document.frmDomainPassword{/literal}{$dompwdCount}{literal}.submit();
		}
	</script>
{/literal}

<form name="frmDomainPassword{$dompwdCount}" method="post">
	<input type="hidden" name="edit_form{$dompwdCount}" value="">
	<input type="hidden" name="dompwdCount" value="{$dompwdCount}">
	<table>
		<tr>
			{if isset($domainDisplayPrompt[{$dompwdCount}]) and $domainDisplayPrompt[{$dompwdCount}] eq 'n'}
				<td colspan="2" class="pwddom_domain_name">{$domain}</td>
			{else}
				<td>{tr}Domain{/tr}</td>
				<td class="pwddom_domain_name">{$domain}</td>
			{/if}
		</tr>
		{if !empty($errors[{$dompwdCount}])}
			<tr>
				<td colspan="2">
					<span id="error" class="alert-warning">
						{foreach from=$errors[{$dompwdCount}] item=error}
							{$error|escape}<br>
						{/foreach}
					</span>
				</td>
			</tr>
		{/if}
		{if !empty($user)}
			<tr>
				<td>{tr}User{/tr}</td>
				<td class="pwddom_username_name">
					{if isset($can_update[{$dompwdCount}]) and $can_update[{$dompwdCount}] eq 'y' and $currentuser[{$dompwdCount}] neq 'y' and $edit_option[{$dompwdCount}] neq 'y'}
						<input type="text" id="domUsername" name="domUsername" value="{$username[{$dompwdCount}]}">
					{else}
						{$username[{$dompwdCount}]}
					{/if}
				</td>
			</tr>
			{if isset($can_update[{$dompwdCount}]) and $can_update[{$dompwdCount}] eq 'y' and $edit_option[{$dompwdCount}] neq 'y'}
				<tr>
					<td>{tr}Pass{/tr}</td>
					<td><input type="password" id="domPassword" name="domPassword"></td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" name="saveButton{$dompwdCount}" value="{tr}Save{/tr}">
						<input type="reset" value="{tr}Cancel{/tr}" onclick="javascript:submitform{$dompwdCount}(false)">
					</td>
				</tr>
			{elseif $edit_option[{$dompwdCount}] eq 'y'}
				<tr>
					<td colspan="2">
						<a href="javascript:submitform{$dompwdCount}(true)">{tr}Edit{/tr}</a>
					</td>
				</tr>
			{/if}
		{/if}
	</table>
</form>
{if !empty($result[{$dompwdCount}])}
	<span id="error" class="alert-warning">
		{$result[{$dompwdCount}]}
	</span>
{/if}
{/tikimodule}
