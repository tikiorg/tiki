<?php /* Smarty version 2.6.22, created on 2009-03-04 13:06:19
         compiled from modules/mod-quick_edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'tikimodule', 'modules/mod-quick_edit.tpl', 4, false),array('modifier', 'escape', 'modules/mod-quick_edit.tpl', 5, false),)), $this); ?>


<?php if (! isset ( $this->_tpl_vars['tpl_module_title'] )): ?><?php $this->assign('tpl_module_title', ($this->_tpl_vars['module_title'])); ?><?php endif; ?>
<?php $this->_tag_stack[] = array('tikimodule', array('error' => $this->_tpl_vars['module_params']['error'],'title' => $this->_tpl_vars['tpl_module_title'],'name' => 'quick_edit','flip' => $this->_tpl_vars['module_params']['flip'],'decorations' => $this->_tpl_vars['module_params']['decorations'],'nobox' => $this->_tpl_vars['module_params']['nobox'],'notitle' => $this->_tpl_vars['module_params']['notitle'])); $_block_repeat=true;smarty_block_tikimodule($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
<form method="get" action="<?php echo ((is_array($_tmp=$this->_tpl_vars['qe_action'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<?php if ($this->_tpl_vars['categId']): ?><input type="hidden" name="categId" value="<?php echo $this->_tpl_vars['categId']; ?>
" /><?php endif; ?>
<?php if ($this->_tpl_vars['templateId']): ?><input type="hidden" name="templateId" value="<?php echo $this->_tpl_vars['templateId']; ?>
" /><?php endif; ?>
<?php if ($this->_tpl_vars['mod_quickedit_heading']): ?><div class="bod-data"><?php echo $this->_tpl_vars['mod_quickedit_heading']; ?>
</div><?php endif; ?>
<input id="<?php echo $this->_tpl_vars['qefield']; ?>
" type="text" <?php if ($this->_tpl_vars['module_params']['size']): ?>size="<?php echo $this->_tpl_vars['size']; ?>
"<?php endif; ?>name="page" />
<input type="submit" name="quickedit" value="<?php echo $this->_tpl_vars['submit']; ?>
" />
</form>
<script type="text/javascript">
<?php if ($this->_tpl_vars['prefs']['feature_mootools'] == 'y'): ?>
<?php echo '
window.addEvent(\'domready\', function() {
	var o = new Autocompleter.Request.JSON(\''; ?>
<?php echo $this->_tpl_vars['qefield']; ?>
<?php echo '\', \'tiki-listpages.php?listonly\', {
		\'postVar\': \'find\'
	});
});
'; ?>

<?php endif; ?>
</script>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_tikimodule($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>