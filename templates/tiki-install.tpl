<div id="fixedwidth"> {* enables fixed-width layouts *}
	<div id="main">
<div id="header">
	<div id="siteheader" class="clearfix">
		<div id="header-top">
			<div id="sitelogo" style="padding-left:0; padding-top: 0px"><h1 style="margin: 0"><img style="border:medium none; vertical-align:middle" alt="{tr}Tiki Wiki CMS Groupware{/tr}" src="{if isset($ie6)}img/tiki/tikisitelogo.gif{else}img/tiki/Tiki_WCG.png{/if}" />
				<span style="vertical-align:middle; margin-left:120px; color: #fff;">{tr}Tiki installer{/tr} {$tiki_version_name} <a title="{tr}Help{/tr}" href="http://doc.tiki.org/Installation" target="help"><img style="border:0" src='pics/icons/help.png' alt="{tr}Help{/tr}" /></a></span></h1>
			</div>
		</div>
	</div>
	<div id="tiki-top"></div> {* added for background image consistency *}
</div>

<div id="middle" class="clearfix">
	<div id="c1c2" class="clearfix">
		<div id="wrapper" class="clearfix">
			<div id="col1" class="marginleft">
				<div id="tiki-center" class="clearfix content">

{if $install_step eq '0' or !$install_step}
{* start of installation *}
<h1>{tr}Welcome{/tr}</h1>
<div class="clearfix">
	<p>{tr}Welcome to the Tiki installation and upgrade script.{/tr} {tr}Use this script to install a new Tiki database or upgrade your existing database to release{/tr} <strong>{$tiki_version_name}</strong></p>
	<ul>
		<li>{tr}For the latest information about this release, please read the{/tr} <a href="http://tiki.org/tiki-index.php?page=ReleaseNotes{$tiki_version_name|urlencode}" target="_blank">{tr}Release Notes{/tr}</a>.</li>
		<li>{tr}For complete documentation, please visit{/tr} <a href="http://doc.tiki.org" target="_blank">doc.tiki.org</a>.</li>
		<li>{tr}For more information about Tiki, please visit{/tr} <a href="http://tiki.org" target="_blank">tiki.org</a>.</li>
	</ul>

	<form action="tiki-install.php" method="post">
		<label>{tr}Select your language:{/tr}
		<select name="lang" id="general-lang" onchange="this.form.submit();" title="{tr}Select your language:{/tr}">
			{section name=ix loop=$languages}
				<option value="{$languages[ix].value|escape}"
					{if $prefs.site_language eq $languages[ix].value}selected="selected"{/if}>{$languages[ix].name}</option>
			{/section}
		</select></label>
		<input type="hidden" name="install_step" value="0" />
		{if $multi}		<input type="hidden" name="multi" value="{$multi}" />{/if}
	</form>
</div>
<div align="center" style="margin-top: 1em;">
	<form action="tiki-install.php" method="post">
{if $multi}		<input type="hidden" name="multi" value="{$multi}" />{/if}
{if $lang}		<input type="hidden" name="lang" value="{$lang}" />{/if}
		<input type="hidden" name="install_step" value="1" />
		<input type="submit" value=" {tr}Continue{/tr} " />
	</form>
</div>

{elseif $install_step eq '1'}
<h1>{tr}Read the License{/tr}</h1>
<p>{tr}Tiki is software distributed under the LGPL license.{/tr} {tr} <a href="http://creativecommons.org/licenses/LGPL/2.1/" target="_blank">Here is a human-readable summary of the license below, including many translations.</a>{/tr}</p>
<div align="center" style="margin-top:1em;">
<iframe src="license.txt" width="700" height="300" style="width:700px;height:300px"> </iframe>
	<form action="tiki-install.php" method="post">
{if $multi}			<input type="hidden" name="multi" value="{$multi}" />{/if}
{if $lang}			<input type="hidden" name="lang" value="{$lang}" />{/if}
		<input type="hidden" name="install_step" value="2" />
		<input type="submit" value=" {tr}Continue{/tr} " />
	</form>
</div>

{elseif $install_step eq '2'}
<h1>{tr}Review the System Requirements{/tr}</h1>
<div style="float:left;width:60px"><img src="img/webmail/compose.gif" alt="{tr}Review{/tr}" /></div>
<div class="clearfix">
	<p>{tr}Before installing Tiki, <a href="http://doc.tiki.org/Requirements" target="_blank">review the documentation</a> and confirm that your system meets the minimum requirements.{/tr}</p>
	<p>{tr}This installer will perform some basic checks automatically.{/tr}</p>
	<br />
	<h2>{tr}Memory{/tr}</h2>
{if $php_memory_limit <= 0}
	<div style="background: #c2eef8; border: 2px solid #2098cd; color:#000;">
		<p align="center"><img src="pics/icons/accept.png" alt="{tr}Success{/tr}" style="vertical-align:middle" /> {tr}Tiki has not detected your PHP memory_limit.{/tr} {tr}This probably means you have no set limit (all is well).{/tr} </p>
	</div>	
{elseif $php_memory_limit < 32 * 1024 * 1024}
	<div style="background: #ffffcc; border: 2px solid #ff0000; color:#000;">
		<p align="center"><img src="pics/icons/delete.png" alt="{tr}Alert{/tr}" style="vertical-align:middle" /> {tr}Tiki has detected your PHP memory limit at:{/tr} {$php_memory_limit|kbsize:true:0}</p>
	</div>
	<p>{tr}Tiki requires <strong>at least</strong> 32MB of PHP memory for script execution.{/tr} {tr}Allocating too little memory will cause Tiki to display blank pages.{/tr}</p>
	<p>{tr}To change the memory limit, use the <strong>memory_limit</strong> key in your <strong>php.ini </strong> file (for example: memory_limit = 32M) and restart your webserver.{/tr}</p>

{else}
	<div style="background: #c2eef8; border: 2px solid #2098cd; color:#000;">
		<p align="center">
		  <span style="font-size: large; padding: 4px;">
		  <img src="pics/icons/accept.png" alt="{tr}Success{/tr}" style="vertical-align:middle" /> {tr}Tiki has detected your PHP memory_limit at:{/tr} {$php_memory_limit|kbsize:true:0}. 
		  </span>
		</p>
	</div>	
{/if}			

	<br />
	<h2>{tr}Mail{/tr}</h2><a name="mail"> </a>
	<p>{tr}Tiki uses the PHP <strong>mail</strong> function to send email notifications and messages.{/tr}</p>
{if $perform_mail_test ne 'y'}
	<p>{tr}To test your system configuration, Tiki will attempt to send a test message to you.{/tr}</p>
	<div>
	<form action="tiki-install.php#mail" method="post">
		<div style="padding:1em 7em;">
			<label for="admin_email_test">{tr}Test email:{/tr}</label>
			<input type="text" size="40" name="email_test_to" id="email_test_to" value="{if isset($email_test_to)}{$email_test_to}{/if}" />
			{if isset($email_test_err)}<span class="attention"><em>{$email_test_err}</em></span>
			{else}<em>{tr}Email address to send test to.{/tr}</em>{/if}
			<br /><br />
			<input type="checkbox" name="email_test_cc" checked="checked" value="1" />
			<em>{tr}Copy test mail to {/tr} {$email_test_tw}?</em>
		</div>
		<input type="hidden" name="install_step" value="2" />
		<input type="hidden" name="perform_mail_test" value="y" />
		<div align="center">
			<input type="submit" value=" {tr}Send Test Message{/tr} " />
		</div>
{if $multi}		<input type="hidden" name="multi" value="{$multi}" />{/if}
{if $lang}		<input type="hidden" name="lang" value="{$lang}" />{/if}
		
	</form>
	</div>
{else}
	
{if $mail_test eq 'y'}
	<div style="background: #c2eef8; border: 2px solid #2098cd; color:#000;">
		<p align="center"><img src="pics/icons/accept.png" alt="{tr}Success{/tr}" style="vertical-align:middle" /> {tr}Tiki was able to send a test message to{/tr} {$email_test_to}.</p>
	</div>
	<p>&nbsp;</p>
{else}
	<div style="background: #ffffcc; border: 2px solid #ff0000; color:#000;">
		<p align="center"><img src="pics/icons/delete.png" alt="{tr}Alert{/tr}" style="vertical-align:middle" /> {tr}Tiki was not able to send a test message.{/tr} {tr}Review your mail log for details.{/tr}</p>
	</div>
	<p>{tr}Review the mail settings in your <strong>php.ini</strong> file (for example: confirm that the <strong>sendmail_path</strong> is correct).{/tr} {tr}If your host requires SMTP authentication, additional configuration may be necessary.{/tr}</p>
{/if}
{/if}
	<br />
	<h2>{tr}Image Processing{/tr}</h2>
{if $gd_test eq 'y'}
	<div style="background: #c2eef8; border: 2px solid #2098cd; color:#000;">
		<p align="center"><img src="pics/icons/accept.png" alt="{tr}Success{/tr}" style="vertical-align:middle" /> {tr}Tiki detected:{/tr} <strong>GD {$gd_info}</strong>.</p>
{if $sample_image eq 'y'}
		<p align="center"><img src="pics/icons/accept.png" alt="{tr}Success{/tr}" style="vertical-align:middle" /> {tr}Tiki can create images.{/tr}</p>
{else}
	<div style="background: #ffffcc; border: 2px solid #ff0000; color:#000;">
		<p align="center"><img src="pics/icons/delete.png" alt="{tr}Alert{/tr}" style="vertical-align:middle" /> {tr}Tiki was not able to create a sample image. Please check your GD library configuration.{/tr}.</p>
	</div>
{/if}
	</div>
{else}
	<div style="background: #ffffcc; border: 2px solid #ff0000; color:#000;">
		<p align="center"><img src="pics/icons/delete.png" alt="{tr}Alert{/tr}" style="vertical-align:middle" /> {tr}Tiki was not able to detect the GD library.{/tr}</p>
	</div>
	<p>&nbsp;</p>
{/if}
	<p>{tr}Tiki uses the GD library to process images for the Image Gallery and CAPTCHA support.{/tr}</p>
</div>

<div align="center" style="margin-top:1em;">
<form action="tiki-install.php" method="post">
	<input type="hidden" name="install_step" value="3" />
	<input type="submit" value=" {tr}Continue{/tr} " />
{if $multi}		<input type="hidden" name="multi" value="{$multi}" />{/if}
{if $lang}		<input type="hidden" name="lang" value="{$lang}" />{/if}
</form>
</div>

{elseif $install_step eq '3' or ($dbcon eq 'n' or $resetdb eq 'y')}
{* we do not have a valid db connection or db reset is requested *}
<h1>{tr}Set the Database Connection{/tr}</h1>
<div style="float:left; width:60px"><img src="pics/large/stock_line-in48x48.png" alt="{tr}Database{/tr}" /></div>
<div class="clearfix">
	<p>{tr}Tiki requires an active database connection.{/tr} {tr}You must create the database and user <em>before</em> completing this page.{/tr}</p>
{if $dbcon ne 'y'}
	<div align="center" style="padding:1em">
		<img src="pics/icons/delete.png" alt="{tr}Alert{/tr}" style="vertical-align:middle" /> <span style="font-weight:bold">{tr}Tiki cannot find a database connection.{/tr}</span> {tr}This is normal for a new installation.{/tr}
	</div>
{else}
	<div align="center" style="padding:1em">
		<p>
			<img src="pics/icons/information.png" alt="{tr}Information{/tr}" style="vertical-align: bottom;" />
			{tr}Tiki found an existing database connection in your local.php file.{/tr}
		</p>
		<form action="tiki-install.php" method="post">
			<input type="hidden" name="install_step" value="4" />
			{if $multi}<input type="hidden" name="multi" value="{$multi}" />{/if}
			{if $lang}<input type="hidden" name="lang" value="{$lang}" />{/if}
			<input type="submit" value=" {tr}Use Existing Connection{/tr} " />
		</form>
		or<br />
		<a href="#" onclick="$('#installer_3_new_db_form').toggle();return false;" class="button">{tr}Modify database connection{/tr}</a>
	</div>
{/if}		
	
{if $tikifeedback}
	<br />
{section name=n loop=$tikifeedback}
	<div class="simplebox {if $tikifeedback[n].num > 0} highlight{/if}">
		<img src="pics/icons/{if $tikifeedback[n].num > 0}delete.png" alt="{tr}Error{/tr}"{else}accept.png" alt="{tr}Success{/tr}"{/if} style="vertical-align:middle" /> {$tikifeedback[n].mes}
	</div>
{/section}
{/if}
  <div id="installer_3_new_db_form"{if $dbcon eq 'y'} style="display:none;"{/if}>
	<p>{tr}Use this page to create a new database connection, or use the <a href="http://doc.tiki.org/Manual+Installation" target="_blank" title="manual installation">manual installation process</a>.{/tr} <a href="http://doc.tiki.org/Manual+Installation" target="_blank" title="{tr}Help{/tr}"><img src="pics/icons/help.png" alt="{tr}Help{/tr}" /></a></p>
	<form action="tiki-install.php" method="post">
		<input type="hidden" name="install_step" value="4" />
{if $multi}		<input type="hidden" name="multi" value="{$multi}" />{/if}
{if $lang}		<input type="hidden" name="lang" value="{$lang}" />{/if}
		<fieldset><legend>{tr}Database information{/tr}</legend>
		<p>{tr}Enter your database connection information.{/tr}</p>
		<div style="padding:5px">
			<label for="db">{tr}Database type:{/tr}</label> 
			<div style="margin-left:1em">
			<select name="db" id="db">
{foreach key=dsn item=dbname from=$dbservers}
	{if $dsn|stristr:"mysql"}
				<option value="{$dsn}"{if isset($smarty.request.db) and $smarty.request.db eq $dsn} selected="selected"{/if}>{$dbname}</option>
	{/if}
{/foreach}
			</select> <a href="javascript:void(0)" onclick="flip('db_help');" title="{tr}Help{/tr}"><img src="pics/icons/help.png" alt="{tr}Help{/tr}" /></a>
			<div style="display:none" id="db_help">
				<p>{tr}Select the type of database to use with Tiki.{/tr}</p>
				<p>{tr}Only databases supported by your PHP installation are listed here. If your database is not in the list, try to install the appropriate PHP extension.{/tr}</p>
			</div>
			</div>
		</div>
		<div style="padding:5px">
			<label for="host">{tr}Host name:{/tr}</label>
			<div style="margin-left:1em">
			<input type="text" name="host" id="host" value="{if isset($smarty.request.host)}{$smarty.request.host|escape:"html"}{else}localhost{/if}" size="40" /> <a href="javascript:void(0)" onclick="flip('host_help');" title="{tr}Help{/tr}"><img src="pics/icons/help.png" alt="{tr}Help{/tr}" /></a>
			<br /><em>{tr}Enter the host name or IP for your database.{/tr}</em>
			<div style="display:none;" id="host_help">
				<p>{tr}Use <strong>localhost</strong> if the database is running on the same machine as Tiki.{/tr}</p>
			</div>
			</div>
		</div>
		<div style="padding:5px;">
			<label for="name">{tr}Database name:{/tr}</label>
			<div style="margin-left:1em;">
			<input type="text" id="name" name="name" size="40" value="{$smarty.request.name|escape:"html"}" /> <a href="javascript:void(0)" onclick="flip('name_help');" title="{tr}Help{/tr}"><img src="pics/icons/help.png" alt="{tr}Help{/tr}" /></a>
		
			<br /><em>{tr}Enter the name of the database that Tiki will use.{/tr}</em> 
			<div style="margin-left:1em;display:none;" id="name_help">
				<p>{tr}The database must already exist. You can create the database using mysqladmin, PHPMyAdmin, cPanel, or ask your hosting provider.  Normally Tiki tables won't conflict with other product names.{/tr}</p>
			</div>
			</div>
		</div>
		</fieldset><br />
		<fieldset><legend>{tr}Database user{/tr}</legend>
		<p>{tr}Enter a database user with administrator permission for the Database.{/tr}</p>
		<div style="padding:5px;">
			<label for="user">{tr}User name:{/tr}</label> <input type="text" id="user" name="user" value="{$smarty.request.user|escape:"html"}" />
		</div>
		<div style="padding:5px;">
			<label for="pass">{tr}Password:{/tr}</label> <input type="password" id="pass" name="pass" />
		</div>
		</fieldset>
		<input type="hidden" name="resetdb" value="y" />
		<fieldset>
			<legend>{tr}Character set{/tr}</legend>
			<p>{tr}Highly recommended for new installations. However, if you are upgrading or migrating a previous tiki database, you are recommended to uncheck this box{/tr}</p>
			<input type="checkbox" name="force_utf8" id="force_utf8" value="y" checked="checked"/>
			<label for="force_utf8">{tr}Always force connection to use UTF-8{/tr}</label>
		<p><a href="http://doc.tiki.org/Understanding+Encoding" onclick="window.open(this.href); return false;">{tr}More information{/tr}</a></p>
		</fieldset>
		<div align="center" style="margin-top:1em;"><input type="submit" name="dbinfo" value=" {tr}Continue{/tr} " /></div>	 
	</form>
  </div>
</div>

{elseif $install_step eq '4'}
<h1>{if $tikidb_created}{tr}Install &amp; Upgrade{/tr}{else}{tr}Install{/tr}{/if}</h1>
{if $max_exec_set_failed eq 'y'}
{remarksbox type="warning" title="{tr}Warning{/tr}"}
{tr}Failed to set max_execution_time for PHP. You may experience problems when creating/upgrading the database using this installer on a slow system. This can manifest itself by a blank page.{/tr}
{/remarksbox}
{/if}
<div class="clearfix">
<p>
{if $tikidb_created}
	{tr}This install will populate (or upgrade) the database.{/tr}<br /><br />
	{tr}If you want to upgrade from a previous Tiki release, ensure that you have read and understood the <a href="http://doc.tiki.org/Upgrade" target="_blank">Upgrade instructions</a>.{/tr}
{else}
	{tr}A new install will populate the database.{/tr}
{/if}
</p>
	  {if $database_charset neq 'utf8' and $tikidb_created}
	  	{remarksbox icon=error title="{tr}Encoding Issue{/tr}"}
			{tr 0=$database_charset}<p>Your database encoding is <strong>not</strong> in UTF-8.</p><p>Current encoding is <em>%0</em>. The languages that will be available for content on the site will be limited. If you plan on using languages not covered by the character set, you should re-create or alter the database so the default encoding is <em>utf8</em>.</p>{/tr}
			<p><a href="http://doc.tiki.org/Understanding+Encoding">{tr}More information{/tr}</a></p>

			<form method="post" action="">
				<fieldset>
					<legend>{tr}Character Set Conversion{/tr}</legend>
					<p>{tr}Use at your own risk. If the data in the database currently contains improperly converted data, this may make matters worse. Suitable for new installations. Requires ALTER privilege on the database.{/tr}</p>
					<p>
						<input type="submit" name="convert_to_utf8" value="{tr}Convert database and tables to UTF-8{/tr}"/>
						<input type="hidden" name="install_step" value="4"/>
					</p>
				</fieldset>
			</form>
		{/remarksbox}
	  {/if}
	  {if $dbdone eq 'n'}
		  {if $logged eq 'y'}
		    {* we are logged if no admin account is found or if the admin user is logged in*}
		    <form method="post" action="tiki-install.php">
		    	<input type="hidden" name="install_step" value="5" />
				{if $multi}<input type="hidden" name="multi" value="{$multi}" />{/if}
				{if $lang}<input type="hidden" name="lang" value="{$lang}" />{/if}
	  <br />
<table class="formcolor">
	<tr>
		<td valign="top">
			<fieldset><legend>{tr}Install{/tr}</legend>
				{if $tikidb_created}<p style="text-align:center"><img src="pics/icons/sticky.png" alt="{tr}Warning{/tr}" style="vertical-align:middle" /> <strong>{tr}Warning:{/tr}</strong> {tr}This will destroy your current database.{/tr}</p>{/if}
				{if $tikidb_created}
				<script type='text/javascript'><!--//--><![CDATA[//><!--
				{literal}
					function install() {
						document.getElementById('install-link').style.display='none';
						document.getElementById('install-table').style.visibility='';
					}
				{/literal}
				//--><!]]></script>
				<div id="install-link">
				<p style="text-align:center"><a style="color: #1174a5;" href="javascript:install()">{tr}Reinstall the database{/tr}</a></p>
				</div>
				<div id="install-table" style="visibility:hidden">
				{else}
				<div id="install-table">
				{/if}
				<p align="center">
					<input type="submit" name="scratch" value=" {if $tikidb_created}{tr}Reinstall{/tr}{else}{tr}Install{/tr}{/if} " style="margin: 32px;" />
				</p>

			</div>
			</fieldset>
		</td>
			{if $tikidb_created}
			<td width="50%" valign="top">
			<fieldset><legend>{tr}Upgrade{/tr}</legend>
			{if $tikidb_oldPerms gt 0}
				{remarksbox type="warning" title="{tr}Warning: Category Permissions Will Not Be Upgraded{/tr}" close="n"}
					{tr}Category permissions have been revamped since version 3. If you have been using category permissions, note that they may not work properly after upgrading to version 4 onwards, and it will be necessary to reconfigure them.{/tr}
				{/remarksbox}
			{/if}
			<p>{tr}Automatically upgrade your existing database to v{/tr}{$tiki_version_name}.</p>
			<p align="center"><input type="submit" name="update" value=" {tr}Upgrade{/tr} " /></p>
			</fieldset>
			</td>
			{/if}
		</tr></table>
		    </form>
 {else}
			{* we are not logged then no admin account found and user not logged *}
			<p><img src="pics/icons/delete.png" alt="{tr}Alert{/tr}" style="vertical-align:middle" />  <span style="font-weight:bold">{tr}This site has an admin account configured.{/tr}</span></p>
		   <p>{tr}Please log in with your admin password to continue.{/tr}</p>

     <form name="loginbox" action="tiki-install.php" method="post">
			<input type="hidden" name="login" value="admin" />
			{if $multi}<input type="hidden" name="multi" value="{$multi}" />{/if}
			{if $lang}<input type="hidden" name="lang" value="{$lang}" />{/if}
          <table>
          <tr><td class="module">{tr}User:{/tr}</td><td><input value="admin" disabled="disabled" size="20" /></td></tr>
          <tr><td class="module">{tr}Pass:{/tr}</td><td><input type="password" name="pass" size="20" /></td></tr>
          <tr><td colspan="2"><p align="center"><input type="submit" name="login" value="{tr}Log in{/tr}" /></p></td></tr>
          </table>
      </form>

		  {/if}
{/if}

</div>

{elseif $install_step eq '5' or ($dbdone ne 'n')}
<h1>{if isset($smarty.post.update)}{tr}Review the Upgrade{/tr}{else}{tr}Review the Installation{/tr}{/if}</h1>
		<div style="background: #c2eef8; border: 2px solid #2098cd; color:#000; padding: 4px;">
		<p style="text-align:center; font-size: large;">{if isset($smarty.post.update)}{tr}Upgrade complete{/tr}{else}{tr}Installation complete{/tr}{/if}.</p>
		<p>{tr}Your database has been configured and Tiki is ready to run!{/tr} 
      {if isset($smarty.post.scratch)}
        {tr}If this is your first install, your admin password is <strong>admin</strong>.{/tr}
      {/if} 
      {tr}You can now log in into Tiki as user <strong>admin</strong> and start configuring the application.{/tr}
		</p>
		</div>
{if $installer->success|@count gt 0}
	<p><img src="pics/icons/accept.png" alt="{tr}Success{/tr}" style="vertical-align:middle" /> <span style="font-weight:bold">
	{if isset($smarty.post.update)}
		{tr}Upgrade operations executed successfully:{/tr}
	{else}
		{tr}Installation operations executed successfully:{/tr}
	{/if}
	</span>
	{$installer->success|@count} {tr}SQL queries.{/tr}</p>
{else}
	<p><img src="pics/icons/accept.png" alt="{tr}Success{/tr}" style="vertical-align:middle" /> <span style="font-weight: bold">{tr}Database was left unchanged.{/tr}</span></p>
{/if}
<form action="tiki-install.php" method="post">
{if $installer->failures|@count > 0}
	<script type='text/javascript'><!--//--><![CDATA[//><!--
				{literal}
				function sql_failed() {
					document.getElementById('sql_failed_log').style.display='block';
				}
				{/literal}
	//--><!]]></script>

<p><img src="pics/icons/delete.png" alt="{tr}Failed{/tr}" style="vertical-align:middle" /> <strong>{tr}Operations failed:{/tr}</strong> {$installer->failures|@count} {tr}SQL queries.{/tr}
<a href="javascript:sql_failed()">{tr}Display details.{/tr}</a>


<div id="sql_failed_log" style="display:none">
 <p>{tr}During an upgrade, it is normal to have SQL failures resulting with <strong>Table already exists</strong> messages.{/tr}</p>
{assign var='patch' value=''} 
{foreach from=$installer->failures item=item}
{if $patch ne $item[2]}{if $patch ne ''}</textarea>{/if}<p><input type="checkbox" name="validPatches[]" value="{$item[2]|escape}" id="ignore_{$item[2]|escape}" /><label for="ignore_{$item[2]|escape}">{$item[2]|escape}</label></p>
<textarea rows="6" cols="80">{assign var='patch' value=$item[2]}{/if}
{$item[0]}
{$item[1]}

{/foreach}
</textarea>
<p>If you think that the errors of a patch can be ignored, please check the checkbox associated to it before clicking on continue.</p>

</div>
{/if}

{if isset($htaccess_error) and $htaccess_error eq 'y'}
<h3>{tr}.htaccess File{/tr} <a title="{tr}Help{/tr}" href="http://doc.tiki.org/Installation" target="help"><img style="border:0" src='pics/icons/help.png' alt="{tr}Help{/tr}" /></a></h3>
{tr}We recommend enabling the <strong>.htaccess</strong> file for your Tiki{/tr}. {tr}This will enable you to use SEFURLs (search engine friendly URLs) and help improve site security{/tr}. 
<p>{tr}To enable this file, simply rename the <strong>_htaccess</strong> file (located in the main directory of your Tiki installation) to <strong>.htaccess</strong>.{/tr}</p>
{/if}

<p>&nbsp;</p>
<div align="center">
	<input type="hidden" name="install_step" value="6" />
	<input type="hidden" name="install_type" value="{$install_type}" />
	<input type="submit" value=" {tr}Continue{/tr} " />
{if $multi}		<input type="hidden" name="multi" value="{$multi}" />{/if}
{if $lang}		<input type="hidden" name="lang" value="{$lang}" />{/if}
</div>
</form>


{elseif $install_step eq '6'}
<h1>{tr}Configure General Settings{/tr}</h1>
<form action="tiki-install.php" method="post">
<div style="float:left; width:60px"><img src="pics/large/icon-configuration48x48.png" alt="{tr}Configure General Settings{/tr}" /></div>
<div class="clearfix">
	<p>{tr}Complete these fields to configure common, general settings for your site.{/tr} {tr}The information you enter here can be changed later.{/tr}</p>
	<p>{tr}Refer to the <a href="http://doc.tiki.org/Admin+Panels" target="_blank">documentation</a> for complete information on these, and other, settings.{/tr}</p>
	<br />
	<fieldset><legend>{tr}General{/tr} <a href="http://doc.tiki.org/general+admin" target="_blank" title="{tr}Help{/tr}"><img src="pics/icons/help.png" alt="{tr}Help{/tr}" /></a></legend>
<div style="padding:5px; clear:both"><label for="browsertitle">{tr}Browser title:{/tr}</label>
		<div style="margin-left:1em"><input type="text" size="40" name="browsertitle" id="browsertitle" onclick="this.value='';" onfocus="origval=this.value;" onblur="if (this.value=='') this.value=origval;" value="{if $prefs.browsertitle eq ''}{tr}My Tiki{/tr}{else}{$prefs.browsertitle|escape}{/if}" />
			<br /><em>{tr}This will appear in the browser title bar.{/tr}</em></div>
		</div>
		<div style="padding:5px; clear:both"><label for="sender_email">{tr}Sender email:{/tr}</label>
			<div style="margin-left:1em"><input type="text" size="40" name="sender_email" id="sender_email" value="{$prefs.sender_email|escape}" />
			<br /><em>{tr}Email sent by your site will use this address.{/tr}</em>
			</div>
		</div>
	</fieldset>
<br />
<fieldset>
	<legend>{tr}Secure Log in{/tr} <a href="http://doc.tiki.org/login+config" target="_blank" title="{tr}Help{/tr}">
		<img src="pics/icons/help.png" alt="{tr}Help{/tr}" /></a>
	</legend>
	<div style="padding:5px; clear:both"><label for="https_login">{tr}HTTPS login:{/tr}</label>
		<select name="https_login" id="https_login" onchange="hidedisabled('httpsoptions',this.value);">
			<option value="disabled"{if $prefs.https_login eq 'disabled'} selected="selected"{/if}>{tr}Disabled{/tr}</option>
			<option value="allowed"{if $prefs.https_login eq 'allowed'} selected="selected"{/if}>{tr}Allow secure (https) login{/tr}</option>
			<option value="encouraged"{if $prefs.https_login eq 'encouraged' or ($prefs.https_login eq '' and $detected_https eq 'on' ) } selected="selected"{/if}>{tr}Encourage secure (https) login{/tr}</option>
			<option value="force_nocheck"{if $prefs.https_login eq 'force_nocheck'} selected="selected"{/if}>{tr}Consider we are always in HTTPS, but do not check{/tr}</option>
			<option value="required"{if $prefs.https_login eq 'required'} selected="selected"{/if}>{tr}Require secure (https) login{/tr}</option>
		</select>
	</div>
	<div id="httpsoptions" style="display:{if $prefs.https_login eq 'disabled' or ( $prefs.https_login eq '' and $detected_https eq '') }none{else}block{/if};">
		<div style="padding:5px">
			<label for="https_port">{tr}HTTPS port:{/tr}</label> <input type="text" name="https_port" id="https_port" size="5" value="{$prefs.https_port|escape}" />
		</div>
		<div style="padding:5px;clear:both">
			<div style="float:left"><input type="checkbox" id="feature_show_stay_in_ssl_mode" name="feature_show_stay_in_ssl_mode" {if $prefs.feature_show_stay_in_ssl_mode eq 'y'}checked="checked"{/if}/></div>
			<div style="margin-left:20px;"><label for="feature_show_stay_in_ssl_mode"> {tr}Users can choose to stay in SSL mode after an HTTPS login.{/tr}</label></div>
		</div>
		<div style="padding:5px;clear:both">
			<div style="float:left"><input type="checkbox" id="feature_switch_ssl_mode" name="feature_switch_ssl_mode" {if $prefs.feature_switch_ssl_mode eq 'y'}checked="checked"{/if}/></div>
			<div style="margin-left:20px;"><label for="feature_switch_ssl_mode">{tr}Users can switch between secured or standard mode at login.{/tr}</label></div>
		</div>
	</div>
</fieldset>
<br />
<fieldset>
	<legend>{tr}Logging and Reporting{/tr}</legend>
	<div class="adminoptionbox">
		<label for="general-error">{tr}PHP error reporting level:{/tr}</label> 
		<select name="error_reporting_level" id="general-error">
			<option value="0" {if $prefs.error_reporting_level eq 0}selected="selected"{/if}>{tr}No error reporting{/tr}</option>
			<option value="2047" {if $prefs.error_reporting_level eq 2047}selected="selected"{/if}>{tr}Report all PHP errors except strict{/tr}</option>
			<option value="-1" {if $prefs.error_reporting_level eq -1}selected="selected"{/if}>{tr}Report all PHP errors{/tr}</option>
			<option value="2039" {if $prefs.error_reporting_level eq 2039}selected="selected"{/if}>{tr}Report all errors except notices{/tr}</option>
			<option value="1" {if $prefs.error_reporting_level eq 1039}selected="selected"{/if}>{tr}According to PHP configuration{/tr}</option>
		</select>
		<div style="padding:5px;clear:both">
			<div style="padding:5px;clear:both">
				<label for="error_reporting_adminonly">{tr}Visible to Admin only{/tr}.</label>
				<input type="checkbox" id="error_reporting_adminonly" name="error_reporting_adminonly"{if $prefs.error_reporting_adminonly eq 'y'} checked="checked"{/if} />
			</div>
			<div style="padding:5px;clear:both">
				<label for="smarty_notice_reporting">{tr}Include Smarty notices{/tr}</label>.
				<input type="checkbox" id="smarty_notice_reporting" name="smarty_notice_reporting"{if $prefs.smarty_notice_reporting eq 'y'} checked="checked"{/if} />
			</div>
			<div style="padding:5px;clear:both">	  
				<label for="log_tpl">{tr}Add HTML comment at start and end of each Smarty template (TPL){/tr}.</label>
				<input type="checkbox" id="log_tpl" name="log_tpl"{if $prefs.log_tpl eq 'y'} checked="checked"{/if}" />
			</div>
		</div>
	</div>
</fieldset>
<br />
	<fieldset><legend>{tr}Administrator{/tr}</legend>
		<div style="padding:5px"><label for="admin_email">{tr}Admin email:{/tr}</label>
			<div style="margin-left:1em"><input type="text" size="40" name="admin_email" id="admin_email" value="{if isset($admin_email)}{$admin_email}{/if}" />
			<br /><em>{tr}This is the email address for your administrator account.{/tr}</em></div>
		</div>
	</fieldset>
	{if $upgradefix eq 'y'}
		<fieldset>
			<legend>{icon _id=error} {tr}Upgrade fix{/tr}</legend>
			<p>{tr}Experiencing problems with the upgrade? Your administrator account lost its privileges? This may occur if you upgraded from a very old version of Tiki.</p>
			<p>We can fix it! Doing so will:{/tr}</p>
			<ol>
				<li>{tr}Create the <em>Admins</em> group, if missing{/tr}</li>
				<li>{tr}Grant <em>tiki_p_admin</em> to the group, if missing{/tr}</li>
				<li>{tr}Add the administrator account to the group, if missing{/tr}</li>
			</ol>
			<p><strong>{tr}To do so enter the name of the main admin account in the field below{/tr}</strong></p>
			<p>Administrator account (optional): <input type="text" name="admin_account"/> <em>The default account is <strong>admin</strong></em></p>
		</fieldset>
	{/if}
</div>

<div align="center" style="margin-top:1em;">
{if $multi}		<input type="hidden" name="multi" value="{$multi}" />{/if}
{if $lang}		<input type="hidden" name="lang" value="{$lang}" />{/if}
	<input type="hidden" name="install_step" value="7" />
	<input type="hidden" name="install_type" value="{$install_type}" />
	<input type="hidden" name="general_settings" value="y" />
	<input type="submit" value=" {tr}Continue{/tr} " />
</div>
</form>

{elseif $install_step eq '7'}
<h1>{tr}Enter Your Tiki{/tr}</h1>
<div style="float:left; width:60px"><img src="pics/large/stock_quit48x48.png" alt="{tr}Log in{/tr}" /></div>
<div class="clearfix">
	<p>{tr}The installation is complete!{/tr} {tr}Your database has been configured and Tiki is ready to run.{/tr} </p>
	<p>{tr}Tiki is an open source project, <em>you</em> can <a href='http://info.tiki.org/Join+the+Community' target='_blank'>join the community</a> and help <a href='http://info.tiki.org/Develop+Tiki' target='_blank'>develop Tiki</a>.{/tr} </p>
	<p>
{if isset($smarty.post.scratch)}	{tr}If this is your first install, your admin password is <strong>admin</strong>.{/tr} 
{/if} 
	{tr}You can now log in into Tiki as user <strong>admin</strong> and start configuring the application.{/tr}
	</p>

{if isset($smarty.post.scratch)}
	<h3><img src="pics/icons/information.png" alt="{tr}Note{/tr}" style="vertical-align:middle" /> {tr}Installation{/tr}</h3>
	<p>{tr}If this is a first time installation, go to <strong>tiki-admin.php</strong> after login to start configuring your new Tiki installation.{/tr}</p>
{/if}

{if isset($smarty.post.update)}
	<h3><img src="pics/icons/information.png" alt="{tr}Note{/tr}" style="vertical-align:middle" /> {tr}Upgrade{/tr}</h3>
	<p>{tr}If this is an upgrade, clean the Tiki caches manually (the <strong>templates_c</strong> directory) or by using the <strong>Admin &gt; System</strong> option from the Admin menu.{/tr}</p>
{/if}

{if $tikidb_is20}
		<span class="button"><a href="tiki-install.php?lockenter&amp;{if $multi}multi={$multi|escape}&amp;{/if}install_type={$install_type}">{tr}Enter Tiki and Lock Installer{/tr} ({tr}Recommended{/tr})</a></span>
		<span class="button"><a href="tiki-install.php?nolockenter&amp;{if $multi}multi={$multi|escape}&amp;{/if}install_type={$install_type}">{tr}Enter Tiki Without Locking Installer{/tr}</a></span>
{/if}

</div>
{if $install_type eq 'update'}
	{if $double_encode_fix_attempted eq 'y'}
		<p>{tr}You can now access the site normally. Report back any issues that you might find (if any) to the Tiki forums or bug tracker{/tr}</p>
	{else}
		<form method="post" action="#" onsubmit="return confirm('{tr}Are you sure you want to attempt to fix the encoding of your entire database?{/tr}');" style="padding-top: 100px;">
			<fieldset>
				<legend>{tr}Upgrading and running into encoding issues?{/tr}</legend>
				<p>{tr}We can try to fix it, but <strong>make sure you have backups, and can restore them</strong>.{/tr}</p>
				{if $client_charset_in_file eq 'utf8'}
					<p>
						{tr}Previous table encoding{/tr}:
						<select name="previous_encoding" id="previous_encoding">
							<option value="">{tr}Please select{/tr}</option>
							<option value="armscii8" title="Armenian, Binary">armscii8</option>
							<option value="ascii" title="West European (multilingual), Binary">ascii</option>
							<option value="big5" title="Traditional Chinese, Binary">big5</option>
							<option value="binary" title="Binary">binary</option>
							<option value="cp1250" title="Central European (multilingual), Binary">cp1250</option>
							<option value="cp1251" title="Cyrillic (multilingual), Binary">cp1251</option>
							<option value="cp1256" title="Arabic, Binary">cp1256</option>
							<option value="cp1257" title="Baltic (multilingual), Binary">cp1257</option>
							<option value="cp850" title="West European (multilingual), Binary">cp850</option>
							<option value="cp852" title="Central European (multilingual), Binary">cp852</option>
							<option value="cp866" title="Russian, Binary">cp866</option>
							<option value="cp932" title="Japanese, Binary">cp932</option>
							<option value="dec8" title="West European (multilingual), Binary">dec8</option>
							<option value="eucjpms" title="Japanese, Binary">eucjpms</option>
							<option value="euckr" title="Korean, Binary">euckr</option>
							<option value="gb2312" title="Simplified Chinese, Binary">gb2312</option>
							<option value="gbk" title="Simplified Chinese, Binary">gbk</option>
							<option value="geostd8" title="Georgian, Binary">geostd8</option>
							<option value="greek" title="Greek, Binary">greek</option>
							<option value="hebrew" title="Hebrew, Binary">hebrew</option>
							<option value="hp8" title="West European (multilingual), Binary">hp8</option>
							<option value="keybcs2" title="Czech-Slovak, Binary">keybcs2</option>
							<option value="koi8r" title="Russian, Binary">koi8r</option>
							<option value="koi8u" title="Ukrainian, Binary">koi8u</option>
							<option value="latin1" title="West European (multilingual), Binary">latin1</option>
							<option value="latin2" title="Central European (multilingual), Binary">latin2</option>
							<option value="latin5" title="Turkish, Binary">latin5</option>
							<option value="latin7" title="Baltic (multilingual), Binary">latin7</option>
							<option value="macce" title="Central European (multilingual), Binary">macce</option>
							<option value="macroman" title="West European (multilingual), Binary">macroman</option>
							<option value="sjis" title="Japanese, Binary">sjis</option>
							<option value="swe7" title="Swedish, Binary">swe7</option>
							<option value="tis620" title="Thai, Binary">tis620</option>
							<option value="ucs2" title="Unicode (multilingual), Binary">ucs2</option>
							<option value="ujis" title="Japanese, Binary">ujis</option>
						</select>
						<input type="submit" name="fix_double_encoding" value="{tr}Dangerous: Fix double encoding{/tr}"/>
						<input type="hidden" name="install_step" value="7"/>
					</p>
				{else}
					<p>{tr}Oops. You need to make sure client charset is forced to UTF-8. Reset the database connection to continue.{/tr}</p>
				{/if}
			</fieldset>
		</form>
	{/if}
{/if}
{/if}{* end elseif $install_step... *}
</div>
			</div>
				</div>
<div id="col2">
	<div class="content">
{if $virt}
		<div class="box-shadow">
			<div class="box">
				<h3 class="box-title">{tr}MultiTiki Setup{/tr} <a title="{tr}Help{/tr}" href="http://doc.tiki.org/MultiTiki" target="help"><img style="border:0" src="pics/icons/help.png" alt="{tr}Help{/tr}" /></a></h3>
				<div class="clearfix box-data">
				<div><a href="tiki-install.php">{tr}Default Installation{/tr}</a></div>
{foreach key=k item=i from=$virt}
				<div>
					<tt>{if $i eq 'y'}<strong style="color:#00CC00">{tr}DB OK{/tr}</strong>{else}<strong style="color:#CC0000">{tr}No DB{/tr}</strong>{/if}</tt>
{if $k eq $multi}
					<strong>{$k}</strong>
{else}
					<a href="tiki-install.php?multi={$k}" class="linkmodule">{$k}</a>
{/if}
				</div>
{/foreach}

<br />
<div><strong>{tr}Adding a new host:{/tr}</strong></div>
{tr}To add a new virtual host run the setup.sh with the domain name of the new host as a last parameter.{/tr}

{if $multi} <h2> ({tr}MultiTiki{/tr}) {$multi|default:"{tr}Default{/tr}"} </h2> {/if}

	
				</div>
			</div>
		</div>
{/if}

{if $dbcon eq 'y' and ($install_step eq '0' or !$install_step)}
		<div class="box-shadow">
			<div class="box">
				<h3 class="box-title"><img src="pics/icons/information.png" alt="{tr}Information{/tr}" style="vertical-align:middle" /> {tr}Upgrade{/tr}</h3>
				<div class="clearfix box-data">
				{tr}Are you upgrading an existing Tiki site?{/tr}
				{tr}Go directly to the <strong>Install/Upgrade</strong> step.{/tr}
				{if $dbcon eq 'y' or isset($smarty.post.scratch) or isset($smarty.post.update)}
					<a href="tiki-install.php?install_step=4{if $multi}&amp;multi={$multi}{/if}{if $lang}&amp;lang={$lang}{/if}" title="{tr}Install/Upgrade{/tr}">
							<img src="pics/icons/arrow_right.png" alt="{tr}Install/Upgrade{/tr}" height="16" width="16" />
					</a>
				{/if}
				</div>
			</div>
		</div>

	
{/if}	



		<div class="box-shadow">
			<div class="box">
				<h3 class="box-title">{tr}Installation{/tr}</h3>
				<div class="clearfix box-data">
				<ol>
					<li>{if $install_step eq '0'}<strong>{else}<a href="tiki-install.php?reset=y{if $multi}&amp;multi={$multi}{/if}{if $lang}&amp;lang={$lang}{/if}" title="{tr}Welcome{/tr} / {tr}Restart the installer.{/tr}">{/if}{tr}Welcome{/tr}{if $install_step eq '0'}</strong>{else}</a>{/if}</li>
					<li>{if $install_step eq '1'}<strong>{else}<a href="tiki-install.php?install_step=1{if $multi}&amp;multi={$multi}{/if}{if $lang}&amp;lang={$lang}{/if}" title="{tr}Read the License{/tr}">{/if}{tr}Read the License{/tr}{if $install_step eq '1'}</strong>{else}</a>{/if}</li>
					<li>{if $install_step eq '2'}<strong>{elseif $install_step ge '3' or $dbcon eq 'y'}<a href="tiki-install.php?install_step=2{if $multi}&amp;multi={$multi}{/if}{if $lang}&amp;lang={$lang}{/if}" title="{tr}Review the System Requirements{/tr}">{/if}{tr}Review the System Requirements{/tr}{if $install_step eq '2'}</strong>{elseif $install_step ge '3' or $dbcon eq 'y'}</a>{/if}</li>
					<li>{if $install_step eq '3'}<strong>{elseif $dbcon eq 'y'}<a href="tiki-install.php?install_step=3{if $multi}&amp;multi={$multi}{/if}{if $lang}&amp;lang={$lang}{/if}" title="{tr}Database Connection{/tr}">{/if}{if $dbcon eq 'y'}{tr}Reset the Database Connection{/tr}{else}{tr}Database Connection{/tr}{/if}{if $install_step eq '3'}</strong>{elseif $dbcon eq 'y'}</a>{/if}</li>
					<li>{if $install_step eq '4'}<strong>{elseif $dbcon eq 'y' or isset($smarty.post.scratch) or isset($smarty.post.update)}<a href="tiki-install.php?install_step=4{if $multi}&amp;multi={$multi}{/if}{if $lang}&amp;lang={$lang}{/if}" title="{if $tikidb_created}{tr}Install/Upgrade{/tr}{else}{tr}Install{/tr}{/if}">{/if}{if $tikidb_created}<em>{tr}Install/Upgrade{/tr}</em>{else}{tr}Install{/tr}{/if}{if $install_step eq '4'}</strong>{elseif ($dbcon eq 'y') or (isset($smarty.post.scratch)) or (isset($smarty.post.update))}</a>{/if}</li>
					<li>{if $install_step eq '5'}<strong>{elseif $tikidb_is20}<a href="tiki-install.php?install_step=5{if $multi}&amp;multi={$multi}{/if}{if $lang}&amp;lang={$lang}{/if}" title="{if isset($smarty.post.update)}{tr}Review the Upgrade{/tr}{else}{tr}Review the Installation{/tr}{/if}">{/if}{if isset($smarty.post.update)}{tr}Review the Upgrade{/tr}{else}{tr}Review the Installation{/tr}{/if}{if $install_step eq '5'}</strong>{elseif $tikidb_is20}</a>{/if}</li>
					<li>{if $install_step eq '6'}<strong>{elseif $tikidb_is20 and !isset($smarty.post.update)}<a href="tiki-install.php?install_step=6{if $multi}&amp;multi={$multi}{/if}{if $lang}&amp;lang={$lang}{/if}" title="{tr}Configure the General Settings{/tr}">{/if}{tr}Configure the General Settings{/tr}{if $install_step eq '6'}</strong>{elseif $tikidb_is20 and !isset($smarty.post.update)}</a>{/if}</li>
					<li>{if $install_step eq '7'}<strong>{elseif $tikidb_is20}<a href="tiki-install.php?install_step=7{if $multi}&amp;multi={$multi}{/if}{if $lang}&amp;lang={$lang}{/if}" title="{tr}Enter Your Tiki{/tr}">{/if}{tr}Enter Your Tiki{/tr}{if $install_step eq '7'}</strong>{elseif $tikidb_is20}</a>{/if}</li>
				</ol>
				</div>
			</div>
		</div>
		<div class="box-shadow">
			<div class="box">
				<h3 class="box-title">{tr}Help{/tr}</h3>
				<div class="clearfix box-data">
				<p><img src="favicon.png" alt="{tr}Tiki Icon{/tr}" style="vertical-align:middle" /> <a href="http://tiki.org" target="_blank">{tr}Tiki Project Web Site{/tr}</a></p>
				<p><img src="pics/icons/book_open.png" alt="{tr}Documentation{/tr}" style="vertical-align:middle" /> <a href="http://doc.tiki.org" target="_blank">{tr}Documentation{/tr}</a></p>
				<p><img src="pics/icons/group.png" alt="{tr}Forums{/tr}" style="vertical-align:middle" /> <a href="http://tiki.org/forums" target="_blank">{tr}Support Forums{/tr}</a></p>
				</div>
			</div>
		</div>
	</div>
</div>			
			
	  	</div>
</div>
<hr />

<p align="center"><a href="http://tiki.org" target="_blank" title="{tr}Powered by{/tr} {tr}Tiki Wiki CMS Groupware{/tr} &#169; 2002&#8211;{$smarty.now|date_format:"%Y"} "><img src="img/tiki/tikibutton.png" alt="{tr}Powered by Tiki Wiki CMS Groupware{/tr}" style="width:88px; height:31px; border:0" /></a></p>

		</div>{* -- END of main -- *}
	</div> {* -- END of fixedwidth -- *}
