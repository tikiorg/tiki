{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-openid_register.tpl,v 1.3.2.2 2008-03-01 20:34:39 lphuberdeau Exp $ *}
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
					{if $prefs.useRegisterPasscode eq 'y'}
					<div>
						<label for="openid_registration_passcode">{tr}Passcode to register (not your user password){/tr}</label>
						<input type="password" name="passcode" id="openid_registration_passcode" />
					</div>
					{/if}
					{if $prefs.rnd_num_reg eq 'y'}
					<div>
						{tr}Your registration code:{/tr}
						<img src="tiki-random_num_img.php" alt='{tr}Random Image{/tr}'/>
						<br />
						<label="openid_registration_code">{tr}Registration code{/tr}</label>
						<input type="text" maxlength="8" size="8" name="regcode" id="openid_registration_code" />
					</div>
					{/if}

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
