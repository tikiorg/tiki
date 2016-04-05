{* $Id$ *}
{if $prefs.login_autogenerate eq 'y'}
	{*do nothing*}
{elseif $prefs.user_register_prettytracker eq 'y' and $prefs.user_register_prettytracker_tpl and $prefs.socialnetworks_user_firstlogin != 'y'}
	<input type="text" name="name" id="name" class="form-control" >
	<span class='text-danger tips' title=":{tr}This field is mandatory{/tr}">*</span>
{else}
		<div class="form-group">
			<label class="col-sm-4 control-label" for="name">{if $prefs.login_is_email eq 'y'}{tr}Email{/tr}{else}{tr}Username{/tr}{/if} {if $trackerEditFormId}<span class='text-danger tips' title=":{tr}This field is mandatory{/tr}">*</span>{/if}</label>
			<div class="col-sm-8">
			{if $prefs.login_is_email eq 'y'}
				<input type="email" name="name" id="name" value="{if !empty($smarty.post.name)}{$smarty.post.name}{/if}" class="form-control" >
				<div class="help-block">{tr}Use your email address as your log-in name{/tr}</div>
			{else}
				<input type="text" name="name" id="name" value="{if !empty($smarty.post.name)}{$smarty.post.name}{/if}" class="form-control" >
				{if $prefs.min_username_length > 1}
					<div class="highlight">
						<em>{tr _0=$prefs.min_username_length}Minimum %0 characters long{/tr}</em>
					</div>
				{/if}
				{if $prefs.lowercase_username eq 'y'}
					<div class="highlight"><em>{tr}Lowercase only{/tr}</em></div>
				{/if}
			{/if}
			</div>
		</div>
{/if}
