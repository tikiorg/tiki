<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:12
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\footer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:237664f1e08f02aea11-33799690%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dd1a99e9f08c231ecad7c4b7efaf4e793274c38d' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\footer.tpl',
      1 => 1302743604,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '237664f1e08f02aea11-33799690',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_button')) include 'lib/smarty_tiki\function.button.php';
if (!is_callable('smarty_function_listfilter')) include 'lib/smarty_tiki\function.listfilter.php';
if (!is_callable('smarty_function_debugger')) include 'lib/smarty_tiki\function.debugger.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\footer.tpl -->

<?php if ((!isset($_smarty_tpl->getVariable('display',null,true,false)->value)||$_smarty_tpl->getVariable('display')->value=='')){?>
	<?php if (count($_smarty_tpl->getVariable('phpErrors')->value)){?>
		<?php echo smarty_function_button(array('_ajax'=>"n",'_id'=>"show-errors-button",'_onclick'=>"flip('errors');return false;",'_text'=>"Show php error messages"),$_smarty_tpl);?>

		<div id="errors" class="rbox warning" style="display:<?php if ((isset($_SESSION['tiki_cookie_jar']['show_errors'])&&$_SESSION['tiki_cookie_jar']['show_errors']=='y')||$_smarty_tpl->getVariable('prefs')->value['javascript_enabled']!='y'){?>block<?php }else{ ?>none<?php }?>;">
			&nbsp;<?php echo smarty_function_listfilter(array('selectors'=>'#errors>div'),$_smarty_tpl);?>

			<?php  $_smarty_tpl->tpl_vars['err'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('phpErrors')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['err']->key => $_smarty_tpl->tpl_vars['err']->value){
?>
				<?php echo $_smarty_tpl->tpl_vars['err']->value;?>

			<?php }} ?>
		</div>
	<?php }?>

	<?php if ($_smarty_tpl->getVariable('tiki_p_admin')->value=='y'&&$_smarty_tpl->getVariable('prefs')->value['feature_debug_console']=='y'){?>
		
		<?php echo smarty_function_debugger(array(),$_smarty_tpl);?>

	<?php }?>

<?php }?>
<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\footer.tpl -->