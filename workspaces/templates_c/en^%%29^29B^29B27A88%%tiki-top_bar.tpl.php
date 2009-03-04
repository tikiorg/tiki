<?php /* Smarty version 2.6.22, created on 2009-03-04 13:06:21
         compiled from tiki-top_bar.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'phplayers', 'tiki-top_bar.tpl', 18, false),array('function', 'menu', 'tiki-top_bar.tpl', 20, false),array('function', 'breadcrumbs', 'tiki-top_bar.tpl', 26, false),array('block', 'tr', 'tiki-top_bar.tpl', 32, false),array('modifier', 'escape', 'tiki-top_bar.tpl', 35, false),)), $this); ?>
<?php if ($this->_tpl_vars['filegals_manager'] == '' && $this->_tpl_vars['print_page'] != 'y'): ?>
<?php if ($this->_tpl_vars['prefs']['feature_sitesearch'] == 'y' && $this->_tpl_vars['prefs']['feature_search'] == 'y' && $this->_tpl_vars['tiki_p_search'] == 'y'): ?>
		<div id="sitesearchbar"<?php if ($this->_tpl_vars['prefs']['feature_sitemycode'] != 'y' && $this->_tpl_vars['prefs']['feature_sitelogo'] != 'y' && $this->_tpl_vars['prefs']['feature_sitead'] != 'y' && $this->_tpl_vars['prefs']['feature_fullscreen'] == 'y' && $this->_tpl_vars['filegals_manager'] == '' && $this->_tpl_vars['print_page'] != 'y'): ?><?php if ($_SESSION['fullscreen'] != 'y'): ?>style="margin-right: 80px"<?php endif; ?><?php endif; ?>>
		<?php if ($this->_tpl_vars['prefs']['feature_search_fulltext'] == 'y'): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "tiki-searchresults.tpl", 'smarty_include_vars' => array('searchNoResults' => 'false','searchStyle' => 'menu','searchOrientation' => 'horiz')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php else: ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "tiki-searchindex.tpl", 'smarty_include_vars' => array('searchNoResults' => 'false','searchStyle' => 'menu','searchOrientation' => 'horiz')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?>
		</div>
<?php endif; ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['prefs']['feature_sitemenu'] == 'y'): ?>
	<?php if ($this->_tpl_vars['prefs']['feature_phplayers'] == 'y'): ?>
		<?php echo smarty_function_phplayers(array('id' => $this->_tpl_vars['prefs']['feature_topbar_id_menu'],'type' => 'horiz'), $this);?>

	<?php else: ?>
		<?php echo smarty_function_menu(array('id' => $this->_tpl_vars['prefs']['feature_topbar_id_menu'],'type' => 'horiz','css' => 'y'), $this);?>

	<?php endif; ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['prefs']['feature_siteloc'] == 'y' && $this->_tpl_vars['prefs']['feature_breadcrumbs'] == 'y'): ?>
		<div id="sitelocbar">
			<small><?php if ($this->_tpl_vars['prefs']['feature_siteloclabel'] == 'y'): ?>Location : <?php endif; ?><?php if ($this->_tpl_vars['trail']): ?><?php echo smarty_function_breadcrumbs(array('type' => 'trail','loc' => 'site','crumbs' => $this->_tpl_vars['trail']), $this);?>
<?php echo smarty_function_breadcrumbs(array('type' => 'pagetitle','loc' => 'site','crumbs' => $this->_tpl_vars['trail']), $this);?>
<?php else: ?><a title="<?php $this->_tag_stack[] = array('tr', array()); $_block_repeat=true;smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['crumbs'][0]->description; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>" href="<?php echo $this->_tpl_vars['crumbs'][0]->url; ?>
" accesskey="1"><?php echo $this->_tpl_vars['crumbs'][0]->title; ?>
</a>
		<?php if ($this->_tpl_vars['structure'] == 'y'): ?>
			<?php unset($this->_sections['ix']);
$this->_sections['ix']['loop'] = is_array($_loop=$this->_tpl_vars['structure_path']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['ix']['name'] = 'ix';
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
				<?php echo ((is_array($_tmp=$this->_tpl_vars['prefs']['site_crumb_seper'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

				<?php if ($this->_tpl_vars['structure_path'][$this->_sections['ix']['index']]['pageName'] != $this->_tpl_vars['page'] || $this->_tpl_vars['structure_path'][$this->_sections['ix']['index']]['page_alias'] != $this->_tpl_vars['page_info']['page_alias']): ?>
			<a href="tiki-index.php?page_ref_id=<?php echo $this->_tpl_vars['structure_path'][$this->_sections['ix']['index']]['page_ref_id']; ?>
">
				<?php endif; ?>
				<?php if ($this->_tpl_vars['structure_path'][$this->_sections['ix']['index']]['page_alias']): ?>
					<?php echo $this->_tpl_vars['structure_path'][$this->_sections['ix']['index']]['page_alias']; ?>

				<?php else: ?>
					<?php if ($this->_tpl_vars['beingStaged'] == 'y' && $this->_tpl_vars['prefs']['wikiapproval_hideprefix'] == 'y'): ?><?php echo $this->_tpl_vars['approvedPageName']; ?>
<?php else: ?><?php echo $this->_tpl_vars['structure_path'][$this->_sections['ix']['index']]['pageName']; ?>
<?php endif; ?>
				<?php endif; ?>
				<?php if ($this->_tpl_vars['structure_path'][$this->_sections['ix']['index']]['pageName'] != $this->_tpl_vars['page'] || $this->_tpl_vars['structure_path'][$this->_sections['ix']['index']]['page_alias'] != $this->_tpl_vars['page_info']['page_alias']): ?>
					</a>
				<?php endif; ?>
			<?php endfor; endif; ?>
		<?php else: ?>
			<?php if ($this->_tpl_vars['page'] != ''): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['prefs']['site_crumb_seper'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
 <?php if ($this->_tpl_vars['beingStaged'] == 'y' && $this->_tpl_vars['prefs']['wikiapproval_hideprefix'] == 'y'): ?><?php echo $this->_tpl_vars['approvedPageName']; ?>
<?php else: ?><?php echo $this->_tpl_vars['page']; ?>
<?php endif; ?>
			<?php elseif ($this->_tpl_vars['title'] != ''): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['prefs']['site_crumb_seper'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
 <?php echo $this->_tpl_vars['title']; ?>

			<?php elseif ($this->_tpl_vars['thread_info']['title'] != ''): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['prefs']['site_crumb_seper'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
 <?php echo $this->_tpl_vars['thread_info']['title']; ?>

			<?php elseif ($this->_tpl_vars['forum_info']['name'] != ''): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['prefs']['site_crumb_seper'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
 <?php echo $this->_tpl_vars['forum_info']['name']; ?>
<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?></small>
		</div>
	<?php if ($this->_tpl_vars['trail']): ?><?php echo smarty_function_breadcrumbs(array('type' => 'desc','loc' => 'site','crumbs' => $this->_tpl_vars['trail']), $this);?>
<?php else: ?><?php echo smarty_function_breadcrumbs(array('type' => 'desc','loc' => 'site','crumbs' => $this->_tpl_vars['crumbs']), $this);?>
<?php endif; ?>
<?php endif; ?>