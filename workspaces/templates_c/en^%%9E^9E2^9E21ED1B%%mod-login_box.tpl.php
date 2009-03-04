<?php /* Smarty version 2.6.22, created on 2009-03-04 13:06:20
         compiled from modules/mod-login_box.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'tikimodule', 'modules/mod-login_box.tpl', 18, false),array('modifier', 'userlink', 'modules/mod-login_box.tpl', 20, false),array('modifier', 'count', 'modules/mod-login_box.tpl', 36, false),array('modifier', 'escape', 'modules/mod-login_box.tpl', 88, false),array('function', 'help', 'modules/mod-login_box.tpl', 30, false),array('function', 'icon', 'modules/mod-login_box.tpl', 107, false),)), $this); ?>

<?php if ($this->_tpl_vars['do_not_show_login_box'] != 'y'): ?>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
<?php echo '
function capLock(e){
 kc = e.keyCode?e.keyCode:e.which;
 sk = e.shiftKey?e.shiftKey:((kc == 16)?true:false);
 if(((kc >= 65 && kc <= 90) && !sk)||((kc >= 97 && kc <= 122) && sk))
  document.getElementById(\'divCapson\').style.visibility = \'visible\';
 else
  document.getElementById(\'divCapson\').style.visibility = \'hidden\';
}
'; ?>

//--><!]]>
</script>
<?php if (! isset ( $this->_tpl_vars['tpl_module_title'] )): ?><?php $this->assign('tpl_module_title', 'Login'); ?><?php endif; ?>
<?php $this->_tag_stack[] = array('tikimodule', array('error' => $this->_tpl_vars['module_params']['error'],'title' => $this->_tpl_vars['tpl_module_title'],'name' => 'login_box','flip' => $this->_tpl_vars['module_params']['flip'],'decorations' => $this->_tpl_vars['module_params']['decorations'],'nobox' => $this->_tpl_vars['module_params']['nobox'],'notitle' => $this->_tpl_vars['module_params']['notitle'])); $_block_repeat=true;smarty_block_tikimodule($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
    <?php if ($this->_tpl_vars['user']): ?>
      <div>Logged in as: <span style="white-space: nowrap"><?php echo ((is_array($_tmp=$this->_tpl_vars['user'])) ? $this->_run_mod_handler('userlink', true, $_tmp) : smarty_modifier_userlink($_tmp)); ?>
</span></div>
      <div style="text-align: center"><span class="button2"><a class="linkmodule" href="tiki-logout.php">Logout</a></span></div>
      <?php if ($this->_tpl_vars['tiki_p_admin'] == 'y'): ?>
        <form action="<?php if ($this->_tpl_vars['prefs']['https_login'] == 'encouraged' || $this->_tpl_vars['prefs']['https_login'] == 'required' || $this->_tpl_vars['prefs']['https_login'] == 'force_nocheck'): ?><?php echo $this->_tpl_vars['base_url_https']; ?>
<?php endif; ?><?php echo $this->_tpl_vars['prefs']['login_url']; ?>
" method="post"<?php if ($this->_tpl_vars['prefs']['desactive_login_autocomplete'] == 'y'): ?> autocomplete="off"<?php endif; ?>>
         <fieldset>
          <legend>Switch User</legend>
          <label for="login-switchuser">User:</label>
          <input type="hidden" name="su" value="1" />
          <input type="text" name="username" id="login-switchuser" size="<?php if (empty ( $this->_tpl_vars['module_params']['input_size'] )): ?>15<?php else: ?><?php echo $this->_tpl_vars['module_params']['input_size']; ?>
<?php endif; ?>" />
<?php if ($this->_tpl_vars['prefs']['feature_help'] == 'y'): ?>
		 <?php echo smarty_function_help(array('url' => "Switch+User",'desc' => 'Help'), $this);?>

<?php endif; ?>
          <div style="text-align: center"><button type="submit" name="actsu">Switch</button></div>
         </fieldset>
        </form>
      <?php endif; ?>
	  <?php if ($this->_tpl_vars['prefs']['auth_method'] == 'openid' && count($this->_tpl_vars['openid_userlist']) > 1): ?>
        <form method="get" action="tiki-login_openid.php">
		  <fieldset>
		  	<legend>Switch user</legend>
			<select name="select">
			<?php $_from = $this->_tpl_vars['openid_userlist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['username']):
?>
				<option<?php if ($this->_tpl_vars['username'] == $this->_tpl_vars['user']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['username']; ?>
</option>
			<?php endforeach; endif; unset($_from); ?>
			</select>
			<input type="hidden" name="action" value="select"/>
			<input type="submit" value="Go"/>
		  </fieldset>
		</form>
	  <?php endif; ?>
      <?php elseif ($this->_tpl_vars['prefs']['auth_method'] == 'cas' && $this->_tpl_vars['showloginboxes'] != 'y'): ?>
		<b><a class="linkmodule" href="tiki-login.php?user">Login through CAS</a></b>
		<?php if ($this->_tpl_vars['prefs']['cas_skip_admin'] == 'y'): ?>
		<br /><a class="linkmodule" href="tiki-login_scr.php?user=admin">Login as admin</a>
      <?php endif; ?>
      <?php elseif ($this->_tpl_vars['prefs']['auth_method'] == 'shib' && $this->_tpl_vars['showloginboxes'] != 'y'): ?>
		<b><a class="linkmodule" href="tiki-login.php">Login through Shibboleth</a></b>
		<?php if ($this->_tpl_vars['prefs']['shib_skip_admin'] == 'y'): ?>
		<br /><a class="linkmodule" href="tiki-login_scr.php?user=admin">Login as admin</a>
      <?php endif; ?>
    <?php else: ?>
     <form name="loginbox" action="<?php if ($this->_tpl_vars['prefs']['https_login'] == 'encouraged' || $this->_tpl_vars['prefs']['https_login'] == 'required' || $this->_tpl_vars['prefs']['https_login'] == 'force_nocheck'): ?><?php echo $this->_tpl_vars['base_url_https']; ?>
<?php endif; ?><?php echo $this->_tpl_vars['prefs']['login_url']; ?>
" method="post" <?php if ($this->_tpl_vars['prefs']['feature_challenge'] == 'y'): ?>onsubmit="doChallengeResponse()"<?php endif; ?><?php if ($this->_tpl_vars['prefs']['desactive_login_autocomplete'] == 'y'): ?> autocomplete="off"<?php endif; ?>> 
     <?php if ($this->_tpl_vars['prefs']['feature_challenge'] == 'y'): ?>
     <script type='text/javascript' src="lib/md5.js"></script>   
     <?php echo '
     <script type=\'text/javascript\'>
     <!--
     function doChallengeResponse() {
       hashstr = document.loginbox.user.value +
       document.loginbox.pass.value +
       document.loginbox.email.value;
       str = document.loginbox.user.value + 
       MD5(hashstr) +
       document.loginbox.challenge.value;
       document.loginbox.response.value = MD5(str);
       document.loginbox.pass.value=\'\';
       /*
       document.login.password.value = "";
       document.logintrue.username.value = document.login.username.value;
       document.logintrue.response.value = MD5(str);
       document.logintrue.submit();
       */
       document.loginbox.submit();
       return false;
     }
     // -->
    </script>
    '; ?>

     <input type="hidden" name="challenge" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['challenge'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
     <input type="hidden" name="response" value="" />
     <?php endif; ?>
        <fieldset>
          <legend>Login as&hellip;</legend>
            <div><label for="login-user"><?php if ($this->_tpl_vars['prefs']['login_is_email'] == 'y'): ?>Email<?php else: ?>User<?php endif; ?>:</label><br />
		<?php if ($this->_tpl_vars['loginuser'] == ''): ?>
              <input type="text" name="user" id="login-user" size="<?php if (empty ( $this->_tpl_vars['module_params']['input_size'] )): ?>15<?php else: ?><?php echo $this->_tpl_vars['module_params']['input_size']; ?>
<?php endif; ?>" />
	  <script type="text/javascript">document.getElementById('login-user').focus();</script>
		<?php else: ?>
		      <input type="hidden" name="user" id="login-user" value="<?php echo $this->_tpl_vars['loginuser']; ?>
" /><b><?php echo $this->_tpl_vars['loginuser']; ?>
</b>
		<?php endif; ?></div>
		<script type="text/javascript">document.getElementById('login-user').focus();</script>
          <?php if ($this->_tpl_vars['prefs']['feature_challenge'] == 'y'): ?> <!-- quick hack to make challenge/response work until 1.8 tiki auth overhaul -->
          <div><label for="login-email">eMail:</label><br />
          <input type="text" name="email" id="login-email" size="<?php if (empty ( $this->_tpl_vars['module_params']['input_size'] )): ?>15<?php else: ?><?php echo $this->_tpl_vars['module_params']['input_size']; ?>
<?php endif; ?>" /></div>
          <?php endif; ?>
          <div><label for="login-pass">Password:</label><br />
          <input onkeypress="capLock(event)" type="password" name="pass" id="login-pass" size="<?php if (empty ( $this->_tpl_vars['module_params']['input_size'] )): ?>15<?php else: ?><?php echo $this->_tpl_vars['module_params']['input_size']; ?>
<?php endif; ?>" />
		  <div id="divCapson" style="visibility:hidden"><?php echo smarty_function_icon(array('_id' => 'error','style' => "vertical-align:middle"), $this);?>
 CapsLock is on.</div>
		  </div>
          <?php if ($this->_tpl_vars['prefs']['rememberme'] != 'disabled'): ?>
            <?php if ($this->_tpl_vars['prefs']['rememberme'] == 'always'): ?>
              <input type="hidden" name="rme" id="login-remember" value="on" />
            <?php else: ?>
              <div style="text-align: center"><input type="checkbox" name="rme" id="login-remember" value="on" /><label for="login-remember">Remember me</label> (for <?php if ($this->_tpl_vars['prefs']['remembertime'] == 300): ?>5 minutes<?php elseif ($this->_tpl_vars['prefs']['remembertime'] == 900): ?>15 minutes<?php elseif ($this->_tpl_vars['prefs']['remembertime'] == 1800): ?>30 minutes<?php elseif ($this->_tpl_vars['prefs']['remembertime'] == 3600): ?>1 hour<?php elseif ($this->_tpl_vars['prefs']['remembertime'] == 7200): ?>2 hours<?php elseif ($this->_tpl_vars['prefs']['remembertime'] == 36000): ?>10 hours<?php elseif ($this->_tpl_vars['prefs']['remembertime'] == 72000): ?>20 hours<?php elseif ($this->_tpl_vars['prefs']['remembertime'] == 86400): ?> 1 day<?php elseif ($this->_tpl_vars['prefs']['remembertime'] == 604800): ?>1 week<?php elseif ($this->_tpl_vars['prefs']['remembertime'] == 2629743): ?>1 month<?php elseif ($this->_tpl_vars['prefs']['remembertime'] == 31556926): ?>1 year<?php endif; ?>)
			  </div>
            <?php endif; ?>
          <?php endif; ?>
          <div style="text-align: center"><input class="button submit" type="submit" name="login" value="Login" /></div>
       </fieldset>
          
          <?php if ($this->_tpl_vars['prefs']['forgotPass'] == 'y' && $this->_tpl_vars['prefs']['allowRegister'] == 'y' && $this->_tpl_vars['prefs']['change_password'] == 'y'): ?>
            <div>[ <a class="linkmodule" href="tiki-register.php" title="Click here to register">Register</a> | <a class="linkmodule" href="tiki-remind_password.php" title="Click here if you've forgotten your password">I forgot my pass</a> ]</div>
          <?php endif; ?>
          <?php if ($this->_tpl_vars['prefs']['forgotPass'] == 'y' && $this->_tpl_vars['prefs']['allowRegister'] != 'y' && $this->_tpl_vars['prefs']['change_password'] == 'y'): ?>
            <div><a class="linkmodule" href="tiki-remind_password.php" title="Click here if you've forgotten your password">I forgot my password</a></div>
          <?php endif; ?>
          <?php if (( $this->_tpl_vars['prefs']['forgotPass'] != 'y' || $this->_tpl_vars['prefs']['change_password'] != 'y' ) && $this->_tpl_vars['prefs']['allowRegister'] == 'y'): ?>
            <div><a class="linkmodule" href="tiki-register.php" title="Click here to register">Register</a></div>
          <?php endif; ?>
          <?php if (( $this->_tpl_vars['prefs']['forgotPass'] != 'y' || $this->_tpl_vars['prefs']['change_password'] != 'y' ) && $this->_tpl_vars['prefs']['allowRegister'] != 'y'): ?>
          &nbsp;
          <?php endif; ?>
          <?php if ($this->_tpl_vars['prefs']['feature_switch_ssl_mode'] == 'y' && ( $this->_tpl_vars['prefs']['https_login'] == 'allowed' || $this->_tpl_vars['prefs']['https_login'] == 'encouraged' )): ?>
          <div>
            <a class="linkmodule" href="<?php echo $this->_tpl_vars['base_url_http']; ?>
<?php echo $this->_tpl_vars['prefs']['login_url']; ?>
" title="Click here to login using the default security protocol">Standard</a> |
            <a class="linkmodule" href="<?php echo $this->_tpl_vars['base_url_https']; ?>
<?php echo $this->_tpl_vars['prefs']['login_url']; ?>
" title="Click here to login using a secure protocol">Secure</a>
          </div>
          <?php endif; ?>
          <?php if ($this->_tpl_vars['prefs']['feature_show_stay_in_ssl_mode'] == 'y' && $this->_tpl_vars['show_stay_in_ssl_mode'] == 'y'): ?>
                <div><label for="login-stayssl">Stay in ssl Mode:</label>?
                <input type="checkbox" name="stay_in_ssl_mode" id="login-stayssl" <?php if ($this->_tpl_vars['stay_in_ssl_mode'] == 'y'): ?>checked="checked"<?php endif; ?> /></div>
          <?php endif; ?>

      <?php if ($this->_tpl_vars['prefs']['feature_show_stay_in_ssl_mode'] != 'y' || $this->_tpl_vars['show_stay_in_ssl_mode'] != 'y'): ?>
        <input type="hidden" name="stay_in_ssl_mode" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['stay_in_ssl_mode'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
      <?php endif; ?>
      
			<?php if ($this->_tpl_vars['use_intertiki_auth'] == 'y'): ?>
				<select name='intertiki'>
					<option value="">local account</option>
					<option value="">-----------</option>
					<?php $_from = $this->_tpl_vars['intertiki']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['i']):
?>
					<option value="<?php echo $this->_tpl_vars['k']; ?>
"><?php echo $this->_tpl_vars['k']; ?>
</option>
					<?php endforeach; endif; unset($_from); ?>
				</select>
			<?php endif; ?>
      </form>
    <?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['auth_method'] == 'openid' && ! $this->_tpl_vars['user']): ?>
		<form method="get" action="tiki-login_openid.php">
			<fieldset>
				<legend>OpenID Login</legend>
				<input class="openid_url" type="text" name="openid_url"/>
				<input type="submit" value="Go"/>
				<a class="linkmodule tikihelp" target="_blank" href="http://doc.tikiwiki.org/OpenID">What is OpenID?</a>

				
			</fieldset>
		</form>
	<?php endif; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_tikimodule($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
<?php endif; ?>