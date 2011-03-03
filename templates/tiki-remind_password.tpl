{title admpage='login'}{tr}I forgot my password{/tr}{/title}

{if $showmsg ne 'n'}
	{if $showmsg eq 'e'}
		<span class="warn">{icon _id=exclamation alt="{tr}Error{/tr}" style="vertical-align:middle;align:left;"}
	{else}
		{icon _id=accept alt="{tr}OK{/tr}" style="vertical-align:middle;align:left;"}
	{/if} 
	{if $prefs.login_is_email ne 'y'}
		{$msg|escape:'html'|@default:"{tr}Enter your username or email.{/tr}"}
	{else}
		{$msg|escape:'html'|@default:"{tr}Enter your email.{/tr}"}
	{/if}
	{if $showmsg eq 'e'}</span>{/if}
	<br /><br />
{/if}

{if $showfrm eq 'y'}
	<form action="tiki-remind_password.php" method="post">
		<table class="formcolor">
			{if $prefs.login_is_email ne 'y'}
				<tr>
					<td><label for="name">{tr}Username:{/tr}</label></td>
					<td><input type="text" name="name" id="name" /></td>
				</tr>
				<tr><td colspan="2">{tr}or{/tr}</td></tr>
			{/if}
			<tr>
				<td><label for="email">{tr}Email:{/tr}</label></td>
				<td>{if $prefs.login_is_email ne 'y'}<input type="text" name="email" id="email" />{else}<input type="text" name="name" />{/if}</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" name="remind" value="{if $prefs.feature_clear_passwords eq 'y'}{tr}Send me my Password{/tr}{else}{tr}Request Password Reset{/tr}{/if}" />
				</td>
			</tr>  
		</table>
	</form>
{/if}
