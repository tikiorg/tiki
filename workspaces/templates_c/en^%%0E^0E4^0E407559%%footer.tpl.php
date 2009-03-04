<?php /* Smarty version 2.6.22, created on 2009-03-04 13:06:12
         compiled from footer.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'button', 'footer.tpl', 5, false),array('function', 'eval', 'footer.tpl', 28, false),)), $this); ?>


<?php if (( ! isset ( $this->_tpl_vars['display'] ) || $this->_tpl_vars['display'] == '' )): ?>
	<?php if (count ( $this->_tpl_vars['phpErrors'] )): ?>
		<?php echo smarty_function_button(array('href' => "#",'_id' => "show-errors-button",'_onclick' => "flip('errors');return false;",'_text' => 'Show php error messages'), $this);?>

		<br />
		<div id="errors" style="display:<?php if (( isset ( $_SESSION['tiki_cookie_jar']['show_errors'] ) && $_SESSION['tiki_cookie_jar']['show_errors'] == 'y' ) || $this->_tpl_vars['prefs']['javascript_enabled'] != 'y'): ?>block<?php else: ?>none<?php endif; ?>;">
			<?php $_from = $this->_tpl_vars['phpErrors']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['err']):
?>
				<?php echo $this->_tpl_vars['err']; ?>

			<?php endforeach; endif; unset($_from); ?>
		</div>
	<?php endif; ?>

	<?php if ($this->_tpl_vars['tiki_p_admin'] == 'y' && $this->_tpl_vars['prefs']['feature_debug_console'] == 'y'): ?>
		

		<?php  include_once("tiki-debug_console.php");  ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "tiki-debug_console.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>
	
	<?php if ($this->_tpl_vars['prefs']['feature_phplayers'] == 'y' && isset ( $this->_tpl_vars['phplayers_LayersMenu'] )): ?>
		<?php echo $this->_tpl_vars['phplayers_LayersMenu']->printHeader(); ?>

		<?php echo $this->_tpl_vars['phplayers_LayersMenu']->printFooter(); ?>

	<?php endif; ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_endbody_code']): ?>
	<?php echo smarty_function_eval(array('var' => $this->_tpl_vars['prefs']['feature_endbody_code']), $this);?>

<?php endif; ?>
</body>
</html>