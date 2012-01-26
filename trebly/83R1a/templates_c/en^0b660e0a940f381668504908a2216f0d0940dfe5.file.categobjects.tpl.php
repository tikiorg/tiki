<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:26:57
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\categobjects.tpl" */ ?>
<?php /*%%SmartyHeaderCode:195914f1e08e133d0a2-23397383%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0b660e0a940f381668504908a2216f0d0940dfe5' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\categobjects.tpl',
      1 => 1309591806,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '195914f1e08e133d0a2-23397383',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_tr_if')) include 'lib/smarty_tiki\modifier.tr_if.php';
if (!is_callable('smarty_modifier_escape')) include 'lib/smarty_tiki\modifier.escape.php';
if (!is_callable('smarty_modifier_sefurl')) include 'lib/smarty_tiki\modifier.sefurl.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\categobjects.tpl -->

<div class="catblock clearfix"> 
	<?php if (!isset($_smarty_tpl->getVariable('params',null,true,false)->value['showTitle'])||$_smarty_tpl->getVariable('params')->value['showTitle']=='y'){?>
		<div class="cattitle">
			<span class="label">Category: </span>
			<?php  $_smarty_tpl->tpl_vars['cattitle'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('titles')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['cattitle']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['cattitle']->iteration=0;
if ($_smarty_tpl->tpl_vars['cattitle']->total > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['cattitle']->key => $_smarty_tpl->tpl_vars['cattitle']->value){
 $_smarty_tpl->tpl_vars['id']->value = $_smarty_tpl->tpl_vars['cattitle']->key;
 $_smarty_tpl->tpl_vars['cattitle']->iteration++;
 $_smarty_tpl->tpl_vars['cattitle']->last = $_smarty_tpl->tpl_vars['cattitle']->iteration === $_smarty_tpl->tpl_vars['cattitle']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['for']['last'] = $_smarty_tpl->tpl_vars['cattitle']->last;
?>
				<?php if ($_smarty_tpl->getVariable('params')->value['categoryshowlink']!='n'){?><a href="tiki-browse_categories.php?parentId=<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
"><?php }?>
				<?php echo smarty_modifier_escape(smarty_modifier_tr_if($_smarty_tpl->tpl_vars['cattitle']->value));?>

				<?php if ($_smarty_tpl->getVariable('params')->value['categoryshowlink']!='n'){?></a><?php }?>
				<?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['for']['last']){?> &amp; <?php }?>
			<?php }} ?>
		</div>
	<?php }?>
  <div class="catlists">
    <ul class="<?php if ($_smarty_tpl->getVariable('params')->value['showtype']!='n'){?>catfeatures<?php }elseif($_smarty_tpl->getVariable('params')->value['one']=='y'){?>catitemsone<?php }else{ ?>catitems<?php }?>">
   <?php  $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['t'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('listcat')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['i']->key => $_smarty_tpl->tpl_vars['i']->value){
 $_smarty_tpl->tpl_vars['t']->value = $_smarty_tpl->tpl_vars['i']->key;
?>
   	<?php if ($_smarty_tpl->getVariable('params')->value['showtype']!='n'){?>
      <li>
      <?php echo $_smarty_tpl->tpl_vars['t']->value;?>
:
      <ul class="<?php if ($_smarty_tpl->getVariable('params')->value['one']=='y'){?>catitemsone<?php }else{ ?>catitems<?php }?>">
	<?php }?>
        <?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['o']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['o']['name'] = 'o';
$_smarty_tpl->tpl_vars['smarty']->value['section']['o']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['i']->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['o']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['o']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['o']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['o']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['o']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['o']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['o']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['o']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['o']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['o']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['o']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['o']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['o']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['o']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['o']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['o']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['o']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['o']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['o']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['o']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['o']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['o']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['o']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['o']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['o']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['o']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['o']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['o']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['o']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['o']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['o']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['o']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['o']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['o']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['o']['total']);
?>
        <li>
			<?php if ($_smarty_tpl->getVariable('params')->value['showlinks']!='n'){?>
				<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_sefurl']=='y'){?>
					<a href="<?php echo smarty_modifier_sefurl($_smarty_tpl->tpl_vars['i']->value[$_smarty_tpl->getVariable('smarty')->value['section']['o']['index']]['itemId'],$_smarty_tpl->tpl_vars['i']->value[$_smarty_tpl->getVariable('smarty')->value['section']['o']['index']]['type']);?>
" class="link">
				<?php }else{ ?>
					<a href="<?php echo $_smarty_tpl->tpl_vars['i']->value[$_smarty_tpl->getVariable('smarty')->value['section']['o']['index']]['href'];?>
" class="link">
				<?php }?>
			<?php }?>
			<?php if ($_smarty_tpl->getVariable('params')->value['showname']!='n'||empty($_smarty_tpl->tpl_vars['i']->value[$_smarty_tpl->getVariable('smarty',null,true,false)->value['section']['o']['index']]['description'])){?>
				<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['i']->value[$_smarty_tpl->getVariable('smarty')->value['section']['o']['index']]['name']);?>

				<?php if ($_smarty_tpl->getVariable('params')->value['showlinks']!='n'){?></a><?php }?>
				<?php if ($_smarty_tpl->getVariable('params')->value['showdescription']=='y'){?> <span class='description'><?php }?>
			<?php }?>
			<?php if ($_smarty_tpl->getVariable('params')->value['showdescription']=='y'){?>
				<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['i']->value[$_smarty_tpl->getVariable('smarty')->value['section']['o']['index']]['description']);?>

				<?php if ($_smarty_tpl->getVariable('params')->value['showname']!='n'||empty($_smarty_tpl->tpl_vars['i']->value[$_smarty_tpl->getVariable('smarty',null,true,false)->value['section']['o']['index']]['description'])){?>
					</span>
				<?php }else{ ?>
					<?php if ($_smarty_tpl->getVariable('params')->value['showlinks']!='n'){?></a><?php }?>
				<?php }?>
			<?php }?>
          </li>
        <?php endfor; endif; ?>
	<?php if ($_smarty_tpl->getVariable('params')->value['showtype']!='n'){?>
        </ul>
      </li>
	<?php }?>
    <?php }} ?>
  </ul>
  </div>
</div>
<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\categobjects.tpl -->