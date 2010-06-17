{if empty($user)}
	{$headerlib->add_jsfile('lib/captcha/captchalib.js')}
	<tr{if !empty($tr_style)} class="{$tr_style}"{/if}>
		<td{if !empty($td_style)} class="{$td_style}"{/if}>
			{tr}Anti-Bot verification code{/tr}:<br />
			{if $captchalib->type eq 'default'}
				<a id="captchaRegenerate">{tr}(regenerate anti-bot code){/tr}</a>
			{/if}
		</td>
		<td id="captcha" {if !empty($td_style)} class="{$td_style}"{/if}>
			{if $captchalib->type eq 'default'}
				{$captchalib->generate()}
				<input type="hidden" name="captcha[id]" id="captchaId" value="{$captchalib->getId()}">
				<img id="captchaImg" src="{$captchalib->getPath()}" alt="{tr}Anti-Bot verification code image{/tr}" />
			{else}
				{$captchalib->render()}
			{/if}
		</td>
	</tr>
	<tr{if !empty($tr_style)} class="{$tr_style}"{/if}>
		<td{if !empty($td_style)} class="{$td_style}"{/if}><label for="antibotcode">{tr}Enter the code you see above{/tr}{if $showmandatory eq 'y'}*{/if}:</label></td>
		<td{if !empty($td_style)} class="{$td_style}"{/if}><input type="text" maxlength="8" size="8" name="captcha[input]" id="antibotcode" /></td>
	</tr>
{/if}
