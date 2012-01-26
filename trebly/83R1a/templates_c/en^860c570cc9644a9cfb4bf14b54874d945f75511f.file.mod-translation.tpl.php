<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:08
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-translation.tpl" */ ?>
<?php /*%%SmartyHeaderCode:205004f1e08ec5cb846-14789458%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '860c570cc9644a9cfb4bf14b54874d945f75511f' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\modules/mod-translation.tpl',
      1 => 1311336370,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '205004f1e08ec5cb846-14789458',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_block_tikimodule')) include 'lib/smarty_tiki\block.tikimodule.php';
if (!is_callable('smarty_modifier_escape')) include 'lib/smarty_tiki\modifier.escape.php';
if (!is_callable('smarty_function_icon')) include 'lib/smarty_tiki\function.icon.php';
if (!is_callable('smarty_modifier_langname')) include 'lib/smarty_tiki\modifier.langname.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-translation.tpl -->

<?php $_smarty_tpl->tpl_vars["default_diff_style"] = new Smarty_variable("inlinediff-full", null, null);?>

<?php if ($_smarty_tpl->getVariable('show_translation_module')->value){?>

	<?php $_smarty_tpl->smarty->_tag_stack[] = array('tikimodule', array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>"translation",'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle'])); $_block_repeat=true; smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>"translation",'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

		<?php if (count($_smarty_tpl->getVariable('trads')->value)=='1'){?><p>There are no translations of this page.<p><?php }?>
			<?php if ($_smarty_tpl->getVariable('prefs')->value['quantify_changes']=='y'){?>
				<div>
					Up-to-date-ness: <?php echo $_smarty_tpl->getVariable('mod_translation_quantification')->value;?>
%
				</div>
				<?php echo $_smarty_tpl->getVariable('mod_translation_gauge')->value;?>

			<?php }?>
			<?php if ($_smarty_tpl->getVariable('mod_translation_better_known')->value||$_smarty_tpl->getVariable('mod_translation_better_other')->value){?>
				<div>			
					<?php if ($_smarty_tpl->getVariable('from_edit_page')->value!='y'){?>
						<b>Incoming:</b>
					<?php }else{ ?>
						To <strong>continue translating</strong>, select the language to translate from:
					<?php }?>			
					<?php if ($_smarty_tpl->getVariable('mod_translation_better_known')->value){?>
						<ul>
							<?php  $_smarty_tpl->tpl_vars['better'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('mod_translation_better_known')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['better']->key => $_smarty_tpl->tpl_vars['better']->value){
?>
								<li>
									<?php if ($_smarty_tpl->getVariable('from_edit_page')->value=='y'){?>
										<a title="update from it" href="tiki-editpage.php?page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,'url');?>
&amp;source_page=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['better']->value['page'],'url');?>
&amp;oldver=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['better']->value['last_update'],'url');?>
&amp;newver=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['better']->value['current_version'],'url');?>
&amp;diff_style=<?php echo $_smarty_tpl->getVariable('default_diff_style')->value;?>
"><?php echo smarty_function_icon(array('_id'=>'page_translate_from','alt'=>"update from it",'style'=>"vertical-align:middle"),$_smarty_tpl);?>
 <?php echo smarty_modifier_langname($_smarty_tpl->tpl_vars['better']->value['lang']);?>
</a> (<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['better']->value['page']);?>
)
									<?php }else{ ?>
										<?php if ($_smarty_tpl->getVariable('tiki_p_edit')->value=='y'){?>
											<a href="tiki-editpage.php?page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,'url');?>
&amp;source_page=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['better']->value['page'],'url');?>
&amp;oldver=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['better']->value['last_update'],'url');?>
&amp;newver=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['better']->value['current_version'],'url');?>
&amp;diff_style=<?php echo $_smarty_tpl->getVariable('default_diff_style')->value;?>
"><?php echo smarty_function_icon(array('_id'=>'page_translate_from','alt'=>"update from it",'style'=>"vertical-align:middle"),$_smarty_tpl);?>
</a>
									<?php }?>
									<a href="tiki-editpage.php?page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,'url');?>
&amp;source_page=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['better']->value['page'],'url');?>
&amp;oldver=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['better']->value['last_update'],'url');?>
&amp;newver=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['better']->value['current_version'],'url');?>
&amp;diff_style=<?php echo $_smarty_tpl->getVariable('default_diff_style')->value;?>
" title="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['better']->value['page']);?>
">					
									<?php if ($_smarty_tpl->getVariable('show_language')->value=='y'){?> 
										<?php echo smarty_modifier_langname($_smarty_tpl->tpl_vars['better']->value['lang']);?>
</a> 
									<?php }else{ ?>
										<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['better']->value['page']);?>
</a> (<?php echo $_smarty_tpl->tpl_vars['better']->value['lang'];?>
)
									<?php }?>
									<?php }?>
								</li>
							<?php }} ?>
						</ul>
					<?php }elseif($_smarty_tpl->getVariable('prefs')->value['change_language']=='y'){?>
						<div id="mod-translation-better-intro" style="display:block">None match your <a href="tiki-user_preferences.php" title="Set your preferred languages.">preferred languages</a>.</div>
					<?php }?> 
					
					<?php if ($_smarty_tpl->getVariable('mod_translation_better_other')->value){?>
						<?php if ($_smarty_tpl->getVariable('prefs')->value['change_language']=='y'){?>
							<a href="javascript:void(0)" onclick="intro=document.getElementById('mod-translation-better-intro');if(intro)intro.style.display='none';document.getElementById('mod-translation-better-ul').style.display='block';this.style.display='none'" class="linkmenu more"><?php echo smarty_function_icon(array('_id'=>'plus_small','alt'=>"More...",'width'=>"11",'height'=>"8",'style'=>"vertical-align:middle;border:0"),$_smarty_tpl);?>
 More...</a>
						<?php }?>
						<ul id="mod-translation-better-ul"<?php if ($_smarty_tpl->getVariable('prefs')->value['change_language']=='y'){?> style="display:none"<?php }?>>
							<?php  $_smarty_tpl->tpl_vars['better'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('mod_translation_better_other')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['better']->key => $_smarty_tpl->tpl_vars['better']->value){
?>
								<li>
									<?php if ($_smarty_tpl->getVariable('from_edit_page')->value=='y'){?>
										<a title="update from it" href="tiki-editpage.php?page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,'url');?>
&amp;source_page=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['better']->value['page'],'url');?>
&amp;oldver=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['better']->value['last_update'],'url');?>
&amp;newver=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['better']->value['current_version'],'url');?>
&amp;diff_style=<?php echo $_smarty_tpl->getVariable('default_diff_style')->value;?>
"><?php echo smarty_function_icon(array('_id'=>'page_translate_from','alt'=>"update from it",'style'=>"vertical-align:middle"),$_smarty_tpl);?>
 <?php echo smarty_modifier_langname($_smarty_tpl->tpl_vars['better']->value['lang']);?>
</a> (<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['better']->value['page']);?>
)
									<?php }else{ ?>
										<?php if ($_smarty_tpl->getVariable('tiki_p_edit')->value=='y'){?>
											<a href="tiki-editpage.php?page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,'url');?>
&amp;source_page=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['better']->value['page'],'url');?>
&amp;oldver=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['better']->value['last_update'],'url');?>
&amp;newver=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['better']->value['current_version'],'url');?>
&amp;diff_style=<?php echo $_smarty_tpl->getVariable('default_diff_style')->value;?>
"><?php echo smarty_function_icon(array('_id'=>'page_translate_from','alt'=>"update from it",'style'=>"vertical-align:middle"),$_smarty_tpl);?>
</a>
										<?php }?>
										<a href="tiki-index.php?page=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['better']->value['page'],'url');?>
&amp;no_bl=y"><?php echo smarty_function_icon(array('_id'=>'page','alt'=>"view",'style'=>"vertical-align:middle"),$_smarty_tpl);?>
</a>
										<a href="tiki-index.php?page=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['better']->value['page'],'url');?>
&amp;no_bl=y" title="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['better']->value['page']);?>
">
										<?php if ($_smarty_tpl->getVariable('show_language')->value=='y'){?>
											<?php echo smarty_modifier_langname($_smarty_tpl->tpl_vars['better']->value['lang']);?>
</a> 
										<?php }else{ ?>
											<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['better']->value['page']);?>
</a> (<?php echo $_smarty_tpl->tpl_vars['better']->value['lang'];?>
)
										<?php }?>
									<?php }?>
								</li>
							<?php }} ?>
						</ul>
					<?php }?>
				</div><br />
			<?php }?>
			
			<?php if ($_smarty_tpl->getVariable('mod_translation_worst_known')->value||$_smarty_tpl->getVariable('mod_translation_worst_other')->value){?>
				<div>			
					<b>Outgoing:</b>
					<?php if ($_smarty_tpl->getVariable('mod_translation_worst_known')->value){?>
					<ul>
						<?php  $_smarty_tpl->tpl_vars['worst'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('mod_translation_worst_known')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['worst']->key => $_smarty_tpl->tpl_vars['worst']->value){
?>
						<li>
							<?php if ($_smarty_tpl->getVariable('tiki_p_edit')->value=='y'){?>
								<a href="tiki-editpage.php?page=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['worst']->value['page'],'url');?>
&amp;source_page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,'url');?>
&amp;oldver=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['worst']->value['last_update'],'url');?>
&amp;newver=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('pageVersion')->value,'url');?>
&amp;diff_style=<?php echo $_smarty_tpl->getVariable('default_diff_style')->value;?>
"><?php echo smarty_function_icon(array('_id'=>'page_translate_to','alt'=>"update it",'style'=>"vertical-align:middle"),$_smarty_tpl);?>
</a>
							<?php }?>
							<a href="tiki-editpage.php?page=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['worst']->value['page'],'url');?>
&amp;source_page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,'url');?>
&amp;oldver=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['worst']->value['last_update'],'url');?>
&amp;newver=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('pageVersion')->value,'url');?>
&amp;diff_style=<?php echo $_smarty_tpl->getVariable('default_diff_style')->value;?>
" title="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['worst']->value['page']);?>
">
							<?php if ($_smarty_tpl->getVariable('show_language')->value=='y'){?>
							<?php echo smarty_modifier_langname($_smarty_tpl->tpl_vars['worst']->value['lang']);?>
</a> 
							<?php }else{ ?>
							<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['worst']->value['page']);?>
</a> (<?php echo $_smarty_tpl->tpl_vars['worst']->value['lang'];?>
)
							<?php }?>
						</li>
						<?php }} ?>
					</ul>
					<?php }elseif($_smarty_tpl->getVariable('prefs')->value['change_language']=='y'){?>
						<div id="mod-translation-worst-intro" style="display:block">None match your <a href="tiki-user_preferences.php">preferred languages</a>.</div>
					<?php }?>
					<?php if ($_smarty_tpl->getVariable('mod_translation_worst_other')->value){?>
		<?php if ($_smarty_tpl->getVariable('prefs')->value['change_language']=='y'){?>
					<a href="javascript:void(0)" onclick="intro=document.getElementById('mod-translation-worst-intro');if(intro)intro.style.display='none';document.getElementById('mod-translation-worst-ul').style.display='block';this.style.display='none'" class="linkmenu more"><?php echo smarty_function_icon(array('_id'=>'plus_small','alt'=>"More...",'width'=>"11",'height'=>"8",'style'=>"vertical-align:middle;border:0"),$_smarty_tpl);?>
More...</a>
		<?php }?>
					<ul id="mod-translation-worst-ul"<?php if ($_smarty_tpl->getVariable('prefs')->value['change_language']=='y'){?> style="display:none"<?php }?>>
						<?php  $_smarty_tpl->tpl_vars['worst'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('mod_translation_worst_other')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['worst']->key => $_smarty_tpl->tpl_vars['worst']->value){
?>
						<li>
							<?php if ($_smarty_tpl->getVariable('tiki_p_edit')->value=='y'){?>
								<a href="tiki-editpage.php?page=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['worst']->value['page'],'url');?>
&amp;source_page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,'url');?>
&amp;oldver=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['worst']->value['last_update'],'url');?>
&amp;newver=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('pageVersion')->value,'url');?>
&amp;diff_style=<?php echo $_smarty_tpl->getVariable('default_diff_style')->value;?>
"><?php echo smarty_function_icon(array('_id'=>'page_translate_to','alt'=>"update it",'style'=>"vertical-align:middle"),$_smarty_tpl);?>
</a>
							<?php }?>
							<a href="tiki-index.php?page=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['worst']->value['page'],'url');?>
&amp;no_bl=y"><?php echo smarty_function_icon(array('_id'=>'page','alt'=>"view",'style'=>"vertical-align:middle"),$_smarty_tpl);?>
</a>
							<a href="tiki-index.php?page=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['worst']->value['page'],'url');?>
&amp;no_bl=y" title="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['worst']->value['page']);?>
">
							<?php if ($_smarty_tpl->getVariable('show_language')->value=='y'){?>
							<?php echo smarty_modifier_langname($_smarty_tpl->tpl_vars['worst']->value['lang']);?>
</a> 
							<?php }else{ ?>
							<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['worst']->value['page']);?>
</a> (<?php echo $_smarty_tpl->tpl_vars['worst']->value['lang'];?>
)
							<?php }?>
						</li>
						<?php }} ?>
					</ul>
					<?php }?>
				</div><br />
			<?php }?>
		
	<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>"translation",'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php }?>
<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-translation.tpl -->