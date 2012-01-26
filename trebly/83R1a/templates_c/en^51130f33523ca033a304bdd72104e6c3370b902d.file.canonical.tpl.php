<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:11
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\canonical.tpl" */ ?>
<?php /*%%SmartyHeaderCode:88274f1e08efe03e98-22262037%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '51130f33523ca033a304bdd72104e6c3370b902d' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\canonical.tpl',
      1 => 1318858622,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '88274f1e08efe03e98-22262037',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_sefurl')) include 'lib/smarty_tiki\modifier.sefurl.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\canonical.tpl -->
<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_canonical_url']=='y'){?>
	<?php if ($_smarty_tpl->getVariable('mid')->value=='tiki-show_page.tpl'||$_smarty_tpl->getVariable('mid')->value=='tiki-index_p.tpl'){?>
		<link rel="canonical" href="<?php echo $_smarty_tpl->getVariable('base_url')->value;?>
<?php echo smarty_modifier_sefurl($_smarty_tpl->getVariable('page')->value);?>
" />
	<?php }elseif($_smarty_tpl->getVariable('mid')->value=='tiki-view_tracker_item.tpl'){?>
		<link rel="canonical" href="<?php echo $_smarty_tpl->getVariable('base_url')->value;?>
<?php echo smarty_modifier_sefurl($_smarty_tpl->getVariable('itemId')->value,'trackeritem');?>
" />
	<?php }elseif($_smarty_tpl->getVariable('mid')->value=='tiki-view_forum_thread.tpl'){?>
		<link rel="canonical" href="<?php echo $_smarty_tpl->getVariable('base_url')->value;?>
tiki-view_forum_thread.php?comments_parentId=<?php echo $_smarty_tpl->getVariable('comments_parentId')->value;?>
" />
	<?php }elseif($_smarty_tpl->getVariable('mid')->value=='tiki-view_blog_post.tpl'){?>
		<link rel="canonical" href="<?php echo $_smarty_tpl->getVariable('base_url')->value;?>
<?php echo smarty_modifier_sefurl($_smarty_tpl->getVariable('postId')->value,'blogpost');?>
" />
	<?php }elseif($_smarty_tpl->getVariable('mid')->value=='tiki-read_article.tpl'){?>
		<link rel="canonical" href="<?php echo $_smarty_tpl->getVariable('base_url')->value;?>
<?php echo smarty_modifier_sefurl($_smarty_tpl->getVariable('articleId')->value,'article');?>
" />
	<?php }?>
<?php }?>
<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\canonical.tpl -->