{if empty($user) || $user eq 'anonymous' || !empty($showantibot)}
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
			{if $captchalib->type eq 'recaptcha' || $captchalib->type eq 'recaptcha20'}
				{$captchalib->render()}
			{else}
				<input type="hidden" name="captcha[id]" id="captchaId" value="{$captchalib->generate()}">
				{if $captchalib->type eq 'default'}
					<img id="captchaImg" src="{$captchalib->getPath()}" alt="{tr}Anti-Bot verification code image{/tr}" height="50">
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
	{if $captchalib->type ne 'recaptcha' and $captchalib->type ne 'recaptcha20'}
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
				<input type="text" maxlength="8" size="22" name="captcha[input]" id="antibotcode">
			{if $captchalib->type eq 'default'}
				{button _id='captchaRegenerate' href='#antibot' _text="{tr}Try another code{/tr}" _onclick="generateCaptcha()"}
			{/if}
		{if $antibot_table ne 'y'}
			</td>
		</tr>
		{else}
			</div>
		{/if}
	{/if}
	<span style="display:none" id="_antibot_form" />
{/if}

{jq}
	function antibotVerification(element, rule) {
		if (!jqueryTiki.validate) return;

		var form = $("#_antibot_form").parents('form');
		if (!form.data("validator")) {
			form.validate({});
		}
		form.data("validator").settings.ignore = [];
		element.rules( "add", rule);
	}
{/jq}

{if $captchalib->type eq 'recaptcha'}
	{jq}
		var existCondition = setInterval(function() {
			if ($('#recaptcha_response_field').length) {
				clearInterval(existCondition);
				antibotVerification($("#recaptcha_response_field"), {required: true});
			}
		}, 100); // wait for captcha to load

	{/jq}
{elseif $captchalib->type eq 'recaptcha20'}
	{jq}
		var existCondition = setInterval(function() {
			if ($('#g-recaptcha-response').length) {
				clearInterval(existCondition);
				antibotVerification($("#g-recaptcha-response"), {required: true});
			}
		}, 100); // wait for captcha to load
	{/jq}
{else}
	{jq}
		antibotVerification($("#antibotcode"),  {
			required: true,
			remote: {
				url: "validate-ajax.php",
				type: "post",
				data: {
					validator: "captcha",
					parameter: function() {
						return $("#captchaId").val();
					},
					input: function() {
						return $("#antibotcode").val();
					}
				}
			}
		});
	{/jq}
{/if}
