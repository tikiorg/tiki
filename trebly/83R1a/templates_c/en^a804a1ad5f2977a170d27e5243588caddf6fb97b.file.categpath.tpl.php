<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:26:56
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\categpath.tpl" */ ?>
<?php /*%%SmartyHeaderCode:27214f1e08e0e27ff9-28858491%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a804a1ad5f2977a170d27e5243588caddf6fb97b' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\categpath.tpl',
      1 => 1316046950,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '27214f1e08e0e27ff9-28858491',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_sefurl')) include 'lib/smarty_tiki\modifier.sefurl.php';
if (!is_callable('smarty_modifier_tr_if')) include 'lib/smarty_tiki\modifier.tr_if.php';
if (!is_callable('smarty_modifier_escape')) include 'lib/smarty_tiki\modifier.escape.php';
if (!is_callable('smarty_modifier_replace')) include 'G:\W3ld1\Teawik\teawik-ld1-83x\83R1\lib\smarty\libs\plugins\modifier.replace.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\categpath.tpl --><span class="categpath">
<?php  $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('catp')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['i']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['i']->iteration=0;
if ($_smarty_tpl->tpl_vars['i']->total > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['i']->key => $_smarty_tpl->tpl_vars['i']->value){
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['i']->key;
 $_smarty_tpl->tpl_vars['i']->iteration++;
 $_smarty_tpl->tpl_vars['i']->last = $_smarty_tpl->tpl_vars['i']->iteration === $_smarty_tpl->tpl_vars['i']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['u']['last'] = $_smarty_tpl->tpl_vars['i']->last;
?>
<a class="categpath" href="<?php echo smarty_modifier_sefurl($_smarty_tpl->tpl_vars['k']->value,'category','','','y',$_smarty_tpl->tpl_vars['i']->value);?>
" title="Browse Category"><?php echo smarty_modifier_replace(smarty_modifier_escape(smarty_modifier_tr_if($_smarty_tpl->tpl_vars['i']->value)),' ','&nbsp;');?>
</a><?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['u']['last']){?>&nbsp;<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['site_crumb_seper'],"html");?>
&nbsp;<?php }?>
<?php }} ?>
</span>
<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\categpath.tpl -->