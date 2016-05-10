{* $Id$ *}

<div class="media">
	<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Upgrade Wizard{/tr}" title="Upgrade Wizard">
		<i class="fa fa-arrow-circle-up fa-stack-2x"></i>
		<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
	</span>
	{tr}Main new features and settings in Tiki 15{/tr}.
	<a href="http://doc.tiki.org/Tiki15" target="tikihelp" class="tikihelp" title="{tr}Tiki15:{/tr}
			{tr}Tiki15 is an LTS version{/tr}.
			{tr}As it is a Long-Term Support (LTS) version, it will be supported for 5 years.{/tr}.
			{tr}The requirements are the same as in the previous version (IE9, PHP 5.5), plus php5-curl and php5-intl are now recommended{/tr}.
			<br/><br/>
			{tr}Click to read more{/tr}
		">
		{icon name="help" size=1}
	</a>
	<br/><br/><br/>
	<div class="media-body">
		<fieldset class="table clearfix featurelist">
			<legend>{tr}Tiki Addons{/tr}</legend>
			{tr}Addons allow a way for developers to add an even broader range of functionality{/tr}
			<a href="https://doc.tiki.org/Addons" target="tikihelp" class="tikihelp" title="{tr}Addons:{/tr}
				{tr}In Tiki 14, the Tiki Addons feature was added to allow a way for developers <br/>to add an even broader range of functionality that can be used with Tiki{/tr}.
				<br/><br/>
				{tr}In Tiki 15, an addons repository was added{/tr}.
				<br/><br/>
				{tr}Click to read more{/tr}
			">
				{icon name="help" size=1}
			</a>
			{foreach $addonprefs as $addon}
				{preference name="{$addon|escape}"}
			{/foreach}
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}Remote Tiki Autologin{/tr}</legend>
			{preference name=login_autologin}
				<div class="adminoptionboxchild" id="login_autologin_childcontainer">
				{preference name=login_autologin_user}
				{preference name=login_autologin_group}
				{preference name=login_autologin_createnew}
				{preference name=login_autologin_allowedgroups}
				{preference name=login_autologin_syncgroups}
				{preference name=login_autologin_logoutremote}
				{preference name=login_autologin_redirectlogin}
				{preference name=login_autologin_redirectlogin_url}
				</div>
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}New Wiki Plugins{/tr}</legend>
			{preference name=wikiplugin_fullwidthtitle}
			{preference name=wikiplugin_googlechart}
			{preference name=wikiplugin_like}
			{preference name=wikiplugin_piwik}
			{preference name=wikiplugin_tour}
			{preference name=wikiplugin_useringroup}
			{preference name=wikiplugin_xmlupdate}
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}Social Networks with Linkedin{/tr}</legend>
				{preference name=socialnetworks_linkedin_login}
					<div class="adminoptionboxchild" id="socialnetworks_linkedin_login_childcontainer">
					{preference name=socialnetworks_linkedin_client_id}
					{preference name=socialnetworks_linkedin_client_secr}
					{preference name=socialnetworks_linkedin_autocreateuser}
					{preference name=socialnetworks_linkedin_email}
					{preference name=socialnetworks_linkedin_names}
					</div>
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}Temporary User Accounts{/tr}</legend>
				<div class="adminoptionbox preference clearfix advanced pref-fake all modified">
					<div class="adminoption form-group">
						<label class="col-sm-4 control-label">
							{tr}Display more information here{/tr}
						</label>
						<div class="col-sm-8">
							<input id="pref-fake" type="checkbox" name="pref-fake" data-tiki-admin-child-block="#pref-fake_childcontainer" data-tiki-admin-child-mode="normal"/>
						</div>
					</div>
				</div>
				<div class="adminoptionboxchild" id="pref-fake_childcontainer">
				{if $prefs['auth_token_access'] != 'y'}
					{remarksbox type="warning" title="Token Access Feature Dependency"}
					{tr}The token access feature is needed for Temporary Users to login.{/tr}
						<a href="tiki-admin.php?page=security">{tr}Turn it on here.{/tr}</a>
					{/remarksbox}
				{/if}
				{remarksbox type="info" title="Temporary Users"}
					<p>{tr}You can use this feature through: {/tr}<a href="tiki-admin.php?page=tiki-adminusers.php#contenttabs_adminusers-4">{tr}Admin Users > Temporary Users (tab){/tr}</a></p>
					<p>{tr}Temporary users cannot login the usual way but instead do so via an autologin URL that is associated with a token.{/tr} {tr}An email will be sent out to invited users containing this URL. You will receive a copy of the email yourself.{/tr}</p>
					<p>{tr}These temporary users will be deleted (but can be set to be preserved in Admin Tokens) once the validity period is over. Normally, these users should have read-only access. Nevertheless, if you are allowing these users to submit information, e.g. fill in a tracker form, make sure to ask for their information again in those forms.{/tr}</p>
					<p>{tr}Please do not assign temporary users to Groups that can access any security sensitive information, since access to these accounts is relatively easy to obtain, for example by intercepting or otherwise getting access to these emails.{/tr}</p>
				{/remarksbox}
				{remarksbox type="info" title="Revoking Access"}
				{tr}To revoke access before validity expires or to review who has access, please see:{/tr} <a href="tiki-admin_tokens.php">{tr}Admin Tokens{/tr}</a>
				{/remarksbox}
				</div>
		</fieldset>
		<fieldset class="table clearfix featurelist">
		<legend>{tr}Statistics Control Panel{/tr}</legend>
		{tr}This is a new control panel on Tiki and work is still in progress.{/tr}
			{tabset}
				{tab name="{tr}Tiki Statistics{/tr}"}
					<fieldset>
						{preference name=feature_stats}
						{preference name=feature_referer_stats}
						{preference name=count_admin_pvs}
					</fieldset>
				{/tab}

				{tab name="{tr}Google Analytics{/tr}"}
					<fieldset>
						{preference name=site_google_analytics_account}
						{preference name=site_google_credentials}
					</fieldset>
				{/tab}

				{tab name="{tr}Piwik Analytics{/tr}"}
					<fieldset>
						{preference name=site_piwik_analytics_server_url}
						{preference name=site_piwik_site_id}
					</fieldset>
				{/tab}
			{/tabset}
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}Other new features{/tr}</legend>
				{preference name="header_custom_less" syntax="css"}
				{preference name=user_multilike_config}
				{preference name='fgal_viewerjs_feature'}
				<div class="adminoptionboxchild" id="fgal_viewerjs_feature_childcontainer">
					{preference name='fgal_viewerjs_uri'}
					{if $viewerjs_err}
						<div class="col-sm-8 pull-right">
							{remarksbox type='errors' title="{tr}Warning{/tr}"}{$viewerjs_err}{/remarksbox}
						</div>
					{/if}
				</div>
				{preference name=jquery_timeago}
				{preference name=user_unique_email}
				{preference name=recaptcha_enabled}
				<div class="adminoptionboxchild" id="recaptcha_enabled_childcontainer">
					{preference name=recaptcha_pubkey}
					{preference name=recaptcha_privkey}
					{preference name=recaptcha_theme}
					{preference name=recaptcha_version}
				</div>
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}Improved and extended features{/tr}</legend>
				{preference name=wikiplugin_datachannel}
				{preference name=wikiplugin_list}
				{preference name=wikiplugin_tracker}
				{preference name=wikiplugin_mediaplayer}
				{preference name=feature_jquery_tablesorter}
				{preference name=feature_wiki_structure}
				{preference name='file_galleries_use_jquery_upload'}
				{preference name='feature_file_galleries_batch'}
				<div class="adminoptionboxchild" id="feature_file_galleries_batch_childcontainer">
					{remarksbox title="Note"}
						{tr}You are highly recommended to use a file directory as the File Gallery storage, when using this feature{/tr}
					{/remarksbox}
					<br/>
					{preference name='fgal_batch_dir'}
				</div>
				<b>{tr}Console{/tr}</b>: 
				{tr}There is a console.php command to set a scheduled batch upload cron task{/tr}.
				<a href="https://doc.tiki.org/Batch+Upload#Console_Command">{tr}More Information{/tr}...</a>
				{tr}A couple of helper commands to manage multitikis.
List the sites in a tiki instance and move a site from one tiki to another to help with upgrades etc (using local file access only){/tr}
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend>{tr}Removed features{/tr}</legend>
			{tr}Synchronize categories of user tracker item to user groups{/tr}
		</fieldset>

		<i>{tr}See the full list of changes{/tr}.</i>
		<a href="http://doc.tiki.org/Tiki15" target="tikihelp" class="tikihelp" title="{tr}Tiki15:{/tr}
			{tr}Click to read more{/tr}
		">
			{icon name="help" size=1}
		</a>
	</div>
</div>
