{* $Id$ *}

{title}
	{if $report eq 'y'}
		{tr}Report to Webmaster{/tr}
	{else}
		{tr}Send a link to a friend{/tr}
	{/if}
{/title}

<div class="t_navbar">
	{button href="$url" class="btn btn-default" _text="{tr}Back{/tr}"}
</div>

{if isset($sent)}
	<div class="alert alert-warning">{icon name='ok' alt="{tr}OK{/tr}" style="vertical-align:middle" align="left"}
		{if $report eq 'y'}
			{tr}Your email was sent{/tr}.
		{else}
			{tr}The link was sent to the following addresses:{/tr}<br>
			{$sent|escape}
		{/if}
	</div>
{/if}

{if !empty($errors)}
	<div class="alert alert-warning">
		{icon name='error' alt="{tr}Error{/tr}" style="vertical-align:middle" align="left"}
		{foreach from=$errors item=m name=errors}
			{$m}
			{if !$smarty.foreach.errors.last}<br>{/if}
		{/foreach}
	</div>
{/if}

<form method="post" action="tiki-tell_a_friend.php" id="tellafriend" class="form-horizontal">
	<input type="hidden" name="url" value="{$url|escape:url}">
	<div class="form-group">
		<label class="control-label col-sm-3">{tr}Link{/tr}</label>
		<div class="col-sm-7 form-control-static">
			<a href={$prefix}{$url}>{$prefix}{$url}</a>
		</div>
	</div>
	{if $report ne 'y'}
		<div class="form-group">
			<label class="control-label col-sm-3">{tr}Friend's email{/tr}</label>
			<div class="col-sm-7">
				<input type="text" size="60" name="addresses" value="{$addresses|escape}" class="form-control">
				<div class="help-block">
					{tr}Separate multiple email addresses with a comma.{/tr}
				</div>
			</div>
		</div>
	{else}
		<input type="hidden" name="report" value="y">
	{/if}
	<div class="form-group">
		<label class="control-label col-sm-3">{tr}Your name{/tr}</label>
		<div class="col-sm-7">
			<input type="text" name="name" value="{$name}" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-3">{tr}Your email{/tr}{if empty($email)} <strong class="mandatory_star">*</strong>{/if}</label>
		<div class="col-sm-7">
			<div class="mandatory_field"><input class="form-control" type="text" name="email" value="{$email}"></div>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-3">{tr}Your comment{/tr}</label>
		<div class="col-sm-7">
			<textarea name="comment" class="form-control" rows="10" id='comment'>{$comment|escape|@default:"{tr}I found an interesting page that I thought you would like.{/tr}"}</textarea>
		</div>
	</div>
	{if $prefs.feature_antibot eq 'y' && $user eq ''}
		{include file='antibot.tpl' td_style="formcolor"}
	{/if}
	<div class="form-group">
		<label class="control-label col-sm-3"></label>
		<div class="col-sm-7">
			<input type="submit" class="btn btn-default btn-sm" name="send" value="{tr}Send{/tr}">
			{if $prefs.auth_token_tellafriend eq 'y'}
				<input type="checkbox" name="share_access" value="1" id="share_access">
				<label for="share_access">{tr}Share access rights{/tr}</label>
			{/if}
		</div>
	</div>
</form>
