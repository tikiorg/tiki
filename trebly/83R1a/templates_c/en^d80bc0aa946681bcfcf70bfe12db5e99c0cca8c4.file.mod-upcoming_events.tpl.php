<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:05
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-upcoming_events.tpl" */ ?>
<?php /*%%SmartyHeaderCode:295184f1e08e9cd5d70-82187920%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd80bc0aa946681bcfcf70bfe12db5e99c0cca8c4' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\modules/mod-upcoming_events.tpl',
      1 => 1313059894,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '295184f1e08e9cd5d70-82187920',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_block_tikimodule')) include 'lib/smarty_tiki\block.tikimodule.php';
if (!is_callable('smarty_modifier_tiki_date_format')) include 'lib/smarty_tiki\modifier.tiki_date_format.php';
if (!is_callable('smarty_modifier_isodate')) include 'lib/smarty_tiki\modifier.isodate.php';
if (!is_callable('smarty_modifier_tiki_short_date')) include 'lib/smarty_tiki\modifier.tiki_short_date.php';
if (!is_callable('smarty_modifier_tiki_short_datetime')) include 'lib/smarty_tiki\modifier.tiki_short_datetime.php';
if (!is_callable('smarty_modifier_tiki_short_time')) include 'lib/smarty_tiki\modifier.tiki_short_time.php';
if (!is_callable('smarty_modifier_username')) include 'lib/smarty_tiki\modifier.username.php';
if (!is_callable('smarty_modifier_truncate')) include 'lib/smarty_tiki\modifier.truncate.php';
if (!is_callable('smarty_modifier_escape')) include 'lib/smarty_tiki\modifier.escape.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-upcoming_events.tpl --><?php $_smarty_tpl->smarty->_tag_stack[] = array('tikimodule', array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>"upcoming_events",'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle'])); $_block_repeat=true; smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>"upcoming_events",'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

<?php if (count($_smarty_tpl->getVariable('modUpcomingEvents')->value)){?>
	<?php if (isset($_smarty_tpl->getVariable('module_params',null,true,false)->value['date_format'])){?>
		<?php $_smarty_tpl->tpl_vars['date_format'] = new Smarty_variable($_smarty_tpl->getVariable('module_params')->value['date_format'], null, null);?>
	<?php }else{ ?>
		<?php $_smarty_tpl->tpl_vars['date_format'] = new Smarty_variable((($_smarty_tpl->getVariable('prefs')->value['short_date_format']).(' ')).($_smarty_tpl->getVariable('prefs')->value['short_time_format']), null, null);?>
	<?php }?>
	<table border="0" cellpadding="<?php if (isset($_smarty_tpl->getVariable('module_params',null,true,false)->value['cellpadding'])){?><?php echo $_smarty_tpl->getVariable('module_params')->value['cellpadding'];?>
<?php }else{ ?>0<?php }?>" cellspacing="<?php if (isset($_smarty_tpl->getVariable('module_params',null,true,false)->value['cellspacing'])){?><?php echo $_smarty_tpl->getVariable('module_params')->value['cellspacing'];?>
<?php }else{ ?>0<?php }?>">
		<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['ix']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['name'] = 'ix';
$_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('modUpcomingEvents')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['total']);
?>
			<?php $_smarty_tpl->tpl_vars['date_value'] = new Smarty_variable(smarty_modifier_tiki_date_format($_smarty_tpl->getVariable('modUpcomingEvents')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['start'],$_smarty_tpl->getVariable('date_format')->value), null, null);?>
			<?php $_smarty_tpl->tpl_vars['calendarId'] = new Smarty_variable($_smarty_tpl->getVariable('modUpcomingEvents')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['calendarId'], null, null);?>
			<?php if (!$_smarty_tpl->getVariable('smarty')->value['section']['ix']['first']){?></td></tr><?php }?><tr>
			<?php if ($_smarty_tpl->getVariable('nonums')->value!='y'){?>
				<td class="module" valign="top"><?php echo $_smarty_tpl->getVariable('smarty')->value['section']['ix']['index_next'];?>
)&nbsp;</td>
			<?php }?>
			<td class="module vevent"<?php if ($_smarty_tpl->getVariable('showColor')->value=='y'&&$_smarty_tpl->getVariable('infocals')->value[$_smarty_tpl->getVariable('calendarId')->value]['custombgcolor']!=''){?> style="background-color:#<?php echo $_smarty_tpl->getVariable('infocals')->value[$_smarty_tpl->getVariable('calendarId')->value]['custombgcolor'];?>
"<?php }?>>
				<?php if ($_smarty_tpl->getVariable('modUpcomingEvents')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['allday']){?>
					<abbr class="dtstart" title="<?php echo smarty_modifier_isodate($_smarty_tpl->getVariable('modUpcomingEvents')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['start']);?>
"><?php echo smarty_modifier_tiki_short_date($_smarty_tpl->getVariable('modUpcomingEvents')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['start']);?>
</abbr>
				<?php }else{ ?>
					<abbr class="dtstart" title="<?php echo smarty_modifier_isodate($_smarty_tpl->getVariable('modUpcomingEvents')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['start']);?>
"><?php echo smarty_modifier_tiki_date_format($_smarty_tpl->getVariable('modUpcomingEvents')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['start'],$_smarty_tpl->getVariable('date_format')->value);?>
</abbr>	
					<?php if ($_smarty_tpl->getVariable('showEnd')->value=='y'){?>
						-
						<abbr class="dtend" title="<?php echo smarty_modifier_isodate($_smarty_tpl->getVariable('modUpcomingEvents')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['end']);?>
"><?php if ($_smarty_tpl->getVariable('module_params')->value['date_format']){?><?php echo smarty_modifier_tiki_date_format($_smarty_tpl->getVariable('modUpcomingEvents')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['end'],$_smarty_tpl->getVariable('date_format')->value);?>
<?php }elseif(smarty_modifier_tiki_short_date($_smarty_tpl->getVariable('modUpcomingEvents')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['start'])!=smarty_modifier_tiki_short_date($_smarty_tpl->getVariable('modUpcomingEvents')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['end'])){?><?php echo smarty_modifier_tiki_short_datetime($_smarty_tpl->getVariable('modUpcomingEvents')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['end']);?>
<?php }else{ ?><?php echo smarty_modifier_tiki_short_time($_smarty_tpl->getVariable('modUpcomingEvents')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['end']);?>
<?php }?></abbr>
					<?php }?>
				<?php }?>
				<br />
				<a class="linkmodule summary" href="tiki-calendar_edit_item.php?viewcalitemId=<?php echo $_smarty_tpl->getVariable('modUpcomingEvents')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['calitemId'];?>
" title="<?php if ($_smarty_tpl->getVariable('tooltip_infos')->value!='n'){?><?php echo smarty_modifier_tiki_short_datetime($_smarty_tpl->getVariable('modUpcomingEvents')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['lastModif']);?>
, by <?php if ($_smarty_tpl->getVariable('modUpcomingEvents')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['user']!=''){?><?php echo smarty_modifier_username($_smarty_tpl->getVariable('modUpcomingEvents')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['user']);?>
<?php }else{ ?>Anonymous<?php }?><?php }else{ ?>click to view<?php }?>"<?php if ($_smarty_tpl->getVariable('modUpcomingEvents')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['status']=='2'){?> style="text-decoration: line-through;"<?php }?>>
					<?php if ($_smarty_tpl->getVariable('maxlen')->value>0){?>
						<?php echo smarty_modifier_escape(smarty_modifier_truncate($_smarty_tpl->getVariable('modUpcomingEvents')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['name'],$_smarty_tpl->getVariable('maxlen')->value,"...",true));?>

					<?php }else{ ?>
						<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('modUpcomingEvents')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['name']);?>

					<?php }?>
				</a>
				<?php if ($_smarty_tpl->getVariable('showDescription')->value=='y'){?>
					<div class="description"><?php echo $_smarty_tpl->getVariable('modUpcomingEvents')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['parsed'];?>
</div>
				<?php }?>
		<?php if ($_smarty_tpl->getVariable('smarty')->value['section']['ix']['last']){?>
			</td>
			</tr>
		<?php }?>
	<?php endfor; endif; ?>
	</table>
<?php }else{ ?>
      <em>No records to display</em>
<?php }?>

<?php if ($_smarty_tpl->getVariable('tiki_p_add_events')->value=='y'&&(empty($_smarty_tpl->getVariable('module_params',null,true,false)->value['showaction'])||$_smarty_tpl->getVariable('module_params')->value['showaction']!='n')){?>
	<p><a href="tiki-calendar_edit_item.php"><img src="pics/icons/add.png" alt="" /> Add Event</a></p>
<?php }?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>"upcoming_events",'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-upcoming_events.tpl -->