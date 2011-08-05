{* $Id$ *}

{title}
	{if $report eq 'y'}
		{tr}Report to Webmaster{/tr}
	{else}
		{tr}Send a link to a friend{/tr}
	{/if}
{/title}

<div class="navbar">
	{button href="$url" _text="{tr}Back{/tr}"}
</div>

{if isset($sent)}
	<div class="simplebox highlight">{icon _id=accept alt="{tr}OK{/tr}" style="vertical-align:middle" align="left"} 
		{if $report eq 'y'}
			{tr}Your email was sent{/tr}.
		{else}
			{tr}The link was sent to the following addresses:{/tr}<br />
			{$sent|escape}
		{/if}
	</div>
{/if}

{if !empty($errors)}
	<div class="simplebox highlight">
		{icon _id=exclamation alt="{tr}Error{/tr}" style="vertical-align:middle" align="left"} 
		{foreach from=$errors item=m name=errors}
			{$m}
			{if !$smarty.foreach.errors.last}<br />{/if}
		{/foreach}
	</div>
{/if}

<form method="post" action="tiki-tell_a_friend.php" id="tellafriend">
	<input type="hidden" name="url" value="{$url|escape:url}" />
	<table class="formcolor">
		<tr>
			<td>{tr}Link{/tr}</td>
			<td><a href={$prefix}{$url}>{$prefix}{$url}</a></td>
		</tr>
		{if $report ne 'y'}
			<tr>
				<td>{tr}Friend's email{/tr}</td>
				<td>
					<input style="width:95%;" type="text" size="60" name="addresses" value="{$addresses|escape}"/>
					<br /><em>{tr}Separate multiple email addresses with a comma.{/tr}</em>
				</td>
			</tr>
		{else}
			<input type="hidden" name="report" value="y" />
		{/if}
		<tr>
			<td>{tr}Your name{/tr}</td>
			<td><input style="width:95%;" type="text" name="name" value="{$name}" /></td>
		</tr>
		<tr>
			<td>{tr}Your email{/tr}{if empty($email)} <strong class="mandatory_star">*</strong>{/if}</td>
			<td><div class="mandatory_field"><input style="width:95%;" type="text" name="email" value="{$email}" /></div></td>
		</tr>
		<tr>
			<td>{tr}Your comment{/tr}</td>
			<td>
				<textarea name="comment" style="width:95%;" rows="10" cols='{$cols}' id='comment'>{$comment|escape|@default:"{tr}I found an interesting page that I thought you would like.{/tr}"}</textarea>
			</td>
		</tr>
		{if $prefs.feature_antibot eq 'y' && $user eq ''}
			{include file='antibot.tpl' td_style="formcolor"}
		{/if}
		<tr>
			<td></td>
			<td>
				<input type="submit" name="send" value="{tr}Send{/tr}" />
				{if $prefs.auth_token_tellafriend eq 'y'}
					<input type="checkbox" name="share_access" value="1" id="share_access"/>
					<label for="share_access">{tr}Share access rights{/tr}</label>
				{/if}
			</td>
		</tr>
	</table>
</form>
