<?php /* Smarty version 2.6.22, created on 2009-03-04 13:56:36
         compiled from tiki-admin-include-sefurl.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'remarksbox', 'tiki-admin-include-sefurl.tpl', 3, false),array('function', 'help', 'tiki-admin-include-sefurl.tpl', 15, false),array('modifier', 'escape', 'tiki-admin-include-sefurl.tpl', 38, false),)), $this); ?>

<?php if ($this->_tpl_vars['warning']): ?>
<?php $this->_tag_stack[] = array('remarksbox', array('type' => 'warning','title' => 'Warning')); $_block_repeat=true;smarty_block_remarksbox($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
	<?php echo $this->_tpl_vars['warning']; ?>

<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_remarksbox($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
<?php endif; ?>

<form class="admin" method="post" action="tiki-admin.php?page=sefurl">
<div style="padding:0.5em;clear:both">
	<div style="float:left;margin-right:1em;">
		<input type="checkbox" id="feature_sefurl" name="feature_sefurl" <?php if ($this->_tpl_vars['prefs']['feature_sefurl'] == 'y'): ?>checked="checked"<?php endif; ?> />
	</div>
	<div>
		<label for="feature_sefurl">Search engine friendly url</label>
		<?php if ($this->_tpl_vars['prefs']['feature_help'] == 'y'): ?> <?php echo smarty_function_help(array('url' => "Rewrite+Rules",'desc' => 'Search engine friendly url'), $this);?>
<?php endif; ?> <br />
	</div>
</div>
<div style="padding:0.5em;clear:both">
	<div style="float:left;margin-right:1em;">
		<input type="checkbox" id="feature_sefurl_filter" name="feature_sefurl_filter" <?php if ($this->_tpl_vars['prefs']['feature_sefurl_filter'] == 'y'): ?>checked="checked"<?php endif; ?> />
	</div>
	<div>
		<label for="feature_sefurl_filter">Search engine friendly url Postfilter</label>
		<?php if ($this->_tpl_vars['prefs']['feature_help'] == 'y'): ?> <?php echo smarty_function_help(array('url' => "Rewrite+Rules",'desc' => 'Search engine friendly url'), $this);?>
<?php endif; ?> <br />
	</div>
</div>
<div style="padding:0.5em;clear:both">
	<div>
		<label for="feature_sefurl_paths">List of Url Parameters that should go in the path</label>
		<?php echo ''; ?><?php ob_start(); ?><?php echo ''; ?><?php $_from = $this->_tpl_vars['prefs']['feature_sefurl_paths']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['path']):
        $this->_foreach['loop']['iteration']++;
?><?php echo ''; ?><?php echo $this->_tpl_vars['path']; ?><?php echo ''; ?><?php if (! ($this->_foreach['loop']['iteration'] == $this->_foreach['loop']['total'])): ?><?php echo '/'; ?><?php endif; ?><?php echo ''; ?><?php endforeach; endif; unset($_from); ?><?php echo ''; ?><?php $this->_smarty_vars['capture']['paths'] = ob_get_contents(); ob_end_clean(); ?><?php echo ''; ?>

		<input type="text" id="feature_sefurl_paths" name="feature_sefurl_paths" value="<?php echo ((is_array($_tmp=$this->_smarty_vars['capture']['paths'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
	</div>
</div>

<div class="heading input_submit_container" style="text-align: center;padding:1em;">
	 <input type="submit" name="save" value="Change Preferences" />
</div>
</form>