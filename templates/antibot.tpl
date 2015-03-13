{* $Id$ *}
{if empty($user) || $user eq 'anonymous' || !empty($showantibot)}
	{*if $antibot_table ne 'y'}
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
	{/if*}
		<div class="form-group">
			<div class="col-md-4 col-sm-6 text-center col-md-offset-4 col-sm-offset-3">
			{if $captchalib->type eq 'recaptcha'}
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
				</div>
				<div class="col-md-4 col-sm-3 text-center">
			{if $captchalib->type eq 'default'}
				{button _id='captchaRegenerate' _class='' href='#antibot' _text='{tr}Try another code{/tr} <span class="glyphicon glyphicon-refresh"></span>' _onclick="generateCaptcha()"}
			{/if}
				</div>
		</div>
	{*if $antibot_table ne 'y'}
		</td>
	</tr>
	{else}
		</div>
	{/if*}
	{if $captchalib->type ne 'recaptcha'}
		{*if $antibot_table ne 'y'}
		<tr{if !empty($tr_style)} class="{$tr_style}"{/if}>
			<td{if !empty($td_style)} class="{$td_style}"{/if}>
		{else}
			<div class="antibot3">
		{/if*}
			<div class="form-group">
				<label class="col-md-4 col-sm-3 control-label" for="antibotcode">{tr}Enter the code you see above:{/tr}{if $showmandatory eq 'y'}<span class="attention"> *</span>{/if}</label>
		{*if $antibot_table ne 'y'}
			</td>
			<td{if !empty($td_style)} class="{$td_style}"{/if}>
		{else}
			</div>
			<div class="antibot4">
		{/if*}
				<div class="col-md-4 col-sm-6">
					<input class="form-control" type="text" maxlength="8" size="22" name="captcha[input]" id="antibotcode">
				</div>
			</div>
		{*if $antibot_table ne 'y'}
			</td>
		</tr>
		{else}
			</div>
		{/if*}
	{/if}
{/if}
{jq}
if($("#antibotcode").parents('form').data("validator")) {
	$( "#antibotcode" ).rules( "add", {
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
} else {
    $("#antibotcode").parents('form').validate({
		rules: {
			"captcha[input]": {
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
			}
		},
		messages: {
			"captcha[input]": { required: "This field is required"}
		},
		submitHandler: function(){form.submit();}
	});
}
{/jq}