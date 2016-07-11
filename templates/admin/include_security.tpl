{* $Id$ *}

<div class="t_navbar btn-group form-group">
	<a role="link" class="btn btn-link" href="tiki-admingroups.php" title="{tr}Admin groups{/tr}">
		{icon name="group"} {tr}Admin Groups{/tr}
	</a>
	<a role="link" class="btn btn-link" href="tiki-adminusers.php" title="{tr}Admin users{/tr}">
		{icon name="user"} {tr}Admin Users{/tr}
	</a>

	{permission_link mode=link label="{tr}Manage permissions{/tr}" icon_name="key" addclass="btn btn-link"}
</div>

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}Please see the <a class='alert-link' target='tikihelp' href='http://dev.tiki.org/Security'>Security page</a> on Tiki's developer site.{/tr}
	{tr}See <a class="alert-link" href="tiki-admin_security.php" title="Security"><strong>Security Admin</strong></a> for additional security settings{/tr}.
{/remarksbox}

<form class="admin form-horizontal" id="security" name="security" action="tiki-admin.php?page=security" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm" name="security" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
			</div>
		</div>
	</div>

	{tabset}

		{tab name="{tr}General Security{/tr}"}
			<h2>{tr}General Security{/tr}</h2>
			<div class="adminoptionboxchild" id="smarty_security_childcontainer">
			{if $haveMySQLSSL}
				{if $mysqlSSL === true}
					<p class="mysqlsslstatus">{icon name="lock" iclass="text-success"} {tr}MySQL SSL connection is active{/tr}
					<a class="tikihelp" title="MySQL SSL" target="tikihelp" href="http://doc.tiki.org/MySQL SSL">
						{icon name="help"}
					</a>
					</p>
				{else}
					<p class="mysqlsslstatus">{icon name="unlock"} {tr}MySQL connection is not encrypted{/tr}<br>
					{tr}To activate SSL, copy the keyfiles (.pem) til db/cert folder. The filenames must end with "-key.pem", "-cert.pem", "-ca.pem"{/tr}
					<a class="tikihelp" title="MySQL SSL" target="tikihelp" href="http://doc.tiki.org/MySQL SSL">
						{icon name="help"}
					</a>
					</p>
				{/if}
			{else}
				<p>{icon name="lock" iclass="text-warning"} {tr}MySQL Server does not have SSL activated{/tr}
				<a class="tikihelp" title="MySQL SSL" target="tikihelp" href="http://doc.tiki.org/MySQL SSL">
					{icon name="help"}
				</a>
				</p>
			{/if}
			</div>
			{preference name=smarty_security}
			<div class="adminoptionboxchild" id="smarty_security_childcontainer">
				{preference name=smarty_security_functions}
				{preference name=smarty_security_modifiers}
				{preference name=smarty_security_dirs}
			</div>
			{preference name=feature_purifier}
			{preference name=feature_htmlpurifier_output}

			{preference name=session_protected}
			{preference name=login_http_basic}

			{tr}Please also see:{/tr} <a href="tiki-admin.php?page=login">{tr}HTTPS (SSL) and other login preferences{/tr}</a>

			{preference name=newsletter_external_client}

			{preference name=tiki_check_file_content}
			{preference name=tiki_allow_trust_input}
			{preference name=feature_quick_object_perms}
			{preference name=feature_user_encryption}
			{preference name=zend_http_sslverifypeer}
			<div class="adminoptionboxchild" id="feature_user_encryption_childcontainer">
				{if isset($no_mcrypt)}
					{remarksbox type="warning" title="{tr}Mcrypt is not loaded{/tr}"}
					{tr}User Encryption requires the PHP extension Mcrypt for encryption.
						You should activate Mcrypt before activating User Encryption{/tr}.
					{/remarksbox}
				{else}
					Requires the Mcrypt PHP extension for encryption. <u>You have Mcrypt installed</u>.<br>
				{/if}
				You may also want to add the "Domain Password" module somewhere.<br>
				<br>
				Comma separated list of password domains, e.g.: Company ABC,Company XYZ<br>
				The user can add passwords for a registered password domain.
				{preference name=feature_password_domains}
			</div>
			<fieldset>
				<legend>{tr}CSRF Security{/tr}{help url="Security"}</legend>
				<div class="adminoptionbox">
					{tr}Use these options to protect against cross-site request forgeries (CSRF){/tr}.
				</div>
				{preference name=feature_ticketlib}
				{preference name=feature_ticketlib2}
			</fieldset>
		{/tab}

		{tab name="{tr}Spam protection{/tr}"}
			<h2>{tr}Spam protection{/tr}</h2>
			{remarksbox type="tip" title="{tr}Tip{/tr}"}
				{tr}You can additionally protect from spam enabling the "<a href="http://doc.tiki.org/Forum+Admin#Forum_moderation" target="_blank">moderation queue on forums</a>", or through <strong>banning</strong> multiple ip's from the "<a href="tiki-admin_actionlog.php" target="_blank">Action log</a>", from "<a href="tiki-adminusers.php" target="_blank">Users registration</a>", or from the "<a href="tiki-list_comments.php" target="_blank">Comments moderation queue</a>" itself{/tr}.
			{/remarksbox}
			<fieldset>
				<legend>{tr}CAPTCHA{/tr}</legend>
				{preference name=feature_antibot}
				<div class="adminoptionboxchild" id="feature_antibot_childcontainer">
					{preference name=captcha_wordLen}
					{preference name=captcha_width}
					{preference name=captcha_noise}
					{preference name=recaptcha_enabled}
					<div class="adminoptionboxchild" id="recaptcha_enabled_childcontainer">
						{preference name=recaptcha_pubkey}
						{preference name=recaptcha_privkey}
						{preference name=recaptcha_theme}
						{preference name=recaptcha_version}
					</div>
					{preference name=captcha_questions_active}
					<div class="adminoptionboxchild" id="captcha_questions_active_childcontainer">
						{preference name=captcha_questions}
					</div>
				</div>
			</fieldset>
			{preference name=feature_wiki_protect_email}
			{preference name=feature_wiki_ext_rel_nofollow}
			{preference name=feature_banning}

			{preference name=feature_comments_moderation}
			{preference name=comments_akismet_filter}
			<div class="adminoptionboxchild" id="comments_akismet_filter_childcontainer">
				{preference name=comments_akismet_apikey}
				{preference name=comments_akismet_check_users}
			</div>

			{preference name=useRegisterPasscode}
			<div class="adminoptionboxchild" id="useRegisterPasscode_childcontainer">
				{preference name=registerPasscode}
				{preference name=showRegisterPasscode}
			</div>

			{preference name=registerKey}
		{/tab}

		{tab name="{tr}Search results{/tr}"}
			<h2>{tr}Search results{/tr}</h2>
			{preference name=feature_search_show_forbidden_cat}
			{preference name=feature_search_show_forbidden_obj}
		{/tab}

		{tab name="{tr}Site Access{/tr}"}
			<h2>{tr}Site Access{/tr}</h2>
			{preference name=site_closed}
			<div class="adminoptionboxchild" id="site_closed_childcontainer">
				{preference name=site_closed_msg}
			</div>

			{preference name=use_load_threshold}
			<div class="adminoptionboxchild" id="use_load_threshold_childcontainer">
				{preference name=load_threshold}
				{preference name=site_busy_msg}
			</div>
		{/tab}

		{tab name="{tr}Tokens{/tr}"}
			<h2>{tr}Tokens{/tr}</h2>
			{remarksbox type="tip" title="{tr}Tip{/tr}"}
				{tr}To manage tokens go to <a href="tiki-admin_tokens.php">Admin Tokens</a> page. Tokens are also used for the Temporary Users feature (see <a href="tiki-adminusers.php">Admin Users</a>).{/tr}
			{/remarksbox}
			{preference name=auth_token_access}
			{preference name=auth_token_access_maxtimeout}
			{preference name=auth_token_access_maxhits}
			{preference name=auth_token_tellafriend}
			{preference name=auth_token_share}
			{preference name=auth_token_preserve_tempusers}
		{/tab}

		{tab name="{tr}OpenPGP{/tr}"}
			<h2>{tr}OpenPGP{/tr}</h2>
			<fieldset>
				<legend>{tr}OpenPGP fuctionality for PGP/MIME encrypted email messaging{/tr}</legend>
				{remarksbox type="tip" title="{tr}Note{/tr}"}
					{tr}Experimental OpenPGP fuctionality for PGP/MIME encrypted email messaging.{/tr}<br><br>
					{tr}All email-messaging/notifications/newsletters are sent as PGP/MIME-encrypted messages, signed with the signer-key, and are completely 100% opaque to outsiders. All user accounts need to be properly configured into gnupg keyring with public-keys related to their tiki-account-related email-addresses.{/tr}
				{/remarksbox}
				{preference name=openpgp_gpg_pgpmimemail}
				<div class="adminoptionboxchild" id="openpgp_gpg_pgpmimemail_childcontainer">
					{preference name=openpgp_gpg_home}
					{preference name=openpgp_gpg_path}
					{preference name=openpgp_gpg_signer_passphrase_store}
					<div class="adminoptionboxchild openpgp_gpg_signer_passphrase_store_childcontainer preferences">
						{preference name=openpgp_gpg_signer_passphrase}
						<br><em>{tr}If you use preferences option for the signer passphrase, clear the file option just for security{/tr}</em>
					</div>
					<div class="adminoptionboxchild openpgp_gpg_signer_passphrase_store_childcontainer file">
						{preference name=openpgp_gpg_signer_passfile}
						<br><em>{tr}If you use file for the signer passphrase, clear the preferences option just for security{/tr}</em>
					</div>
					{remarksbox type="tip" title="{tr}Note{/tr}"}
						{tr}The email of preference <a href="tiki-admin.php?page=general&alt=General">'sender_email'</a> is used as signer key ID, and it must have both private and public key in the gnupg keyring.{/tr}
					{/remarksbox}
				</div>
			</fieldset>
		{/tab}

	{/tabset}

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="text-center">
				<input type="submit" class="btn btn-primary btn-sm" name="security" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>
</form>
