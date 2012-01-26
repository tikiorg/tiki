<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:26:59
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\translated-lang.tpl" */ ?>
<?php /*%%SmartyHeaderCode:107874f1e08e3b04752-86654372%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '771883fe83a739c4e43c55859eaf32b530cec3cb' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\translated-lang.tpl',
      1 => 1316111696,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '107874f1e08e3b04752-86654372',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include 'lib/smarty_tiki\modifier.escape.php';
if (!is_callable('smarty_block_tr')) include 'lib/smarty_tiki\block.tr.php';
if (!is_callable('smarty_block_jq')) include 'lib/smarty_tiki\block.jq.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\translated-lang.tpl -->
	<?php if (isset($_smarty_tpl->getVariable('trads',null,true,false)->value)&&(count($_smarty_tpl->getVariable('trads')->value)>1||$_smarty_tpl->getVariable('trads')->value[0]['langName'])){?>
		<?php if ($_smarty_tpl->getVariable('td')->value=='y'){?><td style="vertical-align:top;text-align: left; width:42px;"><?php }?>
		<?php if (isset($_smarty_tpl->getVariable('verbose',null,true,false)->value)&&$_smarty_tpl->getVariable('verbose')->value=='y'){?>The main text of this page is available in the following languages:<?php }?>
			<?php if (isset($_smarty_tpl->getVariable('type',null,true,false)->value)&&$_smarty_tpl->getVariable('type')->value=='article'){?>
				<div style="float: left;">
					<form action="tiki-read_article.php" method="get">
						<div>
							<input type="hidden" name="type" value="article"/>
							<input type="hidden" name="switchlang" value="y" />
							<select name="articleId" onchange="quick_switch_language( this, 'article', '<?php ob_start();?><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('articleId')->value,"quotes");?>
<?php $_tmp1=ob_get_clean();?><?php echo $_tmp1;?>
' )"> 
								<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('trads')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total']);
?>
								<option value="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('trads')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['objId']);?>
"><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('trads')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['langName']);?>
</option>
								<?php endfor; endif; ?>
								<?php if ($_smarty_tpl->getVariable('tiki_p_edit_article')->value=='y'){?>
									<option value="-">---</option>
									<option value="_manage_">Manage translations</option>
								<?php }?>
							</select>
						</div>
					</form>
				</div>
			<?php }else{ ?>
				<?php if ($_smarty_tpl->getVariable('tiki_p_edit')->value!='y'&&$_smarty_tpl->getVariable('translationsCount')->value=='1'){?>
				  <span title="No translations available"><?php $_smarty_tpl->smarty->_tag_stack[] = array('tr', array()); $_block_repeat=true; smarty_block_tr(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('trads')->value[0]['langName']);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tr(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
				<?php }else{ ?>
			 
				<form action="tiki-index.php" method="get">
					<div>
						<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_machine_translation']=='y'){?>
						<input type="hidden" name="machine_translate_to_lang" value="" />
						<?php }?>
						<select name="page" onchange="quick_switch_language( this, 'wiki page', '<?php ob_start();?><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,"quotes");?>
<?php $_tmp2=ob_get_clean();?><?php echo $_tmp2;?>
' )"> 
							<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_machine_translation']=='y'){?>
							<option value="Human Translations" disabled="disabled" style="color:black;font-weight:bold">Human Translations</option>
							<?php }?>
							<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('trads')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total']);
?>
							<option value="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('trads')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['objName']);?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('tr', array()); $_block_repeat=true; smarty_block_tr(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('trads')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['langName']);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tr(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
							<?php endfor; endif; ?>
							<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_machine_translation']=='y'){?>
							<option value="Machine Translations" disabled="disabled" style="color:black;font-weight:bold">Machine Translations</option>
							<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('langsCandidatesForMachineTranslation')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total']);
?>
							<option value="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('langsCandidatesForMachineTranslation')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['lang']);?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('tr', array()); $_block_repeat=true; smarty_block_tr(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('langsCandidatesForMachineTranslation')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['langName']);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tr(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 *</option>
							<?php endfor; endif; ?>
							<?php }?>
							<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_multilingual_one_page']=='y'&&$_smarty_tpl->getVariable('translationsCount')->value>1){?>
							<option value="-">---</option>
							<option value="_all_"<?php if (basename($_SERVER['PHP_SELF'])=='tiki-all_languages.php'){?> selected="selected"<?php }?>>All</option>
							<?php }?>
							<?php if ($_smarty_tpl->getVariable('tiki_p_edit')->value=='y'){?>
							<option value="-">---</option>
							<option value="_translate_">Translate</option>
							<option value="_manage_">Manage translations</option>
							<?php }?>
						</select>
						<input type="hidden" name="switchlang" value="y" />
						<input type="hidden" name="no_bl" value="y" /> 
					</div>
				</form>
			  <?php }?>
			<?php }?>

			<?php $_smarty_tpl->smarty->_tag_stack[] = array('jq', array('notonready'=>true)); $_block_repeat=true; smarty_block_jq(array('notonready'=>true), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

			function quick_switch_language( element, object_type, object_to_translate )
			{
				var index = element.selectedIndex;
				var option = element.options[index];

				if( option.value == "-" ) {
					return;
				} else if( option.value == "_translate_" ) {
					element.form.action = "tiki-edit_translation.php";
					element.value = object_to_translate;					
					element.form.submit();
				} else if( option.value == '_manage_' ) {
					$(document).serviceDialog({
						title: $(option).text(),
						data: {
							controller: 'translation',
							action: 'manage',
							type: object_type,
							source: object_to_translate
						}
					});
					return;
				} else if( option.value == "_all_" ) {
					element.form.action = "tiki-all_languages.php";
					element.value = object_to_translate;
					element.form.submit();
				} else if (option.text.charAt(option.text.length - 1) == "*") {
					element.form.machine_translate_to_lang.value = element.form.page.options[element.form.page.selectedIndex].value;
					element.value = object_to_translate;
					element.form.submit();				 		
				} else
					element.form.submit();
			}
				<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_jq(array('notonready'=>true), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

		<?php if ($_smarty_tpl->getVariable('td')->value=='y'){?></td><?php }?>
	<?php }?>
<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\translated-lang.tpl -->