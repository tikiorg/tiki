<?php /* Smarty version 2.6.22, created on 2009-03-04 13:06:22
         compiled from tiki-bot_bar.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'eval', 'tiki-bot_bar.tpl', 1, false),array('function', 'icon', 'tiki-bot_bar.tpl', 31, false),array('function', 'elapsed', 'tiki-bot_bar.tpl', 82, false),array('function', 'memusage', 'tiki-bot_bar.tpl', 82, false),array('modifier', 'escape', 'tiki-bot_bar.tpl', 5, false),array('modifier', 'date_format', 'tiki-bot_bar.tpl', 73, false),array('modifier', 'truncate', 'tiki-bot_bar.tpl', 82, false),array('modifier', 'tiki_long_datetime', 'tiki-bot_bar.tpl', 87, false),)), $this); ?>
<?php if ($this->_tpl_vars['prefs']['feature_bot_logo'] == 'y'): ?><?php echo smarty_function_eval(array('var' => $this->_tpl_vars['prefs']['bot_logo_code']), $this);?>
<?php endif; ?>
<?php if (( $this->_tpl_vars['prefs']['feature_site_report'] == 'y' && $this->_tpl_vars['tiki_p_site_report'] == 'y' ) || ( $this->_tpl_vars['prefs']['feature_site_send_link'] == 'y' && $this->_tpl_vars['prefs']['feature_tell_a_friend'] == 'y' && $this->_tpl_vars['tiki_p_tell_a_friend'] == 'y' )): ?>
	<div id="site_report">
		<?php if ($this->_tpl_vars['prefs']['feature_site_report'] == 'y'): ?>
			<a href="tiki-tell_a_friend.php?report=y&amp;url=<?php echo ((is_array($_tmp=$_SERVER['REQUEST_URI'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
">Report to Webmaster</a>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['prefs']['feature_site_send_link'] == 'y'): ?>
			<a href="tiki-tell_a_friend.php?url=<?php echo ((is_array($_tmp=$_SERVER['REQUEST_URI'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
">Email this page</a>
		<?php endif; ?>
	</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['prefs']['feature_bot_bar_icons'] == 'y'): ?>
	<div id="power_icons">
		<a href="http://tikiwiki.org/" title="Tikiwiki"><img alt="Powered by Tikiwiki" src="img/tiki/tikibutton2.png" /></a>
		<a href="http://www.php.net/" title="PHP"><img alt="Powered by PHP" src="img/php.png" /></a>
		<a href="http://smarty.php.net/" title="Smarty"><img alt="Powered by Smarty" src="img/smarty.gif"  /></a>
		<a href="http://adodb.sourceforge.net/" title="ADOdb"><img alt="Powered by ADOdb" src="img/adodb.png" /></a>
		<a href="http://www.w3.org/Style/CSS/" title="CSS"><img alt="Made with CSS" src="img/css1.png" /></a>
		<a href="http://www.w3.org/RDF/" title="RDF"><img alt="Powered by RDF" src="img/rdf.gif"  /></a>
		<?php if ($this->_tpl_vars['prefs']['feature_phplayers'] == 'y'): ?>
		<a href="http://phplayersmenu.sourceforge.net/" title="PHP Layers Menu"><img alt="powered by The PHP Layers Menu System" src="lib/phplayers/LOGOS/powered_by_phplm.png"  /></a>		
		<?php endif; ?>
		<?php if ($this->_tpl_vars['prefs']['feature_mobile'] == 'y'): ?>
		<a href="http://www.hawhaw.de/" title="HAWHAW"><img alt="powered by HAWHAW" src="img/poweredbyhawhaw.gif"  /></a>		
		<?php endif; ?>		
	</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['prefs']['feature_bot_bar_rss'] == 'y'): ?>
	<div id="rss" style="text-align: center">
		<?php if ($this->_tpl_vars['prefs']['feature_wiki'] == 'y' && $this->_tpl_vars['prefs']['rss_wiki'] == 'y' && $this->_tpl_vars['tiki_p_view'] == 'y'): ?>
				<a title="Wiki RSS" href="tiki-wiki_rss.php?ver=<?php echo $this->_tpl_vars['prefs']['rssfeed_default_version']; ?>
"><?php echo smarty_function_icon(array('style' => 'vertical-align: text-bottom;','_id' => 'feed','alt' => 'RSS feed'), $this);?>
</a>
				<small>Wiki</small>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['prefs']['feature_blogs'] == 'y' && $this->_tpl_vars['prefs']['rss_blogs'] == 'y' && $this->_tpl_vars['tiki_p_read_blog'] == 'y'): ?>
				<a title="Blogs RSS" href="tiki-blogs_rss.php?ver=<?php echo $this->_tpl_vars['prefs']['rssfeed_default_version']; ?>
"><?php echo smarty_function_icon(array('style' => 'vertical-align: text-bottom;','_id' => 'feed','alt' => 'RSS feed'), $this);?>
</a>
				<small>Blogs</small>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['prefs']['feature_articles'] == 'y' && $this->_tpl_vars['prefs']['rss_articles'] == 'y' && $this->_tpl_vars['tiki_p_read_article'] == 'y'): ?>
				<a title="Articles RSS" href="tiki-articles_rss.php?ver=<?php echo $this->_tpl_vars['prefs']['rssfeed_default_version']; ?>
"><?php echo smarty_function_icon(array('style' => 'vertical-align: text-bottom;','_id' => 'feed','alt' => 'RSS feed'), $this);?>
</a>
				<small>Articles</small>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['prefs']['feature_galleries'] == 'y' && $this->_tpl_vars['prefs']['rss_image_galleries'] == 'y' && $this->_tpl_vars['tiki_p_view_image_gallery'] == 'y'): ?>
				<a title="Image Galleries RSS" href="tiki-image_galleries_rss.php?ver=<?php echo $this->_tpl_vars['prefs']['rssfeed_default_version']; ?>
"><?php echo smarty_function_icon(array('style' => 'vertical-align: text-bottom;','_id' => 'feed','alt' => 'RSS feed'), $this);?>
</a>
				<small>Image Galleries</small>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['prefs']['feature_file_galleries'] == 'y' && $this->_tpl_vars['prefs']['rss_file_galleries'] == 'y' && $this->_tpl_vars['tiki_p_view_file_gallery'] == 'y'): ?>
				<a title="File Galleries RSS" href="tiki-file_galleries_rss.php?ver=<?php echo $this->_tpl_vars['prefs']['rssfeed_default_version']; ?>
"><?php echo smarty_function_icon(array('style' => 'vertical-align: text-bottom;','_id' => 'feed','alt' => 'RSS feed'), $this);?>
</a>
				<small>File Galleries</small>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['prefs']['feature_forums'] == 'y' && $this->_tpl_vars['prefs']['rss_forums'] == 'y' && $this->_tpl_vars['tiki_p_forum_read'] == 'y'): ?>
				<a title="Forums RSS" href="tiki-forums_rss.php?ver=<?php echo $this->_tpl_vars['prefs']['rssfeed_default_version']; ?>
"><?php echo smarty_function_icon(array('style' => 'vertical-align: text-bottom;','_id' => 'feed','alt' => 'RSS feed'), $this);?>
</a>
				<small>Forums</small>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['prefs']['feature_maps'] == 'y' && $this->_tpl_vars['prefs']['rss_mapfiles'] == 'y' && $this->_tpl_vars['tiki_p_map_view'] == 'y'): ?>
				<a title="Maps RSS" href="tiki-map_rss.php?ver=<?php echo $this->_tpl_vars['prefs']['rssfeed_default_version']; ?>
"><?php echo smarty_function_icon(array('style' => 'vertical-align: text-bottom;','_id' => 'feed','alt' => 'RSS feed'), $this);?>
</a>
				<small>Maps</small>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['prefs']['feature_directory'] == 'y' && $this->_tpl_vars['prefs']['rss_directories'] == 'y' && $this->_tpl_vars['tiki_p_view_directory'] == 'y'): ?>
				<a href="tiki-directories_rss.php?ver=<?php echo $this->_tpl_vars['prefs']['rssfeed_default_version']; ?>
"><?php echo smarty_function_icon(array('style' => 'vertical-align: text-bottom;','_id' => 'feed','alt' => 'RSS feed'), $this);?>
</a>
				<small>Directories</small>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['prefs']['feature_calendar'] == 'y' && $this->_tpl_vars['prefs']['rss_calendar'] == 'y' && $this->_tpl_vars['tiki_p_view_calendar'] == 'y'): ?>
				<a href="tiki-calendars_rss.php?ver=<?php echo $this->_tpl_vars['prefs']['rssfeed_default_version']; ?>
"><?php echo smarty_function_icon(array('style' => 'vertical-align: text-bottom;','_id' => 'feed','alt' => 'RSS feed'), $this);?>
</a>
				<small>Calendars</small>
		<?php endif; ?>
	</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['prefs']['feature_babelfish'] == 'y' || $this->_tpl_vars['prefs']['feature_babelfish_logo'] == 'y'): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "babelfish.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<div id="power">
	<?php if ($this->_tpl_vars['prefs']['feature_bot_bar_power_by_tw'] != 'n'): ?>
		Powered by <a href="http://info.tikiwiki.org" title="&#169; 2002&#8211;<?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%Y") : smarty_modifier_date_format($_tmp, "%Y")); ?>
 The TikiWiki Community">TikiWiki CMS/Groupware</a> <?php if ($this->_tpl_vars['prefs']['feature_topbar_version'] == 'y'): ?> v<?php echo $this->_tpl_vars['tiki_version']; ?>
 <?php if ($this->_tpl_vars['tiki_uses_cvs'] == 'y'): ?> (CVS)<?php endif; ?> -<?php echo $this->_tpl_vars['tiki_star']; ?>
- <?php endif; ?>
	<?php endif; ?>
	<div id="credits">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "credits.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
</div>

<?php if ($this->_tpl_vars['prefs']['feature_bot_bar_debug'] == 'y'): ?>
<div id="loadstats" style="text-align: center">
	<small>[ Execution time: <?php echo smarty_function_elapsed(array(), $this);?>
 secs ] &nbsp; [ Memory usage: <?php echo smarty_function_memusage(array(), $this);?>
 ] &nbsp; [ <?php echo $this->_tpl_vars['num_queries']; ?>
 database queries used in  <?php echo ((is_array($_tmp=$this->_tpl_vars['elapsed_in_db'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 3, '') : smarty_modifier_truncate($_tmp, 3, '')); ?>
 secs ]<?php if ($this->_tpl_vars['server_load'] && $this->_tpl_vars['server_load'] != '?'): ?> &nbsp; [ Server load: <?php echo $this->_tpl_vars['server_load']; ?>
 ]<?php endif; ?></small>
</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['lastup']): ?>
<div class="cvsup" style="font-size:x-small;text-align:center;color:#999;">Last update from SVN(<?php echo $this->_tpl_vars['prefs']['tiki_version']; ?>
): <?php echo ((is_array($_tmp=$this->_tpl_vars['lastup'])) ? $this->_run_mod_handler('tiki_long_datetime', true, $_tmp) : smarty_modifier_tiki_long_datetime($_tmp)); ?>
</div>
<?php endif; ?>