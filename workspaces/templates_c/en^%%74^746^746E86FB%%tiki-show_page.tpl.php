<?php /* Smarty version 2.6.22, created on 2009-03-04 13:06:18
         compiled from tiki-show_page.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'breadcrumbs', 'tiki-show_page.tpl', 10, false),array('function', 'icon', 'tiki-show_page.tpl', 67, false),array('modifier', 'sefurl', 'tiki-show_page.tpl', 17, false),array('modifier', 'escape', 'tiki-show_page.tpl', 23, false),array('modifier', 'lower', 'tiki-show_page.tpl', 76, false),array('modifier', 'userlink', 'tiki-show_page.tpl', 199, false),array('modifier', 'tiki_long_datetime', 'tiki-show_page.tpl', 209, false),array('block', 'remarksbox', 'tiki-show_page.tpl', 49, false),array('block', 'tr', 'tiki-show_page.tpl', 184, false),)), $this); ?>
 
<?php if ($this->_tpl_vars['prefs']['feature_ajax'] == 'y'): ?>
  <script type="text/javascript" src="lib/wiki/wiki-ajax.js"></script>
<?php endif; ?>

<?php if (! $this->_tpl_vars['hide_page_header']): ?>
	
	<?php if ($this->_tpl_vars['prefs']['feature_siteloc'] == 'page' && $this->_tpl_vars['prefs']['feature_breadcrumbs'] == 'y'): ?>
		<?php if ($this->_tpl_vars['prefs']['feature_siteloclabel'] == 'y'): ?>Location : <?php endif; ?>
		<?php echo smarty_function_breadcrumbs(array('type' => 'trail','loc' => 'page','crumbs' => $this->_tpl_vars['crumbs']), $this);?>

		<?php if ($this->_tpl_vars['prefs']['feature_page_title'] == 'y'): ?><?php echo smarty_function_breadcrumbs(array('type' => 'pagetitle','loc' => 'page','crumbs' => $this->_tpl_vars['crumbs']), $this);?>
<?php endif; ?>
	<?php endif; ?>

<?php if ($this->_tpl_vars['beingStaged'] == 'y'): ?>
<div class="tocnav">
<?php if ($this->_tpl_vars['approvedPageExists']): ?>
	This is the staging copy of <a class="link" href="<?php echo ((is_array($_tmp=$this->_tpl_vars['approvedPageName'])) ? $this->_run_mod_handler('sefurl', true, $_tmp) : smarty_modifier_sefurl($_tmp)); ?>
">the approved version of this page.</a>
<?php else: ?>
	This is a new staging page that has not been approved before.
<?php endif; ?>
<?php if ($this->_tpl_vars['outOfSync'] == 'y'): ?>
	<?php if ($this->_tpl_vars['canApproveStaging'] == 'y'): ?>
	<?php if ($this->_tpl_vars['lastSyncVersion']): ?><a class="link" href="tiki-pagehistory.php?page=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;diff2=<?php echo $this->_tpl_vars['lastSyncVersion']; ?>
">View changes since last approval.</a>
	<?php else: ?>Viewing of changes since last approval is possible only after first approval.<?php endif; ?>
	<a class="link" href="tiki-approve_staging_page.php?page=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
">Approve changes.</a>
	<?php elseif ($this->_tpl_vars['approvedPageExists']): ?>
	Latest changes will be synchronized after approval.
	<?php endif; ?>
<?php endif; ?>
</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['needsFirstApproval'] == 'y' && $this->_tpl_vars['canApproveStaging'] == 'y'): ?>
<div class="tocnav">
This is a new staging page that has not been approved before. Edit and manually move it to the category for approved pages to approve it for the first time.
</div>
<?php endif; ?>
<?php endif; ?> 

<?php if (! $this->_tpl_vars['prefs']['wiki_topline_position'] || $this->_tpl_vars['prefs']['wiki_topline_position'] == 'top' || $this->_tpl_vars['prefs']['wiki_topline_position'] == 'both'): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "tiki-wiki_topline.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['print_page'] != 'y'): ?>
<?php if ($this->_tpl_vars['prefs']['page_bar_position'] == 'top'): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "tiki-page_bar.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<?php endif; ?>

<?php if (isset ( $this->_tpl_vars['saved_msg'] ) && $this->_tpl_vars['saved_msg'] != ''): ?>
<?php $this->_tag_stack[] = array('remarksbox', array('type' => 'note','title' => 'Note')); $_block_repeat=true;smarty_block_remarksbox($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['saved_msg']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_remarksbox($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
<?php endif; ?>

<div class="navbar" style="clear: both; text-align: right">
    <?php if ($this->_tpl_vars['user'] && $this->_tpl_vars['prefs']['feature_user_watches'] == 'y'): ?>
        <?php if ($this->_tpl_vars['category_watched'] == 'y'): ?>
            Watched by categories:
            <?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['watching_categories']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
			    <a href="tiki-browse_categories?parentId=<?php echo $this->_tpl_vars['watching_categories'][$this->_sections['i']['index']]['categId']; ?>
"><?php echo $this->_tpl_vars['watching_categories'][$this->_sections['i']['index']]['name']; ?>
</a>&nbsp;
            <?php endfor; endif; ?>
        <?php endif; ?>			
    <?php endif; ?>
</div>

<?php if ($this->_tpl_vars['prefs']['feature_urgent_translation'] == 'y'): ?>
	<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['translation_alert']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
	<div class="cbox">
	<div class="cbox-title">
	<?php echo smarty_function_icon(array('_id' => 'information','style' => "vertical-align:middle"), $this);?>
 Content may be out of date
	</div>
	<div class="cbox-data">
		<p>An urgent request for translation has been sent. Until this page is updated, you can see a corrected version in the following pages:</p>
		<ul>
		<?php unset($this->_sections['j']);
$this->_sections['j']['name'] = 'j';
$this->_sections['j']['loop'] = is_array($_loop=$this->_tpl_vars['translation_alert'][$this->_sections['i']['index']]) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['j']['show'] = true;
$this->_sections['j']['max'] = $this->_sections['j']['loop'];
$this->_sections['j']['step'] = 1;
$this->_sections['j']['start'] = $this->_sections['j']['step'] > 0 ? 0 : $this->_sections['j']['loop']-1;
if ($this->_sections['j']['show']) {
    $this->_sections['j']['total'] = $this->_sections['j']['loop'];
    if ($this->_sections['j']['total'] == 0)
        $this->_sections['j']['show'] = false;
} else
    $this->_sections['j']['total'] = 0;
if ($this->_sections['j']['show']):

            for ($this->_sections['j']['index'] = $this->_sections['j']['start'], $this->_sections['j']['iteration'] = 1;
                 $this->_sections['j']['iteration'] <= $this->_sections['j']['total'];
                 $this->_sections['j']['index'] += $this->_sections['j']['step'], $this->_sections['j']['iteration']++):
$this->_sections['j']['rownum'] = $this->_sections['j']['iteration'];
$this->_sections['j']['index_prev'] = $this->_sections['j']['index'] - $this->_sections['j']['step'];
$this->_sections['j']['index_next'] = $this->_sections['j']['index'] + $this->_sections['j']['step'];
$this->_sections['j']['first']      = ($this->_sections['j']['iteration'] == 1);
$this->_sections['j']['last']       = ($this->_sections['j']['iteration'] == $this->_sections['j']['total']);
?>
			<li>
				<a href="<?php if ($this->_tpl_vars['translation_alert'][$this->_sections['i']['index']][$this->_sections['j']['index']]['approvedPage'] && $this->_tpl_vars['hasStaging'] == 'y'): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['translation_alert'][$this->_sections['i']['index']][$this->_sections['j']['index']]['approvedPage'])) ? $this->_run_mod_handler('sefurl', true, $_tmp, 'wiki', 'with_next') : smarty_modifier_sefurl($_tmp, 'wiki', 'with_next')); ?>
<?php else: ?><?php echo ((is_array($_tmp=$this->_tpl_vars['translation_alert'][$this->_sections['i']['index']][$this->_sections['j']['index']]['page'])) ? $this->_run_mod_handler('sefurl', true, $_tmp, 'wiki', 'with_next') : smarty_modifier_sefurl($_tmp, 'wiki', 'with_next')); ?>
<?php endif; ?>bl=n"><?php if ($this->_tpl_vars['translation_alert'][$this->_sections['i']['index']][$this->_sections['j']['index']]['approvedPage'] && $this->_tpl_vars['hasStaging'] == 'y'): ?><?php echo $this->_tpl_vars['translation_alert'][$this->_sections['i']['index']][$this->_sections['j']['index']]['approvedPage']; ?>
<?php else: ?><?php echo $this->_tpl_vars['translation_alert'][$this->_sections['i']['index']][$this->_sections['j']['index']]['page']; ?>
<?php endif; ?></a>
				(<?php echo $this->_tpl_vars['translation_alert'][$this->_sections['i']['index']][$this->_sections['j']['index']]['lang']; ?>
)
				<?php if ($this->_tpl_vars['editable'] && ( $this->_tpl_vars['tiki_p_edit'] == 'y' || ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('lower', true, $_tmp) : smarty_modifier_lower($_tmp)) == 'sandbox' ) && $this->_tpl_vars['beingEdited'] != 'y' || $this->_tpl_vars['canEditStaging'] == 'y'): ?> 
				<a href="tiki-editpage.php?page=<?php if (isset ( $this->_tpl_vars['stagingPageName'] ) && $this->_tpl_vars['hasStaging'] == 'y'): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['stagingPageName'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
<?php else: ?><?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
<?php endif; ?>&amp;source_page=<?php echo ((is_array($_tmp=$this->_tpl_vars['translation_alert'][$this->_sections['i']['index']][$this->_sections['j']['index']]['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;oldver=<?php echo ((is_array($_tmp=$this->_tpl_vars['translation_alert'][$this->_sections['i']['index']][$this->_sections['j']['index']]['last_update'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;newver=<?php echo ((is_array($_tmp=$this->_tpl_vars['translation_alert'][$this->_sections['i']['index']][$this->_sections['j']['index']]['current_version'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;diff_style=htmldiff" title="update from it"><?php echo smarty_function_icon(array('_id' => 'arrow_refresh','alt' => 'update from it','style' => "vertical-align:middle"), $this);?>
</a>
				<?php endif; ?>
			</li>
		<?php endfor; endif; ?>
		</ul>
	</div>
	</div>
	<?php endfor; endif; ?>
<?php endif; ?>

<?php if (! $this->_tpl_vars['hide_page_header']): ?>
<?php if ($this->_tpl_vars['prefs']['feature_freetags'] == 'y' && $this->_tpl_vars['tiki_p_view_freetags'] == 'y' && isset ( $this->_tpl_vars['freetags']['data'][0] ) && $this->_tpl_vars['prefs']['freetags_show_middle'] == 'y'): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "freetag_list.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['pages'] > 1 && $this->_tpl_vars['prefs']['wiki_page_navigation_bar'] != 'bottom'): ?>
	<div align="center">
		<a href="tiki-index.php?<?php if ($this->_tpl_vars['page_info']): ?>page_ref_id=<?php echo $this->_tpl_vars['page_info']['page_ref_id']; ?>
<?php else: ?>page=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
<?php endif; ?>&amp;pagenum=<?php echo $this->_tpl_vars['first_page']; ?>
"><?php echo smarty_function_icon(array('_id' => 'resultset_first','alt' => 'First page'), $this);?>
</a>

		<a href="tiki-index.php?<?php if ($this->_tpl_vars['page_info']): ?>page_ref_id=<?php echo $this->_tpl_vars['page_info']['page_ref_id']; ?>
<?php else: ?>page=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
<?php endif; ?>&amp;pagenum=<?php echo $this->_tpl_vars['prev_page']; ?>
"><?php echo smarty_function_icon(array('_id' => 'resultset_previous','alt' => 'Previous page'), $this);?>
</a>

		<small>page:<?php echo $this->_tpl_vars['pagenum']; ?>
/<?php echo $this->_tpl_vars['pages']; ?>
</small>

		<a href="tiki-index.php?<?php if ($this->_tpl_vars['page_info']): ?>page_ref_id=<?php echo $this->_tpl_vars['page_info']['page_ref_id']; ?>
<?php else: ?>page=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
<?php endif; ?>&amp;pagenum=<?php echo $this->_tpl_vars['next_page']; ?>
"><?php echo smarty_function_icon(array('_id' => 'resultset_next','alt' => 'Next page'), $this);?>
</a>


		<a href="tiki-index.php?<?php if ($this->_tpl_vars['page_info']): ?>page_ref_id=<?php echo $this->_tpl_vars['page_info']['page_ref_id']; ?>
<?php else: ?>page=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
<?php endif; ?>&amp;pagenum=<?php echo $this->_tpl_vars['last_page']; ?>
"><?php echo smarty_function_icon(array('_id' => 'resultset_last','alt' => 'Last page'), $this);?>
</a>
	</div>
<?php endif; ?>

<div class="wikitext">


<?php if ($this->_tpl_vars['prefs']['feature_page_title'] == 'y'): ?>
	<h1 class="pagetitle"><?php echo smarty_function_breadcrumbs(array('type' => 'pagetitle','loc' => 'page','crumbs' => $this->_tpl_vars['crumbs']), $this);?>
</h1>
    
<?php endif; ?>

<?php if ($this->_tpl_vars['structure'] == 'y'): ?>
<div class="tocnav">
<table>
<tr>
  <td>

    <?php if ($this->_tpl_vars['prev_info'] && $this->_tpl_vars['prev_info']['page_ref_id']): ?><?php if ($this->_tpl_vars['prev_info']['page_alias']): ?><?php $this->assign('icon_title', $this->_tpl_vars['prev_info']['page_alias']); ?><?php else: ?><?php $this->assign('icon_title', $this->_tpl_vars['prev_info']['pageName']); ?><?php endif; ?><a href="<?php echo ((is_array($_tmp=$this->_tpl_vars['prev_info']['pageName'])) ? $this->_run_mod_handler('sefurl', true, $_tmp, 'wiki', 'with_next') : smarty_modifier_sefurl($_tmp, 'wiki', 'with_next')); ?>
structure=<?php echo ((is_array($_tmp=$this->_tpl_vars['home_info']['pageName'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
"><?php echo smarty_function_icon(array('_id' => 'resultset_previous','alt' => 'Previous page','title' => $this->_tpl_vars['icon_title']), $this);?>
</a><?php else: ?><img src="img/icons2/8.gif" alt="" height="1" width="8" /><?php endif; ?>

    <?php if ($this->_tpl_vars['parent_info']): ?><?php if ($this->_tpl_vars['parent_info']['page_alias']): ?><?php $this->assign('icon_title', $this->_tpl_vars['parent_info']['page_alias']); ?><?php else: ?><?php $this->assign('icon_title', $this->_tpl_vars['parent_info']['pageName']); ?><?php endif; ?><a href="<?php echo ((is_array($_tmp=$this->_tpl_vars['parent_info']['pageName'])) ? $this->_run_mod_handler('sefurl', true, $_tmp, 'wiki', 'with_next') : smarty_modifier_sefurl($_tmp, 'wiki', 'with_next')); ?>
structure=<?php echo ((is_array($_tmp=$this->_tpl_vars['home_info']['pageName'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
"><?php echo smarty_function_icon(array('_id' => 'resultset_up','alt' => 'Parent page','title' => $this->_tpl_vars['icon_title']), $this);?>
</a><?php else: ?><img src="img/icons2/8.gif" alt="" height="1" width="8" /><?php endif; ?>

    <?php if ($this->_tpl_vars['next_info'] && $this->_tpl_vars['next_info']['page_ref_id']): ?><?php if ($this->_tpl_vars['next_info']['page_alias']): ?><?php $this->assign('icon_title', $this->_tpl_vars['next_info']['page_alias']); ?><?php else: ?><?php $this->assign('icon_title', $this->_tpl_vars['next_info']['pageName']); ?><?php endif; ?><a href="<?php echo ((is_array($_tmp=$this->_tpl_vars['next_info']['pageName'])) ? $this->_run_mod_handler('sefurl', true, $_tmp, 'wiki', 'with_next') : smarty_modifier_sefurl($_tmp, 'wiki', 'with_next')); ?>
structure=<?php echo ((is_array($_tmp=$this->_tpl_vars['home_info']['pageName'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
"><?php echo smarty_function_icon(array('_id' => 'resultset_next','alt' => 'Next page','title' => $this->_tpl_vars['icon_title']), $this);?>
</a><?php else: ?><img src="img/icons2/8.gif" alt="" height="1" width="8" /><?php endif; ?>

    <?php if ($this->_tpl_vars['home_info']): ?><?php if ($this->_tpl_vars['home_info']['page_alias']): ?><?php $this->assign('icon_title', $this->_tpl_vars['home_info']['page_alias']); ?><?php else: ?><?php $this->assign('icon_title', $this->_tpl_vars['home_info']['pageName']); ?><?php endif; ?><a href="<?php echo ((is_array($_tmp=$this->_tpl_vars['home_info']['pageName'])) ? $this->_run_mod_handler('sefurl', true, $_tmp, 'wiki', 'with_next') : smarty_modifier_sefurl($_tmp, 'wiki', 'with_next')); ?>
structure=<?php echo ((is_array($_tmp=$this->_tpl_vars['home_info']['pageName'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
"><?php echo smarty_function_icon(array('_id' => 'house','alt' => 'TOC','title' => $this->_tpl_vars['icon_title']), $this);?>
</a><?php endif; ?>

  </td>
  <td>
<?php if ($this->_tpl_vars['tiki_p_edit_structures'] && $this->_tpl_vars['tiki_p_edit_structures'] == 'y' && $this->_tpl_vars['struct_editable'] == 'y'): ?>
    <form action="tiki-editpage.php" method="post">
      <input type="hidden" name="current_page_id" value="<?php echo $this->_tpl_vars['page_info']['page_ref_id']; ?>
" />
      <input type="text" name="page" />
      
      <?php if ($this->_tpl_vars['page_info'] && ! $this->_tpl_vars['parent_info']): ?>
      <input type="hidden" name="add_child" value="checked" /> 
      <?php else: ?>
      <input type="checkbox" name="add_child" /> Child
      <?php endif; ?>      
      <input type="submit" name="insert_into_struct" value="Add Page" />
    </form>
<?php endif; ?>
  </td>
</tr>
<tr>
  <td colspan="2">
  	<a href="tiki-edit_structure.php?page_ref_id=<?php echo $this->_tpl_vars['home_info']['page_ref_id']; ?>
"><?php echo smarty_function_icon(array('_id' => 'chart_organisation','alt' => 'Structure'), $this);?>
</a>&nbsp;&nbsp;
	(<?php echo $this->_tpl_vars['cur_pos']; ?>
)&nbsp;&nbsp;	
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
      <?php if ($this->_tpl_vars['structure_path'][$this->_sections['ix']['index']]['parent_id']): ?>&nbsp;<?php echo $this->_tpl_vars['prefs']['site_crumb_seper']; ?>
&nbsp;<?php endif; ?>
	  <a href="<?php echo ((is_array($_tmp=$this->_tpl_vars['structure_path'][$this->_sections['ix']['index']]['pageName'])) ? $this->_run_mod_handler('sefurl', true, $_tmp, 'wiki', 'with_next') : smarty_modifier_sefurl($_tmp, 'wiki', 'with_next')); ?>
structure=<?php echo ((is_array($_tmp=$this->_tpl_vars['home_info']['pageName'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
">
      <?php if ($this->_tpl_vars['structure_path'][$this->_sections['ix']['index']]['page_alias']): ?>
        <?php echo $this->_tpl_vars['structure_path'][$this->_sections['ix']['index']]['page_alias']; ?>

	  <?php else: ?>
        <?php echo $this->_tpl_vars['structure_path'][$this->_sections['ix']['index']]['pageName']; ?>

	  <?php endif; ?>
	  </a>
	<?php endfor; endif; ?>
  </td>
</tr>
</table>
</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['prefs']['feature_wiki_ratings'] == 'y'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "poll.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?>
<?php endif; ?> 

<?php if ($this->_tpl_vars['pageLang'] == 'ar' || $this->_tpl_vars['pageLang'] == 'he'): ?>
<div style="direction:RTL; unicode-bidi:embed; text-align: right; <?php if ($this->_tpl_vars['pageLang'] == 'ar'): ?>font-size: large;<?php endif; ?>">
<?php echo $this->_tpl_vars['parsed']; ?>

</div>
<?php else: ?>
<?php echo $this->_tpl_vars['parsed']; ?>

<?php endif; ?>
<hr style="clear:both; height:0px;"/> 

<?php if ($this->_tpl_vars['pages'] > 1 && $this->_tpl_vars['prefs']['wiki_page_navigation_bar'] != 'top'): ?>
	<br />
	<div align="center">
		<a href="tiki-index.php?<?php if ($this->_tpl_vars['page_info']): ?>page_ref_id=<?php echo $this->_tpl_vars['page_info']['page_ref_id']; ?>
<?php else: ?>page=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
<?php endif; ?>&amp;pagenum=<?php echo $this->_tpl_vars['first_page']; ?>
"><?php echo smarty_function_icon(array('_id' => 'resultset_first','alt' => 'First page'), $this);?>
</a>

		<a href="tiki-index.php?<?php if ($this->_tpl_vars['page_info']): ?>page_ref_id=<?php echo $this->_tpl_vars['page_info']['page_ref_id']; ?>
<?php else: ?>page=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
<?php endif; ?>&amp;pagenum=<?php echo $this->_tpl_vars['prev_page']; ?>
"><?php echo smarty_function_icon(array('_id' => 'resultset_previous','alt' => 'Previous page'), $this);?>
</a>

		<small><?php $this->_tag_stack[] = array('tr', array('0' => $this->_tpl_vars['pagenum'],'1' => $this->_tpl_vars['pages'])); $_block_repeat=true;smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>page: %0/%1<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></small>

		<a href="tiki-index.php?<?php if ($this->_tpl_vars['page_info']): ?>page_ref_id=<?php echo $this->_tpl_vars['page_info']['page_ref_id']; ?>
<?php else: ?>page=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
<?php endif; ?>&amp;pagenum=<?php echo $this->_tpl_vars['next_page']; ?>
"><?php echo smarty_function_icon(array('_id' => 'resultset_next','alt' => 'Next page'), $this);?>
</a>


		<a href="tiki-index.php?<?php if ($this->_tpl_vars['page_info']): ?>page_ref_id=<?php echo $this->_tpl_vars['page_info']['page_ref_id']; ?>
<?php else: ?>page=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
<?php endif; ?>&amp;pagenum=<?php echo $this->_tpl_vars['last_page']; ?>
"><?php echo smarty_function_icon(array('_id' => 'resultset_last','alt' => 'Last page'), $this);?>
</a>
	</div>
<?php endif; ?>
</div> 

<?php if ($this->_tpl_vars['has_footnote'] == 'y'): ?><div class="wikitext" id="wikifootnote"><?php echo $this->_tpl_vars['footnote']; ?>
</div><?php endif; ?>
<?php if ($this->_tpl_vars['wiki_authors_style'] != 'none' || $this->_tpl_vars['prefs']['wiki_feature_copyrights'] == 'y' || $this->_tpl_vars['print_page'] == 'y'): ?>
  <p class="editdate"> 
<?php endif; ?>
<?php if (isset ( $this->_tpl_vars['wiki_authors_style'] ) && $this->_tpl_vars['wiki_authors_style'] == 'business'): ?>
  Last edited by <?php echo ((is_array($_tmp=$this->_tpl_vars['lastUser'])) ? $this->_run_mod_handler('userlink', true, $_tmp) : smarty_modifier_userlink($_tmp)); ?>

  <?php unset($this->_sections['author']);
$this->_sections['author']['name'] = 'author';
$this->_sections['author']['loop'] = is_array($_loop=$this->_tpl_vars['contributors']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['author']['show'] = true;
$this->_sections['author']['max'] = $this->_sections['author']['loop'];
$this->_sections['author']['step'] = 1;
$this->_sections['author']['start'] = $this->_sections['author']['step'] > 0 ? 0 : $this->_sections['author']['loop']-1;
if ($this->_sections['author']['show']) {
    $this->_sections['author']['total'] = $this->_sections['author']['loop'];
    if ($this->_sections['author']['total'] == 0)
        $this->_sections['author']['show'] = false;
} else
    $this->_sections['author']['total'] = 0;
if ($this->_sections['author']['show']):

            for ($this->_sections['author']['index'] = $this->_sections['author']['start'], $this->_sections['author']['iteration'] = 1;
                 $this->_sections['author']['iteration'] <= $this->_sections['author']['total'];
                 $this->_sections['author']['index'] += $this->_sections['author']['step'], $this->_sections['author']['iteration']++):
$this->_sections['author']['rownum'] = $this->_sections['author']['iteration'];
$this->_sections['author']['index_prev'] = $this->_sections['author']['index'] - $this->_sections['author']['step'];
$this->_sections['author']['index_next'] = $this->_sections['author']['index'] + $this->_sections['author']['step'];
$this->_sections['author']['first']      = ($this->_sections['author']['iteration'] == 1);
$this->_sections['author']['last']       = ($this->_sections['author']['iteration'] == $this->_sections['author']['total']);
?>
   <?php if ($this->_sections['author']['first']): ?>, based on work by
   <?php else: ?>
    <?php if (! $this->_sections['author']['last']): ?>,
    <?php else: ?> and
    <?php endif; ?>
   <?php endif; ?>
   <?php echo ((is_array($_tmp=$this->_tpl_vars['contributors'][$this->_sections['author']['index']])) ? $this->_run_mod_handler('userlink', true, $_tmp) : smarty_modifier_userlink($_tmp)); ?>

  <?php endfor; endif; ?>.<br />
  Page last modified on <?php echo ((is_array($_tmp=$this->_tpl_vars['lastModif'])) ? $this->_run_mod_handler('tiki_long_datetime', true, $_tmp) : smarty_modifier_tiki_long_datetime($_tmp)); ?>
. <?php if ($this->_tpl_vars['prefs']['wiki_show_version'] == 'y'): ?>(Version <?php echo $this->_tpl_vars['lastVersion']; ?>
)<?php endif; ?>
<?php elseif (isset ( $this->_tpl_vars['wiki_authors_style'] ) && $this->_tpl_vars['wiki_authors_style'] == 'collaborative'): ?>
  Contributors to this page: <?php echo ((is_array($_tmp=$this->_tpl_vars['lastUser'])) ? $this->_run_mod_handler('userlink', true, $_tmp) : smarty_modifier_userlink($_tmp)); ?>

  <?php unset($this->_sections['author']);
$this->_sections['author']['name'] = 'author';
$this->_sections['author']['loop'] = is_array($_loop=$this->_tpl_vars['contributors']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['author']['show'] = true;
$this->_sections['author']['max'] = $this->_sections['author']['loop'];
$this->_sections['author']['step'] = 1;
$this->_sections['author']['start'] = $this->_sections['author']['step'] > 0 ? 0 : $this->_sections['author']['loop']-1;
if ($this->_sections['author']['show']) {
    $this->_sections['author']['total'] = $this->_sections['author']['loop'];
    if ($this->_sections['author']['total'] == 0)
        $this->_sections['author']['show'] = false;
} else
    $this->_sections['author']['total'] = 0;
if ($this->_sections['author']['show']):

            for ($this->_sections['author']['index'] = $this->_sections['author']['start'], $this->_sections['author']['iteration'] = 1;
                 $this->_sections['author']['iteration'] <= $this->_sections['author']['total'];
                 $this->_sections['author']['index'] += $this->_sections['author']['step'], $this->_sections['author']['iteration']++):
$this->_sections['author']['rownum'] = $this->_sections['author']['iteration'];
$this->_sections['author']['index_prev'] = $this->_sections['author']['index'] - $this->_sections['author']['step'];
$this->_sections['author']['index_next'] = $this->_sections['author']['index'] + $this->_sections['author']['step'];
$this->_sections['author']['first']      = ($this->_sections['author']['iteration'] == 1);
$this->_sections['author']['last']       = ($this->_sections['author']['iteration'] == $this->_sections['author']['total']);
?>
   <?php if (! $this->_sections['author']['last']): ?>,
   <?php else: ?> and
   <?php endif; ?>
   <?php echo ((is_array($_tmp=$this->_tpl_vars['contributors'][$this->_sections['author']['index']])) ? $this->_run_mod_handler('userlink', true, $_tmp) : smarty_modifier_userlink($_tmp)); ?>

  <?php endfor; endif; ?>.<br />
  <?php $this->_tag_stack[] = array('tr', array('0' => ((is_array($_tmp=$this->_tpl_vars['lastModif'])) ? $this->_run_mod_handler('tiki_long_datetime', true, $_tmp) : smarty_modifier_tiki_long_datetime($_tmp)),'1' => ((is_array($_tmp=$this->_tpl_vars['lastUser'])) ? $this->_run_mod_handler('userlink', true, $_tmp) : smarty_modifier_userlink($_tmp)))); $_block_repeat=true;smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Page last modified on %0 by %1<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>. <?php if ($this->_tpl_vars['prefs']['wiki_show_version'] == 'y'): ?>(Version <?php echo $this->_tpl_vars['lastVersion']; ?>
)<?php endif; ?>
<?php elseif (isset ( $this->_tpl_vars['wiki_authors_style'] ) && $this->_tpl_vars['wiki_authors_style'] == 'none'): ?>
<?php elseif (isset ( $this->_tpl_vars['wiki_authors_style'] ) && $this->_tpl_vars['wiki_authors_style'] == 'lastmodif'): ?>
	Page last modified on <?php echo ((is_array($_tmp=$this->_tpl_vars['lastModif'])) ? $this->_run_mod_handler('tiki_long_datetime', true, $_tmp) : smarty_modifier_tiki_long_datetime($_tmp)); ?>

<?php else: ?>
  <?php $this->_tag_stack[] = array('tr', array('0' => ((is_array($_tmp=$this->_tpl_vars['creator'])) ? $this->_run_mod_handler('userlink', true, $_tmp) : smarty_modifier_userlink($_tmp)))); $_block_repeat=true;smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Created by %0<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>.
  <?php $this->_tag_stack[] = array('tr', array('0' => ((is_array($_tmp=$this->_tpl_vars['lastModif'])) ? $this->_run_mod_handler('tiki_long_datetime', true, $_tmp) : smarty_modifier_tiki_long_datetime($_tmp)),'1' => ((is_array($_tmp=$this->_tpl_vars['lastUser'])) ? $this->_run_mod_handler('userlink', true, $_tmp) : smarty_modifier_userlink($_tmp)))); $_block_repeat=true;smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Last Modification: %0 by %1<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>. <?php if ($this->_tpl_vars['prefs']['wiki_show_version'] == 'y'): ?>(Version <?php echo $this->_tpl_vars['lastVersion']; ?>
)<?php endif; ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['wiki_feature_copyrights'] == 'y' && $this->_tpl_vars['prefs']['wikiLicensePage']): ?>
  <?php if ($this->_tpl_vars['prefs']['wikiLicensePage'] == $this->_tpl_vars['page']): ?>
    <?php if ($this->_tpl_vars['tiki_p_edit_copyrights'] == 'y'): ?>
      <br />
      To edit the copyright notices <a href="copyrights.php?page=<?php echo $this->_tpl_vars['copyrightpage']; ?>
">Click Here</a>.
    <?php endif; ?>
  <?php else: ?>
    <br />
    The content on this page is licensed under the terms of the <a href="<?php echo ((is_array($_tmp=$this->_tpl_vars['prefs']['wikiLicensePage'])) ? $this->_run_mod_handler('sefurl', true, $_tmp, 'wiki', 'with_next') : smarty_modifier_sefurl($_tmp, 'wiki', 'with_next')); ?>
copyrightpage=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
"><?php echo $this->_tpl_vars['prefs']['wikiLicensePage']; ?>
</a>.
  <?php endif; ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['print_page'] == 'y'): ?>
    <br />
    The original document is available at <a href="<?php echo $this->_tpl_vars['base_url']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('sefurl', true, $_tmp) : smarty_modifier_sefurl($_tmp)); ?>
"><?php echo $this->_tpl_vars['base_url']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('sefurl', true, $_tmp) : smarty_modifier_sefurl($_tmp)); ?>
</a>
<?php endif; ?>

<?php if ($this->_tpl_vars['wiki_authors_style'] != 'none' || $this->_tpl_vars['prefs']['wiki_feature_copyrights'] == 'y' || $this->_tpl_vars['print_page'] == 'y'): ?>
  </p> 
<?php endif; ?>

<?php if ($this->_tpl_vars['is_categorized'] == 'y' && $this->_tpl_vars['prefs']['feature_categories'] == 'y' && $this->_tpl_vars['prefs']['feature_categoryobjects'] == 'y' && $this->_tpl_vars['tiki_p_view_categories'] == 'y'): ?>
<?php echo $this->_tpl_vars['display_catobjects']; ?>

<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['wiki_topline_position'] == 'bottom' || $this->_tpl_vars['prefs']['wiki_topline_position'] == 'both'): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "tiki-wiki_topline.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['print_page'] != 'y'): ?>
<?php if (! $this->_tpl_vars['prefs']['page_bar_position'] || $this->_tpl_vars['prefs']['page_bar_position'] == 'bottom' || $this->_tpl_vars['prefs']['page_bar_position'] == 'both'): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "tiki-page_bar.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<?php endif; ?>