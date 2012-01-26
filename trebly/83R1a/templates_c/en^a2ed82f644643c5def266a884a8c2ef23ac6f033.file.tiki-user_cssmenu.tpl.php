<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:06
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\tiki-user_cssmenu.tpl" */ ?>
<?php /*%%SmartyHeaderCode:198364f1e08ea56e033-17750612%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a2ed82f644643c5def266a884a8c2ef23ac6f033' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\tiki-user_cssmenu.tpl',
      1 => 1314087316,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '198364f1e08ea56e033-17750612',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_block_repeat')) include 'lib/smarty_tiki\block.repeat.php';
if (!is_callable('smarty_modifier_escape')) include 'lib/smarty_tiki\modifier.escape.php';
if (!is_callable('smarty_function_icon')) include 'lib/smarty_tiki\function.icon.php';
if (!is_callable('smarty_block_tr')) include 'lib/smarty_tiki\block.tr.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\tiki-user_cssmenu.tpl -->
<?php if (count($_smarty_tpl->getVariable('menu_channels')->value)>0){?>
	<?php $_smarty_tpl->tpl_vars['opensec'] = new Smarty_variable('0', null, null);?>
	<?php $_smarty_tpl->tpl_vars['sep'] = new Smarty_variable('', null, null);?>
	<ul id="cssmenu<?php echo $_smarty_tpl->getVariable('idCssmenu')->value;?>
" class="cssmenu<?php if ($_smarty_tpl->getVariable('menu_type')->value){?>_<?php echo $_smarty_tpl->getVariable('menu_type')->value;?>
<?php }?> menu<?php echo $_smarty_tpl->getVariable('menu_info')->value['menuId'];?>
"><?php  $_smarty_tpl->tpl_vars['chdata'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['pos'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('menu_channels')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['chdata']->key => $_smarty_tpl->tpl_vars['chdata']->value){
 $_smarty_tpl->tpl_vars['pos']->value = $_smarty_tpl->tpl_vars['chdata']->key;
?><?php if ($_smarty_tpl->tpl_vars['chdata']->value['type']!='o'&&$_smarty_tpl->tpl_vars['chdata']->value['type']!='-'){?><?php if ($_smarty_tpl->getVariable('opensec')->value>0){?><?php if ($_smarty_tpl->tpl_vars['chdata']->value['type']=='s'||$_smarty_tpl->tpl_vars['chdata']->value['type']=='r'){?><?php $_smarty_tpl->tpl_vars['sectionType'] = new Smarty_variable(0, null, null);?><?php }else{ ?><?php $_smarty_tpl->tpl_vars['sectionType'] = new Smarty_variable($_smarty_tpl->tpl_vars['chdata']->value['type'], null, null);?><?php }?><?php if ($_smarty_tpl->getVariable('opensec')->value>$_smarty_tpl->getVariable('sectionType')->value){?><?php $_smarty_tpl->tpl_vars['nb_opensec'] = new Smarty_variable($_smarty_tpl->getVariable('opensec')->value-$_smarty_tpl->getVariable('sectionType')->value, null, null);?><?php $_smarty_tpl->smarty->_tag_stack[] = array('repeat', array('count'=>$_smarty_tpl->getVariable('nb_opensec')->value)); $_block_repeat=true; smarty_block_repeat(array('count'=>$_smarty_tpl->getVariable('nb_opensec')->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
</ul></li><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_repeat(array('count'=>$_smarty_tpl->getVariable('nb_opensec')->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->tpl_vars['opensec'] = new Smarty_variable($_smarty_tpl->getVariable('sectionType')->value, null, null);?><?php }?><?php }?><li class="option<?php echo $_smarty_tpl->tpl_vars['chdata']->value['optionId'];?>
 menuSection menuSection<?php echo $_smarty_tpl->getVariable('opensec')->value;?>
 menuLevel<?php echo $_smarty_tpl->getVariable('opensec')->value;?>
<?php if (isset($_smarty_tpl->tpl_vars['chdata']->value['selected'])&&$_smarty_tpl->tpl_vars['chdata']->value['selected']){?> selected<?php }?><?php if (isset($_smarty_tpl->tpl_vars['chdata']->value['selectedAscendant'])&&$_smarty_tpl->tpl_vars['chdata']->value['selectedAscendant']){?> selectedAscendant<?php }?>"><a<?php if (!empty($_smarty_tpl->tpl_vars['chdata']->value['url'])){?> href="<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_sefurl']=='y'&&$_smarty_tpl->tpl_vars['chdata']->value['sefurl']){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['chdata']->value['sefurl']);?>
<?php }else{ ?><?php if ($_smarty_tpl->getVariable('prefs')->value['menus_item_names_raw']=='n'){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['chdata']->value['url']);?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['chdata']->value['url'];?>
<?php }?><?php }?>"<?php }?>><?php if ($_smarty_tpl->getVariable('menu_type')->value=='vert'&&$_smarty_tpl->getVariable('prefs')->value['menus_items_icons']=='y'&&$_smarty_tpl->getVariable('menu_info')->value['use_items_icons']=='y'&&$_smarty_tpl->getVariable('opensec')->value==0){?><?php echo smarty_function_icon(array('_id'=>$_smarty_tpl->tpl_vars['chdata']->value['icon'],'alt'=>'','_defaultdir'=>$_smarty_tpl->getVariable('prefs')->value['menus_items_icons_path']),$_smarty_tpl);?>
<?php }elseif(isset($_smarty_tpl->getVariable('icon',null,true,false)->value)&&$_smarty_tpl->getVariable('icon')->value){?><?php echo smarty_function_icon(array('_id'=>'folder','align'=>"left"),$_smarty_tpl);?>
<?php }?><span class="menuText"><?php if ($_smarty_tpl->getVariable('translate')->value=='n'){?><?php if ($_smarty_tpl->getVariable('prefs')->value['menus_item_names_raw']=='n'){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['chdata']->value['name']);?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['chdata']->value['name'];?>
<?php }?><?php }else{ ?><?php $_smarty_tpl->smarty->_tag_stack[] = array('tr', array()); $_block_repeat=true; smarty_block_tr(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php if ($_smarty_tpl->getVariable('prefs')->value['menus_item_names_raw']=='n'){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['chdata']->value['name']);?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['chdata']->value['name'];?>
<?php }?><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tr(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }?></span><?php if ($_smarty_tpl->getVariable('link_on_section')->value!='n'){?></a><?php }?><?php $_smarty_tpl->tpl_vars['opensec'] = new Smarty_variable($_smarty_tpl->getVariable('opensec')->value+1, null, null);?><ul><?php }elseif($_smarty_tpl->tpl_vars['chdata']->value['type']=='o'){?><li class="option<?php echo $_smarty_tpl->tpl_vars['chdata']->value['optionId'];?>
 menuOption menuLevel<?php echo $_smarty_tpl->getVariable('opensec')->value;?>
<?php if (isset($_smarty_tpl->tpl_vars['chdata']->value['selected'])&&$_smarty_tpl->tpl_vars['chdata']->value['selected']){?> selected<?php }?><?php if (isset($_smarty_tpl->tpl_vars['chdata']->value['selectedAscendant'])&&$_smarty_tpl->tpl_vars['chdata']->value['selectedAscendant']){?> selectedAscendant<?php }?>"><a href="<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_sefurl']=='y'&&$_smarty_tpl->tpl_vars['chdata']->value['sefurl']){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['chdata']->value['sefurl']);?>
<?php }else{ ?><?php if ($_smarty_tpl->getVariable('prefs')->value['menus_item_names_raw']=='n'){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['chdata']->value['url']);?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['chdata']->value['url'];?>
<?php }?><?php }?>"><?php if ($_smarty_tpl->getVariable('menu_type')->value=='vert'&&$_smarty_tpl->getVariable('prefs')->value['menus_items_icons']=='y'&&$_smarty_tpl->getVariable('menu_info')->value['use_items_icons']=='y'&&$_smarty_tpl->getVariable('opensec')->value==0){?><?php echo smarty_function_icon(array('_id'=>$_smarty_tpl->tpl_vars['chdata']->value['icon'],'alt'=>'','_defaultdir'=>$_smarty_tpl->getVariable('prefs')->value['menus_items_icons_path']),$_smarty_tpl);?>
<?php }?><span class="menuText"><?php if ($_smarty_tpl->getVariable('translate')->value=='n'){?><?php if ($_smarty_tpl->getVariable('prefs')->value['menus_item_names_raw']=='n'){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['chdata']->value['name']);?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['chdata']->value['name'];?>
<?php }?><?php }else{ ?><?php $_smarty_tpl->smarty->_tag_stack[] = array('tr', array()); $_block_repeat=true; smarty_block_tr(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php if ($_smarty_tpl->getVariable('prefs')->value['menus_item_names_raw']=='n'){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['chdata']->value['name']);?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['chdata']->value['name'];?>
<?php }?><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tr(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }?></span></a></li><?php if ($_smarty_tpl->getVariable('sep')->value=='line'){?><?php $_smarty_tpl->tpl_vars['sep'] = new Smarty_variable('', null, null);?><?php }?><?php }elseif($_smarty_tpl->tpl_vars['chdata']->value['type']=='-'){?><?php if ($_smarty_tpl->getVariable('opensec')->value>0){?></ul></li><?php $_smarty_tpl->tpl_vars['opensec'] = new Smarty_variable($_smarty_tpl->getVariable('opensec')->value-1, null, null);?><?php }?><?php $_smarty_tpl->tpl_vars['sep'] = new Smarty_variable("line", null, null);?><?php }?><?php }} ?><?php if ($_smarty_tpl->getVariable('opensec')->value>0){?><?php $_smarty_tpl->smarty->_tag_stack[] = array('repeat', array('count'=>$_smarty_tpl->getVariable('opensec')->value)); $_block_repeat=true; smarty_block_repeat(array('count'=>$_smarty_tpl->getVariable('opensec')->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
</ul></li><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_repeat(array('count'=>$_smarty_tpl->getVariable('opensec')->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->tpl_vars['opensec'] = new Smarty_variable(0, null, null);?><?php }?></ul>
<?php }?>
<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\tiki-user_cssmenu.tpl -->