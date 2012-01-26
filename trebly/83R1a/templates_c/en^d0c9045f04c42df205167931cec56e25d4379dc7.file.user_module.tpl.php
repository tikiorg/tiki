<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:02
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/user_module.tpl" */ ?>
<?php /*%%SmartyHeaderCode:37544f1e08e6a6c857-47640114%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd0c9045f04c42df205167931cec56e25d4379dc7' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\modules/user_module.tpl',
      1 => 1302743604,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '37544f1e08e6a6c857-47640114',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_block_tikimodule')) include 'lib/smarty_tiki\block.tikimodule.php';
if (!is_callable('smarty_modifier_stringfix')) include 'lib/smarty_tiki\modifier.stringfix.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/user_module.tpl -->
<?php $_smarty_tpl->smarty->_tag_stack[] = array('tikimodule', array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('user_title')->value,'name'=>$_smarty_tpl->getVariable('user_module_name')->value,'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'overflow'=>$_smarty_tpl->getVariable('module_params')->value['overflow'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle'],'type'=>$_smarty_tpl->getVariable('module_type')->value)); $_block_repeat=true; smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('user_title')->value,'name'=>$_smarty_tpl->getVariable('user_module_name')->value,'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'overflow'=>$_smarty_tpl->getVariable('module_params')->value['overflow'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle'],'type'=>$_smarty_tpl->getVariable('module_type')->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>


<div id="<?php echo smarty_modifier_stringfix($_smarty_tpl->getVariable('user_module_name')->value,' ','_');?>
" <?php if ((isset($_COOKIE[$_smarty_tpl->getVariable('user_module_name',null,true,false)->value])&&$_COOKIE[$_smarty_tpl->getVariable('user_module_name')->value]!='c')||!isset($_COOKIE[$_smarty_tpl->getVariable('user_module_name',null,true,false)->value])){?>style="display:block;"<?php }else{ ?>style="display:none;"<?php }?>>
<?php $_template = new Smarty_Internal_Template('eval:'.$_smarty_tpl->getVariable('user_data')->value, $_smarty_tpl->smarty, $_smarty_tpl);echo $_template->getRenderedTemplate(); ?>
</div>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('user_title')->value,'name'=>$_smarty_tpl->getVariable('user_module_name')->value,'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'overflow'=>$_smarty_tpl->getVariable('module_params')->value['overflow'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle'],'type'=>$_smarty_tpl->getVariable('module_type')->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/user_module.tpl -->