<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:06
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-tikitests.tpl" */ ?>
<?php /*%%SmartyHeaderCode:302134f1e08ead2f828-92599425%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2b1f6edaaac83444b3496fdec1a712235a37238b' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\modules/mod-tikitests.tpl',
      1 => 1302743604,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '302134f1e08ead2f828-92599425',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_block_tikimodule')) include 'lib/smarty_tiki\block.tikimodule.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-tikitests.tpl -->

<?php if ($_smarty_tpl->getVariable('tiki_p_admin_tikitests')->value=='y'||$_smarty_tpl->getVariable('tiki_p_play_tikitests')->value=='y'||$_smarty_tpl->getVariable('tiki_p_edit_tikitests')->value=='y'){?>
<?php if (!isset($_smarty_tpl->getVariable('tpl_module_title',null,true,false)->value)){?>
<?php $_template = new Smarty_Internal_Template('eval:'."TikiTests Menu", $_smarty_tpl->smarty, $_smarty_tpl);$_smarty_tpl->assign("tpl_module_title",$_template->getRenderedTemplate()); ?>
<?php }?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('tikimodule', array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>"tikitests",'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle'])); $_block_repeat=true; smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>"tikitests",'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

<?php if ($_smarty_tpl->getVariable('tiki_p_admin_tikitests')->value=='y'||$_smarty_tpl->getVariable('tiki_p_play_tikitests')->value=='y'){?>
<div class="option"><a class="linkmodule" href="tiki_tests/tiki-tests_list.php">List Tests</a></div>
<?php }?>
<?php if ($_smarty_tpl->getVariable('tiki_p_admin_tikitests')->value=='y'||$_smarty_tpl->getVariable('tiki_p_edit_tikitests')->value=='y'){?>
<div class="option"><a class="linkmodule" href="tiki_tests/tiki-tests_record.php">Create Test</a></div>
<?php }?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>"tikitests",'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php }?>

<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-tikitests.tpl -->