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
			<div class="adminoptionbox">{tr}Tiki version{/tr}: <strong>{$tiki_version}</strong> 
				{button href="tiki-install.php" _text="{tr}Reset or upgrade your database{/tr}"}
			</div>
	
			<div class="adminoptionbox">
				{preference name=feature_version_checks}
				{preference name=tiki_version_check_frequency}
				{button href="tiki-admin.php?page=general&amp;forcecheck=1" _text="{tr}Check for updates now{/tr}."}
			</div>
		</fieldset>

    <fieldset>
			<legend>{tr}Site Identity{/tr}</legend>
			{preference name=browsertitle}
			{preference name=sender_email}

			<div class="adminoptionbox">
				{tr}Go to <a href="tiki-admin.php?page=look" title=""><strong>Look &amp; Feel</strong></a> section for additional site related customization preferences{/tr}.
			</div>
		</fieldset>
    
		<fieldset>
			<legend>{tr}Home Page{/tr}</legend>
			<div class="adminoptionbox">	  
				{preference name=useGroupHome}
				{preference name=limitedGoGroupHome}
				
			<div class="adminoptionbox">
				<div id="tiki_home_page" style="display:{if $prefs.useUrlIndex eq 'y'}none{else}block{/if};">{tr}Use TikiWiki feature as homepage{/tr}: 
					<select name="tikiIndex" id="general-homepage">
            <option value="tiki-index.php"
              {if $prefs.site_tikiIndex eq 'tiki-index.php'}selected="selected"{/if}>
              {tr}Wiki{/tr}</option>
{if $prefs.feature_articles eq 'y'}
            <option value="tiki-view_articles.php"
              {if $prefs.site_tikiIndex eq 'tiki-view_articles.php'}selected="selected"{/if}>
              {tr}Articles{/tr}</option>
{/if}
            {if $prefs.home_blog}
              <option value="{$home_blog_url|escape}"
                {if $prefs.site_tikiIndex eq $home_blog_url}selected="selected"{/if}>
                {tr}Blog{/tr}: {$home_blog_name}</option>
            {/if}
            {if $prefs.home_gallery}
              <option value="{$home_gallery_url|escape}"
                {if $prefs.site_tikiIndex eq $home_gallery_url}selected="selected"{/if}>
                {tr}Image Gallery{/tr}: {$home_gal_name}</option>
            {/if}
            {if $prefs.home_file_gallery}
              <option value="{$home_file_gallery_url|escape}"
                {if $prefs.site_tikiIndex eq $home_file_gallery_url}selected="selected"{/if}>
                {tr}File Gallery{/tr}: {$home_fil_name}</option>
            {/if}
            {if $prefs.home_forum}
              <option value="{$home_forum_url|escape}"
                {if $prefs.site_tikiIndex eq $home_forum_url}selected="selected"{/if}>
                {tr}Forum{/tr}: {$home_forum_name}</option>
            {/if}
            {if $prefs.feature_custom_home eq 'y'}
              <option value="tiki-custom_home.php"
                {if $prefs.site_tikiIndex eq 'tiki-custom_home.php'}selected="selected"{/if}>{tr}Custom home{/tr}</option>
            {/if}
					</select>
					<br />{tr}or{/tr}<br />
				</div>
				<div class="adminoption">
					<input type="checkbox" name="useUrlIndex" id="general-uri" {if $prefs.useUrlIndex eq 'y'}checked="checked" {/if}onclick="flip('tiki_home_page');" />
				</div>
				<div>
					<label for="general-uri">{tr}Use different URL as home page{/tr}</label>:<br /><input type="text" name="urlIndex" value="{$prefs.urlIndex|escape}" size="50" />
				</div>
			</div>

		</fieldset>

<fieldset><legend>{tr}Miscellaneous{/tr}</legend>

<div class="adminoptionbox">	  
	{preference name=smarty_security}

<div class="adminoptionbox">	  
	<div class="adminoption"><input type="checkbox" id="feature_pear_date" name="feature_pear_date"{if $prefs.feature_pear_date eq 'y'} checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="feature_pear_date">{tr}Use PEAR::Date library{/tr}.</label></div>
</div>
</fieldset>

<fieldset><legend>{tr}Mail{/tr}</legend>
<div class="adminoptionbox"><label for="general-charset">{tr}Default charset for sending mail{/tr}:</label> 
	<select name="default_mail_charset" id="general-charset">
		<option value="utf-8" {if $prefs.default_mail_charset eq "utf-8"}selected="selected"{/if}>utf-8</option>
		<option value="iso-8859-1" {if $prefs.default_mail_charset eq "iso-8859-1"}selected="selected"{/if}>iso-8859-1</option>
	</select>
</div>
<div class="adminoptionbox"><label for="mail_crlf">{tr}Mail end of line{/tr}:</label> 
	<select name="mail_crlf" id="mail_crlf">
		<option value="CRLF" {if $prefs.mail_crlf eq "CRLF"}selected="selected"{/if}>CRLF {tr}(standard){/tr}</option>
		<option value="LF" {if $prefs.mail_crlf eq "LF"}selected="selected"{/if}>LF {tr}(some Unix MTA){/tr}</option>
	</select>
</div>
<div class="adminoptionbox"><label for="zend_mail_handler">Mail Sender</label>
	<select name="zend_mail_handler" id="zend_mail_handler" onchange="if( this.value == 'smtp' ) show('smtp_options'); else hide('smtp_options');">
		<option value="sendmail" {if $prefs.zend_mail_handler eq 'sendmail'}selected="selected"{/if}>{tr}Sendmail{/tr}</option>
		<option value="smtp" {if $prefs.zend_mail_handler eq 'smtp'}selected="selected"{/if}>{tr}SMTP{/tr}</option>
	</select>
</div>
<div class="adminoptionboxchild" id="smtp_options" {if $prefs.zend_mail_handler neq 'smtp'} style="display: none;" {/if}>
	<div class="adminoptionbox"><label for="zend_mail_smtp_server">SMTP Server</label>
		<input type="text" name="zend_mail_smtp_server" id="zend_mail_smtp_server" value="{$prefs.zend_mail_smtp_server|escape}"/>
	</div>
	<div class="adminoptionbox"><label for="zend_mail_smtp_auth">Authentication</label>
		<select name="zend_mail_smtp_auth" id="zend_mail_smtp_auth" onchange="if( this.value == '' ) hide('smtp_auth_options'); else show('smtp_auth_options');">
			<option value="" {if $prefs.zend_mail_smtp_auth eq ''}selected="selected"{/if}>{tr}None{/tr}</option>
			<option value="login" {if $prefs.zend_mail_smtp_auth eq 'login'}selected="selected"{/if}>LOGIN</option>
			<option value="plain" {if $prefs.zend_mail_smtp_auth eq 'plain'}selected="selected"{/if}>PLAIN</option>
			<option value="crammd5" {if $prefs.zend_mail_smtp_auth eq 'crammd5'}selected="selected"{/if}>CRAM-MD5</option>
		</select>
	</div>
	<div class="adminoptionboxchild" id="smtp_auth_options" {if $prefs.zend_mail_smtp_auth eq ''} style="display: none;" {/if}>
		<p>{tr}These values will be stored in plain text in the database.{/tr}</p>
		<div class="adminoptionbox"><label for="zend_mail_smtp_user">Username</label>
			<input type="text" name="zend_mail_smtp_user" id="zend_mail_smtp_user" value="{$prefs.zend_mail_smtp_user|escape}"/>
		</div>
		<div class="adminoptionbox"><label for="zend_mail_smtp_pass">Password</label>
			<input type="password" name="zend_mail_smtp_pass" id="zend_mail_smtp_pass" value="{$prefs.zend_mail_smtp_pass|escape}"/>
		</div>
	</div>
	<div class="adminoptionbox"><label for="zend_mail_smtp_port">{tr}Port{/tr}</label>
		<input type="text" name="zend_mail_smtp_port" id="zend_mail_smtp_port" value="{$prefs.zend_mail_smtp_port|escape}"/>
	</div>
	<div class="adminoptionbox"><label for="zend_mail_smtp_security">{tr}Security{/tr}</label>
		<select name="zend_mail_smtp_security" id="zend_mail_smtp_security">
			<option value="" {if $prefs.zend_mail_smtp_security eq ''}selected="selected"{/if}>{tr}None{/tr}</option>
			<option value="ssl" {if $prefs.zend_mail_smtp_security eq 'ssl'}selected="selected"{/if}>SSL</option>
			<option value="tls" {if $prefs.zend_mail_smtp_security eq 'tls'}selected="selected"{/if}>TLS</option>
		</select>
	</div>
</div>
</fieldset>

<fieldset><legend>{tr}Logging and Reporting{/tr}</legend>
<div class="adminoptionbox">
	{preference name=error_reporting_level}
	<div class="adminoptionboxchild">
		{preference name=error_reporting_adminonly label="{tr}Visible to admin only{/tr}"}
		{preference name=smarty_notice_reporting label="{tr}Include Smarty notices{/tr}"}
	</div>
</div>
<div class="adminoptionbox">	  
	<div class="adminoption"><input type="checkbox" id="log_mail" name="log_mail"{if $prefs.log_mail eq 'y'} checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="log_mail">{tr}Log mail in Tiki logs{/tr}.</label>{if $prefs.feature_help eq 'y'}{help url="System+Log"}{/if}</div>
</div>
<div class="adminoptionbox">	  
	<div class="adminoption"><input type="checkbox" id="log_sql" name="log_sql"{if $prefs.log_sql eq 'y'} checked="checked"{/if} onclick="flip('log_sql_queries');" /></div>
	<div class="adminoptionlabel"><label for="log_sql">{tr}Log SQL{/tr}.</label>
<div id="log_sql_queries" class="adminoptionboxchild" style="display:{if $prefs.log_sql eq 'y'}display{else}none{/if};">
{tr}Log queries using more than{/tr} <input type="text" name="log_sql_perf_min" value="{$prefs.log_sql_perf_min}" size="5" /> {tr}seconds{/tr}<br /><em>{tr}This may impact performance{/tr}.</em>
</div>
	</div>
</div>
<div class="adminoptionbox">	  
	<div class="adminoption"><input type="checkbox" id="log_tpl" name="log_tpl"{if $prefs.log_tpl eq 'y'} checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="log_tpl">{tr}Add HTML comment at start and end of each Smarty template (TPL){/tr}.</label></div>
</div>
</fieldset>

<fieldset><legend>{tr}CSRF Security{/tr} {if $prefs.feature_help eq 'y'} {help url="Security"}{/if}</legend>
<div class="adminoptionbox">{tr}Use these options to protect against cross-site request forgeries (CSRF){/tr}.</div>

<div class="adminoptionbox">	  
	<div class="adminoption"><input type="checkbox" name="feature_ticketlib" id="feature_ticketlib" {if $prefs.feature_ticketlib eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_ticketlib">{tr}Require confirmation if possible CSRF detected{/tr}.</label></div>
</div>

<div class="adminoptionbox">	  
	<div class="adminoption"><input type="checkbox" name="feature_ticketlib2" id="feature_ticketlib2" {if $prefs.feature_ticketlib2 eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="feature_ticketlib2">{tr}Protect against CSRF with a ticket{/tr}.</label></div>
</div>

<div class="adminoptionbox">{tr}See <a href="tiki-admin_security.php" title="Security"><strong>Admin &gt; Security Admin</strong></a> for additional security settings{/tr}.</div>
</fieldset>
	{/tab}

	{tab name="{tr}General Settings{/tr}"}
<fieldset><legend>{tr}Site Access{/tr}</legend>
<div class="adminoptionbox">
<div class="adminoption"><input type="checkbox" name="site_closed" id="general-access" {if $prefs.site_closed eq 'y'}checked="checked" {/if}onclick="flip('close_site_message');" /></div>
<div class="adminoptionlabel"><label for="general-access">{tr}Close site (except for those with permission){/tr}.</label></div>
<div align="left" id="close_site_message" style="display:{if $prefs.site_closed eq 'y'}block{else}none{/if};" class="adminoptionboxchild">
	<div class="adminoptionlabel"><label for="general-site_closed">{tr}Message to display{/tr}:</label><br /><input type="text" name="site_closed_msg" id="general-site_closed" value="{$prefs.site_closed_msg}" size="60" /></div>
</div></div>

<div class="adminoptionbox">
<div class="adminoption"><input type="checkbox" name="use_load_threshold" id="general-load" {if $prefs.use_load_threshold eq 'y'}checked="checked" {/if}onclick="flip('close_threshold_message');" /></div>
<div class="adminoptionlabel"><label for="general-load">{tr}Close site when server load is above the threshold  (except for those with permission){/tr}.</label></div>
<div align="left" id="close_threshold_message" style="display:{if $prefs.use_load_threshold eq 'y'}block{else}none{/if};" class="adminoptionboxchild">
	<div class="adminoptionlabel">
<label for="general-max_load">{tr}Maximum average server load threshold in the last minute{/tr}:</label><input type="text" name="load_threshold" id="general-max_load" value="{$prefs.load_threshold}" size="5" />
	</div>

	<div class="adminoptionlabel"><label for="general-load_mess">{tr}Message to display{/tr}:</label><br /><input type="text" name="site_busy_msg" id="general-load_mess" value="{$prefs.site_busy_msg}" size="60" /></div>
</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="use_proxy" id="general-proxy" {if $prefs.use_proxy eq 'y'}checked="checked" {/if}onclick="flip('use_proxy_settings');" /></div>
	<div class="adminoptionlabel"><label for="general-proxy">{tr}Use proxy{/tr}.</label></div>
	<div class="adminoptionboxchild" id="use_proxy_settings" style="display:{if $prefs.use_proxy eq 'y'}block{else}none{/if};">
		<div class="adminoptionlabel"><label for="general-proxy_host">{tr}Host{/tr}:</label><input type="text" name="proxy_host" id="general-proxy_host" value="{$prefs.proxy_host|escape}" size="40" /></div>
		<div class="adminoptionlabel"><label for="general-proxy_port">{tr}Port{/tr}:</label><input size="5" type="text" name="proxy_port" id="general-proxy_port" value="{$prefs.proxy_port|escape}" /></div>
	</div>
</div>




<div class="adminoptionbox">
	<div class="adminoption"><input id="permission_denied_login_box" type="checkbox" name="permission_denied_login_box"{if $prefs.permission_denied_login_box eq 'y'} checked="checked"{/if} onclick="flip('urlonerror');" /></div>
	<div class="adminoptionlabel"><label for="permission_denied_login_box">{tr}On permission denied, display login module (for Anonymous){/tr}.</label></div>
	<div class="adminoptionlabel" id="urlonerror" style="display:{if $prefs.permission_denied_login_box eq 'y'}none{else}block{/if};">
	{tr}or{/tr}<br />
		<div class="adminoptionlabel"><label for="permission_denied_url">{tr}Send to URL{/tr}</label>:<br /><input type="text" name="permission_denied_url" id="permission_denied_url"  value="{$prefs.permission_denied_url|escape}" size="50" /></div>
	</div>
</div>

</fieldset>

<fieldset><legend>{tr}Performance{/tr}</legend>
<div class="adminoptionbox">	  
	<div class="adminoption"><input type="checkbox" name="cachepages" id="general-cache_ext_pages" {if $prefs.cachepages eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="general-cache_ext_pages">{tr}Cache external pages{/tr}</label>.</div>
</div>
<div class="adminoptionbox">	  
	<div class="adminoption"><input type="checkbox" name="cacheimages" id="general-cache_ext_imgs" {if $prefs.cacheimages eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="general-cache_ext_imgs">{tr}Cache external images{/tr}</label>.</div>
</div>
<div class="adminoptionbox">	  
	<div class="adminoption"><input type="checkbox" name="feature_obzip" id="general-gzip" {if $prefs.feature_obzip eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="general-gzip">{tr}GZip output{/tr}</label>.{if $prefs.feature_help eq 'y'}
	<a href="{$prefs.helpurl}Compression" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}:">{icon _id=help}</a>{/if}
{if $gzip_handler ne 'none'}
          <br /><div class="highlight" style="margin-left:30px;">
          {tr}Output compression is active.{/tr}<br />
          {tr}Compression is handled by{/tr}: {$gzip_handler}.</div>
{/if}
	</div>
</div>
</fieldset>

<fieldset><legend>{tr}Session{/tr}</legend>
{remarksbox type="note" title="{tr}Advanced configuration warning{/tr}"}
{tr}Note that storing session data in the database is an advanced systems administration option, and is for admins who have comprehensive access and understanding of the database, in order to deal with any unexpected effects.{/tr}
{/remarksbox}
{if $prefs.session_db ne 'y'}
<div style="padding:.5em;" align="left">{icon _id=information style="vertical-align:middle"} {tr}Enabling this feature will immediately log you out when you save this preference.{/tr} {if $prefs.forgotPass ne 'y'}If there is a chance you have forgotten your password, enable "Forget password" feature.<a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.{/if}
</div>
{/if}
<div class="adminoptionbox">	  
	<div class="adminoption"><input type="checkbox" name="session_db" id="general-session_db" {if $prefs.session_db eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="general-session_db">{tr}Store session data in database{/tr}.</label></div>
</div>
<div class="adminoptionbox">	  
	<div class="adminoptionlabel"><label for="general-session_life">{tr}Session lifetime{/tr}:</label> <input size="5" type="text" name="session_lifetime" id="general-session_life" value="{$prefs.session_lifetime|escape}" /> {tr}minutes{/tr}</div>
</div>
</fieldset>

<fieldset><legend>{tr}Contact{/tr}</legend>
{if $prefs.feature_contact ne 'y'}
<div style="padding:.5em;" align="left">{icon _id=information style="vertical-align:middle"} {tr}The "Contact Us" feature is disabled.{/tr} <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.</div>
{/if}
<div class="adminoptionbox">	  
	<div class="adminoption"><input type="checkbox" name="contact_anon" id="contact_anon" {if $prefs.contact_anon eq 'y'}checked="checked" {/if}{if $prefs.feature_contact ne 'y'}disabled="disabled" {/if}/></div>
	<div class="adminoptionlabel"><label for="contact_anon">{tr}Allow anonymous visitors to use the "Contact Us"{/tr} feature.</label>{if $prefs.feature_help eq 'y'}{help url="Contact+Us"}{/if}</div>
</div>
<div class="adminoptionbox">	  
	<div class="adminoptionlabel"><label for="general-contact">{tr}Contact user{/tr}</label>:<br /><input type="text" name="contact_user" id="general-contact" value="{$prefs.contact_user|escape}" size="40" {if $prefs.feature_contact ne 'y'}disabled="disabled" {/if}/></div>
</div>
</fieldset>

<fieldset><legend>{tr}Miscellaneous{/tr}</legend>
<div class="adminoptionbox">	  
	<div class="adminoption"><input type="checkbox" name="count_admin_pvs" id="general-pageviews" {if $prefs.count_admin_pvs eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="general-pageviews">{tr}Count admin pageviews{/tr}</label>.</div>
</div>
<div class="adminoptionbox">	  
	<div class="adminoptionlabel"><label for="general-temp">{tr}Temporary directory{/tr}:</label><br /><input type="text" name="tmpDir" id="general-temp" value="{$prefs.tmpDir|escape}" size="50" /></div>
</div>
<div class="adminoptionbox">	  
	<div class="adminoption"><input type="checkbox" name="feature_help" id="feature_help" {if $prefs.feature_help eq 'y'}checked="checked" {/if}onclick="flip('use_help_system');" /></div>
	<div class="adminoptionlabel"><label for="feature_help">{tr}Help System{/tr}:</label>{if $prefs.feature_help eq 'y'}{help url="Documentation"}{/if}</div>
	<div align="left" id="use_help_system" style="display:{if $prefs.feature_help eq 'y'}block{else}none{/if};" class="adminoptionboxchild">
		<div><label for="general-helpurl">{tr}Help URL{/tr}:</label> <input type="text" name="helpurl" id="general-helpurl" value="{$prefs.helpurl|escape}" size="40" /><br />
		<em>{tr}The default help system may not be complete.{/tr} {tr}You can help with the TikiWiki documentation.{/tr}</em>{help url="Welcome+Authors"}</div>
	</div>	
</div>
<div class="adminoptionbox">	  
	<div class="adminoption"><input type="checkbox" name="user_show_realnames" id="user_show_realnames" {if $prefs.user_show_realnames eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="user_show_realnames">{tr}Show user's real name instead of login (when possible){/tr}.</label>{if $prefs.feature_help eq 'y'} {help url="User+Preferences"}{/if}</div>
</div>
<div class="adminoptionbox">	  
	<div class="adminoptionlabel"><label for="highlight_group">{tr}Highlight group{/tr}:</label> 
	<select name="highlight_group" id="highlight_group">
<option value="0">{tr}None{/tr}</option>
{foreach key=g item=gr from=$listgroups}
<option value="{$gr.groupName|escape}" {if $gr.groupName eq $prefs.highlight_group} selected="selected"{/if}>{$gr.groupName|truncate:"52":" ..."}</option>
{/foreach}
</select>{if $prefs.feature_help eq 'y'} {help url="Groups"}{/if}
</div>
</div>

<div class="adminoptionbox">	  
	<div class="adminoption"><input type="checkbox" id="feature_display_my_to_others"  name="feature_display_my_to_others" {if $prefs.feature_display_my_to_others eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="feature_display_my_to_others">{tr}Show user's contribution on the user information page{/tr}.</label>{if $prefs.feature_help eq 'y'} {help url="User+Preferences"}{/if}</div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="user_tracker_infos">{tr}Display UserTracker information on the user information page{/tr}:</label> {if $prefs.feature_help eq 'y'} {help url="User+Tracker"}{/if}
	<input type="text" id="user_tracker_infos" name="user_tracker_infos" value="{$prefs.user_tracker_infos|escape}" size="50" {if $prefs.userTracker ne 'y'}disabled="disabled" {/if}/>
	<br />
{if $prefs.userTracker ne 'y'}<span>{icon _id=information} {tr}Feature is disabled{/tr}. <a href="tiki-admin.php?page=login" title="{tr}Login{/tr}">{tr}Enable now{/tr}.</a></span>
{else}<em>{tr}Use the format: trackerId, fieldId1, fieldId2, ...{/tr}</em>
{/if}
	</div>
</div>


</fieldset>
<fieldset><legend>{tr}Separators{/tr}</legend>
<div class="adminoptionbox">	  
	<div class="adminoptionlabel"><label for="site_crumb_seper">{tr}Locations (breadcrumbs){/tr}:</label> <input type="text" name="site_crumb_seper" id="site_crumb_seper" value="{$prefs.site_crumb_seper}" size="5" maxlength="8" /><br /><em>{tr}Examples{/tr}: &nbsp; &raquo; &nbsp; / &nbsp; &gt; &nbsp; : &nbsp; -> &nbsp; &#8594;</em></div>
</div>
<div class="adminoptionbox">	  
	<div class="adminoptionlabel"><label for="site_nav_seper">{tr}Choices{/tr}:</label> <input type="text" name="site_nav_seper" id="site_nav_seper" value="{$prefs.site_nav_seper}" size="5" maxlength="8" /><br /><em>{tr}Examples{/tr}: &nbsp; | &nbsp; / &nbsp; &brvbar; &nbsp; :</em></div>
</div>
</fieldset>
	{/tab}

	{tab name="{tr}Date and Time Formats{/tr}"}
<div class="adminoptionbox">	  
	<div class="adminoptionlabel"><label for="general-timezone">{tr}Default timezone{/tr}:</label><br />
		<select name="server_timezone" id="general-timezone">
				{foreach key=tz item=tzinfo from=$timezones}
				{math equation="floor(x / (3600000))" x=$tzinfo.offset assign=offset}{math equation="(x - (y*3600000)) / 60000" y=$offset x=$tzinfo.offset assign=offset_min format="%02d"}
				<option value="{$tz}"{if $prefs.server_timezone eq $tz} selected="selected"{/if}>{$tz|escape:"html"} (UTC{if $offset >= 0}+{/if}{$offset}h{if $offset_min gt 0}{$offset_min}{/if})</option>
				{/foreach}
		</select></div>
</div>
<div class="adminoptionbox">
	<div align="left">
<input type="radio" id="users_prefs_display_timezone" name="users_prefs_display_timezone" value="Site" {if $prefs.users_prefs_display_timezone eq 'Site'}checked="checked"{/if}/> <label for="users_prefs_display_timezone">{tr}Use site default to show times{/tr}.</label><br />
<input type="radio" id="users_prefs_display_timezone2" name="users_prefs_display_timezone" value="Local" {if $prefs.users_prefs_display_timezone ne 'Site'}checked="checked"{/if} /> <label for="users_prefs_display_timezone2">{tr}Detect user timezone (if browser allows). Otherwise use site default.{/tr}</label>
	</div>
</div>

<div class="adminoptionbox">	  
	<div class="adminoptionlabel"><label for="general-long_date">{tr}Long date format{/tr}:</label><br /><input type="text" name="long_date_format" id="general-long_date" value="{$prefs.long_date_format|escape}" size="40" /><br /><em>{tr}Sample{/tr}: {$now|tiki_long_date}</em></div>
</div>
<div class="adminoptionbox">	  
	<div class="adminoptionlabel"><label for="general-short_date">{tr}Short date format{/tr}:</label><br /><input type="text" name="short_date_format" id="general-short_date" value="{$prefs.short_date_format|escape}" size="40" /><br /><em>{tr}Sample{/tr}: {$now|tiki_short_date}</em></div>
</div>
<div class="adminoptionbox">	  
	<div class="adminoptionlabel"><label for="general-long_time">{tr}Long time format{/tr}:</label><br /><input type="text" name="long_time_format" id="general-long_time" value="{$prefs.long_time_format|escape}" size="40" /><br /><em>{tr}Sample{/tr}: {$now|tiki_long_time}</em></div>
</div>
<div class="adminoptionbox">	  
	<div class="adminoptionlabel"><label for="general-short_time">{tr}Short time format{/tr}:</label><br /><input type="text" name="short_time_format" id="general-short_time" value="{$prefs.short_time_format|escape}" size="40" /><br /><em>{tr}Sample{/tr}: {$now|tiki_short_time}</em></div>
</div>
<div class="adminoptionbox">	  
	<div class="adminoptionlabel"><label for="general-display_fieldorder">{tr}Fields display order{/tr}:</label> 
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
<a class="link" target="strftime" href="{$fcnlink}">{tr}Date and Time Format Help{/tr}</a>{if $prefs.feature_help eq 'y'} {help url="Date+and+Time"}{/if}</div>
	{/tab}

	{tab name="{tr}Change admin password{/tr}"}
<p>{tr}Change the <strong>Admin</strong> password{/tr}.</p>
						<div style="float:right;width:150px;margin-left:.5em">
							<div id="mypassword_text"></div>
							<div id="mypassword_bar" style="font-size: .5em; height: 2px; width: 0px;"></div> 
						</div>

<div class="adminoptionbox">	  
	<div class="adminoptionlabel"><label for="general-new_pass">{tr}New password{/tr}:</label><br /><input type="password" name="adminpass" id="general-new_pass" onkeyup="runPassword(this.value, 'mypassword');" />
		{if $prefs.min_pass_length > 1}
								<div class="highlight"><em>{tr}Minimum {$prefs.min_pass_length} characters long{/tr}</em></div>{/if}
		{if $prefs.pass_chr_num eq 'y'}
								<div class="highlight"><em>{tr}Password must contain both letters and numbers{/tr}</em></div>{/if}

	</div>
</div>

<div class="adminoptionbox">	  
	<div class="adminoptionlabel"><label for="general-repeat_pass">{tr}Repeat password{/tr}:</label><br /><input type="password" name="again" id="general-repeat_pass" />
	</div>
</div>

<div style="padding:1em;" align="center">
	<input type="submit" name="newadminpass" value="{tr}Change password{/tr}" />
</div>
	{/tab}
{/tabset}
			<div class="heading input_submit_container" style="text-align: center;">
				<input type="submit" value="{tr}Change preferences{/tr}" />
			</div>
    
    </form>
