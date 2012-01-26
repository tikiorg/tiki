<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:02
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-logo.tpl" */ ?>
<?php /*%%SmartyHeaderCode:310524f1e08e603db02-52727663%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '628c60a665850a3d12302b674f43d36db0182cf2' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\modules/mod-logo.tpl',
      1 => 1313089890,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '310524f1e08e603db02-52727663',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_block_tikimodule')) include 'lib/smarty_tiki\block.tikimodule.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-logo.tpl -->

<?php $_smarty_tpl->smarty->_tag_stack[] = array('tikimodule', array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>"logo",'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle'])); $_block_repeat=true; smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>"logo",'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

   <div id="sitelogo"<?php if ($_smarty_tpl->getVariable('prefs')->value['sitelogo_bgcolor']!=''){?> style="background-color: <?php echo $_smarty_tpl->getVariable('prefs')->value['sitelogo_bgcolor'];?>
;" <?php }?> class="floatleft">
      <?php if ($_smarty_tpl->getVariable('prefs')->value['sitelogo_src']){?><a href="./" title="<?php echo $_smarty_tpl->getVariable('prefs')->value['sitelogo_title'];?>
"<?php if (isset($_smarty_tpl->getVariable('prefs',null,true,false)->value['mobile_mode'])&&$_smarty_tpl->getVariable('prefs')->value['mobile_mode']=="y"){?> rel="external"<?php }?>><img src="<?php echo $_smarty_tpl->getVariable('prefs')->value['sitelogo_src'];?>
" alt="<?php echo $_smarty_tpl->getVariable('prefs')->value['sitelogo_alt'];?>
" style="border: none" /></a>
      <?php }?>
   </div>
   <div id="sitetitles" class="floatleft">
      <div id="sitetitle">
         <a href="./"<?php if (isset($_smarty_tpl->getVariable('prefs',null,true,false)->value['mobile_mode'])&&$_smarty_tpl->getVariable('prefs')->value['mobile_mode']=="y"){?> rel="external"<?php }?>><?php if (!empty($_smarty_tpl->getVariable('prefs',null,true,false)->value['sitetitle'])){?><?php echo $_smarty_tpl->getVariable('prefs')->value['sitetitle'];?>
<?php }?></a>
      </div>
      <div id="sitesubtitle"><?php if (!empty($_smarty_tpl->getVariable('prefs',null,true,false)->value['sitesubtitle'])){?><?php echo $_smarty_tpl->getVariable('prefs')->value['sitesubtitle'];?>
<?php }?></div>
   </div>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>"logo",'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-logo.tpl -->