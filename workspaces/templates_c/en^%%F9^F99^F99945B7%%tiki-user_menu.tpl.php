<?php /* Smarty version 2.6.22, created on 2009-03-04 13:06:19
         compiled from tiki-user_menu.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'cat', 'tiki-user_menu.tpl', 6, false),array('modifier', 'escape', 'tiki-user_menu.tpl', 51, false),array('function', 'icon', 'tiki-user_menu.tpl', 30, false),array('block', 'tr', 'tiki-user_menu.tpl', 51, false),)), $this); ?>

<?php $this->assign('opensec', '0'); ?>
<?php $this->assign('sep', ''); ?>

<?php $_from = $this->_tpl_vars['menu_channels']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['pos'] => $this->_tpl_vars['chdata']):
?>
<?php $this->assign('cname', ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['menu_info']['menuId'])) ? $this->_run_mod_handler('cat', true, $_tmp, '__') : smarty_modifier_cat($_tmp, '__')))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['chdata']['position']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['chdata']['position']))); ?>

<?php if ($this->_tpl_vars['chdata']['type'] != 'o' && $this->_tpl_vars['chdata']['type'] != '-'): ?>

<?php if ($this->_tpl_vars['opensec'] > 0): ?>
<?php $this->assign('sectionType', $this->_tpl_vars['chdata']['type']); ?>
<?php if ($this->_tpl_vars['sectionType'] == 's' || $this->_tpl_vars['sectionType'] == 'r'): ?><?php $this->assign('sectionType', 0); ?><?php endif; ?>
<?php if ($this->_tpl_vars['opensec'] > $this->_tpl_vars['sectionType']): ?>
<?php $this->assign('m', $this->_tpl_vars['opensec']-$this->_tpl_vars['sectionType']); ?>
<?php unset($this->_sections['close']);
$this->_sections['close']['loop'] = is_array($_loop=$this->_tpl_vars['menu_channels']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['close']['name'] = 'close';
$this->_sections['close']['max'] = (int)$this->_tpl_vars['m'];
$this->_sections['close']['show'] = true;
if ($this->_sections['close']['max'] < 0)
    $this->_sections['close']['max'] = $this->_sections['close']['loop'];
$this->_sections['close']['step'] = 1;
$this->_sections['close']['start'] = $this->_sections['close']['step'] > 0 ? 0 : $this->_sections['close']['loop']-1;
if ($this->_sections['close']['show']) {
    $this->_sections['close']['total'] = min(ceil(($this->_sections['close']['step'] > 0 ? $this->_sections['close']['loop'] - $this->_sections['close']['start'] : $this->_sections['close']['start']+1)/abs($this->_sections['close']['step'])), $this->_sections['close']['max']);
    if ($this->_sections['close']['total'] == 0)
        $this->_sections['close']['show'] = false;
} else
    $this->_sections['close']['total'] = 0;
if ($this->_sections['close']['show']):

            for ($this->_sections['close']['index'] = $this->_sections['close']['start'], $this->_sections['close']['iteration'] = 1;
                 $this->_sections['close']['iteration'] <= $this->_sections['close']['total'];
                 $this->_sections['close']['index'] += $this->_sections['close']['step'], $this->_sections['close']['iteration']++):
$this->_sections['close']['rownum'] = $this->_sections['close']['iteration'];
$this->_sections['close']['index_prev'] = $this->_sections['close']['index'] - $this->_sections['close']['step'];
$this->_sections['close']['index_next'] = $this->_sections['close']['index'] + $this->_sections['close']['step'];
$this->_sections['close']['first']      = ($this->_sections['close']['iteration'] == 1);
$this->_sections['close']['last']       = ($this->_sections['close']['iteration'] == $this->_sections['close']['total']);
?>
	   </div>
<?php endfor; endif; ?>
<?php $this->assign('opensec', $this->_tpl_vars['sectionType']); ?>
<?php endif; ?>
<?php endif; ?>

<div class="separator<?php echo $this->_tpl_vars['sep']; ?>
<?php if ($this->_tpl_vars['chdata']['selected']): ?> selected<?php endif; ?><?php if ($this->_tpl_vars['chdata']['selectedAscendant']): ?> selectedAscendant<?php endif; ?>">
<?php if ($this->_tpl_vars['sep'] == 'line'): ?><?php $this->assign('sep', ''); ?><?php endif; ?>
<?php if ($this->_tpl_vars['menu_info']['type'] == 'e' || $this->_tpl_vars['menu_info']['type'] == 'd'): ?>
	<?php if ($this->_tpl_vars['prefs']['feature_menusfolderstyle'] == 'y'): ?>
	<?php $this->assign('icon_name', "icnmenu".($this->_tpl_vars['cname'])); ?>
	<a class='separator' href="javascript:icntoggle('menu<?php echo $this->_tpl_vars['cname']; ?>
');" title="Toggle options">
		<?php if ($this->_tpl_vars['menu_info']['type'] != 'd'): ?>
			<?php if (empty ( $this->_tpl_vars['menu_info']['icon'] )): ?>
				<?php echo smarty_function_icon(array('_id' => 'ofolder','alt' => 'Toggle','name' => ($this->_tpl_vars['icon_name'])), $this);?>

			<?php else: ?>
				<img src="<?php echo $this->_tpl_vars['menu_info']['oicon']; ?>
" alt='Toggle' name="<?php echo $this->_tpl_vars['icon_name']; ?>
" />
			<?php endif; ?>
		<?php else: ?>
			<?php if (empty ( $this->_tpl_vars['menu_info']['icon'] )): ?>
				<?php echo smarty_function_icon(array('_id' => 'folder','alt' => 'Toggle','name' => ($this->_tpl_vars['icon_name'])), $this);?>

			<?php else: ?>
				<img src="<?php echo $this->_tpl_vars['menu_info']['icon']; ?>
" alt='Toggle' name="<?php echo $this->_tpl_vars['icon_name']; ?>
" />
			<?php endif; ?>
		<?php endif; ?>
	</a>
	<?php else: ?>
	<a class='separator' href="javascript:toggle('menu<?php echo $this->_tpl_vars['cname']; ?>
');">[-]</a>
	<?php endif; ?>
<?php endif; ?> 
<?php if ($this->_tpl_vars['chdata']['url'] && $this->_tpl_vars['link_on_section'] == 'y'): ?>
<a href="<?php if ($this->_tpl_vars['prefs']['feature_sefurl'] == 'y' && $this->_tpl_vars['chdata']['sefurl']): ?><?php echo $this->_tpl_vars['chdata']['sefurl']; ?>
<?php else: ?><?php echo $this->_tpl_vars['chdata']['url']; ?>
<?php endif; ?>" class="separator">
<?php else: ?>
<a href="javascript:icntoggle('menu<?php echo $this->_tpl_vars['cname']; ?>
');" class="separator">
<?php endif; ?>
<?php if ($this->_tpl_vars['translate'] == 'n'): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['chdata']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<?php else: ?><?php $this->_tag_stack[] = array('tr', array()); $_block_repeat=true;smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['chdata']['name']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php endif; ?>
</a>
<?php if (( $this->_tpl_vars['menu_info']['type'] == 'e' || $this->_tpl_vars['menu_info']['type'] == 'd' ) && $this->_tpl_vars['prefs']['feature_menusfolderstyle'] != 'y'): ?><a class='separator' href="javascript:toggle('menu<?php echo $this->_tpl_vars['cname']; ?>
');">[+]</a><?php endif; ?> 
</div> 

<?php $this->assign('opensec', $this->_tpl_vars['opensec']+1); ?>
<?php if ($this->_tpl_vars['menu_info']['type'] == 'e' || $this->_tpl_vars['menu_info']['type'] == 'd'): ?>
<div class="menuSection" <?php if ($this->_tpl_vars['menu_info']['type'] == 'd' && $_COOKIE['menu'] != '' && $this->_tpl_vars['prefs']['javascript_enabled'] != 'n'): ?>style="display:none;"<?php else: ?>style="display:block;"<?php endif; ?> id='menu<?php echo $this->_tpl_vars['cname']; ?>
'>
<?php else: ?>
<div class="menuSection">
<?php endif; ?>


<?php elseif ($this->_tpl_vars['chdata']['type'] == 'o'): ?>
<div class="option<?php echo $this->_tpl_vars['sep']; ?>
<?php if ($this->_tpl_vars['chdata']['selected']): ?> selected<?php endif; ?>"><a href="<?php if ($this->_tpl_vars['prefs']['feature_sefurl'] == 'y' && $this->_tpl_vars['chdata']['sefurl']): ?><?php echo $this->_tpl_vars['chdata']['sefurl']; ?>
<?php else: ?><?php echo $this->_tpl_vars['chdata']['url']; ?>
<?php endif; ?>" class="linkmenu"><?php if ($this->_tpl_vars['translate'] == 'n'): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['chdata']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<?php else: ?><?php $this->_tag_stack[] = array('tr', array()); $_block_repeat=true;smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['chdata']['name']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php endif; ?></a></div>
<?php if ($this->_tpl_vars['sep'] == 'line'): ?><?php $this->assign('sep', ''); ?><?php endif; ?>


<?php elseif ($this->_tpl_vars['chdata']['type'] == '-'): ?>
<?php if ($this->_tpl_vars['opensec'] > 0): ?></div><?php $this->assign('opensec', $this->_tpl_vars['opensec']-1); ?><?php endif; ?>
<?php $this->assign('sep', 'line'); ?>
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>

<?php if ($this->_tpl_vars['opensec'] > 0): ?>
<?php unset($this->_sections['close']);
$this->_sections['close']['loop'] = is_array($_loop=$this->_tpl_vars['menu_channels']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['close']['name'] = 'close';
$this->_sections['close']['max'] = (int)$this->_tpl_vars['opensec'];
$this->_sections['close']['show'] = true;
if ($this->_sections['close']['max'] < 0)
    $this->_sections['close']['max'] = $this->_sections['close']['loop'];
$this->_sections['close']['step'] = 1;
$this->_sections['close']['start'] = $this->_sections['close']['step'] > 0 ? 0 : $this->_sections['close']['loop']-1;
if ($this->_sections['close']['show']) {
    $this->_sections['close']['total'] = min(ceil(($this->_sections['close']['step'] > 0 ? $this->_sections['close']['loop'] - $this->_sections['close']['start'] : $this->_sections['close']['start']+1)/abs($this->_sections['close']['step'])), $this->_sections['close']['max']);
    if ($this->_sections['close']['total'] == 0)
        $this->_sections['close']['show'] = false;
} else
    $this->_sections['close']['total'] = 0;
if ($this->_sections['close']['show']):

            for ($this->_sections['close']['index'] = $this->_sections['close']['start'], $this->_sections['close']['iteration'] = 1;
                 $this->_sections['close']['iteration'] <= $this->_sections['close']['total'];
                 $this->_sections['close']['index'] += $this->_sections['close']['step'], $this->_sections['close']['iteration']++):
$this->_sections['close']['rownum'] = $this->_sections['close']['iteration'];
$this->_sections['close']['index_prev'] = $this->_sections['close']['index'] - $this->_sections['close']['step'];
$this->_sections['close']['index_next'] = $this->_sections['close']['index'] + $this->_sections['close']['step'];
$this->_sections['close']['first']      = ($this->_sections['close']['iteration'] == 1);
$this->_sections['close']['last']       = ($this->_sections['close']['iteration'] == $this->_sections['close']['total']);
?>
	</div>
<?php endfor; endif; ?>
<?php $this->assign('opensec', 0); ?>
<?php endif; ?>


<?php if ($this->_tpl_vars['menu_info']['type'] == 'e' || $this->_tpl_vars['menu_info']['type'] == 'd'): ?>
<script type='text/javascript'>
<?php $_from = $this->_tpl_vars['menu_channels']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['pos'] => $this->_tpl_vars['chdata']):
?>
<?php if ($this->_tpl_vars['chdata']['type'] != 'o' && $this->_tpl_vars['chdata']['type'] != '-'): ?>
  <?php if ($this->_tpl_vars['prefs']['feature_menusfolderstyle'] == 'y'): ?>
    setfolderstate('menu<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['menu_info']['menuId'])) ? $this->_run_mod_handler('cat', true, $_tmp, '__') : smarty_modifier_cat($_tmp, '__')))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['chdata']['position']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['chdata']['position'])); ?>
', '<?php echo $this->_tpl_vars['menu_info']['type']; ?>
');
  <?php else: ?>
    setsectionstate('menu<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['menu_info']['menuId'])) ? $this->_run_mod_handler('cat', true, $_tmp, '__') : smarty_modifier_cat($_tmp, '__')))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['chdata']['position']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['chdata']['position'])); ?>
', '<?php echo $this->_tpl_vars['menu_info']['type']; ?>
');
  <?php endif; ?>
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
</script>
<?php endif; ?>
