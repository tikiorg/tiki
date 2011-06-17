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
			{preference name=feature_purifier}
			{preference name=feature_htmlpurifier_output}
			{preference name=menus_item_names_raw_teaser}
			<div class="adminoptionboxchild" id="menus_item_names_raw_teaser_childcontainer">	
				{preference name=menus_item_names_raw}
			</div>
			
			{preference name=session_protected}

			{tr}Please also see:{/tr} <a href="tiki-admin.php?page=login">{tr}HTTPS (SSL) and other login preferences{/tr}</a>

			{preference name=newsletter_external_client}
			
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
				</div>
			</div>
			</fieldset>
			{preference name=feature_wiki_protect_email}
			{preference name=feature_wiki_ext_rel_nofollow}
			{preference name=feature_banning}
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
		
		
{/tabset}	
	
	<div class="input_submit_container" style="margin-top: 5px; text-align: center">
		<input type="submit" name="security" value="{tr}Apply{/tr}" />
	</div>
</form>
