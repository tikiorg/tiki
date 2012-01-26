<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:02
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\module.tpl" */ ?>
<?php /*%%SmartyHeaderCode:321494f1e08e62263f2-72171895%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7fa131726890c2006d56154e3f3d177999b7bbcf' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\module.tpl',
      1 => 1313059894,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '321494f1e08e62263f2-72171895',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_replace')) include 'G:\W3ld1\Teawik\teawik-ld1-83x\83R1\lib\smarty\libs\plugins\modifier.replace.php';
if (!is_callable('smarty_modifier_escape')) include 'lib/smarty_tiki\modifier.escape.php';
if (!is_callable('smarty_function_icon')) include 'lib/smarty_tiki\function.icon.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\module.tpl -->

<?php if (!isset($_smarty_tpl->getVariable('module_position',null,true,false)->value)){?><?php $_smarty_tpl->tpl_vars['module_position'] = new Smarty_variable(' ', null, null);?><?php }?>
<?php if (!isset($_smarty_tpl->getVariable('module_ord',null,true,false)->value)){?><?php $_smarty_tpl->tpl_vars['module_ord'] = new Smarty_variable(' ', null, null);?><?php }?>
<?php ob_start(); ?><?php echo smarty_modifier_escape(((smarty_modifier_replace($_smarty_tpl->getVariable('module_name')->value,"+","_")).($_smarty_tpl->getVariable('module_position')->value)).($_smarty_tpl->getVariable('module_ord')->value));?>
<?php  Smarty::$_smarty_vars['capture']['name']=ob_get_clean();?>
<?php if ($_smarty_tpl->getVariable('module_nobox')->value!='y'){?>
	<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_layoutshadows']=='y'){?>
		<div class="box-shadow"><?php echo $_smarty_tpl->getVariable('prefs')->value['box_shadow_start'];?>

	<?php }?>
	<?php if (!isset($_smarty_tpl->getVariable('moduleId',null,true,false)->value)){?><?php $_smarty_tpl->tpl_vars['moduleId'] = new Smarty_variable(' ', null, null);?><?php }?>
	<div id="module_<?php echo $_smarty_tpl->getVariable('moduleId')->value;?>
" class="box box-<?php echo $_smarty_tpl->getVariable('module_name')->value;?>
<?php if ($_smarty_tpl->getVariable('module_type')->value=='cssmenu'){?> cssmenubox<?php }?> module"<?php if (!empty($_smarty_tpl->getVariable('tpl_module_style',null,true,false)->value)){?> style="<?php echo $_smarty_tpl->getVariable('tpl_module_style')->value;?>
"<?php }?>>
	<?php if ($_smarty_tpl->getVariable('module_decorations')->value!='n'){?>
		<h3 class="box-title clearfix" <?php if (!empty($_smarty_tpl->getVariable('module_params',null,true,false)->value['bgcolor'])){?> style="background-color:<?php echo $_smarty_tpl->getVariable('module_params')->value['bgcolor'];?>
;"<?php }?>>
		<?php if (isset($_smarty_tpl->getVariable('user',null,true,false)->value)&&$_smarty_tpl->getVariable('user')->value&&$_smarty_tpl->getVariable('prefs')->value['user_assigned_modules']=='y'&&$_smarty_tpl->getVariable('prefs')->value['feature_modulecontrols']=='y'){?>
			<span class="modcontrols">
			<a title="Move module up" href="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('current_location')->value);?>
<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('mpchar')->value);?>
mc_up=<?php echo $_smarty_tpl->getVariable('module_name')->value;?>
">
				<?php echo smarty_function_icon(array('_id'=>"resultset_up",'alt'=>"[Up]"),$_smarty_tpl);?>

			</a>
			<a title="Move module down" href="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('current_location')->value);?>
<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('mpchar')->value);?>
mc_down=<?php echo $_smarty_tpl->getVariable('module_name')->value;?>
">
				<?php echo smarty_function_icon(array('_id'=>"resultset_down",'alt'=>"[Down]"),$_smarty_tpl);?>

			</a>
			<a title="Move module to opposite side" href="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('current_location')->value);?>
<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('mpchar')->value);?>
mc_move=<?php echo $_smarty_tpl->getVariable('module_name')->value;?>
">
				<?php echo smarty_function_icon(array('_id'=>"arrow_right-left",'alt'=>"[opp side]"),$_smarty_tpl);?>

			</a>
			<a title="Unassign this module" href="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('current_location')->value);?>
<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('mpchar')->value);?>
mc_unassign=<?php echo $_smarty_tpl->getVariable('module_name')->value;?>
" onclick='return confirmTheLink(this,"Are you sure you want to unassign this module?")'>
				<?php echo smarty_function_icon(array('_id'=>"cross",'alt'=>"[Remove]"),$_smarty_tpl);?>

			 </a>
			</span>
		<?php }?>
		<?php if ($_smarty_tpl->getVariable('module_notitle')->value!='y'){?>
			<span class="moduletitle"><?php echo $_smarty_tpl->getVariable('module_title')->value;?>
</span>
		<?php }?>
		<?php if ($_smarty_tpl->getVariable('module_flip')->value=='y'&&$_smarty_tpl->getVariable('prefs')->value['javascript_enabled']!='n'){?>
			<span class="moduleflip" id="moduleflip-<?php echo Smarty::$_smarty_vars['capture']['name'];?>
">
				<a title="Toggle module contents" class="flipmodtitle" href="javascript:icntoggle('mod-<?php echo Smarty::$_smarty_vars['capture']['name'];?>
','module.png');">
					<?php echo smarty_function_icon(array('id'=>("icnmod-").(Smarty::$_smarty_vars['capture']['name']),'class'=>"flipmodimage",'_id'=>"module",'alt'=>"[toggle]"),$_smarty_tpl);?>

				</a>
			</span>
			<?php if ($_smarty_tpl->getVariable('prefs')->value['menus_items_icons']=='y'){?>
				<span class="moduleflip moduleflip-vert" id="moduleflip-vert-<?php echo Smarty::$_smarty_vars['capture']['name'];?>
">
					<a title="Toggle module contents" class="flipmodtitle" href="javascript:flip_class('main','minimize-modules-left','maximize-modules');icntoggle('modv-<?php echo Smarty::$_smarty_vars['capture']['name'];?>
','vmodule.png');">
						<?php ob_start(); ?>
							icnmodv-<?php echo Smarty::$_smarty_vars['capture']['name'];?>

						<?php  Smarty::$_smarty_vars['capture']['name']=ob_get_clean();?>
						<?php echo smarty_function_icon(array('name'=>("icnmod-").(Smarty::$_smarty_vars['capture']['name']),'class'=>"flipmodimage",'_id'=>"trans",'alt'=>"[Toggle Vertically]",'_defaultdir'=>"pics"),$_smarty_tpl);?>

					</a>
				</span>
			<?php }?>
		<?php }?>
		<!--[if IE]><br class="clear" style="height: 1px !important" /><![endif]--></h3>
	<?php }elseif($_smarty_tpl->getVariable('module_notitle')->value!='y'){?>
		<?php if ($_smarty_tpl->getVariable('module_flip')->value=='y'&&$_smarty_tpl->getVariable('prefs')->value['javascript_enabled']!='n'){?>
			<h3 class="box-title" ondblclick="javascript:icntoggle('mod-<?php echo Smarty::$_smarty_vars['capture']['name'];?>
','module.png');"<?php if (!empty($_smarty_tpl->getVariable('module_params',null,true,false)->value['color'])){?> style="color:<?php echo $_smarty_tpl->getVariable('module_params')->value['color'];?>
;"<?php }?>>
		<?php }else{ ?>
			<h3 class="box-title"<?php if (!empty($_smarty_tpl->getVariable('module_params',null,true,false)->value['color'])){?> style="color:<?php echo $_smarty_tpl->getVariable('module_params')->value['color'];?>
;"<?php }?>>
		<?php }?>
		<?php echo $_smarty_tpl->getVariable('module_title')->value;?>

		<?php if ($_smarty_tpl->getVariable('module_flip')->value=='y'&&$_smarty_tpl->getVariable('prefs')->value['javascript_enabled']!='n'){?>
			<span id="moduleflip-<?php echo Smarty::$_smarty_vars['capture']['name'];?>
">
				<a title="Toggle module contents" class="flipmodtitle" href="javascript:icntoggle('mod-<?php echo Smarty::$_smarty_vars['capture']['name'];?>
','module.png');">
					<?php $_smarty_tpl->tpl_vars["name"] = new Smarty_variable(("icnmod-").(Smarty::$_smarty_vars['capture']['name']), null, null);?>
					<?php ob_start(); ?>
						icnmod-<?php echo Smarty::$_smarty_vars['capture']['name'];?>

					<?php  Smarty::$_smarty_vars['capture']['name']=ob_get_clean();?>
					<?php echo smarty_function_icon(array('name'=>("icnmod-").(Smarty::$_smarty_vars['capture']['name']),'class'=>"flipmodimage",'_id'=>"module",'alt'=>"[Hide]"),$_smarty_tpl);?>

				</a>
			</span>
		<?php }?>
		<!--[if IE]><br class="clear" style="height: 1px !important" /><![endif]--></h3>
	<?php }?>
		<div id="mod-<?php echo Smarty::$_smarty_vars['capture']['name'];?>
" style="display: <?php if (!isset($_smarty_tpl->getVariable('module_display',null,true,false)->value)||$_smarty_tpl->getVariable('module_display')->value){?>block<?php }else{ ?>none<?php }?>;<?php echo $_smarty_tpl->getVariable('module_params')->value['style'];?>
" class="clearfix box-data<?php if (!empty($_smarty_tpl->getVariable('module_params',null,true,false)->value['class'])){?> <?php echo $_smarty_tpl->getVariable('module_params')->value['class'];?>
<?php }?>">
<?php }else{ ?>
		<div id="module_<?php echo $_smarty_tpl->getVariable('moduleId')->value;?>
" style="<?php echo $_smarty_tpl->getVariable('module_params')->value['style'];?>
<?php echo $_smarty_tpl->getVariable('tpl_module_style')->value;?>
" class="module<?php if (!empty($_smarty_tpl->getVariable('module_params',null,true,false)->value['class'])){?> <?php echo $_smarty_tpl->getVariable('module_params')->value['class'];?>
<?php }?> box-<?php echo $_smarty_tpl->getVariable('module_name')->value;?>
 clearfix">
			<div id="mod-<?php echo Smarty::$_smarty_vars['capture']['name'];?>
" class="clearfix">
<?php }?>
<?php echo $_smarty_tpl->getVariable('module_content')->value;?>

<?php echo $_smarty_tpl->getVariable('module_error')->value;?>

<?php if ($_smarty_tpl->getVariable('module_nobox')->value!='y'){?>
		</div>
		<div class="box-footer">

		</div>
	</div>
	<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_layoutshadows']=='y'){?><?php echo $_smarty_tpl->getVariable('prefs')->value['box_shadow_end'];?>
</div><?php }?>
<?php }else{ ?>
		</div>
	</div>
<?php }?>
<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\module.tpl -->