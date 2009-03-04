<?php /* Smarty version 2.6.22, created on 2009-03-04 13:06:21
         compiled from tiki-admin_bar.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'tr', 'tiki-admin_bar.tpl', 5, false),)), $this); ?>
<?php if ($this->_tpl_vars['prefs']['feature_magic'] == 'y' && $this->_tpl_vars['tiki_p_admin'] == 'y'): ?>
<div id="adminBar">
	<ul class="topLevelAdmin">
	<?php unset($this->_sections['top']);
$this->_sections['top']['name'] = 'top';
$this->_sections['top']['loop'] = is_array($_loop=$this->_tpl_vars['toplevelfeatures']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['top']['show'] = true;
$this->_sections['top']['max'] = $this->_sections['top']['loop'];
$this->_sections['top']['step'] = 1;
$this->_sections['top']['start'] = $this->_sections['top']['step'] > 0 ? 0 : $this->_sections['top']['loop']-1;
if ($this->_sections['top']['show']) {
    $this->_sections['top']['total'] = $this->_sections['top']['loop'];
    if ($this->_sections['top']['total'] == 0)
        $this->_sections['top']['show'] = false;
} else
    $this->_sections['top']['total'] = 0;
if ($this->_sections['top']['show']):

            for ($this->_sections['top']['index'] = $this->_sections['top']['start'], $this->_sections['top']['iteration'] = 1;
                 $this->_sections['top']['iteration'] <= $this->_sections['top']['total'];
                 $this->_sections['top']['index'] += $this->_sections['top']['step'], $this->_sections['top']['iteration']++):
$this->_sections['top']['rownum'] = $this->_sections['top']['iteration'];
$this->_sections['top']['index_prev'] = $this->_sections['top']['index'] - $this->_sections['top']['step'];
$this->_sections['top']['index_next'] = $this->_sections['top']['index'] + $this->_sections['top']['step'];
$this->_sections['top']['first']      = ($this->_sections['top']['iteration'] == 1);
$this->_sections['top']['last']       = ($this->_sections['top']['iteration'] == $this->_sections['top']['total']);
?>
		<li<?php if ($this->_tpl_vars['toplevel'] == $this->_tpl_vars['toplevelfeatures'][$this->_sections['top']['index']]['feature_id']): ?> class="current"<?php endif; ?>><a href="tiki-magic.php?featurechain=<?php echo $this->_tpl_vars['toplevelfeatures'][$this->_sections['top']['index']]['feature_path']; ?>
"><?php $this->_tag_stack[] = array('tr', array()); $_block_repeat=true;smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['toplevelfeatures'][$this->_sections['top']['index']]['feature_name']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></a></li>
	<?php endfor; endif; ?>
	<?php if ($this->_tpl_vars['feature']['feature_count'] > 0 && $this->_tpl_vars['templatename'] != 'tiki-magic'): ?>
		<li class="configureThis"><a href="tiki-magic.php?featurechain=<?php echo $this->_tpl_vars['feature']['feature_path']; ?>
">Configure <?php $this->_tag_stack[] = array('tr', array()); $_block_repeat=true;smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['feature']['feature_name']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></a></li>
	<?php endif; ?>
	</ul>
	<?php if ($this->_tpl_vars['secondlevel']): ?>
	<ul class="secondLevelAdmin">
		<?php unset($this->_sections['sec']);
$this->_sections['sec']['name'] = 'sec';
$this->_sections['sec']['loop'] = is_array($_loop=$this->_tpl_vars['secondlevel']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['sec']['show'] = true;
$this->_sections['sec']['max'] = $this->_sections['sec']['loop'];
$this->_sections['sec']['step'] = 1;
$this->_sections['sec']['start'] = $this->_sections['sec']['step'] > 0 ? 0 : $this->_sections['sec']['loop']-1;
if ($this->_sections['sec']['show']) {
    $this->_sections['sec']['total'] = $this->_sections['sec']['loop'];
    if ($this->_sections['sec']['total'] == 0)
        $this->_sections['sec']['show'] = false;
} else
    $this->_sections['sec']['total'] = 0;
if ($this->_sections['sec']['show']):

            for ($this->_sections['sec']['index'] = $this->_sections['sec']['start'], $this->_sections['sec']['iteration'] = 1;
                 $this->_sections['sec']['iteration'] <= $this->_sections['sec']['total'];
                 $this->_sections['sec']['index'] += $this->_sections['sec']['step'], $this->_sections['sec']['iteration']++):
$this->_sections['sec']['rownum'] = $this->_sections['sec']['iteration'];
$this->_sections['sec']['index_prev'] = $this->_sections['sec']['index'] - $this->_sections['sec']['step'];
$this->_sections['sec']['index_next'] = $this->_sections['sec']['index'] + $this->_sections['sec']['step'];
$this->_sections['sec']['first']      = ($this->_sections['sec']['iteration'] == 1);
$this->_sections['sec']['last']       = ($this->_sections['sec']['iteration'] == $this->_sections['sec']['total']);
?>
			<?php if (( $this->_tpl_vars['secondlevel'][$this->_sections['sec']['index']]['feature_type'] == 'feature' && $this->_tpl_vars['secondlevel'][$this->_sections['sec']['index']]['value'] == 'y' && $this->_tpl_vars['secondlevel'][$this->_sections['sec']['index']]['feature_count'] > 0 ) || $this->_tpl_vars['secondlevel'][$this->_sections['sec']['index']]['feature_type'] != 'feature'): ?>
		<li<?php if ($this->_tpl_vars['secondlevelId'] == $this->_tpl_vars['secondlevel'][$this->_sections['sec']['index']]['feature_id']): ?> class="current"<?php endif; ?>><a href="tiki-magic.php?featurechain=<?php echo $this->_tpl_vars['secondlevel'][$this->_sections['sec']['index']]['feature_path']; ?>
"><?php $this->_tag_stack[] = array('tr', array()); $_block_repeat=true;smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['secondlevel'][$this->_sections['sec']['index']]['feature_name']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></a></li>	
			<?php endif; ?>
		<?php endfor; endif; ?>
	</ul>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['thirdlevel']): ?>
	<ul class="secondLevelAdmin">
		<?php unset($this->_sections['sec']);
$this->_sections['sec']['name'] = 'sec';
$this->_sections['sec']['loop'] = is_array($_loop=$this->_tpl_vars['thirdlevel']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['sec']['show'] = true;
$this->_sections['sec']['max'] = $this->_sections['sec']['loop'];
$this->_sections['sec']['step'] = 1;
$this->_sections['sec']['start'] = $this->_sections['sec']['step'] > 0 ? 0 : $this->_sections['sec']['loop']-1;
if ($this->_sections['sec']['show']) {
    $this->_sections['sec']['total'] = $this->_sections['sec']['loop'];
    if ($this->_sections['sec']['total'] == 0)
        $this->_sections['sec']['show'] = false;
} else
    $this->_sections['sec']['total'] = 0;
if ($this->_sections['sec']['show']):

            for ($this->_sections['sec']['index'] = $this->_sections['sec']['start'], $this->_sections['sec']['iteration'] = 1;
                 $this->_sections['sec']['iteration'] <= $this->_sections['sec']['total'];
                 $this->_sections['sec']['index'] += $this->_sections['sec']['step'], $this->_sections['sec']['iteration']++):
$this->_sections['sec']['rownum'] = $this->_sections['sec']['iteration'];
$this->_sections['sec']['index_prev'] = $this->_sections['sec']['index'] - $this->_sections['sec']['step'];
$this->_sections['sec']['index_next'] = $this->_sections['sec']['index'] + $this->_sections['sec']['step'];
$this->_sections['sec']['first']      = ($this->_sections['sec']['iteration'] == 1);
$this->_sections['sec']['last']       = ($this->_sections['sec']['iteration'] == $this->_sections['sec']['total']);
?>
			<?php if (( $this->_tpl_vars['thirdlevel'][$this->_sections['sec']['index']]['feature_type'] == 'feature' && $this->_tpl_vars['thirdlevel'][$this->_sections['sec']['index']]['value'] == 'y' && $this->_tpl_vars['thirdlevel'][$this->_sections['sec']['index']]['feature_count'] > 0 ) || $this->_tpl_vars['thirdlevel'][$this->_sections['sec']['index']]['feature_type'] != 'feature'): ?>
		<li<?php if ($this->_tpl_vars['thirdlevelId'] == $this->_tpl_vars['thirdlevel'][$this->_sections['sec']['index']]['feature_id']): ?> class="current"<?php endif; ?>><a href="tiki-magic.php?featurechain=<?php echo $this->_tpl_vars['thirdlevel'][$this->_sections['sec']['index']]['feature_path']; ?>
"><?php $this->_tag_stack[] = array('tr', array()); $_block_repeat=true;smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['thirdlevel'][$this->_sections['sec']['index']]['feature_name']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></a></li>	
			<?php endif; ?>
		<?php endfor; endif; ?>
	</ul>
	<?php endif; ?>
</div>
<?php endif; ?>