<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:05
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\remarksbox.tpl" */ ?>
<?php /*%%SmartyHeaderCode:251074f1e08e921fbf2-54518788%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b9553b5b49fcd3b53446cc96f31fabefcde7dc8b' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\remarksbox.tpl',
      1 => 1305771262,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '251074f1e08e921fbf2-54518788',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_icon')) include 'lib/smarty_tiki\function.icon.php';
if (!is_callable('smarty_block_tr')) include 'lib/smarty_tiki\block.tr.php';
if (!is_callable('smarty_modifier_escape')) include 'lib/smarty_tiki\modifier.escape.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\remarksbox.tpl --><div class="clearfix rbox <?php echo $_smarty_tpl->getVariable('remarksbox_type')->value;?>
"><?php if ($_smarty_tpl->getVariable('remarksbox_close')->value=='y'&&$_smarty_tpl->getVariable('remarksbox_type')->value!='errors'&&$_smarty_tpl->getVariable('remarksbox_type')->value!='confirm'){?><?php echo smarty_function_icon(array('_id'=>'close','class'=>'rbox-close','onclick'=>'$(this).parent().fadeOut();'),$_smarty_tpl);?>
<?php }?><?php if ($_smarty_tpl->getVariable('remarksbox_title')->value!=''){?><div class="rbox-title"><?php if ($_smarty_tpl->getVariable('remarksbox_icon')->value!='none'){?><?php ob_start(); ?><?php $_smarty_tpl->smarty->_tag_stack[] = array('tr', array()); $_block_repeat=true; smarty_block_tr(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->getVariable('remarksbox_type')->value;?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tr(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php  Smarty::$_smarty_vars['capture']['alt']=ob_get_clean();?><?php echo smarty_function_icon(array('_id'=>$_smarty_tpl->getVariable('remarksbox_icon')->value,'alt'=>Smarty::$_smarty_vars['capture']['alt']),$_smarty_tpl);?>
<?php }?><span><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('remarksbox_title')->value);?>
</span></div><?php }?><div class="rbox-data<?php echo $_smarty_tpl->getVariable('remarksbox_highlight')->value;?>
"<?php if (!empty($_smarty_tpl->getVariable('remarksbox_width',null,true,false)->value)){?> style="width:<?php echo $_smarty_tpl->getVariable('remarksbox_width')->value;?>
"<?php }?>><?php echo $_smarty_tpl->getVariable('remarksbox_content')->value;?>
</div></div><!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\remarksbox.tpl -->