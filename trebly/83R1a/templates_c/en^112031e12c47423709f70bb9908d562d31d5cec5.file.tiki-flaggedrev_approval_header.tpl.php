<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:26:58
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\tiki-flaggedrev_approval_header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:122534f1e08e2715133-05165470%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '112031e12c47423709f70bb9908d562d31d5cec5' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\tiki-flaggedrev_approval_header.tpl',
      1 => 1302743604,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '122534f1e08e2715133-05165470',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_block_remarksbox')) include 'lib/smarty_tiki\block.remarksbox.php';
if (!is_callable('smarty_block_self_link')) include 'lib/smarty_tiki\block.self_link.php';
if (!is_callable('smarty_modifier_sefurl')) include 'lib/smarty_tiki\modifier.sefurl.php';
if (!is_callable('smarty_modifier_escape')) include 'lib/smarty_tiki\modifier.escape.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\tiki-flaggedrev_approval_header.tpl -->
<?php if ($_smarty_tpl->getVariable('prefs')->value['flaggedrev_approval']=='y'&&$_smarty_tpl->getVariable('revision_approval')->value){?>
	<?php if (($_smarty_tpl->getVariable('revision_approved')->value||$_smarty_tpl->getVariable('revision_displayed')->value)&&$_smarty_tpl->getVariable('revision_approved')->value!=$_smarty_tpl->getVariable('lastVersion')->value){?>
		<?php if ($_smarty_tpl->getVariable('lastVersion')->value==$_smarty_tpl->getVariable('revision_displayed')->value){?>
			<?php $_smarty_tpl->smarty->_tag_stack[] = array('remarksbox', array('type'=>'comment','title'=>"Newer content available")); $_block_repeat=true; smarty_block_remarksbox(array('type'=>'comment','title'=>"Newer content available"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

				<p>
					You are currently viewing the latest version of the page.
					<?php if ($_smarty_tpl->getVariable('revision_approved')->value){?>
						You can also view the <?php $_smarty_tpl->smarty->_tag_stack[] = array('self_link', array()); $_block_repeat=true; smarty_block_self_link(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
latest approved version<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_self_link(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.
					<?php }?>
					<?php if ($_smarty_tpl->getVariable('tiki_p_wiki_approve')->value=='y'){?>
						You can approve this revision and make it available to a wider audience. Make sure you review all the changes before approving.
					<?php }?>
				</p>
				<?php if ($_smarty_tpl->getVariable('tiki_p_wiki_approve')->value=='y'){?>
					<form method="post" action="<?php echo smarty_modifier_sefurl($_smarty_tpl->getVariable('page')->value);?>
">
						<?php if ($_smarty_tpl->getVariable('revision_approved')->value){?>
							<p><a href="tiki-pagehistory.php?page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,'url');?>
&compare&oldver=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('revision_approved')->value,'url');?>
&diff_style=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['default_wiki_diff_style'],'url');?>
">Show changes since last approved revision</a></p>
						<?php }else{ ?>
							<p>This page has no prior approved revision. <strong>All of the content must be reviewed.</strong></p>
						<?php }?>
						<div class="submit">
							<input type="hidden" name="revision" value="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('revision_displayed')->value);?>
"/>
							<input type="submit" name="approve" value="Approve current revision"/>
						</div>
					</form>
				<?php }?>
			<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_remarksbox(array('type'=>'comment','title'=>"Newer content available"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

		<?php }else{ ?>
			<?php $_smarty_tpl->smarty->_tag_stack[] = array('remarksbox', array('type'=>'comment','title'=>"Content waiting for approval")); $_block_repeat=true; smarty_block_remarksbox(array('type'=>'comment','title'=>"Content waiting for approval"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

				<p>
					You are currently viewing the approved version of the page.
					<?php if ($_smarty_tpl->getVariable('revision_approved')->value&&$_smarty_tpl->getVariable('tiki_p_wiki_view_latest')->value=='y'){?>
						You can also view the <?php $_smarty_tpl->smarty->_tag_stack[] = array('self_link', array('latest'=>1)); $_block_repeat=true; smarty_block_self_link(array('latest'=>1), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
latest version<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_self_link(array('latest'=>1), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.
					<?php }?>
				</p>
			<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_remarksbox(array('type'=>'comment','title'=>"Content waiting for approval"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

		<?php }?>
	<?php }elseif($_smarty_tpl->getVariable('revision_approval')->value&&!$_smarty_tpl->getVariable('revision_approved')->value&&$_smarty_tpl->getVariable('tiki_p_wiki_view_latest')->value=='y'){?>
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('remarksbox', array('type'=>'comment','title'=>"Content waiting for approval")); $_block_repeat=true; smarty_block_remarksbox(array('type'=>'comment','title'=>"Content waiting for approval"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

			<p>
				View the <?php $_smarty_tpl->smarty->_tag_stack[] = array('self_link', array('latest'=>1)); $_block_repeat=true; smarty_block_self_link(array('latest'=>1), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
latest version<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_self_link(array('latest'=>1), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.
			</p>
		<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_remarksbox(array('type'=>'comment','title'=>"Content waiting for approval"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

	<?php }?>
<?php }?>
<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\tiki-flaggedrev_approval_header.tpl -->