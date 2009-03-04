<?php /* Smarty version 2.6.22, created on 2009-03-04 13:06:12
         compiled from tiki-install.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'tiki-install.tpl', 31, false),array('modifier', 'kbsize', 'tiki-install.tpl', 76, false),array('modifier', 'count', 'tiki-install.tpl', 277, false),array('modifier', 'default', 'tiki-install.tpl', 499, false),array('modifier', 'date_format', 'tiki-install.tpl', 556, false),array('block', 'tr', 'tiki-install.tpl', 284, false),)), $this); ?>
<div id="siteheader" class="clearfix">
	<div id="header-top">
		<div id="sitelogo" style="padding-left: 70px;"><h1><img style="border: medium none ; vertical-align: middle;" alt="TikiWiki CMS/Groupware" src="img/tiki/tiki3.png" />
	<span style="vertical-align: middle">Tiki installer v<?php echo $this->_tpl_vars['tiki_version_name']; ?>
 <a title='help' href='http://doc.tikiwiki.org/Installation' target="help"><img style="border: 0" src='img/icons/help.gif' alt="Help" /></a></span></h1>
	</div>
	</div>
</div>

<div id="middle" class="clearfix">
	<div id="c1c2" class="clearfix">
		<div id="wrapper" class="clearfix">
			<div id="col1" class="marginleft">
				<div id="tiki-center" class="clearfix content">

<?php if ($this->_tpl_vars['install_step'] == '0' || ! $this->_tpl_vars['install_step']): ?>

<h1>Welcome to TikiWiki</h1>
<div style="float:right;"><img src="img/tiki/tikilogo.png" alt="TikiWiki" /></div>
<div class="clearfix">
	<p>Welcome to the TikiWiki installation and upgrade script. Use this script to install a new TikiWiki database or upgrade your existing database to release <strong><?php echo $this->_tpl_vars['tiki_version_name']; ?>
</strong></p>
	<ul>
		<li>For the latest information about this release, please read the <a href="http://www.tikiwiki.org/<?php echo $this->_tpl_vars['tiki_version_name']; ?>
" target="_blank">Release Notes</a>.</li>
		<li>For complete documentation, please visit <a href="http://doc.tikiwiki.org" target="_blank">http://doc.tikiwiki.org</a>.</li>
		<li>For more information about TikiWiki, please visit <a href="http://www.tikiwiki.org" target="_blank">http://www.tikiwiki.org</a>.</li>
	</ul>

	<form action="tiki-install.php" method="post">
		Select your language:
		<select name="lang" id="general-lang" onchange="javascript:submit()">
			<?php unset($this->_sections['ix']);
$this->_sections['ix']['name'] = 'ix';
$this->_sections['ix']['loop'] = is_array($_loop=$this->_tpl_vars['languages']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['ix']['show'] = true;
$this->_sections['ix']['max'] = $this->_sections['ix']['loop'];
$this->_sections['ix']['step'] = 1;
$this->_sections['ix']['start'] = $this->_sections['ix']['step'] > 0 ? 0 : $this->_sections['ix']['loop']-1;
if ($this->_sections['ix']['show']) {
    $this->_sections['ix']['total'] = $this->_sections['ix']['loop'];
    if ($this->_sections['ix']['total'] == 0)
        $this->_sections['ix']['show'] = false;
} else
    $this->_sections['ix']['total'] = 0;
if ($this->_sections['ix']['show']):

            for ($this->_sections['ix']['index'] = $this->_sections['ix']['start'], $this->_sections['ix']['iteration'] = 1;
                 $this->_sections['ix']['iteration'] <= $this->_sections['ix']['total'];
                 $this->_sections['ix']['index'] += $this->_sections['ix']['step'], $this->_sections['ix']['iteration']++):
$this->_sections['ix']['rownum'] = $this->_sections['ix']['iteration'];
$this->_sections['ix']['index_prev'] = $this->_sections['ix']['index'] - $this->_sections['ix']['step'];
$this->_sections['ix']['index_next'] = $this->_sections['ix']['index'] + $this->_sections['ix']['step'];
$this->_sections['ix']['first']      = ($this->_sections['ix']['iteration'] == 1);
$this->_sections['ix']['last']       = ($this->_sections['ix']['iteration'] == $this->_sections['ix']['total']);
?>
				<option value="<?php echo ((is_array($_tmp=$this->_tpl_vars['languages'][$this->_sections['ix']['index']]['value'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"
					<?php if ($this->_tpl_vars['prefs']['site_language'] == $this->_tpl_vars['languages'][$this->_sections['ix']['index']]['value']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['languages'][$this->_sections['ix']['index']]['name']; ?>
</option>
			<?php endfor; endif; ?>
		</select>
		<input type="hidden" name="install_step" value="1" />
		<?php if ($this->_tpl_vars['multi']): ?>		<input type="hidden" name="multi" value="<?php echo $this->_tpl_vars['multi']; ?>
" /><?php endif; ?>
	</form>
</div>
<div align="center" style="margin-top:1em;">
	<form action="tiki-install.php" method="post">
<?php if ($this->_tpl_vars['multi']): ?>		<input type="hidden" name="multi" value="<?php echo $this->_tpl_vars['multi']; ?>
" /><?php endif; ?>
<?php if ($this->_tpl_vars['lang']): ?>		<input type="hidden" name="lang" value="<?php echo $this->_tpl_vars['lang']; ?>
" /><?php endif; ?>
		<input type="hidden" name="install_step" value="1" />
		<input type="submit" value=" Continue " />
	</form>
</div>

<?php elseif ($this->_tpl_vars['install_step'] == '1'): ?>
<h1>Read the License</h1>
<p>TikiWiki is software distributed under the LGPL license. Please read the following license agreement.</p>
<iframe src="license.txt" width="700px" height="400px"> </iframe>
<div align="center" style="margin-top:1em;">
	<p>By clicking &quot;Continue&quot; you agree to the terms of this license.</p>
	<form action="tiki-install.php" method="post">
<?php if ($this->_tpl_vars['multi']): ?>			<input type="hidden" name="multi" value="<?php echo $this->_tpl_vars['multi']; ?>
" /><?php endif; ?>
<?php if ($this->_tpl_vars['lang']): ?>			<input type="hidden" name="lang" value="<?php echo $this->_tpl_vars['lang']; ?>
" /><?php endif; ?>
		<input type="hidden" name="install_step" value="2" />
		<input type="submit" value=" Continue " />
	</form>
</div>

<?php elseif ($this->_tpl_vars['install_step'] == '2'): ?>
<h1>Review the System Requirements</h1>
<div style="float:left;width:60px"><img src="img/webmail/compose.gif" alt="Review" /></div>
<div class="clearfix">
	<p>Before installing TikiWiki, <a href="http://doc.tikiwiki.org/tiki-index.php?page=Requirements+and+Setup&bl=y" target="_blank">review the documentation</a> and confirm that your system meets the minimum requirements.</p>
	<p>This installer will perform some basic checks automatically.</p>
	<br />
	<h2>Memory</h2>
<?php if ($this->_tpl_vars['php_memory_limit'] <= 0): ?>
	<div style="border: solid 1px #000; padding: 5px; background: #a9ff9b;">
		<p align="center"><img src="pics/icons/accept.png" alt="Success" style="vertical-align:middle"/> Tiki has not detected your PHP memory_limit. This probably means you have no set limit (all is well). </p>
	</div>	
<?php elseif ($this->_tpl_vars['php_memory_limit'] < 32 * 1024 * 1024): ?>
	<div style="border-style: solid; border-width: 1; padding: 5px; background: #FF0000">
		<p align="center"><img src="pics/icons/delete.png" alt="alert" style="vertical-align:middle" /> Tiki has detected your PHP memory limit at: <?php echo ((is_array($_tmp=$this->_tpl_vars['php_memory_limit'])) ? $this->_run_mod_handler('kbsize', true, $_tmp, true, 0) : smarty_modifier_kbsize($_tmp, true, 0)); ?>
</p>
	</div>
	<p>TikiWiki requires <strong>at least</strong> 32MB of PHP memory for script execution. Allocating too little memory will cause TikiWiki to display blank pages.</p>
	<p>To change the memory limit, use the <strong>memory_limit</strong> key in your <strong>php.ini </strong> file (for example: memory_limit = 32M) and restart your webserver.</p>

<?php else: ?>
	<div style="border: solid 1px #000; padding: 4px; background-color: #a9ff9b;">
		<p align="center">
		  <span style="font-size: large; padding: 4px;">
		  <img src="pics/icons/accept.png" alt="Success" style="vertical-align:middle"/> Tiki has detected your PHP memory_limit at: <?php echo ((is_array($_tmp=$this->_tpl_vars['php_memory_limit'])) ? $this->_run_mod_handler('kbsize', true, $_tmp, true, 0) : smarty_modifier_kbsize($_tmp, true, 0)); ?>
. 
		  </span>
		</p>
	</div>	
<?php endif; ?>			


	<br />
	<h2>Mail</h2>
	<p>TikiWiki uses the PHP <strong>mail</strong> function to send email notifications and messages.</p>
<?php if ($this->_tpl_vars['perform_mail_test'] != 'y'): ?>
	<p>To test your system configuration, TikiWiki will attempt to send a test message to info@tikiwiki.org.</p>
	<div align="center">
	<form action="tiki-install.php#mail" method="post">
		<input type="hidden" name="install_step" value="2" />
		<input type="hidden" name="perform_mail_test" value="y" />
		<input type="submit" value=" Send Test Message " />
<?php if ($this->_tpl_vars['multi']): ?>		<input type="hidden" name="multi" value="<?php echo $this->_tpl_vars['multi']; ?>
" /><?php endif; ?>
<?php if ($this->_tpl_vars['lang']): ?>		<input type="hidden" name="lang" value="<?php echo $this->_tpl_vars['lang']; ?>
" /><?php endif; ?>
	</form>
	</div>
<?php else: ?>
	
<?php if ($this->_tpl_vars['mail_test'] == 'y'): ?>
	<div style="border: solid 1px #000; padding: 5px; background: #a9ff9b;">
		<p align="center"><img src="pics/icons/accept.png" alt="Success" style="vertical-align:middle"/> Tiki was able to send a test message to info@tikiwiki.org.. </p>
	</div>
	<p>&nbsp;</p>
<?php else: ?>
	<div style="border: solid 1px #000; padding: 5px; background: #FF0000">
		<p align="center"><img src="pics/icons/delete.png" alt="alert" style="vertical-align:middle" /> Tiki was not able to send a test message. Review your mail log for details.</p>
	</div>
	<p>Review the mail settings in your <strong>php.ini</strong> file (for example: confirm that the <strong>sendmail_path</strong> is correct). If your host requires SMTP authentication, additional configuration may be necessary.</p>
<?php endif; ?>
<?php endif; ?>
	<br />
	<h2>Image Processing</h2>
<?php if ($this->_tpl_vars['gd_test'] == 'y'): ?>
	<div style="border: solid 1px #000; padding: 5px; background: #a9ff9b;">
		<p align="center"><img src="pics/icons/accept.png" alt="Success" style="vertical-align:middle"/> Tiki detected GD <?php echo $this->_tpl_vars['gd_info']; ?>
.</p>
	</div>
<?php else: ?>
	<div style="border: solid 1px #000; padding: 5px; background: #FF0000">
		<p align="center"><img src="pics/icons/delete.png" alt="alert" style="vertical-align:middle" /> Tiki was not able to detect the GD library.</p>
	</div>
	<p>&nbsp;</p>
<?php endif; ?>
	<p>TikiWiki uses the GD library to process images for the Image Gallery and CAPTCHA support.</p>
</div>

<div align="center" style="margin-top:1em;">
<form action="tiki-install.php" method="post">
	<input type="hidden" name="install_step" value="3" />
	<input type="submit" value=" Continue " />
<?php if ($this->_tpl_vars['multi']): ?>		<input type="hidden" name="multi" value="<?php echo $this->_tpl_vars['multi']; ?>
" /><?php endif; ?>
<?php if ($this->_tpl_vars['lang']): ?>		<input type="hidden" name="lang" value="<?php echo $this->_tpl_vars['lang']; ?>
" /><?php endif; ?>
</form>
</div>

<?php elseif ($this->_tpl_vars['install_step'] == '3' || ( $this->_tpl_vars['dbcon'] == 'n' || $this->_tpl_vars['resetdb'] == 'y' )): ?>

<h1>Set Database Connection</h1>
<div style="float:left;width:60px"><img src="pics/large/stock_line-in48x48.png" alt="Database" /></div>
<div class="clearfix">
	<p>TikiWiki requires an active database connection. You must create the database and user <em>before</em> completing this page.</p>
<?php if ($this->_tpl_vars['dbcon'] != 'y'): ?>
	<div align="center" style="padding:1em;">
		<img src="pics/icons/delete.png" alt="alert" style="vertical-align:middle" /> <span style="font-weight: bold">Tiki cannot find a database connection</span>. This is normal for a new installation.
	</div>
<?php else: ?>
	<div align="center" style="padding:1em;">
		<img src="pics/icons/information.png" alt="information" style="vertical-align:middle" /> Tiki found an existing database connection in your local.php file.
	<form action="tiki-install.php" method="post">
		<input type="hidden" name="install_step" value="4" />
<?php if ($this->_tpl_vars['multi']): ?>		<input type="hidden" name="multi" value="<?php echo $this->_tpl_vars['multi']; ?>
" /><?php endif; ?>
<?php if ($this->_tpl_vars['lang']): ?>		<input type="hidden" name="lang" value="<?php echo $this->_tpl_vars['lang']; ?>
" /><?php endif; ?>
		<input type="submit" value=" Use Existing Connection " />
	</form>
	</div>
<?php endif; ?>		
	
<?php if ($this->_tpl_vars['tikifeedback']): ?>
	<br />
<?php unset($this->_sections['n']);
$this->_sections['n']['name'] = 'n';
$this->_sections['n']['loop'] = is_array($_loop=$this->_tpl_vars['tikifeedback']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['n']['show'] = true;
$this->_sections['n']['max'] = $this->_sections['n']['loop'];
$this->_sections['n']['step'] = 1;
$this->_sections['n']['start'] = $this->_sections['n']['step'] > 0 ? 0 : $this->_sections['n']['loop']-1;
if ($this->_sections['n']['show']) {
    $this->_sections['n']['total'] = $this->_sections['n']['loop'];
    if ($this->_sections['n']['total'] == 0)
        $this->_sections['n']['show'] = false;
} else
    $this->_sections['n']['total'] = 0;
if ($this->_sections['n']['show']):

            for ($this->_sections['n']['index'] = $this->_sections['n']['start'], $this->_sections['n']['iteration'] = 1;
                 $this->_sections['n']['iteration'] <= $this->_sections['n']['total'];
                 $this->_sections['n']['index'] += $this->_sections['n']['step'], $this->_sections['n']['iteration']++):
$this->_sections['n']['rownum'] = $this->_sections['n']['iteration'];
$this->_sections['n']['index_prev'] = $this->_sections['n']['index'] - $this->_sections['n']['step'];
$this->_sections['n']['index_next'] = $this->_sections['n']['index'] + $this->_sections['n']['step'];
$this->_sections['n']['first']      = ($this->_sections['n']['iteration'] == 1);
$this->_sections['n']['last']       = ($this->_sections['n']['iteration'] == $this->_sections['n']['total']);
?>
	<div class="simplebox <?php if ($this->_tpl_vars['tikifeedback'][$this->_sections['n']['index']]['num'] > 0): ?> highlight<?php endif; ?>">
		<img src="pics/icons/<?php if ($this->_tpl_vars['tikifeedback'][$this->_sections['n']['index']]['num'] > 0): ?>delete.png" alt="Error"<?php else: ?>accept.png" alt="Success"<?php endif; ?> style="vertical-align:middle"/> <?php echo $this->_tpl_vars['tikifeedback'][$this->_sections['n']['index']]['mes']; ?>

	</div>
<?php endfor; endif; ?>
<?php endif; ?>
	<p>Use this page to create a new database connection.</p>
	<form action="tiki-install.php" method="post">
		<input type="hidden" name="install_step" value="4" />
<?php if ($this->_tpl_vars['multi']): ?>		<input type="hidden" name="multi" value="<?php echo $this->_tpl_vars['multi']; ?>
" /><?php endif; ?>
<?php if ($this->_tpl_vars['lang']): ?>		<input type="hidden" name="lang" value="<?php echo $this->_tpl_vars['lang']; ?>
" /><?php endif; ?>
		<fieldset><legend>Database information</legend>
		<p>Enter your database connection information.</p>
		<div style="padding:5px;">
			<label for="db">Database type:</label> 
			<div style="margin-left:1em;">
			<select name="db" id="db">
<?php $_from = $this->_tpl_vars['dbservers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['dsn'] => $this->_tpl_vars['dbname']):
?>
				<option value="<?php echo $this->_tpl_vars['dsn']; ?>
"><?php echo $this->_tpl_vars['dbname']; ?>
</option>
<?php endforeach; endif; unset($_from); ?>
			</select> <a href="#" onclick="flip('db_help');" title="Help"><img src="pics/icons/help.png" alt="Help" /></a>
			<div style="display:none;" id="db_help">
				<p>Select the type of database to use with Tiki.</p>
				<p>Only databases supported by your PHP installation are listed here. If your database is not in the list, try to install the appropriate PHP extension.</p>
			</div>
			</div>
		</div>
		<div style="padding:5px;">
			<label for="host">Host name:</label>
			<div style="margin-left:1em;">
			<input type="text" name="host" id="host" value="localhost" size="40" /> <a href="#" onclick="flip('host_help');" title="Help"><img src="pics/icons/help.png" alt="Help" /></a>
			<br /><em>Enter the host name or IP for your database.</em>
			<div style="display:none;" id="host_help">
				<p>Use <strong>localhost</strong> if the database is running on the same machine as Tiki. For SQLite, enter the path and filename to your database file.</p>
			</div>
			</div>
		</div>
		<div style="padding:5px;">
			<label for="name">Database name:</label>
			<div style="margin-left:1em;">
			<input type="text" id="name" name="name" size="40" /> <a href="#" onclick="flip('name_help');" title="Help"><img src="pics/icons/help.png" alt="Help" /></a>
		
			<br /><em>Enter the name of the database that Tiki will use.</em> 
			<div style="margin-left:1em;display:none;" id="name_help">
				<p>The database must already exist. You can create the database using mysqladmin, PHPMyAdmin, cPanel, or ask your hosting provider.  Normally Tiki tables won't conflict with other product names.</p>
				<p>For Oracle:
				<ul>
					<li>Enter your TNS Name here and leave Host empty.<br />
					or</li>
					<li>Override tnsnames.ora and put your SID here and enter your hostname:port in the Host field.</li>
				</ul></p>
			</div>
			</div>
		</div>
		</fieldset><br />
		<fieldset><legend>Database user</legend>
		<p>Enter a database user with administrator permission for the Database.</p>
		<div style="padding:5px;">
			<label for="user">User name:</label> <input type="text" id="user" name="user" />
		</div>
		<div style="padding:5px;">
			<label for="pass">Password:</label> <input type="password" id="pass" name="pass" />
		</div>
		</fieldset>
		<input type="hidden" name="resetdb" value="<?php echo $this->_tpl_vars['resetdb']; ?>
" />
		<div align="center" style="margin-top:1em;"><input type="submit" name="dbinfo" value=" Continue " /></div>	 
	</form>
</div>

<?php elseif ($this->_tpl_vars['install_step'] == '4'): ?>
<h1>Installation <?php if ($this->_tpl_vars['tikidb_created']): ?>&amp; Upgrade <?php endif; ?>Profiles</h1>
<div style="float:left;width:60px"><img src="pics/large/profiles48x48.png" alt="Profiles" /></div>
<div class="clearfix">
<p>Select the installation <?php if ($this->_tpl_vars['tikidb_created']): ?>(or upgrade) <?php endif; ?>script to use. This script will populate <?php if ($this->_tpl_vars['tikidb_created']): ?>(or upgrade)<?php endif; ?> the database.</p>
<p>Profiles can be used to pre-configure your site with specific features and settings. Visit <a href="http://profiles.tikiwiki.org" target="_blank">http://profiles.tikiwiki.org</a> for more information.</p> 
	  <?php if ($this->_tpl_vars['dbdone'] == 'n'): ?>
		  <?php if ($this->_tpl_vars['logged'] == 'y'): ?>
		    
		    <form method="post" action="tiki-install.php">
		    	<input type="hidden" name="install_step" value="5" />
				<?php if ($this->_tpl_vars['multi']): ?><input type="hidden" name="multi" value="<?php echo $this->_tpl_vars['multi']; ?>
" /><?php endif; ?>
				<?php if ($this->_tpl_vars['lang']): ?><input type="hidden" name="lang" value="<?php echo $this->_tpl_vars['lang']; ?>
" /><?php endif; ?>
	  <br />
<table class="admin">
	<tr>
		<td valign="top">
			<fieldset><legend>Install</legend>
<?php if ($this->_tpl_vars['tikidb_created']): ?>
			<script type="text/javascript">
			<!--//--><![CDATA[//><!--
				<?php echo '
				function install() {
					document.getElementById(\'install-link\').style.display=\'none\';
					document.getElementById(\'install-table\').style.visibility=\'\';
				}
				'; ?>

			//--><!]]>
			</script>
			<div id="install-link">
			
			<p style="text-align:center;"><a class="button" href="javascript:install()">Reinstall the database.</a></p>
			<p style="text-align: center"><img src="img/silk/sticky.png" alt="warning" style="vertical-align:middle"/> <strong>Warning</strong>: This will destroy your current database.</p>
			</div>
		    <div id="install-table" style="visibility:hidden">
			<?php else: ?>
		    <div id="install-table">
			<?php endif; ?>
			 <?php if ($this->_tpl_vars['tikidb_created']): ?><p style="text-align: center"><img src="img/silk/sticky.png" alt="warning" style="vertical-align:middle"/> <strong>Warning</strong>: This will destroy your current database.</p><?php endif; ?>			  
			  <p>Create a new database (clean install) with profile:</p>
			<select name="profile" size="<?php if ($this->_tpl_vars['profiles']): ?><?php echo count($this->_tpl_vars['profiles']); ?>
<?php else: ?>5<?php endif; ?>">
			<option value="" selected="selected">Bare-bones default install</option>
			<option value="Simple_Bug_Tracker">Simple Bug Tracker</option>
			<option value="Small_Business_Web_Presence">Small Business Web Presence</option>
			<option value="Open_Collaboration_Permissions">Open Collaboration Permissions</option>
			<option value="Intranet">Intranet</option>
			</select>
			 <p>See the documentation for <a target="_blank" href="http://doc.tikiwiki.org/Profiles" class="link" title="<?php $this->_tag_stack[] = array('tr', array()); $_block_repeat=true;smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Description of available profiles.">descriptions of the available profiles<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></a>.</p>
			 <p>&nbsp;</p>
				<div align="center">
					<input type="submit" name="scratch" value=" Install " />
				</div>

			</div>
			</fieldset>
		</td>
			<?php if ($this->_tpl_vars['tikidb_created']): ?>
			<td width="50%" valign="top">
			<fieldset><legend>Upgrade</legend>
			<p>Automatically upgrade your existing database to v<?php echo $this->_tpl_vars['tiki_version_name']; ?>
.</p>
			<p align="center"><input type="submit" name="update" value=" Upgrade " /></p>
			</fieldset>
			</td>
			<?php endif; ?>
		</tr></table>
		    </form>
 <?php else: ?>
			
			<p><img src="pics/icons/delete.png" alt="alert" style="vertical-align:middle" />  <span style="font-weight: bold">This site has an admin account configured</span>.</p>
		   <p>Please login with your admin password to continue.</p>

     <form name="loginbox" action="tiki-install.php" method="post">
			<input type="hidden" name="login" value="admin" />
			<?php if ($this->_tpl_vars['multi']): ?><input type="hidden" name="multi" value="<?php echo $this->_tpl_vars['multi']; ?>
" /><?php endif; ?>
			<?php if ($this->_tpl_vars['lang']): ?><input type="hidden" name="lang" value="<?php echo $this->_tpl_vars['lang']; ?>
" /><?php endif; ?>
          <table>
          <tr><td class="module">User:</td><td><input value="admin" disabled="disabled" size="20" /></td></tr>
          <tr><td class="module">Pass:</td><td><input type="password" name="pass" size="20" /></td></tr>
          <tr><td colspan="2"><p align="center"><input type="submit" name="login" value="Login" /></p></td></tr>
          </table>
      </form>

		  <?php endif; ?>
<?php endif; ?>
</div>

<?php elseif ($this->_tpl_vars['install_step'] == '5' || ( $this->_tpl_vars['dbdone'] != 'n' )): ?>
<h1>Review the <?php if (isset ( $_POST['update'] )): ?>Upgrade<?php else: ?>Installation<?php endif; ?></h1>
		<div style="margin: 10px 0 5px 0; border: solid 1px #000; padding: 5px; background: #a9ff9b;">
		<p style="text-align:center; font-size: large;"><?php if (isset ( $_POST['update'] )): ?>Upgrade<?php else: ?>Installation<?php endif; ?> complete.</p>
		<p>Your database has been configured and Tikiwiki is ready to run! 
      <?php if (isset ( $_POST['scratch'] )): ?>
        If this is your first install, your admin password is <strong>admin</strong>.
      <?php endif; ?> 
      You can now log in into Tikiwiki as user <strong>admin</strong> and start configuring the application.
		</p>
		</div>
<p><img src="pics/icons/accept.png" alt="Success" style="vertical-align:middle"/> <span style="font-weight: bold"><?php if (isset ( $_POST['update'] )): ?>Upgrade<?php else: ?>Installation<?php endif; ?> operations executed successfully</span>: <?php echo count($this->_tpl_vars['installer']->success); ?>
 SQL queries.</p>
<?php if (count($this->_tpl_vars['installer']->failures) > 0): ?>
			<script type="text/javascript">
			<!--//--><![CDATA[//><!--
				<?php echo '
				function sql_failed() {
					document.getElementById(\'sql_failed_log\').style.display=\'block\';
				}
				'; ?>

			//--><!]]>
			</script>

<p><img src="pics/icons/delete.png" alt="Failed" style="vertical-align:middle"/> <strong>Operations failed:</strong> <?php echo count($this->_tpl_vars['installer']->failures); ?>
 SQL queries. 
<a href="javascript:sql_failed()">Display details</a>.

<div id="sql_failed_log" style="display:none">
 <p>During an upgrade, it is normal to have SQL failures resulting with <strong>Table already exists</strong> messages.</p>
    		<textarea rows="15" cols="80">
<?php $_from = $this->_tpl_vars['installer']->failures; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
<?php echo $this->_tpl_vars['item'][0]; ?>

<?php echo $this->_tpl_vars['item'][1]; ?>

<?php endforeach; endif; unset($_from); ?>
    		</textarea>

</div>
<?php endif; ?>
<p><a href="tiki-install.php?install_step=4" title="Go back and run another install/upgrade script">Go back and run another install/upgrade script.</a></p>


<?php if (isset ( $this->_tpl_vars['htaccess_error'] )): ?>
<h3><img src="pics/icons/information.png" alt="Note" style="vertical-align:middle"/> Security</h3>
To secure your TikiWiki installation - and if you are using Apache web server - you should rename the <span style="font-weight: bold">_htaccess</span> file to <span style="font-weight: bold">.htaccess</span> (this file is in the main directory).
<?php endif; ?>

<p>&nbsp;</p>
<div align="center">
<form action="tiki-install.php" method="post">
	<input type="hidden" name="install_step" value="<?php if (isset ( $_POST['update'] )): ?>7<?php else: ?>6<?php endif; ?>" />
	<input type="submit" value=" Continue " />
<?php if ($this->_tpl_vars['multi']): ?>		<input type="hidden" name="multi" value="<?php echo $this->_tpl_vars['multi']; ?>
" /><?php endif; ?>
<?php if ($this->_tpl_vars['lang']): ?>		<input type="hidden" name="lang" value="<?php echo $this->_tpl_vars['lang']; ?>
" /><?php endif; ?>
</form>
</div>


<?php elseif ($this->_tpl_vars['install_step'] == '6'): ?>
<h1>General Settings</h1>
<form action="tiki-install.php" method="post">
<div style="float:left;width:60px"><img src="pics/large/icon-configuration48x48.png" alt="General Settings" /></div>
<div class="clearfix">
	<p>Complete these fields to configure common, general settings for your site. The information you enter here can be changed later.</p>
	<p>Refer to the <a href="http://doc.tikiwiki.org/Admin+Panels" target="_blank">documentation</a> for complete information on these, and other, settings.</p>
	<br />
	<fieldset><legend>General <a href="http://doc.tikiwiki.org/general+admin&amp;bl=y" target="_blank" title="Help"><img src="pics/icons/help.png" alt="Help" /></a></legend>
<div style="padding:5px;clear:both;"><label for="site_title">Site title:</label>
		<div style="margin-left:1em;"><input type="text" size="40" name="site_title" id="site_title" value="Tiki <?php echo $this->_tpl_vars['tiki_version_name']; ?>
" />
			<br /><em>This will appear in the browser title bar.</em></div>
		</div>
		<div style="padding:5px;clear:both;"><label for="sender_email">Sender email:</label>
			<div style="margin-left:1em;"><input type="text" size="40" name="sender_email" id="sender_email" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['prefs']['sender_email'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
			<br /><em>Email sent by your site will use this address.</em>
			</div>
		</div>
	</fieldset>
<br />
	<fieldset><legend>Secure Login <a href="http://doc.tikiwiki.org/login+config&amp;bl=y" target="_blank" title="Help"><img src="pics/icons/help.png" alt="Help" /></a></legend>
		<div style="padding:5px;clear:both"><label for="https_login">HTTPS login:</label>
	<select name="https_login" id="https_login" onchange="hidedisabled('httpsoptions',this.value);">
		<option value="disabled"<?php if ($this->_tpl_vars['prefs']['https_login'] == 'disabled'): ?> selected="selected"<?php endif; ?>>Disabled</option>
		<option value="allowed"<?php if ($this->_tpl_vars['prefs']['https_login'] == 'allowed'): ?> selected="selected"<?php endif; ?>>Allow secure (https) login</option>
		<option value="encouraged"<?php if ($this->_tpl_vars['prefs']['https_login'] == 'encouraged'): ?> selected="selected"<?php endif; ?>>Encourage secure (https) login</option>
		<option value="force_nocheck"<?php if ($this->_tpl_vars['prefs']['https_login'] == 'force_nocheck'): ?> selected="selected"<?php endif; ?>>Consider we are always in HTTPS, but do not check</option>
		<option value="required"<?php if ($this->_tpl_vars['prefs']['https_login'] == 'required'): ?> selected="selected"<?php endif; ?>>Require secure (https) login</option>
	</select>
		</div>
<div id="httpsoptions" style="display:<?php if ($this->_tpl_vars['prefs']['https_login'] == 'disabled' || $this->_tpl_vars['prefs']['https_login'] == ''): ?>none<?php else: ?>block<?php endif; ?>;">
		<div style="padding:5px">
			<label for="https_port">HTTPS port:</label> <input type="text" name="https_port" id="https_port" size="5" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['prefs']['https_port'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
		</div>
<div style="padding:5px;clear:both">
	<div style="float:left"><input type="checkbox" id="feature_show_stay_in_ssl_mode" name="feature_show_stay_in_ssl_mode" <?php if ($this->_tpl_vars['prefs']['feature_show_stay_in_ssl_mode'] == 'y'): ?>checked="checked"<?php endif; ?>/></div>
	<div style="margin-left:20px;"><label for="feature_show_stay_in_ssl_mode"> Users can choose to stay in SSL mode after an HTTPS login.</label></div>
</div>
<div style="padding:5px;clear:both">
	<div style="float:left"><input type="checkbox" id="feature_switch_ssl_mode" name="feature_switch_ssl_mode" <?php if ($this->_tpl_vars['prefs']['feature_switch_ssl_mode'] == 'y'): ?>checked="checked"<?php endif; ?>/></div>
	<div style="margin-left:20px;"><label for="feature_switch_ssl_mode">Users can switch between secured or standard mode at login.</label></div>
</div>
</div>
</fieldset>
<br />
<fieldset><legend>Administrator</legend>
<div style="padding:5px"><label for="admin_email">Admin email:</label>
	<div style="margin-left:1em;"><input type="text" size="40" name="admin_email" id="admin_email" />
	<br /><em>This is the email address for your administrator account.</em></div>
</div>
</fieldset>


</div>

<div align="center" style="margin-top:1em;">
<?php if ($this->_tpl_vars['multi']): ?>		<input type="hidden" name="multi" value="<?php echo $this->_tpl_vars['multi']; ?>
" /><?php endif; ?>
<?php if ($this->_tpl_vars['lang']): ?>		<input type="hidden" name="lang" value="<?php echo $this->_tpl_vars['lang']; ?>
" /><?php endif; ?>
	<input type="hidden" name="install_step" value="7" />
	<input type="hidden" name="general_settings" value="y" />
	<input type="submit" value=" Continue " />
</div>
</form>

<?php elseif ($this->_tpl_vars['install_step'] == '7'): ?>
<h1>Enter Your Tiki</h1>
<div style="float:left;width:60px"><img src="pics/large/stock_quit48x48.png" alt="Login" /></div>
<div class="clearfix">
	<p>The installation is complete! Your database has been configured and Tikiwiki is ready to run. </p>
	<p>TikiWiki is an opensource project, <em>you</em> can <a href='http://info.tikiwiki.org/Join+the+Community' target='_blank'>join the community</a> and help <a href='http://info.tikiwiki.org/tiki-index.php?page=Develop+Tiki' target='_blank'>develop Tiki</a>. </p>
	<p>
<?php if (isset ( $_POST['scratch'] )): ?>	If this is your first install, your admin password is <strong>admin</strong>. 
<?php endif; ?> 
	You can now log in into Tikiwiki as user <strong>admin</strong> and start configuring the application.
	</p>

<?php if (isset ( $_POST['scratch'] )): ?>
	<h3><img src="pics/icons/information.png" alt="Note" style="vertical-align:middle"/> Installation</h3>
	<p>If this is a first time installation, go to <strong>tiki-admin.php</strong> after login to start configuring your new Tiki installation.</p>
<?php endif; ?>

<?php if (isset ( $_POST['update'] )): ?>
	<h3><img src="pics/icons/information.png" alt="Note" style="vertical-align:middle"/> Upgrade</h3>
	<p>If this is an upgrade, clean the Tiki caches manually (the <strong>templates_c</strong> directory) or by using the <strong>Admin &gt; System</strong> option from the Admin menu.</p>
<?php endif; ?>
	<ul>
<?php if ($this->_tpl_vars['tikidb_is20']): ?>
		<li><a href="tiki-install.php?lockenter" class="link">Lock installer and enter Tiki</a>. (Recommended)</li>
		<li><a href="tiki-index.php" class="link">Do nothing and enter Tiki</a>.</li>
<?php endif; ?>
		<li><a href="tiki-install.php?reset=yes<?php if ($this->_tpl_vars['lang']): ?>&amp;lang=<?php echo $this->_tpl_vars['lang']; ?>
<?php endif; ?>" class="link">Restart the installer</a>.</li>
	</ul>
</div>
<?php endif; ?>
</div>
			</div>
				</div>
<div id="col2">
	<div class="content">
<?php if ($this->_tpl_vars['virt']): ?>
		<div class="box-shadow">
			<div class="box">
				<h3 class="box-title">MultiTiki Setup <a title='Help' href='http://doc.tikiwiki.org/MultiTiki' target="help"><img style="border:0" src='img/icons/help.gif' alt="Help" /></a></h3>
				<div class="clearfix box-data">
				<div><a href="tiki-install.php">default</a></div>
<?php $_from = $this->_tpl_vars['virt']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['i']):
?>
				<div>
					<tt><?php if ($this->_tpl_vars['i'] == 'y'): ?><strong style="color:#00CC00;">DB OK</strong><?php else: ?><strong style="color:#CC0000;">No DB</strong><?php endif; ?></tt>
<?php if ($this->_tpl_vars['k'] == $this->_tpl_vars['multi']): ?>
					<strong><?php echo $this->_tpl_vars['k']; ?>
</strong>
<?php else: ?>
					<a href="tiki-install.php?multi=<?php echo $this->_tpl_vars['k']; ?>
" class='linkmodule'><?php echo $this->_tpl_vars['k']; ?>
</a>
<?php endif; ?>
				</div>
<?php endforeach; endif; unset($_from); ?>

<br />
<div><strong>Adding a new host:</strong></div>
To add a new virtual host run the setup.sh with the domain name of the new host as a last parameter.

<?php if ($this->_tpl_vars['multi']): ?> <h2> (MultiTiki) <?php echo ((is_array($_tmp=@$this->_tpl_vars['multi'])) ? $this->_run_mod_handler('default', true, $_tmp, 'default') : smarty_modifier_default($_tmp, 'default')); ?>
 </h2> <?php endif; ?>				

	
				</div>
			</div>
		</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['dbcon'] == 'y' && ( $this->_tpl_vars['install_step'] == '0' || ! $this->_tpl_vars['install_step'] )): ?>
		<div class="box-shadow">
			<div class="box">
				<h3 class="box-title"><img src="pics/icons/information.png" alt="Information" style="vertical-align:middle" /> Upgrade</h3>
				<div class="clearfix box-data">
				Are you upgrading an existing Tiki site? 
				Go directly to the <strong>Install/Upgrade Profile</strong> step.
				</div>
			</div>
		</div>

	
<?php endif; ?>	



		<div class="box-shadow">
			<div class="box">
				<h3 class="box-title">Installation</h3>
				<div class="clearfix box-data">
				<ol>
					<li><?php if ($this->_tpl_vars['install_step'] == '0'): ?><strong><?php else: ?><a href="tiki-install.php?reset=y<?php if ($this->_tpl_vars['multi']): ?>&multi=<?php echo $this->_tpl_vars['multi']; ?>
<?php endif; ?><?php if ($this->_tpl_vars['lang']): ?>&lang=<?php echo $this->_tpl_vars['lang']; ?>
<?php endif; ?>" title="Welcome/Restart the installer."><?php endif; ?>Welcome<?php if ($this->_tpl_vars['install_step'] == '0'): ?></strong><?php else: ?></a><?php endif; ?></li>
					<li><?php if ($this->_tpl_vars['install_step'] == '1'): ?><strong><?php else: ?><a href="tiki-install.php?install_step=1<?php if ($this->_tpl_vars['multi']): ?>&multi=<?php echo $this->_tpl_vars['multi']; ?>
<?php endif; ?><?php if ($this->_tpl_vars['lang']): ?>&lang=<?php echo $this->_tpl_vars['lang']; ?>
<?php endif; ?>" title="License"><?php endif; ?>License<?php if ($this->_tpl_vars['install_step'] == '1'): ?></strong><?php else: ?></a><?php endif; ?></li>
					<li><?php if ($this->_tpl_vars['install_step'] == '2'): ?><strong><?php elseif ($this->_tpl_vars['install_step'] >= '3' || $this->_tpl_vars['dbcon'] == 'y'): ?><a href="tiki-install.php?install_step=2<?php if ($this->_tpl_vars['multi']): ?>&multi=<?php echo $this->_tpl_vars['multi']; ?>
<?php endif; ?><?php if ($this->_tpl_vars['lang']): ?>&lang=<?php echo $this->_tpl_vars['lang']; ?>
<?php endif; ?>" title="System Requirements"><?php endif; ?>System Requirements<?php if ($this->_tpl_vars['install_step'] == '2'): ?></strong><?php elseif ($this->_tpl_vars['install_step'] >= '3' || $this->_tpl_vars['dbcon'] == 'y'): ?></a><?php endif; ?></li>
					<li><?php if ($this->_tpl_vars['install_step'] == '3'): ?><strong><?php elseif ($this->_tpl_vars['dbcon'] == 'y'): ?><a href="tiki-install.php?install_step=3<?php if ($this->_tpl_vars['multi']): ?>&multi=<?php echo $this->_tpl_vars['multi']; ?>
<?php endif; ?><?php if ($this->_tpl_vars['lang']): ?>&lang=<?php echo $this->_tpl_vars['lang']; ?>
<?php endif; ?>" title="Database Connection"><?php endif; ?><?php if ($this->_tpl_vars['dbcon'] == 'y'): ?>Reset <?php endif; ?>Database Connection<?php if ($this->_tpl_vars['install_step'] == '3'): ?></strong><?php elseif ($this->_tpl_vars['dbcon'] == 'y'): ?></a><?php endif; ?></li>
					<li><?php if ($this->_tpl_vars['install_step'] == '4'): ?><strong><?php elseif ($this->_tpl_vars['dbcon'] == 'y' || isset ( $_POST['scratch'] ) || isset ( $_POST['update'] )): ?><a href="tiki-install.php?install_step=4<?php if ($this->_tpl_vars['multi']): ?>&multi=<?php echo $this->_tpl_vars['multi']; ?>
<?php endif; ?><?php if ($this->_tpl_vars['lang']): ?>&lang=<?php echo $this->_tpl_vars['lang']; ?>
<?php endif; ?>" title="Install<?php if ($this->_tpl_vars['tikidb_created']): ?> &amp; Upgrade<?php endif; ?> Profile"><?php endif; ?>Install<?php if ($this->_tpl_vars['tikidb_created']): ?>/Upgrade<?php endif; ?> Profile<?php if ($this->_tpl_vars['install_step'] == '4'): ?></strong><?php elseif (( $this->_tpl_vars['dbcon'] == 'y' ) || ( isset ( $_POST['scratch'] ) ) || ( isset ( $_POST['update'] ) )): ?></a><?php endif; ?></li>
					<li><?php if ($this->_tpl_vars['install_step'] == '5'): ?><strong><?php endif; ?>Review <?php if (isset ( $_POST['update'] )): ?>Upgrade<?php else: ?>Installation<?php endif; ?><?php if ($this->_tpl_vars['install_step'] == '5'): ?></strong><?php endif; ?></li>
					<li><?php if ($this->_tpl_vars['install_step'] == '6'): ?><strong><?php elseif ($this->_tpl_vars['tikidb_is20'] && ! isset ( $_POST['update'] )): ?><a href="tiki-install.php?install_step=6<?php if ($this->_tpl_vars['multi']): ?>&multi=<?php echo $this->_tpl_vars['multi']; ?>
<?php endif; ?><?php if ($this->_tpl_vars['lang']): ?>&lang=<?php echo $this->_tpl_vars['lang']; ?>
<?php endif; ?>"><?php endif; ?>General Settings<?php if ($this->_tpl_vars['install_step'] == '6'): ?></strong><?php elseif ($this->_tpl_vars['tikidb_is20'] && ! isset ( $_POST['update'] )): ?></a><?php endif; ?></li>
					<li><?php if ($this->_tpl_vars['install_step'] == '7'): ?><strong><?php elseif ($this->_tpl_vars['tikidb_is20']): ?><a href="tiki-install.php?install_step=7<?php if ($this->_tpl_vars['multi']): ?>&multi=<?php echo $this->_tpl_vars['multi']; ?>
<?php endif; ?><?php if ($this->_tpl_vars['lang']): ?>&lang=<?php echo $this->_tpl_vars['lang']; ?>
<?php endif; ?>"><?php endif; ?>Enter Tiki<?php if ($this->_tpl_vars['install_step'] == '7'): ?></strong><?php elseif ($this->_tpl_vars['tikidb_is20']): ?></a><?php endif; ?></li>
				</ol>
				</div>
			</div>
		</div>
		<div class="box-shadow">
			<div class="box">
				<h3 class="box-title">Help</h3>
				<div class="clearfix box-data">
				<p><img src="favicon.png" alt="TikiWiki" style="vertical-align:middle;" /> <a href="http://www.tikiwiki.org" target="_blank">TikiWiki Web Site</a></p>
				<p><img src="pics/icons/book_open.png" alt="Documentation" style="vertical-align:middle;" /> <a href="http://doc.tikiwiki.org" target="_blank">Documentation</a></p>
				<p><img src="pics/icons/group.png" alt="Forums" style="vertical-align:middle;" /> <a href="http://www.tikiwiki.org/forums" target="_blank">Support Forums</a></p>
				</div>
			</div>
		</div>
	</div>
</div>			
			
	  	</div>
</div>
<hr />
<p align="center"><a href="http://www.tikiwiki.org" target="_blank" title="Powered by TikiWiki CMS/Groupware &#169; 2002&#8211;<?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%Y") : smarty_modifier_date_format($_tmp, "%Y")); ?>
 "><img src="img/tiki/tikibutton2.png" alt="Tikiwiki" style="width: 80px; height: 31px; border:0" /></a></p>