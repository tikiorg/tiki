<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/prefs.php,v 1.16.2.43 2008-02-14 19:47:15 lphuberdeau Exp $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

// RULE1: $prefs does not contain serialized values. Only the database contains serialized values.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

// Initialize prefs for which we want to use the site value (they will be prefixed with 'site_')
// ( this is also used in tikilib, not only when reloading prefs )
$user_overrider_prefs = array('language', 'style', 'userbreadCrumb', 'tikiIndex', 'wikiHomePage');

// Check if prefs needs to be reloaded
if (isset($_SESSION['s_prefs'])) {

	// Reload if there was an update of some prefs
	$lastUpdatePrefs = $tikilib->getOne("select `value` from `tiki_preferences` where `name`=?", array('lastUpdatePrefs'));
	if ( ! isset($lastUpdatePrefs) ) {
		$tikilib->query("insert into `tiki_preferences` (`name`,`value`) values (?,?)", array('lastUpdatePrefs', 0));
	}
	if ( empty($_SESSION['s_prefs']['lastReadingPrefs']) || $lastUpdatePrefs > $_SESSION['s_prefs']['lastReadingPrefs'] ) {
		$_SESSION['need_reload_prefs'] = true;
	}

	// Reload if the virtual host or tikiroot has changed
	//   (this is needed when using the same php sessions for more than one tiki)
	if ( $_SESSION['lastPrefsSite'] != $_SERVER['SERVER_NAME'].'|'.$tikiroot ) {
		$_SESSION['lastPrefsSite'] = $_SERVER['SERVER_NAME'].'|'.$tikiroot;
		$_SESSION['need_reload_prefs'] = true;
	}

} else $_SESSION['need_reload_prefs'] = true;

// Set default prefs only if needed
if ( ! $_SESSION['need_reload_prefs'] ) {
	$prefs = $_SESSION['s_prefs'];
} else {
	$prefs=array();

	# tiki and version
	$prefs['tiki_release'] = '0';
	$prefs['feature_version_checks'] = 'y';
	$prefs['tiki_needs_upgrade'] = 'n';
	$prefs['tiki_version_last_check'] = 0;
	$prefs['tiki_version_check_frequency'] = 604800;

	# wiki
	$prefs['feature_wiki'] = 'y';
	$prefs['default_wiki_diff_style'] = 'minsidediff';
	$prefs['feature_backlinks'] = 'y';
	$prefs['feature_dump'] = 'y';
	$prefs['feature_history'] = 'y';
	$prefs['feature_lastChanges'] = 'y';
	$prefs['feature_likePages'] = 'y';
	$prefs['feature_listPages'] = 'y';
	$prefs['feature_page_title'] = 'y';
	$prefs['feature_sandbox'] = 'y';
	$prefs['feature_warn_on_edit'] = 'n';
	$prefs['feature_wiki_1like_redirection'] = 'y';
	$prefs['feature_wiki_allowhtml'] = 'n';
	$prefs['feature_wiki_attachments'] = 'n';
	$prefs['feature_wiki_comments'] = 'n';
	$prefs['feature_wiki_description'] = 'n';
	$prefs['feature_wiki_discuss'] = 'n';
	$prefs['feature_wiki_export'] = 'y';
	$prefs['feature_wiki_import_page'] = 'n';
	$prefs['feature_wiki_footnotes'] = 'n';
	$prefs['feature_wiki_icache'] = 'n';
	$prefs['feature_wiki_import_html'] = 'n';
	$prefs['feature_wiki_monosp'] = 'n';
	$prefs['feature_wiki_multiprint'] = 'n';
	$prefs['feature_wiki_notepad'] = 'n';
	$prefs['feature_wiki_make_structure'] = 'n';
	$prefs['feature_wiki_open_as_structure'] = 'n';
	$prefs['feature_wiki_pageid'] = 'n';
	$prefs['feature_wiki_paragraph_formatting'] = 'n';
	$prefs['feature_wiki_paragraph_formatting_add_br'] = 'n';
	// $prefs['feature_wiki_pdf'] = 'n';
	$prefs['feature_wiki_pictures'] = 'n';
	$prefs['feature_wiki_plurals'] = 'y';
	$prefs['feature_wiki_print'] = 'y';
	$prefs['feature_wiki_protect_email'] = 'n';
	$prefs['feature_wiki_rankings'] = 'y';
	$prefs['feature_wiki_ratings'] = 'n';
	$prefs['feature_wiki_replace'] = 'n';
	$prefs['feature_wiki_show_hide_before'] = 'n';
	$prefs['feature_wiki_tables'] = 'new';
	$prefs['feature_wiki_templates'] = 'n';
	$prefs['feature_wiki_undo'] = 'n';
	$prefs['feature_wiki_userpage'] = 'y';
	$prefs['feature_wiki_userpage_prefix'] = 'UserPage';
	$prefs['feature_wiki_usrlock'] = 'n';
	$prefs['feature_wikiwords'] = 'y';
	$prefs['feature_wikiwords_usedash'] = 'y';
	$prefs['mailin_autocheck'] = 'n';
	$prefs['mailin_autocheckFreq'] = '0';
	$prefs['mailin_autocheckLast'] = 0;
	$prefs['warn_on_edit_time'] = 2;
	$prefs['wikiHomePage'] = 'HomePage';
	$prefs['wikiLicensePage'] = '';
	$prefs['wikiSubmitNotice'] = '';
	$prefs['wiki_authors_style'] = 'classic';
	$prefs['wiki_show_version'] = 'n';
	$prefs['wiki_bot_bar'] = 'n';
	$prefs['wiki_cache'] = 0;
	$prefs['wiki_comments_default_ordering'] = 'points_desc';
	$prefs['wiki_comments_per_page'] = 10;
	$prefs['wiki_creator_admin'] = 'n';
	$prefs['wiki_feature_copyrights'] = 'n';
	$prefs['wiki_forum_id'] = '';
	$prefs['wiki_left_column'] = 'y';
	$prefs['wiki_list_backlinks'] = 'y';
	$prefs['wiki_list_comment'] = 'y';
	$prefs['wiki_list_creator'] = 'y';
	$prefs['wiki_list_hits'] = 'y';
	$prefs['wiki_list_lastmodif'] = 'y';
	$prefs['wiki_list_lastver'] = 'y';
	$prefs['wiki_list_links'] = 'y';
	$prefs['wiki_list_name'] = 'y';
	$prefs['wiki_list_name_len'] = '40';
	$prefs['wiki_list_size'] = 'y';
	$prefs['wiki_list_status'] = 'y';
	$prefs['wiki_list_user'] = 'y';
	$prefs['wiki_list_versions'] = 'y';
	$prefs['wiki_list_language'] = 'n';
	$prefs['wiki_list_categories'] = 'n';
	$prefs['wiki_list_categories_path'] = 'n';
	$prefs['wiki_list_id'] = 'n';
	$prefs['wiki_page_regex'] = 'strict';
	$prefs['wiki_page_separator'] = '...page...';
	$prefs['wiki_page_navigation_bar'] = 'bottom';
	$prefs['wiki_pagename_strip'] = '';
	$prefs['wiki_right_column'] = 'y';
	$prefs['wiki_top_bar'] = 'y';
	$prefs['wiki_uses_slides'] = 'n';
	$prefs['wiki_watch_author'] = 'n';
	$prefs['wiki_watch_comments'] = 'y';
	$prefs['wiki_watch_editor'] = 'n';
	$prefs['wiki_watch_minor'] = 'y';
	$prefs['feature_wiki_history_full'] = 'n';
	$prefs['feature_wiki_categorize_structure'] = 'n';
	$prefs['feature_wiki_watch_structure'] = 'n';
	$prefs['feature_wikiapproval'] = 'n';
	$prefs['wikiapproval_prefix'] = '*';

	# wysiwyg
	$prefs['feature_wysiwyg'] = 'n';
	$prefs['wysiwyg_optional'] = 'y';
	$prefs['wysiwyg_default'] = 'y';
	$prefs['wysiwyg_wiki_parsed'] = 'y';
	$prefs['wysiwyg_wiki_semi_parsed'] = 'y';
	$prefs['wysiwyg_toolbar_skin'] = 'default';
	$prefs['wysiwyg_toolbar'] ="FitWindow,Templates,-,Cut,Copy,Paste,PasteText,PasteWord,Print,SpellCheck
	Undo,Redo,-,Find,Replace,SelectAll,RemoveFormat,-,Table,Rule,Smiley,SpecialChar,PageBreak,ShowBlocks
	/
	JustifyLeft,JustifyCenter,JustifyRight,JustifyFull,-,OrderedList,UnorderedList,Outdent,Indent,Blockquote
	Bold,Italic,Underline,StrikeThrough,-,Subscript,Superscript,-,tikilink,Link,Unlink,Anchor,-,tikiimage,Flash
	/
	Style,FontName,FontSize,-,TextColor,BGColor,-,Source";

	# wiki3d
	$prefs['wiki_feature_3d'] = 'n';
	$prefs['wiki_3d_width'] = 500;
	$prefs['wiki_3d_height'] = 500;
	$prefs['wiki_3d_navigation_depth'] = 1;
	$prefs['wiki_3d_feed_animation_interval'] = 500;
	$prefs['wiki_3d_existing_page_color'] = '#00CC55';
	$prefs['wiki_3d_missing_page_color'] = '#FF5555';

	# blogs
	$prefs['feature_blogs'] = 'n';
	$prefs['blog_list_order'] = 'created_desc';
	$prefs['home_blog'] = 0;
	$prefs['feature_blog_rankings'] = 'y';
	$prefs['feature_blog_comments'] = 'n';
	$prefs['blog_comments_default_ordering'] = 'points_desc';
	$prefs['blog_comments_per_page'] = 10;
	$prefs['feature_blogposts_comments'] = 'n';
	$prefs['blog_list_user'] = 'text';
	$prefs['blog_list_title'] = 'y';
	$prefs['blog_list_title_len'] = '35';
	$prefs['blog_list_description'] = 'y';
	$prefs['blog_list_created'] = 'y';
	$prefs['blog_list_lastmodif'] = 'y';
	$prefs['blog_list_posts'] = 'y';
	$prefs['blog_list_visits'] = 'y';
	$prefs['blog_list_activity'] = 'y';
	$prefs['feature_blog_mandatory_category'] = '-1';
	$prefs['feature_blog_heading'] = 'y';

	# filegals
	$prefs['feature_file_galleries'] = 'n';
	$prefs['home_file_gallery'] = 0;
	$prefs['fgal_use_db'] = 'y';
	$prefs['fgal_batch_dir'] = '';
	$prefs['fgal_match_regex'] = '';
	$prefs['fgal_nmatch_regex'] = '';
	$prefs['fgal_use_dir'] = '';
	$prefs['fgal_podcast_dir'] = 'files';
	$prefs['feature_file_galleries_comments'] = 'n';
	$prefs['file_galleries_comments_default_ordering'] = 'points_desc';
	$prefs['file_galleries_comments_per_page'] = 10;
	$prefs['feature_file_galleries_batch'] = 'n';
	$prefs['feature_file_galleries_rankings'] = 'n';
	$prefs['fgal_list_id'] = 'n';
	$prefs['fgal_list_name'] = 'y';
	$prefs['fgal_list_description'] = 'y';
	$prefs['fgal_list_created'] = 'y';
	$prefs['fgal_list_lastmodif'] = 'y';
	$prefs['fgal_list_user'] = 'y';
	$prefs['fgal_list_files'] = 'y';
	$prefs['fgal_list_hits'] = 'y';
	$prefs['fgal_enable_auto_indexing'] = 'y';
	$prefs['fgal_allow_duplicates'] = 'n';
	$prefs['fgal_list_parent'] = 'n';
	$prefs['fgal_list_type'] = 'n';
	$prefs['fgal_sort_mode'] = 'created_desc';
	$prefs['feature_file_galleries_author'] = 'n';

	# imagegals
	$prefs['feature_galleries'] = 'n';
	$prefs['feature_gal_batch'] = 'n';
	$prefs['feature_gal_slideshow'] = 'n';
	$prefs['home_gallery'] = 0;
	$prefs['gal_use_db'] = 'y';
	$prefs['gal_use_lib'] = 'gd';
	$prefs['gal_match_regex'] = '';
	$prefs['gal_nmatch_regex'] = '';
	$prefs['gal_use_dir'] = '';
	$prefs['gal_batch_dir'] = '';
	$prefs['feature_gal_rankings'] = 'y';
	$prefs['feature_image_galleries_comments'] = 'n';
	$prefs['image_galleries_comments_default_order'] = 'points_desc';
	$prefs['image_galleries_comments_per_page'] = 10;
	$prefs['gal_list_name'] = 'y';
	$prefs['gal_list_description'] = 'y';
	$prefs['gal_list_created'] = 'y';
	$prefs['gal_list_lastmodif'] = 'y';
	$prefs['gal_list_user'] = 'y';
	$prefs['gal_list_imgs'] = 'y';
	$prefs['gal_list_visits'] = 'y';
	$prefs['feature_image_gallery_mandatory_category'] = '-1';
	$prefs['preset_galleries_thumb'] ='n';
	$prefs['gal_image_mouseover'] = 'n';

	# multimedia
	$prefs['ProgressBarPlay']='#FF8D41';
	$prefs['ProgressBarLoad']="#A7A7A7";
	$prefs['ProgressBarButton']="#FF0000";
	$prefs['ProgressBar']="#C3C3C3";
	$prefs['VolumeOn']="#21AC2A";
	$prefs['VolumeOff']="#8EFF8A";
	$prefs['VolumeButton']=0;
	$prefs['Button']="#555555";
	$prefs['ButtonPressed']="#FF00FF";
	$prefs['ButtonOver']="#B3B3B3";
	$prefs['ButtonInfo']="#C3C3C3";
	$prefs['ButtonInfoPressed']="#555555";
	$prefs['ButtonInfoOver']="#FF8D41";
	$prefs['ButtonInfoText']="#FFFFFF";
	$prefs['ID3']="#6CDCEB";
	$prefs['PlayTime']="#00FF00";
	$prefs['TotalTime']="#FF2020";
	$prefs['PanelDisplay']="#555555";
	$prefs['AlertMesg']="#00FFFF";
	$prefs['PreloadDelay']=3;
	$prefs['VideoHeight']=240;
	$prefs['VideoLength']=300;
	$prefs['ProgressBarPlay']="#FFFFFF";
	$prefs['URLAppend']="";
	$prefs['LimitedMsg']="You are limited to 1 minute";
	$prefs['MaxPlay']=60;
	$prefs['MultimediaGalerie']=1;
	$prefs['MultimediaDefaultLength']=200;
	$prefs['MultimediaDefaultHeight']=100;

	# spellcheck
	if ( file_exists('lib/bablotron.php') ) {
		$prefs['lib_spellcheck'] = 'y';
		$prefs['wiki_spellcheck'] = 'n';
		$prefs['cms_spellcheck'] = 'n';
		$prefs['blog_spellcheck'] = 'n';
	}

	# forums
	$prefs['feature_forums'] = 'n';
	$prefs['home_forum'] = 0;
	$prefs['feature_forum_rankings'] = 'y';
	$prefs['feature_forum_parse'] = 'n';
	$prefs['feature_forum_topics_archiving'] = 'n';
	$prefs['feature_forum_replyempty'] = 'n';
	$prefs['feature_forum_quickjump'] = 'n';
	$prefs['feature_forum_topicd'] = 'y';
	$prefs['feature_forums_allow_thread_titles'] = 'n';
	$prefs['feature_forum_content_search'] = 'y';
	$prefs['feature_forums_name_search'] = 'y';
	$prefs['forums_ordering'] = 'created_desc';
	$prefs['forum_list_topics'] =  'y';
	$prefs['forum_list_posts'] =  'y';
	$prefs['forum_list_ppd'] =  'y';
	$prefs['forum_list_lastpost'] =  'y';
	$prefs['forum_list_visits'] =  'y';
	$prefs['forum_list_desc'] =  'y';
	$prefs['feature_forum_local_search'] = 'n';
	$prefs['feature_forum_local_tiki_search'] = 'n';
	$prefs['forum_thread_defaults_by_forum'] = 'n';
	$prefs['forum_thread_user_settings'] = 'y';
	$prefs['forum_thread_user_settings_keep'] = 'n';
	$prefs['forum_comments_per_page'] = 20;
	$prefs['forum_comments_no_title_prefix'] = 'n';
	$prefs['forum_thread_style'] = 'commentStyle_plain';
	$prefs['forum_thread_sort_mode'] = 'commentDate_desc';

	# articles
	$prefs['feature_articles'] = 'n';
	$prefs['feature_submissions'] = 'n';
	$prefs['feature_cms_rankings'] = 'y';
	$prefs['feature_cms_print'] = 'y';
	$prefs['feature_cms_emails'] = 'n';
	$prefs['art_list_title'] = 'y';
	$prefs['art_list_title_len'] = '20';
	$prefs['art_list_topic'] = 'y';
	$prefs['art_list_date'] = 'y';
	$prefs['art_list_author'] = 'y';
	$prefs['art_list_reads'] = 'y';
	$prefs['art_list_size'] = 'y';
	$prefs['art_list_expire'] = 'y';
	$prefs['art_list_img'] = 'y';
	$prefs['art_list_type'] = 'y';
	$prefs['art_list_visible'] = 'y';
	$prefs['art_view_type'] = 'y';
	$prefs['art_view_title'] = 'y';
	$prefs['art_view_topic'] = 'y';
	$prefs['art_view_date'] = 'y';
	$prefs['art_view_author'] = 'y';
	$prefs['art_view_reads'] = 'y';
	$prefs['art_view_size'] = 'y';
	$prefs['art_view_img'] = 'y';
	$prefs['feature_article_comments'] = 'n';
	$prefs['article_comments_default_ordering'] = 'points_desc';
	$prefs['article_comments_per_page'] = 10;
	$prefs['feature_cms_templates'] = 'n';
	$prefs['cms_bot_bar'] = 'y';
	$prefs['cms_left_column'] = 'y';
	$prefs['cms_right_column'] = 'y';
	$prefs['cms_top_bar'] = 'n';
	$prefs['cms_spellcheck'] = 'n';

	# trackers
	$prefs['feature_trackers'] = 'n';
	$prefs['t_use_db'] = 'y';
	$prefs['t_use_dir'] = '';
	$prefs['groupTracker'] = 'n';
	$prefs['userTracker'] = 'n';
	$prefs['trk_with_mirror_tables'] = 'n';

	# user
	$prefs['feature_userlevels'] = 'n';
	$prefs['userlevels'] = array('1'=>tra('Simple'),'2'=>tra('Advanced'));
	$prefs['userbreadCrumb'] = 4;
	$prefs['user_assigned_modules'] = 'n';
	$prefs['user_flip_modules'] = 'module';
	$prefs['user_show_realnames'] = 'n';
	$prefs['feature_mytiki'] = 'y';
	$prefs['feature_userPreferences'] = 'n';
	$prefs['feature_userVersions'] = 'y';
	$prefs['feature_user_bookmarks'] = 'n';
	$prefs['feature_tasks'] = 'n';
	$prefs['w_use_db'] = 'y';
	$prefs['w_use_dir'] = '';
	$prefs['uf_use_db'] = 'y';
	$prefs['uf_use_dir'] = '';
	$prefs['userfiles_quota'] = 30;
	$prefs['feature_usermenu'] = 'n';
	$prefs['feature_minical'] = 'n';
	$prefs['feature_notepad'] = 'n';
	$prefs['feature_userfiles'] = 'n';
	$prefs['feature_community_mouseover'] = 'n';
	$prefs['feature_community_mouseover_name'] = 'y';
	$prefs['feature_community_mouseover_picture'] = 'y';
	$prefs['feature_community_mouseover_friends'] = 'y';
	$prefs['feature_community_mouseover_score'] = 'y';
	$prefs['feature_community_mouseover_country'] = 'y';
	$prefs['feature_community_mouseover_email'] = 'y';
	$prefs['feature_community_mouseover_lastlogin'] = 'y';
	$prefs['feature_community_mouseover_distance'] = 'y';
	$prefs['feature_community_list_name'] = 'y';
	$prefs['feature_community_list_score'] = 'y';
	$prefs['feature_community_list_country'] = 'y';
	$prefs['feature_community_list_distance'] = 'y';
	$prefs['feature_community_friends_permission'] = 'n';
	$prefs['feature_community_friends_permission_dep'] = '2';
	$prefs['change_language'] = 'y';
	$prefs['change_theme'] = 'y';
	$prefs['login_is_email'] = 'n';
	$prefs['validateUsers'] = 'n';
	$prefs['validateEmail'] = 'n';
	$prefs['forgotPass'] = 'n';
	$prefs['change_password'] = 'y';
	$prefs['available_languages'] = array();
	$prefs['available_styles'] = array();
	$prefs['lowercase_username'] = 'n';
	$prefs['max_username_length'] = '50';
	$prefs['min_username_length'] = '1';
	$prefs['users_prefs_allowMsgs'] = 'n';
	$prefs['users_prefs_country'] = '';
	$prefs['users_prefs_diff_versions'] = 'n';
	$prefs['users_prefs_display_timezone'] = '';
	$prefs['users_prefs_email_is_public'] = 'n';
	$prefs['users_prefs_homePage'] = '';
	$prefs['users_prefs_lat'] = '';
	$prefs['users_prefs_lon'] = '';
	$prefs['users_prefs_mess_archiveAfter'] = '0';
	$prefs['users_prefs_mess_maxRecords'] = '10';
	$prefs['users_prefs_mess_sendReadStatus'] = 'n';
	$prefs['users_prefs_minPrio'] = '1';
	$prefs['users_prefs_mytiki_blogs'] = 'y';
	$prefs['users_prefs_mytiki_gals'] = 'y';
	$prefs['users_prefs_mytiki_items'] = 'y';
	$prefs['users_prefs_mytiki_msgs'] = 'y';
	$prefs['users_prefs_mytiki_pages'] = 'y';
	$prefs['users_prefs_mytiki_tasks'] = 'y';
	$prefs['users_prefs_mytiki_workflow'] = 'y';
	$prefs['users_prefs_realName'] = '';
	$prefs['users_prefs_show_mouseover_user_info'] = 'y';
	$prefs['users_prefs_tasks_maxRecords'] = '10';
	$prefs['users_prefs_user_dbl'] = 'n';
	$prefs['users_prefs_user_information'] = 'public';
	$prefs['users_prefs_userbreadCrumb'] = '4';
	$prefs['users_prefs_mailCharset'] = 'utf-8';
	$prefs['validateRegistration'] = 'n';

	# user messages
	$prefs['feature_messages'] = 'n';
	$prefs['messu_mailbox_size'] = '0';
	$prefs['messu_archive_size'] = '200';
	$prefs['messu_sent_size'] = '200';
	$prefs['allowmsg_by_default'] = 'n';
	$prefs['allowmsg_is_optional'] = 'y';

	# newsreader
	$prefs['feature_newsreader'] = 'n';

	# freetags
	$prefs['feature_freetags'] = 'n';
	$prefs['freetags_browse_show_cloud'] = 'y';
	$prefs['freetags_cloud_colors'] = '';
	$prefs['freetags_preload_random_search'] = 'y';
	$prefs['freetags_browse_amount_tags_in_cloud'] = '100';
	$prefs['freetags_browse_amount_tags_suggestion'] = '10';
	$prefs['freetags_normalized_valid_chars'] = '';
	$prefs['freetags_lowercase_only'] = 'y';
	$prefs['freetags_feature_3d'] = 'n';
	$prefs['freetags_3d_width'] = 500;
	$prefs['freetags_3d_height'] = 500;
	$prefs['freetags_3d_navigation_depth'] = 1;
	$prefs['freetags_3d_feed_animation_interval'] = 500;
	$prefs['freetags_3d_existing_page_color'] = '#00CC55';
	$prefs['freetags_3d_missing_page_color'] = '#FF5555';
	$prefs['morelikethis_algorithm'] = 'basic';
	$prefs['morelikethis_basic_mincommon'] = '2';

	# search
	$prefs['feature_search_stats'] = 'n';
	$prefs['feature_search'] = 'y';
	$prefs['feature_search_fulltext'] = 'y';
	$prefs['feature_search_show_forbidden_obj'] = 'n';
	$prefs['feature_search_show_forbidden_cat'] = 'n';
	$prefs['search_refresh_index_mode'] = 'normal';
	$prefs['search_parsed_snippet'] = 'y';

	# webmail
	$prefs['feature_webmail'] = 'n';
	$prefs['webmail_max_attachment'] = 1500000;
	$prefs['webmail_view_html'] = 'y';

	# contacts
	$prefs['feature_contacts'] = 'n';

	# faq
	$prefs['feature_faqs'] = 'n';
	$prefs['feature_faq_comments'] = 'y';
	$prefs['faq_comments_default_ordering'] = 'points_desc';
	$prefs['faq_comments_per_page'] = 10;

	# quizzes
	$prefs['feature_quizzes'] = 'n';

	# polls
	$prefs['feature_polls'] = 'n';
	$prefs['feature_poll_comments'] = 'n';
	$prefs['feature_poll_anonymous'] = 'n';
	$prefs['poll_comments_default_ordering'] = 'points_desc';
	$prefs['poll_comments_per_page'] = 10;
	$prefs['poll_list_categories'] = 'n';
	$prefs['poll_list_objects'] = 'n';

	# surveys
	$prefs['feature_surveys'] = 'n';

	# featured links
	$prefs['feature_featuredLinks'] = 'n';

	# directories
	$prefs['feature_directory'] = 'n';
	$prefs['directory_columns'] = 3;
	$prefs['directory_links_per_page'] = 20;
	$prefs['directory_open_links'] = 'n';
	$prefs['directory_validate_urls'] = 'n';
	$prefs['directory_cool_sites'] = 'y';
	$prefs['directory_country_flag'] = 'y';

	# calendar
	$prefs['feature_calendar'] = 'n';
	$prefs['calendar_sticky_popup'] = 'n';
	$prefs['calendar_view_mode'] = 'week';
	$prefs['calendar_view_tab'] = 'n';
	$prefs['calendar_firstDayofWeek'] = 'user';
	$prefs['calendar_timespan'] = '5';
	$prefs['feature_cal_manual_time'] = '0';
	$prefs['feature_jscalendar'] = 'n';
	$prefs['feature_action_calendar'] = 'n';
	$prefs['calendar_start_year'] = '+0';
	$prefs['calendar_end_year'] = '+3';

	# dates
	$prefs['server_timezone'] = $tikidate->tz->getID();
	$prefs['long_date_format'] = '%A %d of %B, %Y';
	$prefs['long_time_format'] = '%H:%M:%S %Z';
	$prefs['short_date_format'] = '%a %d of %b, %Y';
	$prefs['short_time_format'] = '%H:%M %Z';
	$prefs['display_field_order'] = 'MDY';

	# charts
	$prefs['feature_charts'] = 'n';

	# rss
	$prefs['rss_forums'] = 'y';
	$prefs['rss_forum'] = 'y';
	$prefs['rss_directories'] = 'y';
	$prefs['rss_articles'] = 'y';
	$prefs['rss_blogs'] = 'y';
	$prefs['rss_image_galleries'] = 'y';
	$prefs['rss_file_galleries'] = 'y';
	$prefs['rss_wiki'] = 'y';
	$prefs['rss_image_gallery'] = 'n';
	$prefs['rss_file_gallery'] = 'n';
	$prefs['rss_blog'] = 'n';
	$prefs['rss_tracker'] = 'n';
	$prefs['rss_trackers'] = 'n';
	$prefs['rss_calendar'] = 'n';
	$prefs['rss_mapfiles'] = 'n';
	$prefs['rss_cache_time'] = '0'; // 0 = disabled (default)
	$prefs['max_rss_forums'] = 10;
	$prefs['max_rss_forum'] = 10;
	$prefs['max_rss_directories'] = 10;
	$prefs['max_rss_articles'] = 10;
	$prefs['max_rss_blogs'] = 10;
	$prefs['max_rss_image_galleries'] = 10;
	$prefs['max_rss_file_galleries'] = 10;
	$prefs['max_rss_wiki'] = 10;
	$prefs['max_rss_image_gallery'] = 10;
	$prefs['max_rss_file_gallery'] = 10;
	$prefs['max_rss_blog'] = 10;
	$prefs['max_rss_mapfiles'] = 10;
	$prefs['max_rss_tracker'] = 10;
	$prefs['max_rss_trackers'] = 10;
	$prefs['max_rss_calendar'] = 10;
	$prefs['rssfeed_default_version'] = '2';
	$prefs['rssfeed_language'] =  'en-us';
	$prefs['rssfeed_editor'] = '';
	$prefs['rssfeed_webmaster'] = '';
	$prefs['rssfeed_creator'] = '';
	$prefs['rssfeed_css'] = 'y';
	$prefs['rssfeed_publisher'] = '';
	$prefs['rssfeed_img'] = 'img/tiki.jpg';

	# maps
	$prefs['feature_maps'] = 'n';
	$prefs['map_path'] = '';
	$prefs['default_map'] = '';
	$prefs['map_help'] = 'MapsHelp';
	$prefs['map_comments'] = 'MapsComments';
	$prefs['gdaltindex'] = '';
	$prefs['ogr2ogr'] = '';
	$prefs['mapzone'] = '';

	# gmap
	$prefs['feature_gmap'] = 'n';
	$prefs['gmap_defaultx'] = '0';
	$prefs['gmap_defaulty'] = '0';
	$prefs['gmap_defaultz'] = '17';
	$prefs['gmap_key'] = '';

	# auth
	$prefs['allowRegister'] = 'n';
	$prefs['eponymousGroups'] = 'n';
	$prefs['useRegisterPasscode'] = 'n';
	$prefs['registerPasscode'] = md5($tikilib->genPass());
	$prefs['rememberme'] = 'disabled';
	$prefs['remembertime'] = 7200;
	$prefs['feature_clear_passwords'] = 'n';
	$prefs['feature_crypt_passwords'] = 'tikihash';
	$prefs['feature_challenge'] = 'n';
	$prefs['min_user_length'] = 1;
	$prefs['min_pass_length'] = 1;
	$prefs['pass_chr_num'] = 'n';
	$prefs['pass_due'] = 999;
	$prefs['email_due'] = -1;
	$prefs['unsuccessful_logins'] = 5;
	$prefs['rnd_num_reg'] = 'n';
	$prefs['auth_method'] = 'tiki';
	$prefs['auth_pear'] = 'tiki';
	$prefs['auth_create_user_tiki'] = 'n';
	$prefs['auth_create_user_auth'] = 'n';
	$prefs['auth_skip_admin'] = 'y';
	$prefs['auth_ldap_url'] = '';
	$prefs['auth_pear_host'] = "localhost";
	$prefs['auth_pear_port'] = "389";
	$prefs['auth_ldap_scope'] = "sub";
	$prefs['auth_ldap_basedn'] = '';
	$prefs['auth_ldap_userdn'] = '';
	$prefs['auth_ldap_userattr'] = 'uid';
	$prefs['auth_ldap_useroc'] = 'inetOrgPerson';
	$prefs['auth_ldap_groupdn'] = '';
	$prefs['auth_ldap_groupattr'] = 'cn';
	$prefs['auth_ldap_groupoc'] = 'groupOfUniqueNames';
	$prefs['auth_ldap_memberattr'] = 'uniqueMember';
	$prefs['auth_ldap_memberisdn'] = 'y';
	$prefs['auth_ldap_adminuser'] = '';
	$prefs['auth_ldap_adminpass'] = '';
	$prefs['auth_ldap_version'] = 3;
	$prefs['auth_ldap_nameattr'] = 'displayName';
	$prefs['https_login'] = 'allowed';
	$prefs['feature_show_stay_in_ssl_mode'] = 'y';
	$prefs['feature_switch_ssl_mode'] = 'y';
	$prefs['https_port'] = 443;
	$prefs['http_port'] = 80;
	$prefs['login_url'] = 'tiki-login.php';
	$prefs['login_scr'] = 'tiki-login_scr.php';
	$prefs['register_url'] = 'tiki-register.php';
	$prefs['error_url'] = 'tiki-error.php';
	$prefs['highlight_group'] = '';
	$prefs['cookie_path'] = '/';
	$prefs['cookie_domain'] = '';
	$prefs['cookie_name'] = 'tikiwiki';
	$prefs['user_tracker_infos'] = '';
	$prefs['desactive_login_autocomplete'] = 'n';
	if ( $phpcas_enabled == 'y' ) {
		$prefs['cas_create_user_tiki'] = 'n';
		$prefs['cas_skip_admin'] = 'n';
		$prefs['cas_version'] = '1.0';
		$prefs['cas_hostname'] = '';
		$prefs['cas_port'] = '';
		$prefs['cas_path'] = '';
	}

	# intertiki
	$prefs['feature_intertiki'] = 'n';
	$prefs['feature_intertiki_server'] = 'n';
	$prefs['feature_intertiki_slavemode'] = 'n';
	$prefs['interlist'] = array('');
	$prefs['feature_intertiki_mymaster'] = '';
	$prefs['feature_intertiki_import_preferences'] = 'n';
	$prefs['feature_intertiki_import_groups'] = 'n';
	$prefs['known_hosts'] = array('');
	$prefs['tiki_key'] = '';
	$prefs['intertiki_logfile'] = '';
	$prefs['intertiki_errfile'] = '';

	# search
	$prefs['search_lru_length'] = '100';
	$prefs['search_lru_purge_rate'] = '5';
	$prefs['search_max_syllwords'] = '100';
	$prefs['search_min_wordlength'] = '3';
	$prefs['search_refresh_rate'] = '5';
	$prefs['search_syll_age'] = '48';

	# categories
	$prefs['feature_categories'] = 'n';
	$prefs['feature_categoryobjects'] = 'n';
	$prefs['feature_categorypath'] = 'n';
	$prefs['feature_category_reinforce'] = 'y';
	$prefs['feature_category_use_phplayers'] = 'y';

	# games
	$prefs['feature_games'] = 'n';

	# html pages
	$prefs['feature_html_pages'] = 'n';

	# use filegals for image inclusion
	$prefs['feature_filegals_manager'] = 'n';

	# contact & mail
	$prefs['feature_contact'] = 'n';
	$prefs['contact_user'] = 'admin';
	$prefs['contact_anon'] = 'n';
	$prefs['mail_crlf'] = 'LF';

	# i18n
	$prefs['feature_detect_language'] = 'n';
	$prefs['feature_homePage_if_bl_missing'] = 'n';
	$prefs['record_untranslated'] = 'n';
	$prefs['feature_best_language'] = 'n';
	$prefs['lang_use_db'] = 'n';
	$prefs['language'] = 'en';
	$prefs['feature_babelfish'] = 'n';
	$prefs['feature_babelfish_logo'] = 'n';

	# html header
	$prefs['metatag_keywords'] = '';
	$prefs['metatag_description'] = '';
	$prefs['metatag_author'] = '';
	$prefs['metatag_geoposition'] = '';
	$prefs['metatag_georegion'] = '';
	$prefs['metatag_geoplacename'] = '';
	$prefs['metatag_robots'] = '';
	$prefs['metatag_revisitafter'] = '';
	$prefs['head_extra_js'] = array();
	$prefs['keep_versions'] = 1;
	$prefs['feature_custom_home'] = 'n';

	# site identity
	$prefs['feature_siteidentity'] = 'n';
	$prefs['site_crumb_seper'] = '>';
	$prefs['site_nav_seper'] = '|';
	$prefs['feature_sitemycode'] = 'n';
	$prefs['sitemycode'] = '<div align="center"><b>{tr}Here you can (as an admin) place a piece of custom XHTML and/or Smarty code. Be careful and properly close all the tags before you choose to publish ! (Javascript, applets and object tags are stripped out.){/tr}</b></div>'; // must be max. 250 chars now unless it'll change in tiki_prefs db table field value from VARCHAR(250) to BLOB by default
	$prefs['sitemycode_publish'] = 'n';
	$prefs['feature_sitelogo'] = 'y';
	$prefs['sitelogo_bgcolor'] = '';
	$prefs['sitelogo_align'] = 'left';
	$prefs['sitelogo_title'] = 'Tikiwiki powered site';
	$prefs['sitelogo_src'] = 'img/tiki/tikilogo.png';
	$prefs['sitelogo_alt'] = 'Site Logo';
	$prefs['feature_siteloc'] = 'y';
	$prefs['feature_sitenav'] = 'n';
	$prefs['sitenav'] = '{tr}Navigation : {/tr}<a href="tiki-contact.php" accesskey="10" title="">{tr}Contact Us{/tr}</a>';
	$prefs['feature_sitead'] = 'y';
	$prefs['sitead'] = '';
	$prefs['sitead_publish'] = 'n';
	$prefs['feature_breadcrumbs'] = 'n';
	$prefs['feature_siteloclabel'] = 'y';
	$prefs['feature_sitesearch'] = 'y';
	$prefs['feature_sitemenu'] = 'n';
	$prefs['feature_topbar_version'] = 'y';
	$prefs['feature_topbar_date'] = 'y';
	$prefs['feature_topbar_debug'] = 'y';
	$prefs['feature_topbar_id_menu'] = '42';
	$prefs['feature_sitetitle'] = 'y';
	$prefs['feature_sitedesc'] = 'n';
	$prefs['feature_bot_logo'] = 'n';

	# layout
	$prefs['feature_left_column'] = 'y';
	$prefs['feature_right_column'] = 'y';
	$prefs['feature_top_bar'] = 'y';
	$prefs['feature_bot_bar'] = 'y';
	$prefs['feature_bot_bar_icons'] = 'y';
	$prefs['feature_bot_bar_debug'] = 'y';
	$prefs['feature_bot_bar_rss'] = 'y';
	$prefs['maxRecords'] = 10;
	$prefs['maxArticles'] = 10;
	$prefs['maxVersions'] = 0;
	$prefs['feature_view_tpl'] = 'n';
	$prefs['slide_style'] = 'slidestyle.css';
	$prefs['site_favicon'] = 'favicon.png';
	$prefs['site_favicon_type'] = 'image/png';
	$prefs['style'] = 'tikineat.css';

	# mods
	$prefs['feature_mods_provider'] = 'n';
	$prefs['mods_dir'] = 'mods';
	$prefs['mods_server'] = 'http://mods.tikiwiki.org';

	# dev
	$prefs['feature_experimental'] = 'n';

	# admin
	$prefs['feature_actionlog'] = 'y';
	$prefs['siteTitle'] = '';
	$prefs['tmpDir'] = 'temp';

	# tell a friend
	$prefs['feature_tell_a_friend'] = 'n';

	# copyright
	$prefs['feature_copyright']='n';
	$prefs['feature_multimedia']='n';

	# mypage
	$prefs['feature_mypage_mandatory_category'] = -1;

	# swffix
	$prefs['feature_swffix'] = 'n';

	# textarea
	$prefs['feature_smileys'] = 'y';
	$prefs['popupLinks'] = 'n';
	$prefs['feature_autolinks'] = 'y';
	$prefs['quicktags_over_textarea'] = 'n';
	$prefs['default_rows_textarea_wiki'] = '20';
	$prefs['default_rows_textarea_comment'] = '6';
	$prefs['default_rows_textarea_forum'] = '20';
	$prefs['default_rows_textarea_forumthread'] = '10';

	# pagination
	$prefs['direct_pagination'] = 'y';
	$prefs['nextprev_pagination'] = 'y';
	$prefs['pagination_firstlast'] = 'y';
	$prefs['pagination_icons'] = 'y';
	$prefs['pagination_fastmove_links'] = 'y';
	$prefs['direct_pagination_max_middle_links'] = 2;
	$prefs['direct_pagination_max_ending_links'] = 0;

	# unsorted features
	$prefs['anonCanEdit'] = 'n';
	$prefs['cacheimages'] = 'n';
	$prefs['cachepages'] = 'n';
	$prefs['count_admin_pvs'] = 'y';
	$prefs['default_mail_charset'] ='utf-8';
	$prefs['error_reporting_adminonly'] = 'y';
	$prefs['error_reporting_level'] = 0;
	$prefs['smarty_notice_reporting'] = 'n';
	$prefs['feature_ajax'] = 'n';
	$prefs['feature_antibot'] = 'n';
	$prefs['feature_banners'] = 'n';
	$prefs['feature_banning'] = 'n';
	$prefs['feature_comm'] = 'n';
	$prefs['feature_contribution'] = 'n';
	$prefs['feature_contribution_display_in_comment'] = 'y';
	$prefs['feature_contribution_mandatory'] = 'y';
	$prefs['feature_contribution_mandatory_blog'] = 'n';
	$prefs['feature_contribution_mandatory_comment'] = 'n';
	$prefs['feature_contribution_mandatory_forum'] = 'n';
	$prefs['feature_debug_console'] = 'n';
	$prefs['feature_debugger_console'] = 'n';
	$prefs['feature_display_my_to_others'] = 'n';
	$prefs['feature_drawings'] = 'n';
	$prefs['feature_dynamic_content'] = 'n';
	$prefs['feature_edit_templates'] = 'n';
	$prefs['feature_editcss'] = 'n';
	$prefs['feature_events'] = 'n';
	$prefs['feature_friends'] = 'n';
	$prefs['feature_fullscreen'] = 'n';
	$prefs['feature_help'] = 'y';
	$prefs['feature_hotwords'] = 'y';
	$prefs['feature_hotwords_nw'] = 'n';
	$prefs['feature_integrator'] = 'n';
	$prefs['feature_live_support'] = 'n';
	$prefs['feature_mailin'] = 'n';
	$prefs['feature_menusfolderstyle'] = 'y';
	$prefs['feature_mobile'] = 'n';
	$prefs['feature_modulecontrols'] = 'n';
	$prefs['feature_morcego'] = 'n';
	$prefs['feature_multilingual'] = 'y';
	$prefs['feature_newsletters'] = 'n';
	$prefs['feature_obzip'] = 'n';
	$prefs['feature_phplayers'] = 'n';
	$prefs['feature_projects'] = 'n';
	$prefs['feature_ranking'] = 'n';
	$prefs['feature_redirect_on_error'] = 'n';
	$prefs['feature_referer_highlight'] = 'n';
	$prefs['feature_referer_stats'] = 'n';
	$prefs['feature_score'] = 'n';
	$prefs['feature_sheet'] = 'n';
	$prefs['feature_shoutbox'] = 'n';
	$prefs['feature_source'] = 'y';
	$prefs['feature_stats'] = 'n';
	$prefs['feature_tabs'] = 'n';
	$prefs['feature_theme_control'] = 'n';
	$prefs['feature_ticketlib'] = 'n';
	$prefs['feature_ticketlib2'] = 'y';
	$prefs['feature_top_banner'] = 'n';
	$prefs['feature_usability'] = 'n';
	$prefs['feature_use_quoteplugin'] = 'n';
	$prefs['feature_user_watches'] = 'n';
	$prefs['feature_user_watches_translations'] = 'y';
	$prefs['feature_workflow'] = 'n';
	$prefs['feature_xmlrpc'] = 'n';
	$prefs['helpurl'] = "http://doc.tikiwiki.org/tiki-index.php?best_lang&amp;page=";
	$prefs['layout_section'] = 'n';
	$prefs['limitedGoGroupHome'] = 'n';
	$prefs['minical_reminders'] = 0;
	$prefs['modallgroups'] = 'y';
	$prefs['modseparateanon'] = 'n';
	$prefs['php_docroot'] = 'http://php.net/';
	$prefs['proxy_host'] = '';
	$prefs['proxy_port'] = '';
	$prefs['sender_email'] = $userlib->get_admin_email();
	$prefs['session_db'] = 'n';
	$prefs['session_lifetime'] = 0;
	$prefs['shoutbox_autolink'] = 'n';
	$prefs['show_comzone'] = 'n';
	$prefs['system_os'] = TikiSetup::os();
	$prefs['tikiIndex'] = 'tiki-index.php';
	$prefs['urlIndex'] = '';
	$prefs['useGroupHome'] = 'n';
	$prefs['useUrlIndex'] = 'n';
	$prefs['use_proxy'] = 'n';
	$prefs['user_list_order'] = 'score_desc';
	$prefs['webserverauth'] = 'n';
	$prefs['feature_purifier'] = 'y';
	$prefs['feature_lightbox'] = 'n';
	$prefs['log_sql'] = 'n';

	$prefs['case_patched'] = 'n';
	$prefs['site_closed'] = 'n';
	$prefs['site_closed_msg'] = 'Site is closed for maintainance; please come back later.';
	$prefs['use_load_threshold'] = 'n';
	$prefs['load_threshold'] = 3;
	$prefs['site_busy_msg'] = 'Server is currently too busy; please come back later.';

	$prefs['bot_logo_code'] = '';
	$prefs['feature_blogposts_pings'] = '';
	$prefs['feature_create_webhelp'] = '';
	$prefs['feature_forums_search'] = '';
	$prefs['feature_forums_tiki_search'] = '';
	$prefs['feature_trackbackpings'] = 'n';
	$prefs['feature_wiki_ext_icon'] = 'y';
	$prefs['feature_wiki_mandatory_category'] = '';
	$prefs['freetags_3d_autoload'] = '';
	$prefs['freetags_3d_camera_distance'] = '';
	$prefs['freetags_3d_elastic_constant'] = '';
	$prefs['freetags_3d_eletrostatic_constant'] = '';
	$prefs['freetags_3d_fov'] = '';
	$prefs['freetags_3d_friction_constant'] = '';
	$prefs['freetags_3d_node_charge'] = '';
	$prefs['freetags_3d_node_mass'] = '';
	$prefs['freetags_3d_node_size'] = '';
	$prefs['freetags_3d_spring_size'] = '';
	$prefs['freetags_3d_text_size'] = '';
	$prefs['feature_intertiki_imported_groups'] = '';
	$prefs['feature_wiki_history_ip'] = 'y';
	$prefs['pam_create_user_tiki'] = '';
	$prefs['pam_service'] = '';
	$prefs['pam_skip_admin'] = '';
	$prefs['shib_affiliation'] = '';
	$prefs['shib_create_user_tiki'] = '';
	$prefs['shib_group'] = 'Shibboleth';
	$prefs['shib_skip_admin'] = '';
	$prefs['shib_usegroup'] = 'n';
	$prefs['wiki_3d_camera_distance'] = '';
	$prefs['wiki_3d_elastic_constant'] = '';
	$prefs['wiki_3d_eletrostatic_constant'] = '';
	$prefs['wiki_3d_fov'] = '';
	$prefs['wiki_3d_friction_constant'] = '';
	$prefs['wiki_3d_node_charge'] = '';
	$prefs['wiki_3d_node_mass'] = '';
	$prefs['wiki_3d_node_size'] = '';
	$prefs['wiki_3d_spring_size'] = '';
	$prefs['wiki_3d_text_size'] = '';
	$prefs['articles_feature_copyrights'] = '';
	$prefs['blogues_feature_copyrights'] = '';
	$prefs['faqs_feature_copyrights'] = '';
	$prefs['feature_contributor_wiki'] = '';
	$prefs['feature_jukebox_files'] = '';
	$prefs['freetags_3d_adjust_camera'] = '';
	$prefs['https_login_required'] = '';
	$prefs['jukebox_album_list_created'] = '';
	$prefs['jukebox_album_list_description'] = '';
	$prefs['jukebox_album_list_genre'] = '';
	$prefs['jukebox_album_list_lastmodif'] = '';
	$prefs['jukebox_album_list_title'] = '';
	$prefs['jukebox_album_list_tracks'] = '';
	$prefs['jukebox_album_list_user'] = '';
	$prefs['jukebox_album_list_visits'] = '';
	$prefs['jukebox_list_order'] = '';
	$prefs['jukebox_list_user'] = '';
	$prefs['maxRowsGalleries'] = '';
	$prefs['replimaster'] = '';
	$prefs['rowImagesGalleries'] = '';
	$prefs['scaleSizeGalleries'] = '';
	$prefs['thumbSizeXGalleries'] = '';
	$prefs['thumbSizeYGalleries'] = '';
	$prefs['wiki_3d_adjust_camera'] = '';
	$prefs['wiki_3d_autoload'] = '';
	$prefs['feature_sefurl'] = 'n';
	$prefs['pref_syntax'] = '1.9';
	$prefs['feature_mootools'] = 'n';

	// Special default values

	if ( is_file('styles/'.$tikidomain.'/'.$prefs['site_favicon']) )
		$prefs['site_favicon'] = 'styles/'.$tikidomain.'/'.$prefs['site_favicon'];
	elseif ( ! is_file($prefs['site_favicon']) )
		$prefs['site_favicon'] = false;

	$_SESSION['tmpDir'] = TikiInit::tempdir(); //??

	$prefs['feature_bidi'] = 'n';
	$prefs['feature_lastup'] = 'y';
	$prefs['transition_style_ver'] = '1.9';

	// Find which preferences need to be serialized/unserialized, based on the default values (those with arrays as values)
	if ( ! isset($_SESSION['serialized_prefs']) ) {
		$_SESSION['serialized_prefs'] = array();
		foreach ( $prefs as $p => $v )
			if ( is_array($v) ) $_SESSION['serialized_prefs'][] = $p;
	}

	// Be sure we have a default value for user prefs
	foreach ( $prefs as $p => $v ) {
		if ( substr($p, 0, 12) == 'users_prefs_' ) {
			$prefs[substr($p, 12)] = $v;
		}
	}

	// Override default prefs with values specified in database
	$tikilib->get_db_preferences();

	// Unserialize serialized preferences
	if ( isset($_SESSION['serialized_prefs']) && is_array($_SESSION['serialized_prefs']) ) {
		foreach ( $_SESSION['serialized_prefs'] as $p ) {
			if ( ! is_array($prefs[$p]) ) $prefs[$p] = unserialize($prefs[$p]);
		}
	}

	// Be absolutely sure we have a value for tikiIndex
	if ( $prefs['tikiIndex'] == '' ) $prefs['tikiIndex'] = 'tiki-index.php';

	// Keep some useful sites values available before overriding with user prefs
	// (they could be used in templates, so we need to set them even for Anonymous)
	foreach ( $user_overrider_prefs as $uop ) {
		$prefs['site_'.$uop] = $prefs[$uop];
	}

	// Assign prefs to the session
	$_SESSION['s_prefs'] = $prefs;
}

// Assign the prefs array in smarty, by reference
$smarty->assign_by_ref('prefs', $prefs);

// Define the special maxRecords global var
$maxRecords = $prefs['maxRecords'];
$smarty->assign_by_ref('maxRecords', $maxRecords);

// DEPRECATED: Use $prefs array instead of each global vars to access prefs ; this will be removed soon
if ($prefs['pref_syntax'] == '1.9') {
	extract($prefs);
	foreach ($prefs as $k=>$v) $smarty->assign($k, $v);
}

