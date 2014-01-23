{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="domain_password" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{literal}
	<script type="text/javascript">
		function submitform(doEdit)
		{
			document.frmDomainPassword.edit_form.value = doEdit ? "y" : "n";
			document.frmDomainPassword.submit();
		}
	</script>
{/literal}

{if !empty($errors)}
	<span id="error">
		{foreach from=$errors item=error}
			{$error|escape}<br>
		{/foreach}
	</span>
{/if}
{if !empty($result)}
	{$result}
{/if}

<form name="frmDomainPassword" method="post" action="">
	<input type="hidden" name="edit_form" value="">
	<table>
		<tr>
			<td>Domain</td>
			<td>{$domain}</td>
		</tr>
		<tr>
			<td>Username</td>
			<td>
				{if isset($can_update) and $can_update eq 'y' and $currentuser neq 'y' and $edit_option neq 'y'}
					<input type="text" id="domUsername" name="domUsername">
				{else}
					{$username}
				{/if}
			</td>
		</tr>
		{if isset($can_update) and $can_update eq 'y' and $edit_option neq 'y'}
			<tr>
				<td>Password</td>
				<td><input type="password" id="domPassword" name="domPassword"></td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" name="saveButton" value="Save">
					<input type="reset" value="Cancel" onclick="javascript:submitform(false)">
				</td>
			</tr>
		{elseif $edit_option eq 'y'}
			<td colspan="2">
				<a href="javascript:submitform(true)">Edit</a>
			</td>
		{/if}
	</table>
</form>
{/tikimodule}
