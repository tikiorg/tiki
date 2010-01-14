{* $Id$ *}

<form action="tiki-admin.php?page=general" class="admin" method="post">
	<input type="hidden" name="new_prefs" />
	<div class="heading input_submit_container" style="text-align: right;">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
	{if !empty($error_msg)}
		{remarksbox type='warning' title="{tr}Warning{/tr}" icon='error'}
			{$error_msg}
		{/remarksbox}	
	{/if}

	{tabset name="admin_general"}
		{tab name="{tr}General Preferences{/tr}"}
			<fieldset>
				<legend>{tr}Release Check{/tr}</legend>
				<div class="adminoptionbox">{tr}Tiki version:{/tr} 
					<strong>
						{if $lastup}
							{tr}Last update from SVN{/tr} ({$tiki_version}): {$lastup|tiki_long_datetime}
								{if $svnrev}
									- REV {$svnrev}
								{/if}
						{else} 
							{$tiki_version}
						{/if}
					</strong>
					{button href="tiki-install.php" _text="{tr}Reset or upgrade your database{/tr}"}
				</div>
	
				<div class="adminoptionbox">
					{preference name=feature_version_checks}
					<div id="feature_version_checks_childcontainer">
						{preference name=tiki_version_check_frequency}
					</div>
					{button href="tiki-admin.php?page=general&amp;forcecheck=1" _text="{tr}Check for updates now{/tr}."}
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Site Identity{/tr}</legend>
				{preference name=browsertitle}
				{preference name=site_title_location}
				{preference name=site_title_breadcrumb}
				{preference name=sender_email}

				<div class="adminoptionbox">
					{tr}Go to <a href="tiki-admin.php?page=look" title=""><strong>Look & Feel</strong></a> section for additional site related customization preferences{/tr}.
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Mail{/tr}</legend>
				{preference name=default_mail_charset}
				{preference name=mail_crlf}

				<div class="adminoptionbox">
					<label for="zend_mail_handler">{tr}Mail Sender{/tr}</label>
					<select name="zend_mail_handler" id="zend_mail_handler" onchange="if( this.value == 'smtp' ) show('smtp_options'); else hide('smtp_options');">
						<option value="sendmail" {if $prefs.zend_mail_handler eq 'sendmail'}selected="selected"{/if}>{tr}Sendmail{/tr}</option>
						<option value="smtp" {if $prefs.zend_mail_handler eq 'smtp'}selected="selected"{/if}>{tr}SMTP{/tr}</option>
					</select>
				</div>
				<div class="adminoptionboxchild" id="smtp_options" {if $prefs.zend_mail_handler neq 'smtp'} style="display: none;" {/if}>
					{preference name=zend_mail_smtp_server}

					<div class="adminoptionbox">
						<label for="zend_mail_smtp_auth">{tr}Authentication{/tr}</label>
						<select name="zend_mail_smtp_auth" id="zend_mail_smtp_auth" onchange="if( this.value == '' ) hide('smtp_auth_options'); else show('smtp_auth_options');">
							<option value="" {if $prefs.zend_mail_smtp_auth eq ''}selected="selected"{/if}>{tr}None{/tr}</option>
							<option value="login" {if $prefs.zend_mail_smtp_auth eq 'login'}selected="selected"{/if}>LOGIN</option>
							<option value="plain" {if $prefs.zend_mail_smtp_auth eq 'plain'}selected="selected"{/if}>PLAIN</option>
							<option value="crammd5" {if $prefs.zend_mail_smtp_auth eq 'crammd5'}selected="selected"{/if}>CRAM-MD5</option>
						</select>
					</div>
					<div class="adminoptionboxchild" id="smtp_auth_options" {if $prefs.zend_mail_smtp_auth eq ''} style="display: none;" {/if}>
						<p>{tr}These values will be stored in plain text in the database.{/tr}</p>
						{preference name=zend_mail_smtp_user}
						{preference name=zend_mail_smtp_pass}
					</div>

					{preference name=zend_mail_smtp_port}
					{preference name=zend_mail_smtp_security}
				</div>
				<div class="adminoptionbox">
					<label for="testMail">{tr}Email to send a test mail{/tr}</label>
					<input type="text" name="testMail" id="testMail" />
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Logging and Reporting{/tr}</legend>
				<div class="adminoptionbox">
					{preference name=error_reporting_level}
					<div class="adminoptionboxchild">
						{preference name=error_reporting_adminonly label="{tr}Visible to admin only{/tr}"}
						{preference name=smarty_notice_reporting label="{tr}Include Smarty notices{/tr}"}
					</div>
				</div>

				{preference name=log_mail}
				{preference name=log_sql}
				<div class="adminoptionboxchild" id="log_sql_childcontainer">
					{preference name=log_sql_perf_min}
				</div>
			</fieldset>

		{/tab}

		{tab name="{tr}General Settings{/tr}"}
			<fieldset>
				<legend>{tr}Server{/tr}</legend>
				{preference name=tmpDir}
				{preference name=use_proxy}
				<div class="adminoptionboxchild" id="use_proxy_childcontainer">
					{preference name=proxy_host}
					{preference name=proxy_port}
				</div>			
			</fieldset>		

			<fieldset>
				<legend>{tr}MultiDomain{/tr}</legend>
				{preference name=multidomain_active}
				<div class="adminoptionboxchild" id="multidomain_active_childcontainer">
					{preference name=multidomain_config}
				</div>			
			</fieldset>			
		
			<fieldset>
				<legend>{tr}Session{/tr}</legend>
				{remarksbox type="note" title="{tr}Advanced configuration warning{/tr}"}
					{tr}Note that storing session data in the database is an advanced systems administration option, and is for admins who have comprehensive access and understanding of the database, in order to deal with any unexpected effects.{/tr}
				{/remarksbox}
				<div style="padding:.5em;" align="left">
					{icon _id=information style="vertical-align:middle"} {tr}Changing this feature will immediately log you out when you save this preference.{/tr} {if $prefs.forgotPass ne 'y'}If there is a chance you have forgotten your password, enable "Forget password" feature.<a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.{/if}
				</div>
				{preference name=session_storage}
				{preference name=session_lifetime}
			</fieldset>

			<fieldset>
				<legend>{tr}Contact{/tr}</legend>
				{preference name=feature_contact}
				<div class="adminoptionboxchild" id="feature_contact_childcontainer">
					{preference name=contact_anon}
					{preference name=contact_user}
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Stats{/tr}</legend>
				{preference name=feature_stats}
				{preference name=feature_referer_stats}
				{preference name=count_admin_pvs}
			</fieldset>
		
			<fieldset>
				<legend>{tr}Miscellaneous{/tr}</legend>
				{preference name=feature_help}
				<div class="adminoptionboxchild" id="feature_help_childcontainer">
					{preference name=helpurl}
				</div>
			</fieldset>
		{/tab}

		{tab name="{tr}Navigation{/tr}"}
			<fieldset>
				<legend>{tr}Menus{/tr}</legend>
				<em>Create and edit menus </em><a href="tiki-admin_menus.php"><em>here</em></a>
				<div class="adminoptionbox">
					{preference name=feature_phplayers}
					{preference name=feature_cssmenus}
					{preference name=feature_featuredLinks}
					{preference name=feature_menusfolderstyle}
					{preference name=menus_items_icons}
					<div id="menus_items_icons_childcontainer">
						{preference name='menus_items_icons_path'}
					</div>
				</div>
			</fieldset>	
	
			<fieldset>
				<legend>{tr}Home Page{/tr}</legend>
				<div class="adminoptionbox">
					{preference name=useGroupHome}
					<div id="useGroupHome_childcontainer">
						{preference name=limitedGoGroupHome}
					</div>
				</div>
				
				{preference name=tikiIndex defaul=$prefs.site_tikiIndex}

				<div class="adminoptionboxchild">
					{tr}or{/tr}
					<div class="adminoption">
						<input type="checkbox" name="useUrlIndex" id="general-uri" {if $prefs.useUrlIndex eq 'y'}checked="checked" {/if}onclick="flip('tiki_home_page');" />
					</div>
					{preference name=urlIndex}
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}Redirects{/tr}</legend>
				<div class="adminoptionbox">
					{preference name=feature_redirect_on_error}
					{preference name='feature_wiki_1like_redirection'}
					<div class="adminoptionbox">
						<div class="adminoption">
							<input id="permission_denied_login_box" type="checkbox" name="permission_denied_login_box"{if $prefs.permission_denied_login_box eq 'y'} checked="checked"{/if} onclick="flip('urlonerror');" />
						</div>
						<div class="adminoptionlabel">
							<label for="permission_denied_login_box">{tr}On permission denied, display login module (for Anonymous){/tr}.</label>
						</div>
						<div class="adminoptionlabel" id="urlonerror" style="display:{if $prefs.permission_denied_login_box eq 'y'}none{else}block{/if};">
							{tr}or{/tr}
							<br />
							{preference name=permission_denied_url}
						</div>

					{preference name='url_after_validation'}	

					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend>{tr}User{/tr}</legend>
				{preference name='urlOnUsername'}
			</fieldset>		

			<fieldset>
				<legend>{tr}Site Access{/tr}</legend>
				{preference name=site_closed}
				<div class="adminoptionboxchild" id="site_closed_childcontainer">
					{preference name=site_closed_msg}
				</div>

				{preference name=use_load_threshold}
				<div class="adminoptionboxchild" id="use_load_threshold_childcontainer">
					{preference name=load_threshold}
					{preference name=site_busy_msg}
				</div>
			</fieldset>
		{/tab}
	
		{tab name="{tr}Date and Time Formats{/tr}"}
			{preference name=feature_pear_date}
	
			<div class="adminoptionbox">
				<div class="adminoptionlabel">
					<label for="general-timezone">{tr}Default timezone:{/tr}</label>
					<br />
					<select name="server_timezone" id="general-timezone">
						{foreach key=tz item=tzinfo from=$timezones}
							{math equation="floor(x / (3600000))" x=$tzinfo.offset assign=offset}{math equation="(x - (y*3600000)) / 60000" y=$offset x=$tzinfo.offset assign=offset_min format="%02d"}
							<option value="{$tz}"{if $prefs.server_timezone eq $tz} selected="selected"{/if}>{$tz|escape:"html"} (UTC{if $offset >= 0}+{/if}{$offset}h{if $offset_min gt 0}{$offset_min}{/if})</option>
						{/foreach}
					</select>
				</div>
			</div>

			{preference name=users_prefs_display_timezone}
			{preference name=long_date_format}
			<em>{tr}Sample:{/tr} {$now|tiki_long_date}</em>

			{preference name=short_date_format}
			<em>{tr}Sample:{/tr} {$now|tiki_short_date}</em>
		
			{preference name=long_time_format}
			<em>{tr}Sample:{/tr} {$now|tiki_long_time}</em>

			{preference name=short_time_format}
			<em>{tr}Sample:{/tr} {$now|tiki_short_time}</em>

			{preference name=display_field_order}
			{preference name=tiki_same_day_time_only}
		
			<div class="adminoptionbox">	
				{assign var="fcnlink" value="http://www.php.net/manual/en/function.strftime.php"}
				<a class="link" target="strftime" href="{$fcnlink}">{tr}Date and Time Format Help{/tr}</a>{help url="Date+and+Time"}
			</div>
		{/tab}

		{tab name="{tr}Change admin password{/tr}"}
			<div style="padding:1em;" align="left">
				<p>{tr}Change the <strong>Admin</strong> password{/tr}.</p>
			</div>
		
			<div style="float:right;width:150px;margin-left:.5em">
				<div id="mypassword_text"></div>
				<div id="mypassword_bar" style="font-size: .5em; height: 2px; width: 0px;"></div>
			</div>

			<div class="adminoptionbox">
				<div class="adminoptionlabel">
					<label for="general-new_pass">{tr}New password:{/tr}</label>
					<br />
					<input type="password" name="adminpass" id="general-new_pass" onkeyup="runPassword(this.value, 'mypassword');" />
					{if $prefs.min_pass_length > 1}
						<div class="highlight">
							<em>{tr}Minimum {$prefs.min_pass_length} characters long{/tr}</em>
						</div>
					{/if}
					{if $prefs.pass_chr_num eq 'y'}
						<div class="highlight">
							<em>{tr}Password must contain both letters and numbers{/tr}</em>
						</div>
					{/if}
				</div>
			</div>

			<div class="adminoptionbox">
				<div class="adminoptionlabel">
					<label for="general-repeat_pass">{tr}Repeat password:{/tr}</label>
					<br />
					<input type="password" name="again" id="general-repeat_pass" />
				</div>
			</div>

			<div style="padding:1em;" align="left">
				<input type="submit" name="newadminpass" value="{tr}Change password{/tr}" />
			</div>
			<br/><br/><br/><br/><br/><br/><br/><br/>
		{/tab}
	{/tabset}

	<div class="heading input_submit_container" style="text-align: center;">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
</form>
