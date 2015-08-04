{if $field.options_array[0] eq 'password'}
	{if ($prefs.auth_method neq 'cas' || ($prefs.cas_skip_admin eq 'y' && $user eq 'admin')) and $prefs.change_password neq 'n'}
		<input type="password" name="{$field.ins_id}" class="form-control">
		<br><i>Leave empty if password is to remain unchanged</i>
	{/if}
{elseif $field.options_array[0] eq 'language'}
	<select name="{$field.ins_id}" class="form-control">
		{section name=ix loop=$languages}
			<option value="{$languages[ix].value|escape}" {if $field.value eq $languages[ix].value}selected="selected"{/if}>
				{$languages[ix].name}
			</option>
		{/section}
		<option value='' {if !$field.value}selected="selected"{/if}>{tr}Site default{/tr}</option>
	</select>
{elseif $field.options_array[0] eq 'country'}
	<select name="{$field.ins_id}" class="form-control">
		<option value="Other" {if $user_prefs.country eq "Other"}selected="selected"{/if}>
			{tr}Other{/tr}
		</option>
		{foreach from=$context.flags item=flag key=fval}{strip}
			{if $fval ne "Other"}
				<option value="{$fval|escape}" {if $field.value eq $fval}selected="selected"{/if}>
					{$flag|stringfix}
				</option>
			{/if}
		{/strip}{/foreach}
	</select>
{else}
	<input type="text" name="{$field.ins_id}" value="{$field.value}" class="form-control">
{/if}
