<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:00
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\wiki_authors.tpl" */ ?>
<?php /*%%SmartyHeaderCode:278654f1e08e45a62b3-16106838%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2e8a41f43b3c0a41de739b193064bcc92b2fb1d1' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\wiki_authors.tpl',
      1 => 1313059894,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '278654f1e08e45a62b3-16106838',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_userlink')) include 'lib/smarty_tiki\modifier.userlink.php';
if (!is_callable('smarty_modifier_tiki_long_datetime')) include 'lib/smarty_tiki\modifier.tiki_long_datetime.php';
if (!is_callable('smarty_block_tr')) include 'lib/smarty_tiki\block.tr.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\wiki_authors.tpl --><?php if ($_smarty_tpl->getVariable('wiki_authors_style')->value=='business'){?>
	Last edited by <?php echo smarty_modifier_userlink($_smarty_tpl->getVariable('lastUser')->value);?>

	<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['author']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['author']['name'] = 'author';
$_smarty_tpl->tpl_vars['smarty']->value['section']['author']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('contributors')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['author']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['author']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['author']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['author']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['author']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['author']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['author']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['author']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['author']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['author']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['author']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['author']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['author']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['author']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['author']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['total']);
?>
		<?php if ($_smarty_tpl->getVariable('smarty')->value['section']['author']['first']){?>
			, based on work by
		<?php }else{ ?>
			<?php if (!$_smarty_tpl->getVariable('smarty')->value['section']['author']['last']){?>
				,
			<?php }else{ ?>
				and
			<?php }?>
		<?php }?>
		<?php echo smarty_modifier_userlink($_smarty_tpl->getVariable('contributors')->value[$_smarty_tpl->getVariable('smarty')->value['section']['author']['index']]);?>

	<?php endfor; endif; ?>.
	<br />
	Page last modified on <?php echo smarty_modifier_tiki_long_datetime($_smarty_tpl->getVariable('lastModif')->value);?>
. <?php if ($_smarty_tpl->getVariable('prefs')->value['wiki_show_version']=='y'){?>(Version <?php echo $_smarty_tpl->getVariable('lastVersion')->value;?>
)<?php }?>
<?php }elseif($_smarty_tpl->getVariable('wiki_authors_style')->value=='collaborative'){?>
	Contributors to this page: <?php echo smarty_modifier_userlink($_smarty_tpl->getVariable('lastUser')->value);?>

	<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['author']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['author']['name'] = 'author';
$_smarty_tpl->tpl_vars['smarty']->value['section']['author']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('contributors')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['author']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['author']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['author']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['author']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['author']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['author']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['author']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['author']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['author']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['author']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['author']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['author']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['author']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['author']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['author']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['author']['total']);
?>
		<?php if (!$_smarty_tpl->getVariable('smarty')->value['section']['author']['last']){?>
			,
		<?php }else{ ?> 
			and
		<?php }?>
		<?php echo smarty_modifier_userlink($_smarty_tpl->getVariable('contributors')->value[$_smarty_tpl->getVariable('smarty')->value['section']['author']['index']]);?>

	<?php endfor; endif; ?>.
	<br />
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('tr', array('_0'=>smarty_modifier_tiki_long_datetime($_smarty_tpl->getVariable('lastModif')->value),'_1'=>smarty_modifier_userlink($_smarty_tpl->getVariable('lastUser')->value))); $_block_repeat=true; smarty_block_tr(array('_0'=>smarty_modifier_tiki_long_datetime($_smarty_tpl->getVariable('lastModif')->value),'_1'=>smarty_modifier_userlink($_smarty_tpl->getVariable('lastUser')->value)), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Page last modified on %0 by %1<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tr(array('_0'=>smarty_modifier_tiki_long_datetime($_smarty_tpl->getVariable('lastModif')->value),'_1'=>smarty_modifier_userlink($_smarty_tpl->getVariable('lastUser')->value)), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
. 
	<?php if ($_smarty_tpl->getVariable('prefs')->value['wiki_show_version']=='y'){?>
		(Version <?php echo $_smarty_tpl->getVariable('lastVersion')->value;?>
)
	<?php }?>

<?php }elseif($_smarty_tpl->getVariable('wiki_authors_style')->value=='lastmodif'){?>
	Page last modified on <?php echo smarty_modifier_tiki_long_datetime($_smarty_tpl->getVariable('lastModif')->value);?>

<?php }else{ ?>
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('tr', array('_0'=>smarty_modifier_userlink($_smarty_tpl->getVariable('creator')->value))); $_block_repeat=true; smarty_block_tr(array('_0'=>smarty_modifier_userlink($_smarty_tpl->getVariable('creator')->value)), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Created by %0<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tr(array('_0'=>smarty_modifier_userlink($_smarty_tpl->getVariable('creator')->value)), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('tr', array('_0'=>smarty_modifier_tiki_long_datetime($_smarty_tpl->getVariable('lastModif')->value),'_1'=>smarty_modifier_userlink($_smarty_tpl->getVariable('lastUser')->value))); $_block_repeat=true; smarty_block_tr(array('_0'=>smarty_modifier_tiki_long_datetime($_smarty_tpl->getVariable('lastModif')->value),'_1'=>smarty_modifier_userlink($_smarty_tpl->getVariable('lastUser')->value)), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Last Modification: %0 by %1<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tr(array('_0'=>smarty_modifier_tiki_long_datetime($_smarty_tpl->getVariable('lastModif')->value),'_1'=>smarty_modifier_userlink($_smarty_tpl->getVariable('lastUser')->value)), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
. 
	<?php if ($_smarty_tpl->getVariable('prefs')->value['wiki_show_version']=='y'){?>
		(Version <?php echo $_smarty_tpl->getVariable('lastVersion')->value;?>
)
	<?php }?>
<?php }?>
<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\wiki_authors.tpl -->