{* $Id$ *}

<div class="userWizardIconleft"><img src="img/icons/large/wizard_user48x48.png" alt="{tr}User Wizard{/tr}" /></div>
{tr}Set up the general settings for your account{/tr}.<br/><br/><br/>
<div class="userWizardContent">
	{if $prefs.feature_userPreferences eq 'y'}
		<fieldset>
			<legend>{tr}General settings{/tr}</legend>
				<div class="userWizardIconright"><img src="img/icons/large/user.png" alt="{tr}User Params{/tr}" /></div>
				<table class="formcolor">
				<tr>
					<td>{tr}Is email public? (uses scrambling to prevent spam){/tr}</td>
					<td>
						{if $userinfo.email}
							<select name="email_isPublic">
								{section name=ix loop=$scramblingMethods}
									<option value="{$scramblingMethods[ix]|escape}" {if $email_isPublic eq $scramblingMethods[ix]}selected="selected"{/if}>
										{$scramblingEmails[ix]}
									</option>
								{/section}
							</select>
						{else}
							{tr}Unavailable - please set your email below{/tr}
						{/if}
					</td>
				</tr>

				<tr>
					<td>{tr}Does your email application need a special character set{/tr}</td>
					<td>
						<select name="mailCharset">
							{section name=ix loop=$mailCharsets}
								<option value="{$mailCharsets[ix]|escape}" {if $mailCharset eq $mailCharsets[ix]}selected="selected"{/if}>
									{$mailCharsets[ix]}
								</option>
							{/section}
						</select>
					</td>
				</tr>
				{if $prefs.change_theme eq 'y' && empty($group_style)}
					<tr>
						<td>{tr}Theme:{/tr}</td>
						<td>
							<select name="mystyle">
								<option value="" style="font-style:italic;border-bottom:1px dashed #666;">
									{tr}Site default{/tr}
								</option>
								{section name=ix loop=$styles}
									{if count($prefs.available_styles) == 0 || empty($prefs.available_styles[0]) || in_array($styles[ix], $prefs.available_styles)}
										<option value="{$styles[ix]|escape}" {if $theme eq $styles[ix]}selected="selected"{/if}>
											{$styles[ix]}
										</option>
									{/if}
								{/section}
							</select>

							{if $prefs.feature_editcss eq 'y' and $tiki_p_create_css eq 'y'}
								<a href="tiki-edit_css.php" class="link" title="{tr}Edit CSS{/tr}">{tr}Edit CSS{/tr}</a>
							{/if}
						</td>
					</tr>
				{/if}

				{if $prefs.change_language eq 'y'}
					<tr>
						<td>{tr}Preferred language:{/tr}</td>
						<td>
							<select name="language">
								{section name=ix loop=$languages}
									<option value="{$languages[ix].value|escape}" {if $language eq $languages[ix].value}selected="selected"{/if}>
										{$languages[ix].name}
									</option>
								{/section}
								<option value='' {if !$language}selected="selected"{/if}>
									{tr}Site default{/tr}
								</option>
							</select>
						</td>
					</tr>
				{/if}

				<tr>
					<td>{tr}Number of visited pages to remember:{/tr}</td>
					<td>
						<select name="userbreadCrumb">
							<option value="1" {if $userbreadCrumb eq 1}selected="selected"{/if}>1</option>
							<option value="2" {if $userbreadCrumb eq 2}selected="selected"{/if}>2</option>
							<option value="3" {if $userbreadCrumb eq 3}selected="selected"{/if}>3</option>
							<option value="4" {if $userbreadCrumb eq 4}selected="selected"{/if}>4</option>
							<option value="5" {if $userbreadCrumb eq 5}selected="selected"{/if}>5</option>
							<option value="10" {if $userbreadCrumb eq 10}selected="selected"{/if}>10</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>{tr}Displayed time zone:{/tr}</td>
					<td>
						<select name="display_timezone" id="display_timezone"{if $warning_site_timezone_set eq 'y'} disabled{/if}>
							<option value="" style="font-style:italic;">
								{tr}Detect user time zone if browser allows, otherwise site default{/tr}
							</option>
							<option value="Site" style="font-style:italic;border-bottom:1px dashed #666;"
									{if isset($display_timezone) and $display_timezone eq 'Site'} selected="selected"{/if}>
								{tr}Site default{/tr}
							</option>
							{foreach key=tz item=tzinfo from=$timezones}
								{math equation="floor(x / (3600000))" x=$tzinfo.offset assign=offset}
								{math equation="(x - (y*3600000)) / 60000" y=$offset x=$tzinfo.offset assign=offset_min format="%02d"}
								<option value="{$tz|escape}"{if isset($display_timezone) and $display_timezone eq $tz} selected="selected"{/if}>
									{$tz|escape} (UTC{if $offset >= 0}+{/if}{$offset}h{if $offset_min gt 0}{$offset_min}{/if})
								</option>
							{/foreach}
						</select>
						{if $warning_site_timezone_set eq 'y'}
							<br/><strong>{tr}Warning:{/tr}</strong> <i>{tr}Site time zone is enforced and overrides user preferences{/tr}</i>
						{/if}
					</td>
				</tr>
				<tr>
					<td>{tr}Use 12-hour clock in time selectors:{/tr}</td>
					<td>
						<input type="checkbox" name="display_12hr_clock" {if $display_12hr_clock eq 'y'}checked="checked"{/if}>
					</td>
				</tr>
				{if $prefs.feature_community_mouseover eq 'y'}
					<tr>
						<td>{tr}Display info tooltip on mouseover for every user who allows his/her information to be public{/tr}</td>
						<td>
							<input type="checkbox" name="show_mouseover_user_info" {if $show_mouseover_user_info eq 'y'}checked="checked"{/if}>
						</td>
					</tr>
				{/if}

				{if $prefs.feature_wiki eq 'y'}
					<tr>
						<td>{tr}Use double-click to edit pages:{/tr}</td>
						<td>
							<input type="checkbox" name="user_dbl" {if $user_dbl eq 'y'}checked="checked"{/if}>
						</td>
					</tr>
				{/if}
			</table>
		</fieldset>

		{if $prefs.feature_messages eq 'y' and $tiki_p_messages eq 'y'}
			<fieldset>
				<legend>{tr}User Messages{/tr}</legend>
				<div class="userWizardIconright"><img src="img/icons/mail.gif" alt="{tr}User Messages{/tr}" /></div>
				<table class="formcolor">

					{if $prefs.allowmsg_is_optional eq 'y'}
						<tr>
							<td>{tr}Allow messages from other users{/tr}</td>
							<td>
								<input type="checkbox" name="allowMsgs" {if $allowMsgs eq 'y'}checked="checked"{/if}>
							</td>
						</tr>
					{/if}
					<tr>
						<td>{tr}Send me an email for messages with priority equal to or greater than:{/tr}</td>
						<td>
							<select name="minPrio">
								<option value="1" {if $minPrio eq 1}selected="selected"{/if}>1 -{tr}Lowest{/tr}-</option>
								<option value="2" {if $minPrio eq 2}selected="selected"{/if}>2 -{tr}Low{/tr}-</option>
								<option value="3" {if $minPrio eq 3}selected="selected"{/if}>3 -{tr}Normal{/tr}-</option>
								<option value="4" {if $minPrio eq 4}selected="selected"{/if}>4 -{tr}High{/tr}-</option>
								<option value="5" {if $minPrio eq 5}selected="selected"{/if}>5 -{tr}Very High{/tr}-</option>
								<option value="6" {if $minPrio eq 6}selected="selected"{/if}>{tr}none{/tr}</option>
							</select>
						</td>
					</tr>
				</table>
			</fieldset>
		{/if}
		{if $prefs.feature_tasks eq 'y' and $tiki_p_tasks eq 'y'}
			<fieldset>
				<legend>{tr}User Tasks{/tr}</legend>
				<div class="userWizardIconright"><img src="img/icons/tasks.gif" alt="{tr}User Tasks{/tr}" /></div>
				<table class="formcolor">

					<tr>
						<td>{tr}Tasks per page{/tr}</td>
						<td>
							<select name="tasks_maxRecords">
								<option value="2" {if $tasks_maxRecords eq 2}selected="selected"{/if}>2</option>
								<option value="5" {if $tasks_maxRecords eq 5}selected="selected"{/if}>5</option>
								<option value="10" {if $tasks_maxRecords eq 10}selected="selected"{/if}>10</option>
								<option value="20" {if $tasks_maxRecords eq 20}selected="selected"{/if}>20</option>
								<option value="30" {if $tasks_maxRecords eq 30}selected="selected"{/if}>30</option>
								<option value="40" {if $tasks_maxRecords eq 40}selected="selected"{/if}>40</option>
								<option value="50" {if $tasks_maxRecords eq 50}selected="selected"{/if}>50</option>
							</select>
						</td>
					</tr>
				</table>
			</fieldset>
		{/if}
		{if $prefs.feature_mytiki eq 'y'}
			<fieldset>
				<legend>{tr}My Account{/tr}</legend>
				<div class="userWizardIconright"><img src="img/icons/project.gif" alt="{tr}My Account{/tr}" /></div>

				<table class="formcolor">

					{if $prefs.feature_wiki eq 'y'}
						<tr>
							<td>{tr}My pages{/tr}:</td>
							<td>
								<input type="checkbox" name="mytiki_pages" {if $mytiki_pages eq 'y'}checked="checked"{/if}>
							</td>
						</tr>
					{/if}

					{if $prefs.feature_blogs eq 'y'}
						<tr>
							<td>{tr}My blogs{/tr}</td>
							<td>
								<input type="checkbox" name="mytiki_blogs" {if $mytiki_blogs eq 'y'}checked="checked"{/if}>
							</td>
						</tr>
					{/if}

					{if $prefs.feature_galleries eq 'y'}
						<tr>
							<td>{tr}My galleries{/tr}</td>
							<td>
								<input type="checkbox" name="mytiki_gals" {if $mytiki_gals eq 'y'}checked="checked"{/if}>
							</td>
						</tr>
					{/if}

					{if $prefs.feature_messages eq 'y'and $tiki_p_messages eq 'y'}
						<tr>
							<td>{tr}My messages{/tr}</td>
							<td>
								<input type="checkbox" name="mytiki_msgs" {if $mytiki_msgs eq 'y'}checked="checked"{/if}>
							</td>
						</tr>
					{/if}

					{if $prefs.feature_tasks eq 'y' and $tiki_p_tasks eq 'y'}
						<tr>
							<td>{tr}My tasks{/tr}</td>
							<td>
								<input type="checkbox" name="mytiki_tasks" {if $mytiki_tasks eq 'y'}checked="checked"{/if}>
							</td>
						</tr>
					{/if}

					{if $prefs.feature_forums eq 'y' and $tiki_p_forum_read eq 'y'}
						<tr>
							<td>{tr}My forum topics{/tr}</td>
							<td>
								<input type="checkbox" name="mytiki_forum_topics" {if $mytiki_forum_topics eq 'y'}checked="checked"{/if}>
							</td>
						</tr>
						<tr>
							<td>{tr}My forum replies{/tr}</td>
							<td>
								<input type="checkbox" name="mytiki_forum_replies" {if $mytiki_forum_replies eq 'y'}checked="checked"{/if}>
							</td>
						</tr>
					{/if}

					{if $prefs.feature_trackers eq 'y'}
						<tr>
							<td>{tr}My user items{/tr}</td>
							<td>
								<input type="checkbox" name="mytiki_items" {if $mytiki_items eq 'y'}checked="checked"{/if}>
							</td>
						</tr>
					{/if}

					{if $prefs.feature_articles eq 'y'}
						<tr>
							<td>{tr}My Articles{/tr}</td>
							<td>
								<input type="checkbox" name="mytiki_articles" {if $mytiki_articles eq 'y'}checked="checked"{/if}>
							</td>
						</tr>
					{/if}

					{if $prefs.feature_userlevels eq 'y'}
						<tr>
							<td>{tr}My level{/tr}</td>
							<td>
								<select name="mylevel">
									{foreach key=levn item=lev from=$prefs.userlevels}
										<option value="{$levn}"{if $mylevel eq $levn} selected="selected"{/if}>{$lev}</option>
									{/foreach}
								</select>
							</td>
						</tr>
					{/if}

					<tr>
						<td>{tr}Reset remark boxes visibility{/tr}</td>
						<td>
							{button _text="{tr}Reset{/tr}" _onclick="if (confirm('{tr}This will reset the visibility of all the tips, notices and warning remarks boxes you have closed.{/tr}')) {ldelim}deleteCookie('rbox');{rdelim}return false;"}
						</td>
					</tr>

				</table>
			</fieldset>
		{/if}
	{else}
		<fieldset>
			{tr}The feature with the user preferences screen is disabled in this site{/tr}.<br/>
			{tr}You might ask your site admin to enable it{/tr}.
		</fieldset>
	{/if}
</div>
