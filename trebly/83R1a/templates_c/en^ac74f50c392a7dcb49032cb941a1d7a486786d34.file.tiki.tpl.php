<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:09
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\tiki.tpl" */ ?>
<?php /*%%SmartyHeaderCode:272534f1e08edc2dab4-80836049%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ac74f50c392a7dcb49032cb941a1d7a486786d34' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\tiki.tpl',
      1 => 1315821498,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '272534f1e08edc2dab4-80836049',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_html_body_attributes')) include 'lib/smarty_tiki\function.html_body_attributes.php';
if (!is_callable('smarty_block_self_link')) include 'lib/smarty_tiki\block.self_link.php';
if (!is_callable('smarty_function_icon')) include 'lib/smarty_tiki\function.icon.php';
if (!is_callable('smarty_modifier_escape')) include 'lib/smarty_tiki\modifier.escape.php';
if (!is_callable('smarty_function_error_report')) include 'lib/smarty_tiki\function.error_report.php';
if (!is_callable('smarty_block_remarksbox')) include 'lib/smarty_tiki\block.remarksbox.php';
if (!is_callable('smarty_function_show_help')) include 'lib/smarty_tiki\function.show_help.php';
if (!is_callable('smarty_function_preference')) include 'lib/smarty_tiki\function.preference.php';
if (!is_callable('smarty_function_interactivetranslation')) include 'lib/smarty_tiki\function.interactivetranslation.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\tiki.tpl --><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php if (!empty($_smarty_tpl->getVariable('pageLang',null,true,false)->value)){?><?php echo $_smarty_tpl->getVariable('pageLang')->value;?>
<?php }else{ ?><?php echo $_smarty_tpl->getVariable('prefs')->value['language'];?>
<?php }?>" lang="<?php if (!empty($_smarty_tpl->getVariable('pageLang',null,true,false)->value)){?><?php echo $_smarty_tpl->getVariable('pageLang')->value;?>
<?php }else{ ?><?php echo $_smarty_tpl->getVariable('prefs')->value['language'];?>
<?php }?>"<?php if (!empty($_smarty_tpl->getVariable('page_id',null,true,false)->value)){?> id="page_<?php echo $_smarty_tpl->getVariable('page_id')->value;?>
"<?php }?>>
	<head>
		<?php $_template = new Smarty_Internal_Template('header.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
	</head>
	<body<?php echo smarty_function_html_body_attributes(array(),$_smarty_tpl);?>
>

		<ul class="jumplinks" style="position:absolute;top:-9000px;left:-9000px;z-index:9;">
			<li><a href="#tiki-center" title="Jump to Content">Jump to Content</a></li>
		</ul>

		<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_fullscreen']=='y'&&$_smarty_tpl->getVariable('filegals_manager')->value==''&&$_smarty_tpl->getVariable('print_page')->value!='y'){?>
			<div id="fullscreenbutton">
				<?php if ($_SESSION['fullscreen']=='n'){?>
					<?php $_smarty_tpl->smarty->_tag_stack[] = array('self_link', array('fullscreen'=>"y",'_ajax'=>'n','_icon'=>'application_get','_title'=>"Fullscreen")); $_block_repeat=true; smarty_block_self_link(array('fullscreen'=>"y",'_ajax'=>'n','_icon'=>'application_get','_title'=>"Fullscreen"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_self_link(array('fullscreen'=>"y",'_ajax'=>'n','_icon'=>'application_get','_title'=>"Fullscreen"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

				<?php }else{ ?>
					<?php $_smarty_tpl->smarty->_tag_stack[] = array('self_link', array('fullscreen'=>"n",'_ajax'=>'n','_icon'=>'application_put','_title'=>"Cancel Fullscreen")); $_block_repeat=true; smarty_block_self_link(array('fullscreen'=>"n",'_ajax'=>'n','_icon'=>'application_put','_title'=>"Cancel Fullscreen"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_self_link(array('fullscreen'=>"n",'_ajax'=>'n','_icon'=>'application_put','_title'=>"Cancel Fullscreen"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

				<?php }?>
			</div>
		<?php }?>

		
		<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_tikitests']=='y'&&!empty($_smarty_tpl->getVariable('tikitest_state',null,true,false)->value)&&$_smarty_tpl->getVariable('tikitest_state')->value!=0){?>
			<?php $_template = new Smarty_Internal_Template('tiki-tests_topbar.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
		<?php }?>

		<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_ajax']=='y'){?>
			<?php $_template = new Smarty_Internal_Template('tiki-ajax_header.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
		<?php }?>
		
		<div id="fixedwidth" class="fixedwidth"> 
			<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_layoutshadows']=='y'){?><div id="main-shadow"><?php $_template = new Smarty_Internal_Template('eval:'.$_smarty_tpl->getVariable('prefs')->value['main_shadow_start'], $_smarty_tpl->smarty, $_smarty_tpl);echo $_template->getRenderedTemplate(); ?><?php }?>
			<div id="main">
				<?php if (($_smarty_tpl->getVariable('prefs')->value['feature_fullscreen']!='y'||$_SESSION['fullscreen']!='y')&&($_smarty_tpl->getVariable('prefs')->value['layout_section']!='y'||$_smarty_tpl->getVariable('prefs')->value['feature_top_bar']!='n')){?>
					<?php if ($_smarty_tpl->getVariable('prefs')->value['module_zones_top']=='fixed'||($_smarty_tpl->getVariable('prefs')->value['module_zones_top']!='n'&&count($_smarty_tpl->getVariable('top_modules')->value)>0)){?>
						<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_layoutshadows']=='y'){?><div id="header-shadow"><?php $_template = new Smarty_Internal_Template('eval:'.$_smarty_tpl->getVariable('prefs')->value['header_shadow_start'], $_smarty_tpl->smarty, $_smarty_tpl);echo $_template->getRenderedTemplate(); ?><?php }?>
							<div class="header_outer">
								<div class="header_container">
									<div class="fixedwidth header_fixedwidth">
										<header class="clearfix header" id="header"<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_bidi']=='y'){?> dir="rtl"<?php }?>>
											<div class="content clearfix modules" id="top_modules">
												<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['name'] = 'homeix';
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('top_modules')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total']);
?>
													<?php echo $_smarty_tpl->getVariable('top_modules')->value[$_smarty_tpl->getVariable('smarty')->value['section']['homeix']['index']]['data'];?>

												<?php endfor; endif; ?>
											</div>
										</header>
									</div>	
								</div>
							</div>
						<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_layoutshadows']=='y'){?><?php $_template = new Smarty_Internal_Template('eval:'.$_smarty_tpl->getVariable('prefs')->value['header_shadow_end'], $_smarty_tpl->smarty, $_smarty_tpl);echo $_template->getRenderedTemplate(); ?></div><?php }?>
					<?php }?>
				<?php }?>
				<div class="middle_outer">
					<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_layoutshadows']=='y'){?><div id="middle-shadow"><?php $_template = new Smarty_Internal_Template('eval:'.$_smarty_tpl->getVariable('prefs')->value['middle_shadow_start'], $_smarty_tpl->smarty, $_smarty_tpl);echo $_template->getRenderedTemplate(); ?><?php }?>
						<div class="clearfix fixedwidth middle" id="middle">
							<div class="content clearfix modules" id="topbar_modules">
								<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['name'] = 'homeix';
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('topbar_modules')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total']);
?>
									<?php echo $_smarty_tpl->getVariable('topbar_modules')->value[$_smarty_tpl->getVariable('smarty')->value['section']['homeix']['index']]['data'];?>

								<?php endfor; endif; ?>
							</div>
							<div class="clearfix <?php if ($_smarty_tpl->getVariable('prefs')->value['feature_fullscreen']!='y'||$_SESSION['fullscreen']!='y'){?>nofullscreen<?php }else{ ?>fullscreen<?php }?>" id="c1c2">
								<div class="clearfix" id="wrapper">
									<div id="col1" class="<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_left_column']=='fixed'||($_smarty_tpl->getVariable('prefs')->value['feature_left_column']!='n'&&count($_smarty_tpl->getVariable('left_modules')->value)>0&&$_smarty_tpl->getVariable('show_columns')->value['left_modules']!='n')){?>marginleft<?php }?><?php if ($_smarty_tpl->getVariable('prefs')->value['feature_right_column']=='fixed'||($_smarty_tpl->getVariable('prefs')->value['feature_right_column']!='n'&&count($_smarty_tpl->getVariable('right_modules')->value)>0&&$_smarty_tpl->getVariable('show_columns')->value['right_modules']!='n')){?> marginright<?php }?>"<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_bidi']=='y'){?> dir="rtl"<?php }?>>
									<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_layoutshadows']=='y'){?><div id="tiki-center-shadow"><?php $_template = new Smarty_Internal_Template('eval:'.$_smarty_tpl->getVariable('prefs')->value['center_shadow_start'], $_smarty_tpl->smarty, $_smarty_tpl);echo $_template->getRenderedTemplate(); ?><?php }?>
										<div id="tiki-center"  class="clearfix content">
										<?php if (($_smarty_tpl->getVariable('prefs')->value['feature_fullscreen']!='y'||$_SESSION['fullscreen']!='y')){?>
											<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_left_column']=='user'||$_smarty_tpl->getVariable('prefs')->value['feature_right_column']=='user'){?>
												<div class="clearfix" id="showhide_columns">
													<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_left_column']=='fixed'||($_smarty_tpl->getVariable('prefs')->value['feature_left_column']=='user'&&count($_smarty_tpl->getVariable('left_modules')->value)>0&&$_smarty_tpl->getVariable('show_columns')->value['left_modules']!='n')){?>
														<div style="text-align:left;float:left;">
															<a class="flip" title="Show/Hide Left Column" href="#" onClick="toggleCols('col2','left'); return false"><?php echo smarty_function_icon(array('_name'=>'oleftcol','_id'=>"oleftcol",'class'=>"colflip",'alt'=>"[Show/Hide Left Column]"),$_smarty_tpl);?>
</a>
														</div>
													<?php }?>
													<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_right_column']=='fixed'||($_smarty_tpl->getVariable('prefs')->value['feature_right_column']=='user'&&count($_smarty_tpl->getVariable('right_modules')->value)>0&&$_smarty_tpl->getVariable('show_columns')->value['right_modules']!='n')){?>
														<div class="clearfix" style="text-align:right;float:right">
															<a class="flip" title="Show/Hide Right Column" href="#" onClick="toggleCols('col3','right'); return false"><?php echo smarty_function_icon(array('_name'=>'orightcol','_id'=>"orightcol",'class'=>"colflip",'alt'=>"[Show/Hide Right Column]"),$_smarty_tpl);?>
</a>
														</div>
													<?php }?>
													<br style="clear:both" />
												</div>
											<?php }?>
										<?php }?>
										<?php if ($_smarty_tpl->getVariable('prefs')->value['module_zones_pagetop']=='fixed'||($_smarty_tpl->getVariable('prefs')->value['module_zones_pagetop']!='n'&&count($_smarty_tpl->getVariable('pagetop_modules')->value)>0)){?>
											<div class="content clearfix modules" id="pagetop_modules">
												<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['name'] = 'homeix';
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('pagetop_modules')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total']);
?>
													<?php echo $_smarty_tpl->getVariable('pagetop_modules')->value[$_smarty_tpl->getVariable('smarty')->value['section']['homeix']['index']]['data'];?>

												<?php endfor; endif; ?>
											</div>
										<?php }?>
										<?php if ((isset($_smarty_tpl->getVariable('section',null,true,false)->value)&&$_smarty_tpl->getVariable('section')->value!='share')&&$_smarty_tpl->getVariable('prefs')->value['feature_share']=='y'&&$_smarty_tpl->getVariable('tiki_p_share')->value=='y'&&(!isset($_smarty_tpl->getVariable('edit_page',null,true,false)->value)||$_smarty_tpl->getVariable('edit_page')->value!='y'&&$_smarty_tpl->getVariable('prefs')->value['feature_site_send_link']!='y')){?>
											<div class="share">
												<a title="Share this page" href="tiki-share.php?url=<?php echo smarty_modifier_escape($_SERVER['REQUEST_URI'],'url');?>
">Share this page</a>
											</div>
										<?php }?>
										<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_tell_a_friend']=='y'&&$_smarty_tpl->getVariable('tiki_p_tell_a_friend')->value=='y'&&(!isset($_smarty_tpl->getVariable('edit_page',null,true,false)->value)||$_smarty_tpl->getVariable('edit_page')->value!='y'&&$_smarty_tpl->getVariable('prefs')->value['feature_site_send_link']!='y')){?>
											<div class="tellafriend">
												<a title="Email this page" href="tiki-tell_a_friend.php?url=<?php echo smarty_modifier_escape($_SERVER['REQUEST_URI'],'url');?>
">Email this page</a>
											</div>
										<?php }?>
											<?php echo smarty_function_error_report(array(),$_smarty_tpl);?>

											<?php if ($_smarty_tpl->getVariable('display_msg')->value){?>
												<?php $_smarty_tpl->smarty->_tag_stack[] = array('remarksbox', array('type'=>"note",'title'=>"Notice")); $_block_repeat=true; smarty_block_remarksbox(array('type'=>"note",'title'=>"Notice"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('display_msg')->value);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_remarksbox(array('type'=>"note",'title'=>"Notice"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

											<?php }?>
											<div id="role_main">
												<?php echo $_smarty_tpl->getVariable('mid_data')->value;?>
  
											</div>
											<?php if ($_smarty_tpl->getVariable('prefs')->value['module_zones_pagebottom']=='fixed'||($_smarty_tpl->getVariable('prefs')->value['module_zones_pagebottom']!='n'&&count($_smarty_tpl->getVariable('pagebottom_modules')->value)>0)){?>
												<div class="content clearfix modules" id="pagebottom_modules">
													<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['name'] = 'homeix';
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('pagebottom_modules')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total']);
?>
														<?php echo $_smarty_tpl->getVariable('pagebottom_modules')->value[$_smarty_tpl->getVariable('smarty')->value['section']['homeix']['index']]['data'];?>

													<?php endfor; endif; ?>
												</div>
											<?php }?>
											<?php echo smarty_function_show_help(array(),$_smarty_tpl);?>

										</div>
										<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_layoutshadows']=='y'){?><?php $_template = new Smarty_Internal_Template('eval:'.$_smarty_tpl->getVariable('prefs')->value['center_shadow_end'], $_smarty_tpl->smarty, $_smarty_tpl);echo $_template->getRenderedTemplate(); ?></div><?php }?>
									</div>
								</div>

								<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_fullscreen']!='y'||$_SESSION['fullscreen']!='y'){?>
									<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_left_column']=='fixed'||($_smarty_tpl->getVariable('prefs')->value['feature_left_column']!='n'&&count($_smarty_tpl->getVariable('left_modules')->value)>0&&$_smarty_tpl->getVariable('show_columns')->value['left_modules']!='n')){?>
										<div id="col2"<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_left_column']=='user'){?> style="display:<?php if (isset($_smarty_tpl->getVariable('cookie',null,true,false)->value['show_col2'])&&$_smarty_tpl->getVariable('cookie')->value['show_col2']!='y'){?> none<?php }elseif(isset($_smarty_tpl->getVariable('ie6',null,true,false)->value)){?> block<?php }else{ ?> table-cell<?php }?>;"<?php }?><?php if ($_smarty_tpl->getVariable('prefs')->value['feature_bidi']=='y'){?> dir="rtl"<?php }?>>
											<div id="left_modules" class="content modules">
												<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['name'] = 'homeix';
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('left_modules')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total']);
?>
													<?php echo $_smarty_tpl->getVariable('left_modules')->value[$_smarty_tpl->getVariable('smarty')->value['section']['homeix']['index']]['data'];?>

												<?php endfor; endif; ?>
											</div>
										</div>
									<?php }?>
								<?php }?>
							</div>
							<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_fullscreen']!='y'||$_SESSION['fullscreen']!='y'){?>
								<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_right_column']=='fixed'||($_smarty_tpl->getVariable('prefs')->value['feature_right_column']!='n'&&count($_smarty_tpl->getVariable('right_modules')->value)>0&&$_smarty_tpl->getVariable('show_columns')->value['right_modules']!='n')||$_smarty_tpl->getVariable('module_pref_errors')->value){?>
									<div class="clearfix" id="col3"<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_right_column']=='user'){?> style="display:<?php if (isset($_smarty_tpl->getVariable('cookie',null,true,false)->value['show_col3'])&&$_smarty_tpl->getVariable('cookie')->value['show_col3']!='y'){?> none<?php }elseif(isset($_smarty_tpl->getVariable('ie6',null,true,false)->value)){?> block<?php }else{ ?> table-cell<?php }?>;"<?php }?><?php if ($_smarty_tpl->getVariable('prefs')->value['feature_bidi']=='y'){?> dir="rtl"<?php }?>>
										<div id="right_modules" class="content modules">
											<?php if ($_smarty_tpl->getVariable('module_pref_errors')->value){?>
												<?php $_smarty_tpl->smarty->_tag_stack[] = array('remarksbox', array('type'=>"warning",'title'=>"Module errors")); $_block_repeat=true; smarty_block_remarksbox(array('type'=>"warning",'title'=>"Module errors"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

													The following modules could not be loaded
													<form method="post" action="tiki-admin.php">
														<?php  $_smarty_tpl->tpl_vars['pref_error'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['index'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('module_pref_errors')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['pref_error']->key => $_smarty_tpl->tpl_vars['pref_error']->value){
 $_smarty_tpl->tpl_vars['index']->value = $_smarty_tpl->tpl_vars['pref_error']->key;
?>
															<p><?php echo $_smarty_tpl->tpl_vars['pref_error']->value['mod_name'];?>
:</p>
															<?php echo smarty_function_preference(array('name'=>$_smarty_tpl->tpl_vars['pref_error']->value['pref_name']),$_smarty_tpl);?>

														<?php }} ?>
														<div class="submit">
															<input type="submit" value="Change"/>
														</div>
													</form>
												<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_remarksbox(array('type'=>"warning",'title'=>"Module errors"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

											<?php }?>
											<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['name'] = 'homeix';
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('right_modules')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total']);
?>
												<?php echo $_smarty_tpl->getVariable('right_modules')->value[$_smarty_tpl->getVariable('smarty')->value['section']['homeix']['index']]['data'];?>

											<?php endfor; endif; ?>
										</div>
									</div>
									<br style="clear:both" />
								<?php }?>
							<?php }?>
							<!--[if IE 7]><br style="clear:both; height: 0" /><![endif]-->
						</div>
					<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_layoutshadows']=='y'){?><?php $_template = new Smarty_Internal_Template('eval:'.$_smarty_tpl->getVariable('prefs')->value['middle_shadow_end'], $_smarty_tpl->smarty, $_smarty_tpl);echo $_template->getRenderedTemplate(); ?></div><?php }?>
				</div>
				<?php if (($_smarty_tpl->getVariable('prefs')->value['feature_fullscreen']!='y'||$_SESSION['fullscreen']!='y')&&($_smarty_tpl->getVariable('prefs')->value['layout_section']!='y'||$_smarty_tpl->getVariable('prefs')->value['feature_bot_bar']!='n')){?>
					<?php if ($_smarty_tpl->getVariable('prefs')->value['module_zones_bottom']=='fixed'||($_smarty_tpl->getVariable('prefs')->value['module_zones_bottom']!='n'&&count($_smarty_tpl->getVariable('bottom_modules')->value)>0)){?>
						<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_layoutshadows']=='y'){?><div id="footer-shadow"><?php $_template = new Smarty_Internal_Template('eval:'.$_smarty_tpl->getVariable('prefs')->value['footer_shadow_start'], $_smarty_tpl->smarty, $_smarty_tpl);echo $_template->getRenderedTemplate(); ?><?php }?>
							<footer class="footer" id="footer">
								<div class="footer_liner">
									<div class="fixedwidth footerbgtrap">
										<div id="bottom_modules" class="content modules"<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_bidi']=='y'){?> dir="rtl"<?php }?>>
											<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['name'] = 'homeix';
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('bottom_modules')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['homeix']['total']);
?>
												<?php echo $_smarty_tpl->getVariable('bottom_modules')->value[$_smarty_tpl->getVariable('smarty')->value['section']['homeix']['index']]['data'];?>

											<?php endfor; endif; ?>
										</div>
									</div>
								</div>
							</footer>
						<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_layoutshadows']=='y'){?><?php $_template = new Smarty_Internal_Template('eval:'.$_smarty_tpl->getVariable('prefs')->value['footer_shadow_end'], $_smarty_tpl->smarty, $_smarty_tpl);echo $_template->getRenderedTemplate(); ?></div><?php }?>
					<?php }?>
				<?php }?>
			</div><?php if ($_smarty_tpl->getVariable('prefs')->value['feature_layoutshadows']=='y'){?><?php $_template = new Smarty_Internal_Template('eval:'.$_smarty_tpl->getVariable('prefs')->value['main_shadow_end'], $_smarty_tpl->smarty, $_smarty_tpl);echo $_template->getRenderedTemplate(); ?></div><?php }?>
		</div> 

		<?php $_template = new Smarty_Internal_Template('footer.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
		<?php if (isset($_smarty_tpl->getVariable('prefs',null,true,false)->value['socialnetworks_user_firstlogin'])&&$_smarty_tpl->getVariable('prefs')->value['socialnetworks_user_firstlogin']=='y'){?>
			<?php $_template = new Smarty_Internal_Template('tiki-socialnetworks_firstlogin_launcher.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
		<?php }?>

		<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_endbody_code']){?>
			<?php $_template = new Smarty_Internal_Template('eval:'.$_smarty_tpl->getVariable('prefs')->value['feature_endbody_code'], $_smarty_tpl->smarty, $_smarty_tpl);echo $_template->getRenderedTemplate(); ?>
		<?php }?>
		<?php echo smarty_function_interactivetranslation(array(),$_smarty_tpl);?>

		<!-- Put JS at the end -->
		<?php if ($_smarty_tpl->getVariable('headerlib')->value){?>
			<?php echo $_smarty_tpl->getVariable('headerlib')->value->output_js_config();?>

			<?php echo $_smarty_tpl->getVariable('headerlib')->value->output_js_files();?>

			<?php echo $_smarty_tpl->getVariable('headerlib')->value->output_js();?>

		<?php }?>
	</body>
</html>
<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\tiki.tpl -->