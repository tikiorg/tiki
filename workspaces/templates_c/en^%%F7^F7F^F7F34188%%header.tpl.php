<?php /* Smarty version 2.6.22, created on 2009-03-04 13:06:12
         compiled from header.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'breadcrumbs', 'header.tpl', 35, false),array('function', 'popup_init', 'header.tpl', 199, false),array('function', 'icon', 'header.tpl', 202, false),array('modifier', 'escape', 'header.tpl', 39, false),array('modifier', 'lower', 'header.tpl', 65, false),array('block', 'tr', 'header.tpl', 126, false),)), $this); ?>
<?php echo '<?xml'; ?>
 version="1.0" encoding="UTF-8"<?php echo '?>'; ?>

<!DOCTYPE html 
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php if (! empty ( $this->_tpl_vars['pageLang'] )): ?><?php echo $this->_tpl_vars['pageLang']; ?>
<?php else: ?><?php echo $this->_tpl_vars['prefs']['language']; ?>
<?php endif; ?>" lang="<?php if (! empty ( $this->_tpl_vars['pageLang'] )): ?><?php echo $this->_tpl_vars['pageLang']; ?>
<?php else: ?><?php echo $this->_tpl_vars['prefs']['language']; ?>
<?php endif; ?>">
<head>
<?php if ($this->_tpl_vars['base_url'] && $this->_tpl_vars['dir_level'] > 0): ?><base href="<?php echo $this->_tpl_vars['base_url']; ?>
"/><?php endif; ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="generator" content="TikiWiki CMS/Groupware - http://TikiWiki.org" />
<?php if (! empty ( $this->_tpl_vars['forum_info']['name'] ) & $this->_tpl_vars['prefs']['metatag_threadtitle'] == 'y'): ?><meta name="keywords" content="Forum <?php echo $this->_tpl_vars['forum_info']['name']; ?>
 <?php echo $this->_tpl_vars['thread_info']['title']; ?>
 <?php if ($this->_tpl_vars['prefs']['feature_freetags'] == 'y'): ?><?php $_from = $this->_tpl_vars['freetags']['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['taginfo']):
?><?php echo $this->_tpl_vars['taginfo']['tag']; ?>
 <?php endforeach; endif; unset($_from); ?><?php endif; ?>" />
<?php elseif ($this->_tpl_vars['galleryId'] != '' & $this->_tpl_vars['prefs']['metatag_imagetitle'] != 'n'): ?><meta name="keywords" content="Images Galleries <?php echo $this->_tpl_vars['title']; ?>
 <?php if ($this->_tpl_vars['prefs']['feature_freetags'] == 'y'): ?><?php $_from = $this->_tpl_vars['freetags']['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['taginfo']):
?><?php echo $this->_tpl_vars['taginfo']['tag']; ?>
 <?php endforeach; endif; unset($_from); ?><?php endif; ?>" />
<?php elseif ($this->_tpl_vars['prefs']['metatag_keywords'] != ''): ?><meta name="keywords" content="<?php echo $this->_tpl_vars['prefs']['metatag_keywords']; ?>
 <?php if ($this->_tpl_vars['prefs']['feature_freetags'] == 'y'): ?><?php $_from = $this->_tpl_vars['freetags']['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['taginfo']):
?><?php echo $this->_tpl_vars['taginfo']['tag']; ?>
 <?php endforeach; endif; unset($_from); ?><?php endif; ?>" />
<?php endif; ?>
<?php if ($this->_tpl_vars['prefs']['metatag_author'] != ''): ?><meta name="author" content="<?php echo $this->_tpl_vars['prefs']['metatag_author']; ?>
" />
<?php endif; ?>
<?php if ($this->_tpl_vars['prefs']['metatag_description'] != ''): ?><meta name="description" content="<?php echo $this->_tpl_vars['prefs']['metatag_description']; ?>
" />
<?php endif; ?>
<?php if ($this->_tpl_vars['prefs']['metatag_geoposition'] != ''): ?><meta name="geo.position" content="<?php echo $this->_tpl_vars['prefs']['metatag_geoposition']; ?>
" />
<?php endif; ?>
<?php if ($this->_tpl_vars['prefs']['metatag_georegion'] != ''): ?><meta name="geo.region" content="<?php echo $this->_tpl_vars['prefs']['metatag_georegion']; ?>
" />
<?php endif; ?>
<?php if ($this->_tpl_vars['prefs']['metatag_geoplacename'] != ''): ?><meta name="geo.placename" content="<?php echo $this->_tpl_vars['prefs']['metatag_geoplacename']; ?>
" />
<?php endif; ?>
<?php if ($this->_tpl_vars['prefs']['metatag_robots'] != ''): ?><meta name="robots" content="<?php echo $this->_tpl_vars['prefs']['metatag_robots']; ?>
" />
<?php endif; ?>
<?php if ($this->_tpl_vars['prefs']['metatag_revisitafter'] != ''): ?><meta name="revisit-after" content="<?php echo $this->_tpl_vars['prefs']['metatag_revisitafter']; ?>
" />
<?php endif; ?>


<script type="text/javascript" src="lib/tiki-js.js"></script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "bidi.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<title>
<?php if (isset ( $this->_tpl_vars['trail'] )): ?><?php echo smarty_function_breadcrumbs(array('type' => 'fulltrail','loc' => 'head','crumbs' => $this->_tpl_vars['trail']), $this);?>

<?php else: ?>
<?php echo $this->_tpl_vars['prefs']['siteTitle']; ?>

<?php if (! empty ( $this->_tpl_vars['headtitle'] )): ?> : <?php echo $this->_tpl_vars['headtitle']; ?>

<?php elseif (! empty ( $this->_tpl_vars['page'] )): ?> : <?php if ($this->_tpl_vars['beingStaged'] == 'y' && $this->_tpl_vars['prefs']['wikiapproval_hideprefix'] == 'y'): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['approvedPageName'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<?php else: ?><?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<?php endif; ?> 
<?php elseif (! empty ( $this->_tpl_vars['arttitle'] )): ?> : <?php echo $this->_tpl_vars['arttitle']; ?>

<?php elseif (! empty ( $this->_tpl_vars['title'] )): ?> : <?php echo $this->_tpl_vars['title']; ?>

<?php elseif (! empty ( $this->_tpl_vars['thread_info']['title'] )): ?> : <?php echo $this->_tpl_vars['thread_info']['title']; ?>

<?php elseif (! empty ( $this->_tpl_vars['post_info']['title'] )): ?> : <?php echo $this->_tpl_vars['post_info']['title']; ?>

<?php elseif (! empty ( $this->_tpl_vars['forum_info']['name'] )): ?> : <?php echo $this->_tpl_vars['forum_info']['name']; ?>

<?php elseif (! empty ( $this->_tpl_vars['categ_info']['name'] )): ?> : <?php echo $this->_tpl_vars['categ_info']['name']; ?>

<?php elseif (! empty ( $this->_tpl_vars['userinfo']['login'] )): ?> : <?php echo $this->_tpl_vars['userinfo']['login']; ?>

<?php elseif (! empty ( $this->_tpl_vars['tracker_item_main_value'] )): ?> : <?php echo $this->_tpl_vars['tracker_item_main_value']; ?>

<?php elseif (! empty ( $this->_tpl_vars['tracker_info']['name'] )): ?> : <?php echo $this->_tpl_vars['tracker_info']['name']; ?>

<?php endif; ?>
<?php endif; ?>
</title>

<?php if ($this->_tpl_vars['prefs']['site_favicon']): ?><link rel="icon" href="<?php echo $this->_tpl_vars['prefs']['site_favicon']; ?>
" /><?php endif; ?>
<!--[if lt IE 7]> <link rel="StyleSheet" href="css/ie6.css" type="text/css" /> <![endif]-->


<?php if ($this->_tpl_vars['prefs']['feature_phplayers'] == 'y' && isset ( $this->_tpl_vars['phplayers_headers'] )): ?><?php echo $this->_tpl_vars['phplayers_headers']; ?>
<?php endif; ?>


<?php if ($this->_tpl_vars['prefs']['feature_cssmenus'] == 'y'): ?>
<link rel="StyleSheet" href="css/cssmenus.css" type="text/css"></link>
<?php endif; ?>


<?php if (( $this->_tpl_vars['editable'] && ( $this->_tpl_vars['tiki_p_edit'] == 'y' || ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('lower', true, $_tmp) : smarty_modifier_lower($_tmp)) == 'sandbox' ) ) || $this->_tpl_vars['tiki_p_admin_wiki'] == 'y' || $this->_tpl_vars['canEditStaging'] == 'y'): ?>
	<link rel="alternate" type="application/x-wiki" title="Edit this page!" href="tiki-editpage.php?page=<?php echo $this->_tpl_vars['page']; ?>
"/>
<?php endif; ?>


<?php if ($this->_tpl_vars['prefs']['feature_wiki'] == 'y' && $this->_tpl_vars['prefs']['rss_wiki'] == 'y' && $this->_tpl_vars['tiki_p_view'] == 'y'): ?>
<link rel="alternate" type="application/rss+xml" title="RSS Wiki" href="tiki-wiki_rss.php?ver=<?php echo $this->_tpl_vars['prefs']['rssfeed_default_version']; ?>
" />
<?php endif; ?>
<?php if ($this->_tpl_vars['prefs']['feature_blogs'] == 'y' && $this->_tpl_vars['prefs']['rss_blogs'] == 'y' && $this->_tpl_vars['tiki_p_read_blog'] == 'y'): ?>
<link rel="alternate" type="application/rss+xml" title="RSS Blogs" href="tiki-blogs_rss.php?ver=<?php echo $this->_tpl_vars['prefs']['rssfeed_default_version']; ?>
" />
<?php endif; ?>
<?php if ($this->_tpl_vars['prefs']['feature_articles'] == 'y' && $this->_tpl_vars['prefs']['rss_articles'] == 'y' && $this->_tpl_vars['tiki_p_read_article'] == 'y'): ?>
<link rel="alternate" type="application/rss+xml" title="RSS Articles" href="tiki-articles_rss.php?ver=<?php echo $this->_tpl_vars['prefs']['rssfeed_default_version']; ?>
" />
<?php endif; ?>
<?php if ($this->_tpl_vars['prefs']['feature_galleries'] == 'y' && $this->_tpl_vars['prefs']['rss_image_galleries'] == 'y' && $this->_tpl_vars['tiki_p_view_image_gallery'] == 'y'): ?>
<link rel="alternate" type="application/rss+xml" title="RSS Image Galleries" href="tiki-image_galleries_rss.php?ver=<?php echo $this->_tpl_vars['prefs']['rssfeed_default_version']; ?>
" />
<?php endif; ?>
<?php if ($this->_tpl_vars['prefs']['feature_file_galleries'] == 'y' && $this->_tpl_vars['prefs']['rss_file_galleries'] == 'y' && $this->_tpl_vars['tiki_p_view_file_gallery'] == 'y'): ?>
<link rel="alternate" type="application/rss+xml" title="RSS File Galleries" href="tiki-file_galleries_rss.php?ver=<?php echo $this->_tpl_vars['prefs']['rssfeed_default_version']; ?>
" />
<?php endif; ?>
<?php if ($this->_tpl_vars['prefs']['feature_forums'] == 'y' && $this->_tpl_vars['prefs']['rss_forums'] == 'y' && $this->_tpl_vars['tiki_p_forum_read'] == 'y'): ?>
<link rel="alternate" type="application/rss+xml" title="RSS Forums" href="tiki-forums_rss.php?ver=<?php echo $this->_tpl_vars['prefs']['rssfeed_default_version']; ?>
" />
<?php endif; ?>
<?php if ($this->_tpl_vars['prefs']['feature_maps'] == 'y' && $this->_tpl_vars['prefs']['rss_mapfiles'] == 'y' && $this->_tpl_vars['tiki_p_map_view'] == 'y'): ?>
<link rel="alternate" type="application/rss+xml" title="RSS Maps" href="tiki-map_rss.php?ver=<?php echo $this->_tpl_vars['prefs']['rssfeed_default_version']; ?>
" />
<?php endif; ?>
<?php if ($this->_tpl_vars['prefs']['feature_directory'] == 'y' && $this->_tpl_vars['prefs']['rss_directories'] == 'y' && $this->_tpl_vars['tiki_p_view_directory'] == 'y'): ?>
<link rel="alternate" type="application/rss+xml" title="RSS Directories" href="tiki-directories_rss.php?ver=<?php echo $this->_tpl_vars['prefs']['rssfeed_default_version']; ?>
" />
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_calendar'] == 'y' && $this->_tpl_vars['prefs']['rss_calendar'] == 'y' && $this->_tpl_vars['tiki_p_view_calendar'] == 'y'): ?>
<link rel="alternate" type="application/rss+xml" title="RSS Calendars" href="tiki-calendars_rss.php?ver=<?php echo $this->_tpl_vars['prefs']['rssfeed_default_version']; ?>
" />
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_mootools'] == 'y'): ?>
<script type="text/javascript" src="lib/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="lib/mootools/mootools-1.2-more.js"></script>
<?php if ($this->_tpl_vars['mootools_windoo'] == 'y'): ?>
<script type="text/javascript" src="lib/mootools/extensions/windoo/windoo.js"></script>
<?php endif; ?>
<?php if ($this->_tpl_vars['mootab'] == 'y'): ?>
<script src="lib/mootools/extensions/tabs/SimpleTabs.js" type="text/javascript" ></script> 
<?php endif; ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_swffix'] == 'y'): ?>
<script type="text/javascript" src="lib/swffix/swffix.js"></script>
<?php endif; ?>
<script type="text/javascript" src="lib/swfobject.js"></script>

<?php if ($this->_tpl_vars['headerlib']): ?><?php echo $this->_tpl_vars['headerlib']->output_headers(); ?>
<?php endif; ?>
<?php if (( $this->_tpl_vars['mid'] == 'tiki-editpage.tpl' )): ?>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
<?php echo '
  var needToConfirm = true;
  
  window.onbeforeunload = confirmExit;
  function confirmExit()
  {
    if (needToConfirm)
		'; ?>
return "<?php $this->_tag_stack[] = array('tr', array('interactive' => 'n')); $_block_repeat=true;smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>You are about to leave this page. If you have made any changes without Saving, your changes will be lost.  Are you sure you want to exit this page?<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>";<?php echo '
  }
'; ?>

//--><!]]>
</script>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_shadowbox'] == 'y'): ?>
<!-- Includes for Shadowbox script -->
	<link rel="stylesheet" type="text/css" href="lib/shadowbox/build/css/shadowbox.css" />

<?php if ($this->_tpl_vars['prefs']['feature_mootools'] == 'y'): ?>
	<script type="text/javascript" src="lib/shadowbox/build/js/adapter/shadowbox-mootools.js" charset="utf-8"></script>
<?php else: ?>
	<script type="text/javascript" src="lib/shadowbox/build/js/adapter/shadowbox-jquery.js" charset="utf-8"></script>
<?php endif; ?>

	<script type="text/javascript" src="lib/shadowbox/build/js/shadowbox.js" charset="utf-8"></script>

	<script type="text/javascript">
<!--//--><![CDATA[//><!--
<?php if ($this->_tpl_vars['prefs']['feature_mootools'] == 'y'): ?>
	<?php echo '
		window.addEvent(\'domready\', function() {
	'; ?>

<?php else: ?>
	<?php echo '
		$(document).ready(function() {
	'; ?>

<?php endif; ?>
<?php echo '
			var options = {
				ext: {
					img:        [\'png\', \'jpg\', \'jpeg\', \'gif\', \'bmp\'],
					qt:         [\'dv\', \'mov\', \'moov\', \'movie\', \'mp4\'],
					wmp:        [\'asf\', \'wm\', \'wmv\'],
					qtwmp:      [\'avi\', \'mpg\', \'mpeg\'],
					iframe: [\'asp\', \'aspx\', \'cgi\', \'cfm\', \'doc\', \'htm\', \'html\', \'pdf\', \'pl\', \'php\', \'php3\', \'php4\', \'php5\', \'phtml\', \'rb\', \'rhtml\', \'shtml\', \'txt\', \'vbs\', \'xls\']
				},
				handleUnsupported: \'remove\',
				loadingImage: \'lib/shadowbox/images/loading.gif\',
				overlayBgImage: \'lib/shadowbox/images/overlay-85.png\',
				handleLgImages:     \'resize\',
				text: {
'; ?>

					cancel:   'Cancel',
					loading:  'Loading',
					close:    '\074span class="shortcut"\076C\074/span\076lose',
					next:     '\074span class="shortcut"\076N\074/span\076ext',
					prev:     '\074span class="shortcut"\076P\074/span\076revious'
<?php echo '
				},
				keysClose:          [\'c\', 27], // c OR esc
				keysNext:           [\'n\', 39], // n OR arrow right
				keysPrev:           [\'p\', 37]  // p OR arrow left
			};

			Shadowbox.init(options);
		});
//--><!]]>
	</script>
'; ?>

<?php endif; ?>
</head>
<body <?php if (isset ( $this->_tpl_vars['section'] ) && $this->_tpl_vars['section'] == 'wiki page' && $this->_tpl_vars['prefs']['user_dbl'] == 'y' && $this->_tpl_vars['dblclickedit'] == 'y' && $this->_tpl_vars['tiki_p_edit'] == 'y'): ?>ondblclick="location.href='tiki-editpage.php?page=<?php echo ((is_array($_tmp=$this->_tpl_vars['page'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
';"<?php endif; ?>
 onload="<?php if ($this->_tpl_vars['prefs']['feature_tabs'] == 'y'): ?>tikitabs(<?php if ($this->_tpl_vars['cookietab'] != ''): ?><?php echo $this->_tpl_vars['cookietab']; ?>
<?php else: ?>1<?php endif; ?>,50);<?php endif; ?><?php if ($this->_tpl_vars['msgError']): ?> javascript:location.hash='msgError'<?php endif; ?>"
<?php if ($this->_tpl_vars['section'] || $_SESSION['fullscreen'] == 'y'): ?>class="
<?php if ($this->_tpl_vars['section']): ?>tiki_<?php echo $this->_tpl_vars['section']; ?>
<?php endif; ?> <?php if ($_SESSION['fullscreen'] == 'y'): ?>fullscreen<?php endif; ?>"<?php endif; ?>>
<ul class="jumplinks" style="position:absolute;top:-9000px;left:-9000px;z-index:9;">
 <li><a href="#tiki-center">Jump to Content</a></li>
 
</ul>

<?php if ($this->_tpl_vars['prefs']['feature_community_mouseover'] == 'y'): ?><?php echo smarty_function_popup_init(array('src' => "lib/overlib.js"), $this);?>
<?php endif; ?>
<?php if ($this->_tpl_vars['prefs']['feature_fullscreen'] == 'y' && $this->_tpl_vars['filegals_manager'] == '' && $this->_tpl_vars['print_page'] != 'y'): ?>
<?php if ($_SESSION['fullscreen'] == 'y'): ?>
<a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>
<?php if ($this->_tpl_vars['fsquery']): ?>?<?php echo ((is_array($_tmp=$this->_tpl_vars['fsquery'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url', "UTF-8") : smarty_modifier_escape($_tmp, 'url', "UTF-8")); ?>
&amp;<?php else: ?>?<?php endif; ?>fullscreen=n" class="menulink" id="fullscreenbutton"><?php echo smarty_function_icon(array('_id' => 'application_put','alt' => 'Cancel Fullscreen'), $this);?>
</a>
<?php else: ?>
<a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>
<?php if ($this->_tpl_vars['fsquery']): ?>?<?php echo ((is_array($_tmp=$this->_tpl_vars['fsquery'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url', "UTF-8") : smarty_modifier_escape($_tmp, 'url', "UTF-8")); ?>
&amp;<?php else: ?>?<?php endif; ?>fullscreen=y" class="menulink" id="fullscreenbutton"><?php echo smarty_function_icon(array('_id' => 'application_get','alt' => 'Fullscreen'), $this);?>
</a>
<?php endif; ?>
<?php endif; ?>