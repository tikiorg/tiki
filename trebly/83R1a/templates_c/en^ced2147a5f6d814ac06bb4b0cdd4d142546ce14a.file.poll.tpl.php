<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:00
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\poll.tpl" */ ?>
<?php /*%%SmartyHeaderCode:192324f1e08e4094c69-39714756%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ced2147a5f6d814ac06bb4b0cdd4d142546ce14a' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\poll.tpl',
      1 => 1320864702,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '192324f1e08e4094c69-39714756',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_button')) include 'lib/smarty_tiki\function.button.php';
if (!is_callable('smarty_modifier_escape')) include 'lib/smarty_tiki\modifier.escape.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\poll.tpl --><?php if (count($_smarty_tpl->getVariable('ratings')->value)&&$_smarty_tpl->getVariable('tiki_p_wiki_view_ratings')->value=='y'){?>
	<div style="display:inline;float:right;padding: 1px 3px; border:1px solid #666666; -moz-border-radius : 10px;font-size:.8em;">
		<div id="pollopen">
			<?php echo smarty_function_button(array('href'=>"#",'_onclick'=>"show('pollzone');hide('polledit');hide('pollopen');return false;",'class'=>"link",'_text'=>"Rating"),$_smarty_tpl);?>

		</div>
		<?php if ($_smarty_tpl->getVariable('tiki_p_wiki_vote_ratings')->value=='y'){?>
			<div id="polledit">
				<div class="pollnav">
					<?php echo smarty_function_button(array('href'=>"#",'_onclick'=>"hide('pollzone');hide('polledit');show('pollopen');return false;",'_text'=>"[-]"),$_smarty_tpl);?>

					<?php echo smarty_function_button(array('href'=>"#",'_onclick'=>"show('pollzone');hide('polledit');hide('pollopen');return false;",'class'=>"link",'_text'=>"View"),$_smarty_tpl);?>

				</div>
				
				<?php  $_smarty_tpl->tpl_vars['r'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('ratings')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['r']->key => $_smarty_tpl->tpl_vars['r']->value){
?>
					<?php if ($_smarty_tpl->tpl_vars['r']->value['title']){?>
						<div><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['r']->value['title']);?>
</div>
					<?php }?>
					<form method="post" action="tiki-index.php">
						<?php if ($_smarty_tpl->getVariable('page')->value){?>
							<input type="hidden" name="wikipoll" value="1" />
							<input type="hidden" name="page" value="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value);?>
" />
						<?php }?>
						<input type="hidden" name="polls_pollId" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['r']->value['info']['pollId']);?>
" />
						<table>
							<?php  $_smarty_tpl->tpl_vars['option'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['r']->value['options']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['option']->key => $_smarty_tpl->tpl_vars['option']->value){
?>
								<tr>
									<td valign="top" <?php if ($_smarty_tpl->tpl_vars['r']->value['vote']==$_smarty_tpl->tpl_vars['option']->value['optionId']){?>class="highlight"<?php }?>>
										<input type="radio" name="polls_optionId" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['option']->value['optionId']);?>
" id="poll<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['r']->value['info']['pollId']);?>
<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['option']->value['optionId']);?>
" <?php if ($_smarty_tpl->tpl_vars['r']->value['vote']==$_smarty_tpl->tpl_vars['option']->value['optionId']){?> checked="checked"<?php }?> />
									</td>
									<td valign="top" <?php if ($_smarty_tpl->tpl_vars['r']->value['vote']==$_smarty_tpl->tpl_vars['option']->value['optionId']){?>class="highlight"<?php }?>> 
										<label for="poll<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['r']->value['info']['pollId']);?>
<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['option']->value['optionId']);?>
"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['option']->value['title']);?>
</label>
									</td>
									<td valign="top" <?php if ($_smarty_tpl->tpl_vars['r']->value['vote']==$_smarty_tpl->tpl_vars['option']->value['optionId']){?>class="highlight"<?php }?>>
										(<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['option']->value['votes']);?>
)
									</td>
								</tr>
							<?php }} ?>
						</table>
						<div align="center">
							<input type="submit" name="pollVote" value="vote" style="border:1px solid #666666;font-size:.8em;"/>
						</div>
					</form>
				<?php }} ?>
			</div>
			<div id="pollzone">
				<div class="pollnav">
					<?php echo smarty_function_button(array('href'=>"#",'_onclick'=>"hide('pollzone');hide('polledit');show('pollopen');return false;",'_text'=>"[-]"),$_smarty_tpl);?>

					<?php echo smarty_function_button(array('href'=>"#",'_onclick'=>"hide('pollzone');show('polledit');hide('pollopen');return false;",'_text'=>"Vote"),$_smarty_tpl);?>

				</div>
				<?php  $_smarty_tpl->tpl_vars['r'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('ratings')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['r']->key => $_smarty_tpl->tpl_vars['r']->value){
?>
					<div>
						<?php if ($_smarty_tpl->tpl_vars['r']->value['title']){?>
							<div><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['r']->value['title']);?>
</div>
						<?php }?>
						<?php  $_smarty_tpl->tpl_vars['option'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['r']->value['options']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['option']->key => $_smarty_tpl->tpl_vars['option']->value){
?>
							<div><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['option']->value['votes']);?>
 : <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['option']->value['title']);?>
</div>
						<?php }} ?>
					</div>
				<?php }} ?>
			</div>
		<?php }else{ ?>
			<div id="pollzone">
				<div class="pollnav">
					<?php echo smarty_function_button(array('href'=>"#",'_onclick'=>"hide('pollzone');hide('polledit');show('pollopen');return false;",'_text'=>"[-]"),$_smarty_tpl);?>

				</div>
				<?php  $_smarty_tpl->tpl_vars['r'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('ratings')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['r']->key => $_smarty_tpl->tpl_vars['r']->value){
?>
					<div>
						<?php if ($_smarty_tpl->tpl_vars['r']->value['title']){?>
							<div><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['r']->value['title']);?>
</div>
						<?php }?>
						<?php  $_smarty_tpl->tpl_vars['option'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['r']->value['options']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['option']->key => $_smarty_tpl->tpl_vars['option']->value){
?>
							<div><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['option']->value['votes']);?>
 : <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['option']->value['title']);?>
</div>
						<?php }} ?>
					</div>
				<?php }} ?>
			</div>
		<?php }?>
	</div>
<?php }?>
<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\poll.tpl -->