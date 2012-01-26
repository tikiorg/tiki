<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:09
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-poweredby.tpl" */ ?>
<?php /*%%SmartyHeaderCode:229124f1e08ed73e3e2-81933838%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '507915370314953e1ead1b0788e067ac992196e3' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\modules/mod-poweredby.tpl',
      1 => 1314275014,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '229124f1e08ed73e3e2-81933838',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_block_tikimodule')) include 'lib/smarty_tiki\block.tikimodule.php';
if (!is_callable('smarty_modifier_date_format')) include 'G:\W3ld1\Teawik\teawik-ld1-83x\83R1\lib\smarty\libs\plugins\modifier.date_format.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-poweredby.tpl -->
<?php $_smarty_tpl->smarty->_tag_stack[] = array('tikimodule', array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>$_smarty_tpl->getVariable('tpl_module_name')->value,'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle'])); $_block_repeat=true; smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>$_smarty_tpl->getVariable('tpl_module_name')->value,'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<div class="power"><?php if (!isset($_smarty_tpl->getVariable('module_params',null,true,false)->value['tiki'])||$_smarty_tpl->getVariable('module_params')->value['tiki']!='n'){?>Powered by <a href="http://tiki.org" title="&#169; 2002&#8211;<?php echo smarty_modifier_date_format(time(),"%Y");?>
 The Tiki Community">Tiki Wiki CMS Groupware</a><?php if (!isset($_smarty_tpl->getVariable('module_params',null,true,false)->value['version'])||$_smarty_tpl->getVariable('module_params')->value['version']!='n'){?> v<?php echo $_smarty_tpl->getVariable('tiki_version')->value;?>
 <?php if ($_smarty_tpl->getVariable('tiki_uses_svn')->value=='y'){?> (SVN)<?php }?> &quot;<?php echo $_smarty_tpl->getVariable('tiki_star')->value;?>
&quot; <?php }?><?php }?><?php if (!isset($_smarty_tpl->getVariable('module_params',null,true,false)->value['credits'])||$_smarty_tpl->getVariable('module_params')->value['credits']!='n'){?><span id="credits">&nbsp;| <?php $_template = new Smarty_Internal_Template('credits.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?></span><?php }?></div><?php if (!isset($_smarty_tpl->getVariable('module_params',null,true,false)->value['icons'])||$_smarty_tpl->getVariable('module_params')->value['icons']!='n'){?><div class="power_icons"><a href="http://tiki.org/" title="Tiki"><img alt="Powered by Tiki" src="img/tiki/tikibutton2.png" /></a><a href="http://php.net/" title="PHP"><img alt="Powered by PHP" src="img/php.png" /></a><a href="http://smarty.net/" title="Smarty"><img alt="Powered by Smarty" src="img/smarty.gif"  /></a><a href="http://www.w3.org/Style/CSS/" title="CSS"><img alt="Made with CSS" src="img/css1.png" /></a></div><?php }?><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>$_smarty_tpl->getVariable('tpl_module_name')->value,'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-poweredby.tpl -->