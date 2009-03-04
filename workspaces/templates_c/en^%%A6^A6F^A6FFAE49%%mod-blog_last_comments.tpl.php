<?php /* Smarty version 2.6.22, created on 2009-03-04 13:06:20
         compiled from modules/mod-blog_last_comments.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'eval', 'modules/mod-blog_last_comments.tpl', 6, false),array('block', 'tikimodule', 'modules/mod-blog_last_comments.tpl', 11, false),array('modifier', 'tiki_short_datetime', 'modules/mod-blog_last_comments.tpl', 14, false),)), $this); ?>


<?php if ($this->_tpl_vars['prefs']['feature_blogs'] == 'y'): ?>
<?php if (! isset ( $this->_tpl_vars['tpl_module_title'] )): ?>
<?php if ($this->_tpl_vars['nonums'] == 'y'): ?>
<?php echo smarty_function_eval(array('var' => "Last ".($this->_tpl_vars['module_rows'])." blog comments",'assign' => 'tpl_module_title'), $this);?>

<?php else: ?>
<?php echo smarty_function_eval(array('var' => 'Last blog comments','assign' => 'tpl_module_title'), $this);?>

<?php endif; ?>
<?php endif; ?>
<?php $this->_tag_stack[] = array('tikimodule', array('error' => $this->_tpl_vars['module_params']['error'],'title' => $this->_tpl_vars['tpl_module_title'],'name' => 'blog_last_comments','flip' => $this->_tpl_vars['module_params']['flip'],'decorations' => $this->_tpl_vars['module_params']['decorations'],'nobox' => $this->_tpl_vars['module_params']['nobox'],'notitle' => $this->_tpl_vars['module_params']['notitle'])); $_block_repeat=true;smarty_block_tikimodule($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
<?php if ($this->_tpl_vars['nonums'] != 'y'): ?><ol><?php else: ?><ul><?php endif; ?>
    <?php unset($this->_sections['ix']);
$this->_sections['ix']['name'] = 'ix';
$this->_sections['ix']['loop'] = is_array($_loop=$this->_tpl_vars['comments']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
          <li><a class="linkmodule" href="tiki-view_blog_post.php?postId=<?php echo $this->_tpl_vars['comments'][$this->_sections['ix']['index']]['postId']; ?>
&amp;comzone=show#threadId<?php echo $this->_tpl_vars['comments'][$this->_sections['ix']['index']]['threadId']; ?>
" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['comments'][$this->_sections['ix']['index']]['commentDate'])) ? $this->_run_mod_handler('tiki_short_datetime', true, $_tmp) : smarty_modifier_tiki_short_datetime($_tmp)); ?>
, by <?php echo $this->_tpl_vars['comments'][$this->_sections['ix']['index']]['userName']; ?>
<?php if ($this->_tpl_vars['moretooltips'] == 'y'): ?> on blogpost <?php echo $this->_tpl_vars['comments'][$this->_sections['ix']['index']]['title']; ?>
<?php endif; ?>">
		<?php if ($this->_tpl_vars['moretooltips'] != 'y'): ?><b><?php echo $this->_tpl_vars['comments'][$this->_sections['ix']['index']]['title']; ?>
:</b><?php endif; ?>
		<?php echo $this->_tpl_vars['comments'][$this->_sections['ix']['index']]['commentTitle']; ?>

		<?php if ($this->_tpl_vars['module_params']['nodate'] != 'y'): ?>
			<small><?php echo ((is_array($_tmp=$this->_tpl_vars['comments'][$this->_sections['ix']['index']]['commentDate'])) ? $this->_run_mod_handler('tiki_short_datetime', true, $_tmp) : smarty_modifier_tiki_short_datetime($_tmp)); ?>
</small>
		<?php endif; ?>
          </a></li>
    <?php endfor; endif; ?>
<?php if ($this->_tpl_vars['nonums'] != 'y'): ?></ol><?php else: ?></ul><?php endif; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_tikimodule($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
<?php endif; ?>