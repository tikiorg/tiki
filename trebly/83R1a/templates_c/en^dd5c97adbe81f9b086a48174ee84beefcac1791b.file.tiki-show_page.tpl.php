<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:26:57
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\tiki-show_page.tpl" */ ?>
<?php /*%%SmartyHeaderCode:265484f1e08e16eb6e7-24422502%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dd5c97adbe81f9b086a48174ee84beefcac1791b' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\tiki-show_page.tpl',
      1 => 1314088240,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '265484f1e08e16eb6e7-24422502',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_breadcrumbs')) include 'lib/smarty_tiki\function.breadcrumbs.php';
if (!is_callable('smarty_block_remarksbox')) include 'lib/smarty_tiki\block.remarksbox.php';
if (!is_callable('smarty_modifier_escape')) include 'lib/smarty_tiki\modifier.escape.php';
if (!is_callable('smarty_function_icon')) include 'lib/smarty_tiki\function.icon.php';
if (!is_callable('smarty_modifier_sefurl')) include 'lib/smarty_tiki\modifier.sefurl.php';
if (!is_callable('smarty_block_tr')) include 'lib/smarty_tiki\block.tr.php';
if (!is_callable('smarty_function_rating')) include 'lib/smarty_tiki\function.rating.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\tiki-show_page.tpl --> 
<?php if (!isset($_smarty_tpl->getVariable('hide_page_header',null,true,false)->value)||!$_smarty_tpl->getVariable('hide_page_header')->value){?>
	<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_siteloc']=='page'&&$_smarty_tpl->getVariable('prefs')->value['feature_breadcrumbs']=='y'){?>
		<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_siteloclabel']=='y'){?>Location : <?php }?>
		<?php echo smarty_function_breadcrumbs(array('type'=>"trail",'loc'=>"page",'crumbs'=>$_smarty_tpl->getVariable('crumbs')->value),$_smarty_tpl);?>

		<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_page_title']=='y'){?>
			<?php echo smarty_function_breadcrumbs(array('type'=>"pagetitle",'loc'=>"page",'crumbs'=>$_smarty_tpl->getVariable('crumbs')->value,'machine_translate'=>$_smarty_tpl->getVariable('machine_translate_to_lang')->value,'source_lang'=>$_smarty_tpl->getVariable('pageLang')->value,'target_lang'=>$_smarty_tpl->getVariable('machine_translate_to_lang')->value),$_smarty_tpl);?>

		<?php }?>
	<?php }?>

<?php $_template = new Smarty_Internal_Template('tiki-flaggedrev_approval_header.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>

<?php }?> 

<?php if (!$_smarty_tpl->getVariable('prefs')->value['wiki_topline_position']||$_smarty_tpl->getVariable('prefs')->value['wiki_topline_position']=='top'||$_smarty_tpl->getVariable('prefs')->value['wiki_topline_position']=='both'){?>
	<?php $_template = new Smarty_Internal_Template('tiki-wiki_topline.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
<?php }?>

<?php if ($_smarty_tpl->getVariable('print_page')->value!='y'){?>
	<?php if ($_smarty_tpl->getVariable('prefs')->value['page_bar_position']=='top'){?>
		<?php $_template = new Smarty_Internal_Template('tiki-page_bar.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
	<?php }?>
<?php }?>

<?php if (isset($_smarty_tpl->getVariable('saved_msg',null,true,false)->value)&&$_smarty_tpl->getVariable('saved_msg')->value!=''){?>
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('remarksbox', array('type'=>"note",'title'=>"Note")); $_block_repeat=true; smarty_block_remarksbox(array('type'=>"note",'title'=>"Note"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->getVariable('saved_msg')->value;?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_remarksbox(array('type'=>"note",'title'=>"Note"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php }?>

<?php if (isset($_smarty_tpl->getVariable('user',null,true,false)->value)&&$_smarty_tpl->getVariable('user')->value&&$_smarty_tpl->getVariable('prefs')->value['feature_user_watches']=='y'&&$_smarty_tpl->getVariable('category_watched')->value=='y'){?>
	<div class="categbar" style="clear: both; text-align: right">
		Watched by categories:
		<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('watching_categories')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
			<a href="tiki-browse_categories.php?parentId=<?php echo $_smarty_tpl->getVariable('watching_categories')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['categId'];?>
"><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('watching_categories')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['name']);?>
</a>&nbsp;
		<?php endfor; endif; ?>
	</div>
<?php }?>

<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_urgent_translation']=='y'){?>
	<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('translation_alert')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
		<div class="cbox">
			<div class="cbox-title">
				<?php echo smarty_function_icon(array('_id'=>'information','style'=>"vertical-align:middle"),$_smarty_tpl);?>
 Content may be out of date
			</div>
			<div class="cbox-data">
				<p>
					An urgent request for translation has been sent. Until this page is updated, you can see a corrected version in the following pages:
				</p>
				<ul>
					<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['j']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['j']['name'] = 'j';
$_smarty_tpl->tpl_vars['smarty']->value['section']['j']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('translation_alert')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['j']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['j']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['j']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['j']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['j']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['j']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['j']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['j']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['j']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['j']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['j']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['j']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['j']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['j']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['j']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['j']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['j']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['j']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['j']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['j']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['j']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['j']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['j']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['j']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['j']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['j']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['j']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['j']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['j']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['j']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['j']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['j']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['j']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['j']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['j']['total']);
?>
						<li>
							<a href="<?php echo smarty_modifier_sefurl($_smarty_tpl->getVariable('translation_alert')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['j']['index']]['page'],'wiki','with_next');?>
no_bl=y">
								<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('translation_alert')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['j']['index']]['page']);?>

							</a>
							(<?php echo $_smarty_tpl->getVariable('translation_alert')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['j']['index']]['lang'];?>
)
							<?php if ($_smarty_tpl->getVariable('editable')->value&&($_smarty_tpl->getVariable('tiki_p_edit')->value=='y'||((mb_detect_encoding($_smarty_tpl->getVariable('page')->value, 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtolower($_smarty_tpl->getVariable('page')->value,SMARTY_RESOURCE_CHAR_SET) : strtolower($_smarty_tpl->getVariable('page')->value))=='sandbox')&&$_smarty_tpl->getVariable('beingEdited')->value!='y'){?> 
								<a href="tiki-editpage.php?page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,'url');?>
&amp;source_page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('translation_alert')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['j']['index']]['page'],'url');?>
&amp;oldver=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('translation_alert')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['j']['index']]['last_update'],'url');?>
&amp;newver=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('translation_alert')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']][$_smarty_tpl->getVariable('smarty')->value['section']['j']['index']]['current_version'],'url');?>
&amp;diff_style=htmldiff" title="update from it">
									<?php echo smarty_function_icon(array('_id'=>'arrow_refresh','alt'=>"update from it",'style'=>"vertical-align:middle"),$_smarty_tpl);?>

								</a>
							<?php }?>
						</li>
					<?php endfor; endif; ?>
				</ul>
			</div>
		</div>
	<?php endfor; endif; ?>
<?php }?>

<article id="top" class="wikitext clearfix<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_page_title']!='y'){?> nopagetitle<?php }?>">
	<?php if (!isset($_smarty_tpl->getVariable('hide_page_header',null,true,false)->value)||!$_smarty_tpl->getVariable('hide_page_header')->value){?>
		<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_freetags']=='y'&&$_smarty_tpl->getVariable('tiki_p_view_freetags')->value=='y'&&isset($_smarty_tpl->getVariable('freetags',null,true,false)->value['data'][0])&&$_smarty_tpl->getVariable('prefs')->value['freetags_show_middle']=='y'){?>
			<?php $_template = new Smarty_Internal_Template('freetag_list.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
		<?php }?>

		<?php if ($_smarty_tpl->getVariable('pages')->value>1&&$_smarty_tpl->getVariable('prefs')->value['wiki_page_navigation_bar']!='bottom'){?>
			<div class="center navigation_bar pagination position_top">
				<a href="tiki-index.php?<?php if ($_smarty_tpl->getVariable('page_info')->value){?>page_ref_id=<?php echo $_smarty_tpl->getVariable('page_info')->value['page_ref_id'];?>
<?php }else{ ?>page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,"url");?>
<?php }?>&amp;pagenum=<?php echo $_smarty_tpl->getVariable('first_page')->value;?>
"><?php echo smarty_function_icon(array('_id'=>'resultset_first','alt'=>"First page"),$_smarty_tpl);?>
</a>

				<a href="tiki-index.php?<?php if ($_smarty_tpl->getVariable('page_info')->value){?>page_ref_id=<?php echo $_smarty_tpl->getVariable('page_info')->value['page_ref_id'];?>
<?php }else{ ?>page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,"url");?>
<?php }?>&amp;pagenum=<?php echo $_smarty_tpl->getVariable('prev_page')->value;?>
"><?php echo smarty_function_icon(array('_id'=>'resultset_previous','alt'=>"Previous page"),$_smarty_tpl);?>
</a>

				<small><?php $_smarty_tpl->smarty->_tag_stack[] = array('tr', array('_0'=>$_smarty_tpl->getVariable('pagenum')->value,'_1'=>$_smarty_tpl->getVariable('pages')->value)); $_block_repeat=true; smarty_block_tr(array('_0'=>$_smarty_tpl->getVariable('pagenum')->value,'_1'=>$_smarty_tpl->getVariable('pages')->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
page: %0/%1<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tr(array('_0'=>$_smarty_tpl->getVariable('pagenum')->value,'_1'=>$_smarty_tpl->getVariable('pages')->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</small>

				<a href="tiki-index.php?<?php if ($_smarty_tpl->getVariable('page_info')->value){?>page_ref_id=<?php echo $_smarty_tpl->getVariable('page_info')->value['page_ref_id'];?>
<?php }else{ ?>page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,"url");?>
<?php }?>&amp;pagenum=<?php echo $_smarty_tpl->getVariable('next_page')->value;?>
"><?php echo smarty_function_icon(array('_id'=>'resultset_next','alt'=>"Next page"),$_smarty_tpl);?>
</a>

				<a href="tiki-index.php?<?php if ($_smarty_tpl->getVariable('page_info')->value){?>page_ref_id=<?php echo $_smarty_tpl->getVariable('page_info')->value['page_ref_id'];?>
<?php }else{ ?>page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,"url");?>
<?php }?>&amp;pagenum=<?php echo $_smarty_tpl->getVariable('last_page')->value;?>
"><?php echo smarty_function_icon(array('_id'=>'resultset_last','alt'=>"Last page"),$_smarty_tpl);?>
</a>
			</div>
		<?php }?>

		<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_page_title']=='y'){?>
			<h1 class="pagetitle"><?php echo smarty_function_breadcrumbs(array('type'=>"pagetitle",'loc'=>"page",'crumbs'=>$_smarty_tpl->getVariable('crumbs')->value,'machine_translate'=>$_smarty_tpl->getVariable('machine_translate_to_lang')->value,'source_lang'=>$_smarty_tpl->getVariable('pageLang')->value,'target_lang'=>$_smarty_tpl->getVariable('machine_translate_to_lang')->value),$_smarty_tpl);?>
</h1>
		<?php }?>

		<?php if ($_smarty_tpl->getVariable('structure')->value=='y'&&($_smarty_tpl->getVariable('prefs')->value['wiki_structure_bar_position']!='bottom')){?>
			<?php $_template = new Smarty_Internal_Template('tiki-wiki_structure_bar.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
		<?php }?>

		<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_wiki_ratings']=='y'){?>
			<?php $_template = new Smarty_Internal_Template('poll.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
		<?php }?>

		<?php if ($_smarty_tpl->getVariable('prefs')->value['wiki_simple_ratings']=='y'&&$_smarty_tpl->getVariable('tiki_p_assign_perm_wiki_page')->value=='y'){?>
			<form method="post" action="">
				<?php echo smarty_function_rating(array('type'=>"wiki page",'id'=>$_smarty_tpl->getVariable('page_id')->value),$_smarty_tpl);?>

			</form>
		<?php }?>
	<?php }?> 

	<?php if ($_smarty_tpl->getVariable('machine_translate_to_lang')->value!=''){?>
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('remarksbox', array('type'=>"warning",'title'=>"Warning",'highlight'=>"y")); $_block_repeat=true; smarty_block_remarksbox(array('type'=>"warning",'title'=>"Warning",'highlight'=>"y"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

			This text was automatically translated by Google Translate from the following page: <a href="tiki-index.php?page=<?php echo $_smarty_tpl->getVariable('page')->value;?>
"><?php echo $_smarty_tpl->getVariable('page')->value;?>
</a>
		<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_remarksbox(array('type'=>"warning",'title'=>"Warning",'highlight'=>"y"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

	<?php }?>

	<?php if (isset($_smarty_tpl->getVariable('pageLang',null,true,false)->value)&&($_smarty_tpl->getVariable('pageLang')->value=='ar'||$_smarty_tpl->getVariable('pageLang')->value=='he')){?>
		<div style="direction:RTL; unicode-bidi:embed; text-align: right; <?php if ($_smarty_tpl->getVariable('pageLang')->value=='ar'){?>font-size: large;<?php }?>">
			<?php echo $_smarty_tpl->getVariable('parsed')->value;?>

		</div>
	<?php }else{ ?>
		<?php echo $_smarty_tpl->getVariable('parsed')->value;?>

	<?php }?>

	
	<hr class="hrwikibottom" /> 

	<?php if ($_smarty_tpl->getVariable('structure')->value=='y'&&(($_smarty_tpl->getVariable('prefs')->value['wiki_structure_bar_position']=='bottom')||($_smarty_tpl->getVariable('prefs')->value['wiki_structure_bar_position']=='both'))){?>
		<?php $_template = new Smarty_Internal_Template('tiki-wiki_structure_bar.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
	<?php }?>

	<?php if ($_smarty_tpl->getVariable('pages')->value>1&&$_smarty_tpl->getVariable('prefs')->value['wiki_page_navigation_bar']!='top'){?>
		<br />
		<div class="center navigation_bar pagination position_bottom">
			<a href="tiki-index.php?<?php if ($_smarty_tpl->getVariable('page_info')->value){?>page_ref_id=<?php echo $_smarty_tpl->getVariable('page_info')->value['page_ref_id'];?>
<?php }else{ ?>page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,"url");?>
<?php }?>&amp;pagenum=<?php echo $_smarty_tpl->getVariable('first_page')->value;?>
"><?php echo smarty_function_icon(array('_id'=>'resultset_first','alt'=>"First page"),$_smarty_tpl);?>
</a>

			<a href="tiki-index.php?<?php if ($_smarty_tpl->getVariable('page_info')->value){?>page_ref_id=<?php echo $_smarty_tpl->getVariable('page_info')->value['page_ref_id'];?>
<?php }else{ ?>page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,"url");?>
<?php }?>&amp;pagenum=<?php echo $_smarty_tpl->getVariable('prev_page')->value;?>
"><?php echo smarty_function_icon(array('_id'=>'resultset_previous','alt'=>"Previous page"),$_smarty_tpl);?>
</a>

			<small><?php $_smarty_tpl->smarty->_tag_stack[] = array('tr', array('_0'=>$_smarty_tpl->getVariable('pagenum')->value,'_1'=>$_smarty_tpl->getVariable('pages')->value)); $_block_repeat=true; smarty_block_tr(array('_0'=>$_smarty_tpl->getVariable('pagenum')->value,'_1'=>$_smarty_tpl->getVariable('pages')->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
page: %0/%1<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tr(array('_0'=>$_smarty_tpl->getVariable('pagenum')->value,'_1'=>$_smarty_tpl->getVariable('pages')->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</small>

			<a href="tiki-index.php?<?php if ($_smarty_tpl->getVariable('page_info')->value){?>page_ref_id=<?php echo $_smarty_tpl->getVariable('page_info')->value['page_ref_id'];?>
<?php }else{ ?>page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,"url");?>
<?php }?>&amp;pagenum=<?php echo $_smarty_tpl->getVariable('next_page')->value;?>
"><?php echo smarty_function_icon(array('_id'=>'resultset_next','alt'=>"Next page"),$_smarty_tpl);?>
</a>

			<a href="tiki-index.php?<?php if ($_smarty_tpl->getVariable('page_info')->value){?>page_ref_id=<?php echo $_smarty_tpl->getVariable('page_info')->value['page_ref_id'];?>
<?php }else{ ?>page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,"url");?>
<?php }?>&amp;pagenum=<?php echo $_smarty_tpl->getVariable('last_page')->value;?>
"><?php echo smarty_function_icon(array('_id'=>'resultset_last','alt'=>"Last page"),$_smarty_tpl);?>
</a>
		</div>
	<?php }?>
</article> 

<?php if ($_smarty_tpl->getVariable('has_footnote')->value=='y'){?>
	<div class="wikitext" id="wikifootnote"><?php echo $_smarty_tpl->getVariable('footnote')->value;?>
</div>
<?php }?>

<p class="editdate">
	<?php if (isset($_smarty_tpl->getVariable('wiki_authors_style',null,true,false)->value)&&$_smarty_tpl->getVariable('wiki_authors_style')->value!='none'){?>
		<?php $_template = new Smarty_Internal_Template('wiki_authors.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
	<?php }?>

	<?php $_template = new Smarty_Internal_Template('show_copyright.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>

	<?php if ($_smarty_tpl->getVariable('print_page')->value=='y'){?>
		<br />
		<?php ob_start(); ?><?php echo $_smarty_tpl->getVariable('base_url')->value;?>
<?php echo smarty_modifier_sefurl($_smarty_tpl->getVariable('page')->value);?>
<?php if (!empty($_REQUEST['itemId'])){?>&amp;itemId=<?php echo $_REQUEST['itemId'];?>
<?php }?><?php  Smarty::$_smarty_vars['capture']['url']=ob_get_clean();?>
		The original document is available at <a href="<?php echo Smarty::$_smarty_vars['capture']['url'];?>
"><?php echo Smarty::$_smarty_vars['capture']['url'];?>
</a>
	<?php }?>
</p>

<?php if ($_smarty_tpl->getVariable('is_categorized')->value=='y'&&$_smarty_tpl->getVariable('prefs')->value['feature_categories']=='y'&&$_smarty_tpl->getVariable('prefs')->value['feature_categoryobjects']=='y'){?>
	<?php echo $_smarty_tpl->getVariable('display_catobjects')->value;?>

<?php }?>
<?php if ($_smarty_tpl->getVariable('is_categorized')->value=='y'&&$_smarty_tpl->getVariable('prefs')->value['feature_categories']=='y'&&$_smarty_tpl->getVariable('prefs')->value['category_morelikethis_algorithm']!=''){?>
	<?php $_template = new Smarty_Internal_Template('category_related_objects.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
<?php }?>

<?php if ($_smarty_tpl->getVariable('prefs')->value['wiki_topline_position']=='bottom'||$_smarty_tpl->getVariable('prefs')->value['wiki_topline_position']=='both'){?>
	<?php $_template = new Smarty_Internal_Template('tiki-wiki_topline.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
<?php }?>

<?php if ($_smarty_tpl->getVariable('print_page')->value!='y'){?>
	<?php if ((!$_smarty_tpl->getVariable('prefs')->value['page_bar_position']||$_smarty_tpl->getVariable('prefs')->value['page_bar_position']=='bottom'||$_smarty_tpl->getVariable('prefs')->value['page_bar_position']=='both')&&$_smarty_tpl->getVariable('machine_translate_to_lang')->value==''){?>
		<?php $_template = new Smarty_Internal_Template('tiki-page_bar.tpl', $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
	<?php }?>
<?php }?>
<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\tiki-show_page.tpl -->