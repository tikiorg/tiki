<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:09
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-share.tpl" */ ?>
<?php /*%%SmartyHeaderCode:302114f1e08ed526e94-22241802%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6586aabd08cc24fb378471a3c780050c5df825f6' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\modules/mod-share.tpl',
      1 => 1314275014,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '302114f1e08ed526e94-22241802',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_block_tikimodule')) include 'lib/smarty_tiki\block.tikimodule.php';
if (!is_callable('smarty_modifier_escape')) include 'lib/smarty_tiki\modifier.escape.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-share.tpl -->
<?php $_smarty_tpl->smarty->_tag_stack[] = array('tikimodule', array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>$_smarty_tpl->getVariable('tpl_module_name')->value,'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle'])); $_block_repeat=true; smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>$_smarty_tpl->getVariable('tpl_module_name')->value,'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<div id="site_report"><?php if ((!isset($_smarty_tpl->getVariable('module_params',null,true,false)->value['report'])||$_smarty_tpl->getVariable('module_params')->value['report']!='n')&&$_smarty_tpl->getVariable('tiki_p_site_report')->value=='y'){?><a href="tiki-tell_a_friend.php?report=y&amp;url=<?php echo smarty_modifier_escape($_SERVER['REQUEST_URI'],'url');?>
">Report to Webmaster</a><?php }?><?php if ((!isset($_smarty_tpl->getVariable('module_params',null,true,false)->value['share'])||$_smarty_tpl->getVariable('module_params')->value['share']!='n')&&$_smarty_tpl->getVariable('tiki_p_tell_a_friend')->value=='y'){?><a href="tiki-share.php?url=<?php echo smarty_modifier_escape($_SERVER['REQUEST_URI'],'url');?>
">Share this page</a><?php }?><?php if ((!isset($_smarty_tpl->getVariable('module_params',null,true,false)->value['email'])||$_smarty_tpl->getVariable('module_params')->value['email']!='n')&&$_smarty_tpl->getVariable('tiki_p_tell_a_friend')->value=='y'){?><a href="tiki-tell_a_friend.php?url=<?php echo smarty_modifier_escape($_SERVER['REQUEST_URI'],'url');?>
">Email this page</a><?php }?></div><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>$_smarty_tpl->getVariable('tpl_module_name')->value,'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-share.tpl -->