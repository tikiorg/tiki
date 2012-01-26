<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:02
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-login_box.tpl" */ ?>
<?php /*%%SmartyHeaderCode:241204f1e08e6c413e2-71549520%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5d85939182257f60d069b24381555b6f3d5a33fb' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\modules/mod-login_box.tpl',
      1 => 1323701902,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '241204f1e08e6c413e2-71549520',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_block_jq')) include 'lib/smarty_tiki\block.jq.php';
if (!is_callable('smarty_block_tikimodule')) include 'lib/smarty_tiki\block.tikimodule.php';
if (!is_callable('smarty_modifier_userlink')) include 'lib/smarty_tiki\modifier.userlink.php';
if (!is_callable('smarty_function_button')) include 'lib/smarty_tiki\function.button.php';
if (!is_callable('smarty_function_help')) include 'lib/smarty_tiki\function.help.php';
if (!is_callable('smarty_function_autocomplete')) include 'lib/smarty_tiki\function.autocomplete.php';
if (!is_callable('smarty_modifier_escape')) include 'lib/smarty_tiki\modifier.escape.php';
if (!is_callable('smarty_block_remarksbox')) include 'lib/smarty_tiki\block.remarksbox.php';
if (!is_callable('smarty_function_icon')) include 'lib/smarty_tiki\function.icon.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-login_box.tpl -->
<?php $_smarty_tpl->smarty->_tag_stack[] = array('jq', array('notonready'=>true)); $_block_repeat=true; smarty_block_jq(array('notonready'=>true), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

function capLock(e, el){
	kc = e.keyCode ? e.keyCode : e.which;
	sk = e.shiftKey ? e.shiftKey : (kc == 16 ? true : false);
	if ((kc >= 65 && kc <= 90 && !sk) || (kc >= 97 && kc <= 122 && sk)) {
		$('.divCapson', $(el).parents('div:first')).show();
	} else {
		$('.divCapson', $(el).parents('div:first')).hide();
	}
}
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_jq(array('notonready'=>true), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php if (!isset($_smarty_tpl->getVariable('tpl_module_title',null,true,false)->value)){?><?php $_smarty_tpl->tpl_vars['tpl_module_title'] = new Smarty_variable("Log in", null, null);?><?php }?>
<?php if (!isset($_smarty_tpl->getVariable('module_params',null,true,false)->value)){?><?php $_smarty_tpl->tpl_vars['module_params'] = new Smarty_variable(' ', null, null);?><?php }?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('tikimodule', array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>"login_box",'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle'])); $_block_repeat=true; smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>"login_box",'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	<?php if ($_smarty_tpl->getVariable('mode')->value=="header"){?><div class="siteloginbar<?php if ($_smarty_tpl->getVariable('user')->value){?> logged-in<?php }?>"><?php }?>
	<?php if (isset($_smarty_tpl->getVariable('user',null,true,false)->value)&&$_smarty_tpl->getVariable('user')->value){?>
		<?php if (empty($_smarty_tpl->getVariable('mode',null,true,false)->value)||$_smarty_tpl->getVariable('mode')->value=="module"){?>
			<div>Logged in as: <span style="white-space: nowrap"><?php echo smarty_modifier_userlink($_smarty_tpl->getVariable('user')->value);?>
</span></div>
			<div style="text-align: center;">
				<?php echo smarty_function_button(array('href'=>"tiki-logout.php",'_text'=>"Log out"),$_smarty_tpl);?>

			</div>
			<?php if ($_smarty_tpl->getVariable('tiki_p_admin')->value=='y'){?>
				<form action="<?php if ($_smarty_tpl->getVariable('prefs')->value['https_login']=='encouraged'||$_smarty_tpl->getVariable('prefs')->value['https_login']=='required'||$_smarty_tpl->getVariable('prefs')->value['https_login']=='force_nocheck'){?><?php echo $_smarty_tpl->getVariable('base_url_https')->value;?>
<?php }?><?php echo $_smarty_tpl->getVariable('prefs')->value['login_url'];?>
" method="post"<?php if ($_smarty_tpl->getVariable('prefs')->value['desactive_login_autocomplete']=='y'){?> autocomplete="off"<?php }?>>
					<fieldset>
						<legend>Switch User</legend>
						<label for="login-switchuser_<?php echo $_smarty_tpl->getVariable('module_logo_instance')->value;?>
">Username:</label>
						<input type="hidden" name="su" value="1" />
						<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_help']=='y'){?>
							<?php echo smarty_function_help(array('url'=>"Switch+User",'desc'=>"Switch User:Enter user name and click 'Switch'.<br />Useful for testing permissions."),$_smarty_tpl);?>

						<?php }?>
						<input type="text" name="username" id="login-switchuser_<?php echo $_smarty_tpl->getVariable('module_logo_instance')->value;?>
" size="<?php if (empty($_smarty_tpl->getVariable('module_params',null,true,false)->value['input_size'])){?>15<?php }else{ ?><?php echo $_smarty_tpl->getVariable('module_params')->value['input_size'];?>
<?php }?>" />
						<div style="text-align: center"><button type="submit" name="actsu">Switch</button></div>
						<?php echo smarty_function_autocomplete(array('element'=>("#login-switchuser_").($_smarty_tpl->getVariable('module_logo_instance')->value),'type'=>"username"),$_smarty_tpl);?>

					</fieldset>
				</form>
			<?php }?>
		<?php }elseif($_smarty_tpl->getVariable('mode')->value=="header"){?>
			<span style="white-space: nowrap"><?php echo smarty_modifier_userlink($_smarty_tpl->getVariable('user')->value);?>
</span> <a href="tiki-logout.php" title="Log out">Log out</a>
		<?php }elseif($_smarty_tpl->getVariable('mode')->value=="popup"){?>
			<div class="siteloginbar_popup">
				<ul class="clearfix cssmenu_horiz">
					<li id="logout_link_<?php echo $_smarty_tpl->getVariable('module_logo_instance')->value;?>
"><div class="tabmark"><a href="tiki-logout.php" class="login_link">Log out<span class="sf-sub-indicator"> »</span></a></div>
						<ul class="siteloginbar_poppedup">
							<li class="tabcontent">
								<?php echo smarty_modifier_userlink($_smarty_tpl->getVariable('user')->value);?>
 <a href="tiki-logout.php" title="Log out">Log out</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		<?php }?>
		<?php if ($_smarty_tpl->getVariable('prefs')->value['auth_method']=='openid'&&count($_smarty_tpl->getVariable('openid_userlist')->value)>1){?>
		<form method="get" action="tiki-login_openid.php">
			<fieldset>
				<legend>Switch user</legend>
				<select name="select">
				<?php  $_smarty_tpl->tpl_vars['username'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('openid_userlist')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['username']->key => $_smarty_tpl->tpl_vars['username']->value){
?>
					<option<?php if ($_smarty_tpl->tpl_vars['username']->value==$_smarty_tpl->getVariable('user')->value){?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['username']->value;?>
</option>
				<?php }} ?>
				</select>
				<input type="hidden" name="action" value="select"/>
				<input type="submit" value="Go"/>
			</fieldset>
		</form>
		<?php }?>
	<?php }elseif($_smarty_tpl->getVariable('prefs')->value['auth_method']=='cas'&&$_smarty_tpl->getVariable('showloginboxes')->value!='y'){?>
		<b><a class="linkmodule" href="tiki-login.php?cas=y">Log in through CAS</a></b>
		<?php if ($_smarty_tpl->getVariable('prefs')->value['cas_skip_admin']=='y'){?>
			<br /><a class="linkmodule" href="tiki-login_scr.php?user=admin">Log in as admin</a>
		<?php }?>
	<?php }elseif($_smarty_tpl->getVariable('prefs')->value['auth_method']=='shib'&&$_smarty_tpl->getVariable('showloginboxes')->value!='y'){?>
		<b><a class="linkmodule" href="tiki-login.php">Log in through Shibboleth</a></b>
		<?php if ($_smarty_tpl->getVariable('prefs')->value['shib_skip_admin']=='y'){?>
			<br /><a class="linkmodule" href="tiki-login_scr.php?user=admin">Log in as admin</a>
		<?php }?>
	<?php }else{ ?>
		<?php $_smarty_tpl->tpl_vars['close_tags'] = new Smarty_variable('', null, null);?>
		<form name="loginbox" action="<?php if ($_smarty_tpl->getVariable('prefs')->value['https_login']=='encouraged'||$_smarty_tpl->getVariable('prefs')->value['https_login']=='required'||$_smarty_tpl->getVariable('prefs')->value['https_login']=='force_nocheck'){?><?php echo $_smarty_tpl->getVariable('base_url_https')->value;?>
<?php }?><?php echo $_smarty_tpl->getVariable('prefs')->value['login_url'];?>
"
				method="post" <?php if ($_smarty_tpl->getVariable('prefs')->value['feature_challenge']=='y'){?>onsubmit="doChallengeResponse()"<?php }?>
				<?php if ($_smarty_tpl->getVariable('prefs')->value['desactive_login_autocomplete']=='y'){?> autocomplete="off"<?php }?>> 
		<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_challenge']=='y'){?>
			<script type='text/javascript' src="lib/md5.js"></script>
			<?php $_smarty_tpl->smarty->_tag_stack[] = array('jq', array('notonready'=>true)); $_block_repeat=true; smarty_block_jq(array('notonready'=>true), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

function doChallengeResponse() {
	hashstr = document.loginbox.user.value +
				document.loginbox.pass.value +
				document.loginbox.email.value;
	str = document.loginbox.user.value + 
			MD5(hashstr) + document.loginbox.challenge.value;
	document.loginbox.response.value = MD5(str);
	document.loginbox.pass.value='';
	document.loginbox.submit();
	return false;
}
			<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_jq(array('notonready'=>true), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			<input type="hidden" name="challenge" value="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('challenge')->value);?>
" />
			<input type="hidden" name="response" value="" />
		<?php }?>
		<?php if (!empty($_smarty_tpl->getVariable('urllogin',null,true,false)->value)){?><input type="hidden" name="url" value="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('urllogin')->value);?>
" /><?php }?>
		<?php if ($_smarty_tpl->getVariable('mode')->value=="popup"){?>
			<div class="siteloginbar_popup">
				<ul class="clearfix cssmenu_horiz">
					<li id="logout_link_<?php echo $_smarty_tpl->getVariable('module_logo_instance')->value;?>
"><div class="tabmark"><a href="tiki-login.php" class="login_link">Log in<span class="sf-sub-indicator"> »</span></a></div>
						<ul class="siteloginbar_poppedup">
							<li class="tabcontent">
								<?php ob_start(); ?></li></ul></li></ul></div><?php echo $_smarty_tpl->getVariable('close_tags')->value;?>
<?php  $_smarty_tpl->assign("close_tags", ob_get_contents()); Smarty::$_smarty_vars['capture']['default']=ob_get_clean();?>
		<?php }?>
		<?php if ($_smarty_tpl->getVariable('module_params')->value['nobox']!='y'){?>
			<fieldset>
				<?php ob_start(); ?></fieldset><?php echo $_smarty_tpl->getVariable('close_tags')->value;?>
<?php  $_smarty_tpl->assign("close_tags", ob_get_contents()); Smarty::$_smarty_vars['capture']['default']=ob_get_clean();?>
		<?php }?>
		<?php if (!empty($_smarty_tpl->getVariable('error_login',null,true,false)->value)){?>
			<?php $_smarty_tpl->smarty->_tag_stack[] = array('remarksbox', array('type'=>'errors','title'=>"Error")); $_block_repeat=true; smarty_block_remarksbox(array('type'=>'errors','title'=>"Error"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

				<?php if ($_smarty_tpl->getVariable('error_login')->value==-5){?>Invalid username
				<?php }elseif($_smarty_tpl->getVariable('error_login')->value==-3){?>Invalid password
				<?php }else{ ?><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('error_login')->value);?>
<?php }?>
			<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_remarksbox(array('type'=>'errors','title'=>"Error"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

		<?php }?>
		<div>
			<?php if (!isset($_smarty_tpl->getVariable('module_logo_instance',null,true,false)->value)){?><?php $_smarty_tpl->tpl_vars['module_logo_instance'] = new Smarty_variable(' ', null, null);?><?php }?>
			<label for="login-user_<?php echo $_smarty_tpl->getVariable('module_logo_instance')->value;?>
"><?php if ($_smarty_tpl->getVariable('prefs')->value['login_is_email']=='y'){?>Email:<?php }else{ ?>Username:<?php }?></label>
			<?php if (!isset($_smarty_tpl->getVariable('loginuser',null,true,false)->value)||$_smarty_tpl->getVariable('loginuser')->value==''){?>
				<input type="text" name="user" id="login-user_<?php echo $_smarty_tpl->getVariable('module_logo_instance')->value;?>
" size="<?php if (empty($_smarty_tpl->getVariable('module_params',null,true,false)->value['input_size'])){?>15<?php }else{ ?><?php echo $_smarty_tpl->getVariable('module_params')->value['input_size'];?>
<?php }?>" <?php if (!empty($_smarty_tpl->getVariable('error_login',null,true,false)->value)){?> value="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('error_user')->value);?>
"<?php }elseif(!empty($_smarty_tpl->getVariable('adminuser',null,true,false)->value)){?> value="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('adminuser')->value);?>
"<?php }?>/>
				<?php $_smarty_tpl->smarty->_tag_stack[] = array('jq', array()); $_block_repeat=true; smarty_block_jq(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
if ($('#login-user_<?php echo $_smarty_tpl->getVariable('module_logo_instance')->value;?>
:visible').length) {$('#login-user_<?php echo $_smarty_tpl->getVariable('module_logo_instance')->value;?>
')[0].focus();}<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_jq(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			<?php }else{ ?>
				<input type="hidden" name="user" id="login-user_<?php echo $_smarty_tpl->getVariable('module_logo_instance')->value;?>
" value="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('loginuser')->value);?>
" /><b><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('loginuser')->value);?>
</b>
			<?php }?>
		</div>
		<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_challenge']=='y'){?> <!-- quick hack to make challenge/response work until 1.8 tiki auth overhaul -->
			<div>
				<label for="login-email_<?php echo $_smarty_tpl->getVariable('module_logo_instance')->value;?>
">eMail:</label>
				<input type="text" name="email" id="login-email_<?php echo $_smarty_tpl->getVariable('module_logo_instance')->value;?>
" size="<?php if (empty($_smarty_tpl->getVariable('module_params',null,true,false)->value['input_size'])){?>15<?php }else{ ?><?php echo $_smarty_tpl->getVariable('module_params')->value['input_size'];?>
<?php }?>" />
			</div>
		<?php }?>
		<div>
			<label for="login-pass_<?php echo $_smarty_tpl->getVariable('module_logo_instance')->value;?>
">Password:</label>
			<input onkeypress="capLock(event, this)" type="password" name="pass" id="login-pass_<?php echo $_smarty_tpl->getVariable('module_logo_instance')->value;?>
" size="<?php if (empty($_smarty_tpl->getVariable('module_params',null,true,false)->value['input_size'])){?>15<?php }else{ ?><?php echo $_smarty_tpl->getVariable('module_params')->value['input_size'];?>
<?php }?>" />
			<div class="divCapson" style="display:none;">
				<?php echo smarty_function_icon(array('_id'=>'error','style'=>"vertical-align:middle"),$_smarty_tpl);?>
 CapsLock is on.
			</div>
		</div>
		<?php if ($_smarty_tpl->getVariable('prefs')->value['rememberme']!='disabled'&&(empty($_smarty_tpl->getVariable('module_params',null,true,false)->value['remember'])||$_smarty_tpl->getVariable('module_params')->value['remember']!='n')){?>
			<?php if ($_smarty_tpl->getVariable('prefs')->value['rememberme']=='always'){?>
				<input type="hidden" name="rme" id="login-remember-module-input_<?php echo $_smarty_tpl->getVariable('module_logo_instance')->value;?>
" value="on" />
			<?php }else{ ?>
				<div style="text-align: center" class="rme">
					<input type="checkbox" name="rme" id="login-remember-module_<?php echo $_smarty_tpl->getVariable('module_logo_instance')->value;?>
" value="on" />
					<label for="login-remember-module_<?php echo $_smarty_tpl->getVariable('module_logo_instance')->value;?>
">Remember me</label>
					(for
					<?php if ($_smarty_tpl->getVariable('prefs')->value['remembertime']==300){?>
						5 minutes
					<?php }elseif($_smarty_tpl->getVariable('prefs')->value['remembertime']==900){?>
						15 minutes
					<?php }elseif($_smarty_tpl->getVariable('prefs')->value['remembertime']==1800){?>
						30 minutes
					<?php }elseif($_smarty_tpl->getVariable('prefs')->value['remembertime']==3600){?>
						1 hour
					<?php }elseif($_smarty_tpl->getVariable('prefs')->value['remembertime']==7200){?>
						2 hours
					<?php }elseif($_smarty_tpl->getVariable('prefs')->value['remembertime']==36000){?>
						10 hours
					<?php }elseif($_smarty_tpl->getVariable('prefs')->value['remembertime']==72000){?>
						20 hours
					<?php }elseif($_smarty_tpl->getVariable('prefs')->value['remembertime']==86400){?>
						1 day
					<?php }elseif($_smarty_tpl->getVariable('prefs')->value['remembertime']==604800){?>
						1 week
					<?php }elseif($_smarty_tpl->getVariable('prefs')->value['remembertime']==2629743){?>
						1 month
					<?php }elseif($_smarty_tpl->getVariable('prefs')->value['remembertime']==31556926){?>
						1 year
					<?php }?>
					)
					<?php ob_start(); ?></div><?php echo $_smarty_tpl->getVariable('close_tags')->value;?>
<?php  $_smarty_tpl->assign("close_tags", ob_get_contents()); Smarty::$_smarty_vars['capture']['default']=ob_get_clean();?>
			<?php }?>
		<?php }?>
		<div style="text-align: center">
			<input class="button submit" type="submit" name="login" value="Log in" />
		</div>
		<?php if ($_smarty_tpl->getVariable('module_params')->value['show_forgot']=='y'||$_smarty_tpl->getVariable('module_params')->value['show_register']=='y'){?>
			<div>
				<?php if ($_smarty_tpl->getVariable('module_params')->value['show_forgot']=='y'){?><div class="pass"><a  href="tiki-remind_password.php" title="Click here if you've forgotten your password">I forgot my password.</a></div><?php }?><?php if ($_smarty_tpl->getVariable('module_params')->value['show_register']=='y'){?><div class="register"><a  href="tiki-register.php" title="Click here to register">Register</a></div><?php }?>
			</div>
		<?php }else{ ?>
			&nbsp;
		<?php }?>
		<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_switch_ssl_mode']=='y'&&($_smarty_tpl->getVariable('prefs')->value['https_login']=='allowed'||$_smarty_tpl->getVariable('prefs')->value['https_login']=='encouraged')){?>
			<div>
				<a class="linkmodule" href="<?php echo $_smarty_tpl->getVariable('base_url_http')->value;?>
<?php echo $_smarty_tpl->getVariable('prefs')->value['login_url'];?>
" title="Click here to login using the default security protocol">Standard</a>
				<a class="linkmodule" href="<?php echo $_smarty_tpl->getVariable('base_url_https')->value;?>
<?php echo $_smarty_tpl->getVariable('prefs')->value['login_url'];?>
" title="Click here to login using a secure protocol">Secure</a>
			</div>
		<?php }?>
		<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_show_stay_in_ssl_mode']=='y'&&$_smarty_tpl->getVariable('show_stay_in_ssl_mode')->value=='y'){?>
			<div>
				<label for="login-stayssl_<?php echo $_smarty_tpl->getVariable('module_logo_instance')->value;?>
">Stay in SSL mode:</label>?
				<input type="checkbox" name="stay_in_ssl_mode" id="login-stayssl_<?php echo $_smarty_tpl->getVariable('module_logo_instance')->value;?>
" <?php if ($_smarty_tpl->getVariable('stay_in_ssl_mode')->value=='y'){?>checked="checked"<?php }?> />
			</div>
		<?php }?>
		
		<input type="hidden" name="stay_in_ssl_mode_present" value="y" />
		<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_show_stay_in_ssl_mode']!='y'||$_smarty_tpl->getVariable('show_stay_in_ssl_mode')->value!='y'){?>
			<input type="hidden" name="stay_in_ssl_mode" value="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('stay_in_ssl_mode')->value);?>
" />
		<?php }?>
		
		<?php if (isset($_smarty_tpl->getVariable('use_intertiki_auth',null,true,false)->value)&&$_smarty_tpl->getVariable('use_intertiki_auth')->value=='y'){?>
			<select name='intertiki'>
				<option value="">local account</option>
				<option value="">-----------</option>
				<?php  $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('intertiki')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['i']->key => $_smarty_tpl->tpl_vars['i']->value){
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['i']->key;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['k']->value;?>
</option>
				<?php }} ?>
			</select>
		<?php }?>
		
		<?php echo $_smarty_tpl->getVariable('close_tags')->value;?>

	</form>
<?php }?>
<?php if ($_smarty_tpl->getVariable('prefs')->value['auth_method']=='openid'&&!$_smarty_tpl->getVariable('user')->value&&(!isset($_smarty_tpl->getVariable('registration',null,true,false)->value)||$_smarty_tpl->getVariable('registration')->value!='y')){?>
	<form method="get" action="tiki-login_openid.php">
		<fieldset>
			<legend>OpenID Log in</legend>
			<input class="openid_url" type="text" name="openid_url"/>
			<input type="submit" value="Go"/>
			<a class="linkmodule tikihelp" target="_blank" href="http://doc.tiki.org/OpenID">What is OpenID?</a>
		</fieldset>
	</form>
<?php }?>
<?php if ($_smarty_tpl->getVariable('prefs')->value['socialnetworks_facebook_login']=='y'){?>
	<div style="text-align: center"><a href="tiki-socialnetworks.php?request_facebook=true"><img src="http://developers.facebook.com/images/devsite/login-button.png" /></a></div>
<?php }?>
<?php if ($_smarty_tpl->getVariable('mode')->value=="header"){?></div><?php }?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>"login_box",'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-login_box.tpl -->