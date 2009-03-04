<?php /* Smarty version 2.6.22, created on 2009-03-04 13:06:20
         compiled from modules/mod-since_last_visit_new.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'tikimodule', 'modules/mod-since_last_visit_new.tpl', 4, false),array('modifier', 'tiki_short_date', 'modules/mod-since_last_visit_new.tpl', 9, false),array('modifier', 'escape', 'modules/mod-since_last_visit_new.tpl', 36, false),)), $this); ?>

<?php if ($this->_tpl_vars['user']): ?>
	<?php $this->assign('module_title', $this->_tpl_vars['slvn_info']['label']); ?>
	<?php $this->_tag_stack[] = array('tikimodule', array('error' => $this->_tpl_vars['module_params']['error'],'title' => ($this->_tpl_vars['module_title']),'name' => 'since_last_visit_new','flip' => $this->_tpl_vars['module_params']['flip'],'decorations' => $this->_tpl_vars['module_params']['decorations'],'nobox' => $this->_tpl_vars['module_params']['nobox'],'notitle' => $this->_tpl_vars['module_params']['notitle'])); $_block_repeat=true;smarty_block_tikimodule($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
	<div style="margin-bottom: 5px; text-align:center;">
		<?php if ($this->_tpl_vars['prefs']['feature_calendar'] == 'y'): ?>
			<a class="linkmodule" href="tiki-calendar.php?todate=<?php echo $this->_tpl_vars['slvn_info']['lastLogin']; ?>
" title="click to edit">
		<?php endif; ?>
		<b><?php echo ((is_array($_tmp=$this->_tpl_vars['slvn_info']['lastLogin'])) ? $this->_run_mod_handler('tiki_short_date', true, $_tmp) : smarty_modifier_tiki_short_date($_tmp)); ?>
</b>
		<?php if ($this->_tpl_vars['prefs']['feature_calendar'] == 'y'): ?>
			</a>
		<?php endif; ?>
	</div>
	<?php if ($this->_tpl_vars['slvn_info']['cant'] == 0): ?>
		<div class="separator">Nothing has changed</div>
	<?php else: ?>
		<?php $_from = $this->_tpl_vars['slvn_info']['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['pos'] => $this->_tpl_vars['slvn_item']):
?>
			<?php if ($this->_tpl_vars['slvn_item']['count'] > 0): ?>
				<?php $this->assign('cname', $this->_tpl_vars['slvn_item']['cname']); ?>
				<div class="separator"><a class="separator" href="javascript:flip('<?php echo $this->_tpl_vars['cname']; ?>
');"><?php echo $this->_tpl_vars['slvn_item']['count']; ?>
&nbsp;<?php echo $this->_tpl_vars['slvn_item']['label']; ?>
</a></div>
				<?php $this->assign('showcname', "show_".($this->_tpl_vars['cname'])); ?>

             	<?php if ($this->_tpl_vars['pos'] == 'trackers' || $this->_tpl_vars['pos'] == 'utrackers'): ?>
					<div id="<?php echo $this->_tpl_vars['cname']; ?>
" style="display:<?php if (! isset ( $this->_tpl_vars['cookie'][$this->_tpl_vars['showcname']] ) || $this->_tpl_vars['cookie'][$this->_tpl_vars['showcname']] == 'y'): ?>block<?php else: ?>none<?php endif; ?>;">

                
					<?php $_from = $this->_tpl_vars['slvn_item']['tid']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['tp'] => $this->_tpl_vars['tracker']):
?>
						<?php $this->assign('tcname', $this->_tpl_vars['tracker']['cname']); ?>
						<div class="separator"  style="margin-left: 10px; display:<?php if (! isset ( $this->_tpl_vars['cookie'][$this->_tpl_vars['showcname']] ) || $this->_tpl_vars['cookie'][$this->_tpl_vars['showcname']] == 'y'): ?>block<?php else: ?>none<?php endif; ?>;">
							<?php $this->assign('showtcname', "show_".($this->_tpl_vars['tcname'])); ?>
							<a class="separator" href="javascript:flip('<?php echo $this->_tpl_vars['tcname']; ?>
');"><?php echo $this->_tpl_vars['tracker']['count']; ?>
&nbsp;<?php echo $this->_tpl_vars['tracker']['label']; ?>
</a>
							<div id="<?php echo $this->_tpl_vars['tcname']; ?>
" style="display:<?php if (! isset ( $this->_tpl_vars['cookie'][$this->_tpl_vars['showtcname']] ) || $this->_tpl_vars['cookie'][$this->_tpl_vars['showtcname']] == 'y'): ?>block<?php else: ?>none<?php endif; ?>;"> 
								<?php if ($this->_tpl_vars['nonums'] != 'y'): ?><ol><?php else: ?><ul><?php endif; ?>
								<?php unset($this->_sections['xx']);
$this->_sections['xx']['name'] = 'xx';
$this->_sections['xx']['loop'] = is_array($_loop=$this->_tpl_vars['tracker']['list']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['xx']['show'] = true;
$this->_sections['xx']['max'] = $this->_sections['xx']['loop'];
$this->_sections['xx']['step'] = 1;
$this->_sections['xx']['start'] = $this->_sections['xx']['step'] > 0 ? 0 : $this->_sections['xx']['loop']-1;
if ($this->_sections['xx']['show']) {
    $this->_sections['xx']['total'] = $this->_sections['xx']['loop'];
    if ($this->_sections['xx']['total'] == 0)
        $this->_sections['xx']['show'] = false;
} else
    $this->_sections['xx']['total'] = 0;
if ($this->_sections['xx']['show']):

            for ($this->_sections['xx']['index'] = $this->_sections['xx']['start'], $this->_sections['xx']['iteration'] = 1;
                 $this->_sections['xx']['iteration'] <= $this->_sections['xx']['total'];
                 $this->_sections['xx']['index'] += $this->_sections['xx']['step'], $this->_sections['xx']['iteration']++):
$this->_sections['xx']['rownum'] = $this->_sections['xx']['iteration'];
$this->_sections['xx']['index_prev'] = $this->_sections['xx']['index'] - $this->_sections['xx']['step'];
$this->_sections['xx']['index_next'] = $this->_sections['xx']['index'] + $this->_sections['xx']['step'];
$this->_sections['xx']['first']      = ($this->_sections['xx']['iteration'] == 1);
$this->_sections['xx']['last']       = ($this->_sections['xx']['iteration'] == $this->_sections['xx']['total']);
?>
									<li><a  class="linkmodule"
												href="<?php echo ((is_array($_tmp=$this->_tpl_vars['tracker']['list'][$this->_sections['xx']['index']]['href'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"
												title="<?php echo ((is_array($_tmp=$this->_tpl_vars['tracker']['list'][$this->_sections['xx']['index']]['title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"><?php if ($this->_tpl_vars['tracker']['list'][$this->_sections['xx']['index']]['label'] == ''): ?>-<?php else: ?><?php echo ((is_array($_tmp=$this->_tpl_vars['tracker']['list'][$this->_sections['xx']['index']]['label'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<?php endif; ?>
											</a>
									</li>
								<?php endfor; endif; ?>
								<?php if ($this->_tpl_vars['nonums'] != 'y'): ?></ol><?php else: ?></ul><?php endif; ?>
							</div>
						</div>
					<?php endforeach; endif; unset($_from); ?>
                
					</div>

				<?php else: ?>
					<div id="<?php echo $this->_tpl_vars['cname']; ?>
" style="display:<?php if (! isset ( $this->_tpl_vars['cookie'][$this->_tpl_vars['showcname']] ) || $this->_tpl_vars['cookie'][$this->_tpl_vars['showcname']] == 'y'): ?>block<?php else: ?>none<?php endif; ?>;">
						<?php if ($this->_tpl_vars['nonums'] != 'y'): ?><ol><?php else: ?><ul><?php endif; ?>
						<?php unset($this->_sections['ix']);
$this->_sections['ix']['name'] = 'ix';
$this->_sections['ix']['loop'] = is_array($_loop=$this->_tpl_vars['slvn_item']['list']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['ix']['show'] = true;
$this->_sections['ix']['max'] = $this->_sections['ix']['loop'];
$this->_sections['ix']['step'] = 1;
$this->_sections['ix']['start'] = $this->_sections['ix']['step'] > 0 ? 0 : $this->_sections['ix']['loop']-1;
if ($this->_sections['ix']['show']) {
    $this->_sections['ix']['total'] = $this->_sections['ix']['loop'];
    if ($this->_sections['ix']['total'] == 0)
        $this->_sections['ix']['show'] = false;
} else
    $this->_sections['ix']['total'] = 0;
if ($this->_sections['ix']['show']):

            for ($this->_sections['ix']['index'] = $this->_sections['ix']['start'], $this->_sections['ix']['iteration'] = 1;
                 $this->_sections['ix']['iteration'] <= $this->_sections['ix']['total'];
                 $this->_sections['ix']['index'] += $this->_sections['ix']['step'], $this->_sections['ix']['iteration']++):
$this->_sections['ix']['rownum'] = $this->_sections['ix']['iteration'];
$this->_sections['ix']['index_prev'] = $this->_sections['ix']['index'] - $this->_sections['ix']['step'];
$this->_sections['ix']['index_next'] = $this->_sections['ix']['index'] + $this->_sections['ix']['step'];
$this->_sections['ix']['first']      = ($this->_sections['ix']['iteration'] == 1);
$this->_sections['ix']['last']       = ($this->_sections['ix']['iteration'] == $this->_sections['ix']['total']);
?>
							<li>
									<a  class="linkmodule" 
										href="<?php echo ((is_array($_tmp=$this->_tpl_vars['slvn_item']['list'][$this->_sections['ix']['index']]['href'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"
										title="<?php echo ((is_array($_tmp=$this->_tpl_vars['slvn_item']['list'][$this->_sections['ix']['index']]['title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
										<?php if ($this->_tpl_vars['slvn_item']['list'][$this->_sections['ix']['index']]['label'] == ''): ?>-<?php else: ?><?php echo ((is_array($_tmp=$this->_tpl_vars['slvn_item']['list'][$this->_sections['ix']['index']]['label'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<?php endif; ?>
									</a>
								</li>
						<?php endfor; endif; ?>
						<?php if ($this->_tpl_vars['nonums'] != 'y'): ?></ol><?php else: ?></ul><?php endif; ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		<?php endforeach; endif; unset($_from); ?>
	<?php endif; ?>

	<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_tikimodule($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
<?php endif; ?>
      