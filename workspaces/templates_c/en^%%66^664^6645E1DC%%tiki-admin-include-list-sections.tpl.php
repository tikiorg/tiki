<?php /* Smarty version 2.6.22, created on 2009-03-04 13:09:01
         compiled from tiki-admin-include-list-sections.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'remarksbox', 'tiki-admin-include-list-sections.tpl', 4, false),array('block', 'tr', 'tiki-admin-include-list-sections.tpl', 10, false),array('function', 'help', 'tiki-admin-include-list-sections.tpl', 11, false),)), $this); ?>



<?php $this->_tag_stack[] = array('remarksbox', array('type' => 'tip','title' => 'Tip')); $_block_repeat=true;smarty_block_remarksbox($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
	Enable/disable Tiki features in <a class="rbox-link" href="tiki-admin.php?page=features">Admin&nbsp;<?php echo $this->_tpl_vars['prefs']['site_crumb_seper']; ?>
&nbsp;Features</a>, but configure them elsewhere
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_remarksbox($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>

<div class="cbox">
 	<div class="cbox-title">
		<?php $this->_tag_stack[] = array('tr', array()); $_block_repeat=true;smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['crumbs'][$this->_tpl_vars['crumb']]->description; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
		<?php echo smarty_function_help(array('crumb' => $this->_tpl_vars['crumbs'][$this->_tpl_vars['crumb']]), $this);?>

	</div>

	<div class="clearfix cbox-data">

    <a href="tiki-admin.php?page=features" class="admbox" style="background-image: url('pics/large/boot48x48.png')">
      <img src="pics/trans.png" alt="Features" title="Features" /><span>Features</span>
    </a>
	
	<a href="tiki-admin.php?page=general" class="admbox" style="background-image: url('pics/large/icon-configuration48x48.png')">
			<img src="pics/trans.png" alt="General" title="General" /><span>General</span>
	</a>

    <a href="tiki-admin.php?page=login" class="admbox" style="background-image: url('pics/large/stock_quit48x48.png')">
      <img src="pics/trans.png" alt="Login" title="Login" /><span>Login</span>
    </a>

    <a href="tiki-admin.php?page=profiles" class="admbox" style="background-image: url('pics/large/profiles48x48.png')">
      <img src="pics/trans.png" alt="Profiles" title="Profiles" /><span>Profiles</span>
    </a>	
	
    <a href="tiki-admin.php?page=look" class="admbox" style="background-image: url('pics/large/gnome-settings-background48x48.png')">
      <img src="pics/trans.png" alt="Look &amp; Feel" title="Customize look and feel of your Tiki" /><span>Look &amp; Feel</span>
    </a>

    <a href="tiki-admin.php?page=i18n" class="admbox" style="background-image: url('pics/large/i18n48x48.png')">
      <img src="pics/trans.png" alt="i18n" title="i18n" /><span>i18n</span>
    </a>

	<a href="tiki-admin.php?page=textarea" class="admbox" style="background-image: url('img/icons/admin_textarea.png')">
      <img src="pics/trans.png" alt="Text Area" title="Text Area" /><span>Text Area</span>
    </a>

    <a href="tiki-admin.php?page=module" class="admbox" style="background-image: url('pics/large/display-capplet48x48.png')">
      <img src="pics/trans.png" alt="Module" title="Module" /><span>Module</span>
    </a>

    <a href="tiki-admin.php?page=metatags" class="admbox" style="background-image: url('pics/large/metatags48x48.png')">
      <img src="pics/trans.png" alt="Meta Tags" title="Meta Tags" /><span>Meta Tags</span>
    </a>

    <a href="tiki-admin.php?page=rss" class="admbox" style="background-image: url('pics/large/gnome-globe48x48.png')">
      <img src="pics/trans.png" alt="RSS" title="RSS" /><span>RSS</span>
    </a>

    <a href="tiki-admin.php?page=community" class="admbox" style="background-image: url('pics/large/users48x48.png')">
      <img src="pics/trans.png" alt="Community" title="Community" /><span>Community</span>
    </a>

    <a href="tiki-admin.php?page=wiki" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_wiki'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/wikipages48x48.png')">
      <img src="pics/trans.png" alt="Wiki" title="Wiki<?php if ($this->_tpl_vars['prefs']['feature_wiki'] != 'y'): ?> (Disabled)<?php endif; ?>" /><span>Wiki</span>
    </a>

    <a href="tiki-admin.php?page=blogs" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_blogs'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/blogs48x48.png')">
      <img src="pics/trans.png" alt="Blogs" title="Blogs<?php if ($this->_tpl_vars['prefs']['feature_blogs'] != 'y'): ?> (Disabled)<?php endif; ?>" /><span>Blogs</span>
    </a>
	
    <a href="tiki-admin.php?page=gal" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_galleries'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/stock_select-color48x48.png')">
      <img src="pics/trans.png" alt="Image Galleries" title="Image Galleries<?php if ($this->_tpl_vars['prefs']['feature_galleries'] != 'y'): ?> (Disabled)<?php endif; ?>" /><span>Image Galleries</span>
    </a>

    <a href="tiki-admin.php?page=fgal" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_file_galleries'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/file-manager48x48.png')">
      <img src="pics/trans.png" alt="File Galleries" title="File Galleries<?php if ($this->_tpl_vars['prefs']['feature_file_galleries'] != 'y'): ?> (Disabled)<?php endif; ?>" /><span>File Galleries</span>
    </a>

    <a href="tiki-admin.php?page=cms" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_articles'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/stock_bold48x48.png')">
      <img src="pics/trans.png" alt="Articles" title="Articles<?php if ($this->_tpl_vars['prefs']['feature_articles'] != 'y'): ?> (Disabled)<?php endif; ?>" /><span>Articles</span>
    </a>        

    <a href="tiki-admin.php?page=forums" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_forums'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/stock_index48x48.png')">
      <img src="pics/trans.png" alt="Forums" title="Forums<?php if ($this->_tpl_vars['prefs']['feature_forums'] != 'y'): ?> (Disabled)<?php endif; ?>" /><span>Forums</span>
    </a>

    <a href="tiki-admin.php?page=trackers" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_trackers'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/gnome-settings-font48x48.png')">
      <img src="pics/trans.png" alt="Trackers" title="Trackers<?php if ($this->_tpl_vars['prefs']['feature_trackers'] != 'y'): ?> (Disabled)<?php endif; ?>" /><span>Trackers</span>
    </a>

    <a href="tiki-admin.php?page=polls" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_polls'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/stock_missing-image48x48.png')">
      <img src="pics/trans.png" alt="Polls" title="Polls<?php if ($this->_tpl_vars['prefs']['feature_polls'] != 'y'): ?> (Disabled)<?php endif; ?>" /><span>Polls</span>
    </a>
	
    <a href="tiki-admin.php?page=calendar" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_calendar'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/date48x48.png')">
      <img src="pics/trans.png" alt="Calendar" title="Calendar<?php if ($this->_tpl_vars['prefs']['feature_calendar'] != 'y'): ?> (Disabled)<?php endif; ?>" /><span>Calendar</span>
    </a>

    <a href="tiki-admin.php?page=category" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_categories'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/categories48x48.png')">
      <img src="pics/trans.png" alt="Categories" title="Categories<?php if ($this->_tpl_vars['prefs']['feature_categories'] != 'y'): ?> (Disabled)<?php endif; ?>" /><span>Categories</span>
    </a>

    <a href="tiki-admin.php?page=score" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_score'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/stock_about48x48.png')">
      <img src="pics/trans.png" alt="Score" title="Score<?php if ($this->_tpl_vars['prefs']['feature_score'] != 'y'): ?> (Disabled)<?php endif; ?>" /><span>Score</span>
    </a>

    <a href="tiki-admin.php?page=freetags" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_freetags'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/vcard48x48.png')">
      <img src="pics/trans.png" alt="Freetags" title="Freetags<?php if ($this->_tpl_vars['prefs']['feature_freetags'] != 'y'): ?> (Disabled)<?php endif; ?>" /><span>Freetags</span>
    </a>

    <a href="tiki-admin.php?page=search" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_search'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/xfce4-appfinder48x48.png')">
      <img src="pics/trans.png" alt="Search" title="Search<?php if ($this->_tpl_vars['prefs']['feature_search'] != 'y'): ?> (Disabled)<?php endif; ?>" /><span>Search</span>
    </a>
	
    <a href="tiki-admin.php?page=faqs" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_faqs'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/stock_dialog_question48x48.png')">
      <img src="pics/trans.png" alt="FAQs" title="FAQs<?php if ($this->_tpl_vars['prefs']['feature_faqs'] != 'y'): ?> (Disabled)<?php endif; ?>" /><span>FAQs</span>
    </a>

    <a href="tiki-admin.php?page=directory" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_directory'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/gnome-fs-server48x48.png')">
      <img src="pics/trans.png" alt="Directory" title="Directory<?php if ($this->_tpl_vars['prefs']['feature_directory'] != 'y'): ?> (Disabled)<?php endif; ?>" /><span>Directory</span>
    </a>

    <a href="tiki-admin.php?page=maps" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_maps'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/maps48x48.png')">
      <img src="pics/trans.png" alt="Maps" title="Maps<?php if ($this->_tpl_vars['prefs']['feature_maps'] != 'y'): ?> (Disabled)<?php endif; ?>" /><span>Maps</span>
    </a>
	
    <a href="tiki-admin.php?page=gmap" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_gmap'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/google_maps48x48.png')">
      <img src="pics/trans.png" alt="Google Maps" title="Google Maps<?php if ($this->_tpl_vars['prefs']['feature_gmap'] != 'y'): ?> (Disabled)<?php endif; ?>" /><span>Google Maps</span>
    </a>
	<a href="tiki-admin.php?page=multimedia" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_multimedia'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/multimedia48x48.png')">
      <img src="pics/trans.png" alt="Multimedia" title="Multimedia" /><span>Multimedia</span>
    </a>

    <a href="tiki-admin.php?page=copyright" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_copyright'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/copyright48x48.png')">
      <img src="pics/trans.png" alt="Copyright" title="Copyright<?php if ($this->_tpl_vars['prefs']['feature_copyright'] != 'y'): ?> (Disabled)<?php endif; ?>" /><span>Copyright</span>
    </a>

	<a href="tiki-admin.php?page=messages" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_messages'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/messages48x48.png')">
			<img src="pics/trans.png" alt="Messages" title="Messages<?php if ($this->_tpl_vars['prefs']['feature_messages'] != 'y'): ?> (Disabled)<?php endif; ?>" /><span>Messages</span>
	</a>
	
    <a href="tiki-admin.php?page=userfiles" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_userfiles'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/userfiles48x48.png')">
      <img src="pics/trans.png" alt="User files" title="User files<?php if ($this->_tpl_vars['prefs']['feature_userfiles'] != 'y'): ?> (Disabled)<?php endif; ?>" /><span>User files</span>
    </a>	
	
    <a href="tiki-admin.php?page=webmail" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_webmail'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/evolution48x48.png')">
      <img src="pics/trans.png" alt="Webmail" title="Webmail<?php if ($this->_tpl_vars['prefs']['feature_webmail'] != 'y'): ?> (Disabled)<?php endif; ?>" /><span>Webmail</span>
    </a>
	
    <a href="tiki-admin.php?page=wysiwyg" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_wysiwyg'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/wysiwyg48x48.png')">
      <img src="pics/trans.png" alt="Wysiwyg" title="Wysiwyg<?php if ($this->_tpl_vars['prefs']['feature_wysiwyg'] != 'y'): ?> (Disabled)<?php endif; ?>" /><span>Wysiwyg</span>
    </a>	
	
	<a href="tiki-admin.php?page=ads" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_banners'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/ads48x48.png')">
			<img src="pics/trans.png" alt="Site Ads and Banners" title="Site Ads and Banners" <?php if ($this->_tpl_vars['prefs']['feature_banners'] != 'y'): ?> (Disabled)<?php endif; ?>/><span>Site Ads and Banners</span>
	</a>

	<a href="tiki-admin.php?page=intertiki" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_intertiki'] != 'y'): ?> off<?php endif; ?>" style="background-image: url('pics/large/intertiki48x48.png')">
      <img src="pics/trans.png" alt="InterTiki" title="InterTiki<?php if ($this->_tpl_vars['prefs']['feature_intertiki'] != 'y'): ?> (Disabled)<?php endif; ?>" /><span>InterTiki</span>
    </a>
	
	<a href="tiki-admin.php?page=plugins" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_plugins'] == 'n'): ?> off<?php endif; ?>" style="background-image: url('pics/large/stock_line-in48x48.png')">
      <img src="pics/trans.png" alt="Plugin aliases" title="Plugin aliases<?php if ($this->_tpl_vars['prefs']['feature_plugins'] == 'n'): ?> (Disabled)<?php endif; ?>" /><span>Plugin aliases</span>
    </a>

	<a href="tiki-admin.php?page=semantic" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_semantic'] == 'n'): ?> off<?php endif; ?>" style="background-image: url('pics/large/semantic48x48.png')">
      <img src="pics/trans.png" alt="Semantic" title="Semantic wiki links<?php if ($this->_tpl_vars['prefs']['feature_semantic'] == 'n'): ?> (Disabled)<?php endif; ?>" /><span>Semantic links</span>
    </a>

	<a href="tiki-admin.php?page=webservices" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_webservices'] == 'n'): ?> off<?php endif; ?>" style="background-image: url('pics/large/webservices48x48.png')">
      <img src="pics/trans.png" alt="Webservices" title="Webservices management<?php if ($this->_tpl_vars['prefs']['feature_webservices'] == 'n'): ?> (Disabled)<?php endif; ?>" /><span>Webservices</span>
    </a>

	<a href="tiki-admin.php?page=sefurl" class="admbox<?php if ($this->_tpl_vars['prefs']['feature_sefurl'] == 'n'): ?> off<?php endif; ?>" style="background-image: url('pics/large/goto48x48.png')">
      <img src="pics/trans.png" alt="Search engine friendly url" title="Search engine friendly url<?php if ($this->_tpl_vars['prefs']['feature_sefurl'] == 'n'): ?> (Disabled)<?php endif; ?>" /><span>Search engine friendly url</span>
    </a>
	</div>
</div>

