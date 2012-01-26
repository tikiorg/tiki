<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:04
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\tiki-sitelocbar.tpl" */ ?>
<?php /*%%SmartyHeaderCode:97004f1e08e8cfa546-44769839%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7c18d59a7a7e11e27812c79b49169e6eb81892b7' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\tiki-sitelocbar.tpl',
      1 => 1311336370,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '97004f1e08e8cfa546-44769839',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_breadcrumbs')) include 'lib/smarty_tiki\function.breadcrumbs.php';
if (!is_callable('smarty_block_tr')) include 'lib/smarty_tiki\block.tr.php';
if (!is_callable('smarty_modifier_escape')) include 'lib/smarty_tiki\modifier.escape.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\tiki-sitelocbar.tpl -->

<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_siteloc']=='y'&&$_smarty_tpl->getVariable('prefs')->value['feature_breadcrumbs']=='y'){?>
		<div id="sitelocbar">
			<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_siteloclabel']=='y'){?>Location : <?php }?><?php if ($_smarty_tpl->getVariable('trail')->value){?><?php echo smarty_function_breadcrumbs(array('type'=>"trail",'loc'=>"site",'crumbs'=>$_smarty_tpl->getVariable('trail')->value),$_smarty_tpl);?>
<?php echo smarty_function_breadcrumbs(array('type'=>"pagetitle",'loc'=>"site",'crumbs'=>$_smarty_tpl->getVariable('trail')->value),$_smarty_tpl);?>
<?php }else{ ?><a title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('tr', array()); $_block_repeat=true; smarty_block_tr(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->getVariable('crumbs')->value[0]->description;?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tr(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" href="<?php echo $_smarty_tpl->getVariable('crumbs')->value[0]->url;?>
" accesskey="1"><?php echo $_smarty_tpl->getVariable('crumbs')->value[0]->title;?>
</a>
		<?php if ($_smarty_tpl->getVariable('structure')->value=='y'){?>
			<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['ix']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('structure_path')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['name'] = 'ix';
$_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['total']);
?>
				<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['site_crumb_seper'],"html");?>

				<?php if ($_smarty_tpl->getVariable('structure_path')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['pageName']!=$_smarty_tpl->getVariable('page')->value||$_smarty_tpl->getVariable('structure_path')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['page_alias']!=$_smarty_tpl->getVariable('page_info')->value['page_alias']){?>
			<a href="tiki-index.php?page_ref_id=<?php echo $_smarty_tpl->getVariable('structure_path')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['page_ref_id'];?>
">
				<?php }?>
				<?php if ($_smarty_tpl->getVariable('structure_path')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['page_alias']){?>
					<?php echo $_smarty_tpl->getVariable('structure_path')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['page_alias'];?>

				<?php }else{ ?>
					<?php echo $_smarty_tpl->getVariable('structure_path')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['pageName'];?>

				<?php }?>
				<?php if ($_smarty_tpl->getVariable('structure_path')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['pageName']!=$_smarty_tpl->getVariable('page')->value||$_smarty_tpl->getVariable('structure_path')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['page_alias']!=$_smarty_tpl->getVariable('page_info')->value['page_alias']){?>
					</a>
				<?php }?>
			<?php endfor; endif; ?>
		<?php }else{ ?>
			<?php if ($_smarty_tpl->getVariable('page')->value!=''){?><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['site_crumb_seper'],"html");?>
 <?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value);?>

			<?php }elseif($_smarty_tpl->getVariable('title')->value!=''){?><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['site_crumb_seper'],"html");?>
 <?php echo $_smarty_tpl->getVariable('title')->value;?>

			<?php }elseif($_smarty_tpl->getVariable('thread_info')->value['title']!=''){?><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['site_crumb_seper'],"html");?>
 <?php echo $_smarty_tpl->getVariable('thread_info')->value['title'];?>

			<?php }elseif($_smarty_tpl->getVariable('forum_info')->value['name']!=''){?><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['site_crumb_seper'],"html");?>
 <?php echo $_smarty_tpl->getVariable('forum_info')->value['name'];?>
<?php }?>
		<?php }?>
	<?php }?>
		</div>
	<?php if ($_smarty_tpl->getVariable('trail')->value){?><?php echo smarty_function_breadcrumbs(array('type'=>"desc",'loc'=>"site",'crumbs'=>$_smarty_tpl->getVariable('trail')->value),$_smarty_tpl);?>

	<?php }else{ ?><?php echo smarty_function_breadcrumbs(array('type'=>"desc",'loc'=>"site",'crumbs'=>$_smarty_tpl->getVariable('crumbs')->value),$_smarty_tpl);?>
<?php }?>
<?php }?>
<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\tiki-sitelocbar.tpl -->