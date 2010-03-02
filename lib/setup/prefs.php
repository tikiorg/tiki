<?php

// $Id$
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

// RULE1: $prefs does not contain serialized values. Only the database contains serialized values.
// RULE2: put array() in default prefs for serialized values

//this script may only be included - so its better to die if called directly.
if ( basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__) ) {
  header("location: index.php");
  exit;
}

function get_default_prefs() {
	static $prefs;
	if( is_array($prefs) )
		return $prefs;

	global $cachelib;
	if( isset($cachelib) && $cachelib->isCached("tiki_default_preferences_cache") ) {
		$prefs = unserialize( $cachelib->getCached("tiki_default_preferences_cache") );
		if ( $prefs !== false ) return $prefs;
	}

	global $tikidate, $tikilib;
	$prefs = array(
		// tiki and version
		'tiki_release' => '0',
		'feature_version_checks' => 'y',
		'tiki_needs_upgrade' => 'n',
		'tiki_version_last_check' => 0,
		'tiki_version_check_frequency' => 604800,
		'lastUpdatePrefs' => 1,

		'feature_print_indexed' => 'n',  //Still needs to be in admin panel somwhere

		// wiki
		'feature_wiki' => 'y',
		'default_wiki_diff_style' => 'sidediff',
		'feature_backlinks' => 'n',
		'feature_dump' => 'n',
		'feature_history' => 'y',
		'feature_lastChanges' => 'y',
		'feature_likePages' => 'n',
		'feature_listPages' => 'y',
		'feature_listorphanPages' => 'n',
		'feature_page_title' => 'y',
		'feature_sandbox' => 'n',
		'feature_warn_on_edit' => 'y',
		'feature_wiki_1like_redirection' => 'y',
		'feature_wiki_allowhtml' => 'n',
		'feature_wiki_argvariable' => 'n',
		'feature_wiki_attachments' => 'n',
		'feature_wiki_comments' => 'n',
		'feature_wiki_description' => 'n',
		'feature_wiki_discuss' => 'n',
		'feature_wiki_export' => 'n',
		'feature_wiki_structure' => 'n',
		'feature_wiki_import_page' => 'n',
		'feature_wiki_footnotes' => 'n',
		'feature_wiki_icache' => 'n',
		'feature_wiki_import_html' => 'n',
		'feature_wiki_mindmap' => 'n',
		'feature_wiki_monosp' => 'n',
		'feature_wiki_multiprint' => 'n',
		'feature_wiki_notepad' => 'n',
		'feature_wiki_make_structure' => 'n',
		'feature_wiki_open_as_structure' => 'n',
		'feature_wiki_pageid' => 'n',
		'feature_wiki_paragraph_formatting' => 'n',
		'feature_wiki_paragraph_formatting_add_br' => 'n',
		'feature_wiki_pictures' => 'y',
		'feature_wiki_plurals' => 'y',
		'feature_wiki_print' => 'n',
		'feature_wiki_protect_email' => 'y',
		'feature_wiki_rankings' => 'n',
		'feature_wiki_ratings' => 'n',
		'feature_wiki_replace' => 'n',
		'feature_wiki_show_hide_before' => 'n',
		'feature_wiki_tables' => 'new',
		'feature_wiki_templates' => 'n',
		'feature_wiki_undo' => 'n',
		'feature_wiki_userpage' => 'y',
		'feature_wiki_userpage_prefix' => 'UserPage',
		'feature_wiki_usrlock' => 'n',
		'feature_wiki_feedback_polls' => array(),
		'feature_wiki_save_draft' => 'n',
		'feature_wikiwords' => 'n',
		'feature_wikiwords_usedash' => 'y',
		'feature_wiki_pagealias' => 'y',
		'mailin_autocheck' => 'n',
		'mailin_autocheckFreq' => '0',
		'mailin_autocheckLast' => 0,
		'page_bar_position' => 'bottom',
		'warn_on_edit_time' => 2,
		'wikiHomePage' => 'HomePage',
		'wikiLicensePage' => '',
		'wikiSubmitNotice' => '',
		'wiki_authors_style' => 'none',
		'wiki_authors_style_by_page' => 'n',
		'wiki_show_version' => 'n',
		'wiki_bot_bar' => 'n',
		'wiki_cache' => 0,
		'wiki_comments_default_ordering' => 'points_desc',
		'wiki_comments_displayed_default' => 'n',
		'wiki_comments_per_page' => 10,
		'wiki_creator_admin' => 'n',
		'wiki_feature_copyrights' => 'n',
		'wiki_forum_id' => '',
		'wiki_left_column' => 'y',
		'wiki_list_backlinks' => 'n',
		'wiki_list_comment' => 'y',
		'wiki_list_comment_len' => '200',
		'wiki_list_description' => 'y',
		'wiki_list_description_len' => '200',
		'wiki_list_creator' => 'n',
		'wiki_list_hits' => 'y',
		'wiki_list_lastmodif' => 'y',
		'wiki_list_lastver' => 'n',
		'wiki_list_links' => 'n',
		'wiki_list_name' => 'y',
		'wiki_list_name_len' => '40',
		'wiki_list_size' => 'n',
		'wiki_list_status' => 'n',
		'wiki_list_user' => 'y',
		'wiki_list_versions' => 'y',
		'wiki_list_language' => 'n',
		'wiki_list_categories' => 'n',
		'wiki_list_categories_path' => 'n',
		'wiki_list_id' => 'n',
		'wiki_list_sortorder' => 'pageName',
		'wiki_list_sortdirection' => 'asc',
		'wiki_pagealias_tokens' => 'alias',
		'wiki_page_regex' => 'strict',
		'wiki_page_separator' => '...page...',
		'wiki_page_navigation_bar' => 'bottom',
		'wiki_actions_bar' => 'bottom',
		'wiki_pagename_strip' => '',
		'wiki_right_column' => 'y',
		'wiki_top_bar' => 'y',
		'wiki_topline_position' => 'top',
		'wiki_uses_slides' => 'n',
		'wiki_watch_author' => 'n',
		'wiki_watch_comments' => 'y',
		'wiki_watch_editor' => 'n',
		'wiki_watch_minor' => 'y',
		'feature_wiki_history_full' => 'n',
		'feature_wiki_categorize_structure' => 'n',
		'feature_wiki_watch_structure' => 'n',
		'feature_wikiapproval' => 'n',
		'wikiapproval_prefix' => '*',
		'wikiapproval_hideprefix' => 'n',
		'wikiapproval_delete_staging' => 'n',
		'wikiapproval_master_group' => '',
		'wikiapproval_staging_category' => '0',
		'wikiapproval_approved_category' => '0',
		'wikiapproval_outofsync_category' => '0',
		'wikiapproval_block_editapproved' => 'n',
		'wikiapproval_sync_categories' => 'n',
		'wikiapproval_update_freetags' => 'n',
		'wikiapproval_combine_freetags' => 'n',
		'wiki_edit_section' => 'y',
		'wiki_edit_section_level' => '0',
		'wiki_edit_plugin' => 'n',
		'wiki_validate_plugin' => 'y',
		'wiki_edit_minor' => 'n',

		'wikiplugin_agentinfo' => 'n',
		'wikiplugin_alink' => 'n',
		'wikiplugin_aname' => 'n',
		'wikiplugin_annotation' => 'n',
		'wikiplugin_article' => 'y',
		'wikiplugin_articles' => 'y',
		'wikiplugin_attach' => 'y',
		'wikiplugin_avatar' => 'n',
		'wikiplugin_back' => 'n',
		'wikiplugin_backlinks' => 'n',
		'wikiplugin_banner' => 'n',
		'wikiplugin_bloglist' => 'n',
		'wikiplugin_box' => 'y',
		'wikiplugin_category' => 'y',
		'wikiplugin_catorphans' => 'y',
		'wikiplugin_catpath' => 'y',
		'wikiplugin_center' => 'y',
		'wikiplugin_chart' => 'y',
		'wikiplugin_code' => 'y',
		'wikiplugin_content' => 'y',
		'wikiplugin_cookie' => 'n',
		'wikiplugin_copyright' => 'y',
		'wikiplugin_countdown' => 'n',
		'wikiplugin_dbreport' => 'n',
		'wikiplugin_div' => 'n',
		'wikiplugin_dl' => 'y',
		'wikiplugin_draw' => 'y',
		'wikiplugin_equation' => 'n',
		'wikiplugin_events' => 'y',
		'wikiplugin_fade' => 'y',
		'wikiplugin_fancylist' => 'y',
		'wikiplugin_fancytable' => 'y',
		'wikiplugin_file' => 'y',
		'wikiplugin_files' => 'y',
		'wikiplugin_flash' => 'y',
		'wikiplugin_footnote' => 'n',
		'wikiplugin_footnotearea' => 'n',
		'wikiplugin_ftp' => 'n',
		'wikiplugin_gauge' => 'n',
		'wikiplugin_googleanalytics' => 'n',
		'wikiplugin_googledoc' => 'n',
		'wikiplugin_group' => 'y',
		'wikiplugin_html' => 'n',
		'wikiplugin_iframe' => 'n',
		'wikiplugin_img' => 'y',
		'wikiplugin_image' => 'n',    // Experimental, may supercede img in 4.0
		'wikiplugin_include' => 'y',
		'wikiplugin_jabber' => 'n',
		'wikiplugin_js' => 'n',
		'wikiplugin_jq' => 'n',
		'wikiplugin_lang' => 'y',
		'wikiplugin_lastmod' => 'n',
		'wikiplugin_listpages' => 'n',
		'wikiplugin_lsdir' => 'n',
		'wikiplugin_map' => 'y',
		'wikiplugin_mediaplayer' => 'y',
		'wikiplugin_miniquiz' => 'y',
		'wikiplugin_module' => 'y',
		'wikiplugin_mono' => 'n',
		'wikiplugin_mouseover' => 'y',
		'wikiplugin_mwtable' => 'n',
		'wikiplugin_myspace' => 'n',
		'wikiplugin_objecthits' => 'n',
		'wikiplugin_picture' => 'n',  // Old syntax for images
		'wikiplugin_pluginmanager' => 'n',
		'wikiplugin_poll' => 'y',
		'wikiplugin_profile' => 'n',		
		'wikiplugin_proposal' => 'n',
		'wikiplugin_quote' => 'y',
		'wikiplugin_rcontent' => 'y',
		'wikiplugin_redirect' => 'n',
		'wikiplugin_regex' => 'n',
		'wikiplugin_remarksbox' => 'y',
		'wikiplugin_rss' => 'y',
		'wikiplugin_sf' => 'n',
		'wikiplugin_share' => 'n',
		'wikiplugin_sharethis' => 'n',
		'wikiplugin_sheet' => 'y',
		'wikiplugin_showpages' => 'n',
		'wikiplugin_skype' => 'n',
		'wikiplugin_snarf' => 'n',
		'wikiplugin_sort' => 'y',
		'wikiplugin_split' => 'y',
		'wikiplugin_sql' => 'n',
		'wikiplugin_sub' => 'y',
		'wikiplugin_subscribegroup' => 'n',
		'wikiplugin_subscribegroups' => 'n',
		'wikiplugin_sup' => 'y',
		'wikiplugin_survey' => 'y',
		'wikiplugin_tag' => 'n',
		'wikiplugin_thumb' => 'y',
		'wikiplugin_titlesearch' => 'n',
		'wikiplugin_toc' => 'y',
		'wikiplugin_topfriends' => 'y',
		'wikiplugin_trackerfilter' => 'y',
		'wikiplugin_trackeritemfield' => 'y',
		'wikiplugin_trackerlist' => 'y',
		'wikiplugin_trackertimeline' => 'y',
		'wikiplugin_tracker' => 'y',
		'wikiplugin_trackerprefill' => 'y',
		'wikiplugin_trackerstat' => 'y',
		'wikiplugin_translated' => 'y',
		'wikiplugin_tr' => 'n',
		'wikiplugin_usercount' => 'n',
		'wikiplugin_userlist' => 'n',
		'wikiplugin_versions' => 'n',
		'wikiplugin_vote' => 'y',
		'wikiplugin_wantedpages' => 'n',
		'wikiplugin_webservice' => 'n',
		'wikiplugin_youtube' => 'y',

		// Inline wiki plugins have their edit plugin icon disabled
		'wikiplugininline_agentinfo' => 'n',
		'wikiplugininline_alink' => 'n',
		'wikiplugininline_aname' => 'n',
		'wikiplugininline_annotation' => 'n',
		'wikiplugininline_article' => 'n',
		'wikiplugininline_articles' => 'n',
		'wikiplugininline_attach' => 'n',
		'wikiplugininline_avatar' => 'n',
		'wikiplugininline_back' => 'n',
		'wikiplugininline_backlinks' => 'n',
		'wikiplugininline_banner' => 'n',
		'wikiplugininline_bloglist' => 'n',
		'wikiplugininline_box' => 'n',
		'wikiplugininline_category' => 'n',
		'wikiplugininline_catorphans' => 'n',
		'wikiplugininline_catpath' => 'n',
		'wikiplugininline_center' => 'n',
		'wikiplugininline_chart' => 'n',
		'wikiplugininline_code' => 'n',
		'wikiplugininline_content' => 'n',
		'wikiplugininline_cookie' => 'n',
		'wikiplugininline_copyright' => 'n',
		'wikiplugininline_countdown' => 'n',
		'wikiplugininline_dbreport' => 'n',
		'wikiplugininline_div' => 'n',
		'wikiplugininline_dl' => 'n',
		'wikiplugininline_draw' => 'n',
		'wikiplugininline_equation' => 'n',
		'wikiplugininline_events' => 'n',
		'wikiplugininline_fade' => 'n',
		'wikiplugininline_fancylist' => 'n',
		'wikiplugininline_fancytable' => 'n',
		'wikiplugininline_file' => 'y',
		'wikiplugininline_files' => 'n',
		'wikiplugininline_flash' => 'n',
                'wikiplugininline_footnote' => 'n',
                'wikiplugininline_footnotearea' => 'n',
		'wikiplugininline_ftp' => 'n',
		'wikiplugininline_gauge' => 'n',
		'wikiplugininline_googleanalytics' => 'n',
		'wikiplugininline_googledoc' => 'n',
		'wikiplugininline_group' => 'y',
		'wikiplugininline_html' => 'n',
		'wikiplugininline_iframe' => 'n',
		'wikiplugininline_img' => 'n',
		'wikiplugininline_image' => 'n',    // Experimental, may supercede img in 4.0
		'wikiplugininline_include' => 'n',
		'wikiplugininline_jabber' => 'n',
		'wikiplugininline_js' => 'n',
		'wikiplugininline_jq' => 'n',
		'wikiplugininline_lang' => 'n',
		'wikiplugininline_lastmod' => 'n',
		'wikiplugininline_listpages' => 'n',
		'wikiplugininline_lsdir' => 'n',
		'wikiplugininline_map' => 'n',
		'wikiplugininline_mediaplayer' => 'n',
		'wikiplugininline_miniquiz' => 'n',
		'wikiplugininline_module' => 'n',
		'wikiplugininline_mono' => 'n',
		'wikiplugininline_mouseover' => 'n',
		'wikiplugininline_mwtable' => 'n',
		'wikiplugininline_myspace' => 'n',
		'wikiplugininline_objecthits' => 'n',
		'wikiplugininline_picture' => 'n',  // Old syntax for images
		'wikiplugininline_pluginmanager' => 'n',
		'wikiplugininline_poll' => 'n',
		'wikiplugininline_profile' => 'n',
		'wikiplugininline_proposal' => 'n',
		'wikiplugininline_quote' => 'n',
		'wikiplugininline_rcontent' => 'n',
		'wikiplugininline_redirect' => 'n',
		'wikiplugininline_regex' => 'n',
		'wikiplugininline_remarksbox' => 'n',
		'wikiplugininline_rss' => 'n',
		'wikiplugininline_sf' => 'n',
		'wikiplugininline_share' => 'n',
		'wikiplugininline_sharethis' => 'n',
		'wikiplugininline_sheet' => 'n',
		'wikiplugininline_showpages' => 'n',
		'wikiplugininline_skype' => 'n',
		'wikiplugininline_snarf' => 'n',
		'wikiplugininline_sort' => 'n',
		'wikiplugininline_split' => 'n',
		'wikiplugininline_sql' => 'n',
		'wikiplugininline_sub' => 'n',
		'wikiplugininline_subscribegroup' => 'n',
		'wikiplugininline_subscribegroups' => 'n',
		'wikiplugininline_sup' => 'n',
		'wikiplugininline_survey' => 'n',
		'wikiplugininline_tag' => 'n',
		'wikiplugininline_thumb' => 'n',
		'wikiplugininline_titlesearch' => 'n',
		'wikiplugininline_toc' => 'n',
		'wikiplugininline_topfriends' => 'n',
		'wikiplugininline_trackerfilter' => 'n',
		'wikiplugininline_trackeritemfield' => 'y',
		'wikiplugininline_trackerlist' => 'n',
		'wikiplugininline_trackertimeline' => 'n',
		'wikiplugininline_tracker' => 'n',
		'wikiplugininline_trackerprefill' => 'n',
		'wikiplugininline_trackerstat' => 'n',
		'wikiplugininline_translated' => 'n',
		'wikiplugininline_tr' => 'n',
		'wikiplugininline_usercount' => 'n',
		'wikiplugininline_userlist' => 'n',
		'wikiplugininline_versions' => 'n',
		'wikiplugininline_vote' => 'n',
		'wikiplugininline_wantedpages' => 'n',
		'wikiplugininline_webservice' => 'n',
		'wikiplugininline_youtube' => 'n',

		// webservices
		'webservice_consume_defaultcache' => 300, // 5 min

		// wysiwyg
		'feature_wysiwyg' => 'n',
		'wysiwyg_optional' => 'y',
		'wysiwyg_default' => 'n',
		'wysiwyg_memo' => 'y',
		'wysiwyg_wiki_parsed' => 'y',
		'wysiwyg_wiki_semi_parsed' => 'n',
		'wysiwyg_toolbar_skin' => 'default',
		'wysiwyg_toolbar' =>"FitWindow,Templates,-,Cut,Copy,Paste,PasteText,PasteWord,Print,SpellCheck
Undo,Redo,-,Find,Replace,SelectAll,RemoveFormat,-,Table,Rule,Smiley,SpecialChar,PageBreak,ShowBlocks
/
JustifyLeft,JustifyCenter,JustifyRight,JustifyFull,-,OrderedList,UnorderedList,Outdent,Indent,Blockquote
Bold,Italic,Underline,StrikeThrough,-,Subscript,Superscript,-,tikilink,Link,Unlink,Anchor,-,tikiimage,Flash
/
Style,FontName,FontSize,-,TextColor,BGColor,-,Source",

		// wiki3d
		'wiki_feature_3d' => 'n',
		'wiki_3d_width' => 500,
		'wiki_3d_height' => 500,
		'wiki_3d_navigation_depth' => 1,
		'wiki_3d_feed_animation_interval' => 500,
		'wiki_3d_existing_page_color' => '#00CC55',
		'wiki_3d_missing_page_color' => '#FF5555',

		// blogs
		'feature_blogs' => 'n',
		'blog_list_order' => 'created_desc',
		'home_blog' => 0,
		'feature_blog_rankings' => 'n',
		'feature_blog_comments' => 'n',
		'blog_comments_default_ordering' => 'points_desc',
		'blog_comments_per_page' => 10,
		'feature_blogposts_comments' => 'n',
		'blog_list_user' => 'text',
		'blog_list_title' => 'y',
		'blog_list_title_len' => '35',
		'blog_list_description' => 'y',
		'blog_list_created' => 'y',
		'blog_list_lastmodif' => 'y',
		'blog_list_posts' => 'y',
		'blog_list_visits' => 'y',
		'blog_list_activity' => 'n',
		'feature_blog_mandatory_category' => '-1',
		'feature_blog_heading' => 'y',

		// filegals
		'feature_file_galleries' => 'y',
		'home_file_gallery' => 0,
		'fgal_use_db' => 'y',
		'fgal_batch_dir' => '',
		'fgal_match_regex' => '',
		'fgal_nmatch_regex' => '',
		'fgal_use_dir' => '',
		'fgal_podcast_dir' => 'files/',
		'feature_file_galleries_comments' => 'n',
		'file_galleries_comments_default_ordering' => 'points_desc',
		'file_galleries_comments_per_page' => 10,
		'feature_file_galleries_batch' => 'n',
		'feature_file_galleries_rankings' => 'n',
		'fgal_enable_auto_indexing' => 'y',
		'fgal_asynchronous_indexing' => 'y',
		'fgal_allow_duplicates' => 'different_galleries',
		'fgal_sort_mode' => '',
		'feature_file_galleries_author' => 'n',
		'fgal_list_id' => 'n',
		'fgal_list_type' => 'y',
		'fgal_list_name' => 'n',
		'fgal_list_description' => 'o',
		'fgal_list_size' => 'y',
		'fgal_list_created' => 'o',
		'fgal_list_lastmodif' => 'y',
		'fgal_list_creator' => 'o',
		'fgal_list_author' => 'o',
		'fgal_list_last_user' => 'o',
		'fgal_list_comment' => 'o',
		'fgal_list_files' => 'o',
		'fgal_list_hits' => 'o',
		'fgal_list_lockedby' => 'a',
		'fgal_show_path' => 'y',
		'fgal_show_explorer' => 'y',
		'fgal_show_slideshow' => 'n',
		'fgal_default_view' => 'list',
		'fgal_limit_hits_per_file' => 'n',
		'fgal_prevent_negative_score' => 'n',

		// imagegals
		'feature_galleries' => 'n',
		'feature_gal_batch' => 'n',
		'feature_gal_slideshow' => 'n',
		'home_gallery' => 0,
		'gal_use_db' => 'y',
		'gal_use_lib' => 'imagick',
		'gal_match_regex' => '',
		'gal_nmatch_regex' => '',
		'gal_use_dir' => '',
		'gal_batch_dir' => '',
		'feature_gal_rankings' => 'n',
		'feature_image_galleries_comments' => 'n',
		'image_galleries_comments_default_order' => 'points_desc',
		'image_galleries_comments_per_page' => 10,
		'gal_list_name' => 'y',
		'gal_list_parent' => 'n',
		'gal_list_description' => 'y',
		'gal_list_created' => 'n',
		'gal_list_lastmodif' => 'y',
		'gal_list_user' => 'n',
		'gal_list_imgs' => 'y',
		'gal_list_visits' => 'y',
		'feature_image_gallery_mandatory_category' => '-1',
		'preset_galleries_info' =>'n',
		'gal_image_mouseover' => 'n',

		// multimedia
		'ProgressBarPlay' => '//FF8D41',
		'ProgressBarLoad' => "//A7A7A7",
		'ProgressBarButton' => "//FF0000",
		'ProgressBar' => "//C3C3C3",
		'VolumeOn' => "//21AC2A",
		'VolumeOff' => "//8EFF8A",
		'VolumeButton' => 0,
		'Button' => "//555555",
		'ButtonPressed' => "//FF00FF",
		'ButtonOver' => "//B3B3B3",
		'ButtonInfo' => "//C3C3C3",
		'ButtonInfoPressed' => "//555555",
		'ButtonInfoOver' => "//FF8D41",
		'ButtonInfoText' => "//FFFFFF",
		'ID3' => "//6CDCEB",
		'PlayTime' => "//00FF00",
		'TotalTime' => "//FF2020",
		'PanelDisplay' => "//555555",
		'AlertMesg' => "//00FFFF",
		'PreloadDelay' => 3,
		'VideoHeight' => 240,
		'VideoLength' => 300,
		'ProgressBarPlay' => "//FFFFFF",
		'URLAppend' => "",
		'LimitedMsg' => "You are limited to 1 minute",
		'MaxPlay' => 60,
		'MultimediaGalerie' => 1,
		'MultimediaDefaultLength' => 200,
		'MultimediaDefaultHeight' => 100,

		// forums
		'feature_forums' => 'n',
		'home_forum' => 0,
		'feature_forum_rankings' => 'n',
		'feature_forum_parse' => 'n',
		'feature_forum_topics_archiving' => 'n',
		'feature_forum_replyempty' => 'n',
		'feature_forum_quickjump' => 'n',
		'feature_forums_allow_thread_titles' => 'n',
		'feature_forum_content_search' => 'y',
		'feature_forums_name_search' => 'y',
		'forums_ordering' => 'created_desc',
		'forum_list_topics' =>  'n',
		'forum_list_posts' =>  'y',
		'forum_list_ppd' =>  'n',
		'forum_list_lastpost' =>  'y',
		'forum_list_visits' =>  'y',
		'forum_list_desc' =>  'y',
		'forum_list_description_len' => '240',
		'feature_forum_local_search' => 'n',
		'feature_forum_local_tiki_search' => 'n',
		'forum_thread_defaults_by_forum' => 'n',
		'forum_thread_user_settings' => 'y',
		'forum_thread_user_settings_keep' => 'n',
		'forum_comments_per_page' => 20,
		'forum_comments_no_title_prefix' => 'n',
		'forum_thread_style' => 'commentStyle_plain',
		'forum_thread_sort_mode' => 'commentDate_asc',
		'forum_match_regex' => '',

		// articles
		'feature_articles' => 'n',
		'feature_submissions' => 'n',
		'feature_cms_rankings' => 'n',
		'feature_cms_print' => 'y',
		'feature_cms_emails' => 'n',
		'art_list_title' => 'y',
		'art_list_title_len' => '20',
		'art_list_topic' => 'y',
		'art_list_date' => 'y',
		'art_list_author' => 'y',
		'art_list_reads' => 'y',
		'art_list_size' => 'y',
		'art_list_expire' => 'y',
		'art_list_img' => 'y',
		'art_list_type' => 'y',
		'art_list_visible' => 'y',
		'art_view_type' => 'y',
		'art_view_title' => 'y',
		'art_view_topic' => 'y',
		'art_view_date' => 'y',
		'art_view_author' => 'y',
		'art_view_reads' => 'y',
		'art_view_size' => 'y',
		'art_view_img' => 'y',
		'art_list_lang' => 'n',
		'feature_article_comments' => 'n',
		'article_comments_default_ordering' => 'points_desc',
		'article_comments_per_page' => 10,
		'article_image_size_x' => '0',
		'article_image_size_y' => '0',
		'feature_cms_templates' => 'n',
		'cms_bot_bar' => 'y',
		'cms_left_column' => 'y',
		'cms_right_column' => 'y',
		'cms_top_bar' => 'n',
		'cms_spellcheck' => 'n',
		'art_home_title' => '',

		// trackers
		'feature_trackers' => 'n',
		't_use_db' => 'y',
		't_use_dir' => '',
		'groupTracker' => 'n',
		'userTracker' => 'n',
		'trk_with_mirror_tables' => 'n',

		// user
		'feature_userlevels' => 'n',
		'userlevels' => function_exists('tra') ? array('1'=>tra('Simple'),'2'=>tra('Advanced')) : array('1'=>'Simple','2'=>'Advanced'),
		'userbreadCrumb' => 4,
		'user_assigned_modules' => 'n',
		'user_flip_modules' => 'module',
		'user_show_realnames' => 'n',
		'feature_mytiki' => 'n',
		'feature_userPreferences' => 'n',
		'feature_user_bookmarks' => 'n',
		'feature_tasks' => 'n',
		'w_use_db' => 'y',
		'w_use_dir' => '',
		'w_displayed_default' => 'n',
		'uf_use_db' => 'y',
		'uf_use_dir' => '',
		'userfiles_quota' => 30,
		'feature_usermenu' => 'n',
		'feature_minical' => 'n',
		'feature_notepad' => 'n',
		'feature_userfiles' => 'n',
		'feature_community_gender' => 'n',
		'feature_community_mouseover' => 'n',
		'feature_community_mouseover_name' => 'y',
		'feature_community_mouseover_gender' => 'y',
		'feature_community_mouseover_picture' => 'y',
		'feature_community_mouseover_friends' => 'y',
		'feature_community_mouseover_score' => 'y',
		'feature_community_mouseover_country' => 'y',
		'feature_community_mouseover_email' => 'y',
		'feature_community_mouseover_lastlogin' => 'y',
		'feature_community_mouseover_distance' => 'y',
		'feature_community_list_name' => 'y',
		'feature_community_list_score' => 'y',
		'feature_community_list_country' => 'y',
		'feature_community_list_distance' => 'y',
		'feature_community_friends_permission' => 'n',
		'feature_community_friends_permission_dep' => '2',
		'change_language' => 'y',
		'change_theme' => 'n',
		'login_is_email' => 'n',
		'validateUsers' => 'y',
		'validateEmail' => 'n',
		'forgotPass' => 'y',
		'change_password' => 'y',
		'available_languages' => array(),
		'available_styles' => array(),
		'lowercase_username' => 'n',
		'username_pattern' => '/^[ \'\-_a-zA-Z0-9@\.]*$/',
		'max_username_length' => '50',
		'min_username_length' => '1',
		'users_prefs_allowMsgs' => 'y',
		'users_prefs_country' => '',
		'users_prefs_diff_versions' => 'n',
		'users_prefs_display_timezone' => 'Local',
		'users_prefs_email_is_public' => 'n',
		'users_prefs_homePage' => '',
		'users_prefs_lat' => '0',
		'users_prefs_lon' => '0',
		'users_prefs_mess_archiveAfter' => '0',
		'users_prefs_mess_maxRecords' => '10',
		'users_prefs_mess_sendReadStatus' => 'n',
		'users_prefs_minPrio' => '3',
		'users_prefs_mytiki_blogs' => 'y',
		'users_prefs_mytiki_articles' => 'y',
		'users_prefs_mytiki_gals' => 'y',
		'users_prefs_mytiki_items' => 'y',
		'users_prefs_mytiki_msgs' => 'y',
		'users_prefs_mytiki_pages' => 'y',
		'users_prefs_mytiki_tasks' => 'y',
		'users_prefs_mytiki_workflow' => 'y',
		'users_prefs_mytiki_forum_topics' => 'y',
		'users_prefs_mytiki_forum_replies' => 'y',
		'users_prefs_realName' => '',
		'users_prefs_gender' => '',
		'users_prefs_show_mouseover_user_info' => 'n',
		'users_prefs_tasks_maxRecords' => '10',
		'users_prefs_user_dbl' => 'n',
		'users_prefs_user_information' => 'private',
		'users_prefs_userbreadCrumb' => '4',
		'users_prefs_mailCharset' => 'utf-8',
		'validateRegistration' => 'n',

		// user messages
		'feature_messages' => 'n',
		'messu_mailbox_size' => '0',
		'messu_archive_size' => '200',
		'messu_sent_size' => '200',
		'allowmsg_by_default' => 'y',
		'allowmsg_is_optional' => 'y',

		// newsreader
		'feature_newsreader' => 'n',

		// freetags
		'feature_freetags' => 'n',
		'freetags_browse_show_cloud' => 'y',
		'freetags_cloud_colors' => '',
		'freetags_preload_random_search' => 'y',
		'freetags_browse_amount_tags_in_cloud' => '100',
		'freetags_browse_amount_tags_suggestion' => '10',
		'freetags_normalized_valid_chars' => '',
		'freetags_lowercase_only' => 'y',
		'freetags_feature_3d' => 'n',
		'freetags_3d_width' => 500,
		'freetags_3d_height' => 500,
		'freetags_3d_navigation_depth' => 1,
		'freetags_3d_feed_animation_interval' => 500,
		'freetags_3d_existing_page_color' => '#00CC55',
		'freetags_3d_missing_page_color' => '#FF5555',
		'freetags_3d_autoload' => 'false',
		'freetags_3d_camera_distance' => '200',
		'freetags_3d_elastic_constant' => '0.5f',
		'freetags_3d_eletrostatic_constant' => '1000f',
		'freetags_3d_fov' => '250',
		'freetags_3d_friction_constant' => '0.4f',
		'freetags_3d_node_charge' => '1',
		'freetags_3d_node_mass' => '5',
		'freetags_3d_node_size' => '30',
		'freetags_3d_spring_size' => '100',
		'freetags_3d_text_size' => '40',
		'freetags_3d_adjust_camera' => 'false',
		'freetags_multilingual' => 'n',
		'morelikethis_algorithm' => 'basic',
		'morelikethis_basic_mincommon' => '2',
		'freetags_show_middle' => 'y',

		// search
		'feature_search_stats' => 'n',
		'feature_search' => 'n',
		'feature_search_fulltext' => 'y',
		'feature_search_show_forbidden_obj' => 'n',
		'feature_search_show_forbidden_cat' => 'n',
		'feature_search_show_object_filter' => 'n',
		'feature_search_show_search_box' => 'y',
		'feature_search_show_visit_count' => 'n',
		'feature_search_show_pertinence' => 'n',
		'feature_search_show_object_type' => 'n',
		'feature_search_show_last_modification' => 'y',
		'search_refresh_index_mode' => 'normal',
		'search_parsed_snippet' => 'y',

		// webmail
		'feature_webmail' => 'n',
		'webmail_max_attachment' => 1500000,
		'webmail_view_html' => 'y',

		// contacts
		'feature_contacts' => 'n',

		// faq
		'feature_faqs' => 'n',
		'feature_faq_comments' => 'y',
		'faq_comments_per_page' => 10,
		'faq_comments_default_ordering' => 'points_desc',
		'faq_prefix' => 'QA',

		// quizzes
		'feature_quizzes' => 'n',

		// polls
		'feature_polls' => 'n',
		'feature_poll_comments' => 'n',
		'feature_poll_anonymous' => 'n',
		'poll_comments_default_ordering' => 'points_desc',
		'poll_comments_per_page' => 10,
		'poll_list_categories' => 'n',
		'poll_list_objects' => 'n',
		'feature_poll_revote' => 'y',

		// surveys
		'feature_surveys' => 'n',

		// featured links
		'feature_featuredLinks' => 'n',

		// directories
		'feature_directory' => 'n',
		'directory_columns' => 3,
		'directory_links_per_page' => 20,
		'directory_open_links' => 'n',
		'directory_validate_urls' => 'n',
		'directory_cool_sites' => 'y',
		'directory_country_flag' => 'y',

		// calendar
		'feature_calendar' => 'n',
		'feature_default_calendars' => 'n',
		'calendar_sticky_popup' => 'y',
		'default_calendars' => array(),
		'calendar_view_mode' => 'month',
		'calendar_view_tab' => 'n',
		'calendar_firstDayofWeek' => 'user',
		'calendar_timespan' => '5',
		'feature_jscalendar' => 'y',
		'feature_action_calendar' => 'n',
		'calendar_start_year' => '+0',
		'calendar_end_year' => '+3',
		'calendar_list_begins_focus' => 'n',

		// dates
		'server_timezone' => isset($tikidate) ? $tikidate->getTimezoneId() : 'UTC',
		'long_date_format' => '%A %d of %B, %Y',
		'long_time_format' => '%H:%M:%S %Z',
		'short_date_format' => '%a %d of %b, %Y',
		'short_time_format' => '%H:%M %Z',
		'display_field_order' => 'MDY',

		// charts
		'feature_charts' => 'n',

		// rss
		'rss_forums' => 'n',
		'rss_forum' => 'n',
		'rss_directories' => 'n',
		'rss_articles' => 'n',
		'rss_blogs' => 'n',
		'rss_image_galleries' => 'n',
		'rss_file_galleries' => 'n',
		'rss_wiki' => 'n',
		'rss_image_gallery' => 'n',
		'rss_file_gallery' => 'n',
		'rss_blog' => 'n',
		'rss_tracker' => 'n',
		'rss_trackers' => 'n',
		'rss_calendar' => 'n',
		'rss_mapfiles' => 'n',
		'rss_cache_time' => '0', // 0 = disabled (default)
		'max_rss_forums' => 10,
		'max_rss_forum' => 10,
		'max_rss_directories' => 10,
		'max_rss_articles' => 10,
		'max_rss_blogs' => 10,
		'max_rss_image_galleries' => 10,
		'max_rss_file_galleries' => 10,
		'max_rss_wiki' => 10,
		'max_rss_image_gallery' => 10,
		'max_rss_file_gallery' => 10,
		'max_rss_blog' => 10,
		'max_rss_mapfiles' => 10,
		'max_rss_tracker' => 10,
		'max_rss_trackers' => 10,
		'max_rss_calendar' => 10,
		'summary_rss_blogs' => 'n',
		'rssfeed_default_version' => '2',
		'rssfeed_language' =>  'en-us',
		'rssfeed_editor' => '',
		'rssfeed_webmaster' => '',
		'rssfeed_creator' => '',
		'rssfeed_css' => 'y',
		'rssfeed_publisher' => '',
		'rssfeed_img' => 'img/tiki/tikilogo.png',
		'rss_basic_auth' => 'n',

		// maps
		'feature_maps' => 'n',
		'map_path' => '',
		'default_map' => '',
		'map_help' => 'MapsHelp',
		'map_comments' => 'MapsComments',
		'gdaltindex' => '',
		'ogr2ogr' => '',
		'mapzone' => '',

		// gmap
		'feature_gmap' => 'n',
		'gmap_defaultx' => '0',
		'gmap_defaulty' => '0',
		'gmap_defaultz' => '1',
		'gmap_key' => '',

		// auth
		'allowRegister' => 'n',
		'eponymousGroups' => 'n',
		'useRegisterPasscode' => 'n',
		'registerPasscode' => isset($tikilib) ? md5($tikilib->genPass()) : md5(mt_rand()),
		'rememberme' => 'disabled',
		'remembertime' => 7200,
		'feature_clear_passwords' => 'n',
		'feature_crypt_passwords' => (CRYPT_MD5 == 1)? 'crypt-md5': 'tikihash',
		'feature_challenge' => 'n',
		'min_user_length' => 1,
		'min_pass_length' => 1,
		'pass_chr_num' => 'n',
		'pass_due' => -1,
		'email_due' => -1,
		'unsuccessful_logins' => 20,
		'rnd_num_reg' => 'y',
		'generate_password' => 'n',
		'auth_method' => 'tiki',
		'auth_pear' => 'tiki',
		'auth_create_user_tiki' => 'n',
		'auth_create_user_auth' => 'n',
		'auth_skip_admin' => 'y',
		'auth_ldap_url' => '',
		'auth_pear_host' => "localhost",
		'auth_pear_port' => "389",
		'auth_ldap_scope' => "sub",
		'auth_ldap_basedn' => '',
		'auth_ldap_userdn' => '',
		'auth_ldap_userattr' => 'uid',
		'auth_ldap_useroc' => 'inetOrgPerson',
		'auth_ldap_groupdn' => '',
		'auth_ldap_groupattr' => 'cn',
		'auth_ldap_groupoc' => 'groupOfUniqueNames',
		'auth_ldap_memberattr' => 'uniqueMember',
		'auth_ldap_memberisdn' => 'y',
		'auth_ldap_adminuser' => '',
		'auth_ldap_adminpass' => '',
		'auth_ldap_version' => 3,
		'auth_ldap_nameattr' => 'displayName',
		'auth_ldap_countryattr' => '',
		'auth_ldap_emailattr' => '',
		'https_login' => 'allowed',
		'feature_show_stay_in_ssl_mode' => 'y',
		'feature_switch_ssl_mode' => 'n',
		'https_port' => 443,
		'http_port' => 80,
		'login_url' => 'tiki-login.php',
		'login_scr' => 'tiki-login_scr.php',
		'register_url' => 'tiki-register.php',
		'error_url' => 'tiki-error.php',
		'highlight_group' => '0',
		'cookie_path' => '/',
		'cookie_domain' => '',
		'cookie_name' => 'tikiwiki',
		'user_tracker_infos' => '',
		'desactive_login_autocomplete' => 'n',
		'permission_denied_login_box' => 'n',
		'permission_denied_url' => '',

		// intertiki
		'feature_intertiki' => 'n',
		'feature_intertiki_server' => 'n',
		'feature_intertiki_slavemode' => 'n',
		'interlist' => array(),
		'feature_intertiki_mymaster' => '',
		'feature_intertiki_import_preferences' => 'n',
		'feature_intertiki_import_groups' => 'n',
		'known_hosts' => array(),
		'tiki_key' => '',
		'intertiki_logfile' => '',
		'intertiki_errfile' => '',
		'feature_intertiki_sharedcookie' => 'n',

		// search
		'search_lru_length' => '100',
		'search_lru_purge_rate' => '5',
		'search_max_syllwords' => '100',
		'search_min_wordlength' => '3',
		'search_refresh_rate' => '5',
		'search_syll_age' => '48',

		// categories
		'feature_categories' => 'n',
		'feature_categoryobjects' => 'n',
		'feature_categorypath' => 'n',
		'feature_category_reinforce' => 'y',
		'feature_category_use_phplayers' => 'y',
		'categorypath_excluded' => '',
		'categories_used_in_tpl' => 'n',

		// games
		'feature_games' => 'n',

		// html pages
		'feature_html_pages' => 'n',

		// use filegals for image inclusion
		'feature_filegals_manager' => 'y',

		// contact & mail
		'feature_contact' => 'n',
		'contact_user' => 'admin',
		'contact_anon' => 'n',
		'mail_crlf' => 'LF',

		// i18n
		'feature_detect_language' => 'n',
		'feature_homePage_if_bl_missing' => 'n',
		'record_untranslated' => 'n',
		'feature_best_language' => 'n',
		'feature_translation' => 'n',
		'feature_urgent_translation' => 'n',
		'lang_use_db' => 'n',
		'language' => 'en',
		'feature_babelfish' => 'n',
		'feature_babelfish_logo' => 'n',
		'quantify_changes' => 'n',
		'feature_sync_language' => 'n',
		'show_available_translations' =>'y',

		// html header
		'metatag_keywords' => '',
		'metatag_threadtitle' => 'n',
		'metatag_imagetitle' => 'n',
		'metatag_freetags' => 'n',
		'metatag_description' => '',
		'metatag_author' => '',
		'metatag_geoposition' => '',
		'metatag_georegion' => '',
		'metatag_geoplacename' => '',
		'metatag_robots' => '',
		'metatag_revisitafter' => '',
		'head_extra_js' => array(),
		'keep_versions' => 1,
		'feature_custom_home' => 'n',

		// look and feel

		'site_crumb_seper' => '»',
		'site_nav_seper' => '|',
		'feature_sitemycode' => 'y',
		'sitemycode' => '{if $user eq "admin"}
<div style="align: left; padding-left: 15px;">
<a class="link" href="tiki-admin.php?page=look">
{tr}Modify the look & feel (logo, theme, etc.){/tr}
</a>
</div>
{/if}', // must be max. 250 chars now unless it'll change in tiki_prefs db table field value from VARCHAR(250) to BLOB by default
		'sitemycode_publish' => 'n',
		'feature_sitelogo' => 'y',
		'sitelogo_bgcolor' => 'transparent',
		'sitelogo_bgstyle' => '',
		'sitelogo_align' => 'left',
		'sitelogo_title' => 'Tikiwiki powered site',
		'sitelogo_src' => 'img/tiki/tiki3.png',
		'sitelogo_alt' => 'Site Logo',
		'feature_siteloc' => 'y',
		'feature_sitenav' => 'n',
		'sitenav' => '{tr}Navigation : {/tr}<a href="tiki-contact.php" accesskey="10" title="">{tr}Contact Us{/tr}</a>',
		'feature_sitead' => 'y',
		'sitead' => '',
		'sitead_publish' => 'n',
		'feature_breadcrumbs' => 'n',
		'feature_siteloclabel' => 'y',
		'feature_sitesearch' => 'y',
		'feature_site_login' => 'y',
		'feature_sitemenu' => 'n',
		'feature_topbar_version' => 'n',
		'feature_topbar_debug' => 'n',
		'feature_topbar_id_menu' => '42',
		'feature_topbar_custom_code' => '',
		'feature_sitetitle' => 'y',
		'feature_sitedesc' => 'n',
		'feature_bot_logo' => 'n',
		'feature_endbody_code' => '',

		// layout
		'feature_left_column' => 'y',
		'feature_right_column' => 'y',
		'feature_top_bar' => 'n',
		'feature_bot_bar' => 'y',
		'feature_bot_bar_icons' => 'n',
		'feature_bot_bar_debug' => 'n',
		'feature_bot_bar_rss' => 'y',
		'feature_bot_bar_power_by_tw' => 'y',
		'maxRecords' => 25,
		'maxArticles' => 10,
		'maxVersions' => 0,
		'feature_view_tpl' => 'n',
		'slide_style' => 'slidestyle.css',
		'site_favicon' => 'favicon.png',
		'site_favicon_type' => 'image/png',
		'style' => 'thenews.css',
		'style_option' => '',
		'site_style' => 'thenews.css',
		'site_style_option' => '',
		'use_context_menu_icon' => 'y',
		'use_context_menu_text' => 'y',
		'feature_site_report' => 'n',
		'feature_site_send_link' => 'n',
		'feature_layoutshadows' => 'n',
		'main_shadow_start' => '',
		'main_shadow_end' => '',
		'header_shadow_start' => '',
		'header_shadow_end' => '',
		'middle_shadow_start' => '',
		'middle_shadow_end' => '',
		'center_shadow_start' => '',
		'center_shadow_end' => '',
		'footer_shadow_start' => '',
		'footer_shadow_end' => '',

		// mods
		'feature_mods_provider' => 'n',
		'mods_dir' => 'mods',
		'mods_server' => 'http://mods.tikiwiki.org',

		// dev
		'feature_experimental' => 'n',

		// Action logs
		'feature_actionlog' => 'n',
		'feature_actionlog_bytes' => 'n',

		// admin
		'browsertitle' => 'Tiki 3.0',
		'tmpDir' => 'temp',

		// tell a friend
		'feature_tell_a_friend' => 'n',

		// copyright
		'feature_copyright' => 'n',
		'feature_multimedia' => 'n',

		// swfobj
		'feature_swfobj' => 'n',

		// textarea
		'feature_smileys' => 'n',
		'popupLinks' => 'y',
		'feature_autolinks' => 'y',
		'quicktags_over_textarea' => 'y',
		'default_rows_textarea_wiki' => '20',
		'default_rows_textarea_comment' => '6',
		'default_rows_textarea_forum' => '20',
		'default_rows_textarea_forumthread' => '10',
		'feature_floating_help' => 'y',

		// pagination
		'direct_pagination' => 'y',
		'nextprev_pagination' => 'y',
		'pagination_firstlast' => 'y',
		'pagination_hide_if_one_page' => 'y',
		'pagination_icons' => 'y',
		'pagination_fastmove_links' => 'y',
		'direct_pagination_max_middle_links' => 2,
		'direct_pagination_max_ending_links' => 0,

		// unsorted features
		'anonCanEdit' => 'n',
		'cacheimages' => 'n',
		'cachepages' => 'n',
		'count_admin_pvs' => 'n',
		'default_mail_charset' =>'utf-8',
		'error_reporting_adminonly' => 'y',
		'error_reporting_level' => 0,
		'smarty_notice_reporting' => 'n',
		'smarty_security' => 'y',
		'feature_ajax' => 'n',
		'feature_ajax_autosave' => 'n',
		'feature_antibot' => 'y',
		'feature_banners' => 'n',
		'feature_banning' => 'n',
		'feature_comm' => 'n',
		'feature_contribution' => 'n',
		'feature_contribution_display_in_comment' => 'y',
		'feature_contribution_mandatory' => 'n',
		'feature_contribution_mandatory_blog' => 'n',
		'feature_contribution_mandatory_comment' => 'n',
		'feature_contribution_mandatory_forum' => 'n',
		'feature_debug_console' => 'n',
		'feature_debugger_console' => 'n',
		'feature_display_my_to_others' => 'n',
		'feature_drawings' => 'n',
		'feature_dynamic_content' => 'n',
		'feature_edit_templates' => 'n',
		'feature_editcss' => 'n',
		'feature_events' => 'n',
		'feature_friends' => 'n',
		'feature_fullscreen' => 'n',
		'feature_help' => 'y',
		'feature_hotwords' => 'n',
		'feature_hotwords_nw' => 'n',
		'feature_integrator' => 'n',
		'feature_live_support' => 'n',
		'feature_mailin' => 'n',
		'feature_menusfolderstyle' => 'y',
		'feature_mobile' => 'n',
		'feature_modulecontrols' => 'n',
		'feature_morcego' => 'n',
		'feature_multilingual' => 'n',
		'feature_multilingual_one_page' => 'n',
		'feature_multilingual_structures' => 'n',
		'feature_newsletters' => 'n',
		'feature_obzip' => 'n',
		'feature_phplayers' => 'y', // Enabled by default for a better file gallery tree explorer
		'feature_cssmenus' => 'n',
		'feature_projects' => 'n',
		'feature_ranking' => 'n',
		'feature_redirect_on_error' => 'n',
		'feature_referer_highlight' => 'n',
		'feature_referer_stats' => 'n',
		'feature_score' => 'n',
		'feature_sheet' => 'n',
		'feature_shoutbox' => 'n',
		'feature_source' => 'y',
		'feature_stats' => 'n',
		'feature_tabs' => 'y',
		'feature_theme_control' => 'n',
		'feature_ticketlib' => 'n',
		'feature_ticketlib2' => 'y',
		'feature_top_banner' => 'n',
		'feature_usability' => 'n',
		'feature_use_quoteplugin' => 'n',
		'feature_user_watches' => 'n',
		'feature_group_watches' => 'n',
		'feature_user_watches_translations' => 'n',
		'feature_workflow' => 'n',
		'feature_xmlrpc' => 'n',
		'helpurl' => "http://doc.tikiwiki.org/",
		'layout_section' => 'n',
		'limitedGoGroupHome' => 'n',
		'minical_reminders' => 0,
		'modallgroups' => 'n',
		'modseparateanon' => 'n',
		'php_docroot' => 'http://php.net/',
		'proxy_host' => '',
		'proxy_port' => '',
		'ip_can_be_checked' => 'n',
		'sender_email' => '',
		'feature_site_report_email' => '',
		'session_db' => 'n',
		'session_lifetime' => 0,
		'shoutbox_autolink' => 'n',
		'show_comzone' => 'n',
		'system_os' => class_exists('TikiSetup') ? TikiSetup::os() : 'unix',
		'tikiIndex' => 'tiki-index.php',
		'urlIndex' => '',
		'useGroupHome' => 'n',
		'useGroupTheme' => 'n',
		'useUrlIndex' => 'n',
		'use_proxy' => 'n',
		'user_list_order' => 'score_desc',
		'webserverauth' => 'n',
		'feature_purifier' => 'n',
		'feature_shadowbox' => 'y',
		'log_sql' => 'n',
		'log_sql_perf_min' => '0.05',
		'log_mail' => 'n',
		'log_tpl' => 'n',

		'case_patched' => 'n',
		'site_closed' => 'n',
		'site_closed_msg' => 'Site is closed for maintenance; please come back later.',
		'use_load_threshold' => 'n',
		'load_threshold' => 3,
		'site_busy_msg' => 'Server is currently too busy; please come back later.',

		'bot_logo_code' => '',
		'feature_blogposts_pings' => 'n',
		'feature_create_webhelp' => 'n',
		'feature_forums_search' => 'n',
		'feature_trackbackpings' => 'n',
		'feature_wiki_ext_icon' => 'y',
		'feature_wiki_mandatory_category' => -1,
		'feature_intertiki_imported_groups' => '',
		'feature_wiki_history_ip' => 'n',
		'pam_create_user_tiki' => 'n',
		'pam_service' => '',
		'pam_skip_admin' => 'n',
		'shib_affiliation' => '',
		'shib_create_user_tiki' => 'n',
		'shib_group' => 'Shibboleth',
		'shib_skip_admin' => 'n',
		'shib_usegroup' => 'n',
		'wiki_3d_camera_distance' => '200',
		'wiki_3d_elastic_constant' => '0.3f',
		'wiki_3d_eletrostatic_constant' => '1000f',
		'wiki_3d_fov' => '250',
		'wiki_3d_friction_constant' => '0.8f',
		'wiki_3d_node_charge' => '1',
		'wiki_3d_node_mass' => '5',
		'wiki_3d_node_size' => '15',
		'wiki_3d_spring_size' => '100',
		'wiki_3d_text_size' => '20',
		'articles_feature_copyrights' => 'n',
		'blogues_feature_copyrights' => 'n',
		'faqs_feature_copyrights' => 'n',
		'feature_contributor_wiki' => '',
		'https_login_required' => '',
		'maxRowsGalleries' => '',
		'replimaster' => '',
		'rowImagesGalleries' => '',
		'scaleSizeGalleries' => '',
		'thumbSizeXGalleries' => '',
		'thumbSizeYGalleries' => '',
		'wiki_3d_adjust_camera' => 'true',
		'wiki_3d_autoload' => '',
		'feature_mootools' => 'y', // Needed for shadowbox
		'javascript_enabled' => 'n',
		'feature_comments_post_as_anonymous' => 'n',
		'feature_comments_moderation' => 'n',
		'feature_comments_locking' => 'n',
		'feature_template_zoom' => 'y',
		'feature_iepngfix' => 'n',
		'iepngfix_selectors' => '#sitelogo a img',
		'iepngfix_elements' => '',
		'use_minified_scripts' => 'y',		// for debugging
		'valid_email_regex' => '^[_a-z0-9\+\.\-]+@[_a-z0-9\.\-]+\.[a-z]{2,4}$',

		// JQuery
		'feature_jquery' => 'n',			// Alternative lib for shadowbox etc
		'jquery_effect' => '',				// Default effect for general show/hide: ['' | 'slide' | 'fade' | and
											// see http://docs.jquery.com/UI/Effects: 'blind' | 'clip' | 'explode' etc]
		'jquery_effect_direction' => 'vertical', 	// ['horizontal' | 'vertical' | 'left' | 'right' | 'up' | 'down' ]
		'jquery_effect_speed' => 'normal', 	// ['slow' | 'normal' | 'fast' | milliseconds (int) ]

		'jquery_effect_tabs' => 'slide',	// Different effect for tabs (['none' | 'normal' (for jq) | 'slide' etc]
		'jquery_effect_tabs_direction' => 'vertical',
		'jquery_effect_tabs_speed' => 'fast',

		'feature_jquery_ui' => 'n',				// include UI lib for more effects
		'feature_jquery_tooltips' => 'y',		// use JQuery tooltips and override Overlib
		'feature_jquery_autocomplete' => 'y',	// autocomplete on pages in QuickEdit (more coming soon)
		'feature_jquery_superfish' => 'y',		// Effects on CSS (Suckerfish) menus
		'feature_jquery_reflection' => 'y',		// reflection effects on images
		'feature_jquery_sheet' => 'n',			// spreadsheet TODO: implement
		'feature_jquery_tablesorter' => 'n',	// sortable tables ([will] override existing)
		'feature_jquery_cycle' => 'y',			// slideshow lib

		// SefUrl
		'feature_sefurl' => 'n',
		'feature_sefurl_filter' => 'n',
		'feature_sefurl_paths' => array(),
		'feature_sefurl_title_article' =>'n',
		'feature_sefurl_title_blog' =>'n',

		// TikiTests
		'feature_tikitests' => 'n',

		// Magic Admin Panel
		'feature_magic' => 'n',

		// Tiki Profiles
		'profile_sources' => 'http://profiles.tikiwiki.org/profiles',
		'profile_channels' => '',

		// Minichat
		'feature_minichat' => 'n',

		// Pear::Date
		'feature_pear_date' => 'y',

		'feature_bidi' => 'n',
		'feature_lastup' => 'y',
		'transition_style_ver' => 'none',

		'magic_last_load' => 0,

		//groupalert
		'feature_groupalert' => 'n',
	);

	// spellcheck
	if ( file_exists('lib/bablotron.php') ) {
		$prefs['lib_spellcheck'] = 'y';
		$prefs['wiki_spellcheck'] = 'n';
		$prefs['cms_spellcheck'] = 'n';
		$prefs['blog_spellcheck'] = 'n';
	}
	global $phpcas_enabled;
	if ( $phpcas_enabled == 'y' ) {
		$prefs['cas_create_user_tiki'] = 'n';
		$prefs['cas_skip_admin'] = 'n';
		$prefs['cas_version'] = '1.0';
		$prefs['cas_hostname'] = '';
		$prefs['cas_port'] = '';
		$prefs['cas_path'] = '';
	}

	// Special default values

	if ( is_file('styles/'.$tikidomain.'/'.$prefs['site_favicon']) )
		$prefs['site_favicon'] = 'styles/'.$tikidomain.'/'.$prefs['site_favicon'];
	elseif ( ! is_file($prefs['site_favicon']) )
		$prefs['site_favicon'] = false;

	$_SESSION['tmpDir'] = class_exists('TikiInit') ? TikiInit::tempdir() : '/tmp';

	$prefs['feature_bidi'] = 'n';
	$prefs['feature_lastup'] = 'y';

	// Be sure we have a default value for user prefs
	foreach ( $prefs as $p => $v ) {
		if ( substr($p, 0, 12) == 'users_prefs_' ) {
			$prefs[substr($p, 12)] = $v;
		}
	}

	if ( isset($cachelib) ) $cachelib->cacheItem("tiki_default_preferences_cache",serialize($prefs));
	return $prefs;
}

// Initialize prefs for which we want to use the site value (they will be prefixed with 'site_')
// ( this is also used in tikilib, not only when reloading prefs )
$user_overrider_prefs = array('language', 'style', 'userbreadCrumb', 'tikiIndex', 'wikiHomePage','default_calendars', 'metatag_robots');

// Check if prefs needs to be reloaded
if (isset($_SESSION['s_prefs'])) {

	// lastUpdatePrefs pref is retrived in tiki-setup_base
	$lastUpdatePrefs = $prefs['lastUpdatePrefs'];

	// Reload if there was an update of some prefs
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

$defaults = get_default_prefs();
// Set default prefs only if needed
if ( ! $_SESSION['need_reload_prefs'] ) {
	$modified = $_SESSION['s_prefs'];
} elseif ( isset($tikilib) ) {

	// Find which preferences need to be serialized/unserialized, based on the default values (those with arrays as values)
	if ( ! isset($_SESSION['serialized_prefs']) ) {
		$_SESSION['serialized_prefs'] = array();
		foreach ( $defaults as $p => $v )
			if ( is_array($v) ) $_SESSION['serialized_prefs'][] = $p;
	}

	// Override default prefs with values specified in database
	$modified = $tikilib->get_db_preferences();

	// Unserialize serialized preferences
	if ( isset($_SESSION['serialized_prefs']) && is_array($_SESSION['serialized_prefs']) ) {
		foreach ( $_SESSION['serialized_prefs'] as $p ) {
			if ( isset($modified[$p]) && ! is_array($modified[$p]) ) $modified[$p] = unserialize($modified[$p]);
		}
	}

	// Keep some useful sites values available before overriding with user prefs
	// (they could be used in templates, so we need to set them even for Anonymous)
	global $user_overrider_prefs;
	foreach ( $user_overrider_prefs as $uop ) {
		$modified['site_'.$uop] = isset($modified[$uop])?$modified[$uop]:$defaults[$uop];
	}

	// Assign prefs to the session
	$_SESSION['s_prefs'] = $modified;
}

$prefs = empty($modified) ? $defaults : array_merge( $defaults, $modified );

if ( isset($smarty) ) {
	// Assign the prefs array in smarty, by reference
	$smarty->assign_by_ref('prefs', $prefs);

	// Define the special maxRecords global var
	$maxRecords = $prefs['maxRecords'];
	$smarty->assign_by_ref('maxRecords', $maxRecords);
}
