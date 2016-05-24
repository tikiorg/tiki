{* $Id$ *}
{if $wpSubscribe eq 'y'}
	{if empty($subscribeThanks)}
		{tr}Subscription confirmed!{/tr}
	{else}
		{$subscribeThanks|escape}
	{/if}
{else}
	<form method="post" class="form-horizontal">
		<input type="hidden" name="wpNlId" value="{$subscribeInfo.nlId|escape}">
		{if empty($user)}
			{if !empty($wpError)}
				{remarksbox type='errors'}
						{$wpError|escape}
				{/remarksbox}
			{/if}
			<div class="form-group">
				<label class="col-md-3 control-label" for="wpEmail">{tr}Email:{/tr}</label>
				<div class="col-md-9">
					<input type="text" class="form-control" id="wpEmail" name="wpEmail" value="{$subscribeEmail|escape}">
				</div>
			</div>
		{/if}
		{if !$user and $prefs.feature_antibot eq 'y'}
			{include file='antibot.tpl' antibot_table="y"}
		{/if}
		<div class="form-group text-center">
			{if empty($subcribeMessage)}
				<input type="submit" class="btn btn-default" name="wpSubscribe" value="{tr}Subscribe to the newsletter:{/tr} {$subscribeInfo.name}">
			{else}
				<input type="submit" class="btn btn-default" name="wpSubscribe" value="{$subcribeMessage|escape}">
			{/if}
		</div>
	</form>
{/if}
