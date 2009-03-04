<?php /* Smarty version 2.6.22, created on 2009-03-04 13:06:18
         compiled from tiki-wiki_topline.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'breadcrumbs', 'tiki-wiki_topline.tpl', 9, false),array('function', 'query', 'tiki-wiki_topline.tpl', 20, false),array('function', 'icon', 'tiki-wiki_topline.tpl', 20, false),array('modifier', 'lower', 'tiki-wiki_topline.tpl', 22, false),array('modifier', 'escape', 'tiki-wiki_topline.tpl', 23, false),array('block', 'ajax_href', 'tiki-wiki_topline.tpl', 23, false),array('block', 'popup_link', 'tiki-wiki_topline.tpl', 56, false),)), $this); ?>
<div class="wikitopline" style="clear: both;">
	<div class="content">
		<?php if (! $this->_tpl_vars['hide_page_header']): ?>
		<div class="wikiinfo" style="float: left">
<?php if ($this->_tpl_vars['prefs']['feature_wiki_pageid'] == 'y' && $this->_tpl_vars['print_page'] != 'y'): ?>
			<small><a class="link" href="tiki-index.php?page_id=<?php echo $this->_tpl_vars['page_id']; ?>
">page id: <?php echo $this->_tpl_vars['page_id']; ?>
</a></small>
<?php endif; ?>

<?php echo smarty_function_breadcrumbs(array('type' => 'desc','loc' => 'page','crumbs' => $this->_tpl_vars['crumbs']), $this);?>


<?php if ($this->_tpl_vars['cached_page'] == 'y'): ?><small>(Cached)</small><?php endif; ?>
<?php if ($this->_tpl_vars['is_categorized'] == 'y' && $this->_tpl_vars['prefs']['feature_categories'] == 'y' && $this->_tpl_vars['prefs']['feature_categorypath'] == 'y'): ?>
	<?php echo $this->_tpl_vars['display_catpath']; ?>

<?php endif; ?>
		</div>
<?php if ($this->_tpl_vars['print_page'] != 'y'): ?>
		<div class="wikiactions" style="float: right; padding-left:10px; white-space: nowrap">
			<div class="icons" style="float: left;">
	<?php if ($this->_tpl_vars['pdf_export'] == 'y'): ?>
				<a href="tiki-print.php?<?php echo smarty_function_query(array('display' => 'pdf'), $this);?>
" title="PDF"><?php echo smarty_function_icon(array('_id' => 'page_white_acrobat','alt' => 'PDF'), $this);?>
</a>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['editable'] && ( $this->_tpl_vars['tiki_p_edit'] == 'y' || ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('lower', true, $_tmp) : smarty_modifier_lower($_tmp)) == 'sandbox' ) && $this->_tpl_vars['beingEdited'] != 'y' || $this->_tpl_vars['canEditStaging'] == 'y'): ?>
				<a title="Edit" <?php $this->_tag_stack[] = array('ajax_href', array('template' => "tiki-editpage.tpl",'htmlelement' => "tiki-center")); $_block_repeat=true;smarty_block_ajax_href($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>tiki-editpage.php?page=<?php if ($this->_tpl_vars['needsStaging'] == 'y'): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['stagingPageName'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
<?php else: ?><?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
<?php endif; ?><?php if (! empty ( $this->_tpl_vars['page_ref_id'] ) && $this->_tpl_vars['needsStaging'] != 'y'): ?>&amp;page_ref_id=<?php echo $this->_tpl_vars['page_ref_id']; ?>
<?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_ajax_href($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>><?php echo smarty_function_icon(array('_id' => 'page_edit'), $this);?>
</a>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['feature_morcego'] == 'y' && $this->_tpl_vars['prefs']['wiki_feature_3d'] == 'y'): ?>
				<a title="3d browser" href="javascript:wiki3d_open('<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
',<?php echo $this->_tpl_vars['prefs']['wiki_3d_width']; ?>
, <?php echo $this->_tpl_vars['prefs']['wiki_3d_height']; ?>
)"><?php echo smarty_function_icon(array('_id' => 'wiki3d','alt' => '3d browser'), $this);?>
</a>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['cached_page'] == 'y'): ?>
				<a title="Refresh" href="tiki-index.php?page=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;refresh=1"><?php echo smarty_function_icon(array('_id' => 'arrow_refresh'), $this);?>
</a>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['feature_wiki_print'] == 'y'): ?>
				<a title="Print" href="tiki-print.php?<?php if (! empty ( $this->_tpl_vars['page_ref_id'] )): ?>page_ref_id=<?php echo $this->_tpl_vars['page_ref_id']; ?>
&amp;<?php endif; ?>page=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
"><?php echo smarty_function_icon(array('_id' => 'printer','alt' => 'Print'), $this);?>
</a>
	<?php endif; ?>

	<?php if ($this->_tpl_vars['prefs']['feature_tell_a_friend'] == 'y' && $this->_tpl_vars['tiki_p_tell_a_friend'] == 'y'): ?>
				<a title="Send a link" href="tiki-tell_a_friend.php?url=<?php echo ((is_array($_tmp=$_SERVER['REQUEST_URI'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
"><?php echo smarty_function_icon(array('_id' => 'email_link','alt' => 'Send a link'), $this);?>
</a>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['user'] && $this->_tpl_vars['prefs']['feature_notepad'] == 'y' && $this->_tpl_vars['tiki_p_notepad'] == 'y'): ?>
				<a title="Save to notepad" href="tiki-index.php?page=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;savenotepad=1<?php if (! empty ( $this->_tpl_vars['page_ref_id'] )): ?>&amp;page_ref_id=<?php echo $this->_tpl_vars['page_ref_id']; ?>
<?php endif; ?>"><?php echo smarty_function_icon(array('_id' => 'disk','alt' => 'Save to notepad'), $this);?>
</a>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['user'] && $this->_tpl_vars['prefs']['feature_user_watches'] == 'y'): ?>
		<?php if ($this->_tpl_vars['user_watching_page'] == 'n'): ?>
				<a href="tiki-index.php?page=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;watch_event=wiki_page_changed&amp;watch_object=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;watch_action=add<?php if ($this->_tpl_vars['structure'] == 'y'): ?>&amp;structure=<?php echo ((is_array($_tmp=$this->_tpl_vars['home_info']['pageName'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
<?php endif; ?>" class="icon"><?php echo smarty_function_icon(array('_id' => 'eye','alt' => 'Monitor this Page'), $this);?>
</a>
		<?php else: ?>
				<a href="tiki-index.php?page=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;watch_event=wiki_page_changed&amp;watch_object=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;watch_action=remove<?php if ($this->_tpl_vars['structure'] == 'y'): ?>&amp;structure=<?php echo ((is_array($_tmp=$this->_tpl_vars['home_info']['pageName'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
<?php endif; ?>" class="icon"><?php echo smarty_function_icon(array('_id' => 'no_eye','alt' => 'Stop Monitoring this Page'), $this);?>
</a>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['structure'] == 'y' && $this->_tpl_vars['tiki_p_watch_structure'] == 'y'): ?>
			<?php if ($this->_tpl_vars['user_watching_structure'] != 'y'): ?>
				<a href="tiki-index.php?page=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;watch_event=structure_changed&amp;watch_object=<?php echo $this->_tpl_vars['page_info']['page_ref_id']; ?>
&amp;watch_action=add_desc&amp;structure=<?php echo ((is_array($_tmp=$this->_tpl_vars['home_info']['pageName'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
"><?php echo smarty_function_icon(array('_id' => 'eye_arrow_down','alt' => 'Monitor the Sub-Structure'), $this);?>
</a>
			<?php else: ?>
				<a href="tiki-index.php?page=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;watch_event=structure_changed&amp;watch_object=<?php echo $this->_tpl_vars['page_info']['page_ref_id']; ?>
&amp;watch_action=remove_desc&amp;structure=<?php echo ((is_array($_tmp=$this->_tpl_vars['home_info']['pageName'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
"><?php echo smarty_function_icon(array('_id' => 'no_eye_arrow_down','alt' => 'Stop Monitoring the Sub-Structure'), $this);?>
</a>
			<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['feature_group_watches'] == 'y' && ( $this->_tpl_vars['tiki_p_admin_users'] == 'y' || $this->_tpl_vars['tiki_p_admin'] == 'y' )): ?>
		<?php $this->_tag_stack[] = array('popup_link', array('block' => 'page_group_watch')); $_block_repeat=true;smarty_block_popup_link($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
			<?php echo smarty_function_icon(array('_id' => 'eye','alt' => 'Show Group Watches on page'), $this);?>

		<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_popup_link($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
		<div id="page_group_watch" class="popup-group-watch">
			<?php $_from = $this->_tpl_vars['grouplist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['g']):
?>
				<div>
					<?php if (! in_array ( $this->_tpl_vars['g'] , $this->_tpl_vars['page_group_watches'] )): ?>
						<a href="tiki-index.php?page=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;watch_group=<?php echo ((is_array($_tmp=$this->_tpl_vars['g'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;watch_event=wiki_page_changed&amp;watch_object=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;watch_action=add<?php if ($this->_tpl_vars['structure'] == 'y'): ?>&amp;structure=<?php echo ((is_array($_tmp=$this->_tpl_vars['home_info']['pageName'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
<?php endif; ?>" class="icon">
							<?php echo smarty_function_icon(array('_id' => 'eye','alt' => 'Enable Page Monitoring for Group'), $this);?>

						</a>

						<?php echo ((is_array($_tmp=$this->_tpl_vars['g'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

					<?php else: ?>
						<a href="tiki-index.php?page=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;watch_group=<?php echo ((is_array($_tmp=$this->_tpl_vars['g'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;watch_event=wiki_page_changed&amp;watch_object=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;watch_action=remove<?php if ($this->_tpl_vars['structure'] == 'y'): ?>&amp;structure=<?php echo ((is_array($_tmp=$this->_tpl_vars['home_info']['pageName'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
<?php endif; ?>" class="icon">
							<?php echo smarty_function_icon(array('_id' => 'no_eye','alt' => 'Disable Page Monitoring for Group'), $this);?>

						</a>

						<?php echo ((is_array($_tmp=$this->_tpl_vars['g'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

					<?php endif; ?>
				</div>
			<?php endforeach; endif; unset($_from); ?>
		</div>

		<?php if ($this->_tpl_vars['structure'] == 'y'): ?>
			<?php $this->_tag_stack[] = array('popup_link', array('block' => 'structure_group_watch')); $_block_repeat=true;smarty_block_popup_link($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
				<?php echo smarty_function_icon(array('_id' => 'eye_arrow_down','alt' => 'Show Group Watches on structure'), $this);?>

			<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_popup_link($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
			<div id="structure_group_watch" class="popup-group-watch">
				<?php $_from = $this->_tpl_vars['grouplist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['g']):
?>
					<div>
						<?php if (! in_array ( $this->_tpl_vars['g'] , $this->_tpl_vars['structure_group_watches'] )): ?>
							<a href="tiki-index.php?page=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;watch_group=<?php echo ((is_array($_tmp=$this->_tpl_vars['g'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;watch_event=structure_changed&amp;watch_object=<?php echo ((is_array($_tmp=$this->_tpl_vars['page_info']['page_ref_id'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;watch_action=add_desc<?php if ($this->_tpl_vars['structure'] == 'y'): ?>&amp;structure=<?php echo ((is_array($_tmp=$this->_tpl_vars['home_info']['pageName'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
<?php endif; ?>" class="icon">
								<?php echo smarty_function_icon(array('_id' => 'eye_arrow_down','alt' => 'Enable Sub-Structure Monitoring for Group'), $this);?>

							</a>

							<?php echo ((is_array($_tmp=$this->_tpl_vars['g'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

						<?php else: ?>
							<a href="tiki-index.php?page=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;watch_group=<?php echo ((is_array($_tmp=$this->_tpl_vars['g'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;watch_event=structure_changed&amp;watch_object=<?php echo ((is_array($_tmp=$this->_tpl_vars['page_info']['page_ref_id'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;watch_action=remove_desc<?php if ($this->_tpl_vars['structure'] == 'y'): ?>&amp;structure=<?php echo ((is_array($_tmp=$this->_tpl_vars['home_info']['pageName'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
<?php endif; ?>" class="icon">
								<?php echo smarty_function_icon(array('_id' => 'no_eye_arrow_down','alt' => 'Disable Sub-Structure Monitoring for Group'), $this);?>

							</a>

							<?php echo ((is_array($_tmp=$this->_tpl_vars['g'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

						<?php endif; ?>
					</div>
				<?php endforeach; endif; unset($_from); ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>
			</div><!-- END of icons -->

	<?php if ($this->_tpl_vars['prefs']['feature_backlinks'] == 'y' && $this->_tpl_vars['backlinks'] && $this->_tpl_vars['tiki_p_view_backlink'] == 'y'): ?>
			<form action="tiki-index.php" method="get" style="display: block; float: left">
				<select name="page" onchange="page.form.submit()">
					<option>Backlinks...</option>
		<?php unset($this->_sections['back']);
$this->_sections['back']['name'] = 'back';
$this->_sections['back']['loop'] = is_array($_loop=$this->_tpl_vars['backlinks']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['back']['show'] = true;
$this->_sections['back']['max'] = $this->_sections['back']['loop'];
$this->_sections['back']['step'] = 1;
$this->_sections['back']['start'] = $this->_sections['back']['step'] > 0 ? 0 : $this->_sections['back']['loop']-1;
if ($this->_sections['back']['show']) {
    $this->_sections['back']['total'] = $this->_sections['back']['loop'];
    if ($this->_sections['back']['total'] == 0)
        $this->_sections['back']['show'] = false;
} else
    $this->_sections['back']['total'] = 0;
if ($this->_sections['back']['show']):

            for ($this->_sections['back']['index'] = $this->_sections['back']['start'], $this->_sections['back']['iteration'] = 1;
                 $this->_sections['back']['iteration'] <= $this->_sections['back']['total'];
                 $this->_sections['back']['index'] += $this->_sections['back']['step'], $this->_sections['back']['iteration']++):
$this->_sections['back']['rownum'] = $this->_sections['back']['iteration'];
$this->_sections['back']['index_prev'] = $this->_sections['back']['index'] - $this->_sections['back']['step'];
$this->_sections['back']['index_next'] = $this->_sections['back']['index'] + $this->_sections['back']['step'];
$this->_sections['back']['first']      = ($this->_sections['back']['iteration'] == 1);
$this->_sections['back']['last']       = ($this->_sections['back']['iteration'] == $this->_sections['back']['total']);
?>
					<option value="<?php echo $this->_tpl_vars['backlinks'][$this->_sections['back']['index']]['fromPage']; ?>
"><?php echo $this->_tpl_vars['backlinks'][$this->_sections['back']['index']]['fromPage']; ?>
</option>
		<?php endfor; endif; ?>
				</select>
			</form>
	<?php endif; ?>

	<?php if ($this->_tpl_vars['structure'] == 'y' && count ( $this->_tpl_vars['showstructs'] ) > 1 || $this->_tpl_vars['structure'] == 'n' && count ( $this->_tpl_vars['showstructs'] ) != 0): ?>
			<form action="tiki-index.php" method="post" style="float: left">
				<select name="page_ref_id" onchange="page_ref_id.form.submit()">
					<option>Structures...</option>
		<?php unset($this->_sections['struct']);
$this->_sections['struct']['name'] = 'struct';
$this->_sections['struct']['loop'] = is_array($_loop=$this->_tpl_vars['showstructs']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['struct']['show'] = true;
$this->_sections['struct']['max'] = $this->_sections['struct']['loop'];
$this->_sections['struct']['step'] = 1;
$this->_sections['struct']['start'] = $this->_sections['struct']['step'] > 0 ? 0 : $this->_sections['struct']['loop']-1;
if ($this->_sections['struct']['show']) {
    $this->_sections['struct']['total'] = $this->_sections['struct']['loop'];
    if ($this->_sections['struct']['total'] == 0)
        $this->_sections['struct']['show'] = false;
} else
    $this->_sections['struct']['total'] = 0;
if ($this->_sections['struct']['show']):

            for ($this->_sections['struct']['index'] = $this->_sections['struct']['start'], $this->_sections['struct']['iteration'] = 1;
                 $this->_sections['struct']['iteration'] <= $this->_sections['struct']['total'];
                 $this->_sections['struct']['index'] += $this->_sections['struct']['step'], $this->_sections['struct']['iteration']++):
$this->_sections['struct']['rownum'] = $this->_sections['struct']['iteration'];
$this->_sections['struct']['index_prev'] = $this->_sections['struct']['index'] - $this->_sections['struct']['step'];
$this->_sections['struct']['index_next'] = $this->_sections['struct']['index'] + $this->_sections['struct']['step'];
$this->_sections['struct']['first']      = ($this->_sections['struct']['iteration'] == 1);
$this->_sections['struct']['last']       = ($this->_sections['struct']['iteration'] == $this->_sections['struct']['total']);
?>
					<option value="<?php echo $this->_tpl_vars['showstructs'][$this->_sections['struct']['index']]['req_page_ref_id']; ?>
" <?php if ($this->_tpl_vars['showstructs'][$this->_sections['struct']['index']]['pageName'] == $this->_tpl_vars['structure_path'][0]['pageName']): ?>selected="selected"<?php endif; ?>>
		<?php if ($this->_tpl_vars['showstructs'][$this->_sections['struct']['index']]['page_alias']): ?> 
			<?php echo $this->_tpl_vars['showstructs'][$this->_sections['struct']['index']]['page_alias']; ?>

		<?php else: ?>
			<?php echo $this->_tpl_vars['showstructs'][$this->_sections['struct']['index']]['pageName']; ?>

		<?php endif; ?>
					</option>
		<?php endfor; endif; ?>
				</select>
			</form>
	<?php endif; ?>

	<?php if ($this->_tpl_vars['prefs']['feature_multilingual'] == 'y' && $this->_tpl_vars['prefs']['show_available_translations'] == 'y'): ?>
			<div style="float: left">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "translated-lang.tpl", 'smarty_include_vars' => array('td' => 'n')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			</div>
	<?php endif; ?>
		</div>
		<br class="clear" style="clear: both" />
<?php endif; ?> 
<?php endif; ?> 
	</div> 
</div> 