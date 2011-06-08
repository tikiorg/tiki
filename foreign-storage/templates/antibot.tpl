{if empty($user)}
	{if $antibot_table ne 'y'}
		<tr{if !empty($tr_style)} class="{$tr_style}"{/if}>
		<td{if !empty($td_style)} class="{$td_style}"{/if}>
	{else}
		<div class="antibot1">
	{/if}
	{if $antibot_table ne 'y'}
		</td>
		<td id="captcha" {if !empty($td_style)} class="{$td_style}"{/if}>
	{else}
		</div>
		<div class="antibot2">
	{/if}
			{if $captchalib->type eq 'recaptcha'}
				{$captchalib->render()}
			{else}
				{$captchalib->generate()}
				<input type="hidden" name="captcha[id]" id="captchaId" value="{$captchalib->getId()}" />
				{if $captchalib->type eq 'default'}
					<img id="captchaImg" src="{$captchalib->getPath()}" alt="{tr}Anti-Bot verification code image{/tr}" height="50" />
				{else}
					{* dumb captcha *}
					{$captchalib->render()}
				{/if}
			{/if}
	{if $antibot_table ne 'y'}
		</td>
	</tr>
	{else}
		</div>
	{/if}
	{if $captchalib->type ne 'recaptcha'}
		{if $antibot_table ne 'y'}
		<tr{if !empty($tr_style)} class="{$tr_style}"{/if}>
			<td{if !empty($td_style)} class="{$td_style}"{/if}>
		{else}
			<div class="antibot3">
		{/if}
			<label for="antibotcode">{tr}Enter the code you see above{/tr}{if $showmandatory eq 'y'}<span class="attention"> *</span>{/if}</label>
		{if $antibot_table ne 'y'}
			</td>
			<td{if !empty($td_style)} class="{$td_style}"{/if}>
		{else}
			</div>
			<div class="antibot4">
		{/if}
				<input type="text" maxlength="8" size="22" name="captcha[input]" id="antibotcode" />
			{if $captchalib->type eq 'default'}
				{button _id='captchaRegenerate' href='#antibot' _text="{tr}Try another code{/tr}"}
			{/if}
		{if $antibot_table ne 'y'}
			</td>
		</tr>
		{else}
			</div>
		{/if}
	{/if}
{/if}
