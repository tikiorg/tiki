<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// RULE1: $prefs does not contain serialized values. Only the database contains serialized values.
// RULE2: put array('') in default prefs for serialized values

//this script may only be included - so its better to die if called directly.
if ( basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__) ) {
  header("location: index.php");
  exit;
}

$user_overrider_prefs = array('language', 'style', 'style_option', 'userbreadCrumb', 'tikiIndex', 'wikiHomePage',
								'default_calendars', 'metatag_robots', 'themegenerator_theme');
initialize_prefs();

function get_default_prefs() {
	static $prefs;
	if( is_array($prefs) )
		return $prefs;

	global $cachelib; require_once 'lib/cache/cachelib.php';
	if( $prefs = $cachelib->getSerialized("tiki_default_preferences_cache") ) {
		return $prefs;
	}
	global $tikidate, $tikilib, $url_host;
	$prefslib = TikiLib::lib('prefs');
	$prefs = $prefslib->getDefaults();
	$prefs = array_merge($prefs, array(
		// tiki and version
		'tiki_release' => '0',
		'tiki_needs_upgrade' => 'n',
		'tiki_version_last_check' => 0,
		'lastUpdatePrefs' => 1,


		'groups_are_emulated' => 'n',


		// wiki
		'backlinks_name_len' => '0',
		'feature_wiki_notepad' => 'n',
		'feature_wiki_feedback_polls' => array(),
		'feature_wiki_pagealias' => 'y',
		'mailin_autocheck' => 'n',
		'mailin_autocheckFreq' => '0',
		'mailin_autocheckLast' => 0,
		'wiki_bot_bar' => 'n',
		'wiki_left_column' => 'y',
		'wiki_page_separator' => '...page...',
		'wiki_right_column' => 'y',
		'wiki_top_bar' => 'y',
		'feature_wiki_watch_structure' => 'n',
		'wiki_validate_plugin' => 'y',

		'wikiplugin_addtocart' => 'n',
		'wikiplugin_addtogooglecal' => 'n',
		'wikiplugin_agentinfo' => 'n',
		'wikiplugin_alink' => 'n',
		'wikiplugin_aname' => 'n',
		'wikiplugin_annotation' => 'n',
		'wikiplugin_archivebuilder' => 'n',
		'wikiplugin_article' => 'y',
		'wikiplugin_articles' => 'y',
		'wikiplugin_attach' => 'y',
		'wikiplugin_attributes' => 'n',
		'wikiplugin_author' => 'y',
		'wikiplugin_avatar' => 'n',
		'wikiplugin_back' => 'n',
		'wikiplugin_backlinks' => 'n',
		'wikiplugin_banner' => 'n',
		'wikiplugin_bigbluebutton' => 'y',
		'wikiplugin_bliptv' => 'y',
		'wikiplugin_bloglist' => 'n',
		'wikiplugin_box' => 'y',
		'wikiplugin_button' => 'n',
		'wikiplugin_calendar' => 'y',
		'wikiplugin_category' => 'y',
		'wikiplugin_catorphans' => 'y',
		'wikiplugin_cclite' => 'n',
		'wikiplugin_catpath' => 'y',
		'wikiplugin_center' => 'y',
		'wikiplugin_chart' => 'y',
		'wikiplugin_code' => 'y',
		'wikiplugin_colorbox' => 'n',
		'wikiplugin_content' => 'y',
		'wikiplugin_cookie' => 'n',
		'wikiplugin_copyright' => 'y',
		'wikiplugin_countdown' => 'n',
		'wikiplugin_datachannel' => 'n',
		'wikiplugin_dbreport' => 'n',
		'wikiplugin_div' => 'y',
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
		'wikiplugin_freetagged' => 'n',
		'wikiplugin_ftp' => 'n',
		'wikiplugin_gauge' => 'n',
		'wikiplugin_getaccesstoken' => 'n',
		'wikiplugin_googleanalytics' => 'n',
		'wikiplugin_googledoc' => 'n',
		'wikiplugin_googlemap' => 'y',
		'wikiplugin_group' => 'y',
		'wikiplugin_groupexpiry' => 'n',
		'wikiplugin_grouplist' => 'n',
		'wikiplugin_groupmailcore' => 'n',
		'wikiplugin_groupstat' => 'n',
		'wikiplugin_html' => 'y',
		'wikiplugin_iframe' => 'n',
		'wikiplugin_img' => 'y',
		'wikiplugin_image' => 'n',    // Experimental, intended to be phased out with new img
		'wikiplugin_include' => 'y',
		'wikiplugin_randominclude' => 'n',
		'wikiplugin_invite' => 'y',
		'wikiplugin_jabber' => 'n',
		'wikiplugin_js' => 'n',
		'wikiplugin_jq' => 'n',
		'wikiplugin_lang' => 'y',
		'wikiplugin_lastmod' => 'n',
		'wikiplugin_list' => 'n',
		'wikiplugin_listpages' => 'n',
		'wikiplugin_lsdir' => 'n',
		'wikiplugin_mail' => 'n',
		'wikiplugin_map' => 'y',
		'wikiplugin_mcalendar' => 'n',
		'wikiplugin_mediaplayer' => 'y',
		'wikiplugin_memberlist' => 'n',
		'wikiplugin_memberpayment' => 'y',
		'wikiplugin_miniquiz' => 'y',
		'wikiplugin_module' => 'y',
		'wikiplugin_mono' => 'n',
		'wikiplugin_mouseover' => 'y',
		'wikiplugin_mwtable' => 'n',
		'wikiplugin_myspace' => 'n',
		'wikiplugin_objecthits' => 'n',
		'wikiplugin_param' => 'n',
		'wikiplugin_payment' => 'y',
		'wikiplugin_perm' => 'n',
		'wikiplugin_picture' => 'n',  // Old syntax for images
		'wikiplugin_pluginmanager' => 'n',
		'wikiplugin_poll' => 'y',
		'wikiplugin_profile' => 'n',		
		'wikiplugin_proposal' => 'n',
		'wikiplugin_quote' => 'y',
		'wikiplugin_rcontent' => 'y',
		'wikiplugin_realnamelist' => 'n',
		'wikiplugin_redirect' => 'n',
		'wikiplugin_regex' => 'n',
		'wikiplugin_remarksbox' => 'y',
		'wikiplugin_rss' => 'y',
		'wikiplugin_scroll' => 'n',
		'wikiplugin_sf' => 'n',
		'wikiplugin_share' => 'n',
		'wikiplugin_sharethis' => 'n',
		'wikiplugin_sheet' => 'y',
		'wikiplugin_slideshow' => 'n',
		'wikiplugin_showpages' => 'n',
		'wikiplugin_skype' => 'n',
		'wikiplugin_smarty' => 'n',
		'wikiplugin_snarf' => 'n',
		'wikiplugin_snarf_cache' => 0,
		'wikiplugin_sort' => 'y',
		'wikiplugin_split' => 'y',
		'wikiplugin_sql' => 'n',
		'wikiplugin_stat' => 'n',
		'wikiplugin_sub' => 'y',
		'wikiplugin_subscribegroup' => 'n',
		'wikiplugin_subscribegroups' => 'n',
		'wikiplugin_subscribenewsletter' => 'n',
		'wikiplugin_sup' => 'y',
		'wikiplugin_survey' => 'y',
		'wikiplugin_tag' => 'n',
		'wikiplugin_tabs' => 'y',
		'wikiplugin_thumb' => 'y',
		'wikiplugin_titlesearch' => 'n',
		'wikiplugin_toc' => 'y',
		'wikiplugin_topfriends' => 'y',
		'wikiplugin_trackercomments' => 'y',
		'wikiplugin_trackerfilter' => 'y',
		'wikiplugin_trackeritemfield' => 'y',
		'wikiplugin_trackerlist' => 'y',
		'wikiplugin_trackertimeline' => 'y',
		'wikiplugin_tracker' => 'y',
		'wikiplugin_trackerprefill' => 'y',
		'wikiplugin_trackerstat' => 'y',
		'wikiplugin_trackerif' => 'y',
		'wikiplugin_trade' => 'n',
		'wikiplugin_transclude' => 'y',
		'wikiplugin_translated' => 'y',
		'wikiplugin_tr' => 'n',
		'wikiplugin_twitter' => 'y',
		'wikiplugin_usercount' => 'n',
		'wikiplugin_userlink' => 'y',
		'wikiplugin_userlist' => 'n',
		'wikiplugin_userpref' => 'n',
		'wikiplugin_versions' => 'n',
		'wikiplugin_vimeo' => 'y',	
		'wikiplugin_vote' => 'y',
		'wikiplugin_watershed' => 'n',
		'wikiplugin_wantedpages' => 'n',
		'wikiplugin_webservice' => 'n',
		'wikiplugin_youtube' => 'y',
		'wikiplugin_zotero' => 'y',

		// Inline wiki plugins have their edit plugin icon disabled
		'wikiplugininline_addtocart' => 'n',
		'wikiplugininline_addtogooglecal' => 'n',
		'wikiplugininline_agentinfo' => 'n',
		'wikiplugininline_alink' => 'n',
		'wikiplugininline_aname' => 'n',
		'wikiplugininline_annotation' => 'n',
		'wikiplugininline_archivebuilder' => 'n',
		'wikiplugininline_article' => 'n',
		'wikiplugininline_articles' => 'n',
		'wikiplugininline_attach' => 'n',
		'wikiplugininline_attributes' => 'n',
		'wikiplugininline_avatar' => 'n',
		'wikiplugininline_back' => 'n',
		'wikiplugininline_backlinks' => 'n',
		'wikiplugininline_banner' => 'n',
		'wikiplugininline_bigbluebutton' => 'n',
		'wikiplugininline_bliptv' => 'n',
		'wikiplugininline_bloglist' => 'n',
		'wikiplugininline_box' => 'n',
		'wikiplugininline_button' => 'n',
		'wikiplugininline_calendar' => 'n',
		'wikiplugininline_category' => 'n',
		'wikiplugininline_catorphans' => 'n',
		'wikiplugininline_catpath' => 'n',
		'wikiplugininline_cclite' => 'n',
		'wikiplugininline_center' => 'n',
		'wikiplugininline_chart' => 'n',
		'wikiplugininline_code' => 'n',
		'wikiplugininline_colorbox' => 'n',
		'wikiplugininline_content' => 'n',
		'wikiplugininline_cookie' => 'n',
		'wikiplugininline_copyright' => 'n',
		'wikiplugininline_countdown' => 'n',
		'wikiplugininline_datachannel' => 'n',
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
		'wikiplugininline_freetagged' => 'n',
		'wikiplugininline_ftp' => 'n',
		'wikiplugininline_gauge' => 'n',
		'wikiplugininline_getaccesstoken' => 'y',
		'wikiplugininline_googleanalytics' => 'y',
		'wikiplugininline_googledoc' => 'n',
		'wikiplugininline_googlemap' => 'n',
		'wikiplugininline_group' => 'y',
		'wikiplugininline_groupexpiry' => 'n',
		'wikiplugininline_grouplist' => 'y',
		'wikiplugininline_groupmailcore' => 'n',
		'wikiplugininline_groupstat' => 'n',
		'wikiplugininline_html' => 'n',
		'wikiplugininline_iframe' => 'n',
		'wikiplugininline_img' => 'n',
		'wikiplugininline_image' => 'n',    // Experimental, may supercede img in 4.0
		'wikiplugininline_include' => 'n',
		'wikiplugininline_invite' => 'n',
		'wikiplugininline_jabber' => 'n',
		'wikiplugininline_js' => 'n',
		'wikiplugininline_jq' => 'n',
		'wikiplugininline_lang' => 'n',
		'wikiplugininline_lastmod' => 'n',
		'wikiplugininline_list' => 'n',
		'wikiplugininline_listpages' => 'n',
		'wikiplugininline_lsdir' => 'n',
		'wikiplugininline_mail' => 'y',
		'wikiplugininline_map' => 'n',
		'wikiplugininline_mcalendar' => 'n',
		'wikiplugininline_mediaplayer' => 'n',
		'wikiplugininline_memberlist' => 'n',
		'wikiplugininline_memberpayment' => 'n',
		'wikiplugininline_miniquiz' => 'n',
		'wikiplugininline_module' => 'n',
		'wikiplugininline_mono' => 'n',
		'wikiplugininline_mouseover' => 'n',
		'wikiplugininline_mwtable' => 'n',
		'wikiplugininline_myspace' => 'n',
		'wikiplugininline_objecthits' => 'n',
		'wikiplugininline_param' => 'n',
		'wikiplugininline_payment' => 'n',
		'wikiplugininline_perm' => 'y',
		'wikiplugininline_picture' => 'n',  // Old syntax for images
		'wikiplugininline_pluginmanager' => 'n',
		'wikiplugininline_poll' => 'n',
		'wikiplugininline_profile' => 'n',
		'wikiplugininline_proposal' => 'n',
		'wikiplugininline_quote' => 'n',
		'wikiplugininline_rcontent' => 'n',
		'wikiplugininline_randominclude' => 'n',
		'wikiplugininline_realnamelist' => 'n',
		'wikiplugininline_redirect' => 'n',
		'wikiplugininline_regex' => 'n',
		'wikiplugininline_remarksbox' => 'n',
		'wikiplugininline_rss' => 'n',
		'wikiplugininline_scroll' => 'n',
		'wikiplugininline_sf' => 'n',
		'wikiplugininline_share' => 'n',
		'wikiplugininline_sharethis' => 'n',
		'wikiplugininline_sheet' => 'n',
		'wikiplugininline_showpages' => 'n',
		'wikiplugininline_skype' => 'n',
		'wikiplugininline_smarty' => 'y',
		'wikiplugininline_snarf' => 'n',
		'wikiplugininline_sort' => 'n',
		'wikiplugininline_split' => 'n',
		'wikiplugininline_sql' => 'n',
		'wikiplugininline_stat' => 'n',
		'wikiplugininline_sub' => 'n',
		'wikiplugininline_subscribegroup' => 'n',
		'wikiplugininline_subscribegroups' => 'n',
		'wikiplugininline_subscribenewsletter' => 'n',
		'wikiplugininline_sup' => 'n',
		'wikiplugininline_survey' => 'n',
		'wikiplugininline_tag' => 'n',
		'wikiplugininline_tabs' => 'n',
		'wikiplugininline_thumb' => 'n',
		'wikiplugininline_titlesearch' => 'n',
		'wikiplugininline_toc' => 'n',
		'wikiplugininline_topfriends' => 'n',
		'wikiplugininline_trackercomments' => 'n',
		'wikiplugininline_trackerfilter' => 'n',
		'wikiplugininline_trackeritemfield' => 'y',
		'wikiplugininline_trackerlist' => 'n',
		'wikiplugininline_trackertimeline' => 'n',
		'wikiplugininline_tracker' => 'n',
		'wikiplugininline_trackerprefill' => 'n',
		'wikiplugininline_trackerstat' => 'n',
		'wikiplugininline_trade' => 'n',
		'wikiplugininline_transclude' => 'y',
		'wikiplugininline_translated' => 'n',
		'wikiplugininline_tr' => 'n',
		'wikiplugininline_twitter' => 'n',
		'wikiplugininline_usercount' => 'n',
		'wikiplugininline_userlink' => 'n',
		'wikiplugininline_userlist' => 'n',
		'wikiplugininline_userpref' => 'n',
		'wikiplugininline_versions' => 'n',
		'wikiplugininline_vimeo' => 'n',
		'wikiplugininline_vote' => 'n',
		'wikiplugininline_watershed' => 'n',
		'wikiplugininline_wantedpages' => 'n',
		'wikiplugininline_webservice' => 'n',
		'wikiplugininline_youtube' => 'n',
		'wikiplugininline_author' => 'n',
		'wikiplugininline_zotero' => 'y',

		// webservices
		'webservice_consume_defaultcache' => 300, // 5 min

		// filegals
		'fgal_root_id' => 1,
		'fgal_root_user_id' => 2,
		'fgal_root_wiki_attachments_id' => 3,
		'fgal_enable_auto_indexing' => 'y',
		'fgal_asynchronous_indexing' => 'y',
		'fgal_sort_mode' => '',
		'fgal_list_id' => 'o',
		'fgal_list_type' => 'y',
		'fgal_list_name' => 'a',
		'fgal_list_description' => 'o',
		'fgal_list_size' => 'y',
		'fgal_list_created' => 'o',
		'fgal_list_lastModif' => 'y',
		'fgal_list_creator' => 'o',
		'fgal_list_author' => 'o',
		'fgal_list_last_user' => 'o',
		'fgal_list_comment' => 'o',
		'fgal_list_files' => 'o',
		'fgal_list_hits' => 'o',
		'fgal_list_lastDownload' => 'n',
		'fgal_list_lockedby' => 'a',
		'fgal_list_deleteAfter' => 'n',
		'fgal_list_share' => 'n',
		'fgal_show_path' => 'y',
		'fgal_show_explorer' => 'y',
		'fgal_show_slideshow' => 'n',
		'fgal_default_view' => 'list',
		'fgal_list_backlinks' => 'n',
		'fgal_list_id_admin' => 'y',
		'fgal_list_type_admin' => 'y',
		'fgal_list_name_admin' => 'n',
		'fgal_list_description_admin' => 'o',
		'fgal_list_size_admin' => 'y',
		'fgal_list_created_admin' => 'o',
		'fgal_list_lastModif_admin' => 'y',
		'fgal_list_creator_admin' => 'o',
		'fgal_list_author_admin' => 'o',
		'fgal_list_last_user_admin' => 'o',
		'fgal_list_comment_admin' => 'o',
		'fgal_list_files_admin' => 'o',
		'fgal_list_hits_admin' => 'o',
		'fgal_list_lastDownload_admin' => 'n',
		'fgal_list_lockedby_admin' => 'n',
		'fgal_list_backlinks_admin' => 'y',
		'fgal_list_ratio_hits' => 'n',
		'fgal_show_checked' => 'y',

		// imagegals
		'feature_gal_batch' => 'n',
		'feature_gal_slideshow' => 'n',
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

		// articles
		'cms_bot_bar' => 'y',
		'cms_left_column' => 'y',
		'cms_right_column' => 'y',
		'cms_top_bar' => 'n',


		// trackers
		't_use_db' => 'y',
		't_use_dir' => '',
		'trackerCreatorGroupName' => ' ',

		// user
		'userlevels' => function_exists('tra') ? array('1'=>tra('Simple'),'2'=>tra('Advanced')) : array('1'=>'Simple','2'=>'Advanced'),
		'userbreadCrumb' => 4,
		'w_use_db' => 'y',
		'w_use_dir' => '',
		'w_displayed_default' => 'n',
		'uf_use_db' => 'y',
		'uf_use_dir' => '',
		'userfiles_quota' => 30,
		'feature_community_friends_permission' => 'n',
		'feature_community_friends_permission_dep' => '2',
		'lowercase_username' => 'n',
		'max_username_length' => '50',
		'min_username_length' => '1',
		'users_prefs_country' => '',
		'users_prefs_email_is_public' => 'n',
		'users_prefs_homePage' => '',
		'users_prefs_lat' => '0',
		'users_prefs_lon' => '0',
		'users_prefs_mytiki_articles' => 'y',
		'users_prefs_realName' => '',
		'users_prefs_gender' => '',
		'users_prefs_mailCurrentAccount' => '0',

		// freetags
		'freetags_cloud_colors' => '',


		// calendar
		'feature_default_calendars' => 'n',
		'default_calendars' => array(),

		// feed
		'max_rss_mapfiles' => 10,
		'rss_mapfiles' => 'n',
		'title_rss_mapfiles' => '',

	
		// auth
		'min_user_length' => 1,
		'auth_pear' => 'tiki',
		'auth_ldap_url' => '',
		'auth_pear_host' => "localhost",
		'auth_pear_port' => "389",
		'auth_ldap_groupnameatr' => '',
		'auth_ldap_groupdescatr' => '',
		'auth_ldap_syncuserattr' => 'uid',
		'auth_ldap_syncgroupattr' => 'cn',

		
		'auth_phpbb_dbport' => '',
		'auth_phpbb_dbtype' => 'mysql',


		'login_url' => 'tiki-login.php',
		'login_scr' => 'tiki-login_scr.php',
		'register_url' => 'tiki-register.php',
		'error_url' => 'tiki-error.php',

		// intertiki
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

		// categories
		'category_i18n_unsynced' => array(),
		'expanded_category_jail' => '',
		'expanded_category_jail_key' => '',
		'ws_container' => 0,

		// i18n
		'feature_homePage_if_bl_missing' => 'n',

		// html header
		'head_extra_js' => array(),

		// look and feel

		'feature_sitenav' => 'n',
		'sitenav' => '{tr}Navigation : {/tr}<a href="tiki-contact.php" accesskey="10" title="">{tr}Contact Us{/tr}</a>',

		// layout
		'feature_theme_generator' => 'n',

		// mods
		'feature_mods_provider' => 'n',
		'mods_dir' => 'mods',
		'mods_server' => 'http://mods.tiki.org',


		// toolbars
		// comma delimited items, / delimited rows and | denotes items right justified in toolbar (in reverse order)
		// full list in lib/toolbars/toolbarslib.php Toolbar::getList()
		// cannot contain spaces, commas, forward-slash or pipe chars
		'toolbar_global' => '
			bold,italic,underline,strike, sub, sup,-,color,-,wikiplugin_img,tikiimage,wikiplugin_file,tikilink,link, unlink, anchor,-,
			undo, redo,-,find,replace,-, removeformat,specialchar,smiley|help,switcheditor,autosave/
			templates, cut, copy, paste, pastetext, pasteword,-,h1,h2,h3, left,center,-,
			blockquote,list,numlist,wikiplugin_mouseover,wikiplugin_module,wikiplugin_html, outdent, indent,-,
			pagebreak,rule,-,table,-,wikiplugin_code, source, showblocks,nonparsed|fullscreen/
			format,style,-,fontname,fontsize/
		',
		'toolbar_global_comments' => '
			bold, italic, underline, strike , - , link, smiley | help
		',
		'toolbar_sheet' => 'addrow,addrowbefore,addrowmulti,deleterow,-,addcolumn,addcolumnbefore,addcolumnmulti,deletecolumn,-,
							sheetgetrange,sheetrefresh,-,sheetfind|sheetclose,sheetsave,help/
							bold,italic,underline,strike,center,-,color,bgcolor,-,tikilink,nonparsed|fullscreen/',

		// kaltura
		'wikiplugin_kaltura' => 'y',
		'wikiplugininline_kaltura' => 'n',


		// unsorted features
		'anonCanEdit' => 'n',
		'feature_contribution_display_in_comment' => 'y',
		'feature_contribution_mandatory' => 'n',
		'feature_contribution_mandatory_blog' => 'n',
		'feature_contribution_mandatory_comment' => 'n',
		'feature_contribution_mandatory_forum' => 'n',
		'feature_debugger_console' => 'n',
		'feature_events' => 'n',
		'feature_projects' => 'n',
		'feature_ranking' => 'n',
		'feature_top_banner' => 'n',
		'feature_usability' => 'n',
		'minical_reminders' => 0,
		'php_docroot' => 'http://php.net/',
		'ip_can_be_checked' => 'n',
		'shoutbox_autolink' => 'n',
		'show_comzone' => 'n',
		'use_proxy' => 'n',
		'webserverauth' => 'n',
	
		'case_patched' => 'n',

		'feature_intertiki_imported_groups' => '',
		'feature_contributor_wiki' => '',
		'https_login_required' => '',
		'maxRowsGalleries' => '',
		'replimaster' => '',
		'rowImagesGalleries' => '',
		'scaleSizeGalleries' => '',
		'thumbSizeXGalleries' => '',
		'thumbSizeYGalleries' => '',
		'javascript_enabled' => 'n',


		// SefUrl
		'feature_sefurl_paths' => array(''), //empty string needed to keep preference from setting unexpectedly

		// Pear::Date
		'feature_pear_date' => 'y',

		'feature_bidi' => 'n',
		'feature_lastup' => 'y',

		'terminology_profile_installed' => 'n',
	));

	// Special default values

	global $tikidomain;
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

	$cachelib->cacheItem("tiki_default_preferences_cache",serialize($prefs));
	return $prefs;
}


function initialize_prefs() {
	// Initialize prefs for which we want to use the site value (they will be prefixed with 'site_')
	// ( this is also used in tikilib, not only when reloading prefs )
	
	global $prefs, $tikiroot, $tikilib, $user_overrider_prefs;
		
	// Check if prefs needs to be reloaded
	if (isset($_SESSION['s_prefs'])) {

		// lastUpdatePrefs pref is retrived in tiki-setup_base
		$lastUpdatePrefs = isset($prefs['lastUpdatePrefs']) ? $prefs['lastUpdatePrefs'] : 1;

		// Reload if there was an update of some prefs
		if ( empty($_SESSION['s_prefs']['lastReadingPrefs']) || $lastUpdatePrefs > $_SESSION['s_prefs']['lastReadingPrefs'] ) {
			$_SESSION['need_reload_prefs'] = true;
		} else {
			$_SESSION['need_reload_prefs'] = false;
		}

		// Reload if the virtual host or tikiroot has changed
		if (!isset($_SESSION['lastPrefsSite'])) $_SESSION['lastPrefsSite'] = '';
		//   (this is needed when using the same php sessions for more than one tiki)
		if ( $_SESSION['lastPrefsSite'] != $_SERVER['SERVER_NAME'].'|'.$tikiroot ) {
			$_SESSION['lastPrefsSite'] = $_SERVER['SERVER_NAME'].'|'.$tikiroot;
			$_SESSION['need_reload_prefs'] = true;
		}

	} else {
		$_SESSION['need_reload_prefs'] = true;
	}

	$defaults = get_default_prefs();
	// Set default prefs only if needed
	if ( ! $_SESSION['need_reload_prefs'] ) {
		$modified = $_SESSION['s_prefs'];
	} else {

		// Find which preferences need to be serialized/unserialized, based on the default values (those with arrays as values)
		if ( ! isset($_SESSION['serialized_prefs']) ) {
			$_SESSION['serialized_prefs'] = array();
			foreach ( $defaults as $p => $v )
			if ( is_array($v) ) $_SESSION['serialized_prefs'][] = $p;
		}

		// Override default prefs with values specified in database
		$modified = isset($tikilib) ? $tikilib->get_db_preferences() : "";

		// Unserialize serialized preferences
		if ( isset($_SESSION['serialized_prefs']) && is_array($_SESSION['serialized_prefs']) ) {
			foreach ( $_SESSION['serialized_prefs'] as $p ) {
				if ( isset($modified[$p]) && ! is_array($modified[$p]) ) $modified[$p] = unserialize($modified[$p]);
			}
		}

		// Keep some useful sites values available before overriding with user prefs
		// (they could be used in templates, so we need to set them even for Anonymous)
		foreach ( $user_overrider_prefs as $uop ) {
			$modified['site_'.$uop] = isset($modified[$uop])?$modified[$uop]:$defaults[$uop];
		}

		// Assign prefs to the session
		$_SESSION['s_prefs'] = $modified;
	}

	// Disabled by default so it has to be modified
	global $in_installer, $section;	// but not if called during installer
	if( isset($modified['feature_perspective']) && $modified['feature_perspective'] == 'y' && empty($in_installer) ) {
		if( ! isset( $section ) || $section != 'admin' ) {
			require_once 'lib/perspectivelib.php';
			if( $persp = $perspectivelib->get_current_perspective( $modified ) ) {
				$changes = $perspectivelib->get_preferences( $persp );
				$modified = array_merge( $modified, $changes );
			}
		}
	}

	$prefs = empty($modified) ? $defaults : array_merge( $defaults, $modified );

}

// PHP fonctionnalities
$prefs['php_libxml'] = class_exists('DOMDocument') ? 'y' :'n';
$prefs['php_datetime'] = class_exists('DateTime') ? 'y' :'n';
