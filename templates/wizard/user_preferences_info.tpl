{* $Id$ *}

<div class="userWizardIconleft"><img src="img/icons/large/wizard_user48x48.png" alt="{tr}User Wizard{/tr}" /></div>
{tr}Set up basic information as a user on this site{/tr}.
<div class="userWizardContent">
	<fieldset>
		<legend>{tr}Personal Information{/tr}</legend>
		<div class="userWizardIconright"><img src="img/icons/large/vcard48x48.png" alt="{tr}User Preferences{/tr}" /></div>
		{if $prefs.feature_userPreferences eq 'y'}
			<table class="formcolor">
				<tr>
					<td>{tr}User:{/tr}</td>
					<td>
						<strong>{$userinfo.login|escape}</strong>
						{if $prefs.login_is_email eq 'y' and $userinfo.login neq 'admin'}
							<em>({tr}Use the email as username{/tr})</em>
						{/if}
					</td>
				</tr>

				<tr>
					<td>
						{tr}Real Name:{/tr}
					</td>
					<td>
						{if $prefs.auth_ldap_nameattr eq '' || $prefs.auth_method ne 'ldap'}
							<input type="text" name="realName" value="{$realName|escape}" style="width:20em;font-size:1.1em;">
						{else}
							{$realName|escape}
						{/if}
					</td>
				</tr>

				<tr>
					<td>{tr}Profile picture:{/tr}</td>
					<td>
						{$avatar}
						{if $prefs.user_use_gravatar eq 'y'}
							<a href="http://www.gravatar.com" class="link">
								{tr}Pick user profile picture{/tr}
							</a>
						{else}
							<a href="tiki-pick_avatar.php{if $userwatch ne $user}?view_user={$userwatch}{/if}" class="link" target="_blank">
								{tr}Pick user profile picture{/tr}
							</a>
						{/if}
					</td>
				</tr>

				{if $prefs.feature_community_gender eq 'y'}
					<tr>
						<td>{tr}Gender:{/tr}</td>
						<td>
							<input type="radio" name="gender" value="Male" {if $gender eq 'Male'}checked="checked"{/if}> {tr}Male{/tr}
							<input type="radio" name="gender" value="Female" {if $gender eq 'Female'}checked="checked"{/if}> {tr}Female{/tr}
							<input type="radio" name="gender" value="Hidden" {if $gender ne 'Male' and $gender ne 'Female'}checked="checked"{/if}> {tr}Hidden{/tr}
						</td>
					</tr>
				{/if}

				<tr>
					<td>{tr}Country:{/tr}</td>
					<td>
						{if isset($country) && $country != "None" && $country != "Other"}
							{$userinfo.login|countryflag}
						{/if}
						<select name="country">
							<option value="Other" {if $country eq "Other"}selected="selected"{/if}>
								{tr}Other{/tr}
							</option>
							{foreach from=$flags item=flag key=fval}
								{if $flag ne "Other"}
									<option value="{$fval|escape}" {if $user_prefs.country eq $flag}selected="selected"{/if}>
										{$flag|stringfix}
									</option>
								{/if}
							{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td>{tr}Homepage URL:{/tr}</td>
					<td>
						<input type="text" size="40" name="homePage" value="{$homePage|escape}">
					</td>
				</tr>

				{if $prefs.feature_wiki eq 'y' and $prefs.feature_wiki_userpage eq 'y'}
					<tr>
						<td>{tr}Your personal Wiki Page:{/tr}</td>
						<td>
							{if $userPageExists eq 'y'}
								<a class="link" href="tiki-index.php?page={$prefs.feature_wiki_userpage_prefix}{$userinfo.login}" title="{tr}View{/tr}">
									{$prefs.feature_wiki_userpage_prefix}{$userinfo.login|escape}
								</a>
								(<a class="link" href="tiki-editpage.php?page={$prefs.feature_wiki_userpage_prefix}{$userinfo.login}">
									{tr}Edit{/tr}
								</a>)
							{else}
								{$prefs.feature_wiki_userpage_prefix}{$userinfo.login|escape}
								(<a class="link" href="tiki-editpage.php?page={$prefs.feature_wiki_userpage_prefix}{$userinfo.login}">
									{tr}Create{/tr}
								</a>)
							{/if}
						</td>
					</tr>
				{/if}

				{if $prefs.userTracker eq 'y' && $usertrackerId}
					{if $tiki_p_admin eq 'y' and !empty($userwatch) and $userwatch neq $user}
						<tr>
							<td>{tr}User's personal tracker information:{/tr}</td>
							<td>
								<a class="link" href="tiki-view_tracker_item.php?trackerId={$usertrackerId}&user={$userwatch|escape:url}&view=+user">
									{tr}View extra information{/tr}
								</a>
							</td>
						</tr>
					{else}
						<tr>
							<td>{tr}Your personal tracker information:{/tr}</td>
							<td>
								<a class="link" href="tiki-view_tracker_item.php?view=+user">
									{tr}View extra information{/tr}
								</a>
							</td>
						</tr>
					{/if}
				{/if}
				{* Custom fields *}
				{section name=ir loop=$customfields}
					{if $customfields[ir].show}
						<tr>
							<td>{$customfields[ir].label}:</td>
							<td>
								<input type="{$customfields[ir].type}" name="{$customfields[ir].prefName}" value="{$customfields[ir].value}" size="{$customfields[ir].size}">
							</td>
						</tr>
					{/if}
				{/section}
				<tr>
					<td>{tr}User information:{/tr}</td>
					<td>
						<select name="user_information">
							<option value='private' {if $user_information eq 'private'}selected="selected"{/if}>
								{tr}Private{/tr}
							</option>
							<option value='public' {if $user_information eq 'public'}selected="selected"{/if}>
								{tr}Public{/tr}
							</option>
						</select>
					</td>
				</tr>
			</table>

		{else}
			{tr}The feature 'User Preferences' is currently disabled in this site{/tr}.<br/>
			{if $userwatch=='admin'}
				{remarksbox title="{tr}Enable the feature{/tr}" type="errors"}
					<table>
						<tr>
							<td>
								{tr}Enable it:{/tr}
							</td>
							<td>
								{preference name=feature_userPreferences}
							</td>
						</tr>
					</table>
				{/remarksbox}
			{else}
				<br/><strong>{tr}You might ask your site admin to enable it{/tr}.</strong>
				<br/><br/>
			{/if}
		{/if}

	</fieldset>

</div>
