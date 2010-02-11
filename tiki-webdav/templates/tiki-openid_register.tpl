{* $Id$ *}
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

					{if $prefs.useRegisterPasscode eq 'y'}
					<div>
						<label for="openid_registration_passcode">{tr}Passcode to register (not your user password){/tr}</label>
						<input type="password" name="passcode" id="openid_registration_passcode" />
					</div>
					{/if}

					<div>
						<label for="openid_email">{tr}Email{/tr}</label>
						<input id="openid_email" type="text" name="email" value="{$email}"/>
						{if $prefs.validateUsers eq 'y' and $prefs.validateEmail ne 'y'}
						<div class="highlight"><em class='mandatory_note'>{tr}A valid email is mandatory to register{/tr}</em></div>
						{/if}
					</div>

					{* Groups *}
					{if isset($theChoiceGroup)}
								<input type="hidden" name="chosenGroup" value="{$theChoiceGroup|escape}" />
					{elseif isset($listgroups)}
								<div>
									<label for="chosenGroup">{tr}Group{/tr}</label>
						{foreach item=gr from=$listgroups}
							{if $gr.registrationChoice eq 'y'}
								<div class="registergroup">
									<input type="radio" name="chosenGroup" id="gr_{$gr.groupName}" value="{$gr.groupName|escape}" /> 
									<label for="gr_{$gr.groupName}">
										{if $gr.groupDesc}
											{tr}{$gr.groupDesc|escape}{/tr}
										{else}
											{$gr.groupName|escape}
										{/if}
									</label>
								</div>
							{/if}
						{/foreach}
								</div>
					{/if}

					{if $prefs.rnd_num_reg eq 'y'}
					<div>
						{tr}Anti-Bot verification code:{/tr}
						<img src="tiki-random_num_img.php" alt='{tr}Random Image{/tr}'/>
						<br />
						<label="antibotcode">{tr}Enter the code you see above:{/tr}</label>
						<input type="text" maxlength="8" size="8" name="antibotcode" id="antibotcode" />
					</div>
					{/if}
					<input type="submit" name="register" value="{tr}Register{/tr}"/>
				</fieldset>
			</form>
		</td>
		<td>
			<p>
				{tr}Associate OpenID with an existing Tikiwiki account{/tr}
			</p>
			{assign value=1 var='display_login'} {* Hack to display the login module only once if it is also actually used as a module *}
			{include file="modules/mod-login_box.tpl"}
		</td>
	</tr>
</table>
