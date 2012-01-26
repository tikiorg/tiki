<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:06
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-rsslist.tpl" */ ?>
<?php /*%%SmartyHeaderCode:302984f1e08eaec2ae9-27786330%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '646245268da4e5b6adbee67a0b4648333e0f5655' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\modules/mod-rsslist.tpl',
      1 => 1283249102,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '302984f1e08eaec2ae9-27786330',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_block_tikimodule')) include 'lib/smarty_tiki\block.tikimodule.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-rsslist.tpl --><?php $_smarty_tpl->smarty->_tag_stack[] = array('tikimodule', array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>"rsslist",'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle'])); $_block_repeat=true; smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>"rsslist",'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

  <div id="rss">
    <?php if ($_smarty_tpl->getVariable('prefs')->value['feature_wiki']=='y'&&$_smarty_tpl->getVariable('prefs')->value['feed_wiki']=='y'&&$_smarty_tpl->getVariable('tiki_p_view')->value=='y'){?>
        <a class="linkmodule" title="Wiki feed" href="tiki-wiki_rss.php?ver=<?php echo $_smarty_tpl->getVariable('prefs')->value['feed_default_version'];?>
"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt="Feed" title="Feed" width='16' height='16' />
        Wiki
        </a>
        <br />
    <?php }?>
    <?php if ($_smarty_tpl->getVariable('prefs')->value['feature_blogs']=='y'&&$_smarty_tpl->getVariable('prefs')->value['feed_blogs']=='y'&&$_smarty_tpl->getVariable('tiki_p_read_blog')->value=='y'){?>
        <a class="linkmodule" title="Blogs feed" href="tiki-blogs_rss.php?ver=<?php echo $_smarty_tpl->getVariable('prefs')->value['feed_default_version'];?>
"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt="Feed" title="Feed" width='16' height='16' />
        Blogs
        </a>
        <br />
    <?php }?>
    <?php if ($_smarty_tpl->getVariable('prefs')->value['feature_articles']=='y'&&$_smarty_tpl->getVariable('prefs')->value['feed_articles']=='y'&&$_smarty_tpl->getVariable('tiki_p_read_article')->value=='y'){?>
        <a class="linkmodule" title="Articles feed" href="tiki-articles_rss.php?ver=<?php echo $_smarty_tpl->getVariable('prefs')->value['feed_default_version'];?>
"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt="Feed" title="Feed" width='16' height='16' />
        Articles
        </a>
        <br />
    <?php }?>
    <?php if ($_smarty_tpl->getVariable('prefs')->value['feature_galleries']=='y'&&$_smarty_tpl->getVariable('prefs')->value['feed_image_galleries']=='y'&&$_smarty_tpl->getVariable('tiki_p_view_image_gallery')->value=='y'){?>
        <a class="linkmodule" title="Image Galleries feed" href="tiki-image_galleries_rss.php?ver=<?php echo $_smarty_tpl->getVariable('prefs')->value['feed_default_version'];?>
"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt="Feed" title="Feed" width='16' height='16' />
        Image Galleries
        </a>
        <br />
    <?php }?>
    <?php if ($_smarty_tpl->getVariable('prefs')->value['feature_file_galleries']=='y'&&$_smarty_tpl->getVariable('prefs')->value['feed_file_galleries']=='y'&&$_smarty_tpl->getVariable('tiki_p_view_file_gallery')->value=='y'){?>
        <a class="linkmodule" title="File Galleries feed" href="tiki-file_galleries_rss.php?ver=<?php echo $_smarty_tpl->getVariable('prefs')->value['feed_default_version'];?>
"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt="Feed" title="Feed" width='16' height='16' />
        File Galleries
        </a>
        <br />
    <?php }?>
    <?php if ($_smarty_tpl->getVariable('prefs')->value['feature_forums']=='y'&&$_smarty_tpl->getVariable('prefs')->value['feed_forums']=='y'&&$_smarty_tpl->getVariable('tiki_p_forum_read')->value=='y'){?>
        <a class="linkmodule" title="Forums feed" href="tiki-forums_rss.php?ver=<?php echo $_smarty_tpl->getVariable('prefs')->value['feed_default_version'];?>
"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt="Feed" title="Feed" width='16' height='16' />
        Forums
        </a>
        <br />
    <?php }?>
    <?php if ($_smarty_tpl->getVariable('prefs')->value['feature_maps']=='y'&&$_smarty_tpl->getVariable('prefs')->value['rss_mapfiles']=='y'&&$_smarty_tpl->getVariable('tiki_p_map_view')->value=='y'){?>
        <a class="linkmodule" title="Maps feed" href="tiki-map_rss.php?ver=<?php echo $_smarty_tpl->getVariable('prefs')->value['feed_default_version'];?>
"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt="Feed" title="Feed" width='16' height='16' />
        Maps
        </a>
        <br />
    <?php }?>
    <?php if ($_smarty_tpl->getVariable('prefs')->value['feature_directory']=='y'&&$_smarty_tpl->getVariable('prefs')->value['feed_directories']=='y'&&$_smarty_tpl->getVariable('tiki_p_view_directory')->value=='y'){?>
        <a class="linkmodule" href="tiki-directories_rss.php?ver=<?php echo $_smarty_tpl->getVariable('prefs')->value['feed_default_version'];?>
"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt="Feed" title="Feed" width='16' height='16' />
        Directories
        </a>
        <br />
    <?php }?>
    <?php if ($_smarty_tpl->getVariable('prefs')->value['feature_calendar']=='y'&&$_smarty_tpl->getVariable('prefs')->value['feed_calendar']=='y'&&$_smarty_tpl->getVariable('tiki_p_view_calendar')->value=='y'){?>
        <a class="linkmodule" href="tiki-calendars_rss.php?ver=<?php echo $_smarty_tpl->getVariable('prefs')->value['feed_default_version'];?>
"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt="Feed" title="Feed" width='16' height='16' />
        Calendars
        </a>
        <br />
    <?php }?>
  </div>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_tikimodule(array('error'=>$_smarty_tpl->getVariable('module_params')->value['error'],'title'=>$_smarty_tpl->getVariable('tpl_module_title')->value,'name'=>"rsslist",'flip'=>$_smarty_tpl->getVariable('module_params')->value['flip'],'decorations'=>$_smarty_tpl->getVariable('module_params')->value['decorations'],'nobox'=>$_smarty_tpl->getVariable('module_params')->value['nobox'],'notitle'=>$_smarty_tpl->getVariable('module_params')->value['notitle']), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\modules/mod-rsslist.tpl -->