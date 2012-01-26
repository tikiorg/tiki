<?php /* Smarty version Smarty-3.0.9, created on 2012-01-24 02:27:10
         compiled from "G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:107624f1e08eeda86a2-65357863%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '474594a0782382a0f409dbda4c5c9cfc46dbc146' => 
    array (
      0 => 'G:\\W3ld1\\Teawik\\teawik-ld1-83x\\83R1\\templates\\header.tpl',
      1 => 1321713270,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '107624f1e08eeda86a2-65357863',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include 'lib/smarty_tiki\modifier.escape.php';
if (!is_callable('smarty_modifier_tr_if')) include 'lib/smarty_tiki\modifier.tr_if.php';
if (!is_callable('smarty_function_breadcrumbs')) include 'lib/smarty_tiki\function.breadcrumbs.php';
if (!is_callable('smarty_modifier_truncate')) include 'lib/smarty_tiki\modifier.truncate.php';
if (!is_callable('smarty_modifier_username')) include 'lib/smarty_tiki\modifier.username.php';
if (!is_callable('smarty_modifier_stringfix')) include 'lib/smarty_tiki\modifier.stringfix.php';
?><!-- TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\header.tpl -->
<?php if ($_smarty_tpl->getVariable('base_uri')->value&&($_smarty_tpl->getVariable('dir_level')->value>0||$_smarty_tpl->getVariable('prefs')->value['feature_html_head_base_tag']=='y')){?>
	<base href="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('base_uri')->value);?>
" />
<?php }?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="generator" content="Tiki Wiki CMS Groupware - http://tiki.org" />


<?php $_template = new Smarty_Internal_Template("canonical.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>	

<?php if (!empty($_smarty_tpl->getVariable('forum_info',null,true,false)->value['name'])&$_smarty_tpl->getVariable('prefs')->value['metatag_threadtitle']=='y'){?>
	<meta name="keywords" content="Forum <?php echo smarty_modifier_escape($_smarty_tpl->getVariable('forum_info')->value['name']);?>
 <?php echo smarty_modifier_escape($_smarty_tpl->getVariable('thread_info')->value['title']);?>
 <?php if ($_smarty_tpl->getVariable('prefs')->value['feature_freetags']=='y'){?><?php  $_smarty_tpl->tpl_vars['taginfo'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('freetags')->value['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['taginfo']->key => $_smarty_tpl->tpl_vars['taginfo']->value){
?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['taginfo']->value['tag']);?>
 <?php }} ?><?php }?>" />
<?php }elseif(isset($_smarty_tpl->getVariable('galleryId',null,true,false)->value)&&$_smarty_tpl->getVariable('galleryId')->value!=''&&$_smarty_tpl->getVariable('prefs')->value['metatag_imagetitle']!='n'){?>
	<meta name="keywords" content="Images Galleries <?php echo smarty_modifier_escape($_smarty_tpl->getVariable('title')->value);?>
 <?php if ($_smarty_tpl->getVariable('prefs')->value['feature_freetags']=='y'){?><?php  $_smarty_tpl->tpl_vars['taginfo'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('freetags')->value['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['taginfo']->key => $_smarty_tpl->tpl_vars['taginfo']->value){
?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['taginfo']->value['tag']);?>
 <?php }} ?><?php }?>" />
<?php }elseif($_smarty_tpl->getVariable('prefs')->value['metatag_keywords']!=''||!empty($_smarty_tpl->getVariable('metatag_local_keywords',null,true,false)->value)){?>
	<meta name="keywords" content="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['metatag_keywords']);?>
 <?php if ($_smarty_tpl->getVariable('prefs')->value['feature_freetags']=='y'){?><?php  $_smarty_tpl->tpl_vars["taginfo"] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('freetags')->value['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars["taginfo"]->key => $_smarty_tpl->tpl_vars["taginfo"]->value){
?><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('taginfo')->value['tag']);?>
 <?php }} ?><?php }?> <?php echo smarty_modifier_escape($_smarty_tpl->getVariable('metatag_local_keywords')->value);?>
" />
<?php }?>
<?php if ($_smarty_tpl->getVariable('prefs')->value['metatag_author']!=''){?>
	<meta name="author" content="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['metatag_author']);?>
" />
<?php }?>
<?php if ($_smarty_tpl->getVariable('section')->value=="blogs"){?>
	<?php if ($_smarty_tpl->getVariable('blog_data')->value['title']==''){?>
	<meta name="description" content="Blog listing" />
	<?php }elseif($_smarty_tpl->getVariable('postId')->value==''){?>
	<meta name="description" content="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('blog_data')->value['title']);?>
" />
	<?php }else{ ?> 
	<meta name="description" content="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('post_info')->value['title']);?>
 - <?php echo smarty_modifier_escape($_smarty_tpl->getVariable('blog_data')->value['title']);?>
" />
	<?php }?>
<?php }elseif($_smarty_tpl->getVariable('prefs')->value['metatag_pagedesc']=='y'&&$_smarty_tpl->getVariable('description')->value!=''){?>
	<meta name="description" content="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('description')->value);?>
" />
<?php }elseif($_smarty_tpl->getVariable('prefs')->value['metatag_description']!=''||(isset($_smarty_tpl->getVariable('description',null,true,false)->value)&&$_smarty_tpl->getVariable('description')->value=='')){?>
	<meta name="description" content="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['metatag_description']);?>
" />
<?php }?>
<?php if ($_smarty_tpl->getVariable('prefs')->value['metatag_geoposition']!=''){?>
	<meta name="geo.position" content="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['metatag_geoposition']);?>
" />
<?php }?>
<?php if ($_smarty_tpl->getVariable('prefs')->value['metatag_georegion']!=''){?>
	<meta name="geo.region" content="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['metatag_georegion']);?>
" />
<?php }?>
<?php if ($_smarty_tpl->getVariable('prefs')->value['metatag_geoplacename']!=''){?>
	<meta name="geo.placename" content="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['metatag_geoplacename']);?>
" />
<?php }?>
<?php if ((isset($_smarty_tpl->getVariable('prefs',null,true,false)->value['metatag_robots'])&&$_smarty_tpl->getVariable('prefs')->value['metatag_robots']!='')&&(!isset($_smarty_tpl->getVariable('metatag_robots',null,true,false)->value)||$_smarty_tpl->getVariable('metatag_robots')->value=='')){?>
        <meta name="robots" content="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['metatag_robots']);?>
" />
<?php }?>
<?php if ((!isset($_smarty_tpl->getVariable('prefs',null,true,false)->value['metatag_robots'])||$_smarty_tpl->getVariable('prefs')->value['metatag_robots']=='')&&(isset($_smarty_tpl->getVariable('metatag_robots',null,true,false)->value)&&$_smarty_tpl->getVariable('metatag_robots')->value!='')){?>
        <meta name="robots" content="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('metatag_robots')->value);?>
" />
<?php }?>
<?php if ((isset($_smarty_tpl->getVariable('prefs',null,true,false)->value['metatag_robots'])&&$_smarty_tpl->getVariable('prefs')->value['metatag_robots']!='')&&(isset($_smarty_tpl->getVariable('metatag_robots',null,true,false)->value)&&$_smarty_tpl->getVariable('metatag_robots')->value!='')){?>
        <meta name="robots" content="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['metatag_robots']);?>
, <?php echo smarty_modifier_escape($_smarty_tpl->getVariable('metatag_robots')->value);?>
" />
<?php }?>
<?php if ($_smarty_tpl->getVariable('prefs')->value['metatag_revisitafter']!=''){?>
	<meta name="revisit-after" content="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['metatag_revisitafter']);?>
" />
<?php }?>


<title><?php if ($_smarty_tpl->getVariable('prefs')->value['site_title_location']=='before'){?><?php echo smarty_modifier_escape(smarty_modifier_tr_if($_smarty_tpl->getVariable('prefs')->value['browsertitle']));?>
 <?php echo $_smarty_tpl->getVariable('prefs')->value['site_nav_seper'];?>
 <?php }?><?php ob_start(); ?><?php if (($_smarty_tpl->getVariable('prefs')->value['feature_breadcrumbs']=='y'||$_smarty_tpl->getVariable('prefs')->value['site_title_breadcrumb']=="desc")&&isset($_smarty_tpl->getVariable('trail',null,true,false)->value)){?><?php echo smarty_function_breadcrumbs(array('type'=>$_smarty_tpl->getVariable('prefs')->value['site_title_breadcrumb'],'loc'=>"head",'crumbs'=>$_smarty_tpl->getVariable('trail')->value),$_smarty_tpl);?>
<?php }?><?php  $_smarty_tpl->assign("page_description_title", ob_get_contents()); Smarty::$_smarty_vars['capture']['default']=ob_get_clean();?>
	<?php if (!empty($_smarty_tpl->getVariable('page_description_title',null,true,false)->value)){?>
		<?php echo $_smarty_tpl->getVariable('page_description_title')->value;?>

	<?php }else{ ?>
		<?php if (!empty($_smarty_tpl->getVariable('tracker_item_main_value',null,true,false)->value)){?>
			<?php echo smarty_modifier_escape(smarty_modifier_truncate($_smarty_tpl->getVariable('tracker_item_main_value')->value,255));?>

		<?php }elseif(!empty($_smarty_tpl->getVariable('title',null,true,false)->value)&&!is_array($_smarty_tpl->getVariable('title')->value)){?>
			<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('title')->value);?>

		<?php }elseif(!empty($_smarty_tpl->getVariable('page',null,true,false)->value)){?>
			<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value);?>

		<?php }elseif(!empty($_smarty_tpl->getVariable('description',null,true,false)->value)){?><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('description')->value);?>

		
		<?php }elseif(!empty($_smarty_tpl->getVariable('arttitle',null,true,false)->value)){?>
			<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('arttitle')->value);?>

		<?php }elseif(!empty($_smarty_tpl->getVariable('thread_info',null,true,false)->value['title'])){?>
			<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('thread_info')->value['title']);?>

		<?php }elseif(!empty($_smarty_tpl->getVariable('forum_info',null,true,false)->value['name'])){?>
			<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('forum_info')->value['name']);?>

		<?php }elseif(!empty($_smarty_tpl->getVariable('categ_info',null,true,false)->value['name'])){?>
			<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('categ_info')->value['name']);?>

		<?php }elseif(!empty($_smarty_tpl->getVariable('userinfo',null,true,false)->value['login'])){?>
			<?php echo smarty_modifier_username($_smarty_tpl->getVariable('userinfo')->value['login']);?>

		<?php }elseif(!empty($_smarty_tpl->getVariable('tracker_info',null,true,false)->value['name'])){?>
			<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('tracker_info')->value['name']);?>

		<?php }elseif(!empty($_smarty_tpl->getVariable('headtitle',null,true,false)->value)){?>
			<?php echo smarty_modifier_escape(smarty_modifier_stringfix($_smarty_tpl->getVariable('headtitle')->value,"&nbsp;"));?>

		<?php }?>
	<?php }?>
	<?php if ($_smarty_tpl->getVariable('prefs')->value['site_title_location']=='after'){?> <?php echo $_smarty_tpl->getVariable('prefs')->value['site_nav_seper'];?>
 <?php echo smarty_modifier_escape(smarty_modifier_tr_if($_smarty_tpl->getVariable('prefs')->value['browsertitle']));?>
<?php }?>
</title>

<?php if ($_smarty_tpl->getVariable('prefs')->value['site_favicon']){?>
	<link rel="icon" href="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['site_favicon']);?>
" />
<?php }?>


<?php if ((isset($_smarty_tpl->getVariable('editable',null,true,false)->value)&&$_smarty_tpl->getVariable('editable')->value)&&($_smarty_tpl->getVariable('tiki_p_edit')->value=='y'||((mb_detect_encoding($_smarty_tpl->getVariable('page')->value, 'UTF-8, ISO-8859-1') === 'UTF-8') ? mb_strtolower($_smarty_tpl->getVariable('page')->value,SMARTY_RESOURCE_CHAR_SET) : strtolower($_smarty_tpl->getVariable('page')->value))=='sandbox'||$_smarty_tpl->getVariable('tiki_p_admin_wiki')->value=='y')){?>
	<link rel="alternate" type="application/x-wiki" title="Edit this page!" href="tiki-editpage.php?page=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('page')->value,'url');?>
" />
<?php }?>


<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_wiki']=='y'&&$_smarty_tpl->getVariable('prefs')->value['feed_wiki']=='y'&&$_smarty_tpl->getVariable('tiki_p_view')->value=='y'){?>
	<link rel="alternate" type="application/rss+xml" title='<?php echo (($tmp = @smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['feed_wiki_title']))===null||$tmp==='' ? "RSS Wiki" : $tmp);?>
' href="tiki-wiki_rss.php?ver=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['feed_default_version'],'url');?>
" />
<?php }?>
<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_blogs']=='y'&&$_smarty_tpl->getVariable('prefs')->value['feed_blogs']=='y'&&$_smarty_tpl->getVariable('tiki_p_read_blog')->value=='y'){?>
	<link rel="alternate" type="application/rss+xml" title='<?php echo (($tmp = @smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['feed_blogs_title']))===null||$tmp==='' ? "RSS Blogs" : $tmp);?>
' href="tiki-blogs_rss.php?ver=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['feed_default_version'],'url');?>
" />
<?php }?>
<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_articles']=='y'&&$_smarty_tpl->getVariable('prefs')->value['feed_articles']=='y'&&$_smarty_tpl->getVariable('tiki_p_read_article')->value=='y'){?>
	<link rel="alternate" type="application/rss+xml" title='<?php echo (($tmp = @smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['feed_articles_title']))===null||$tmp==='' ? "RSS Articles" : $tmp);?>
' href="tiki-articles_rss.php?ver=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['feed_default_version'],'url');?>
" />
<?php }?>
<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_galleries']=='y'&&$_smarty_tpl->getVariable('prefs')->value['feed_image_galleries']=='y'&&$_smarty_tpl->getVariable('tiki_p_view_image_gallery')->value=='y'){?>
	<link rel="alternate" type="application/rss+xml" title='<?php echo (($tmp = @smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['feed_image_galleries_title']))===null||$tmp==='' ? "RSS Image Galleries" : $tmp);?>
' href="tiki-image_galleries_rss.php?ver=<?php echo $_smarty_tpl->getVariable('prefs')->value['feed_default_version'];?>
" />
<?php }?>
<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_file_galleries']=='y'&&$_smarty_tpl->getVariable('prefs')->value['feed_file_galleries']=='y'&&$_smarty_tpl->getVariable('tiki_p_view_file_gallery')->value=='y'){?>
	<link rel="alternate" type="application/rss+xml" title='<?php echo (($tmp = @smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['feed_file_galleries_title']))===null||$tmp==='' ? "RSS File Galleries" : $tmp);?>
' href="tiki-file_galleries_rss.php?ver=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['feed_default_version'],'url');?>
" />
<?php }?>
<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_forums']=='y'&&$_smarty_tpl->getVariable('prefs')->value['feed_forums']=='y'&&$_smarty_tpl->getVariable('tiki_p_forum_read')->value=='y'){?>
	<link rel="alternate" type="application/rss+xml" title='<?php echo (($tmp = @smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['feed_forums_title']))===null||$tmp==='' ? "RSS Forums" : $tmp);?>
' href="tiki-forums_rss.php?ver=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['feed_default_version'],'url');?>
" />
<?php }?>
<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_maps']=='y'&&$_smarty_tpl->getVariable('prefs')->value['rss_mapfiles']=='y'&&$_smarty_tpl->getVariable('tiki_p_map_view')->value=='y'){?>
	<link rel="alternate" type="application/rss+xml" title='<?php echo (($tmp = @smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['title_rss_mapfiles']))===null||$tmp==='' ? "RSS Maps" : $tmp);?>
' href="tiki-map_rss.php?ver=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['feed_default_version'],'url');?>
" />
<?php }?>
<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_directory']=='y'&&$_smarty_tpl->getVariable('prefs')->value['feed_directories']=='y'&&$_smarty_tpl->getVariable('tiki_p_view_directory')->value=='y'){?>
	<link rel="alternate" type="application/rss+xml" title='<?php echo (($tmp = @smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['feed_directories_title']))===null||$tmp==='' ? "RSS Directories" : $tmp);?>
' href="tiki-directories_rss.php?ver=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['feed_default_version'],'url');?>
" />
<?php }?>

<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_calendar']=='y'&&$_smarty_tpl->getVariable('prefs')->value['feed_calendar']=='y'&&$_smarty_tpl->getVariable('tiki_p_view_calendar')->value=='y'){?>
	<link rel="alternate" type="application/rss+xml" title='<?php echo (($tmp = @smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['feed_calendar_title']))===null||$tmp==='' ? "RSS Calendars" : $tmp);?>
' href="tiki-calendars_rss.php?ver=<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('prefs')->value['feed_default_version'],'url');?>
" />
<?php }?>

<?php if (($_smarty_tpl->getVariable('prefs')->value['feature_blogs']=='y'&&$_smarty_tpl->getVariable('prefs')->value['feature_blog_sharethis']=='y')||($_smarty_tpl->getVariable('prefs')->value['feature_articles']=='y'&&$_smarty_tpl->getVariable('prefs')->value['feature_cms_sharethis']=='y')){?>
	<?php if ($_smarty_tpl->getVariable('prefs')->value['blog_sharethis_publisher']!=''&&$_smarty_tpl->getVariable('prefs')->value['article_sharethis_publisher']!=''){?>
		<script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#publisher=<?php echo $_smarty_tpl->getVariable('prefs')->value['blog_sharethis_publisher'];?>
&amp;type=website&amp;buttonText=&amp;onmouseover=false&amp;send_services=aim"></script>
	<?php }elseif($_smarty_tpl->getVariable('prefs')->value['blog_sharethis_publisher']!=''&&$_smarty_tpl->getVariable('prefs')->value['article_sharethis_publisher']==''){?>
		<script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#publisher=<?php echo $_smarty_tpl->getVariable('prefs')->value['blog_sharethis_publisher'];?>
&amp;type=website&amp;buttonText=&amp;onmouseover=false&amp;send_services=aim"></script>
	<?php }elseif($_smarty_tpl->getVariable('prefs')->value['blog_sharethis_publisher']==''&&$_smarty_tpl->getVariable('prefs')->value['article_sharethis_publisher']!=''){?>
		<script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#publisher=<?php echo $_smarty_tpl->getVariable('prefs')->value['article_sharethis_publisher'];?>
&amp;type=website&amp;buttonText=&amp;onmouseover=false&amp;send_services=aim"></script>
	<?php }elseif($_smarty_tpl->getVariable('prefs')->value['blog_sharethis_publisher']==''&&$_smarty_tpl->getVariable('prefs')->value['article_sharethis_publisher']==''){?>
		<script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#type=website&amp;buttonText=&amp;onmouseover=false&amp;send_services=aim"></script>
	<?php }?>
<?php }?>

<!--[if lt IE 9]>
	<script src="lib/html5shim/html5.js" type="text/javascript"></script>
<![endif]-->

<?php if ($_smarty_tpl->getVariable('headerlib')->value){?>		<?php echo $_smarty_tpl->getVariable('headerlib')->value->output_headers();?>
<?php }?>

<?php if ($_smarty_tpl->getVariable('prefs')->value['feature_custom_html_head_content']){?>
	<?php $_template = new Smarty_Internal_Template('eval:'.$_smarty_tpl->getVariable('prefs')->value['feature_custom_html_head_content'], $_smarty_tpl->smarty, $_smarty_tpl);echo $_template->getRenderedTemplate(); ?>
<?php }?>

<!-- /TPL: G:\W3ld1\Teawik\teawik-ld1-83x\83R1\templates\header.tpl -->