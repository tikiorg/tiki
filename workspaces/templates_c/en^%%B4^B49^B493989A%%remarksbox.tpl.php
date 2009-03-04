<?php /* Smarty version 2.6.22, created on 2009-03-04 13:09:01
         compiled from remarksbox.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'icon', 'remarksbox.tpl', 7, false),)), $this); ?>


<div class="rbox <?php echo $this->_tpl_vars['remarksbox_type']; ?>
">
<?php if ($this->_tpl_vars['remarksbox_title'] != ''): ?>
	<div class="rbox-title">
<?php if ($this->_tpl_vars['remarksbox_icon'] != 'none'): ?>
		<?php echo smarty_function_icon(array('_id' => $this->_tpl_vars['remarksbox_icon'],'style' => 'vertical-align: middle'), $this);?>

<?php endif; ?>
		<span><?php echo $this->_tpl_vars['remarksbox_title']; ?>
</span>
	</div>
<?php endif; ?>
	<div class="rbox-data<?php echo $this->_tpl_vars['remarksbox_highlight']; ?>
">
		<?php echo $this->_tpl_vars['remarksbox_content']; ?>

	</div>
</div>