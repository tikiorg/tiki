<div style="margin:10px 30px;">


<h1>{tr}Tiki installer{/tr} v{$tiki_version_name} <a title='help' href='http://doc.tikiwiki.org/Installation' target="help"><img border='0' src='img/icons/help.gif' alt="{tr}Help{/tr}" /></a></h1>
<hr />
{if $tikifeedback}
<br />{section name=n loop=$tikifeedback}
<div class="simplebox {if $tikifeedback[n].num > 0} highlight{/if}" style="margin:10px 0px 10px 0px">
<img src="pics/icons/{if $tikifeedback[n].num > 0}delete.png" alt="{tr}Error{/tr}"{else}accept.png" alt="{tr}Success{/tr}"{/if} style="vertical-align:middle"/> {$tikifeedback[n].mes}
</div>
{/section}
{/if}

{* multitiki ----------------------------- *}
{if $virt}
<table><tr><td width="180">
<div class="box">
<div class="box-title">
<a title='{tr}Help{/tr}' href='http://doc.tikiwiki.org/MultiTiki' target="help"><img border='0' src='img/icons/help.gif' alt="{tr}Help{/tr}" /></a>
{tr}MultiTiki setup{/tr}</div>
<div class="box-data">
<div><a href="tiki-install.php">default</a></div><br />
{foreach key=k item=i from=$virt}
<div>
<tt>{if $i eq 'y'}<b style="color:#00CC00;">DBok</b>{else}<b style="color:#CC0000;">NoDB</b>{/if}</tt>
{if $k eq $multi}
<b>{$k}</b>
{else}
<a href="tiki-install.php?multi={$k}" class='linkmodule'>{$k}</a>
{/if}
</div>
{/foreach}
</div></div>

<div class="box">
<div class="box-title">{tr}To add a new virtual host{/tr}</div>
<div class="box-data">
{tr}To add a new virtual host run the setup.sh with the domain name of the new host as a last parameter{/tr}.
</div>
</td>
<td valign="top">
{/if}

{if $multi} <h2> ({tr}MultiTiki{/tr}) {$multi|default:"default"} </h2> {/if}
{* / multitiki --------------------------- *}


{* we do not have a valid db connection or db reset is requested *}

{if $dbcon eq 'n' or $resetdb eq 'y'}
<p><img src="pics/icons/delete.png" alt="alert" style="vertical-align:middle" /> <strong>{tr}Tiki cannot find a database connection{/tr}</strong></p>
<p>{tr}Please enter your database connection information{/tr}:</p>
<form action="tiki-install.php" method="post">
{if $multi}<input type="hidden" name="multi" value="{$multi}" />{/if}
{if $lang}<input type="hidden" name="lang" value="{$lang}" />{/if}
<table class="normal" cellpadding="5">
 <tr class="formcolor">
  <td>{tr}Database type{/tr}:<br />
<select name="db">
{foreach key=dsn item=dbname from=$dbservers}
<option value="{$dsn}">{$dbname}</option>
{/foreach}
</select>
  </td>
<td>{tr}Select the type of database to use with Tiki.{/tr}
<p>{tr}Only databases supported by your PHP installation are listed here. If your database is not in the list, try to install the appropriate PHP extension.{/tr}</p>
</td>
</tr>

<tr class="formcolor">
 <td>{tr}Host:{/tr}<br />
<input type="text" name="host" value="localhost" />
</td><td>
{tr}Enter the hostname or IP for your database. Use <strong>localhost</strong> if the database is running on the same machine as Tiki{/tr}.<br />
<p>{tr}For SQLite, enter the path and filename to your database file{/tr}.</p>
</td>
</tr>

<tr class="formcolor">
<td>{tr}Database name{/tr}:<br />
<input type="text" name="name" />
</td><td>
{tr}Enter the name of the database that Tiki will use.{/tr} {tr}The database must already exist. You can create the database using mysqladmin, PHPMyAdmin, cPanel, or ask your
hosting provider.  Normally Tiki tables won't conflict with other product names{/tr}.<br />
<p>{tr}For Oracle{/tr}:
  <ul>
   <li>{tr}Enter your TNS Name here and leave Host empty{/tr}.<br />
   {tr}or{/tr}</li>
   <li>{tr}Override tnsnames.ora and put your SID here and enter your hostname:port in the Host field{/tr}.</li>
  </ul></p>
</td>
</tr>

<tr class="formcolor">
<td>{tr}Database User{/tr}:<br />
<input type="text" name="user" />
</td><td>{tr}Enter the database user with administrator permission for the Database{/tr}.
</td>
</tr>

<tr class="formcolor">
<td>{tr}Password{/tr}:<br />
<input type="password" name="pass" />
</td><td>
{tr}Enter the password for the database user{/tr}.
</td>
</tr>


			
<tr class="formcolor">
<td colspan="2"><p align="center"><input type="hidden" name="resetdb" value="{$resetdb}" />
<input type="submit" name="dbinfo" value=" {tr}Connect{/tr} " /></p></td>
</tr>
	  	
	  </table>
	  </form>
<p>&nbsp;</p>
	{else}
	  {* we do have a database connection *}
	  {if $dbdone eq 'n'}
		  {if $logged eq 'y'}
		    {* we are logged if no admin account is found or if the admin user is logged in*}
			<br />
		    <h2>{tr}Welcome to the installation{if $tikidb_created} &amp; upgrade{/if} script!{/tr}</h2>

		    <form method="post" action="tiki-install.php">
				{if $multi}<input type="hidden" name="multi" value="{$multi}" />{/if}
				{if $lang}<input type="hidden" name="lang" value="{$lang}" />{/if}
	  <br />
		<table><tr><td width="50%">
			<fieldset>
			{if $tikidb_created}
			<script type="text/javascript">
			<!--//--><![CDATA[//><!--
				{literal}
				function install() {
					document.getElementById('install-link').style.display='none';
					document.getElementById('install-table').style.visibility='';
				}
				{/literal}
			//--><!]]>
			</script>
			<div id="install-link">
			<p><a href="javascript:install()">{tr}Reinstall database.{/tr}</a></p>
			<p align="center"><img src="img/silk/sticky.png" alt="warning" style="vertical-align:middle"/> <strong>{tr}Warning{/tr}</strong>: {tr}This will destroy your current database{/tr}.</p>
			</div>
		    <table id="install-table" style="visibility:hidden">
			{else}
		    <table id="install-table">
			{/if}
		    <tr>
		     <td><h2>{tr}Install{/tr}</h2></td>
		    </tr>
			 <tr>
			  <td>{if $tikidb_created}<p align="center"><img src="img/silk/sticky.png" alt="warning" style="vertical-align:middle"/> <strong>{tr}Warning{/tr}</strong>: {tr}This will destroy your current database{/tr}.</p>{/if}			  
			  <p>{tr}Create a new database (clean install) with profile{/tr}:<br />
			<select name="profile" size="{if $profiles}{$profiles|@count}{else}3{/if}">
			{section name=ix loop=$profiles}
			<option value="{$profiles[ix].name|escape}"{if $profiles[ix].name|escape eq '_default.prf'} selected="selected"{/if}>{$profiles[ix].desc}  </option>
			{sectionelse}
			<option vlaue="" disabled="disabled">{tr}No profiles available.{/tr}</option>
			{/section}
			</select></p>
			 <p>{tr}See the documentation for <a target="_blank" href="http://doc.tikiwiki.org/Profiles" class="link" title="{tr}Description of available profiles.{/tr}">descriptions of the available profiles{/tr}</a>.</p>
		    </td>
			</tr>
			<tr>
				<td style="text-align:center">
					<input type="submit" name="scratch" value=" {tr}Install{/tr} " />
				</td>
			</tr>
			</table>
			</fieldset>
		</td><td>
			{if $tikidb_created}
			<fieldset>
		    <table>
			<tr><td><h2>{tr}Upgrade{/tr}</h2></td></tr>
			<tr><td><p align="center"><img src="img/silk/sticky.png" alt="warning" style="vertical-align:middle"/> <strong>{tr}Important{/tr}</strong>: {tr}Backup your database with mysqldump, phpmyadmin, or other before upgrading.{/tr}</p>
			<p>{tr}Update database using script{/tr}: <br />
			<select name="file" size="{if $files}{$files|@count}{else}3{/if}">
			{section name=ix loop=$files}
			<option value="{$files[ix]|escape}">{$files[ix]}&nbsp;</option>
{sectionelse}
			<option value="" disabled="disabled">{tr}No scripts available.{/tr}</option>
			{/section}
			</select></p>
			<p align="center"><input type="submit" name="update" value="{tr}Upgrade{/tr}" /></p>
		    </td></tr>
		    <tr><td>
<table class="normal" cellpadding="5">
	<tr><th>{tr}To upgrade from{/tr}:</th><th>{tr}Use this script{/tr}:</th></tr>
	<tr class="odd">
		<td>2.0.x</td>
		<td>tiki_1.9to2.0</td>
	</tr>
	<tr class="even">
		<td>1.9.x or<br/>1.8.x</td>
		<td>tiki_1.8to1.9<br />{tr}Then rerun the installer using tiki_1.9to2.0.{/tr}</td>
	</tr>
	<tr class="odd">
		<td>1.7.x</td>
		<td>{tr}See <a target="help" class="link" href="http://doc.tikiwiki.org/Upgrade+1.7+to+1.8">Tiki database 1.7.x to 1.8x instructions{/tr}</a>.</td>
	</tr>
</table>
			<p>{tr}For information about <strong>tiki-secdb_*.sql</strong> files, please see <a target="help" class="link" href="http://doc.tikiwiki.org/Security+Admin">http://doc.tikiwiki.org/Security+Admin{/tr}</a>.

			
		</td></tr>		
    </table>
			</fieldset>
			{/if}
		</td></tr></table>
		    </form><br />
<br />
<h2>{tr}Other Options{/tr}</h2>
<ul>
	{if $tikidb_is20}
	<li><a href="tiki-index.php" class="link">{tr}Do nothing and enter Tiki{/tr}</a>.</li>
	{/if}
	<li><a href="tiki-install.php?reset=yes{if $lang}&amp;lang={$lang}{/if}" class="link">{tr}Reset database connection settings{/tr}</a>.</li>
</ul>
<p>&nbsp;</p>
		  {else}
			{* we are not logged then no admin account found and user not logged*}
			<p><img src="pics/icons/delete.png" alt="alert" style="vertical-align:middle" />  <strong>{tr}This site has an admin account configured{/tr}</strong>.</p>
		   <p>{tr}Please login with your admin password to continue{/tr}.</p>

     <form name="loginbox" action="tiki-install.php" method="post">
			<input type="hidden" name="login" value="admin" />
			{if $multi}<input type="hidden" name="multi" value="{$multi}" />{/if}
			{if $lang}<input type="hidden" name="lang" value="{$lang}" />{/if}
          <table>
          <tr><td class="module">{tr}User{/tr}:</td><td><input value="admin" disabled="disabled" size="20" /></td></tr>
          <tr><td class="module">{tr}Pass{/tr}:</td><td><input type="password" name="pass" size="20" /></td></tr>
          <tr><td colspan="2"><p align="center"><input type="submit" name="login" value="{tr}Login{/tr}" /></p></td></tr>
          </table>
      </form>

		  {/if}
    	{else}

		<div style="margin:10px 0px 5px 0px;border-style: solid; border-width: 1; padding: 5px; background-color: #a9ff9b;">
		<p align="center" style="font-size: large;">{if isset($smarty.post.update)}{tr}Upgrade{/tr}{else}{tr}Installation{/tr}{/if} {tr}complete{/tr}.</p>
		<p>{tr}Your database has been configured and Tikiwiki is ready to run!{/tr} 
      {if isset($smarty.post.scratch)}
        {tr}If this is your first install, your admin password is <strong>admin</strong>.{/tr}
      {/if} 
      {tr}You can now log in into Tikiwiki as user <strong>admin</strong> and start configuring the application.{/tr}
		</p>
		</div>
    	
<p><img src="pics/icons/accept.png" alt="{tr}Success{/tr}" style="vertical-align:middle"/> <strong>{if isset($smarty.post.update)}{tr}Upgrade{/tr}{else}{tr}Installation{/tr}{/if} {tr}operations executed successfully{/tr}</strong>: {$succcommands|@count} {tr}SQL queries{/tr}.</p>
{if $failedcommands|@count > 0}
			<script type="text/javascript">
			<!--//--><![CDATA[//><!--
				{literal}
				function sql_failed() {
					document.getElementById('sql_failed_log').style.display='block';
				}
				{/literal}
			//--><!]]>
			</script>

<p><img src="pics/icons/delete.png" alt="{tr}Failed{/tr}" style="vertical-align:middle"/> <strong>{tr}Operations failed{/tr}:</strong> {$failedcommands|@count} {tr}SQL queries{/tr}. 
<a href="javascript:sql_failed()">{tr}Display details{/tr}</a>.

<div id="sql_failed_log" style="display:none">
 <p>{tr}During an upgrade, it is normal to have SQL failures resulting with <strong>Table already exists</strong> messages.{/tr}</p>
    		<textarea rows="15" cols="80">
{section loop=$failedcommands name=ix}
{$failedcommands[ix]}
{/section}
    		</textarea>

</div>


{/if}
<p>&nbsp;</p>
<h2>{tr}Important Information{/tr}</h2>
{tr}Please read the following notes before entering Tikiwiki.{/tr}
<p>&nbsp;</p>
<h3><img src="pics/icons/information.png" alt="{tr}Note{/tr}" style="vertical-align:middle"/> {tr}Memory{/tr}</h3>
{tr}TikiWiki requires <strong>at least</strong> 16MB of PHP memory for script execution. Use the <strong>memory_limit</strong> key in your <strong>php.ini </strong> file (for example: memory_limit = 16M) and restart your webserver{/tr}.
<p>{tr}Allocating too little memory will cause TikiWiki to display blank pages{/tr}.</p>
{if $php_memory_limit <= 0}
	<div style="border-style: solid; border-width: 1; padding: 5px; background-color: #a9ff9b;">
		<p align="center"><img src="pics/icons/accept.png" alt="{tr}Success{/tr}" style="vertical-align:middle"/> {tr}Tiki has not detected your PHP memory_limit. This probably means you have no set limit (all is well){/tr}. </p>
	</div>	
{elseif $php_memory_limit <= 8 * 1024 * 1024}
	<div style="border-style: solid; border-width: 1; padding: 5px; background-color: #FF0000">
		<p align="center"><img src="pics/icons/delete.png" alt="alert" style="vertical-align:middle" /> {tr}Tiki has detected your PHP memory limit at{/tr}: {$php_memory_limit|kbsize:true:0}</p>
	</div>
{else}
	<div style="border-style: solid; border-width: 1; padding: 4">
	  <p align="center">
		  <span style="font-size: large; padding: 4px;">
		  {tr}Tiki has detected your PHP memory_limit at{/tr}: {$php_memory_limit|kbsize:true:0}. 
		  </span>
		</div>	
	{/if}			
<p>&nbsp;</p>

{if isset($smarty.post.scratch)}
<h3><img src="pics/icons/information.png" alt="{tr}Note{/tr}" style="vertical-align:middle"/> {tr}Installation{/tr}</h3>
{tr}If this is a first time installation, go to <strong>tiki-admin.php</strong> after login to start configuring your new Tiki installation{/tr}.
{/if}

{if isset($smarty.post.update)}
<h3><img src="pics/icons/information.png" alt="{tr}Note{/tr}" style="vertical-align:middle"/> {tr}Upgrade{/tr}</h3>
{tr}If this is an upgrade, clean the Tiki caches manually (the <strong>templates_c</strong> directory) or by using the <strong>Admin &gt; System</strong> option from the Admin menu.{/tr}
{/if}

<p>&nbsp;</p>
<h2>{tr}Entering TikiWiki{/tr}</h2>
<ul>
 <li><a href="tiki-change_password.php?user=admin" class="link">{tr}Change the administrator password{/tr}</a>. {tr}Old password{/tr}: <em>admin</em></li>
</ul>
<p>&nbsp;</p>
<h3>{tr}Continue in installer{/tr}</h3>
<ul>
 <li><a href="tiki-install.php{if $multi}?multi={$multi}{/if}{if $lang}{if $multi}&amp;{else}?{/if}lang={$lang}{/if}" class="link">{tr}Go back and run another install/upgrade script{/tr}</a>. {tr}Do not use your browser's Back button.{/tr}</li>
 <li><a href="tiki-install.php?reset=yes{if $multi}&amp;multi={$multi}{/if}{if $lang}&amp;lang={$lang}{/if}" class="link">{tr}Reset database connection settings{/tr}</a>.</li>
</ul>






    	{/if}
	{/if}
</div>

{* multitiki ----------------------------- *}
{if $virt}
</td></tr></table>
{/if}
{* / multitiki --------------------------- *}

<hr />
<p align="center"><a href="http://www.tikiwiki.org" title="Tikiwiki"><img src="img/tiki/tikibutton2.png" alt="Tikiwiki" width="80" height="31" border="0" /></p>
