{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-openid_register.tpl,v 1.3.2.1 2008-02-16 08:52:27 luciash Exp $ *}
<h1>{tr}Your OpenID identity is valid{/tr}</h1>
<p>{tr}However, no account is associated to the OpenID identifier.{/tr}</p>
<table width="100%">
	<col width="50%"/>
	<col width="50%"/>
	<tr>
		<td>
			<p>
				{tr}Create a new Tikiwiki account from OpenID{/tr}
			</p>
			<form method="post" action="tiki-register.php">
				<fieldset>
					<legend>{tr}Register{/tr}</legend>
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
			<p>
				{tr}Associate OpenID with an existing Tikiwiki account{/tr}
			</p>
			{include file="modules/mod-login_box.tpl"}
		</td>
	</tr>
</table>
