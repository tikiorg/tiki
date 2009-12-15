{* $Id$ *}

<form action="tiki-admin.php?page=general" class="admin" method="post">
	<input type="hidden" name="new_prefs" />
	<div class="heading input_submit_container" style="text-align: right;">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>

{tabset name="admin_general"}
	{tab name="{tr}General Preferences{/tr}"}

		<fieldset>
			<legend>{tr}Release Check{/tr}</legend>
			<div class="adminoptionbox">{tr}Tiki version:{/tr} <strong>{$tiki_version}</strong>
				{button href="tiki-install.php" _text="{tr}Reset or upgrade your database{/tr}"}
			</div>
	
			<div class="adminoptionbox">
				{preference name=feature_version_checks}
				<div  id="feature_version_checks_childcontainer">
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
				<div class="adminoptionbox">
					<label for="zend_mail_smtp_server">{tr}SMTP Server{/tr}</label>
					<input type="text" name="zend_mail_smtp_server" id="zend_mail_smtp_server" value="{$prefs.zend_mail_smtp_server|escape}"/>
				</div>
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
					<div class="adminoptionbox">
						<label for="zend_mail_smtp_user">{tr}Username{/tr}</label>
						<input type="text" name="zend_mail_smtp_user" id="zend_mail_smtp_user" value="{$prefs.zend_mail_smtp_user|escape}"/>
					</div>
					<div class="adminoptionbox">
						<label for="zend_mail_smtp_pass">{tr}Password{/tr}</label>
							<input type="password" name="zend_mail_smtp_pass" id="zend_mail_smtp_pass" value="{$prefs.zend_mail_smtp_pass|escape}"/>
					</div>
				</div>
				<div class="adminoptionbox">
					<label for="zend_mail_smtp_port">{tr}Port{/tr}</label>
					<input type="text" name="zend_mail_smtp_port" id="zend_mail_smtp_port" value="{$prefs.zend_mail_smtp_port|escape}"/>
				</div>
				<div class="adminoptionbox">
					<label for="zend_mail_smtp_security">{tr}Security{/tr}</label>
					<select name="zend_mail_smtp_security" id="zend_mail_smtp_security">
						<option value="" {if $prefs.zend_mail_smtp_security eq ''}selected="selected"{/if}>{tr}None{/tr}</option>
						<option value="ssl" {if $prefs.zend_mail_smtp_security eq 'ssl'}selected="selected"{/if}>SSL</option>
						<option value="tls" {if $prefs.zend_mail_smtp_security eq 'tls'}selected="selected"{/if}>TLS</option>
					</select>
				</div>
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

		<fieldset>
			<legend>{tr}Spam protection{/tr}</legend>
				{preference name=feature_antibot}
				{preference name=feature_wiki_protect_email}
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
			<div class="adminoptionbox">
				<div id="tiki_home_page" style="display:{if $prefs.useUrlIndex eq 'y'}none{else}block{/if};">{tr}Use TikiWiki feature as homepage:{/tr}
					<select name="tikiIndex" id="general-homepage">
						<option value="tiki-index.php" {if $prefs.site_tikiIndex eq 'tiki-index.php'}selected="selected"{/if}>
							{tr}Wiki{/tr}
						</option>
						{if $prefs.feature_articles eq 'y'}
							<option value="tiki-view_articles.php" {if $prefs.site_tikiIndex eq 'tiki-view_articles.php'}selected="selected"{/if}>
								{tr}Articles{/tr}
							</option>
						{/if}
						{if $prefs.home_blog}
							<option value="{$home_blog_url|escape}" {if $prefs.site_tikiIndex eq $home_blog_url}selected="selected"{/if}>
								{tr}Blog:{/tr} {$home_blog_name|escape}
							</option>
						{/if}
						{if $prefs.home_gallery}
							<option value="{$home_gallery_url|escape}" {if $prefs.site_tikiIndex eq $home_gallery_url}selected="selected"{/if}>
								{tr}Image Gallery:{/tr} {$home_gal_name|escape}
							</option>
						{/if}
						{if $prefs.home_file_gallery}
							<option value="{$home_file_gallery_url|escape}" {if $prefs.site_tikiIndex eq $home_file_gallery_url}selected="selected"{/if}>
								{tr}File Gallery:{/tr} {$home_fil_name|escape}
							</option>
						{/if}
						{if $prefs.home_forum}
							<option value="{$home_forum_url|escape}" {if $prefs.site_tikiIndex eq $home_forum_url}selected="selected"{/if}>
								{tr}Forum:{/tr} {$home_forum_name|escape}
							</option>
						{/if}
						{if $prefs.feature_custom_home eq 'y'}
							<option value="tiki-custom_home.php" {if $prefs.site_tikiIndex eq 'tiki-custom_home.php'}selected="selected"{/if}>
								{tr}Custom home{/tr}
							</option>
						{/if}
					</select>
					<br />{tr}or{/tr}<br />
				</div>
				<div class="adminoption">
					<input type="checkbox" name="useUrlIndex" id="general-uri" {if $prefs.useUrlIndex eq 'y'}checked="checked" {/if}onclick="flip('tiki_home_page');" />
				</div>
				<div>
					<label for="general-uri">{tr}Use different URL as home page{/tr}</label>:
					<br />
					<input type="text" name="urlIndex" value="{$prefs.urlIndex|escape}" size="50" />
				</div>
			</div>

		</fieldset>

		<fieldset>
			<legend>{tr}Redirects{/tr}</legend>
			<div class="adminoptionbox">
				{preference name=feature_redirect_on_error}

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_wiki_1like_redirection" name="feature_wiki_1like_redirection" {if $prefs.feature_wiki_1like_redirection eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_wiki_1like_redirection">{tr}If a requested page doesn't exist, redirect to a similarly named page{/tr}.</label></div>
</div>				
				
			<div class="adminoptionbox">
				<div class="adminoption">
					<input id="permission_denied_login_box" type="checkbox" name="permission_denied_login_box"{if $prefs.permission_denied_login_box eq 'y'} checked="checked"{/if} onclick="flip('urlonerror');" />
				</div>
				<div class="adminoptionlabel">
					<label for="permission_denied_login_box">{tr}On permission denied, display login module (for Anonymous){/tr}.</label>
				</div>
				<div class="adminoptionlabel" id="urlonerror" style="display:{if $prefs.permission_denied_login_box eq 'y'}none{else}block{/if};">
					{tr}or{/tr}<br />
					<div class="adminoptionlabel">
						<label for="permission_denied_url">{tr}Send to URL{/tr}</label>:
						<br />
						<input type="text" name="permission_denied_url" id="permission_denied_url" value="{$prefs.permission_denied_url|escape}" size="50" />
					</div>
				</div>
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
		<div class="adminoptionbox">
			<div align="left">
				<input type="radio" id="users_prefs_display_timezone" name="users_prefs_display_timezone" value="Site" {if $prefs.users_prefs_display_timezone eq 'Site'}checked="checked"{/if}/>
				<label for="users_prefs_display_timezone">{tr}Use site default to show times{/tr}.</label>
				<br />
				<input type="radio" id="users_prefs_display_timezone2" name="users_prefs_display_timezone" value="Local" {if $prefs.users_prefs_display_timezone ne 'Site'}checked="checked"{/if} />
				<label for="users_prefs_display_timezone2">{tr}Detect user timezone (if browser allows). Otherwise use site default.{/tr}</label>
			</div>
		</div>

		<div class="adminoptionbox">
			<div class="adminoptionlabel">
				<label for="general-long_date">{tr}Long date format:{/tr}</label>
				<br />
				<input type="text" name="long_date_format" id="general-long_date" value="{$prefs.long_date_format|escape}" size="40" />
				<br />
				<em>{tr}Sample:{/tr} {$now|tiki_long_date}</em>
			</div>
		</div>
		<div class="adminoptionbox">
			<div class="adminoptionlabel">
				<label for="general-short_date">{tr}Short date format:{/tr}</label>
				<br />
				<input type="text" name="short_date_format" id="general-short_date" value="{$prefs.short_date_format|escape}" size="40" />
				<br />
				<em>{tr}Sample:{/tr} {$now|tiki_short_date}</em>
			</div>
		</div>
		<div class="adminoptionbox">
			<div class="adminoptionlabel">
				<label for="general-long_time">{tr}Long time format:{/tr}</label>
				<br />
				<input type="text" name="long_time_format" id="general-long_time" value="{$prefs.long_time_format|escape}" size="40" />
				<br />
				<em>{tr}Sample:{/tr} {$now|tiki_long_time}</em>
			</div>
		</div>
		<div class="adminoptionbox">
			<div class="adminoptionlabel">
				<label for="general-short_time">{tr}Short time format:{/tr}</label>
				<br />
				<input type="text" name="short_time_format" id="general-short_time" value="{$prefs.short_time_format|escape}" size="40" />
				<br />
				<em>{tr}Sample:{/tr} {$now|tiki_short_time}</em>
			</div>
		</div>
		<div class="adminoptionbox">
			<div class="adminoptionlabel">
				<label for="general-display_fieldorder">{tr}Fields display order:{/tr}</label>
				<select name="display_field_order" id="general-display_fieldorder">
					<option value="DMY" {if $prefs.display_field_order=="DMY"}selected="selected"{/if}>{tr}Day{/tr} {tr}Month{/tr} {tr}Year{/tr}</option>
					<option value="DYM" {if $prefs.display_field_order=="DYM"}selected="selected"{/if}>{tr}Day{/tr} {tr}Year{/tr} {tr}Month{/tr}</option>
					<option value="MDY" {if $prefs.display_field_order=="MDY"}selected="selected"{/if}>{tr}Month{/tr} {tr}Day{/tr} {tr}Year{/tr}</option>
					<option value="MYD" {if $prefs.display_field_order=="MYD"}selected="selected"{/if}>{tr}Month{/tr} {tr}Year{/tr} {tr}Day{/tr}</option>
					<option value="YDM" {if $prefs.display_field_order=="YDM"}selected="selected"{/if}>{tr}Year{/tr} {tr}Day{/tr} {tr}Month{/tr}</option>
					<option value="YMD" {if $prefs.display_field_order=="YMD"}selected="selected"{/if}>{tr}Year{/tr} {tr}Month{/tr} {tr}Day{/tr}</option>
				</select>
			</div>
		</div>
		<div class="adminoptionbox">	
			{assign var="fcnlink" value="http://www.php.net/manual/en/function.strftime.php"}
			<a class="link" target="strftime" href="{$fcnlink}">{tr}Date and Time Format Help{/tr}</a>{if $prefs.feature_help eq 'y'} {help url="Date+and+Time"}{/if}
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
