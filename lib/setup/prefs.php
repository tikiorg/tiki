<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/prefs.php,v 1.11 2007-10-12 23:49:52 nkoth Exp $
// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

// Set default prefs if they are not already in session vars
if (isset($_SESSION['prefs'])) {
	$query = "select `value` from `tiki_preferences` where `name`=?";
	$lastUpdatePrefs = $tikilib->getOne($query, array('lastUpdatePrefs'));
}

// Set default prefs
if ( ! isset($_SESSION['prefs']) || $_SESSION['need_reload_prefs'] ) {
	$_SESSION['prefs'] = array();

	$_SESSION['prefs']['tiki_release'] = '1.10';
	
	# wiki
	$_SESSION['prefs']['feature_wiki'] = 'y';
	$_SESSION['prefs']['default_wiki_diff_style'] = 'minsidediff';
	$_SESSION['prefs']['feature_backlinks'] = 'y';
	$_SESSION['prefs']['feature_dump'] = 'y';
	$_SESSION['prefs']['feature_history'] = 'y';
	$_SESSION['prefs']['feature_lastChanges'] = 'y';
	$_SESSION['prefs']['feature_likePages'] = 'y';
	$_SESSION['prefs']['feature_listPages'] = 'y';
	$_SESSION['prefs']['feature_page_title'] = 'y';
	$_SESSION['prefs']['feature_sandbox'] = 'y';
	$_SESSION['prefs']['feature_warn_on_edit'] = 'n';
	$_SESSION['prefs']['feature_wiki_1like_redirection'] = 'y';
	$_SESSION['prefs']['feature_wiki_allowhtml'] = 'n';
	$_SESSION['prefs']['feature_wiki_attachments'] = 'n';
	$_SESSION['prefs']['feature_wiki_comments'] = 'n';
	$_SESSION['prefs']['feature_wiki_description'] = 'n';
	$_SESSION['prefs']['feature_wiki_discuss'] = 'n';
	$_SESSION['prefs']['feature_wiki_export'] = 'y';
	$_SESSION['prefs']['feature_wiki_import_page'] = 'n';
	$_SESSION['prefs']['feature_wiki_footnotes'] = 'n';
	$_SESSION['prefs']['feature_wiki_icache'] = 'n';
	$_SESSION['prefs']['feature_wiki_import_html'] = 'n';
	$_SESSION['prefs']['feature_wiki_monosp'] = 'n';
	$_SESSION['prefs']['feature_wiki_multiprint'] = 'n';
	$_SESSION['prefs']['feature_wiki_notepad'] = 'n';
	$_SESSION['prefs']['feature_wiki_open_as_structure'] = 'n';
	$_SESSION['prefs']['feature_wiki_pageid'] = 'n';
	$_SESSION['prefs']['feature_wiki_paragraph_formatting'] = 'n';
	$_SESSION['prefs']['feature_wiki_pdf'] = 'n';
	$_SESSION['prefs']['feature_wiki_pictures'] = 'n';
	$_SESSION['prefs']['feature_wiki_plurals'] = 'y';
	$_SESSION['prefs']['feature_wiki_print'] = 'y';
	$_SESSION['prefs']['feature_wiki_protect_email'] = 'n';
	$_SESSION['prefs']['feature_wiki_rankings'] = 'y';
	$_SESSION['prefs']['feature_wiki_ratings'] = 'n';
	$_SESSION['prefs']['feature_wiki_replace'] = 'n';
	$_SESSION['prefs']['feature_wiki_show_hide_before'] = 'n';
	$_SESSION['prefs']['feature_wiki_tables'] = 'new';
	$_SESSION['prefs']['feature_wiki_templates'] = 'n';
	$_SESSION['prefs']['feature_wiki_undo'] = 'n';
	$_SESSION['prefs']['feature_wiki_userpage'] = 'y';
	$_SESSION['prefs']['feature_wiki_userpage_prefix'] = 'UserPage';
	$_SESSION['prefs']['feature_wiki_usrlock'] = 'n';
	$_SESSION['prefs']['feature_wikiwords'] = 'y';
	$_SESSION['prefs']['feature_wikiwords_usedash'] = 'y';
	$_SESSION['prefs']['mailin_autocheck'] = 'n';
	$_SESSION['prefs']['mailin_autocheckFreq'] = '0';
	$_SESSION['prefs']['mailin_autocheckLast'] = 0;
	$_SESSION['prefs']['warn_on_edit_time'] = 2;
	$_SESSION['prefs']['wikiHomePage'] = 'HomePage';
	$_SESSION['prefs']['wikiLicensePage'] = '';
	$_SESSION['prefs']['wikiSubmitNotice'] = '';
	$_SESSION['prefs']['wiki_authors_style'] = 'classic';
	$_SESSION['prefs']['wiki_show_version'] = 'n';
	$_SESSION['prefs']['wiki_bot_bar'] = 'n';
	$_SESSION['prefs']['wiki_cache'] = 0;
	$_SESSION['prefs']['wiki_comments_default_ordering'] = 'points_desc';
	$_SESSION['prefs']['wiki_comments_per_page'] = 10;
	$_SESSION['prefs']['wiki_creator_admin'] = 'n';
	$_SESSION['prefs']['wiki_feature_copyrights'] = 'n';
	$_SESSION['prefs']['wiki_forum_id'] = '';
	$_SESSION['prefs']['wiki_left_column'] = 'y';
	$_SESSION['prefs']['wiki_list_backlinks'] = 'y';
	$_SESSION['prefs']['wiki_list_comment'] = 'y';
	$_SESSION['prefs']['wiki_list_creator'] = 'y';
	$_SESSION['prefs']['wiki_list_hits'] = 'y';
	$_SESSION['prefs']['wiki_list_lastmodif'] = 'y';
	$_SESSION['prefs']['wiki_list_lastver'] = 'y';
	$_SESSION['prefs']['wiki_list_links'] = 'y';
	$_SESSION['prefs']['wiki_list_name'] = 'y';
	$_SESSION['prefs']['wiki_list_name_len'] = '40';
	$_SESSION['prefs']['wiki_list_size'] = 'y';
	$_SESSION['prefs']['wiki_list_status'] = 'y';
	$_SESSION['prefs']['wiki_list_user'] = 'y';
	$_SESSION['prefs']['wiki_list_versions'] = 'y';
	$_SESSION['prefs']['wiki_list_language'] = 'n';
	$_SESSION['prefs']['wiki_list_categories'] = 'n';
	$_SESSION['prefs']['wiki_list_categories_path'] = 'n';
	$_SESSION['prefs']['wiki_page_regex'] = 'strict';
	$_SESSION['prefs']['wiki_page_separator'] = '...page...';
	$_SESSION['prefs']['wiki_page_navigation_bar'] = 'bottom';
	$_SESSION['prefs']['wiki_pagename_strip'] = '';
	$_SESSION['prefs']['wiki_right_column'] = 'y';
	$_SESSION['prefs']['wiki_top_bar'] = 'y';
	$_SESSION['prefs']['wiki_uses_slides'] = 'n';
	$_SESSION['prefs']['wiki_watch_author'] = 'n';
	$_SESSION['prefs']['wiki_watch_comments'] = 'y';
	$_SESSION['prefs']['wiki_watch_editor'] = 'n';
	$_SESSION['prefs']['feature_wiki_history_full'] = 'n';
	$_SESSION['prefs']['feature_wiki_categorize_structure'] = 'n';
	$_SESSION['prefs']['feature_wiki_watch_structure'] = 'n';
	
	# wysiwyg
	$_SESSION['prefs']['feature_wysiwyg'] = 'n';
	$_SESSION['prefs']['wysiwyg_optional'] = 'y';
	$_SESSION['prefs']['wysiwyg_default'] = 'y';
	$_SESSION['prefs']['wysiwyg_wiki_parsed'] = 'y';
	$_SESSION['prefs']['wysiwyg_wiki_semi_parsed'] = 'y';
	$_SESSION['prefs']['wysiwyg_toolbar_skin'] = 'default';
	$_SESSION['prefs']['wysiwyg_toolbar'] ="FitWindow,Templates,-,Cut,Copy,Paste,PasteWord,Print,SpellCheck
	Undo,Redo,-,Replace,RemoveFormat,-,Image,Table,Rule,SpecialChar,PageBreak
	/
	JustifyLeft,JustifyCenter,JustifyRight,JustifyFull,-,OrderedList,UnorderedList,Outdent,Indent
	Bold,Italic,Underline,StrikeThrough,-,Subscript,Superscript,-,Link,Unlink,Anchor,-,tikilink,tikiimage
	/
	Style,FontName,FontSize,-,TextColor,BGColor,-,Source";
	
	# wiki3d
	$_SESSION['prefs']['wiki_feature_3d'] = 'n';
	$_SESSION['prefs']['wiki_3d_width'] = 500;
	$_SESSION['prefs']['wiki_3d_height'] = 500;
	$_SESSION['prefs']['wiki_3d_navigation_depth'] = 1;
	$_SESSION['prefs']['wiki_3d_feed_animation_interval'] = 500;
	$_SESSION['prefs']['wiki_3d_existing_page_color'] = '#00CC55';
	$_SESSION['prefs']['wiki_3d_missing_page_color'] = '#FF5555';
	
	# blogs
	$_SESSION['prefs']['feature_blogs'] = 'n';
	$_SESSION['prefs']['blog_list_order'] = 'created_desc';
	$_SESSION['prefs']['home_blog'] = 0;
	$_SESSION['prefs']['feature_blog_rankings'] = 'y';
	$_SESSION['prefs']['feature_blog_comments'] = 'n';
	$_SESSION['prefs']['blog_comments_default_ordering'] = 'points_desc';
	$_SESSION['prefs']['blog_comments_per_page'] = 10;
	$_SESSION['prefs']['feature_blogposts_comments'] = 'n';
	$_SESSION['prefs']['blog_list_user'] = 'text';
	$_SESSION['prefs']['blog_list_title'] = 'y';
	$_SESSION['prefs']['blog_list_title_len'] = '35';
	$_SESSION['prefs']['blog_list_description'] = 'y';
	$_SESSION['prefs']['blog_list_created'] = 'y';
	$_SESSION['prefs']['blog_list_lastmodif'] = 'y';
	$_SESSION['prefs']['blog_list_posts'] = 'y';
	$_SESSION['prefs']['blog_list_visits'] = 'y';
	$_SESSION['prefs']['blog_list_activity'] = 'y';
	$_SESSION['prefs']['feature_blog_mandatory_category'] = '-1';
	$_SESSION['prefs']['feature_blog_heading'] = 'y';
	
	# filegals
	$_SESSION['prefs']['feature_file_galleries'] = 'n';
	$_SESSION['prefs']['home_file_gallery'] = 0;
	$_SESSION['prefs']['fgal_use_db'] = 'y';
	$_SESSION['prefs']['fgal_batch_dir'] = '';
	$_SESSION['prefs']['fgal_match_regex'] = '';
	$_SESSION['prefs']['fgal_nmatch_regex'] = '';
	$_SESSION['prefs']['fgal_use_dir'] = '';
	$_SESSION['prefs']['fgal_podcast_dir'] = 'files';
	$_SESSION['prefs']['feature_file_galleries_comments'] = 'n';
	$_SESSION['prefs']['file_galleries_comments_default_ordering'] = 'points_desc';
	$_SESSION['prefs']['file_galleries_comments_per_page'] = 10;
	$_SESSION['prefs']['feature_file_galleries_batch'] = 'n';
	$_SESSION['prefs']['feature_file_galleries_rankings'] = 'n';
	$_SESSION['prefs']['fgal_list_id'] = 'n';
	$_SESSION['prefs']['fgal_list_name'] = 'y';
	$_SESSION['prefs']['fgal_list_description'] = 'y';
	$_SESSION['prefs']['fgal_list_created'] = 'y';
	$_SESSION['prefs']['fgal_list_lastmodif'] = 'y';
	$_SESSION['prefs']['fgal_list_user'] = 'y';
	$_SESSION['prefs']['fgal_list_files'] = 'y';
	$_SESSION['prefs']['fgal_list_hits'] = 'y';
	$_SESSION['prefs']['fgal_enable_auto_indexing'] = 'y';
	$_SESSION['prefs']['fgal_allow_duplicates'] = 'n';
	$_SESSION['prefs']['fgal_list_parent'] = 'n';
	$_SESSION['prefs']['fgal_list_type'] = 'n';
	$_SESSION['prefs']['fgal_sort_mode'] = 'created_desc';
	$_SESSION['prefs']['feature_file_galleries_author'] = 'n';
	
	# imagegals
	$_SESSION['prefs']['feature_galleries'] = 'n';
	$_SESSION['prefs']['feature_gal_batch'] = 'n';
	$_SESSION['prefs']['feature_gal_slideshow'] = 'n';
	$_SESSION['prefs']['home_gallery'] = 0;
	$_SESSION['prefs']['gal_use_db'] = 'y';
	$_SESSION['prefs']['gal_use_lib'] = 'gd';
	$_SESSION['prefs']['gal_match_regex'] = '';
	$_SESSION['prefs']['gal_nmatch_regex'] = '';
	$_SESSION['prefs']['gal_use_dir'] = '';
	$_SESSION['prefs']['gal_batch_dir'] = '';
	$_SESSION['prefs']['feature_gal_rankings'] = 'y';
	$_SESSION['prefs']['feature_image_galleries_comments'] = 'n';
	$_SESSION['prefs']['image_galleries_comments_default_order'] = 'points_desc';
	$_SESSION['prefs']['image_galleries_comments_per_page'] = 10;
	$_SESSION['prefs']['gal_list_name'] = 'y';
	$_SESSION['prefs']['gal_list_description'] = 'y';
	$_SESSION['prefs']['gal_list_created'] = 'y';
	$_SESSION['prefs']['gal_list_lastmodif'] = 'y';
	$_SESSION['prefs']['gal_list_user'] = 'y';
	$_SESSION['prefs']['gal_list_imgs'] = 'y';
	$_SESSION['prefs']['gal_list_visits'] = 'y';
	$_SESSION['prefs']['feature_image_gallery_mandatory_category'] = '-1';
	$_SESSION['prefs']['preset_galleries_scale'] ='n';
	$_SESSION['prefs']['gal_image_mouseover'] = 'n';
	
	# multimedia
	$_SESSION['prefs']['ProgressBarPlay']='#FF8D41';
	$_SESSION['prefs']['ProgressBarLoad']="#A7A7A7";
	$_SESSION['prefs']['ProgressBarButton']="#FF0000";
	$_SESSION['prefs']['ProgressBar']="#C3C3C3";
	$_SESSION['prefs']['VolumeOn']="#21AC2A";
	$_SESSION['prefs']['VolumeOff']="#8EFF8A";
	$_SESSION['prefs']['VolumeButton']=0;
	$_SESSION['prefs']['Button']="#555555";
	$_SESSION['prefs']['ButtonPressed']="#FF00FF";
	$_SESSION['prefs']['ButtonOver']="#B3B3B3";
	$_SESSION['prefs']['ButtonInfo']="#C3C3C3";
	$_SESSION['prefs']['ButtonInfoPressed']="#555555";
	$_SESSION['prefs']['ButtonInfoOver']="#FF8D41";
	$_SESSION['prefs']['ButtonInfoText']="#FFFFFF";
	$_SESSION['prefs']['ID3']="#6CDCEB";
	$_SESSION['prefs']['PlayTime']="#00FF00";
	$_SESSION['prefs']['TotalTime']="#FF2020";
	$_SESSION['prefs']['PanelDisplay']="#555555";
	$_SESSION['prefs']['AlertMesg']="#00FFFF";
	$_SESSION['prefs']['PreloadDelay']=3;
	$_SESSION['prefs']['VideoHeight']=240;
	$_SESSION['prefs']['VideoLength']=300;
	$_SESSION['prefs']['ProgressBarPlay']="#FFFFFF";
	$_SESSION['prefs']['URLAppend']="";
	$_SESSION['prefs']['LimitedMsg']="You are limited to 1 minute";
	$_SESSION['prefs']['MaxPlay']=60;
	$_SESSION['prefs']['MultimediaGalerie']=1;
	$_SESSION['prefs']['MultimediaDefaultLength']=200;
	$_SESSION['prefs']['MultimediaDefaultHeight']=100;
	
	# spellcheck
	if ( file_exists('lib/bablotron.php') ) {
		$_SESSION['prefs']['lib_spellcheck'] = 'y';
		$_SESSION['prefs']['wiki_spellcheck'] = 'n';
		$_SESSION['prefs']['cms_spellcheck'] = 'n';
		$_SESSION['prefs']['blog_spellcheck'] = 'n';
	}
	
	# forums
	$_SESSION['prefs']['feature_forums'] = 'n';
	$_SESSION['prefs']['home_forum'] = 0;
	$_SESSION['prefs']['feature_forum_rankings'] = 'y';
	$_SESSION['prefs']['feature_forum_parse'] = 'n';
	$_SESSION['prefs']['feature_forum_replyempty'] = 'n';
	$_SESSION['prefs']['feature_forum_quickjump'] = 'n';
	$_SESSION['prefs']['feature_forum_topicd'] = 'y';
	$_SESSION['prefs']['feature_forums_allow_thread_titles'] = 'n';
	$_SESSION['prefs']['feature_forum_content_search'] = 'y';
	$_SESSION['prefs']['feature_forums_name_search'] = 'y';
	$_SESSION['prefs']['forums_ordering'] = 'created_desc';
	$_SESSION['prefs']['forum_list_topics'] =  'y';
	$_SESSION['prefs']['forum_list_posts'] =  'y';
	$_SESSION['prefs']['forum_list_ppd'] =  'y';
	$_SESSION['prefs']['forum_list_lastpost'] =  'y';
	$_SESSION['prefs']['forum_list_visits'] =  'y';
	$_SESSION['prefs']['forum_list_desc'] =  'y';
	$_SESSION['prefs']['feature_forum_local_search'] = 'n';
	$_SESSION['prefs']['feature_forum_local_tiki_search'] = 'n';
	$_SESSION['prefs']['forum_thread_defaults_by_forum'] = 'n';
	$_SESSION['prefs']['forum_thread_user_settings'] = 'y';
	$_SESSION['prefs']['forum_thread_user_settings_keep'] = 'n';
	$_SESSION['prefs']['forum_comments_per_page'] = 20;
	$_SESSION['prefs']['forum_thread_style'] = 'commentStyle_plain';
	$_SESSION['prefs']['forum_thread_sort_mode'] = 'commentDate_desc';
	
	# articles
	$_SESSION['prefs']['feature_articles'] = 'n';
	$_SESSION['prefs']['feature_submissions'] = 'n';
	$_SESSION['prefs']['feature_cms_rankings'] = 'y';
	$_SESSION['prefs']['feature_cms_print'] = 'y';
	$_SESSION['prefs']['feature_cms_emails'] = 'n';
	$_SESSION['prefs']['art_list_title'] = 'y';
	$_SESSION['prefs']['art_list_title_len'] = '20';
	$_SESSION['prefs']['art_list_topic'] = 'y';
	$_SESSION['prefs']['art_list_date'] = 'y';
	$_SESSION['prefs']['art_list_author'] = 'y';
	$_SESSION['prefs']['art_list_reads'] = 'y';
	$_SESSION['prefs']['art_list_size'] = 'y';
	$_SESSION['prefs']['art_list_expire'] = 'y';
	$_SESSION['prefs']['art_list_img'] = 'y';
	$_SESSION['prefs']['art_list_type'] = 'y';
	$_SESSION['prefs']['art_list_visible'] = 'y';
	$_SESSION['prefs']['art_view_type'] = 'y';
	$_SESSION['prefs']['art_view_title'] = 'y';
	$_SESSION['prefs']['art_view_topic'] = 'y';
	$_SESSION['prefs']['art_view_date'] = 'y';
	$_SESSION['prefs']['art_view_author'] = 'y';
	$_SESSION['prefs']['art_view_reads'] = 'y';
	$_SESSION['prefs']['art_view_size'] = 'y';
	$_SESSION['prefs']['art_view_img'] = 'y';
	$_SESSION['prefs']['feature_article_comments'] = 'n';
	$_SESSION['prefs']['article_comments_default_ordering'] = 'points_desc';
	$_SESSION['prefs']['article_comments_per_page'] = 10;
	$_SESSION['prefs']['feature_cms_templates'] = 'n';
	$_SESSION['prefs']['cms_bot_bar'] = 'y';
	$_SESSION['prefs']['cms_left_column'] = 'y';
	$_SESSION['prefs']['cms_right_column'] = 'y';
	$_SESSION['prefs']['cms_top_bar'] = 'n';
	$_SESSION['prefs']['cms_spellcheck'] = 'n';
	
	# trackers
	$_SESSION['prefs']['feature_trackers'] = 'n';
	$_SESSION['prefs']['t_use_db'] = 'y';
	$_SESSION['prefs']['t_use_dir'] = '';
	$_SESSION['prefs']['groupTracker'] = 'n';
	$_SESSION['prefs']['userTracker'] = 'n';
	$_SESSION['prefs']['trk_with_mirror_tables'] = 'n';
	
	# user
	$_SESSION['prefs']['feature_userlevels'] = 'n';
	$_SESSION['prefs']['userlevels'] = array('1'=>tra('Simple'),'2'=>tra('Advanced'));
	$_SESSION['prefs']['userbreadCrumb'] = 4;
	$_SESSION['prefs']['user_assigned_modules'] = 'n';
	$_SESSION['prefs']['user_flip_modules'] = 'module';
	$_SESSION['prefs']['user_show_realnames'] = 'n';
	$_SESSION['prefs']['feature_mytiki'] = 'y';
	$_SESSION['prefs']['feature_userPreferences'] = 'n';
	$_SESSION['prefs']['feature_userVersions'] = 'y';
	$_SESSION['prefs']['feature_user_bookmarks'] = 'n';
	$_SESSION['prefs']['feature_tasks'] = 'n';
	$_SESSION['prefs']['w_use_db'] = 'y';
	$_SESSION['prefs']['w_use_dir'] = '';
	$_SESSION['prefs']['uf_use_db'] = 'y';
	$_SESSION['prefs']['uf_use_dir'] = '';
	$_SESSION['prefs']['userfiles_quota'] = 30;
	$_SESSION['prefs']['feature_usermenu'] = 'n';
	$_SESSION['prefs']['feature_minical'] = 'n';
	$_SESSION['prefs']['feature_notepad'] = 'n';
	$_SESSION['prefs']['feature_userfiles'] = 'n';
	$_SESSION['prefs']['feature_community_mouseover'] = 'n';
	$_SESSION['prefs']['feature_community_mouseover_name'] = 'y';
	$_SESSION['prefs']['feature_community_mouseover_picture'] = 'y';
	$_SESSION['prefs']['feature_community_mouseover_friends'] = 'y';
	$_SESSION['prefs']['feature_community_mouseover_score'] = 'y';
	$_SESSION['prefs']['feature_community_mouseover_country'] = 'y';
	$_SESSION['prefs']['feature_community_mouseover_email'] = 'y';
	$_SESSION['prefs']['feature_community_mouseover_lastlogin'] = 'y';
	$_SESSION['prefs']['feature_community_mouseover_distance'] = 'y';
	$_SESSION['prefs']['feature_community_list_name'] = 'y';
	$_SESSION['prefs']['feature_community_list_score'] = 'y';
	$_SESSION['prefs']['feature_community_list_country'] = 'y';
	$_SESSION['prefs']['feature_community_list_distance'] = 'y';
	$_SESSION['prefs']['feature_community_friends_permission'] = 'n';
	$_SESSION['prefs']['feature_community_friends_permission_dep'] = '2';
	$_SESSION['prefs']['change_language'] = 'y';
	$_SESSION['prefs']['change_theme'] = 'y';
	$_SESSION['prefs']['login_is_email'] = 'n';
	$_SESSION['prefs']['validateUsers'] = 'n';
	$_SESSION['prefs']['validateEmail'] = 'n';
	$_SESSION['prefs']['forgotPass'] = 'n';
	$_SESSION['prefs']['change_password'] = 'y';
	$_SESSION['prefs']['available_languages'] = array();
	$_SESSION['prefs']['available_styles'] = array();
	$_SESSION['prefs']['lowercase_username'] = 'n';
	$_SESSION['prefs']['max_username_length'] = '50';
	$_SESSION['prefs']['min_username_length'] = '1';
	$_SESSION['prefs']['users_prefs_allowMsgs'] = 'n';
	$_SESSION['prefs']['users_prefs_country'] = '';
	$_SESSION['prefs']['users_prefs_diff_versions'] = 'n';
	$_SESSION['prefs']['users_prefs_display_timezone'] = 'UTC';
	$_SESSION['prefs']['users_prefs_email_is_public'] = 'n';
	$_SESSION['prefs']['users_prefs_homePage'] = '';
	$_SESSION['prefs']['users_prefs_lat'] = '';
	$_SESSION['prefs']['users_prefs_lon'] = '';
	$_SESSION['prefs']['users_prefs_mess_archiveAfter'] = '0';
	$_SESSION['prefs']['users_prefs_mess_maxRecords'] = '10';
	$_SESSION['prefs']['users_prefs_mess_sendReadStatus'] = 'n';
	$_SESSION['prefs']['users_prefs_minPrio'] = '1';
	$_SESSION['prefs']['users_prefs_mytiki_blogs'] = 'y';
	$_SESSION['prefs']['users_prefs_mytiki_gals'] = 'y';
	$_SESSION['prefs']['users_prefs_mytiki_items'] = 'y';
	$_SESSION['prefs']['users_prefs_mytiki_msgs'] = 'y';
	$_SESSION['prefs']['users_prefs_mytiki_pages'] = 'y';
	$_SESSION['prefs']['users_prefs_mytiki_tasks'] = 'y';
	$_SESSION['prefs']['users_prefs_mytiki_workflow'] = 'y';
	$_SESSION['prefs']['users_prefs_realName'] = '';
	$_SESSION['prefs']['users_prefs_show_mouseover_user_info'] = 'y';
	$_SESSION['prefs']['users_prefs_tasks_maxRecords'] = '10';
	$_SESSION['prefs']['users_prefs_user_dbl'] = 'n';
	$_SESSION['prefs']['users_prefs_user_information'] = 'public';
	$_SESSION['prefs']['users_prefs_userbreadCrumb'] = '4';
	$_SESSION['prefs']['users_prefs_mailCharset'] = 'utf-8';
	$_SESSION['prefs']['validateRegistration'] = 'n';
	
	# user messages
	$_SESSION['prefs']['feature_messages'] = 'n';
	$_SESSION['prefs']['messu_mailbox_size'] = '0';
	$_SESSION['prefs']['messu_archive_size'] = '200';
	$_SESSION['prefs']['messu_sent_size'] = '200';
	$_SESSION['prefs']['allowmsg_by_default'] = 'n';
	$_SESSION['prefs']['allowmsg_is_optional'] = 'y';
	
	# newsreader
	$_SESSION['prefs']['feature_newsreader'] = 'n';
	
	# freetags
	$_SESSION['prefs']['feature_freetags'] = 'n';
	$_SESSION['prefs']['freetags_browse_show_cloud'] = 'y';
	$_SESSION['prefs']['freetags_browse_amount_tags_in_cloud'] = '100';
	$_SESSION['prefs']['freetags_ascii_only'] = 'y';
	$_SESSION['prefs']['freetags_lowercase_only'] = 'y';
	$_SESSION['prefs']['freetags_feature_3d'] = 'n';
	$_SESSION['prefs']['freetags_3d_width'] = 500;
	$_SESSION['prefs']['freetags_3d_height'] = 500;
	$_SESSION['prefs']['freetags_3d_navigation_depth'] = 1;
	$_SESSION['prefs']['freetags_3d_feed_animation_interval'] = 500;
	$_SESSION['prefs']['freetags_3d_existing_page_color'] = '#00CC55';
	$_SESSION['prefs']['freetags_3d_missing_page_color'] = '#FF5555';
	$_SESSION['prefs']['morelikethis_algorithm'] = 'basic';
	$_SESSION['prefs']['morelikethis_basic_mincommon'] = '2';
	
	# search
	$_SESSION['prefs']['feature_search_stats'] = 'n';
	$_SESSION['prefs']['feature_search'] = 'y';
	$_SESSION['prefs']['feature_search_fulltext'] = 'y';
	$_SESSION['prefs']['feature_search_show_forbidden_obj'] = 'n';
	$_SESSION['prefs']['feature_search_show_forbidden_cat'] = 'n';
	$_SESSION['prefs']['search_refresh_index_mode'] = 'normal';
	
	# chat
	$_SESSION['prefs']['feature_chat'] = 'n';
	
	# webmail
	$_SESSION['prefs']['feature_webmail'] = 'n';
	$_SESSION['prefs']['webmail_max_attachment'] = 1500000;
	$_SESSION['prefs']['webmail_view_html'] = 'y';
	
	# contacts
	$_SESSION['prefs']['feature_contacts'] = 'n';
	
	# faq
	$_SESSION['prefs']['feature_faqs'] = 'n';
	$_SESSION['prefs']['feature_faq_comments'] = 'y';
	$_SESSION['prefs']['faq_comments_default_ordering'] = 'points_desc';
	$_SESSION['prefs']['faq_comments_per_page'] = 10;
	
	# quizzes
	$_SESSION['prefs']['feature_quizzes'] = 'n';
	
	# polls
	$_SESSION['prefs']['feature_polls'] = 'n';
	$_SESSION['prefs']['feature_poll_comments'] = 'n';
	$_SESSION['prefs']['feature_poll_anonymous'] = 'n';
	$_SESSION['prefs']['poll_comments_default_ordering'] = 'points_desc';
	$_SESSION['prefs']['poll_comments_per_page'] = 10;
	$_SESSION['prefs']['poll_list_categories'] = 'n';
	$_SESSION['prefs']['poll_list_objects'] = 'n';
	
	# surveys
	$_SESSION['prefs']['feature_surveys'] = 'n';
	
	# featured links
	$_SESSION['prefs']['feature_featuredLinks'] = 'n';
	
	# directories
	$_SESSION['prefs']['feature_directory'] = 'n';
	$_SESSION['prefs']['directory_columns'] = 3;
	$_SESSION['prefs']['directory_links_per_page'] = 20;
	$_SESSION['prefs']['directory_open_links'] = 'n';
	$_SESSION['prefs']['directory_validate_urls'] = 'n';
	$_SESSION['prefs']['directory_cool_sites'] = 'y';
	$_SESSION['prefs']['directory_country_flag'] = 'y';
	
	# calendar
	$_SESSION['prefs']['feature_calendar'] = 'n';
	$_SESSION['prefs']['calendar_sticky_popup'] = 'n';
	$_SESSION['prefs']['calendar_view_mode'] = 'week';
	$_SESSION['prefs']['calendar_view_tab'] = 'n';
	$_SESSION['prefs']['calendar_firstDayofWeek'] = 'user';
	$_SESSION['prefs']['calendar_timespan'] = '5';
	$_SESSION['prefs']['feature_cal_manual_time'] = '0';
	$_SESSION['prefs']['feature_jscalendar'] = 'n';
	$_SESSION['prefs']['feature_action_calendar'] = 'n';
	
	# dates
	$_SESSION['prefs']['server_timezone'] = $tikidate->tz->getID();
	$_SESSION['prefs']['long_date_format'] = '%A %d of %B, %Y';
	$_SESSION['prefs']['long_time_format'] = '%H:%M:%S %Z';
	$_SESSION['prefs']['short_date_format'] = '%a %d of %b, %Y';
	$_SESSION['prefs']['short_time_format'] = '%H:%M %Z';
	$_SESSION['prefs']['display_field_order'] = 'MDY';
	
	# charts
	$_SESSION['prefs']['feature_charts'] = 'n';
	
	# rss
	$_SESSION['prefs']['rss_forums'] = 'y';
	$_SESSION['prefs']['rss_forum'] = 'y';
	$_SESSION['prefs']['rss_directories'] = 'y';
	$_SESSION['prefs']['rss_articles'] = 'y';
	$_SESSION['prefs']['rss_blogs'] = 'y';
	$_SESSION['prefs']['rss_image_galleries'] = 'y';
	$_SESSION['prefs']['rss_file_galleries'] = 'y';
	$_SESSION['prefs']['rss_wiki'] = 'y';
	$_SESSION['prefs']['rss_image_gallery'] = 'n';
	$_SESSION['prefs']['rss_file_gallery'] = 'n';
	$_SESSION['prefs']['rss_blog'] = 'n';
	$_SESSION['prefs']['rss_tracker'] = 'n';
	$_SESSION['prefs']['rss_trackers'] = 'n';
	$_SESSION['prefs']['rss_calendar'] = 'n';
	$_SESSION['prefs']['rss_mapfiles'] = 'n';
	$_SESSION['prefs']['rss_cache_time'] = '0'; // 0 = disabled (default)
	$_SESSION['prefs']['max_rss_forums'] = 10;
	$_SESSION['prefs']['max_rss_forum'] = 10;
	$_SESSION['prefs']['max_rss_directories'] = 10;
	$_SESSION['prefs']['max_rss_articles'] = 10;
	$_SESSION['prefs']['max_rss_blogs'] = 10;
	$_SESSION['prefs']['max_rss_image_galleries'] = 10;
	$_SESSION['prefs']['max_rss_file_galleries'] = 10;
	$_SESSION['prefs']['max_rss_wiki'] = 10;
	$_SESSION['prefs']['max_rss_image_gallery'] = 10;
	$_SESSION['prefs']['max_rss_file_gallery'] = 10;
	$_SESSION['prefs']['max_rss_blog'] = 10;
	$_SESSION['prefs']['max_rss_mapfiles'] = 10;
	$_SESSION['prefs']['max_rss_tracker'] = 10;
	$_SESSION['prefs']['max_rss_trackers'] = 10;
	$_SESSION['prefs']['max_rss_calendar'] = 10;
	$_SESSION['prefs']['rssfeed_default_version'] = '2';
	$_SESSION['prefs']['rssfeed_language'] =  'en-us';
	$_SESSION['prefs']['rssfeed_editor'] = '';
	$_SESSION['prefs']['rssfeed_webmaster'] = '';
	$_SESSION['prefs']['rssfeed_creator'] = '';
	$_SESSION['prefs']['rssfeed_css'] = 'y';
	$_SESSION['prefs']['rssfeed_publisher'] = '';
	
	# maps
	$_SESSION['prefs']['feature_maps'] = 'n';
	$_SESSION['prefs']['map_path'] = '';
	$_SESSION['prefs']['default_map'] = '';
	$_SESSION['prefs']['map_help'] = 'MapsHelp';
	$_SESSION['prefs']['map_comments'] = 'MapsComments';
	$_SESSION['prefs']['gdaltindex'] = '';
	$_SESSION['prefs']['ogr2ogr'] = '';
	$_SESSION['prefs']['mapzone'] = '';
	
	# gmap
	$_SESSION['prefs']['feature_gmap'] = 'n';
	$_SESSION['prefs']['gmap_defaultx'] = '0';
	$_SESSION['prefs']['gmap_defaulty'] = '0';
	$_SESSION['prefs']['gmap_defaultz'] = '17';
	$_SESSION['prefs']['gmap_key'] = '';
	
	# auth
	$_SESSION['prefs']['allowRegister'] = 'n';
	$_SESSION['prefs']['eponymousGroups'] = 'n';
	$_SESSION['prefs']['useRegisterPasscode'] = 'n';
	$_SESSION['prefs']['registerPasscode'] = md5($tikilib->genPass());
	$_SESSION['prefs']['rememberme'] = 'disabled';
	$_SESSION['prefs']['remembertime'] = 7200;
	$_SESSION['prefs']['feature_clear_passwords'] = 'n';
	$_SESSION['prefs']['feature_crypt_passwords'] = 'tikihash';
	$_SESSION['prefs']['feature_challenge'] = 'n';
	$_SESSION['prefs']['min_user_length'] = 1;
	$_SESSION['prefs']['min_pass_length'] = 1;
	$_SESSION['prefs']['pass_chr_num'] = 'n';
	$_SESSION['prefs']['pass_due'] = 999;
	$_SESSION['prefs']['email_due'] = -1;
	$_SESSION['prefs']['unsuccessful_logins'] = -1;
	$_SESSION['prefs']['rnd_num_reg'] = 'n';
	$_SESSION['prefs']['auth_method'] = 'tiki';
	$_SESSION['prefs']['auth_pear'] = 'tiki';
	$_SESSION['prefs']['auth_create_user_tiki'] = 'n';
	$_SESSION['prefs']['auth_create_user_auth'] = 'n';
	$_SESSION['prefs']['auth_skip_admin'] = 'y';
	$_SESSION['prefs']['auth_ldap_url'] = '';
	$_SESSION['prefs']['auth_pear_host'] = "localhost";
	$_SESSION['prefs']['auth_pear_port'] = "389";
	$_SESSION['prefs']['auth_ldap_scope'] = "sub";
	$_SESSION['prefs']['auth_ldap_basedn'] = '';
	$_SESSION['prefs']['auth_ldap_userdn'] = '';
	$_SESSION['prefs']['auth_ldap_userattr'] = 'uid';
	$_SESSION['prefs']['auth_ldap_useroc'] = 'inetOrgPerson';
	$_SESSION['prefs']['auth_ldap_groupdn'] = '';
	$_SESSION['prefs']['auth_ldap_groupattr'] = 'cn';
	$_SESSION['prefs']['auth_ldap_groupoc'] = 'groupOfUniqueNames';
	$_SESSION['prefs']['auth_ldap_memberattr'] = 'uniqueMember';
	$_SESSION['prefs']['auth_ldap_memberisdn'] = 'y';
	$_SESSION['prefs']['auth_ldap_adminuser'] = '';
	$_SESSION['prefs']['auth_ldap_adminpass'] = '';
	$_SESSION['prefs']['auth_ldap_version'] = 3;
	$_SESSION['prefs']['auth_ldap_nameattr'] = 'displayName';
	$_SESSION['prefs']['https_login'] = 'allowed';
	$_SESSION['prefs']['feature_show_stay_in_ssl_mode'] = 'y';
	$_SESSION['prefs']['feature_switch_ssl_mode'] = 'y';
	$_SESSION['prefs']['https_port'] = 443;
	$_SESSION['prefs']['http_port'] = 80;
	$_SESSION['prefs']['login_url'] = 'tiki-login.php';
	$_SESSION['prefs']['login_scr'] = 'tiki-login_scr.php';
	$_SESSION['prefs']['register_url'] = 'tiki-register.php';
	$_SESSION['prefs']['error_url'] = 'tiki-error.php';
	$_SESSION['prefs']['highlight_group'] = '';
	$_SESSION['prefs']['cookie_path'] = '/';
	$_SESSION['prefs']['cookie_domain'] = '';
	$_SESSION['prefs']['cookie_name'] = 'tikiwiki';
	$_SESSION['prefs']['user_tracker_infos'] = '';
	$_SESSION['prefs']['desactive_login_autocomplete'] = 'n';
	if ( $phpcas_enabled == 'y' ) {
		$_SESSION['prefs']['cas_create_user_tiki'] = 'n';
		$_SESSION['prefs']['cas_skip_admin'] = 'n';
		$_SESSION['prefs']['cas_version'] = '1.0';
		$_SESSION['prefs']['cas_hostname'] = '';
		$_SESSION['prefs']['cas_port'] = '';
		$_SESSION['prefs']['cas_path'] = '';
	}
	
	# intertiki
	$_SESSION['prefs']['feature_intertiki'] = 'n';
	$_SESSION['prefs']['feature_intertiki_server'] = 'n';
	$_SESSION['prefs']['feature_intertiki_slavemode'] = 'n';
	$_SESSION['prefs']['interlist'] = serialize(array(''));
	$_SESSION['prefs']['feature_intertiki_mymaster'] = '';
	$_SESSION['prefs']['feature_intertiki_import_preferences'] = 'n';
	$_SESSION['prefs']['feature_intertiki_import_groups'] = 'n';
	$_SESSION['prefs']['known_hosts'] = array('');
	$_SESSION['prefs']['tiki_key'] = '';
	$_SESSION['prefs']['intertiki_logfile'] = '';
	$_SESSION['prefs']['intertiki_errfile'] = '';
	
	# search
	$_SESSION['prefs']['search_lru_length'] = '100';
	$_SESSION['prefs']['search_lru_purge_rate'] = '5';
	$_SESSION['prefs']['search_max_syllwords'] = '100';
	$_SESSION['prefs']['search_min_wordlength'] = '3';
	$_SESSION['prefs']['search_refresh_rate'] = '5';
	$_SESSION['prefs']['search_syll_age'] = '48';
	
	# categories
	$_SESSION['prefs']['feature_categories'] = 'n';
	$_SESSION['prefs']['feature_categoryobjects'] = 'n';
	$_SESSION['prefs']['feature_categorypath'] = 'n';
	$_SESSION['prefs']['feature_category_reinforce'] = 'y';
	$_SESSION['prefs']['feature_category_use_phplayers'] = 'y';
	
	# games
	$_SESSION['prefs']['feature_games'] = 'n';
	
	# html pages
	$_SESSION['prefs']['feature_html_pages'] = 'n';
	
	# use filegals for image inclusion
	$_SESSION['prefs']['feature_filegals_manager'] = 'n';
	
	# contact & mail
	$_SESSION['prefs']['feature_contact'] = 'n';
	$_SESSION['prefs']['contact_user'] = 'admin';
	$_SESSION['prefs']['contact_anon'] = 'n';
	$_SESSION['prefs']['mail_crlf'] = 'LF';
	
	# i18n
	$_SESSION['prefs']['feature_detect_language'] = 'n';
	$_SESSION['prefs']['feature_homePage_if_bl_missing'] = 'n';
	$_SESSION['prefs']['record_untranslated'] = 'n';
	$_SESSION['prefs']['feature_best_language'] = 'n';
	$_SESSION['prefs']['lang_use_db'] = 'n';
	$_SESSION['prefs']['language'] = 'en';
	$_SESSION['prefs']['feature_babelfish'] = 'n';
	$_SESSION['prefs']['feature_babelfish_logo'] = 'n';
	
	# html header
	$_SESSION['prefs']['metatag_keywords'] = '';
	$_SESSION['prefs']['metatag_description'] = '';
	$_SESSION['prefs']['metatag_author'] = '';
	$_SESSION['prefs']['metatag_geoposition'] = '';
	$_SESSION['prefs']['metatag_georegion'] = '';
	$_SESSION['prefs']['metatag_geoplacename'] = '';
	$_SESSION['prefs']['metatag_robots'] = '';
	$_SESSION['prefs']['metatag_revisitafter'] = '';
	$_SESSION['prefs']['head_extra_js'] = array();
	$_SESSION['prefs']['keep_versions'] = 1;
	$_SESSION['prefs']['feature_custom_home'] = 'n';
	
	# site identity
	$_SESSION['prefs']['feature_siteidentity'] = 'n';
	$_SESSION['prefs']['site_crumb_seper'] = '>';
	$_SESSION['prefs']['site_nav_seper'] = '|';
	$_SESSION['prefs']['feature_sitemycode'] = 'n';
	$_SESSION['prefs']['sitemycode'] = '<div align="center"><b>{tr}Here you can (as an admin) place a piece of custom XHTML and/or Smarty code. Be careful and properly close all the tags before you choose to publish ! (Javascript, applets and object tags are stripped out.){/tr}</b></div>'; // must be max. 250 chars now unless it'll change in tiki_prefs db table field value from VARCHAR(250) to BLOB by default
	$_SESSION['prefs']['sitemycode_publish'] = 'n';
	$_SESSION['prefs']['feature_sitelogo'] = 'y';
	$_SESSION['prefs']['sitelogo_bgcolor'] = '';
	$_SESSION['prefs']['sitelogo_align'] = 'left';
	$_SESSION['prefs']['sitelogo_title'] = 'Tikiwiki powered site';
	$_SESSION['prefs']['sitelogo_src'] = 'img/tiki/tikilogo.png';
	$_SESSION['prefs']['sitelogo_alt'] = 'Site Logo';
	$_SESSION['prefs']['feature_siteloc'] = 'y';
	$_SESSION['prefs']['feature_sitenav'] = 'n';
	$_SESSION['prefs']['sitenav'] = '{tr}Navigation : {/tr}<a href="tiki-contact.php" accesskey="10" title="">{tr}Contact Us{/tr}</a>';
	$_SESSION['prefs']['feature_sitead'] = 'y';
	$_SESSION['prefs']['sitead'] = '';
	$_SESSION['prefs']['sitead_publish'] = 'n';
	$_SESSION['prefs']['feature_breadcrumbs'] = 'n';
	$_SESSION['prefs']['feature_siteloclabel'] = 'y';
	$_SESSION['prefs']['feature_sitesearch'] = 'y';
	$_SESSION['prefs']['feature_sitemenu'] = 'n';
	$_SESSION['prefs']['feature_topbar_version'] = 'y';
	$_SESSION['prefs']['feature_topbar_date'] = 'y';
	$_SESSION['prefs']['feature_topbar_debug'] = 'y';
	$_SESSION['prefs']['feature_topbar_id_menu'] = '42';
	$_SESSION['prefs']['feature_sitetitle'] = 'y';
	$_SESSION['prefs']['feature_sitedesc'] = 'n';
	$_SESSION['prefs']['feature_bot_logo'] = 'n';
	
	# layout
	$_SESSION['prefs']['feature_left_column'] = 'y';
	$_SESSION['prefs']['feature_right_column'] = 'y';
	$_SESSION['prefs']['feature_top_bar'] = 'y';
	$_SESSION['prefs']['feature_bot_bar'] = 'y';
	$_SESSION['prefs']['feature_bot_bar_icons'] = 'y';
	$_SESSION['prefs']['feature_bot_bar_debug'] = 'y';
	$_SESSION['prefs']['feature_bot_bar_rss'] = 'y';
	$_SESSION['prefs']['maxRecords'] = 10;
	$_SESSION['prefs']['maxArticles'] = 10;
	$_SESSION['prefs']['maxVersions'] = 0;
	$_SESSION['prefs']['feature_view_tpl'] = 'n';
	$_SESSION['prefs']['slide_style'] = 'slidestyle.css';
	$_SESSION['prefs']['site_favicon'] = 'favicon.png';
	$_SESSION['prefs']['site_favicon_type'] = 'image/png';
	$_SESSION['prefs']['style'] = 'tikineat.css';
	
	# mods
	$_SESSION['prefs']['feature_mods_provider'] = 'n';
	$_SESSION['prefs']['mods_dir'] = 'mods';
	$_SESSION['prefs']['mods_server'] = 'http://tikiwiki.org/mods';
	
	# dev
	$_SESSION['prefs']['feature_experimental'] = 'n';
	
	# admin
	$_SESSION['prefs']['feature_actionlog'] = 'y';
	$_SESSION['prefs']['siteTitle'] = '';
	$_SESSION['prefs']['tmpDir'] = 'temp';
	
	# tell a friend
	$_SESSION['prefs']['feature_tell_a_friend'] = 'n';
	
	# copyright
	$_SESSION['prefs']['feature_copyright']='n';
	$_SESSION['prefs']['feature_multimedia']='n';
	
	# mypage
	$_SESSION['prefs']['feature_mypage_mandatory_category'] = -1;
	
	# swffix
	$_SESSION['prefs']['feature_swffix'] = 'n';
	
	# unsorted features
	$_SESSION['prefs']['anonCanEdit'] = 'n';
	$_SESSION['prefs']['cacheimages'] = 'n';
	$_SESSION['prefs']['cachepages'] = 'n';
	$_SESSION['prefs']['count_admin_pvs'] = 'y';
	$_SESSION['prefs']['dblclickedit'] =  'n';
	$_SESSION['prefs']['default_mail_charset'] ='utf-8';
	$_SESSION['prefs']['direct_pagination'] = 'n';
	$_SESSION['prefs']['error_reporting_adminonly'] = 'y';
	$_SESSION['prefs']['error_reporting_level'] = 0;
	$_SESSION['prefs']['smarty_notice_reporting'] = 'n';
	$_SESSION['prefs']['feature_ajax'] = 'n';
	$_SESSION['prefs']['feature_antibot'] = 'n';
	$_SESSION['prefs']['feature_autolinks'] = 'y';
	$_SESSION['prefs']['feature_banners'] = 'n';
	$_SESSION['prefs']['feature_banning'] = 'n';
	$_SESSION['prefs']['feature_comm'] = 'n';
	$_SESSION['prefs']['feature_contribution'] = 'n';
	$_SESSION['prefs']['feature_contribution_display_in_comment'] = 'y';
	$_SESSION['prefs']['feature_contribution_mandatory'] = 'y';
	$_SESSION['prefs']['feature_contribution_mandatory_blog'] = 'n';
	$_SESSION['prefs']['feature_contribution_mandatory_comment'] = 'n';
	$_SESSION['prefs']['feature_contribution_mandatory_forum'] = 'n';
	$_SESSION['prefs']['feature_debug_console'] = 'n';
	$_SESSION['prefs']['feature_debugger_console'] = 'n';
	$_SESSION['prefs']['feature_display_my_to_others'] = 'n';
	$_SESSION['prefs']['feature_drawings'] = 'n';
	$_SESSION['prefs']['feature_dynamic_content'] = 'n';
	$_SESSION['prefs']['feature_edit_templates'] = 'n';
	$_SESSION['prefs']['feature_editcss'] = 'n';
	$_SESSION['prefs']['feature_events'] = 'n';
	$_SESSION['prefs']['feature_friends'] = 'n';
	$_SESSION['prefs']['feature_fullscreen'] = 'n';
	$_SESSION['prefs']['feature_help'] = 'y';
	$_SESSION['prefs']['feature_hotwords'] = 'y';
	$_SESSION['prefs']['feature_hotwords_nw'] = 'n';
	$_SESSION['prefs']['feature_integrator'] = 'n';
	$_SESSION['prefs']['feature_live_support'] = 'n';
	$_SESSION['prefs']['feature_mailin'] = 'n';
	$_SESSION['prefs']['feature_menusfolderstyle'] = 'y';
	$_SESSION['prefs']['feature_mobile'] = 'n';
	$_SESSION['prefs']['feature_modulecontrols'] = 'n';
	$_SESSION['prefs']['feature_morcego'] = 'n';
	$_SESSION['prefs']['feature_multilingual'] = 'y';
	$_SESSION['prefs']['feature_newsletters'] = 'n';
	$_SESSION['prefs']['feature_obzip'] = 'n';
	$_SESSION['prefs']['feature_phplayers'] = 'n';
	$_SESSION['prefs']['feature_projects'] = 'n';
	$_SESSION['prefs']['feature_ranking'] = 'n';
	$_SESSION['prefs']['feature_redirect_on_error'] = 'n';
	$_SESSION['prefs']['feature_referer_highlight'] = 'n';
	$_SESSION['prefs']['feature_referer_stats'] = 'n';
	$_SESSION['prefs']['feature_score'] = 'n';
	$_SESSION['prefs']['feature_sheet'] = 'n';
	$_SESSION['prefs']['feature_shoutbox'] = 'n';
	$_SESSION['prefs']['feature_smileys'] = 'y';
	$_SESSION['prefs']['feature_source'] = 'y';
	$_SESSION['prefs']['feature_stats'] = 'n';
	$_SESSION['prefs']['feature_tabs'] = 'n';
	$_SESSION['prefs']['feature_theme_control'] = 'n';
	$_SESSION['prefs']['feature_ticketlib'] = 'n';
	$_SESSION['prefs']['feature_ticketlib2'] = 'y';
	$_SESSION['prefs']['feature_top_banner'] = 'n';
	$_SESSION['prefs']['feature_usability'] = 'n';
	$_SESSION['prefs']['feature_use_quoteplugin'] = 'n';
	$_SESSION['prefs']['feature_user_watches'] = 'n';
	$_SESSION['prefs']['feature_user_watches_translations'] = 'y';
	$_SESSION['prefs']['feature_workflow'] = 'n';
	$_SESSION['prefs']['feature_xmlrpc'] = 'n';
	$_SESSION['prefs']['helpurl'] = "http://doc.tikiwiki.org/tiki-index.php?best_lang&amp;page=";
	$_SESSION['prefs']['layout_section'] = 'n';
	$_SESSION['prefs']['limitedGoGroupHome'] = 'n';
	$_SESSION['prefs']['minical_reminders'] = 0;
	$_SESSION['prefs']['modallgroups'] = 'y';
	$_SESSION['prefs']['modseparateanon'] = 'n';
	$_SESSION['prefs']['php_docroot'] = 'http://php.net/';
	$_SESSION['prefs']['popupLinks'] = 'n';
	$_SESSION['prefs']['proxy_host'] = '';
	$_SESSION['prefs']['proxy_port'] = '';
	$_SESSION['prefs']['sender_email'] = $userlib->get_admin_email();
	$_SESSION['prefs']['session_db'] = 'n';
	$_SESSION['prefs']['session_lifetime'] = 0;
	$_SESSION['prefs']['shoutbox_autolink'] = 'n';
	$_SESSION['prefs']['show_comzone'] = 'n';
	$_SESSION['prefs']['system_os'] = TikiSetup::os();
	$_SESSION['prefs']['tikiIndex'] = 'tiki-index.php';
	$_SESSION['prefs']['urlIndex'] = '';
	$_SESSION['prefs']['useGroupHome'] = 'n';
	$_SESSION['prefs']['useUrlIndex'] = 'n';
	$_SESSION['prefs']['use_proxy'] = 'n';
	$_SESSION['prefs']['user_list_order'] = 'score_desc';
	$_SESSION['prefs']['webserverauth'] = 'n';
	$_SESSION['prefs']['feature_purifier'] = 'y';
	$_SESSION['prefs']['feature_lightbox'] = 'n';
	$_SESSION['prefs']['log_sql'] = 'n';

	$_SESSION['prefs']['case_patched'] = 'n';
	$_SESSION['prefs']['site_closed'] = 'n';
	$_SESSION['prefs']['site_closed_msg'] = 'Site is closed for maintainance; please come back later.';
	$_SESSION['prefs']['use_load_threshold'] = 'n';
	$_SESSION['prefs']['load_threshold'] = 3;
	$_SESSION['prefs']['site_busy_msg'] = 'Server is currently too busy; please come back later.';


	$_SESSION['prefs']['bot_logo_code'] = '';
	$_SESSION['prefs']['feature_blogposts_pings'] = '';
	$_SESSION['prefs']['feature_create_webhelp'] = '';
	$_SESSION['prefs']['feature_forums_search'] = '';
	$_SESSION['prefs']['feature_forums_tiki_search'] = '';
	$_SESSION['prefs']['feature_trackbackpings'] = '';
	$_SESSION['prefs']['feature_wiki_ext_icon'] = '';
	$_SESSION['prefs']['feature_wiki_mandatory_category'] = '';
	$_SESSION['prefs']['freetags_3d_autoload'] = '';
	$_SESSION['prefs']['freetags_3d_camera_distance'] = '';
	$_SESSION['prefs']['freetags_3d_elastic_constant'] = '';
	$_SESSION['prefs']['freetags_3d_eletrostatic_constant'] = '';
	$_SESSION['prefs']['freetags_3d_fov'] = '';
	$_SESSION['prefs']['freetags_3d_friction_constant'] = '';
	$_SESSION['prefs']['freetags_3d_node_charge'] = '';
	$_SESSION['prefs']['freetags_3d_node_mass'] = '';
	$_SESSION['prefs']['freetags_3d_node_size'] = '';
	$_SESSION['prefs']['freetags_3d_spring_size'] = '';
	$_SESSION['prefs']['freetags_3d_text_size'] = '';
	$_SESSION['prefs']['feature_intertiki_imported_groups'] = '';
	$_SESSION['prefs']['feature_wiki_history_ip'] = '';
	$_SESSION['prefs']['pam_create_user_tiki'] = '';
	$_SESSION['prefs']['pam_service'] = '';
	$_SESSION['prefs']['pam_skip_admin'] = '';
	$_SESSION['prefs']['shib_affiliation'] = '';
	$_SESSION['prefs']['shib_create_user_tiki'] = '';
	$_SESSION['prefs']['shib_group'] = 'Shibboleth';
	$_SESSION['prefs']['shib_skip_admin'] = '';
	$_SESSION['prefs']['shib_usegroup'] = 'n';
	$_SESSION['prefs']['wiki_3d_camera_distance'] = '';
	$_SESSION['prefs']['wiki_3d_elastic_constant'] = '';
	$_SESSION['prefs']['wiki_3d_eletrostatic_constant'] = '';
	$_SESSION['prefs']['wiki_3d_fov'] = '';
	$_SESSION['prefs']['wiki_3d_friction_constant'] = '';
	$_SESSION['prefs']['wiki_3d_node_charge'] = '';
	$_SESSION['prefs']['wiki_3d_node_mass'] = '';
	$_SESSION['prefs']['wiki_3d_node_size'] = '';
	$_SESSION['prefs']['wiki_3d_spring_size'] = '';
	$_SESSION['prefs']['wiki_3d_text_size'] = '';
	$_SESSION['prefs']['articles_feature_copyrights'] = '';
	$_SESSION['prefs']['blogues_feature_copyrights'] = '';
	$_SESSION['prefs']['faqs_feature_copyrights'] = '';
	$_SESSION['prefs']['feature_contributor_wiki'] = '';
	$_SESSION['prefs']['feature_jukebox_files'] = '';
	$_SESSION['prefs']['freetags_3d_adjust_camera'] = '';
	$_SESSION['prefs']['https_login_required'] = '';
	$_SESSION['prefs']['jukebox_album_list_created'] = '';
	$_SESSION['prefs']['jukebox_album_list_description'] = '';
	$_SESSION['prefs']['jukebox_album_list_genre'] = '';
	$_SESSION['prefs']['jukebox_album_list_lastmodif'] = '';
	$_SESSION['prefs']['jukebox_album_list_title'] = '';
	$_SESSION['prefs']['jukebox_album_list_tracks'] = '';
	$_SESSION['prefs']['jukebox_album_list_user'] = '';
	$_SESSION['prefs']['jukebox_album_list_visits'] = '';
	$_SESSION['prefs']['jukebox_list_order'] = '';
	$_SESSION['prefs']['jukebox_list_user'] = '';
	$_SESSION['prefs']['maxRowsGalleries'] = '';
	$_SESSION['prefs']['replimaster'] = '';
	$_SESSION['prefs']['rowImagesGalleries'] = '';
	$_SESSION['prefs']['scaleSizeGalleries'] = '';
	$_SESSION['prefs']['thumbSizeXGalleries'] = '';
	$_SESSION['prefs']['thumbSizeYGalleries'] = '';
	$_SESSION['prefs']['wiki_3d_adjust_camera'] = '';
	$_SESSION['prefs']['wiki_3d_autoload'] = '';

	// Special default values

	if ( is_file('styles/'.$tikidomain.'/'.$_SESSION['prefs']['site_favicon']) )
		$_SESSION['prefs']['site_favicon'] = 'styles/'.$tikidomain.'/'.$_SESSION['prefs']['site_favicon'];
	elseif ( ! is_file($_SESSION['prefs']['site_favicon']) )
		$_SESSION['prefs']['site_favicon'] = false;

	$_SESSION['tmpDir'] = TikiInit::tempdir();

	$_SESSION['prefs']['display_timezone'] = $_SESSION['prefs']['server_timezone'];
	$_SESSION['prefs']['feature_bidi'] = 'n';
	$_SESSION['prefs']['feature_lastup'] = 'y';
	$_SESSION['prefs']['display_server_load'] = 'y';

	// Find which preferences need to be serialized/unserialized, based on the default values (those with arrays as values)
	if ( ! isset($_SESSION['serialized_prefs']) ) {	
		$_SESSION['serialized_prefs'] = array();
		foreach ( $_SESSION['prefs'] as $p => $v )
			if ( is_array($v) ) $_SESSION['serialized_prefs'][] = $p;
	}
}
$prefs =& $_SESSION['prefs'];

// Check if prefs needs to be reloaded
if ( empty($prefs['lastReadingPrefs']) || $lastUpdatePrefs > $prefs['lastReadingPrefs']) {

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
}

// Assign the prefs array in smarty, by reference
$smarty->assign_by_ref('prefs', $_SESSION['prefs']);

// DEPRECATED: Use $prefs array instead of each global vars to access prefs ; this will be removed soon
extract($_SESSION['prefs']);
