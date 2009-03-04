<?php /* Smarty version 2.6.22, created on 2009-03-04 13:06:20
         compiled from tiki.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'tiki.tpl', 25, false),array('modifier', 'escape', 'tiki.tpl', 43, false),array('function', 'icon', 'tiki.tpl', 31, false),array('function', 'show_help', 'tiki.tpl', 48, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if ($this->_tpl_vars['prefs']['feature_tikitests'] == 'y' && $this->_tpl_vars['tikitest_state'] != 0): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "tiki-tests_topbar.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['prefs']['feature_ajax'] == 'y'): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "tiki-ajax_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<div id="main">
<?php if ($this->_tpl_vars['prefs']['feature_fullscreen'] != 'y' || $_SESSION['fullscreen'] != 'y'): ?>
<div class="clearfix" id="header"<?php if ($this->_tpl_vars['prefs']['feature_bidi'] == 'y'): ?> dir="rtl"<?php endif; ?>>
	<?php if ($this->_tpl_vars['prefs']['feature_siteidentity'] == 'y'): ?>
	
	<div class="clearfix" id="siteheader">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "tiki-site_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
	<?php endif; ?>
</div>
<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "tiki-admin_bar.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="clearfix" id="middle">
	<div class="clearfix <?php if ($this->_tpl_vars['prefs']['feature_fullscreen'] != 'n' && $_SESSION['fullscreen'] != 'n'): ?>fullscreen<?php endif; ?><?php if ($this->_tpl_vars['prefs']['feature_fullscreen'] != 'y' && $_SESSION['fullscreen'] != 'n'): ?>nofullscreen<?php endif; ?>" id="c1c2">
		<div class="clearfix" id="wrapper">
			<div id="col1" class="<?php if ($this->_tpl_vars['prefs']['feature_left_column'] != 'n' && count($this->_tpl_vars['left_modules']) > 0 && $this->_tpl_vars['show_columns']['left_modules'] != 'n'): ?>marginleft<?php endif; ?><?php if ($this->_tpl_vars['prefs']['feature_right_column'] != 'n' && count($this->_tpl_vars['right_modules']) > 0 && $this->_tpl_vars['show_columns']['right_modules'] != 'n'): ?> marginright<?php endif; ?>"<?php if ($this->_tpl_vars['prefs']['feature_bidi'] == 'y'): ?> dir="rtl"<?php endif; ?>>
				<?php if ($_SESSION['fullscreen'] != 'y'): ?>
		<?php if ($this->_tpl_vars['prefs']['feature_left_column'] == 'user' || $this->_tpl_vars['prefs']['feature_right_column'] == 'user'): ?>
			<div class="clearfix" id="showhide_columns">
			<?php if ($this->_tpl_vars['prefs']['feature_left_column'] == 'user' && count($this->_tpl_vars['left_modules']) > 0 && $this->_tpl_vars['show_columns']['left_modules'] != 'n'): ?>
				<div style="text-align:left;float:left;"><a class="flip" 
					href="#" onclick="toggleCols('col2','left'); return false"><?php echo smarty_function_icon(array('_id' => 'ofolder','name' => 'leftcolumnicn','class' => 'colflip','alt' => "+/-"), $this);?>
&nbsp;Show/Hide Left Menus&nbsp;</a></div>
    		<?php endif; ?>
			<?php if ($this->_tpl_vars['prefs']['feature_right_column'] == 'user' && count($this->_tpl_vars['right_modules']) > 0 && $this->_tpl_vars['show_columns']['right_modules'] != 'n'): ?>
				<div class="clearfix" style="text-align:right;float:right"><a class="flip"
					href="#" onclick="toggleCols('col3','right'); return false">&nbsp;Show/Hide Right Menus&nbsp;<?php echo smarty_function_icon(array('_id' => 'ofolder','name' => 'rightcolumnicn','class' => 'colflip','alt' => "+/-"), $this);?>
</a>
				</div>
			<?php endif; ?>
			<br style="clear:both" />
			</div>
		<?php endif; ?>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['feature_tell_a_friend'] == 'y' && $this->_tpl_vars['tiki_p_tell_a_friend'] == 'y' && ( ! isset ( $this->_tpl_vars['edit_page'] ) || $this->_tpl_vars['edit_page'] != 'y' && $this->_tpl_vars['prefs']['feature_site_send_link'] != 'y' )): ?>
				<div class="tellafriend"><a href="tiki-tell_a_friend.php?url=<?php echo ((is_array($_tmp=$_SERVER['REQUEST_URI'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
">Email this page</a>
				</div>
				<?php endif; ?>
					<div id="tiki-center"  class="clearfix content">
						<?php echo $this->_tpl_vars['mid_data']; ?>

						<?php echo smarty_function_show_help(array(), $this);?>

					</div>
				</div>
			</div>
			<?php if ($this->_tpl_vars['prefs']['feature_fullscreen'] != 'y' || $_SESSION['fullscreen'] != 'y'): ?>
			<hr class="hidden" /> 
			<?php if ($this->_tpl_vars['prefs']['feature_left_column'] != 'n' && count($this->_tpl_vars['left_modules']) > 0 && $this->_tpl_vars['show_columns']['left_modules'] != 'n'): ?>
				<div id="col2"<?php if ($this->_tpl_vars['prefs']['feature_bidi'] == 'y'): ?> dir="rtl"<?php endif; ?>>
				<h2 class="hidden">Sidebar</h2>
					<div class="content">
						<?php unset($this->_sections['homeix']);
$this->_sections['homeix']['name'] = 'homeix';
$this->_sections['homeix']['loop'] = is_array($_loop=$this->_tpl_vars['left_modules']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['homeix']['show'] = true;
$this->_sections['homeix']['max'] = $this->_sections['homeix']['loop'];
$this->_sections['homeix']['step'] = 1;
$this->_sections['homeix']['start'] = $this->_sections['homeix']['step'] > 0 ? 0 : $this->_sections['homeix']['loop']-1;
if ($this->_sections['homeix']['show']) {
    $this->_sections['homeix']['total'] = $this->_sections['homeix']['loop'];
    if ($this->_sections['homeix']['total'] == 0)
        $this->_sections['homeix']['show'] = false;
} else
    $this->_sections['homeix']['total'] = 0;
if ($this->_sections['homeix']['show']):

            for ($this->_sections['homeix']['index'] = $this->_sections['homeix']['start'], $this->_sections['homeix']['iteration'] = 1;
                 $this->_sections['homeix']['iteration'] <= $this->_sections['homeix']['total'];
                 $this->_sections['homeix']['index'] += $this->_sections['homeix']['step'], $this->_sections['homeix']['iteration']++):
$this->_sections['homeix']['rownum'] = $this->_sections['homeix']['iteration'];
$this->_sections['homeix']['index_prev'] = $this->_sections['homeix']['index'] - $this->_sections['homeix']['step'];
$this->_sections['homeix']['index_next'] = $this->_sections['homeix']['index'] + $this->_sections['homeix']['step'];
$this->_sections['homeix']['first']      = ($this->_sections['homeix']['iteration'] == 1);
$this->_sections['homeix']['last']       = ($this->_sections['homeix']['iteration'] == $this->_sections['homeix']['total']);
?>
						 	<?php echo $this->_tpl_vars['left_modules'][$this->_sections['homeix']['index']]['data']; ?>

						<?php endfor; endif; ?>
    			    </div>
				</div>
			<?php endif; ?>
			<?php endif; ?>
			</div>
<?php if ($this->_tpl_vars['prefs']['feature_fullscreen'] != 'y' || $_SESSION['fullscreen'] != 'y'): ?>
	<?php if ($this->_tpl_vars['prefs']['feature_right_column'] != 'n' && count($this->_tpl_vars['right_modules']) > 0 && $this->_tpl_vars['show_columns']['right_modules'] != 'n'): ?>
		<div class="clearfix" id="col3" 
		<?php if ($this->_tpl_vars['prefs']['feature_right_column'] == 'user'): ?> 
		style="display:<?php if (isset ( $this->_tpl_vars['cookie']['show_rightcolumn'] ) && $this->_tpl_vars['cookie']['show_rightcolumn'] != 'y'): ?>none<?php else: ?>table-cell;_display:block<?php endif; ?>;"
		<?php endif; ?><?php if ($this->_tpl_vars['prefs']['feature_bidi'] == 'y'): ?> dir="rtl"<?php endif; ?>>
		<h2 class="hidden">Sidebar</h2>
		<div class="content">
			<?php unset($this->_sections['homeix']);
$this->_sections['homeix']['name'] = 'homeix';
$this->_sections['homeix']['loop'] = is_array($_loop=$this->_tpl_vars['right_modules']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['homeix']['show'] = true;
$this->_sections['homeix']['max'] = $this->_sections['homeix']['loop'];
$this->_sections['homeix']['step'] = 1;
$this->_sections['homeix']['start'] = $this->_sections['homeix']['step'] > 0 ? 0 : $this->_sections['homeix']['loop']-1;
if ($this->_sections['homeix']['show']) {
    $this->_sections['homeix']['total'] = $this->_sections['homeix']['loop'];
    if ($this->_sections['homeix']['total'] == 0)
        $this->_sections['homeix']['show'] = false;
} else
    $this->_sections['homeix']['total'] = 0;
if ($this->_sections['homeix']['show']):

            for ($this->_sections['homeix']['index'] = $this->_sections['homeix']['start'], $this->_sections['homeix']['iteration'] = 1;
                 $this->_sections['homeix']['iteration'] <= $this->_sections['homeix']['total'];
                 $this->_sections['homeix']['index'] += $this->_sections['homeix']['step'], $this->_sections['homeix']['iteration']++):
$this->_sections['homeix']['rownum'] = $this->_sections['homeix']['iteration'];
$this->_sections['homeix']['index_prev'] = $this->_sections['homeix']['index'] - $this->_sections['homeix']['step'];
$this->_sections['homeix']['index_next'] = $this->_sections['homeix']['index'] + $this->_sections['homeix']['step'];
$this->_sections['homeix']['first']      = ($this->_sections['homeix']['iteration'] == 1);
$this->_sections['homeix']['last']       = ($this->_sections['homeix']['iteration'] == $this->_sections['homeix']['total']);
?>
				<?php echo $this->_tpl_vars['right_modules'][$this->_sections['homeix']['index']]['data']; ?>

			<?php endfor; endif; ?>
        </div>
	</div><br style="clear:both" />
	<?php endif; ?>
<?php endif; ?>
</div>
</div>
<?php if ($this->_tpl_vars['prefs']['feature_fullscreen'] != 'y' || $_SESSION['fullscreen'] != 'y'): ?>
<?php if ($this->_tpl_vars['prefs']['feature_bot_bar'] == 'y'): ?>
<div id="footer">
	<div class="footerbgtrap">
		<div class="content"<?php if ($this->_tpl_vars['prefs']['feature_bidi'] == 'y'): ?> dir="rtl"<?php endif; ?>>
   			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "tiki-bot_bar.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
	</div>
</div>

<?php endif; ?>
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>