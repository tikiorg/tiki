<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:09
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-freetags_morelikethis.tpl" */ ?>
<?php /*%%SmartyHeaderCode:303864f1e08ed3b02a3-72720587%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '90229f63b2f30e2482cdcfed37beed91f3ce2fc5' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\modules/mod-freetags_morelikethis.tpl',
      1 => 1302743604,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '303864f1e08ed3b02a3-72720587',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_block_tikimodule')) include 'lib/smarty_tiki\block.tikimodule.php';
if (!is_callable('smarty_modifier_escape')) include 'lib/smarty_tiki\modifier.escape.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-freetags_morelikethis.tpl -->

<?php if (isset($_smarty_tpl->getVariable('modMoreLikeThis',null,true,false)->value)&&count($_smarty_tpl->getVariable('modMoreLikeThis')->value)>0){?>
  <?php $_smarty_tpl->smarty->_tag_stack[] = array('tikimodule', array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>"freetags_morelikethis",'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle'])); $_block_repeat=true; smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>"freetags_morelikethis",'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

  <?php if (($_smarty_tpl->getVariable('nonums')->value=='y')){?><ul><?php }else{ ?><ol><?php }?>
  <?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('modMoreLikeThis')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
?>
     <li><a class="linkmodule" href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['row']->value['href']);?>
"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['row']->value['name']);?>
</a></li>
  <?php }} ?>
  <?php if (($_smarty_tpl->getVariable('nonums')->value=='y')){?></ul><?php }else{ ?></ol><?php }?>
  <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>"freetags_morelikethis",'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php }?>
<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-freetags_morelikethis.tpl -->