<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:00
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\tiki-page_bar.tpl" */ ?>
<?php /*%%SmartyHeaderCode:177914f1e08e4b97b46-26858154%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dd478c8e4f9c4cf82e9483c8a50686648abcffb9' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\tiki-page_bar.tpl',
      1 => 1316450220,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '177914f1e08e4b97b46-26858154',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_favorite')) include 'lib/smarty_tiki\function.favorite.php';
if (!is_callable('smarty_function_button')) include 'lib/smarty_tiki\function.button.php';
if (!is_callable('smarty_block_self_link')) include 'lib/smarty_tiki\block.self_link.php';
if (!is_callable('smarty_modifier_escape')) include 'lib/smarty_tiki\modifier.escape.php';
if (!is_callable('smarty_function_service')) include 'lib/smarty_tiki\function.service.php';
if (!is_callable('smarty_block_jq')) include 'lib/smarty_tiki\block.jq.php';
if (!is_callable('smarty_block_tr')) include 'lib/smarty_tiki\block.tr.php';
if (!is_callable('smarty_modifier_sefurl')) include 'lib/smarty_tiki\modifier.sefurl.php';
if (!is_callable('smarty_function_attachments')) include 'lib/smarty_tiki\function.attachments.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\tiki-page_bar.tpl -->
<?php if (!isset($_smarty_tpl->getVariable('versioned',null,true,false)->value)||!$_smarty_tpl->getVariable('versioned')->value){?>
	<?php ob_start(); ?><?php echo smarty_function_favorite(array('type'=>"wiki page",'object'=>$_smarty_tpl->getVariable('page')->value),$_smarty_tpl);?>
<?php if ($_smarty_tpl->getVariable('edit_page')->value!='y'){?><?php if (($_smarty_tpl->getVariable('editable')->value&&($_smarty_tpl->getVariable('tiki_p_edit')->value=='y'||((mb_detect_encoding($_smarty_tpl->getVariable('page')->value, 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtolower($_smarty_tpl->getVariable('page')->value,SMARTY_RESOURCE_CHAR_SET) : strtolower($_smarty_tpl->getVariable('page')->value))=='sandbox')||((!isset($_smarty_tpl->getVariable('user',null,true,false)->value)||!$_smarty_tpl->getVariable('user')->value)&&$_smarty_tpl->getVariable('prefs')->value['wiki_encourage_contribution']=='y'))||$_smarty_tpl->getVariable('tiki_p_admin_wiki')->value=='y'){?><?php if ($_smarty_tpl->getVariable('beingEdited')->value=='y'){?><?php $_smarty_tpl->tpl_vars['thisPageClass'] = new Smarty_variable('+highlight', null, null);?><?php }else{ ?><?php $_smarty_tpl->tpl_vars['thisPageClass'] = new Smarty_variable('', null, null);?><?php }?><?php if ($_smarty_tpl->getVariable('prefs')->value['flaggedrev_approval']!='y'||!$_smarty_tpl->getVariable('revision_approval')->value||$_smarty_tpl->getVariable('lastVersion')->value==$_smarty_tpl->getVariable('revision_displayed')->value){?><?php echo smarty_function_button(array('_keepall'=>'y','href'=>"tiki-editpage.php",'page'=>$_smarty_tpl->getVariable('page')->value,'page_ref_id'=>$_smarty_tpl->getVariable('page_ref_id')->value,'_class'=>$_smarty_tpl->getVariable('thisPageClass')->value,'_text'=>"Edit this page"),$_smarty_tpl);?>
<?php }elseif($_smarty_tpl->getVariable('tiki_p_wiki_view_latest')->value=='y'){?><span class="button"><?php $_smarty_tpl->smarty->_tag_stack[] = array('self_link', array('latest'=>1)); $_block_repeat=true; smarty_block_self_link(array('latest'=>1), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
View latest version before editing<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_self_link(array('latest'=>1), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span><?php }?><?php }?><?php if ($_smarty_tpl->getVariable('prefs')->value['feature_source']=='y'&&$_smarty_tpl->getVariable('tiki_p_wiki_view_source')->value=='y'){?><?php echo smarty_function_button(array('_keepall'=>'y','href'=>"tiki-pagehistory.php",'page'=>$_smarty_tpl->getVariable('page')->value,'source'=>"0",'_text'=>"Source"),$_smarty_tpl);?>
<?php }?><?php if (((mb_detect_encoding($_smarty_tpl->getVariable('page')->value, 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtolower($_smarty_tpl->getVariable('page')->value,SMARTY_RESOURCE_CHAR_SET) : strtolower($_smarty_tpl->getVariable('page')->value))!='sandbox'){?><?php if ($_smarty_tpl->getVariable('tiki_p_remove')->value=='y'&&$_smarty_tpl->getVariable('editable')->value){?><?php echo smarty_function_button(array('_keepall'=>'y','href'=>"tiki-removepage.php",'page'=>$_smarty_tpl->getVariable('page')->value,'version'=>"last",'_text'=>"Remove"),$_smarty_tpl);?>
<?php }?><?php if ($_smarty_tpl->getVariable('tiki_p_rename')->value=='y'&&$_smarty_tpl->getVariable('editable')->value){?><?php echo smarty_function_button(array('_keepall'=>'y','href'=>"tiki-rename_page.php",'page'=>$_smarty_tpl->getVariable('page')->value,'_text'=>"Rename"),$_smarty_tpl);?>
<?php }?><?php if ($_smarty_tpl->getVariable('prefs')->value['feature_wiki_usrlock']=='y'&&isset($_smarty_tpl->getVariable('user',null,true,false)->value)&&$_smarty_tpl->getVariable('user')->value&&$_smarty_tpl->getVariable('tiki_p_lock')->value=='y'){?><?php if (!$_smarty_tpl->getVariable('lock')->value){?><?php echo smarty_function_button(array('_keepall'=>'y','href'=>"tiki-index.php",'page'=>$_smarty_tpl->getVariable('page')->value,'action'=>"lock",'_text'=>"Lock"),$_smarty_tpl);?>
<?php }elseif($_smarty_tpl->getVariable('tiki_p_admin_wiki')->value=='y'||$_smarty_tpl->getVariable('user')->value==$_smarty_tpl->getVariable('page_user')->value){?><?php echo smarty_function_button(array('_keepall'=>'y','href'=>"tiki-index.php",'page'=>$_smarty_tpl->getVariable('page')->value,'action'=>"unlock",'_text'=>"Unlock"),$_smarty_tpl);?>
<?php }?><?php }?><?php if ($_smarty_tpl->getVariable('tiki_p_admin_wiki')->value=='y'||$_smarty_tpl->getVariable('tiki_p_assign_perm_wiki_page')->value=='y'){?><?php echo smarty_function_button(array('_keepall'=>'y','href'=>"tiki-objectpermissions.php",'objectId'=>$_smarty_tpl->getVariable('page')->value,'objectName'=>$_smarty_tpl->getVariable('page')->value,'objectType'=>"wiki+page",'permType'=>"wiki",'_text'=>"Permissions"),$_smarty_tpl);?>
<?php }?><?php if ($_smarty_tpl->getVariable('prefs')->value['feature_history']=='y'&&$_smarty_tpl->getVariable('tiki_p_wiki_view_history')->value=='y'){?><?php echo smarty_function_button(array('_keepall'=>'y','href'=>"tiki-pagehistory.php",'page'=>$_smarty_tpl->getVariable('page')->value,'_text'=>"History"),$_smarty_tpl);?>
<?php }?><?php if ($_smarty_tpl->getVariable('prefs')->value['feature_page_contribution']=='y'&&$_smarty_tpl->getVariable('tiki_p_page_contribution_view')->value=='y'){?><?php echo smarty_function_button(array('_keepall'=>'y','href'=>"tiki-page_contribution.php",'page'=>$_smarty_tpl->getVariable('page')->value,'_text'=>"Contributions by author"),$_smarty_tpl);?>
<?php }?><?php }?><?php if ($_smarty_tpl->getVariable('prefs')->value['feature_likePages']=='y'&&$_smarty_tpl->getVariable('tiki_p_wiki_view_similar')->value=='y'){?><?php echo smarty_function_button(array('_keepall'=>'y','href'=>"tiki-likepages.php",'page'=>$_smarty_tpl->getVariable('page')->value,'_text'=>"Similar"),$_smarty_tpl);?>
<?php }?><?php if ($_smarty_tpl->getVariable('prefs')->value['feature_wiki_undo']=='y'&&$_smarty_tpl->getVariable('canundo')->value=='y'){?><?php echo smarty_function_button(array('_keepall'=>'y','href'=>"tiki-index.php",'page'=>$_smarty_tpl->getVariable('page')->value,'undo'=>"1",'_text'=>"Undo"),$_smarty_tpl);?>
<?php }?><?php if ($_smarty_tpl->getVariable('prefs')->value['feature_wiki_make_structure']=='y'&&$_smarty_tpl->getVariable('tiki_p_edit_structures')->value=='y'&&$_smarty_tpl->getVariable('editable')->value&&$_smarty_tpl->getVariable('structure')->value=='n'&&count($_smarty_tpl->getVariable('showstructs')->value)==0){?><?php echo smarty_function_button(array('_keepall'=>'y','href'=>"tiki-index.php",'page'=>$_smarty_tpl->getVariable('page')->value,'convertstructure'=>"1",'_text'=>"Make Structure"),$_smarty_tpl);?>
<?php }?><?php if ($_smarty_tpl->getVariable('prefs')->value['wiki_uses_slides']=='y'){?><?php if ($_smarty_tpl->getVariable('show_slideshow')->value=='y'){?><?php echo smarty_function_button(array('_keepall'=>'y','href'=>"tiki-slideshow.php",'page'=>$_smarty_tpl->getVariable('page')->value,'_text'=>"Slideshow"),$_smarty_tpl);?>
<?php }elseif($_smarty_tpl->getVariable('structure')->value=='y'){?><?php echo smarty_function_button(array('_keepall'=>'y','href'=>"tiki-slideshow2.php",'page_ref_id'=>$_smarty_tpl->getVariable('page_info')->value['page_ref_id'],'_text'=>"Slideshow"),$_smarty_tpl);?>
<?php }?><?php }?><?php if ($_smarty_tpl->getVariable('prefs')->value['feature_wiki_export']=='y'&&($_smarty_tpl->getVariable('tiki_p_admin_wiki')->value=='y'||$_smarty_tpl->getVariable('tiki_p_export_wiki')->value=='y')){?><?php echo smarty_function_button(array('_keepall'=>'y','href'=>"tiki-export_wiki_pages.php",'page'=>$_smarty_tpl->getVariable('page')->value,'_text'=>"Export"),$_smarty_tpl);?>
<?php }?><?php if ($_smarty_tpl->getVariable('prefs')->value['feature_wiki_discuss']=='y'&&$_smarty_tpl->getVariable('show_page')->value=='y'&&$_smarty_tpl->getVariable('tiki_p_forum_post')->value=='y'){?><?php ob_start(); ?><?php $_template = new Smarty_Internal_Template('wiki-discussion.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?> [tiki-index.php?page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,'url');?>
|<?php echo $_smarty_tpl->getVariable('page')->value;?>
]<?php  $_smarty_tpl->assign('wiki_discussion_string', ob_get_contents()); Smarty::$_smarty_vars['capture']['default']=ob_get_clean();?><?php echo smarty_function_button(array('_keepall'=>'y','href'=>"tiki-view_forum.php",'forumId'=>$_smarty_tpl->getVariable('prefs')->value['wiki_forum_id'],'comments_postComment'=>"post",'comments_title'=>$_smarty_tpl->getVariable('page')->value,'comments_data'=>$_smarty_tpl->getVariable('wiki_discussion_string')->value,'comment_topictype'=>"n",'_text'=>"Discuss"),$_smarty_tpl);?>
<?php }?><?php if ($_smarty_tpl->getVariable('show_page')->value=='y'){?><?php if ($_smarty_tpl->getVariable('prefs')->value['feature_wiki_comments']=='y'&&($_smarty_tpl->getVariable('prefs')->value['wiki_comments_allow_per_page']!='y'||$_smarty_tpl->getVariable('info')->value['comments_enabled']=='y')&&$_smarty_tpl->getVariable('tiki_p_wiki_view_comments')->value=='y'&&$_smarty_tpl->getVariable('tiki_p_read_comments')->value=='y'){?><span class="button"><a id="comment-toggle" href="<?php echo smarty_function_service(array('controller'=>'comment','action'=>'list','type'=>"wiki page",'objectId'=>$_smarty_tpl->getVariable('page')->value),$_smarty_tpl);?>
#comment-container">Comments</a></span><?php $_smarty_tpl->smarty->_tag_stack[] = array('jq', array()); $_block_repeat=true; smarty_block_jq(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

						$('#comment-toggle').comment_toggle();
					<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_jq(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }?><?php if ($_smarty_tpl->getVariable('prefs')->value['feature_wiki_attachments']=='y'&&($_smarty_tpl->getVariable('tiki_p_wiki_view_attachments')->value=='y'&&count($_smarty_tpl->getVariable('atts')->value)>0||$_smarty_tpl->getVariable('tiki_p_wiki_attach_files')->value=='y'||$_smarty_tpl->getVariable('tiki_p_wiki_admin_attachments')->value=='y')){?><?php if (count($_smarty_tpl->getVariable('atts')->value)>0){?><?php $_smarty_tpl->tpl_vars['thisbuttonclass'] = new Smarty_variable('highlight', null, null);?><?php }else{ ?><?php $_smarty_tpl->tpl_vars['thisbuttonclass'] = new Smarty_variable('', null, null);?><?php }?><?php ob_start(); ?><?php if (count($_smarty_tpl->getVariable('atts')->value)==0||$_smarty_tpl->getVariable('tiki_p_wiki_attach_files')->value=='y'&&$_smarty_tpl->getVariable('tiki_p_wiki_view_attachments')->value=='n'&&$_smarty_tpl->getVariable('tiki_p_wiki_admin_attachments')->value=='n'){?>Attach File<?php }elseif(count($_smarty_tpl->getVariable('atts')->value)==1){?>1 File Attached<?php }else{ ?><?php $_smarty_tpl->smarty->_tag_stack[] = array('tr', array()); $_block_repeat=true; smarty_block_tr(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo count($_smarty_tpl->getVariable('atts')->value);?>
 files attached<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tr(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }?><?php  $_smarty_tpl->assign('thistext', ob_get_contents()); Smarty::$_smarty_vars['capture']['default']=ob_get_clean();?>
					<?php if (count($_smarty_tpl->getVariable('atts')->value)>0||$_smarty_tpl->getVariable('editable')->value){?>
					<?php echo smarty_function_button(array('href'=>"#attachments",'_flip_id'=>"attzone".($_smarty_tpl->getVariable('pagemd5')->value),'_class'=>$_smarty_tpl->getVariable('thisbuttonclass')->value,'_text'=>$_smarty_tpl->getVariable('thistext')->value,'_flip_default_open'=>$_smarty_tpl->getVariable('prefs')->value['w_displayed_default']),$_smarty_tpl);?>

					<?php }?>
				<?php }?>

				<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_multilingual']=='y'&&($_smarty_tpl->getVariable('tiki_p_edit')->value=='y'||((!isset($_smarty_tpl->getVariable('user',null,true,false)->value)||!$_smarty_tpl->getVariable('user')->value)&&$_smarty_tpl->getVariable('prefs')->value['wiki_encourage_contribution']=='y'))&&!$_smarty_tpl->getVariable('lock')->value){?>
					<?php echo smarty_function_button(array('_keepall'=>'y','href'=>"tiki-edit_translation.php",'page'=>$_smarty_tpl->getVariable('page')->value,'_text'=>"Translate"),$_smarty_tpl);?>

				<?php }?>

				<?php if ($_smarty_tpl->getVariable('tiki_p_admin_wiki')->value=='y'&&$_smarty_tpl->getVariable('prefs')->value['wiki_keywords']=='y'){?>
					<?php echo smarty_function_button(array('_keepall'=>'y','href'=>"tiki-admin_keywords.php",'page'=>$_smarty_tpl->getVariable('page')->value,'_text'=>"Keywords"),$_smarty_tpl);?>

				<?php }?>
				<?php if ((isset($_smarty_tpl->getVariable('user',null,true,false)->value)&&$_smarty_tpl->getVariable('user')->value)&&(isset($_smarty_tpl->getVariable('tiki_p_create_bookmarks',null,true,false)->value)&&$_smarty_tpl->getVariable('tiki_p_create_bookmarks')->value=='y')&&$_smarty_tpl->getVariable('prefs')->value['feature_user_bookmarks']=='y'){?>
					<?php echo smarty_function_button(array('_script'=>"tiki-user_bookmarks.php",'urlname'=>$_smarty_tpl->getVariable('page')->value,'urlurl'=>smarty_modifier_sefurl($_smarty_tpl->getVariable('page')->value),'addurl'=>"Add",'_text'=>"Bookmark",'_auto_args'=>"urlname,urlurl,addurl"),$_smarty_tpl);?>

				<?php }?>
			<?php }?>
		<?php }?>
	<?php  $_smarty_tpl->assign('page_bar', ob_get_contents()); Smarty::$_smarty_vars['capture']['default']=ob_get_clean();?>

	<?php if ($_smarty_tpl->getVariable('page_bar')->value!=''){?>
		<div class="clearfix" id="page-bar">
			<?php echo $_smarty_tpl->getVariable('page_bar')->value;?>

		</div>
	<?php }?>

	<?php if ($_smarty_tpl->getVariable('wiki_extras')->value=='y'&&$_smarty_tpl->getVariable('prefs')->value['feature_wiki_attachments']=='y'&&$_smarty_tpl->getVariable('tiki_p_wiki_view_attachments')->value=='y'){?>
		<a name="attachments"></a>
		<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_use_fgal_for_wiki_attachments']=='y'){?>
			<?php echo smarty_function_attachments(array('_id'=>$_smarty_tpl->getVariable('page')->value,'_type'=>'wiki page'),$_smarty_tpl);?>

		<?php }else{ ?>
			<?php $_template = new Smarty_Internal_Template('attachments.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
		<?php }?>
	<?php }?>

	<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_wiki_comments']=='y'&&$_smarty_tpl->getVariable('tiki_p_wiki_view_comments')->value=='y'&&$_smarty_tpl->getVariable('edit_page')->value!='y'){?>
		<div id="comment-container"></div>
	<?php }?>

	
<?php }?>
<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\tiki-page_bar.tpl -->