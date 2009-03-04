<?php /* Smarty version 2.6.22, created on 2009-03-04 13:06:21
         compiled from tiki-site_header.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'eval', 'tiki-site_header.tpl', 7, false),array('function', 'banner', 'tiki-site_header.tpl', 52, false),)), $this); ?>


<div id="header-top">
<?php if ($this->_tpl_vars['prefs']['feature_sitemycode'] == 'y' && ( $this->_tpl_vars['prefs']['sitemycode_publish'] == 'y' || $this->_tpl_vars['tiki_p_admin'] == 'y' )): ?>
	<?php if ($this->_tpl_vars['prefs']['feature_sitead'] == 'y' && ( $this->_tpl_vars['prefs']['sitead_publish'] == 'y' || $this->_tpl_vars['tiki_p_admin'] == 'y' )): ?>
		<div id="sitead" class="floatright">
			<?php echo smarty_function_eval(array('var' => $this->_tpl_vars['prefs']['sitead']), $this);?>

		</div>
		<div id="customcodewith_ad">
			<?php echo smarty_function_eval(array('var' => $this->_tpl_vars['prefs']['sitemycode']), $this);?>

		</div>
		<?php else: ?>
		<div id="customcode">
			<?php echo smarty_function_eval(array('var' => $this->_tpl_vars['prefs']['sitemycode']), $this);?>

		</div>
	<?php endif; ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_sitelogo'] == 'y' && $this->_tpl_vars['prefs']['sitelogo_align'] != 'center'): ?>
<div class="clearfix" id="sioptions">
	<?php if ($this->_tpl_vars['prefs']['feature_sitelogo'] == 'y' && $this->_tpl_vars['prefs']['sitelogo_align'] == 'left'): ?>
		<?php if ($this->_tpl_vars['prefs']['feature_sitead'] == 'y' && ( $this->_tpl_vars['prefs']['sitead_publish'] == 'y' || $this->_tpl_vars['tiki_p_admin'] == 'y' )): ?>
		<div id="sitead" class="floatright"><?php echo smarty_function_eval(array('var' => $this->_tpl_vars['prefs']['sitead']), $this);?>
</div>
		<?php endif; ?>
		<div id="sitelogo" class="floatleft" <?php if ($this->_tpl_vars['prefs']['sitelogo_bgcolor'] != ''): ?>style="background-color: <?php echo $this->_tpl_vars['prefs']['sitelogo_bgcolor']; ?>
;"<?php endif; ?>>
		<?php if ($this->_tpl_vars['prefs']['sitelogo_src']): ?><a href="./" title="<?php echo $this->_tpl_vars['prefs']['sitelogo_title']; ?>
"><img src="<?php echo $this->_tpl_vars['prefs']['sitelogo_src']; ?>
" alt="<?php echo $this->_tpl_vars['prefs']['sitelogo_alt']; ?>
" style="border: none" /></a><?php endif; ?>
		<div id="sitetitles">
			<div id="sitetitle"><a href="index.php"><?php echo $this->_tpl_vars['prefs']['sitetitle']; ?>
</a></div>
			<div id="sitesubtitle"><?php echo $this->_tpl_vars['prefs']['sitesubtitle']; ?>
</div>
		</div>
	</div>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['feature_sitelogo'] == 'y' && $this->_tpl_vars['prefs']['sitelogo_align'] == 'right'): ?>
		<?php if ($this->_tpl_vars['prefs']['feature_sitead'] == 'y' && ( $this->_tpl_vars['prefs']['sitead_publish'] == 'y' || $this->_tpl_vars['tiki_p_admin'] == 'y' )): ?>
		<div id="sitead" class="floatleft"><?php echo smarty_function_eval(array('var' => $this->_tpl_vars['prefs']['sitead']), $this);?>
</div>
		<?php endif; ?>
		<div id="sitelogo" class="floatright"<?php if ($this->_tpl_vars['prefs']['sitelogo_bgcolor'] != ''): ?> style="background-color: <?php echo $this->_tpl_vars['prefs']['sitelogo_bgcolor']; ?>
;" <?php endif; ?>>
		<?php if ($this->_tpl_vars['prefs']['sitelogo_src']): ?><a href="./" title="<?php echo $this->_tpl_vars['prefs']['sitelogo_title']; ?>
"><img src="<?php echo $this->_tpl_vars['prefs']['sitelogo_src']; ?>
" alt="<?php echo $this->_tpl_vars['prefs']['sitelogo_alt']; ?>
" style="border: none" /></a><?php endif; ?>
	<div id="sitetitles">
			<div id="sitetitle"><a href="index.php"><?php echo $this->_tpl_vars['prefs']['sitetitle']; ?>
</a></div>
			<div id="sitesubtitle"><?php echo $this->_tpl_vars['prefs']['sitesubtitle']; ?>
</div>
		</div>
	</div>
	<?php endif; ?>
</div>
<?php endif; ?>


<?php if ($this->_tpl_vars['prefs']['feature_sitelogo'] == 'y' && $this->_tpl_vars['prefs']['sitelogo_align'] == 'center'): ?>
<div class="clearfix" id="sioptionscentered">
	<?php if ($this->_tpl_vars['prefs']['feature_sitead'] == 'y' && ( $this->_tpl_vars['prefs']['sitead_publish'] == 'y' || $this->_tpl_vars['tiki_p_admin'] == 'y' )): ?>
	<div class="floatright"><div id="bannertopright"><?php echo smarty_function_banner(array('zone' => 'topright'), $this);?>
</div></div><?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['feature_sitead'] == 'y' && ( $this->_tpl_vars['prefs']['sitead_publish'] == 'y' || $this->_tpl_vars['tiki_p_admin'] == 'y' )): ?>
		<div id="sitead" class="floatleft" ><?php echo smarty_function_eval(array('var' => $this->_tpl_vars['prefs']['sitead']), $this);?>
</div>
		<?php endif; ?>
	<div id="sitelogo"<?php if ($this->_tpl_vars['prefs']['sitelogo_bgcolor'] != ''): ?> style="background-color: <?php echo $this->_tpl_vars['prefs']['sitelogo_bgcolor']; ?>
;" <?php endif; ?>><?php if ($this->_tpl_vars['prefs']['sitelogo_src']): ?><a href="./" title="<?php echo $this->_tpl_vars['prefs']['sitelogo_title']; ?>
"><img src="<?php echo $this->_tpl_vars['prefs']['sitelogo_src']; ?>
" alt="<?php echo $this->_tpl_vars['prefs']['sitelogo_alt']; ?>
" style="border: none" /></a><?php endif; ?>
	<div id="sitetitles">
			<div id="sitetitle"><a href="index.php"><?php echo $this->_tpl_vars['prefs']['sitetitle']; ?>
</a></div>
			<div id="sitesubtitle"><?php echo $this->_tpl_vars['prefs']['sitesubtitle']; ?>
</div>
		</div>
	</div>	
</div>
<?php endif; ?>


<?php if ($this->_tpl_vars['prefs']['feature_sitelogo'] == 'n'): ?>
	<?php if ($this->_tpl_vars['prefs']['feature_sitead'] == 'y' && ( $this->_tpl_vars['prefs']['sitead_publish'] == 'y' || $this->_tpl_vars['tiki_p_admin'] == 'y' )): ?>
	<div align="center">
	<?php echo smarty_function_eval(array('var' => $this->_tpl_vars['prefs']['sitead']), $this);?>
</div>
	<?php endif; ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['filegals_manager'] == '' && $this->_tpl_vars['print_page'] != 'y'): ?>
<?php if ($this->_tpl_vars['prefs']['feature_site_login'] == 'y'): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "tiki-site_header_login.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<?php endif; ?>
<!--[if IE]><br style="clear:both; height: 0" /> <![endif]-->
</div>

<div class="clearfix" id="tiki-top">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "tiki-top_bar.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>

<?php if ($this->_tpl_vars['prefs']['feature_siteidentity'] == 'y' && $this->_tpl_vars['prefs']['feature_topbar_custom_code']): ?>
<div class="clearfix" id="topbar_custom_code">
	<?php echo smarty_function_eval(array('var' => $this->_tpl_vars['prefs']['feature_topbar_custom_code']), $this);?>

</div>
<?php endif; ?>