<div style="margin:10px 30px;">
<h1>{tr}Tiki installer{/tr} v1.10.1 (CVS)<a title='help' href='http://doc.tikiwiki.org/Installation' target="help"><img border='0' src='img/icons/help.gif' alt="{tr}Help{/tr}" /></a></h1>

{if $tikifeedback}
<br />{section name=n loop=$tikifeedback}<div class="simplebox {if $tikifeedback[n].num > 0} highlight{/if}">{$tikifeedback[n].mes}</div>{/section}
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

<a href="tiki-install.php?restart=1{if $multi}&amp;multi={$multi}{/if}{if $lang}&amp;lang={$lang}{/if}" class="link">{tr}Reload{/tr}</a><br /><br />

{* we do not have a valid db connection or db reset is requested *}

{if $dbcon eq 'n' or $resetdb eq 'y'}
<b>{tr}Tiki cannot find a database connection{/tr}</b><br />
{tr}Please enter your database connection info{/tr}<br /><br />
<form action="tiki-install.php" method="post">
{if $multi}<input type="hidden" name="multi" value="{$multi}" />{/if}
{if $lang}<input type="hidden" name="lang" value="{$lang}" />{/if}
<table class="normal"><tr class="formcolor">
<td>{tr}Database type{/tr}:</td>
<td>
<select name="db">
{foreach key=dsn item=dbname from=$dbservers}
<option value="{$dsn}">{$dbname}</option>
{/foreach}
</select>
</td>
<td>{tr}The type of database you intend to use{/tr}<br />
<i>{tr}Only databases supported by your PHP installation are listed here. If your database is not in the list, try to install the appropriate PHP extension.{/tr}</i>
</td>
</tr>

<tr class="formcolor">
<td>{tr}Host:{/tr}</td>
<td>
<input type="text" name="host" value="localhost" />
</td><td>
{tr}Hostname or IP for your MySQL database, example: localhost if running in the same machine as tiki{/tr}<br />
{tr}If you use SQLite, insert the path and filename to your database file{/tr}
</td>
</tr>

<tr class="formcolor">
<td>{tr}User{/tr}:</td>
<td>
<input type="text" name="user" />
</td><td>
{tr}Database user{/tr}
</td>
</tr>

<tr class="formcolor">
<td>{tr}Password{/tr}:</td>
<td>
<input type="password" name="pass" />
</td><td>
{tr}Database password{/tr}
</td>
</tr>

<tr class="formcolor">
<td>{tr}Database name{/tr}:</td>
<td>
<input type="text" name="name" />
</td><td>
{tr}The name of the database where tiki will create tables. You can create the database using mysqladmin, or PHPMyAdmin or ask your
hosting service to create a MySQL database.  Normally Tiki tables won't conflict with other product names{/tr}.<br />
{tr}If you use Oracle, you can put your TNS Name here and leave hostname empty
or you override tnsnames.ora and put your SID here and fill your hostname:port above{/tr}.
</td>
</tr>
			
<tr class="formcolor">
<td>&nbsp;</td>
<td><input type="hidden" name="resetdb" value="{$resetdb}" />
<input type="submit" name="dbinfo" /></td>
<td>&nbsp;</td>
</tr>
	  	
	  </table>
	  </form>
	{else}
	  {* we do have a database connection *}
	  {if $dbdone eq 'n'}
		  {if $logged eq 'y'}
		    {* we are logged if no admin account is found or if the admin user is logged in*}
		    <b>{tr}Welcome to the installation &amp; upgrade script!{/tr}</b><br />
		    <br /><br />
			
		    <form method="post" action="tiki-install.php">
				{if $multi}<input type="hidden" name="multi" value="{$multi}" />{/if}
				{if $lang}<input type="hidden" name="lang" value="{$lang}" />{/if}
				<hr />
		    <table>
		    <tr><td style="text-align: center;" colspan="2"
 rowspan="1" height="26"><font size="5"><b>{tr}Install{/tr}</b></font>
 			</td></tr>
			<tr><td>
			{tr}Create database (clean install) with profile{/tr}:
			</td><td>
			<select name="profile" size="{$profiles|@count}">
			{section name=ix loop=$profiles}
			<option value="{$profiles[ix].name|escape}">{$profiles[ix].desc}</option>
			{/section}
			</select>
			<input type="submit" name="scratch" value="{tr}Create{/tr}" />
		    </td></tr><tr>
			<td height="100" valign="top">
			</td><td height="100" valign="top">
			<a target="_blank" href="http://doc.tikiwiki.org/Profiles" class="link">{tr}Descriptions of the available profiles{/tr}</a>
		    </td>
		    <tr><td colspan="2">

			<hr />
			<tr><td style="text-align: center;" colspan="2"
 rowspan="1" height="26"><font size="5"><b>{tr}Upgrade{/tr}</b></font>
 			</td></tr>
		    <tr><td colspan="2">				
			{tr}Important{/tr}: <b>{tr}backup your database{/tr}</b> {tr}with mysqldump or phpmyadmin before you proceed{/tr}. <br />
			</td></tr>
		    <tr><td>			
			{tr}Update database using script{/tr}: 
			</td><td>
			<select name="file" size="{$files|@count}">
			{section name=ix loop=$files}
			<option value="{$files[ix]|escape}">{$files[ix]}</option>
			{/section}
			</select>
			<input type="submit" name="update" value="{tr}Update{/tr}" />
		    </td></tr>
		    <tr><td colspan="2">
			{tr}For database update from 1.8 or later{/tr}:
			<ol>
				<li>{tr}If you upgrade from 1.8.x you <b>MUST</b> run tiki_1.8to1.9 and don't need an additional script{/tr}.</li>
				<li>{tr}If you upgrade from a previous 1.9.x version, use tiki_1.8to1.9, too. (ex.: 1.9.2 to 1.9.5){/tr}</li>
			</ol>
		    <tr><td colspan="2">
		    	{tr}For database update from 1.7.x, please visit <a target="help" href="http://tikiwiki.org/UpgradeTo18">Tiki database 1.7.x to 1.8x instructions{/tr}</a>

			
		</td></tr>
		    <tr><td colspan="2">
		    	{tr}For information about tiki-secdb_*.sql files, please see <a target="help" href="http://tikiwiki.org/AdminSecurity">http://tikiwiki.org/AdminSecurity{/tr}</a>

			
		</td></tr>		
		
		
		
		
		    </table>
		    </form><br />
			<hr />
			<br /><br /><br />
			<a href="tiki-index.php" class="link">{tr}Do nothing and enter Tiki{/tr}</a><br />
			<a href="tiki-install.php?reset=yes{if $lang}&amp;lang={$lang}{/if}" class="link">{tr}Reset database connection settings{/tr}</a>
		  {else}
			{* we are not logged then no admin account found and user not logged*}
			<b>{tr}This site has an admin account configured{/tr}</b><br />
		    {tr}Please enter your admin password to continue{/tr}<br /><br />

     <form name="loginbox" action="tiki-install.php" method="post">
			<input type="hidden" name="login" value="admin" />
			{if $multi}<input type="hidden" name="multi" value="{$multi}" />{/if}
			{if $lang}<input type="hidden" name="lang" value="{$lang}" />{/if}
          <table>
          <tr><td class="module">{tr}User{/tr}:</td></tr>
          <tr><td>admin</td></tr>
          <tr><td class="module">{tr}pass{/tr}:</td></tr>
          <tr><td><input type="password" name="pass" size="20" /></td></tr>
          <tr><td><input type="submit" name="login" value="{tr}Login{/tr}" /></td></tr>
          </table>
      </form>

		  {/if}
    	{else}
    		<b>{tr}Print operations executed successfully{/tr}:</b> {$succcommands|@count} {tr}sql queries{/tr}<br />
    		<b>{tr}Print operations failed{/tr}:</b> {$failedcommands|@count} {tr}sql queries{/tr}<br />
		{if $failedcommands|@count > 0}
    		<textarea rows="15" cols="80">
    		{section loop=$failedcommands name=ix}
    		{$failedcommands[ix]}
    		{/section}
    		</textarea><br />
		{/if}
		<br />

		<div style="border-style: solid; border-width: 1; padding: 4; background-color: #a9ff9b;">
		<p align="center">
		<span style="font-size: large; padding: 4px;">
    		{tr}Your database has been configured and Tikiwiki is ready to run! If
    		this is your first install, your admin password is 'admin'. You can
    		now log in into Tikiwiki as user 'admin' and start configuring
    		the application.{/tr}
		</span>
		</p>
		</div>
    		<br />

    		{tr}READ THE FOLLOWING NOTES BEFORE ENTERING TIKI USING THE LINKS BELOW!{/tr}

		<div class="rbox" name="tip">
		<div class="rbox-title" name="tip">Note</div>
		<div class="rbox-data" name="tip">
		{tr}Note: This install script may be potentially harmful so we strongly
		recommend you to disable the script and then proceed into Tiki. If
		you decide to reuse later, just follow the instructions in
		tiki-install.php to restore{/tr}.
		</div>
		</div>

{if $php_memory_limit < 64 * 1024 * 1024}
    		<div class="rbox" name="tip">
            <div class="rbox-title" name="tip">Note</div>  
            <div class="rbox-data" name="tip">{tr}Make sure tiki gets more than 8 MB of memory for script execution. 
See file php.ini, the relevant key is memory_limit. Use something like memory_limit = 16M and restart your 
webserver. Too little memory will cause blank pages!{/tr}</div>
            </div>
			
	{if $php_memory_limit <= 0}
		<div style="border-style: solid; border-width: 1; padding: 4">
		  <p align="center">
		  <span style="padding: 2px; background-color: #00FF00">
		  {tr}Tiki has not detected your PHP memory_limit. This probably means you have no set limit (all is well){/tr}. 
		  </span>
		</div>	
	
	{elseif $php_memory_limit <= 8 * 1024 * 1024}
		<div style="border-style: solid; border-width: 1; padding: 4">
		  <p align="center">
		  <span style="text-decoration: blink; font-size: x-large; padding: 4px; background-color: #FF0000">
		  {tr}Tiki has detected your PHP memory limit at{/tr}: {$php_memory_limit|kbsize:true:0}
		  </span>
		</div>

	{else}
		<div style="border-style: solid; border-width: 1; padding: 4">
		  <p align="center">
		  <span style="font-size: large; padding: 4px;">
		  {tr}Tiki has detected your PHP memory_limit at{/tr}: {$php_memory_limit|kbsize:true:0}. 
		  </span>
		</div>	
	{/if}			
{/if}			

		{if isset($smarty.post.scratch)}
    		<div class="rbox" name="tip">
            <div class="rbox-title" name="tip">{tr}Note{/tr}</div>  
            <div class="rbox-data" name="tip">{tr}If this is a first time installation, go to tiki-admin.php after login to start configuring your new Tiki installation{/tr}.</div>
            </div>
            <br />
	    	{/if}
	
		{if isset($smarty.post.update)}
    		<div class="rbox" name="tip">
            <div class="rbox-title" name="tip">{tr}Note{/tr}</div>  
            <div class="rbox-data" name="tip">{tr}If you did a Tiki upgrade, make sure to clean the caches (templates_c/) manually or by using the feature on admin / system{/tr}.</div>
            </div>
            <br />
	    	{/if}
            <br />
            <b>{tr}Now you may proceed by clicking one of these links{/tr}:</b><br /><br />

    		<a href="tiki-install.php?kill=1" class="link"><b>{tr}Click here to disable the install script and proceed into tiki{/tr}.</b></a><br /><br />
    		<a href="tiki-index.php" class="link">{tr}Click here to proceed into tiki without disabling the script{/tr}.</a><br /><br />
    		<a href="tiki-install.php?reset=yes{if $multi}&amp;multi={$multi}{/if}{if $lang}&amp;lang={$lang}{/if}" class="link">{tr}Reset database connection settings{/tr}.</a><br /><br />
    		<a href="tiki-install.php{if $multi}?multi={$multi}{/if}{if $lang}{if $multi}&amp;{else}?{/if}lang={$lang}{/if}" class="link">{tr}Go back and run another install/upgrade script{/tr}</a> - {tr}do not use your Back button in your browser!{/tr}<br /><br />
    	{/if}
	{/if}
</div>

{* multitiki ----------------------------- *}
{if $virt}
</td></tr></table>
{/if}
{* / multitiki --------------------------- *}
