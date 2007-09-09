<h1>{tr}Your OpenID identity is valid{/tr}</h1>
<p>{tr}However, no account is associated to the OpenID identifier.{/tr}</p>
<table width="100%">
	<col width="50%"/>
	<col width="50%"/>
	<tr>
		<td>
			<form method="post" action="tiki-register.php">
				<fieldset>
					<legend>{tr}Register a new account{/tr}</legend>
					<div>
						<label for="openid_nickname">{tr}Username{/tr}</label>
						<input id="openid_nickname" type="text" name="name" value="{$username}"/>
					</div>
					<div>
						<label for="openid_email">{tr}Email{/tr}</label>
						<input id="openid_email" type="text" name="email" value="{$email}"/>
					</div>
					<input type="submit" name="register" value="{tr}Register{/tr}"/>
				</fieldset>
			</form>
		</td>
		<td>
			{include file="modules/mod-login_box.tpl"}
		</td>
	</tr>
</table>
