<?php /* Smarty version 2.6.22, created on 2009-03-04 13:06:19
         compiled from modules/user_module.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'tikimodule', 'modules/user_module.tpl', 2, false),array('function', 'eval', 'modules/user_module.tpl', 5, false),)), $this); ?>

<?php $this->_tag_stack[] = array('tikimodule', array('error' => $this->_tpl_vars['module_params']['error'],'title' => $this->_tpl_vars['user_title'],'name' => $this->_tpl_vars['user_module_name'],'flip' => $this->_tpl_vars['module_params']['flip'],'decorations' => $this->_tpl_vars['module_params']['decorations'],'overflow' => $this->_tpl_vars['module_params']['overflow'],'nobox' => $this->_tpl_vars['module_params']['nobox'],'notitle' => $this->_tpl_vars['module_params']['notitle'])); $_block_repeat=true;smarty_block_tikimodule($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>

<div id="<?php echo $this->_tpl_vars['user_module_name']; ?>
" <?php if (( isset ( $_COOKIE[$this->_tpl_vars['user_module_name']] ) && $_COOKIE[$this->_tpl_vars['user_module_name']] != 'c' ) || ! isset ( $_COOKIE[$this->_tpl_vars['user_module_name']] )): ?>style="display:block;"<?php else: ?>style="display:none;"<?php endif; ?>>
<?php echo smarty_function_eval(array('var' => $this->_tpl_vars['user_data']), $this);?>

</div>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_tikimodule($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>