<?php /* Smarty version 2.6.22, created on 2009-03-04 13:06:19
         compiled from module.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'module.tpl', 5, false),array('modifier', 'cat', 'module.tpl', 28, false),array('function', 'icon', 'module.tpl', 11, false),)), $this); ?>


<?php if ($this->_tpl_vars['module_nobox'] != 'y'): ?>
<div class="box-shadow">
	<div class="box box-<?php echo ((is_array($_tmp=$this->_tpl_vars['module_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php if ($this->_tpl_vars['module_params']['overflow']): ?> style="overflow:visible !important"<?php endif; ?>>
	<?php if ($this->_tpl_vars['module_decorations'] != 'n'): ?>
		<h3 class="box-title clearfix"<?php if (! empty ( $this->_tpl_vars['module_params']['bgcolor'] )): ?> style="background-color:<?php echo $this->_tpl_vars['module_params']['bgcolor']; ?>
;"<?php endif; ?>>
		<?php if ($this->_tpl_vars['user'] && $this->_tpl_vars['prefs']['user_assigned_modules'] == 'y' && $this->_tpl_vars['prefs']['feature_modulecontrols'] == 'y'): ?>
			<span class="modcontrols">
			<a title="Move module up" href="<?php echo ((is_array($_tmp=$this->_tpl_vars['current_location'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['mpchar'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
mc_up=<?php echo ((is_array($_tmp=$this->_tpl_vars['module_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
				<?php echo smarty_function_icon(array('_id' => 'resultset_up','alt' => "[Up]"), $this);?>

			</a>
			<a title="Move module down" href="<?php echo ((is_array($_tmp=$this->_tpl_vars['current_location'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['mpchar'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
mc_down=<?php echo ((is_array($_tmp=$this->_tpl_vars['module_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
				<?php echo smarty_function_icon(array('_id' => 'resultset_down','alt' => "[Down]"), $this);?>

			</a>
			<a title="Move module to opposite side" href="<?php echo ((is_array($_tmp=$this->_tpl_vars['current_location'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['mpchar'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
mc_move=<?php echo ((is_array($_tmp=$this->_tpl_vars['module_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
				<?php echo smarty_function_icon(array('_id' => "arrow_right-left",'alt' => "[opp side]"), $this);?>

			</a>
			<a title="Unassign this module" href="<?php echo ((is_array($_tmp=$this->_tpl_vars['current_location'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['mpchar'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
mc_unassign=<?php echo ((is_array($_tmp=$this->_tpl_vars['module_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" onclick="return confirmTheLink(this,'Are you sure you want to unassign this module?')">
				<?php echo smarty_function_icon(array('_id' => 'cross','alt' => "[Remove]"), $this);?>

			 </a>
			</span>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['module_notitle'] != 'y'): ?>
		<span class="moduletitle"><?php echo $this->_tpl_vars['module_title']; ?>
</span>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['module_flip'] == 'y' && $this->_tpl_vars['prefs']['javascript_enabled'] != 'n'): ?>
			<span class="moduleflip" id="moduleflip-<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['module_name'])) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['module_position']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['module_position'])))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['module_ord']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['module_ord'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
				<a title="Toggle module contents" class="flipmodtitle" href="javascript:icntoggle('mod-<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['module_name'])) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['module_position']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['module_position'])))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['module_ord']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['module_ord'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
','module.png');">
					<?php ob_start(); ?>icnmod-<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['module_name'])) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['module_position']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['module_position'])))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['module_ord']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['module_ord'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<?php $this->_smarty_vars['capture']['name'] = ob_get_contents(); ob_end_clean(); ?>
					<?php echo smarty_function_icon(array('name' => $this->_smarty_vars['capture']['name'],'class' => 'flipmodimage','_id' => 'module','alt' => "[toggle]"), $this);?>

				</a>
			</span>
		<?php endif; ?>
		</h3>
	<?php elseif ($this->_tpl_vars['module_notitle'] != 'y'): ?>
		<?php if ($this->_tpl_vars['module_flip'] == 'y' && $this->_tpl_vars['prefs']['javascript_enabled'] != 'n'): ?>
			<h3 class="box-title" ondblclick="javascript:icntoggle('mod-<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['module_name'])) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['module_position']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['module_position'])))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['module_ord']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['module_ord'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
','module.png');"<?php if (! empty ( $this->_tpl_vars['module_params']['color'] )): ?> style="color:<?php echo $this->_tpl_vars['module_params']['color']; ?>
;"<?php endif; ?>>
		<?php else: ?>
			<h3 class="box-title"<?php if (! empty ( $this->_tpl_vars['module_params']['color'] )): ?> style="color:<?php echo $this->_tpl_vars['module_params']['color']; ?>
;"<?php endif; ?>>
		<?php endif; ?>
		<?php echo $this->_tpl_vars['module_title']; ?>

		<?php if ($this->_tpl_vars['module_flip'] == 'y' && $this->_tpl_vars['prefs']['javascript_enabled'] != 'n'): ?>
			<span id="moduleflip-<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['module_name'])) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['module_position']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['module_position'])))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['module_ord']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['module_ord'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
				<a title="Toggle module contents" class="flipmodtitle" href="javascript:icntoggle('mod-<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['module_name'])) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['module_position']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['module_position'])))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['module_ord']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['module_ord'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
','module.png');">
					<?php $this->assign('name', "`icnmod-".($this->_tpl_vars['module_name'])."|cat:".($this->_tpl_vars['module_position'])."|cat:".($this->_tpl_vars['module_ord'])."|escape`"); ?>
					<?php ob_start(); ?>
						icnmod-<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['module_name'])) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['module_position']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['module_position'])))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['module_ord']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['module_ord'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

					<?php $this->_smarty_vars['capture']['name'] = ob_get_contents(); ob_end_clean(); ?>
					<?php echo smarty_function_icon(array('name' => $this->_smarty_vars['capture']['name'],'class' => 'flipmodimage','_id' => 'module','alt' => "[Hide]"), $this);?>

				</a>
			</span>
		<?php endif; ?>
		</h3>
	<?php endif; ?>
		<div id="mod-<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['module_name'])) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['module_position']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['module_position'])))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['module_ord']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['module_ord'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" style="display: block" class="clearfix box-data">
<?php endif; ?>
<?php echo $this->_tpl_vars['module_content']; ?>

<?php echo $this->_tpl_vars['module_error']; ?>

<?php if ($this->_tpl_vars['module_nobox'] != 'y'): ?>
<?php if ($this->_tpl_vars['module_flip'] == 'y'): ?>
			<script type="text/javascript">
<!--//--><![CDATA[//><!--
				setsectionstate('mod-<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['module_name'])) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['module_position']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['module_position'])))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['module_ord']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['module_ord'])))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
','<?php echo $this->_tpl_vars['module_dstate']; ?>
', 'module.png');
//--><!]]>
			</script>
<?php endif; ?>
		</div>
		<div class="box-footer">

		</div>
	</div>
</div>
<?php endif; ?>