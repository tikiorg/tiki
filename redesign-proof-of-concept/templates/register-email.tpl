{if $prefs.user_register_prettytracker eq 'y' and $prefs.user_register_prettytracker_tpl and $prefs.socialnetworks_user_firstlogin != 'y'}
	<input type="text" id="email" name="email">
	&nbsp;<strong class='mandatory_star'>*</strong>
{else}
	{if $prefs.login_is_email ne 'y'}
		<tr>
			<td>
				<label for="email">{tr}Email:{/tr}</label>
				{if $trackerEditFormId}&nbsp;<strong class='mandatory_star'>*</strong>&nbsp;{/if}
			</td>
			<td>
				<input type="text" id="email" name="email" value="{if !empty($smarty.post.email)}{$smarty.post.email}{/if}">
				{if $prefs.validateUsers eq 'y' and $prefs.validateEmail ne 'y'}
					<p class="highlight">
						<em class='mandatory_note'>{tr}A valid email is mandatory to register{/tr}</em>
					</p>
				{/if}
			</td>
		</tr>
	{/if}
{/if}
