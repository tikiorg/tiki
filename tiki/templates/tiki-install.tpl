<div style="margin-left:180px;margin-right:180px;">
<h1>Tiki installer v{$tiki_version} (CVS) <a title='help' href='http://tikiwiki.org/InstallTiki' target="help"><img
border='0' src='img/icons/help.gif' alt="{tr}help{/tr}" /></a></h1>

{if $tikifeedback}
<br />{section name=n loop=$tikifeedback}<div class="simplebox {if $tikifeedback[n].num > 0} highlight{/if}">{$tikifeedback[n].mes}</div>{/section}
{/if}

{* multitiki ----------------------------- *}
{if $virt}
<table><tr><td width="180">
<div class="box">
<div class="box-title">MultiTiki setup</div>
<div class="box-data">
<div><a href="tiki-install.php">default</a></div><br />
{foreach key=k item=i from=$virt}
<div>
{if $k eq $multi}
<b>{$k}</b>
{else}
<a href="tiki-install.php?multi={$k}" class='linkmodule'>{$k}</a>
{/if}
{if $i eq 'y'} (db ok){else} (db not set){/if}
</div>
{/foreach}
</div></div>

<div class="box">
<div class="box-title">To add a new virtual host</div>
<div class="box-data">
To add a new virtual host run the setup.sh with the domain name of the new host as a last parameter.
</div>
</td>
<td valign="top">
{/if}

<h2>(MultiTiki) {$multi|default:"default"}</h2>
{* / multitiki --------------------------- *}

<a href="tiki-install.php?restart=1{if $multi}&amp;multi={$multi}{/if}" class="link">reload</a><br /><br />

{* we do not have a valid db connection or db reset is requested *}

{if $dbcon eq 'n' or $resetdb eq 'y'}
<b>Tiki cannot find a database connection</b><br />
Please enter your database connection info<br /><br />
<form action="tiki-install.php" method="post">
{if $multi}<input type="hidden" name="multi" value="{$multi}" />{/if}
<table class="normal"><tr class="formcolor">
<td>Database type:</td>
<td>
<select name="db">
{section name=dbnames loop=$dbservers}
<option value="{$dbservers[dbnames]}">{$dbservers[dbnames]}</option>
{/section}
</select>
</td>
<td>The type of database you intend to use</td>
</tr>

<tr class="formcolor">
<td>Host:</td>
<td>
<input type="text" name="host" value="localhost" />
</td><td>
Hostname or IP for your MySQL database, example: localhost if running in the same machine as tiki<br />
If you use Oracle, insert your TNS Name here<br />
If you use SQLite, insert the path and filename to your database file
</td>
</tr>

<tr class="formcolor">
<td>User:</td>
<td>
<input type="text" name="user" />
</td><td>
Database user
</td>
</tr>

<tr class="formcolor">
<td>Password:</td>
<td>
<input type="password" name="pass" />
</td><td>
Database password
</td>
</tr>

<tr class="formcolor">
<td>Database name:</td>
<td>
<input type="text" name="name" />
</td><td>
The name of the database where tiki will create tables. You can create the database using mysqladmin, or PHPMyAdmin or ask your
hosting service to create a MySQL database.  Normally Tiki tables won't conflict with other product names. 
</td>
</tr>
			
<tr class="formcolor">
<td>&nbsp;</td>
<td><input type="hidden" name="resetdb" value="{$resetdb}">
<input type="submit" name="dbinfo" /></td>
<td>&nbsp;</td>
</tr>
	  	
</table>
</form>


{* we do have a database connection *}

{else}

{if $dbdone eq 'n'}
{if $logged eq 'y'}
{* we are logged if no admin account is found or if he user logged in*}
<b>Welcome to the installation script!</b><br />
You can now create a new database or update your current database<br /><br />
<form method="post" action="tiki-install.php">
{if $multi}<input type="hidden" name="multi" value="{$multi}" />{/if}
<table>
<tr><td>
Create database with profile:
</td><td>
<select name="profile">
{section name=ix loop=$profiles}
<option value="{$profiles[ix].name|escape}">{$profiles[ix].desc}</option>
{/section}
</select>
<input type="submit" name="scratch" value="create" />	    
</td></tr>
<tr><td>
</td><td>
<a target="_new" href="http://tikiwiki.org/TikiProfiles" class="link">Descriptions of the available profiles</a>
<p>
</td></tr>
<tr><td>
Update database using script: 
</td><td>
<select name="file">
{section name=ix loop=$files}
<option value="{$files[ix]|escape}">{$files[ix]}</option>
{/section}
</select>
<input type="submit" name="update" value="update" />
</td></tr>
<tr><td colspan="2">
For database update from 1.7 you should use this order:
<ol>
<li>tiki_1.7to1.8.sql - can be run more than once if errors occur</li>
<li>comments_fix_1.7to1.8.sql - use only once!</li>
<li>structure_fix_1.7to1.8.sql use only once!</li>
</ol>
We recommend that you <b>backup your database</b> with mysqldump :<br />
(mysqldump tiki_dbname &gt; backup.sql).
</td></tr>
</table>
</form><br />
{else}
{* we are not logged then no admin account found and user not logged*}
<b>This site has an admin account configured</b><br />
Please enter your admin password to continue<br /><br />

<form name="loginbox" action="tiki-install.php" method="post"> 
{if $multi}<input type="hidden" name="multi" value="{$multi}" />{/if}
<table>
<tr><td class="module">{tr}user{/tr}:</td></tr>
<tr><td>admin</td></tr>
<tr><td class="module">{tr}pass{/tr}:</td></tr>
<tr><td><input type="password" name="pass" size="20" /></td></tr>
<tr><td><input type="submit" name="login" value="{tr}login{/tr}" /></td></tr>
</table>
</form>
{/if}
{else}
<b>Print operations executed successfully</b><br />
<textarea rows="15" cols="80">
{section loop=$succcommands name=ix}
{$succcommands[ix]}
{/section}
</textarea><br /><br />
<b>Print operations failed</b><br />
<textarea rows="15" cols="80">
{section loop=$failedcommands name=ix}
{$failedcommands[ix]}
{/section}
</textarea><br /><br />
Your database has been configured and Tiki is ready to run, if
this is your first install your admin password is 'admin'. You can
now log in into Tiki as 'admin' - 'admin' and start configuring
the application.<br />
Note: This install script may be potentially harmful so we strongly
recommend you to remove the script and then proceed into Tiki. If
you decide to remove the script it will be renamed to tiki-install.done<br /><br />
{/if}
{/if}
{if $dbcon eq 'y'}
{if $dbdone eq 'y'} 
<a href="tiki-install.php?kill=1" class="link">Click here to remove the install script and proceed into tiki</a><br />
<a href="tiki-index.php" class="link">Click here to proceed into tiki without removing the script</a><br />
{else}
<a href="tiki-index.php" class="link">Do nothing and enter Tiki</a><br />
{/if}
<a href="tiki-install.php?reset=yes{if $multi}&amp;multi={$multi}{/if}" class="link">Reset database connection settings</a>
{/if}
</div>

{* multitiki ----------------------------- *}
{if $virt}
</td></tr></table>
{/if}
{* / multitiki --------------------------- *}
