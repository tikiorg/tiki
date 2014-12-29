{* $Id$ *}
{if $prefs.user_register_prettytracker eq 'y' and $prefs.user_register_prettytracker_tpl and $prefs.socialnetworks_user_firstlogin != 'y'}
	<input type="text" name="name" id="name">
	&nbsp;<strong class='mandatory_star'>*</strong>
{else}
		<div class="form-group">
			<label class="col-md-4 col-sm-3 control-label" for="name">{if $prefs.login_is_email eq 'y'}{tr}Email:{/tr}{else}{tr}Username:{/tr}{/if}</label>
			{if $trackerEditFormId}&nbsp;<strong class='mandatory_star'>*</strong>&nbsp;{/if}
			<div class="col-md-4 col-sm-6">
				<input class="form-control" type="text" name="name" id="name" value="{if !empty($smarty.post.name)}{$smarty.post.name}{/if}">
			{if $prefs.login_is_email eq 'y'}
				<em class="help-block">{tr}Use your email as login{/tr}</em>
			{else}
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
