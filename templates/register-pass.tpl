	{if $prefs.useRegisterPasscode eq 'y'}
				<tr>
					<td class="formcolor"><label for="passcode">{tr}Passcode to register:{/tr}</label></td>
					<td class="formcolor">
						<input type="password" name="passcode" id="passcode" onkeypress="regCapsLock(event)" />
						<em>{tr}Not your password.{/tr} {tr}To request a passcode, {if $prefs.feature_contact eq 'y'}<a href="tiki-contact.php">{/if}
						contact the system administrator{if $prefs.feature_contact eq 'y'}</a>{/if}{/tr}.</em>
					</td>
				</tr>
	{/if}
 
	{if $openid_associate neq 'y'}
				<tr>
					<td class="formcolor"><label for="pass1">{tr}Password:{/tr}</label>{if $trackerEditFormId}&nbsp;<strong class='mandatory_star'>*</strong>&nbsp;{/if}</td>
					<td class="formcolor">
						<input id='pass1' type="password" name="pass" onkeypress="regCapsLock(event)" onkeyup="{if $prefs.feature_ajax neq 'y'}runPassword(this.value, 'mypassword');checkPasswordsMatch('#pass2', '#pass1', '#mypassword2_text');{else}check_pass();{/if}" />
						<div style="float:right;margin-left:5px;">
							<div id="mypassword_text"></div>
							<div id="mypassword_bar" style="font-size: 5px; height: 2px; width: 0px;"></div> 
						</div>
						{if $prefs.feature_ajax ne 'y'}
							{if $prefs.min_pass_length > 1}<div class="highlight"><em>{tr}Minimum {$prefs.min_pass_length} characters long{/tr}</em></div>{/if}
							{if $prefs.pass_chr_num eq 'y'}<div class="highlight"><em>{tr}Password must contain both letters and numbers{/tr}</em></div>{/if}
						{/if}
					</td>
				</tr>

				<tr>
					<td class="formcolor" style="vertical-align:top"><label for="pass2">{tr}Repeat password:{/tr}</label>{if $trackerEditFormId}&nbsp;<strong class='mandatory_star'>*</strong>&nbsp;{/if}</td>
					<td class="formcolor">
						<input id='pass2' type="password" name="passAgain" onkeypress="regCapsLock(event)" onkeyup="{if $prefs.feature_ajax neq 'y'}checkPasswordsMatch('#pass2', '#pass1', '#mypassword2_text');{else}check_pass();{/if}" />
						<div style="float:right;margin-left:5px;">
							<div id="mypassword2_text"></div>
						</div>
						{if $prefs.feature_ajax eq'y'}<span id="checkpass"></span>{/if}
						{if $prefs.generate_password eq 'y'}
							<p>
							<input id='genepass' name="genepass" type="text" tabindex="0" style="display: none" />
							<span id="genPass">
							{if $prefs.feature_ajax eq 'y'}
								{button href="#" _onclick="check_pass();" _text="{tr}Generate a password{/tr}"}
							{else}
								{button href="#" _onclick="" _text="{tr}Generate a password{/tr}"}
							{/if}
							</span>
							</p>
						{/if}
					</td>
				</tr>
	{/if}
