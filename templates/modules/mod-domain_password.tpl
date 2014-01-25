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

{if !empty($errors[{$dompwdCount}])}
	<span id="error">
		{foreach from=$errors[{$dompwdCount}] item=error}
			{$error|escape}<br>
		{/foreach}
	</span>
{/if}

<form name="frmDomainPassword{$dompwdCount}" method="post" action="">
	<input type="hidden" name="edit_form{$dompwdCount}" value="">
	<input type="hidden" name="dompwdCount" value="{$dompwdCount}">
	<table>
		<tr>
			<td>Domain</td>
			<td>{$domain}</td>
		</tr>
		{if !empty($user)}
			<tr>
				<td>Username</td>
				<td>
					{if isset($can_update[{$dompwdCount}]) and $can_update[{$dompwdCount}] eq 'y' and $currentuser[{$dompwdCount}] neq 'y' and $edit_option[{$dompwdCount}] neq 'y'}
						<input type="text" id="domUsername" name="domUsername" value="{$username[{$dompwdCount}]}">
					{else}
						{$username[{$dompwdCount}]}
					{/if}
				</td>
			</tr>
			{if isset($can_update[{$dompwdCount}]) and $can_update[{$dompwdCount}] eq 'y' and $edit_option[{$dompwdCount}] neq 'y'}
				<tr>
					<td>Password</td>
					<td><input type="password" id="domPassword" name="domPassword"></td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" name="saveButton{$dompwdCount}" value="Save">
						<input type="reset" value="Cancel" onclick="javascript:submitform{$dompwdCount}(false)">
					</td>
				</tr>
			{elseif $edit_option[{$dompwdCount}] eq 'y'}
				<td colspan="2">
					<a href="javascript:submitform{$dompwdCount}(true)">Edit</a>
				</td>
			{/if}
		{/if}
	</table>
</form>
{if !empty($result[{$dompwdCount}])}
	<span id="error">
		{$result[{$dompwdCount}]}
	</span>
{/if}
{/tikimodule}
