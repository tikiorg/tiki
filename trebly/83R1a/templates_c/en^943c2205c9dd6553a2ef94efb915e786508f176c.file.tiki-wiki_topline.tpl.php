<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:26:58
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\tiki-wiki_topline.tpl" */ ?>
<?php /*%%SmartyHeaderCode:264504f1e08e2d00e07-37417566%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '943c2205c9dd6553a2ef94efb915e786508f176c' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\tiki-wiki_topline.tpl',
      1 => 1313089890,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '264504f1e08e2d00e07-37417566',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include 'lib/smarty_tiki\modifier.escape.php';
if (!is_callable('smarty_function_breadcrumbs')) include 'lib/smarty_tiki\function.breadcrumbs.php';
if (!is_callable('smarty_function_query')) include 'lib/smarty_tiki\function.query.php';
if (!is_callable('smarty_function_icon')) include 'lib/smarty_tiki\function.icon.php';
if (!is_callable('smarty_block_ajax_href')) include 'lib/smarty_tiki\block.ajax_href.php';
if (!is_callable('smarty_block_jq')) include 'lib/smarty_tiki\block.jq.php';
if (!is_callable('smarty_modifier_truncate')) include 'lib/smarty_tiki\modifier.truncate.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\tiki-wiki_topline.tpl --><div class="wikitopline clearfix" style="clear: both;">
	<div class="content">
		<?php if (!isset($_smarty_tpl->getVariable('hide_page_header',null,true,false)->value)||!$_smarty_tpl->getVariable('hide_page_header')->value){?>
			<div class="wikiinfo" style="float: left">
				<?php if ($_smarty_tpl->getVariable('prefs')->value['wiki_page_name_above']=='y'&&$_smarty_tpl->getVariable('print_page')->value!='y'){?>
				    <a href="javascript:self.location=self.location;" class="titletop" title="refresh"><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value);?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?php }?>
				
				<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_wiki_pageid']=='y'&&$_smarty_tpl->getVariable('print_page')->value!='y'){?>
					<small><a class="link" href="tiki-index.php?page_id=<?php echo $_smarty_tpl->getVariable('page_id')->value;?>
">page id: <?php echo $_smarty_tpl->getVariable('page_id')->value;?>
</a></small>
				<?php }?>
				
				<?php echo smarty_function_breadcrumbs(array('type'=>"desc",'loc'=>"page",'crumbs'=>$_smarty_tpl->getVariable('crumbs')->value),$_smarty_tpl);?>

				
				<?php if ($_smarty_tpl->getVariable('cached_page')->value=='y'){?><span class="cachedStatus">(Cached)</span><?php }?>
				<?php if ($_smarty_tpl->getVariable('is_categorized')->value=='y'&&$_smarty_tpl->getVariable('prefs')->value['feature_categories']=='y'&&$_smarty_tpl->getVariable('prefs')->value['feature_categorypath']=='y'&&$_smarty_tpl->getVariable('tiki_p_view_category')->value=='y'){?>
					<?php echo $_smarty_tpl->getVariable('display_catpath')->value;?>

				<?php }?>
			</div>

			<?php if (!isset($_smarty_tpl->getVariable('versioned',null,true,false)->value)){?>
				<?php if ($_smarty_tpl->getVariable('print_page')->value!='y'){?>
					<div class="wikiactions" style="float: right; padding-left:10px; white-space: nowrap">
						<div class="icons" style="float: left;">
							<?php if ($_smarty_tpl->getVariable('pdf_export')->value=='y'){?>
								<a href="tiki-print.php?<?php echo smarty_function_query(array('display'=>"pdf",'page'=>$_smarty_tpl->getVariable('page')->value),$_smarty_tpl);?>
" title="PDF"><?php echo smarty_function_icon(array('_id'=>'page_white_acrobat','alt'=>"PDF"),$_smarty_tpl);?>
</a>
							<?php }?>
							<?php if ($_smarty_tpl->getVariable('prefs')->value['flaggedrev_approval']!='y'||!$_smarty_tpl->getVariable('revision_approval')->value||$_smarty_tpl->getVariable('lastVersion')->value==$_smarty_tpl->getVariable('revision_displayed')->value){?>
								<?php if ($_smarty_tpl->getVariable('editable')->value&&($_smarty_tpl->getVariable('tiki_p_edit')->value=='y'||((mb_detect_encoding($_smarty_tpl->getVariable('page')->value, 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtolower($_smarty_tpl->getVariable('page')->value,SMARTY_RESOURCE_CHAR_SET) : strtolower($_smarty_tpl->getVariable('page')->value))=='sandbox')&&$_smarty_tpl->getVariable('beingEdited')->value!='y'&&$_smarty_tpl->getVariable('machine_translate_to_lang')->value==''){?>
									<a title="Edit this page" <?php $_smarty_tpl->smarty->_tag_stack[] = array('ajax_href', array('template'=>"tiki-editpage.tpl")); $_block_repeat=true; smarty_block_ajax_href(array('template'=>"tiki-editpage.tpl"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
tiki-editpage.php?page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,"url");?>
<?php if (!empty($_smarty_tpl->getVariable('page_ref_id',null,true,false)->value)&&$_smarty_tpl->getVariable('needsStaging')->value!='y'){?>&amp;page_ref_id=<?php echo $_smarty_tpl->getVariable('page_ref_id')->value;?>
<?php }?><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_ajax_href(array('template'=>"tiki-editpage.tpl"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
><?php echo smarty_function_icon(array('_id'=>'page_edit','alt'=>"Edit this page"),$_smarty_tpl);?>
</a>
									<?php if ($_smarty_tpl->getVariable('prefs')->value['wiki_edit_icons_toggle']=='y'&&($_smarty_tpl->getVariable('prefs')->value['wiki_edit_plugin']=='y'||$_smarty_tpl->getVariable('prefs')->value['wiki_edit_section']=='y')){?>
										<?php $_smarty_tpl->smarty->_tag_stack[] = array('jq', array()); $_block_repeat=true; smarty_block_jq(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

											$("#wiki_plugin_edit_view").click( function () {
												var src = $("#wiki_plugin_edit_view img").attr("src");
												if (src.indexOf("wiki_plugin_edit_view") > -1) {
													$(".editplugin, .icon_edit_section").show();
													$("#wiki_plugin_edit_view img").attr("src", src.replace("wiki_plugin_edit_view", "wiki_plugin_edit_hide"));
													setCookieBrowser("wiki_plugin_edit_view", true);
												} else {
													$(".editplugin, .icon_edit_section").hide();
													$("#wiki_plugin_edit_view img").attr("src", src.replace("wiki_plugin_edit_hide", "wiki_plugin_edit_view"));
													deleteCookie("wiki_plugin_edit_view");
												}
												return false;
											});
											if (!getCookie("wiki_plugin_edit_view")) {$(".editplugin, .icon_edit_section").hide(); } else { $("#wiki_plugin_edit_view").click(); }
										<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_jq(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

										<a title="View edit icons" href="#" id="wiki_plugin_edit_view"><?php echo smarty_function_icon(array('_id'=>'wiki_plugin_edit_view','title'=>"View edit icons"),$_smarty_tpl);?>
</a>
									<?php }?>
								<?php }?>
							<?php }?>
							<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_morcego']=='y'&&$_smarty_tpl->getVariable('prefs')->value['wiki_feature_3d']=='y'){?>
								<a title="3d browser" href="javascript:wiki3d_open('<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value);?>
',<?php echo $_smarty_tpl->getVariable('prefs')->value['wiki_3d_width'];?>
, <?php echo $_smarty_tpl->getVariable('prefs')->value['wiki_3d_height'];?>
)"><?php echo smarty_function_icon(array('_id'=>'wiki3d','alt'=>"3d browser"),$_smarty_tpl);?>
</a>
							<?php }?>
							<?php if ($_smarty_tpl->getVariable('cached_page')->value=='y'){?>
								<a title="Refresh" href="tiki-index.php?page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,"url");?>
&amp;refresh=1"><?php echo smarty_function_icon(array('_id'=>'arrow_refresh'),$_smarty_tpl);?>
</a>
							<?php }?>
							<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_wiki_print']=='y'){?>
								<a title="Print" href="tiki-print.php?<?php if (!empty($_smarty_tpl->getVariable('page_ref_id',null,true,false)->value)){?>page_ref_id=<?php echo $_smarty_tpl->getVariable('page_ref_id')->value;?>
&amp;<?php }?>page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,"url");?>
"><?php echo smarty_function_icon(array('_id'=>'printer','alt'=>"Print"),$_smarty_tpl);?>
</a>
							<?php }?>
					
							<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_share']=='y'&&$_smarty_tpl->getVariable('tiki_p_share')->value=='y'){?>
								<a title="Share this page" href="tiki-share.php?url=<?php echo smarty_modifier_escape($_SERVER['REQUEST_URI'],'url');?>
"><?php echo smarty_function_icon(array('_id'=>'share_link','alt'=>"Share this page"),$_smarty_tpl);?>
</a>
							<?php }?>
							<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_tell_a_friend']=='y'&&$_smarty_tpl->getVariable('tiki_p_tell_a_friend')->value=='y'){?>
								<a title="Send a link" href="tiki-tell_a_friend.php?url=<?php echo smarty_modifier_escape($_SERVER['REQUEST_URI'],'url');?>
"><?php echo smarty_function_icon(array('_id'=>'email_link','alt'=>"Send a link"),$_smarty_tpl);?>
</a>
							<?php }?>
							<?php if (!empty($_smarty_tpl->getVariable('user',null,true,false)->value)&&$_smarty_tpl->getVariable('prefs')->value['feature_notepad']=='y'&&$_smarty_tpl->getVariable('tiki_p_notepad')->value=='y'){?>
								<a title="Save to notepad" href="tiki-index.php?page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,"url");?>
&amp;savenotepad=1<?php if (!empty($_smarty_tpl->getVariable('page_ref_id',null,true,false)->value)){?>&amp;page_ref_id=<?php echo $_smarty_tpl->getVariable('page_ref_id')->value;?>
<?php }?>"><?php echo smarty_function_icon(array('_id'=>'disk','alt'=>"Save to notepad"),$_smarty_tpl);?>
</a>
							<?php }?>
							<?php if (!empty($_smarty_tpl->getVariable('user',null,true,false)->value)&&$_smarty_tpl->getVariable('prefs')->value['feature_user_watches']=='y'){?>
								<?php if ($_smarty_tpl->getVariable('user_watching_page')->value=='n'){?>
									<a href="tiki-index.php?page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,"url");?>
&amp;watch_event=wiki_page_changed&amp;watch_object=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,"url");?>
&amp;watch_action=add<?php if ($_smarty_tpl->getVariable('structure')->value=='y'){?>&amp;structure=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('home_info')->value['pageName'],'url');?>
<?php }?>" class="icon"><?php echo smarty_function_icon(array('_id'=>'eye','alt'=>"Page is NOT being monitored. Click icon to START monitoring."),$_smarty_tpl);?>
</a>
								<?php }else{ ?>
									<a href="tiki-index.php?page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,"url");?>
&amp;watch_event=wiki_page_changed&amp;watch_object=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,"url");?>
&amp;watch_action=remove<?php if ($_smarty_tpl->getVariable('structure')->value=='y'){?>&amp;structure=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('home_info')->value['pageName'],'url');?>
<?php }?>" class="icon"><?php echo smarty_function_icon(array('_id'=>'no_eye','alt'=>"Page IS being monitored. Click icon to STOP monitoring."),$_smarty_tpl);?>
</a>
								<?php }?>
								<?php if ($_smarty_tpl->getVariable('structure')->value=='y'&&$_smarty_tpl->getVariable('tiki_p_watch_structure')->value=='y'){?>
									<?php if ($_smarty_tpl->getVariable('user_watching_structure')->value!='y'){?>
										<a href="tiki-index.php?page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,"url");?>
&amp;watch_event=structure_changed&amp;watch_object=<?php echo $_smarty_tpl->getVariable('page_info')->value['page_ref_id'];?>
&amp;watch_action=add_desc&amp;structure=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('home_info')->value['pageName'],'url');?>
"><?php echo smarty_function_icon(array('_id'=>'eye_arrow_down','alt'=>"Monitor the Sub-Structure"),$_smarty_tpl);?>
</a>
									<?php }else{ ?>
										<a href="tiki-index.php?page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,"url");?>
&amp;watch_event=structure_changed&amp;watch_object=<?php echo $_smarty_tpl->getVariable('page_info')->value['page_ref_id'];?>
&amp;watch_action=remove_desc&amp;structure=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('home_info')->value['pageName'],'url');?>
"><?php echo smarty_function_icon(array('_id'=>'no_eye_arrow_down','alt'=>"Stop Monitoring the Sub-Structure"),$_smarty_tpl);?>
</a>
									<?php }?>
								<?php }?>
							<?php }?>
							<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_group_watches']=='y'&&($_smarty_tpl->getVariable('tiki_p_admin_users')->value=='y'||$_smarty_tpl->getVariable('tiki_p_admin')->value=='y')){?>
								<a href="tiki-object_watches.php?objectId=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,"url");?>
&amp;watch_event=wiki_page_changed&amp;objectType=wiki+page&amp;objectName=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,"url");?>
&amp;objectHref=<?php echo smarty_modifier_escape(('tiki-index.php?page=').($_smarty_tpl->getVariable('page')->value),"url");?>
" class="icon"><?php echo smarty_function_icon(array('_id'=>'eye_group','alt'=>"Group Monitor"),$_smarty_tpl);?>
</a>
					
								<?php if ($_smarty_tpl->getVariable('structure')->value=='y'){?>
									<a href="tiki-object_watches.php?objectId=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page_info')->value['page_ref_id'],"url");?>
&amp;watch_event=structure_changed&amp;objectType=structure&amp;objectName=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,"url");?>
&amp;objectHref=<?php echo smarty_modifier_escape(('tiki-index.php?page_ref_id=').($_smarty_tpl->getVariable('page_ref_id')->value),"url");?>
" class="icon"><?php echo smarty_function_icon(array('_id'=>'eye_group_arrow_down','alt'=>"Group Monitor on Structure"),$_smarty_tpl);?>
</a>
								<?php }?>
							<?php }?>
							<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_backlinks']=='y'&&$_smarty_tpl->getVariable('backlinks')->value&&$_smarty_tpl->getVariable('tiki_p_view_backlink')->value=='y'){?>
								<div class="backlinks_button">
									<ul class="clearfix cssmenu_horiz">
										<li class="tabmark">
											<?php echo smarty_function_icon(array('_id'=>'arrow_turn_left','title'=>"Backlinks",'class'=>"icon"),$_smarty_tpl);?>

											<ul class="backlinks_poppedup">
												<li class="tabcontent">
													<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['back']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['back']['name'] = 'back';
$_smarty_tpl->tpl_vars['smarty']->value['section']['back']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('backlinks')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['back']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['back']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['back']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['back']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['back']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['back']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['back']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['back']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['back']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['back']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['back']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['back']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['back']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['back']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['back']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['back']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['back']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['back']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['back']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['back']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['back']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['back']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['back']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['back']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['back']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['back']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['back']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['back']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['back']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['back']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['back']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['back']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['back']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['back']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['back']['total']);
?>
													<a href="tiki-index.php?page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('backlinks')->value[$_smarty_tpl->getVariable('smarty')->value['section']['back']['index']]['fromPage'],'url');?>
" title="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('backlinks')->value[$_smarty_tpl->getVariable('smarty')->value['section']['back']['index']]['fromPage']);?>
">
														<?php if ($_smarty_tpl->getVariable('prefs')->value['wiki_backlinks_name_len']>='1'){?><?php echo smarty_modifier_escape(smarty_modifier_truncate($_smarty_tpl->getVariable('backlinks')->value[$_smarty_tpl->getVariable('smarty')->value['section']['back']['index']]['fromPage'],$_smarty_tpl->getVariable('prefs')->value['wiki_backlinks_name_len'],"...",true));?>
<?php }else{ ?><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('backlinks')->value[$_smarty_tpl->getVariable('smarty')->value['section']['back']['index']]['fromPage']);?>
<?php }?>
													</a>
													<?php endfor; endif; ?>
												</li>
											</ul>
										</li>
									</ul>
								</div>		
							<?php }?>
						</div><!-- END of icons -->
			
						<?php if (($_smarty_tpl->getVariable('structure')->value=='y'&&count($_smarty_tpl->getVariable('showstructs')->value)>1)||($_smarty_tpl->getVariable('structure')->value=='n'&&count($_smarty_tpl->getVariable('showstructs')->value)!=0)){?>
							<form action="tiki-index.php" method="post" style="float: left">
								<select name="page_ref_id" onchange="this.form.submit()">
									<option>Structures...</option>
									<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['struct']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['name'] = 'struct';
$_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('showstructs')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['struct']['total']);
?>
										<option value="<?php echo $_smarty_tpl->getVariable('showstructs')->value[$_smarty_tpl->getVariable('smarty')->value['section']['struct']['index']]['req_page_ref_id'];?>
" <?php if ($_smarty_tpl->getVariable('showstructs')->value[$_smarty_tpl->getVariable('smarty')->value['section']['struct']['index']]['pageName']==$_smarty_tpl->getVariable('structure_path')->value[0]['pageName']){?>selected="selected"<?php }?>>
											<?php if ($_smarty_tpl->getVariable('showstructs')->value[$_smarty_tpl->getVariable('smarty')->value['section']['struct']['index']]['page_alias']){?>
												<?php echo $_smarty_tpl->getVariable('showstructs')->value[$_smarty_tpl->getVariable('smarty')->value['section']['struct']['index']]['page_alias'];?>

											<?php }else{ ?>
												<?php echo $_smarty_tpl->getVariable('showstructs')->value[$_smarty_tpl->getVariable('smarty')->value['section']['struct']['index']]['pageName'];?>

											<?php }?>
										</option>
									<?php endfor; endif; ?>
								</select>
							</form>
						<?php }?>
				
						<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_multilingual']=='y'&&$_smarty_tpl->getVariable('prefs')->value['show_available_translations']=='y'&&$_smarty_tpl->getVariable('machine_translate_to_lang')->value==''){?>
							<div style="float: left">
								<?php $_template = new Smarty_Internal_Template('translated-lang.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
$_template->assign('td','n'); echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
							</div>
						<?php }?>
					</div> 
				<?php }?> 
			<?php }?>
		<?php }?> 
	</div> 
</div> 
<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\tiki-wiki_topline.tpl -->