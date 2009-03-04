<?php /* Smarty version 2.6.22, created on 2009-03-04 13:06:19
         compiled from modules/mod-assistant.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'eval', 'modules/mod-assistant.tpl', 2, false),array('block', 'tikimodule', 'modules/mod-assistant.tpl', 3, false),)), $this); ?>

<?php if (! isset ( $this->_tpl_vars['tpl_module_title'] )): ?><?php echo smarty_function_eval(array('assign' => 'tpl_module_title','var' => 'Tikiwiki Assistant'), $this);?>
<?php endif; ?>
<?php $this->_tag_stack[] = array('tikimodule', array('error' => $this->_tpl_vars['module_params']['error'],'title' => $this->_tpl_vars['tpl_module_title'],'name' => 'assistant','flip' => $this->_tpl_vars['module_params']['flip'],'decorations' => $this->_tpl_vars['module_params']['decorations'],'nobox' => $this->_tpl_vars['module_params']['nobox'],'notitle' => $this->_tpl_vars['module_params']['notitle'])); $_block_repeat=true;smarty_block_tikimodule($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
	<div align="center">
		<strong>Thank you for installing Tikiwiki!</strong>
	</div>
	<?php if ($this->_tpl_vars['tiki_p_admin'] == 'y'): ?>
	<p>
		<img src="pics/icons/arrow_small.png" alt="" style="border:0;margin-right:2px;vertical-align:middle" align="left" />
		<strong>To configure your Tiki</strong>:<br />
		Select <a class="link" href="tiki-admin.php" title="Admin Home">Admin &gt; Admin Home</a> from the menu.
	</p>
	<p>
		Read the <a class="link" href="http://doc.tikiwiki.org/Configuration" title="Tikiwiki Documentation" target="_blank">configuration documentation</a>.
	</p>
	<p>
		Watch the <a class="link" href="http://tikiwiki.org/TikiMovies" title="Demos" target="_blank">demo movies</a>.
	</p>
	<p>
		<img src="pics/icons/arrow_small.png" alt="" style="border:0;margin-right:2px;vertical-align:middle" align="left" />
		<strong>To remove this module</strong>:<br />
		Select <a class="link" href="tiki-admin_modules.php#leftmod" title="Admin Modules">Admin &gt; Modules</a> and remove the assistant module. You can also add other modules.
	</p>
	<p>
		<img src="pics/icons/arrow_small.png" alt="" style="border:0;margin-right:2px;vertical-align:middle" align="left" />
		<strong>To customize the menu</strong>:<br />
		Select <a class="link" href="tiki-admin_menus.php" title="Admin Menus">Admin &gt; Menus</a> and edit menu ID 42.<br />Or, create your own menu and add it to a module.
	</p>
	<hr />
	<?php else: ?>
	<p>
		<a href="tiki-login.php" title="Login"><img src="pics/icons/accept.png" alt="Login" style="border:0;margin-right:2px;vertical-align:middle" align="left" /></a>To begin configuring Tiki, please <a href="tiki-login.php" title="Login">login</a> as the Admin.
	</p>
	<?php endif; ?>
	<p>
		<a href="http://www.tikiwiki.org" title="The Tikiwiki Community" target="_blank"><img src="favicon.png" alt="The Tikiwiki Community" style="border:0;margin-right:2px;vertical-align:middle" align="left" /></a>To learn more, visit: <a href="http://www.tikiwiki.org" title="The Tikiwiki Community" target="_blank">http://www.tikiwiki.org</a>.
	</p>
	<p>
		<a href="http://doc.tikiwiki.org" title="Tikiwiki Documentation" target="_blank"><img src="pics/icons/help.png" alt="Tikiwiki Documentation" style="border:0px;margin-right:2px;vertical-align:middle" align="left" /></a>For help, visit <a href="http://doc.tikiwiki.org" title="Tikiwiki Documentation" target="_blank">http://doc.tikiwiki.org</a>.
	</p>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_tikimodule($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>