<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:00
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\show_copyright.tpl" */ ?>
<?php /*%%SmartyHeaderCode:98864f1e08e4a87cf6-57365024%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a61f4df9f2fe0d41a725495fb3027f127556a323' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\show_copyright.tpl',
      1 => 1266516656,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '98864f1e08e4a87cf6-57365024',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_sefurl')) include 'lib/smarty_tiki\modifier.sefurl.php';
if (!is_callable('smarty_modifier_escape')) include 'lib/smarty_tiki\modifier.escape.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\show_copyright.tpl --><?php if ($_smarty_tpl->getVariable('prefs')->value['wiki_feature_copyrights']=='y'&&$_smarty_tpl->getVariable('prefs')->value['wikiLicensePage']){?>
	<?php if ($_smarty_tpl->getVariable('prefs')->value['wikiLicensePage']==$_smarty_tpl->getVariable('page')->value){?>
		<?php if ($_smarty_tpl->getVariable('tiki_p_edit_copyrights')->value=='y'){?>
			<br />
			To edit the copyright notices <a href="copyrights.php?page=<?php echo $_smarty_tpl->getVariable('copyrightpage')->value;?>
">Click Here</a>.
		<?php }?>
	<?php }else{ ?>
		<br />
		The content on this page is licensed under the terms of the <a href="<?php echo smarty_modifier_sefurl($_smarty_tpl->getVariable('prefs')->value['wikiLicensePage'],'wiki','with_next');?>
copyrightpage=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,"url");?>
"><?php echo $_smarty_tpl->getVariable('prefs')->value['wikiLicensePage'];?>
</a>.
	<?php }?>
<?php }?>
<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\show_copyright.tpl -->