{* $Id$ *}
{title url="tiki-invite.php"}{tr}Invitation{/tr}{/title}

<div class="t_navbar">
	{button class="btn btn-default" _text="{tr}Invitations list{/tr}" href="tiki-list_invite.php"}
</div>

{if $sentresult}
	<div class="highlight">{tr}The mail has been sent to: {/tr}</div>
	<ul>
		{foreach from=$emails item=mail}
			<li>{$mail.email|escape}</li>
		{/foreach}
	</ul>
{elseif $smarty.request.send && !$smarty.request.confirm && !$smarty.request.back}
	<div class="highlight">{tr}You are about to send an invitation to theses people, please confirm{/tr}</div>
	<form method='POST' action='tiki-invite.php'>
		<input type='hidden' name='emailslist' value='{$smarty.request.emailslist|escape}'>
		<input type='hidden' name='emailslist_format' value='{$smarty.request.emailslist_format|escape}'>
		<input type='hidden' name='emailsubject' value='{$smarty.request.emailsubject|escape}'>
		<input type='hidden' name='emailcontent' value='{$smarty.request.emailcontent|escape}'>
		<input type='hidden' name='wikicontent' value='{$smarty.request.wikicontent|escape}'>
		<input type='hidden' name='wikipageafter' value='{$smarty.request.wikipageafter|escape}'>
		<input type='hidden' name='send' value='{$smarty.request.send|escape}'>
		{foreach from=$smarty.request.invitegroups item=g}
			<input type='hidden' name='invitegroups[]' value='{$g|escape}'>
		{/foreach}

		<input type='submit' name='confirm' value="{tr}Ok{/tr}">
		<input type='submit' name='back' value="{tr}Go back{/tr}">
	</form>
	<ul>
		{foreach from=$emails item=mail}
			<li>{$mail.email|escape}</li>
		{/foreach}
	</ul>
{else}
	<form method='POST' action='tiki-invite.php'>
		<br>
		<div style='text-align: right'>
			Load a previous invitation settings :
			<select name='loadprevious' onchange='this.form.submit()'>
				<option value=''>-</option>
				{foreach from=$previous item=prev}
					<option value='{$prev.id}'>{$prev.datetime|escape}, {tr}by{/tr} {$prev.inviter|escape}</option>
				{/foreach}
			</select>
		</div>
		<br>
		<div>{tr}Fill this box with the list of emails you want to invite :{/tr}</div>
		<div><textarea name='emailslist' style='width: 100%; height: 150px;'>{$smarty.request.emailslist|escape}</textarea></div>

		<div>{tr}Format of the list above :{/tr}
			<div><input type='radio' name='emailslist_format' value='csv' {if (! $smarty.request.emailslist_format) || $smarty.request.emailslist_format == 'csv'}checked{/if} > {tr}CSV Style: One line per invitation, with the format: lastname,firstname,email{/tr}</div>
			<div><input type='radio' name='emailslist_format' value='all' {if $smarty.request.emailslist_format == 'all'}checked{/if}> {tr}Everything that appear as an email in the text will be detected and used (in that case, {literal}{firstname} and {lastname}{/literal} will be ignored in the email content){/tr}</div>
		</div>

		<br>
		<div>{tr}Type here the email subject you'll want to be sent to them :{/tr}</div>
		<div><input name='emailsubject' style='width: 100%;' value='{if isset($smarty.request.emailsubject)}{$smarty.request.emailsubject|escape}{else}Invitation{/if}'></div>

		<br>
		<div>{tr}Type here the email content you'll want to be sent to them (and let the {literal}{link}{/literal} word, it will be replaced with the good link for registering) :{/tr}</div>
		<div><textarea name='emailcontent' style='width: 100%; height: 150px;'>{if isset($smarty.request.emailcontent)}{$smarty.request.emailcontent|escape}{else}Hi {literal}{firstname} {lastname}{/literal},

We would like to invite you to register on our web site
To register, just follow this link:

{literal}{link}{/literal}

Kind regards
{/if}</textarea> <!-- code style is broken because textarea contents must not have extra spaces -->
		</div>

		<br>
		<div>{tr}Type here the content that the user will see when he'll click on the link from the mail :{/tr}</div>
		<div>
			<textarea name='wikicontent' style='width: 100%; height: 150px;'>{if isset($smarty.request.emailcontent)}{$smarty.request.wikicontent|escape}{else}Hi {literal}{firstname} {lastname}{/literal},

You are here because you have just clicked on the link from my invitation email.

{/if}</textarea> <!-- code style is broken because textarea contents must not have extra spaces -->
		</div>

		{if count($invitegroups) > 0 && count($usergroups) > 0}
			<br>
			<div>{tr}Choose one or more groups that you want these subscriptions to be in. Don't choose any if you don't want anything special :{/tr}</div>
			<div>
				<select multiple="multiple" name='invitegroups[]'>
					{foreach from=$usergroups item=ug}
						<option value='{$ug|escape}' {if is_array($smarty.request.invitegroups) && in_array($ug,$smarty.request.invitegroups)}selected{/if}>{$ug|escape}{if !empty($invitegroups[$ug])} ({$invitegroups[$ug]|escape}){/if}</option>
					{/foreach}
				</select>
			</div>
		{else}
			<input type='hidden' name='invitegroups' value=''>
		{/if}
		<br>
		<div>Redirect to this wiki page after invitation acceptance (let it blank if unwanted) : <input type='text' name='wikipageafter' value='{$smarty.request.wikipageafter|escape}'></div>
		<br>
		<div><input type='submit' name='send' value="{tr}Send{/tr}"></div>
	</form>
{/if}
