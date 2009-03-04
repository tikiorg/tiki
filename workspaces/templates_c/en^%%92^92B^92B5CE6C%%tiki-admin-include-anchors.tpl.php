<?php /* Smarty version 2.6.22, created on 2009-03-04 13:13:14
         compiled from tiki-admin-include-anchors.tpl */ ?>

<a href="tiki-admin.php?page=features" title="Features" class="icon"><img class="icon" src="pics/large/boot.png" alt="Features" width="32" height="32" /></a>

<a href="tiki-admin.php?page=general" title="General" class="icon"><img class="icon" src="pics/large/icon-configuration.png" alt="General" width="32" height="32" /></a>

<a href="tiki-admin.php?page=login" title="Login" class="icon"><img class="icon" src="pics/large/stock_quit.png" alt="Login" width="32" height="32" /></a>

<a href="tiki-admin.php?page=profiles" title="Profiles" class="icon"><img class="icon" src="pics/large/profiles.png" alt="Profiles" width="32" height="32" /></a>

<a href="tiki-admin.php?page=look" title="Customize look and feel of your Tiki" class="icon"><img class="icon" src="pics/large/gnome-settings-background.png" alt="Look &amp; Feel" /></a>

<a href="tiki-admin.php?page=i18n" title="i18n" class="icon"><img class="icon" src="pics/large/i18n.png" alt="i18n" width="32" height="32" /></a>

<a href="tiki-admin.php?page=textarea" title="Text area" class="icon"><img class="icon" src="img/icons/admin_textarea.png" alt="Text area" /></a>      

<a href="tiki-admin.php?page=module" title="Module" class="icon"><img class="icon" src="pics/large/display-capplet.png" alt="Module" /></a>   

<a href="tiki-admin.php?page=metatags" title="Meta Tags" class="icon"><img class="icon" src="pics/large/metatags.png" alt="Meta Tags" width="32" height="32" /></a>

<a href="tiki-admin.php?page=rss" title="RSS" class="icon"><img class="icon" src="pics/large/gnome-globe.png" alt="RSS" width="32" height="32" /></a>

<a href="tiki-admin.php?page=community" title="Community" class="icon"><img class="icon" src="pics/large/users.png" alt="Community" width="32" height="32" /></a>

<?php if ($this->_tpl_vars['prefs']['feature_wiki'] == 'y'): ?>
<a href="tiki-admin.php?page=wiki" title="Wiki" class="icon"><img class="icon" src="pics/large/wikipages.png" alt="Wiki" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_blogs'] == 'y'): ?>
<a href="tiki-admin.php?page=blogs" title="Blogs" class="icon"><img class="icon" src="pics/large/blogs.png" alt="Blogs" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_galleries'] == 'y'): ?>
<a href="tiki-admin.php?page=gal" title="Image Galleries" class="icon"><img class="icon" src="pics/large/stock_select-color.png" alt="Image Galleries" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_file_galleries'] == 'y'): ?>
<a href="tiki-admin.php?page=fgal" title="File Galleries" class="icon"><img class="icon" src="pics/large/file-manager.png" alt="File Galleries" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_articles'] == 'y'): ?>
<a href="tiki-admin.php?page=cms" title="Articles" class="icon"><img class="icon" src="pics/large/stock_bold.png" alt="Articles" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_forums'] == 'y'): ?>
<a href="tiki-admin.php?page=forums" title="Forums" class="icon"><img class="icon" src="pics/large/stock_index.png" alt="Forums" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_trackers'] == 'y'): ?>
<a href="tiki-admin.php?page=trackers" title="Trackers" class="icon"><img class="icon" src="pics/large/gnome-settings-font.png" alt="Trackers" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_polls'] == 'y'): ?>
<a href="tiki-admin.php?page=polls" title="Polls" class="icon"><img class="icon" src="pics/large/stock_missing-image.png" alt="Polls" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_calendar'] == 'y'): ?>
<a href="tiki-admin.php?page=calendar" title="Calendar" class="icon"><img class="icon" src="pics/large/date.png" alt="Calendar" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_categories'] == 'y'): ?>
<a href="tiki-admin.php?page=category" title="Category" class="icon"><img class="icon" src="pics/large/categories.png" alt="Category" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_score'] == 'y'): ?>
<a href="tiki-admin.php?page=score" title="Score" class="icon"><img class="icon" src="pics/large/stock_about.png" alt="Score" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_freetags'] == 'y'): ?>
<a href="tiki-admin.php?page=freetags" title="Freetags" class="icon"><img class="icon" src="pics/large/vcard.png" alt="Freetags" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_search'] == 'y'): ?>
<a href="tiki-admin.php?page=search" title="Search" class="icon"><img class="icon" src="pics/large/xfce4-appfinder.png" alt="Search" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_faqs'] == 'y'): ?>
<a href="tiki-admin.php?page=faqs" title="FAQs" class="icon"><img class="icon" src="pics/large/stock_dialog_question.png" alt="FAQs" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_directory'] == 'y'): ?>
<a href="tiki-admin.php?page=directory" title="Directory" class="icon"><img class="icon" src="pics/large/gnome-fs-server.png" alt="Directory" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_maps'] == 'y'): ?>
<a href="tiki-admin.php?page=maps" title="Maps" class="icon"><img class="icon" src="pics/large/maps.png" alt="Maps" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_gmap'] == 'y'): ?>
<a href="tiki-admin.php?page=gmap" title="Google Maps" class="icon"><img class="icon" src="pics/large/google_maps.png" alt="Google Maps" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_multimedia'] == 'y'): ?>
<a href="tiki-admin.php?page=multimedia" title="Multimedia" class="icon"><img class="icon" src="pics/large/multimedia.png" alt="Multimedia"  width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_copyright'] == 'y'): ?>
<a href="tiki-admin.php?page=copyright" title="Copyright" class="icon"><img class="icon" src="pics/large/copyright.png" alt="Copyright" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_messages'] == 'y'): ?>
<a href="tiki-admin.php?page=messages" title="Messages" class="icon"><img class="icon" src="pics/large/messages.png" alt="Messages" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_userfiles'] == 'y'): ?>
<a href="tiki-admin.php?page=userfiles" title="User files" class="icon"><img class="icon" src="pics/large/userfiles.png" alt="User files" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_webmail'] == 'y'): ?>
<a href="tiki-admin.php?page=webmail" title="Webmail" class="icon"><img class="icon" src="pics/large/evolution.png" alt="Webmail" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_wysiwyg'] == 'y'): ?>
<a href="tiki-admin.php?page=wysiwyg" title="Wysiwyg editor" class="icon"><img class="icon" src="pics/large/wysiwyg.png" alt="Wysiwyg editor" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_banners'] == 'y'): ?>
<a href="tiki-admin.php?page=ads" title="Site Ads and Banners" class="icon"><img class="icon" src="pics/large/ads.png" alt="Site Ads and Banners" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_intertiki'] == 'y'): ?>
<a href="tiki-admin.php?page=intertiki" title="Intertiki" class="icon"><img class="icon" src="pics/large/intertiki.png" alt="InterTiki" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_plugins'] != 'n'): ?>
<a href="tiki-admin.php?page=plugins" title="Plugin aliases" class="icon"><img class="icon" src="pics/large/stock_line-in.png" alt="Plugin aliases" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_semantic'] != 'n'): ?>
<a href="tiki-admin.php?page=semantic" title="Semantic wiki links" class="icon"><img class="icon" src="pics/large/semantic.png" alt="Semantic links" width="32" height="32" /></a>
<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_webservices'] != 'n'): ?>
<a href="tiki-admin.php?page=webservices" title="Webservices" class="icon"><img class="icon" src="pics/large/webservices.png" alt="Webservices" width="32" height="32" /></a>
<?php endif; ?>
<?php if ($this->_tpl_vars['prefs']['feature_sefurl'] != 'n'): ?>
<a href="tiki-admin.php?page=sefurl" title="Sef URL" class="icon"><img class="icon" src="pics/large/goto.png" alt="Sef URL" width="32" height="32" /></a>
<?php endif; ?>
