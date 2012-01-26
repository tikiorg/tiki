<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:09
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-loadstats.tpl" */ ?>
<?php /*%%SmartyHeaderCode:194544f1e08ed992502-28044776%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9c07cf6820a025aafd93c0fab3486e3ef7856bb7' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\modules/mod-loadstats.tpl',
      1 => 1314275014,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '194544f1e08ed992502-28044776',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_block_tikimodule')) include 'lib/smarty_tiki\block.tikimodule.php';
if (!is_callable('smarty_function_elapsed')) include 'lib/smarty_tiki\function.elapsed.php';
if (!is_callable('smarty_function_memusage')) include 'lib/smarty_tiki\function.memusage.php';
if (!is_callable('smarty_modifier_truncate')) include 'lib/smarty_tiki\modifier.truncate.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-loadstats.tpl -->
<?php $_smarty_tpl->smarty->_tag_stack[] = array('tikimodule', array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>$_smarty_tpl->getVariable('tpl_module_name')->value,'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle'])); $_block_repeat=true; smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>$_smarty_tpl->getVariable('tpl_module_name')->value,'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<small>[ Execution time: <?php echo smarty_function_elapsed(array(),$_smarty_tpl);?>
 secs ] &nbsp; [ Memory usage: <?php echo smarty_function_memusage(array(),$_smarty_tpl);?>
 ] &nbsp; [ <?php echo $_smarty_tpl->getVariable('num_queries')->value;?>
 database queries used in  <?php echo smarty_modifier_truncate($_smarty_tpl->getVariable('elapsed_in_db')->value,3,'');?>
 secs ]<?php if (isset($_smarty_tpl->getVariable('server_load',null,true,false)->value)&&$_smarty_tpl->getVariable('server_load')->value!='?'){?> &nbsp; [ Server load: <?php echo $_smarty_tpl->getVariable('server_load')->value;?>
 ]<?php }?></small><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>$_smarty_tpl->getVariable('tpl_module_name')->value,'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-loadstats.tpl -->