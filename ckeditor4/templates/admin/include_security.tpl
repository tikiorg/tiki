{* $Id$ *}

<div class="navbar">
	{button href="tiki-admingroups.php" _text="{tr}Admin Groups{/tr}"}
	{button href="tiki-adminusers.php" _text="{tr}Admin Users{/tr}"}
	{button href="tiki-objectpermissions.php" _text="{tr}Manage permissions{/tr}"}
</div>

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}Please see the <a class='rbox-link' target='tikihelp' href='http://dev.tiki.org/Security'>Security page</a> on Tiki's developer site.{/tr}
	{tr}See <a href="tiki-admin_security.php" title="Security"><strong>Admin &gt; Security Admin</strong></a> for additional security settings{/tr}.
{/remarksbox}

<form class="admin" id="security" name="security" action="tiki-admin.php?page=security" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="security" value="{tr}Apply{/tr}" />
		<input type="reset" name="securityreset" value="{tr}Reset{/tr}" />
	</div>

{tabset}

		{tab name="{tr}General Security{/tr}"}
			{preference name=smarty_security}
			<div class="adminoptionboxchild" id="smarty_security_childcontainer">	
				{preference name=smarty_security_functions}
				{preference name=smarty_security_modifiers}
			</div>
			{preference name=feature_purifier}
			{preference name=feature_htmlpurifier_output}
			{preference name=menus_item_names_raw_teaser}
			<div class="adminoptionboxchild" id="menus_item_names_raw_teaser_childcontainer">	
				{preference name=menus_item_names_raw}
			</div>
			
			{preference name=session_protected}
			{preference name=login_http_basic}

			{tr}Please also see:{/tr} <a href="tiki-admin.php?page=login">{tr}HTTPS (SSL) and other login preferences{/tr}</a>

			{preference name=newsletter_external_client}

			{preference name=tiki_check_file_content}
			{preference name=tiki_allow_trust_input}
			{preference name=feature_quick_object_perms}
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
			<fieldset>
			<legend>{tr}Captcha{/tr}</legend>
			{preference name=feature_antibot}
				{preference name=captcha_wordLen}
				{preference name=captcha_width}
				{preference name=captcha_noise}
			<div class="adminoptionboxchild" id="feature_antibot_childcontainer">
				{preference name=recaptcha_enabled}
				<div class="adminoptionboxchild" id="recaptcha_enabled_childcontainer">
					{preference name=recaptcha_pubkey}
					{preference name=recaptcha_privkey}
					{preference name=recaptcha_theme}
				</div>
			</div>
			</fieldset>
			{preference name=feature_wiki_protect_email}
			{preference name=feature_wiki_ext_rel_nofollow}
			{preference name=feature_banning}
			
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
		{/tab}
	
		{tab name="{tr}Search results{/tr}"}
				{preference name=feature_search_show_forbidden_cat}
				{preference name=feature_search_show_forbidden_obj}
		{/tab}

		{tab name="{tr}Site Access{/tr}"}
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
				{remarksbox type="tip" title="{tr}Tip{/tr}"}
					{tr}To manage tokens go to <a href="tiki-admin_tokens.php">Admin Tokens</a> page{/tr}
				{/remarksbox}
				{preference name=auth_token_access}
				{preference name=auth_token_access_maxtimeout}
				{preference name=auth_token_access_maxhits}
				{preference name=auth_token_tellafriend}
				{preference name=auth_token_share}
		{/tab}

		{tab name="{tr}Clipperz online password management{/tr}"}
			<fieldset>
			{tr}Tiki doesn't offer a built-in password management feature.{/tr} <a href="http://doc.tiki.org/clipperz" target="_blank">{tr}Learn more about Clipperz{/tr}</a>
			</fieldset>
		{/tab}		
		
		{tab name="{tr}OpenPGP{/tr}"}
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
	
	<div class="input_submit_container" style="margin-top: 5px; text-align: center">
		<input type="submit" name="security" value="{tr}Apply{/tr}" />
	</div>
</form>
