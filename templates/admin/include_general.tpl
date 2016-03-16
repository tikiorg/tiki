{* $Id$ *}
<form class="form-horizontal" action="tiki-admin.php?page=general" class="admin" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">
	<input type="hidden" name="new_prefs" />
	<div class="t_navbar margin-bottom-md">
		{button _class="btn btn-link tips" _type="text" href="tiki-install.php" _icon_name="database" _text="{tr}Tiki installer{/tr}" _title=":{tr}Reset or upgrade your database{/tr}"}
		{button _class="btn btn-link tips" _type="text" href="tiki-admin_menus.php" _icon_name="menu" _text="{tr}Menus{/tr}" _title=":{tr}Create and edit menus{/tr}"}
		{button _class="btn btn-link tips" href="tiki-admin.php?page=general&amp;forcecheck=1" _icon_name="search" _text="{tr}Check for updates now{/tr}" _title=":{tr}Check for updates now{/tr}"}
		<div class="pull-right">
			<input type="submit" class="btn btn-primary btn-sm tips" title=":{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
		</div>
	</div>
	{if !empty($error_msg)}
		{remarksbox type='warning' title="{tr}Warning{/tr}" icon='error'}
			{$error_msg}
		{/remarksbox}
	{/if}
	{tabset name="admin_general"}
		{tab name="{tr}General Preferences{/tr}"}
			<h2>{tr}General Preferences{/tr}</h2>
			{remarksbox type="info" title="{tr}Server Fitness{/tr}" close="n"}
				{tr}To check if your server meets the requirements for running Tiki please visit <a href="tiki-check.php" class="alert-link">Tiki Server Compatibility Check</a>{/tr}.
			{/remarksbox}
			<fieldset>
				<legend>{tr}Release Check{/tr}</legend>
				{remarksbox type="info" title="{tr}Tiki version{/tr}" close="n"}
					{if !empty($lastup)}
						{tr}Last update from SVN{/tr} ({$tiki_version}): {$lastup|tiki_long_datetime}
					{else}
						{$tiki_version}
					{/if}
					{if $svnrev}
						- REV {$svnrev}
					{/if}
					({$db_engine_type})
				{/remarksbox}
				<div class="adminoptionbox">
					{preference name=tiki_release_cycle}
					{preference name=feature_version_checks}
					<div id="feature_version_checks_childcontainer">
						{preference name=tiki_version_check_frequency}
					</div>
				</div>
			</fieldset>
			<fieldset>
				<legend>{tr}Site Identity{/tr}</legend>
				{preference name=sender_email}
				{preference name=browsertitle}
				{preference name=site_title_location}
				{preference name=site_title_breadcrumb}
				{remarksbox type="info" title="{tr}Themes{/tr}"}
					{tr}Go to the <a href="tiki-admin.php?page=look" class="alert-link">Look & Feel</a> section for additional site customization preferences{/tr}.
				{/remarksbox}
			</fieldset>
			<fieldset>
				<legend>{tr}Mail{/tr}</legend>
				{preference name=default_mail_charset}
				{preference name=mail_crlf}
				{preference name=zend_mail_handler}
				<div class="adminoptionboxchild zend_mail_handler_childcontainer smtp">
					<input type="password" style="display:none" name="zend_mail_smtp_server_autocomplete_off"> {* This is now required so the browser don't store the user's login here *}
					{preference name=zend_mail_smtp_server}
					{preference name=zend_mail_smtp_auth}
					<div class="adminoptionboxchild zend_mail_smtp_auth_childcontainer login plain crammd5">
						<p>{tr}These values will be stored in plain text in the database:{/tr}</p>
						<input type="password" style="display:none" name="zend_mail_smtp_user_autocomplete_off"> {* This is now required so the browser don't store the user's login here *}
						{preference name=zend_mail_smtp_user}
						<input type="password" style="display:none" name="zend_mail_smtp_pass_autocomplete_off"> {* This is now required so the browser don't store the user's password here *}
						{preference name=zend_mail_smtp_pass}
					</div>
					{preference name=zend_mail_smtp_port}
					{preference name=zend_mail_smtp_security}
					{preference name=zend_mail_smtp_helo}
					{preference name=zend_mail_queue}
				</div>
				<div class="adminoptionbox form-group clearfix">
					<label for="testMail" class="col-md-4 control-label">{tr}Email to send a test mail{/tr}</label>
					<div class="col-md-8">
						<input type="text" name="testMail" id="testMail" class="form-control">
					</div>
				</div>
				{preference name=email_footer}
				{preference name=mail_template_custom_text}
			</fieldset>
			<fieldset>
				<legend>{tr}Newsletter{/tr}</legend>
				{preference name=newsletter_throttle}
				<div class="adminoptionboxchild" id="newsletter_throttle_childcontainer">
					{preference name=newsletter_pause_length}
					{preference name=newsletter_batch_size}
				</div>
				{preference name=newsletter_external_client}
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
				{preference name=disableJavascript}
				{preference name=log_mail}
				{preference name=log_sql}
				<div class="adminoptionboxchild" id="log_sql_childcontainer">
					{preference name=log_sql_perf_min}
				</div>
			</fieldset>
		{/tab}
		{tab name="{tr}General Settings{/tr}"}
			<h2>{tr}General Settings{/tr}</h2>
			<fieldset>
				<legend>{tr}Server{/tr}</legend>
				{preference name=tmpDir}
				{preference name=use_proxy}
				<div class="adminoptionboxchild" id="use_proxy_childcontainer">
					{preference name=proxy_host}
					{preference name=proxy_port}
					{preference name=proxy_user}
					{preference name=proxy_pass}
				</div>
				{preference name=http_skip_frameset}
				{preference name=feature_loadbalancer}
				{preference name=feature_port_rewriting}
				{preference name=access_control_allow_origin}
			</fieldset>
			<fieldset>
				<legend>{tr}Multi-domain{/tr}</legend>
				{preference name=multidomain_active}
				{preference name=multidomain_switchdomain}
				<div class="adminoptionboxchild" id="multidomain_active_childcontainer">
					{preference name=multidomain_config}
				</div>
			</fieldset>
			<fieldset>
				<legend>{tr}Sessions{/tr}</legend>
				{remarksbox type="note" title="{tr}Advanced configuration{/tr}"}
					{tr}Note that storing session data in the database is an advanced systems administration option, and is for admins who have comprehensive access and understanding of the database, in order to deal with any unexpected effects.{/tr}
				{/remarksbox}
				{remarksbox type="warning" title="{tr}Warning{/tr}" close="n"}
					{tr}Changing this feature will immediately log you out when you save this preference.{/tr} {if $prefs.forgotPass ne 'y'}If there is a chance you have forgotten your password, enable "Forget password" feature.<a href="tiki-admin.php?page=features" title="{tr}Features{/tr}" class="alert-link">{tr}Enable now{/tr}</a>.{/if}
				{/remarksbox}
				{preference name=session_storage}
				{preference name=session_lifetime}
				{preference name=session_cookie_name}
			</fieldset>
			<fieldset>
				<legend>{tr}Site terminal{/tr}</legend>
				{preference name=site_terminal_active}
				<div class="adminoptionboxchild" id="site_terminal_active_childcontainer">
					{preference name=site_terminal_config}
				</div>
			</fieldset>
			<fieldset>
				<legend>{tr}Contact{/tr}</legend>
				{preference name=feature_contact}
				<div class="adminoptionboxchild" id="feature_contact_childcontainer">
					{preference name=contact_anon}
					{preference name=contact_priority_onoff}
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
				<legend>{tr}Print{/tr}</legend>
				{preference name=print_pdf_from_url}
				<div class="adminoptionboxchild print_pdf_from_url_childcontainer webkit">
					{preference name=print_pdf_webkit_path}
				</div>
				<div class="adminoptionboxchild print_pdf_from_url_childcontainer weasyprint">
					{preference name=print_pdf_weasyprint_path}
				</div>
				<div class="adminoptionboxchild print_pdf_from_url_childcontainer webservice">
					{preference name=print_pdf_webservice_url}
				</div>
			</fieldset>
			<fieldset>
				<legend>{tr}Terms and Conditions{/tr}</legend>
				{preference name=conditions_enabled}
				<div class="adminoptionboxchild" id="conditions_enabled_childcontainer">
					{preference name=conditions_page_name}
					{preference name=conditions_minimum_age}
				</div>
			</fieldset>
			<fieldset>
				<legend>{tr}Miscellaneous{/tr}</legend>
				{preference name=feature_help}
				<div class="adminoptionboxchild" id="feature_help_childcontainer">
					{preference name=helpurl}
				</div>
				{remarksbox type="info" title="{tr}Change admin password{/tr}"}
					{tr}Change the <strong>Admin</strong> password:{/tr} <a href="tiki-adminusers.php?find=admin" class="alert-link">{tr}User administration{/tr}</a>
				{/remarksbox}
			</fieldset>
		{/tab}
		{tab name="{tr}Navigation{/tr}"}
			<h2>{tr}Navigation{/tr}</h2>
			<fieldset>
				<legend>{tr}Menus{/tr}</legend>
				<div class="adminoptionbox">
					{preference name=feature_cssmenus}
					{preference name=feature_userlevels}
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
				{preference name=useUrlIndex}
				<div class="adminoptionboxchild" id="useUrlIndex_childcontainer">
					{preference name=urlIndex}
				</div>
				{preference name=wikiHomePage}
				{preference name=home_blog}
				{preference name=home_forum}
				{preference name=home_file_gallery}
				{preference name=home_gallery}
			</fieldset>
			<fieldset>
				<legend>{tr}Redirects{/tr}</legend>
				{preference name=tiki_domain_prefix}
				{preference name=tiki_domain_redirects}
				{preference name=feature_redirect_on_error}
				{preference name='feature_wiki_1like_redirection'}
				<div class="well well-sm">
					{preference name='permission_denied_login_box' mode='invert'}
					<div class="adminoptionboxchild" id="permission_denied_login_box_childcontainer">
						<div class="text-center">
							<strong>{tr}or{/tr}</strong>
						</div>
						</br>
						{preference name=permission_denied_url}
					</div>
				</div>
				{preference name='url_anonymous_page_not_found'}
				{preference name='url_after_validation'}
				{preference name='feature_alternate_registration_page'}
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
			<fieldset>
				<legend class="heading">{tr}Breadcrumbs{/tr}</legend>
				{preference name=feature_breadcrumbs}
				<div class="adminoptionboxchild" id="feature_breadcrumbs_childcontainer">
					{preference name=feature_siteloclabel}
					{preference name=feature_siteloc}
					{preference name=feature_sitetitle}
					{preference name=feature_sitedesc}
				</div>
			</fieldset>
			<fieldset>
				<legend class="heading">{tr}Namespace{/tr}</legend>
				{preference name=namespace_enabled}
				<div class="adminoptionboxchild" id="namespace_enabled_childcontainer">
					{preference name=namespace_separator}
					{remarksbox type="warning" title="{tr}Warning{/tr}" close="n"}
						{tr}The namespace separator should not{/tr}
						<ul>
							<li>{tr}contain any of the characters not allowed in wiki page names, typically{/tr} /?#[]@$&amp;+;=&lt;&gt;</li>
							<li>{tr}conflict with wiki syntax tagging{/tr}</li>
						</ul>
					{/remarksbox}
					{preference name=namespace_indicator_in_structure}
					{preference name=feature_use_three_colon_centertag}
					{preference name=wiki_pagename_strip}
					{remarksbox type="note" title="{tr}Information{/tr}"}
						{tr}To use :: as a separator, you should also use ::: as the wiki center tag syntax{/tr}.<br/>
						{tr}Note: a conversion of :: to ::: for existing pages must be done manually{/tr}.<br/>
						{tr}If the page name display stripper conflicts with the namespace separator, the namespace is used and the page name display is not stripped.{/tr}
					{/remarksbox}
				</div>
			</fieldset>
		{/tab}
		{tab name="{tr}Date and Time{/tr}"}
			<h2>{tr}Date and Time{/tr}{help url="Date+and+Time"}</h2>
			{remarksbox type="info" title="{tr}php.net{/tr}"}
				<a class="alert-link" href="http://www.php.net/manual/en/function.strftime.php">
					{tr}Date and Time Format Help{/tr}
				</a>
			{/remarksbox}
			{preference name=server_timezone}
			{preference name=users_prefs_display_timezone}
			<div class="clearfix">
				{preference name=long_date_format}
				<span class="help-block col-md-8 col-md-push-4">
					{tr}Sample:{/tr} {$now|tiki_long_date}
				</span>
			</div>
			<div class="clearfix">
				{preference name=short_date_format}
				<span class="help-block col-md-8 col-md-push-4">
					{tr}Sample:{/tr} {$now|tiki_short_date}
				</span>
			</div>
			<div class="clearfix">
				{preference name=long_time_format}
				<span class="help-block col-md-8 col-md-push-4">
					{tr}Sample:{/tr} {$now|tiki_long_time}
				</span>
			</div>
			<div class="clearfix">
				{preference name=short_time_format}
				<span class="help-block col-md-8 col-md-push-4">
					{tr}Sample:{/tr} {$now|tiki_short_time}
				</span>
			</div>
			{preference name=short_date_format_js}
			{preference name=short_time_format_js}
			<fieldset>
				<legend>{tr}Date/time selectors{/tr}</legend>
				{preference name=display_field_order}
				{preference name=display_start_year}
				{preference name=display_end_year}
				{preference name=users_prefs_display_12hr_clock}
			</fieldset>
			{preference name=tiki_same_day_time_only}
			{preference name=jquery_timeago}
			{preference name=wikiplugin_now}
			{preference name=wikiplugin_countdown}
			{preference name=wikiplugin_timesheet}
			{preference name=wikiplugin_convene}
		{/tab}
	{/tabset}
	<div class="t_navbar margin-bottom-md text-center">
		<input type="submit" class="btn btn-primary btn-sm tips" title=":{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
	</div>
</form>
