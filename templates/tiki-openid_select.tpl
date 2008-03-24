<h1>{tr}Your OpenID identity is valid{/tr}</h1>
<p>
	{tr}Your identity gives you access to multiple user accounts.{/tr}
</p>
<form method="get" action="tiki-login_openid.php">
	<fieldset>
		<legend>{tr}Select account{/tr}</legend>

		{foreach item=username from=$openid_userlist}
		<div>
			<input type="radio" name="select" id="oid_{$username}" value="{$username}"/>
			<label for="oid_{$username}">{$username}</label>
		</div>
		{/foreach}

		<input type="hidden" name="action" value="select"/>
		<input type="submit" value="{tr}Select{/tr}"/>
	</fieldset>
</form>
