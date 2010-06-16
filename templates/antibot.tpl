{if empty($user)}
	{$headerlib->add_jsfile('lib/captcha/captchalib.js')}
	<input type="hidden" name="captcha[id]" id="captchaId" value=""> {* value filled with ajax call to antibot.php *}
	<tr{if !empty($tr_style)} class="{$tr_style}"{/if}>
		<td{if !empty($td_style)} class="{$td_style}"{/if}>
			{tr}Anti-Bot verification code{/tr}:<br />
			<a id="captchaRegenerate">{tr}(regenerate anti-bot code){/tr}</a>
		</td>
		<td id="captcha" {if !empty($td_style)} class="{$td_style}"{/if}><img id="captchaImg" src="img/spinner.gif" alt="{tr}Anti-Bot verification code image{/tr}" /></td> {* src replaced with ajax call to antibot.php *}
	</tr>
	<tr{if !empty($tr_style)} class="{$tr_style}"{/if}>
		<td{if !empty($td_style)} class="{$td_style}"{/if}><label for="antibotcode">{tr}Enter the code you see above{/tr}{if $showmandatory eq 'y'}*{/if}:</label></td>
		<td{if !empty($td_style)} class="{$td_style}"{/if}><input type="text" maxlength="8" size="8" name="captcha[input]" id="antibotcode" /></td>
	</tr>
{/if}
