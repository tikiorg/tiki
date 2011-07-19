{if $prefs.user_register_prettytracker eq 'y' and $prefs.user_register_prettytracker_tpl}
	<input id='pass2' type="password" name="passAgain" onkeypress="regCapsLock(event)" /><strong class='mandatory_star'>*</strong>
{else}
	{if $openid_associate neq 'y'}
		<tr>
			<td style="vertical-align:top">
				<label for="pass2">{tr}Repeat password:{/tr}</label>
				{if $trackerEditFormId}&nbsp;<strong class='mandatory_star'>*</strong>&nbsp;{/if}
			</td>
			<td>
				<input id='pass2' type="password" name="passAgain" onkeypress="regCapsLock(event)" onkeyup="{if 0 and $prefs.feature_ajax neq 'y' && !$userTrackerData}checkPasswordsMatch('#pass2', '#pass1', '#mypassword2_text');{elseif 0 && !$userTrackerData}check_pass();{/if}" />
				<div style="float:right;margin-left:5px;">
					<div id="mypassword2_text"></div>
				</div>
				{if 0 and $prefs.feature_ajax eq 'y'}<span id="checkpass"></span>{/if}{* AJAX_TODO *}
				{if $prefs.generate_password eq 'y'}
					<p>
						<input id='genepass' name="genepass" type="text" tabindex="0" style="display: none" />
						<span id="genPass">
							{if 0 and $prefs.feature_ajax eq 'y'}
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
{/if}
