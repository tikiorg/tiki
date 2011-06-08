{if $field.options_array[0] eq 'password'}
	{if ($prefs.auth_method neq 'cas' || ($prefs.cas_skip_admin eq 'y' && $user eq 'admin')) and $prefs.change_password neq 'n'}
		<input type="password" name="{$field.ins_id}" />
		<br /><i>Leave empty if password is to remain unchanged</i>
	{/if}
{elseif $field.options_array[0] eq 'language'}
	<select name="{$field.ins_id}">
		{section name=ix loop=$languages}
			{if count($prefs.available_languages) == 0 || in_array($languages[ix].value, $prefs.available_languages)}
				<option value="{$languages[ix].value|escape}" {if $field.value eq $languages[ix].value}selected="selected"{/if}>
					{$languages[ix].name}
				</option>
			{/if}
		{/section}
		<option value='' {if !$field.value}selected="selected"{/if}>{tr}Site default{/tr}</option>
	</select>
{else}
	<input type="text" name="{$field.ins_id}" value="{$field.value}" />
{/if}