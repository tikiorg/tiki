<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:09
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-svnup.tpl" */ ?>
<?php /*%%SmartyHeaderCode:35004f1e08edb0b129-41244936%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '69d0b10586282e1359a92d79adac875428dc0a59' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\modules/mod-svnup.tpl',
      1 => 1295276556,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '35004f1e08edb0b129-41244936',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_block_tikimodule')) include 'lib/smarty_tiki\block.tikimodule.php';
if (!is_callable('smarty_modifier_tiki_long_datetime')) include 'lib/smarty_tiki\modifier.tiki_long_datetime.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-svnup.tpl -->
<?php $_smarty_tpl->smarty->_tag_stack[] = array('tikimodule', array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>$_smarty_tpl->getVariable('tpl_module_name')->value,'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle'])); $_block_repeat=true; smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>$_smarty_tpl->getVariable('tpl_module_name')->value,'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php if (!empty($_smarty_tpl->getVariable('lastup',null,true,false)->value)){?><div class="cvsup" style="font-size:x-small;text-align:center;color:#999;">Last update from SVN (<?php echo $_smarty_tpl->getVariable('tiki_version')->value;?>
): <?php echo smarty_modifier_tiki_long_datetime($_smarty_tpl->getVariable('lastup')->value);?>
<?php }?><?php if (!empty($_smarty_tpl->getVariable('svnrev',null,true,false)->value)){?>- REV <?php echo $_smarty_tpl->getVariable('svnrev')->value;?>
<?php }?><?php if (!empty($_smarty_tpl->getVariable('lastup',null,true,false)->value)||!empty($_smarty_tpl->getVariable('svnrev',null,true,false)->value)){?></div><?php }?><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>$_smarty_tpl->getVariable('tpl_module_name')->value,'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-svnup.tpl -->