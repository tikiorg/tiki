<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:05
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-calendar_new.tpl" */ ?>
<?php /*%%SmartyHeaderCode:154404f1e08e94c9930-05415998%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c89174dffbfb98e239584ed2846977728d929f78' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\modules/mod-calendar_new.tpl',
      1 => 1310994358,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '154404f1e08e94c9930-05415998',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_block_tikimodule')) include 'lib/smarty_tiki\block.tikimodule.php';
if (!is_callable('smarty_function_cycle')) include 'G:\W3ld1\Teawik\teawik-ld1-83x\83R1\lib\smarty\libs\plugins\function.cycle.php';
if (!is_callable('smarty_modifier_tiki_date_format')) include 'lib/smarty_tiki\modifier.tiki_date_format.php';
if (!is_callable('smarty_modifier_escape')) include 'lib/smarty_tiki\modifier.escape.php';
if (!is_callable('smarty_function_popup')) include 'lib/smarty_tiki\function.popup.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-calendar_new.tpl -->
<?php if ($_smarty_tpl->getVariable('show_calendar_module')->value=='y'){?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('tikimodule', array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>$_smarty_tpl->getVariable('name')->value,'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle'])); $_block_repeat=true; smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>$_smarty_tpl->getVariable('name')->value,'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

<?php if ($_smarty_tpl->getVariable('viewlist')->value=='list'){?>
	<?php $_template = new Smarty_Internal_Template('tiki-calendar_listmode.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
<?php }else{ ?>
	<?php $_template = new Smarty_Internal_Template('tiki-calendar_nav.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
$_template->assign('ajax','n');$_template->assign('module','y'); echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>

	<table cellpadding="0" cellspacing="0" border="0" class="caltable" style="text-align:center;">
		<tr>
			<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['dn']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['name'] = 'dn';
$_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('daysnames_abr')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['dn']['total']);
?>
				<th class="days" width="14%"><?php echo ucfirst($_smarty_tpl->getVariable('daysnames_abr')->value[$_smarty_tpl->getVariable('smarty')->value['section']['dn']['index']]);?>
</th>
			<?php endfor; endif; ?>
		</tr>
		<?php echo smarty_function_cycle(array('values'=>"odd,even",'print'=>false),$_smarty_tpl);?>

		<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['w']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['w']['name'] = 'w';
$_smarty_tpl->tpl_vars['smarty']->value['section']['w']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('cell')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['w']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['w']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['w']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['w']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['w']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['w']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['w']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['w']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['w']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['w']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['w']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['w']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['w']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['w']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['w']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['w']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['w']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['w']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['w']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['w']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['w']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['w']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['w']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['w']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['w']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['w']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['w']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['w']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['w']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['w']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['w']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['w']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['w']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['w']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['w']['total']);
?>
			<tr>
				<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['d']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['d']['name'] = 'd';
$_smarty_tpl->tpl_vars['smarty']->value['section']['d']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('daysnames_abr')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['d']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['d']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['d']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['d']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['d']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['d']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['d']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['d']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['d']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['d']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['d']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['d']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['d']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['d']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['d']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['d']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['d']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['d']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['d']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['d']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['d']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['d']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['d']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['d']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['d']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['d']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['d']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['d']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['d']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['d']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['d']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['d']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['d']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['d']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['d']['total']);
?>
					<?php if (empty($_smarty_tpl->getVariable('cell',null,true,false)->value[$_smarty_tpl->getVariable('smarty',null,true,false)->value['section']['w']['index']][$_smarty_tpl->getVariable('smarty',null,true,false)->value['section']['d']['index']]['date'])){?><?php $_smarty_tpl->tpl_vars['date'] = new Smarty_variable($_smarty_tpl->getVariable('cell')->value[$_smarty_tpl->getVariable('smarty')->value['section']['w']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['d']['index']]['day'], null, null);?><?php }else{ ?><?php $_smarty_tpl->tpl_vars['date'] = new Smarty_variable($_smarty_tpl->getVariable('cell')->value[$_smarty_tpl->getVariable('smarty')->value['section']['w']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['d']['index']]['date'], null, null);?><?php }?>
					<?php if (in_array($_smarty_tpl->getVariable('viewmode')->value,array('bimester','trimester','quarter','semester','year'))){?>
						<?php if (in_array($_smarty_tpl->getVariable('prefs')->value['display_field_order'],array('DMY','DYM','YDM'))){?>
							<?php $_smarty_tpl->tpl_vars['day_cursor'] = new Smarty_variable(smarty_modifier_tiki_date_format($_smarty_tpl->getVariable('date')->value,"%d-%m"), null, null);?>
						<?php }else{ ?>
							<?php $_smarty_tpl->tpl_vars['day_cursor'] = new Smarty_variable(smarty_modifier_tiki_date_format($_smarty_tpl->getVariable('date')->value,"%m-%d"), null, null);?>
						<?php }?>
					<?php }else{ ?>
						<?php $_smarty_tpl->tpl_vars['day_cursor'] = new Smarty_variable(smarty_modifier_tiki_date_format($_smarty_tpl->getVariable('date')->value,"%d"), null, null);?>
					<?php }?>
					<?php $_smarty_tpl->tpl_vars['month_cursor'] = new Smarty_variable(smarty_modifier_tiki_date_format($_smarty_tpl->getVariable('date')->value,"%m"), null, null);?>
					<?php $_smarty_tpl->tpl_vars['day_today'] = new Smarty_variable(smarty_modifier_tiki_date_format(time(),"%d"), null, null);?>
					<?php $_smarty_tpl->tpl_vars['month_today'] = new Smarty_variable(smarty_modifier_tiki_date_format(time(),"%m"), null, null);?>

					<?php if ($_smarty_tpl->getVariable('cell')->value[$_smarty_tpl->getVariable('smarty')->value['section']['w']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['d']['index']]['focus']){?>
						<?php echo smarty_function_cycle(array('values'=>"odd,even",'print'=>false),$_smarty_tpl);?>

					<?php }else{ ?>
						<?php echo smarty_function_cycle(array('values'=>"notoddoreven",'print'=>false),$_smarty_tpl);?>

					<?php }?>
					<td class="<?php echo smarty_function_cycle(array('advance'=>false),$_smarty_tpl);?>
<?php if ($_smarty_tpl->getVariable('date')->value==$_smarty_tpl->getVariable('today')->value){?> highlight<?php }?><?php if (isset($_smarty_tpl->getVariable('cell',null,true,false)->value[$_smarty_tpl->getVariable('smarty',null,true,false)->value['section']['w']['index']][$_smarty_tpl->getVariable('smarty',null,true,false)->value['section']['d']['index']]['items'][0])&&($_smarty_tpl->getVariable('cell')->value[$_smarty_tpl->getVariable('smarty')->value['section']['w']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['d']['index']]['items'][0]['modifiable']=="y"||$_smarty_tpl->getVariable('cell')->value[$_smarty_tpl->getVariable('smarty')->value['section']['w']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['d']['index']]['items'][0]['visible']=='y')){?> focus<?php }?>" width="14%" style="text-align:center; font-size:0.8em;">

						<?php if (isset($_smarty_tpl->getVariable('cell',null,true,false)->value[$_smarty_tpl->getVariable('smarty',null,true,false)->value['section']['w']['index']][$_smarty_tpl->getVariable('smarty',null,true,false)->value['section']['d']['index']]['over'])){?>
							<?php $_smarty_tpl->tpl_vars['over'] = new Smarty_variable($_smarty_tpl->getVariable('cell')->value[$_smarty_tpl->getVariable('smarty')->value['section']['w']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['d']['index']]['over'], null, null);?>
						<?php }elseif(isset($_smarty_tpl->getVariable('cell',null,true,false)->value[$_smarty_tpl->getVariable('smarty',null,true,false)->value['section']['w']['index']][$_smarty_tpl->getVariable('smarty',null,true,false)->value['section']['d']['index']]['items'][0])){?>
							<?php $_smarty_tpl->tpl_vars['over'] = new Smarty_variable($_smarty_tpl->getVariable('cell')->value[$_smarty_tpl->getVariable('smarty')->value['section']['w']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['d']['index']]['items'][0]['over'], null, null);?><?php }else{ ?><?php $_smarty_tpl->tpl_vars['over'] = new Smarty_variable('', null, null);?>
						<?php }?>
						<?php if (isset($_smarty_tpl->getVariable('cell',null,true,false)->value[$_smarty_tpl->getVariable('smarty',null,true,false)->value['section']['w']['index']][$_smarty_tpl->getVariable('smarty',null,true,false)->value['section']['d']['index']]['items'][0])&&($_smarty_tpl->getVariable('cell')->value[$_smarty_tpl->getVariable('smarty')->value['section']['w']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['d']['index']]['items'][0]['modifiable']=="y"||$_smarty_tpl->getVariable('cell')->value[$_smarty_tpl->getVariable('smarty')->value['section']['w']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['d']['index']]['items'][0]['visible']=='y')){?>
							<?php if (empty($_smarty_tpl->getVariable('calendar_popup',null,true,false)->value)||$_smarty_tpl->getVariable('calendar_popup')->value=="y"){?>
								<a style="text-decoration: underline; font-weight: bold" href="<?php echo $_smarty_tpl->getVariable('myurl')->value;?>
?todate=<?php echo $_smarty_tpl->getVariable('date')->value;?>
&amp;viewmode=<?php echo $_smarty_tpl->getVariable('viewmodelink')->value;?>
" 
								<?php if ($_smarty_tpl->getVariable('sticky_popup')->value=='y'||($_smarty_tpl->getVariable('prefs')->value['calendar_sticky_popup']=="y"&&$_smarty_tpl->getVariable('cell')->value[$_smarty_tpl->getVariable('smarty')->value['section']['w']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['d']['index']]['items'][0]['calitemId'])){?>
									<?php echo smarty_function_popup(array('sticky'=>true,'fullhtml'=>"1",'text'=>smarty_modifier_escape(smarty_modifier_escape($_smarty_tpl->getVariable('over')->value,"javascript"),"html")),$_smarty_tpl);?>

								<?php }else{ ?>
									<?php echo smarty_function_popup(array('fullhtml'=>"1",'text'=>smarty_modifier_escape(smarty_modifier_escape($_smarty_tpl->getVariable('over')->value,"javascript"),"html")),$_smarty_tpl);?>

								<?php }?>
								>
								<?php echo $_smarty_tpl->getVariable('day_cursor')->value;?>

								</a>
							<?php }else{ ?>
								<?php echo $_smarty_tpl->getVariable('day_cursor')->value;?>

								<?php echo $_smarty_tpl->getVariable('over')->value;?>

							<?php }?>
						<?php }elseif($_smarty_tpl->getVariable('linkall')->value=='y'){?>
							<a style="text-decoration: underline; font-weight: bold" href="<?php echo $_smarty_tpl->getVariable('myurl')->value;?>
?todate=<?php echo $_smarty_tpl->getVariable('cell')->value[$_smarty_tpl->getVariable('smarty')->value['section']['w']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['d']['index']]['day'];?>
&amp;viewmode=<?php echo $_smarty_tpl->getVariable('viewmodelink')->value;?>
"><?php echo $_smarty_tpl->getVariable('day_cursor')->value;?>
</a>
						<?php }else{ ?>
							<?php echo $_smarty_tpl->getVariable('day_cursor')->value;?>

						<?php }?>

					</td>
				<?php endfor; endif; ?>
			</tr>
		<?php endfor; endif; ?>
	</table>
<?php }?>
<?php if ($_smarty_tpl->getVariable('tiki_p_add_events')->value=='y'&&(empty($_smarty_tpl->getVariable('module_params',null,true,false)->value['showaction'])||$_smarty_tpl->getVariable('module_params')->value['showaction']!='n')){?>
	<p><a href="tiki-calendar_edit_item.php"><img src="pics/icons/add.png" alt="" /> Add event</a></p>
<?php }?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>$_smarty_tpl->getVariable('name')->value,'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php }?>
<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-calendar_new.tpl -->