{if $prefs.user_register_prettytracker eq 'y' and $prefs.user_register_prettytracker_tpl}
	<input type="password" name="passcode" id="passcode" onkeypress="regCapsLock(event)" />
{else}
	{if $prefs.useRegisterPasscode eq 'y'}
		<tr>
			<td><label for="passcode">{tr}Passcode to register:{/tr}</label></td>
			<td>
				<input type="password" name="passcode" id="passcode" onkeypress="regCapsLock(event)" />
				<em>{tr}Not your password.{/tr} {tr}To request a passcode, {if $prefs.feature_contact eq 'y'}<a href="tiki-contact.php">{/if}
				contact the system administrator{if $prefs.feature_contact eq 'y'}</a>{/if}{/tr}.</em>
			</td>
		</tr>
	{/if}
{/if}
