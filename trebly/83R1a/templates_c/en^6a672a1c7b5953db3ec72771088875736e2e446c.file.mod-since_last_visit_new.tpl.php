<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:07
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-since_last_visit_new.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1394f1e08eb3c8d83-05566327%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6a672a1c7b5953db3ec72771088875736e2e446c' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\modules/mod-since_last_visit_new.tpl',
      1 => 1313059894,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1394f1e08eb3c8d83-05566327',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_block_tikimodule')) include 'lib/smarty_tiki\block.tikimodule.php';
if (!is_callable('smarty_modifier_tiki_short_date')) include 'lib/smarty_tiki\modifier.tiki_short_date.php';
if (!is_callable('smarty_modifier_escape')) include 'lib/smarty_tiki\modifier.escape.php';
if (!is_callable('smarty_block_jq')) include 'lib/smarty_tiki\block.jq.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-since_last_visit_new.tpl -->
<?php if ($_smarty_tpl->getVariable('user')->value){?>
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('tikimodule', array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>"since_last_visit_new",'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle'])); $_block_repeat=true; smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>"since_last_visit_new",'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	<div style="margin-bottom: 5px; text-align:center;">
		<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_calendar']=='y'&&$_smarty_tpl->getVariable('date_as_link')->value=='y'){?>
			<a class="linkmodule" href="tiki-calendar.php?todate=<?php echo $_smarty_tpl->getVariable('slvn_info')->value['lastLogin'];?>
" title="click to edit">
		<?php }?>
		<b><?php echo smarty_modifier_tiki_short_date($_smarty_tpl->getVariable('slvn_info')->value['lastLogin']);?>
</b>
		<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_calendar']=='y'){?>
			</a>
		<?php }?>
	</div>
	<?php if ($_smarty_tpl->getVariable('slvn_info')->value['cant']==0){?>
		<div class="separator">Nothing has changed</div>
	<?php }else{ ?>
		<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_jquery_ui']=="y"&&$_smarty_tpl->getVariable('use_jquery_ui')->value=="y"){?>
			<?php $_smarty_tpl->tpl_vars['fragment'] = new Smarty_variable(1, null, null);?>
			<div id="mytabs">
	  		<ul>
				<?php  $_smarty_tpl->tpl_vars['slvn_item'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['pos'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('slvn_info')->value['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['slvn_item']->key => $_smarty_tpl->tpl_vars['slvn_item']->value){
 $_smarty_tpl->tpl_vars['pos']->value = $_smarty_tpl->tpl_vars['slvn_item']->key;
?>
					<?php if ($_smarty_tpl->tpl_vars['slvn_item']->value['count']>0){?>
						<li>
							<a href="#fragment-<?php echo $_smarty_tpl->getVariable('fragment')->value;?>
">
								<?php if ($_smarty_tpl->tpl_vars['pos']->value=="blogs"||$_smarty_tpl->tpl_vars['pos']->value=="blogPosts"){?>
									<img src="pics/large/blogs.png" alt="Blogs" title="Blogs"/>
								<?php }elseif($_smarty_tpl->tpl_vars['pos']->value=="articles"){?>
									<img src="pics/large/stock_bold.png" alt="Articles" title="Articles"/>
								<?php }elseif($_smarty_tpl->tpl_vars['pos']->value=="posts"){?>
									<img src="pics/large/stock_index.png" alt="Forums" title="Forums"/>
								<?php }elseif($_smarty_tpl->tpl_vars['pos']->value=="fileGalleries"||$_smarty_tpl->tpl_vars['pos']->value=="files"){?>
									<img src="pics/large/file-manager.png" alt="File Gallery" title="File Gallery"/>
								<?php }elseif($_smarty_tpl->tpl_vars['pos']->value=="poll"){?>
									<img src="pics/large/stock_missing-image.png" alt="Poll" title="Poll"/>
								<?php }elseif($_smarty_tpl->tpl_vars['pos']->value=="pages"){?>
									<img src="pics/large/wikipages.png" alt="Wiki" title="Wiki"/>
								<?php }elseif($_smarty_tpl->tpl_vars['pos']->value=="comments"){?>
									<img src="pics/large/comments.png" alt="Comments" title="Comments"/>
								<?php }elseif($_smarty_tpl->tpl_vars['pos']->value=="forums"){?>
									<img src="pics/large/stock_index.png" alt="Forums" title="Forums"/>
								<?php }elseif($_smarty_tpl->tpl_vars['pos']->value=="trackers"){?>
									<img src="pics/large/gnome-settings-font.png" alt="Trackers" title="Trackers"/>
								<?php }elseif($_smarty_tpl->tpl_vars['pos']->value=="users"){?>
									<img src="pics/large/vcard.png" alt="Users" title="Users"/>
								<?php }else{ ?>
									<?php echo $_smarty_tpl->tpl_vars['pos']->value;?>

								<?php }?>
							</a>
						</li>
					<?php $_smarty_tpl->tpl_vars['fragment'] = new Smarty_variable($_smarty_tpl->getVariable('fragment')->value+1, null, null);?>
					<?php }?>
				<?php }} ?>
			</ul>
			<?php $_smarty_tpl->tpl_vars['fragment'] = new Smarty_variable(1, null, null);?>
		<?php }?>
		<?php  $_smarty_tpl->tpl_vars['slvn_item'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['pos'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('slvn_info')->value['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['slvn_item']->key => $_smarty_tpl->tpl_vars['slvn_item']->value){
 $_smarty_tpl->tpl_vars['pos']->value = $_smarty_tpl->tpl_vars['slvn_item']->key;
?>
			<?php if ($_smarty_tpl->tpl_vars['slvn_item']->value['count']>0){?>
				<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_jquery_ui']=="y"&&$_smarty_tpl->getVariable('use_jquery_ui')->value=="y"){?><div id="fragment-<?php echo $_smarty_tpl->getVariable('fragment')->value;?>
"><?php }?>
				<?php $_smarty_tpl->tpl_vars['cname'] = new Smarty_variable($_smarty_tpl->tpl_vars['slvn_item']->value['cname'], null, null);?>
				<?php if ($_smarty_tpl->tpl_vars['slvn_item']->value['count']==$_smarty_tpl->getVariable('module_rows')->value){?>
					<div class="separator"><a class="separator" href="javascript:flip('<?php echo $_smarty_tpl->getVariable('cname')->value;?>
');">Multiple <?php echo $_smarty_tpl->tpl_vars['slvn_item']->value['label'];?>
, including</a></div>
				<?php }else{ ?>
					<div class="separator"><a class="separator" href="javascript:flip('<?php echo $_smarty_tpl->getVariable('cname')->value;?>
');"><?php echo $_smarty_tpl->tpl_vars['slvn_item']->value['count'];?>
&nbsp;<?php echo $_smarty_tpl->tpl_vars['slvn_item']->value['label'];?>
</a></div>
				<?php }?>
				<?php $_smarty_tpl->tpl_vars['showcname'] = new Smarty_variable(("show_").($_smarty_tpl->getVariable('cname')->value), null, null);?>
	
	        	<?php if ($_smarty_tpl->tpl_vars['pos']->value=='trackers'||$_smarty_tpl->tpl_vars['pos']->value=='utrackers'){?>
					<div id="<?php echo $_smarty_tpl->getVariable('cname')->value;?>
" style="display:<?php if (!isset($_smarty_tpl->getVariable('cookie',null,true,false)->value[$_smarty_tpl->getVariable('showcname',null,true,false)->value])||$_smarty_tpl->getVariable('cookie')->value[$_smarty_tpl->getVariable('showcname')->value]=='y'){?><?php echo $_smarty_tpl->getVariable('default_folding')->value;?>
<?php }else{ ?><?php echo $_smarty_tpl->getVariable('opposite_folding')->value;?>
<?php }?>;">
	
	        			
					 	<?php  $_smarty_tpl->tpl_vars['tracker'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['tp'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['slvn_item']->value['tid']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['tracker']->key => $_smarty_tpl->tpl_vars['tracker']->value){
 $_smarty_tpl->tpl_vars['tp']->value = $_smarty_tpl->tpl_vars['tracker']->key;
?>
					 		<?php $_smarty_tpl->tpl_vars['tcname'] = new Smarty_variable($_smarty_tpl->tpl_vars['tracker']->value['cname'], null, null);?>
					 		<div class="separator" style="margin-left: 10px; display:<?php if (!isset($_smarty_tpl->getVariable('cookie',null,true,false)->value[$_smarty_tpl->getVariable('showcname',null,true,false)->value])||$_smarty_tpl->getVariable('cookie')->value[$_smarty_tpl->getVariable('showcname')->value]=='y'){?><?php echo $_smarty_tpl->getVariable('default_folding')->value;?>
<?php }else{ ?><?php echo $_smarty_tpl->getVariable('opposite_folding')->value;?>
<?php }?>;">
					 			<?php $_smarty_tpl->tpl_vars['showtcname'] = new Smarty_variable(("show_").($_smarty_tpl->getVariable('tcname')->value), null, null);?>
					 			<a class="separator" href="javascript:flip('<?php echo $_smarty_tpl->getVariable('tcname')->value;?>
');"><?php echo $_smarty_tpl->tpl_vars['tracker']->value['count'];?>
&nbsp;<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['tracker']->value['label']);?>
</a>
					 			<div id="<?php echo $_smarty_tpl->getVariable('tcname')->value;?>
" style="display:<?php if (!isset($_smarty_tpl->getVariable('cookie',null,true,false)->value[$_smarty_tpl->getVariable('showtcname',null,true,false)->value])||$_smarty_tpl->getVariable('cookie')->value[$_smarty_tpl->getVariable('showtcname')->value]=='y'){?><?php echo $_smarty_tpl->getVariable('default_folding')->value;?>
<?php }else{ ?><?php echo $_smarty_tpl->getVariable('opposite_folding')->value;?>
<?php }?>;">
					 				<?php if ($_smarty_tpl->getVariable('nonums')->value!='y'){?><ol><?php }else{ ?><ul><?php }?>
					 				<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['xx']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['name'] = 'xx';
$_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['tracker']->value['list']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['xx']['total']);
?>
					 					<li><a  class="linkmodule"
					 								href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['tracker']->value['list'][$_smarty_tpl->getVariable('smarty')->value['section']['xx']['index']]['href']);?>
"
					 								title="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['tracker']->value['list'][$_smarty_tpl->getVariable('smarty')->value['section']['xx']['index']]['title']);?>
"><?php if ($_smarty_tpl->tpl_vars['tracker']->value['list'][$_smarty_tpl->getVariable('smarty')->value['section']['xx']['index']]['label']==''){?>-<?php }else{ ?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['tracker']->value['list'][$_smarty_tpl->getVariable('smarty')->value['section']['xx']['index']]['label']);?>
<?php }?>
					 							</a>
					 					</li>
					 				<?php endfor; endif; ?>
					 				<?php if ($_smarty_tpl->getVariable('nonums')->value!='y'){?></ol><?php }else{ ?></ul><?php }?>
					 			</div>
					 		</div>
					 	<?php }} ?>
	        		   
					</div>
				<?php }else{ ?>
					 <div id="<?php echo $_smarty_tpl->getVariable('cname')->value;?>
" style="display:<?php if (!isset($_smarty_tpl->getVariable('cookie',null,true,false)->value[$_smarty_tpl->getVariable('showcname',null,true,false)->value])||$_smarty_tpl->getVariable('cookie')->value[$_smarty_tpl->getVariable('showcname')->value]=='y'){?><?php echo $_smarty_tpl->getVariable('default_folding')->value;?>
<?php }else{ ?><?php echo $_smarty_tpl->getVariable('opposite_folding')->value;?>
<?php }?>;">
						<?php if ($_smarty_tpl->getVariable('nonums')->value!='y'){?><ol><?php }else{ ?><ul><?php }?>
						<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['ix']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['name'] = 'ix';
$_smarty_tpl->tpl_vars['smarty']->value['section']['ix']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['slvn_item']->value['list']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
							<li>
								<a  class="linkmodule" 
									href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['slvn_item']->value['list'][$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['href']);?>
"
									title="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['slvn_item']->value['list'][$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['title']);?>
">
									<?php if ($_smarty_tpl->tpl_vars['slvn_item']->value['list'][$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['label']==''){?>-<?php }else{ ?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['slvn_item']->value['list'][$_smarty_tpl->getVariable('smarty')->value['section']['ix']['index']]['label']);?>
<?php }?>
								</a>
							</li>
						<?php endfor; endif; ?>
						<?php if ($_smarty_tpl->getVariable('nonums')->value!='y'){?></ol><?php }else{ ?></ul><?php }?>
					</div>
				<?php }?>
				<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_jquery_ui']=="y"&&$_smarty_tpl->getVariable('use_jquery_ui')->value=="y"){?>
					</div>
	           <?php $_smarty_tpl->tpl_vars['fragment'] = new Smarty_variable($_smarty_tpl->getVariable('fragment')->value+1, null, null);?>
				<?php }?>
			<?php }?>
		<?php }} ?>
		<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_jquery_ui']=="y"&&$_smarty_tpl->getVariable('use_jquery_ui')->value=="y"){?></div><?php }?>
	<?php }?>
	<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_jquery_ui']=="y"&&$_smarty_tpl->getVariable('use_jquery_ui')->value=="y"){?><?php $_smarty_tpl->smarty->_tag_stack[] = array('jq', array()); $_block_repeat=true; smarty_block_jq(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
 $(function() {$("#mytabs").tabs({});}); <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_jq(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }?>
	<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>"since_last_visit_new",'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php }?>
<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-since_last_visit_new.tpl -->