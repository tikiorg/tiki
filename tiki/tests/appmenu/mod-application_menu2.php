<?php
// tiki-setup has already set the $language variable
//Create a list of languages
//$appmenu = array();

$appmenu=array();
$appmenu[]=array("link"=>"$tikiIndex","text"=>tra("Home"));

if (($feature_chat == 'y') and ($tiki_p_chat == 'y')) { 
	$appmenu[]=array("link"=>"tiki-chat.php","text"=>tra("Chat")); }
if ($feature_contact == 'y') { 
	$appmenu[]=array("link"=>"tiki-contact.php","text"=>tra("Contact us")); }
if (($feature_stats == 'y') and ($tiki_p_view_stats == 'y')) { 
	$appmenu[]=array("link"=>"tiki-stats.php","text"=>tra("Stats")); }
if (($feature_categories == 'y') and ($tiki_p_view_categories == 'y')) { 
	$appmenu[]=array("link"=>"tiki-browse_categories.php","text"=>tra("Categories")); }
if (($feature_games ==  'y') and ($tiki_p_play_games == 'y')) { 
	$appmenu[]=array("link"=>"tiki-list_games.php","text"=>tra("Games")); }
if (($feature_calendar == 'y') and ($tiki_p_view_calendar == 'y'))  { 
	$appmenu[]=array("link"=>"tiki-calendar.php","text"=>tra("Calendar")); }
if (($tiki_p_admin == 'y') and ($feature_debug_console == 'y') and ($feature_top_bar != 'y')) { 
	$appmenu=array("link"=>"javascript:toggle('debugconsole');","text"=>tra("Debugger console")); } 

# MyTiki
if ($user) {
	$appmenu[]=array("link"=>"tiki-my_tiki.php","text"=>tra("MyTiki (click!)"),"menu"=>"mymenu2");
	if ($feature_userPreferences == 'y') {
		$appmenu[]=array("link"=>"tiki-user_preferences.php","text"=>tra("Preferences"),"menu"=>"mymenu2"); }
	if (($feature_messages == 'y') and ($tiki_p_messages == 'y')) {
		$appmenu[]=array("link"=>"messu-mailbox.php","text"=>tra("Messages"),"menu"=>"mymenu2"); }
	if (($feature_tasks == 'y') and ($tiki_p_tasks == 'y')) {
		$appmenu[]=array("link"=>"tiki-user_tasks.php","text"=>tra("Tasks"),"menu"=>"mymenu2"); }
	if (($feature_user_bookmarks == 'y') and ($tiki_p_create_bookmarks == 'y')) {
		$appmenu[]=array("link"=>"tiki-user_bookmarks.php","text"=>tra("Bookmarks"),"menu"=>"mymenu2"); }
	if (($user_assigned_modules == 'y') and ($tiki_p_configure_modules == 'y')) {
		$appmenu[]=array("link"=>"tiki-user_assigned_modules.php","text"=>tra("Modules"),"menu"=>"mymenu2"); }
	if (($feature_newsreader == 'y') and ($tiki_p_newsreader == 'y')) {
		$appmenu[]=array("link"=>"tiki-newsreader_servers.php","text"=>tra("Newsreader"),"menu"=>"mymenu2"); }
	if (($feature_webmail == 'y') and ($tiki_p_use_webmail == 'y')) {
		$appmenu[]=array("link"=>"tiki-webmail.php","text"=>tra("Webmail"),"menu"=>"mymenu2"); }
	if (($feature_notepad == 'y') and ($tiki_p_notepad == 'y')) {
		$appmenu[]=array("link"=>"tiki-notepad_list.php","text"=>tra("Notepad"),"menu"=>"mymenu2"); }
	if (($feature_userfiles == 'y') and ($tiki_p_userfiles == 'y')) {
		$appmenu[]=array("link"=>"tiki-userfiles.php","text"=>tra("My files"),"menu"=>"mymenu2"); }
	if ($feature_usermenu == 'y') {
		$appmenu[]=array("link"=>"tiki-usermenu.php","text"=>tra("User menu"),"menu"=>"mymenu2"); }
	if ($feature_minical == 'y') {
		$appmenu[]=array("link"=>"tiki-minical.php","text"=>tra("Mini calendar"),"menu"=>"mymenu2"); }
	if ($feature_user_watches == 'y') {
		$appmenu[]=array("link"=>"tiki-user_watches.php","text"=>tra("My watches"),"menu"=>"mymenu2"); }
 }

# Workflow
 if (($feature_workflow == 'y') and ($tiki_p_use_workflow == 'y')) {
	$appmenu[]=array("link"=>"tiki-g-user_processes.php","text"=>tra("Workflow"),"menu"=>"wfmenu2"); 
	if ($tiki_p_admin_workflow == 'y') {
		$appmenu[]=array("link"=>"tiki-g-admin_processes.php","text"=>tra("Admin processes"),"menu"=>"wfmenu2"); 
		$appmenu[]=array("link"=>"tiki-g-monitor_processes.php","text"=>tra("Monitor processes"),"menu"=>"wfmenu2"); 
		$appmenu[]=array("link"=>"tiki-g-monitor_activities.php","text"=>tra("Monitor activities"),"menu"=>"wfmenu2"); 
		$appmenu[]=array("link"=>"tiki-g-monitor_instances.php","text"=>tra("Monitor instances"),"menu"=>"wfmenu2"); 
	}
	$appmenu[]=array("link"=>"tiki-g-user_processes.php","text"=>tra("User processes"),"menu"=>"wfmenu2"); 
	$appmenu[]=array("link"=>"tiki-g-user_activities.php","text"=>tra("User activities"),"menu"=>"wfmenu2"); 
	$appmenu[]=array("link"=>"tiki-g-user_instances.php","text"=>tra("User instances"),"menu"=>"wfmenu2"); 
 }

# Wiki
 if ($feature_wiki == 'y') {
	$appmenu[]=array("link"=>"tiki-index.php","text"=>tra("Wiki"),"menu"=>"wikimenu2");
	if ($tiki_p_view == 'y') {
		if ($feature_lastChanges == 'y') {
			$appmenu[]=array("link"=>"tiki-lastchanges.php","text"=>tra("Last changes"),"menu"=>"wikimenu2");
		}
		if ($feature_dump == 'y') {
			$appmenu[]=array("link"=>"dump/". $tikidomain ."new.tar","text"=>tra("Dump"),"menu"=>"wikimenu2");
		}
		if ($feature_wiki_rankings == 'y') {
			$appmenu[]=array("link"=>"tiki-wiki_rankings.php","text"=>tra("Rankings"),"menu"=>"wikimenu2");
		}
		if ($feature_listPages == 'y') {
			$appmenu[]=array("link"=>"tiki-listpages.php","text"=>tra("List pages"),"menu"=>"wikimenu2");
			$appmenu[]=array("link"=>"tiki-orphan_pages.php","text"=>tra("Orphan pages"),"menu"=>"wikimenu2");
		}
		if ($feature_sandbox == 'y') {
			$appmenu[]=array("link"=>"tiki-editpage.php?page=SandBox","text"=>tra("Sandbox"),"menu"=>"wikimenu2");
		}
		if ($feature_wiki_multiprint == 'y') {
			$appmenu[]=array("link"=>"tiki-print_pages.php","text"=>tra("Print"),"menu"=>"wikimenu2");
		}
	}
	if ($feature_comm == 'y') {
		if ($tiki_p_send_pages == 'y')  {
			$appmenu[]=array("link"=>"tiki-send_objects.php","text"=>tra("Send pages"),"menu"=>"wikimenu2");
		}
		if ($tiki_p_admin_received_pages == 'y') {
			$appmenu[]=array("link"=>"tiki-received_pages.php","text"=>tra("Received pages"),"menu"=>"wikimenu2");
		}
	}
	if ($tiki_p_edit_structures == 'y') {
		$appmenu[]=array("link"=>"tiki-admin_structures.php","text"=>tra("Structures"),"menu"=>"wikimenu2");
	}
 }

# Image galleries
 if ($feature_galleries == 'y') {
	$appmenu[]=array("link"=>"tiki-galleries.php","text"=>tra("Image Galleries"),"menu"=>"galmenu2");
	if ($tiki_p_view_image_gallery == 'y') {
		$appmenu[]=array("link"=>"tiki-galleries.php","text"=>tra("Galleries"),"menu"=>"galmenu2");	
		if ($feature_gal_rankings == 'y') {
			$appmenu[]=array("link"=>"tiki-galleries_rankings.php","text"=>tra("Rankings"),"menu"=>"galmenu2");
		}
	}
	if ($tiki_p_upload_images == 'y') {
		$appmenu[]=array("link"=>"tiki-upload_image.php","text"=>tra("Upload image"),"menu"=>"galmenu2");
	}
	if ($tiki_p_admin_galleries == 'y') {
		$appmenu[]=array("link"=>"tiki-list_gallery.php?galleryId=0","text"=>tra("System gallery"),"menu"=>"galmenu2");
	}
 }

# Articles
 if (($feature_articles == 'y') or ($feature_submissions == 'y')) {
	$appmenu[]=array("link"=>"tiki-view_articles.php","text"=>tra("Articles"),"menu"=>"cmsmenu2");	
	if ($tiki_p_read_article == 'y') {
		$appmenu[]=array("link"=>"tiki-view_articles.php","text"=>tra("Articles home"),"menu"=>"cmsmenu2");
		$appmenu[]=array("link"=>"tiki-list_articles.php","text"=>tra("List articles"),"menu"=>"cmsmenu2");
		if ($feature_cms_rankings == 'y') {
			$appmenu[]=array("link"=>"tiki-cms_rankings.php","text"=>tra("Rankings"),"menu"=>"cmsmenu2");
		}
	}
	if ($feature_submissions == 'y') {
		if ($tiki_p_submit_article == 'y') {
			$appmenu[]=array("link"=>"tiki-edit_submission.php","text"=>tra("Submit article"),"menu"=>"cmsmenu2");
		}
		if (($tiki_p_submit_article == 'y') or ($tiki_p_approve_submission == 'y') or ($tiki_p_remove_submission == 'y')) {
			$appmenu[]=array("link"=>"tiki-list_submissions.php","text"=>tra("View submissions"),"menu"=>"cmsmenu2");
		}
	}
	if ($tiki_p_edit_article == 'y') {
		$appmenu[]=array("link"=>"tiki-edit_article.php","text"=>tra("Edit article"),"menu"=>"cmsmenu2");
	}
	if (($tiki_p_send_articles == 'y') and ($feature_comm == 'y')) {
		$appmenu[]=array("link"=>"tiki-send_objects.php","text"=>tra("Send articles"),"menu"=>"cmsmenu2");
	}
	if ($tiki_p_admin_cms == 'y') {
		$appmenu[]=array("link"=>"tiki-admin_topics.php","text"=>tra("Admin topics"),"menu"=>"cmsmenu2");
		$appmenu[]=array("link"=>"tiki-article_types.php","text"=>tra("Admin types"),"menu"=>"cmsmenu2");
	}
 }

# Blogs
 if ($feature_blogs == 'y') {
	$appmenu[]=array("link"=>"tiki-list_blogs.php","text"=>tra("Blogs"),"menu"=>"blogmenu2");
	if ($tiki_p_read_blog == 'y') {
		$appmenu[]=array("link"=>"tiki-list_blogs.php","text"=>tra("List blogs"),"menu"=>"blogmenu2");
		if ($feature_blog_rankings == 'y') {
			$appmenu[]=array("link"=>"tiki-blog_rankings.php","text"=>tra("Rankings"),"menu"=>"blogmenu2");
		}
	}
	if ($tiki_p_create_blogs == 'y') {
		$appmenu[]=array("link"=>"tiki-edit_blog.php","text"=>tra("Create/Edit blog"),"menu"=>"blogmenu2");
	}
	if ($tiki_p_blog_post == 'y') {
		$appmenu[]=array("link"=>"tiki-blog_post.php","text"=>tra("Post"),"menu"=>"blogmenu2");
	}
	if ($tiki_p_blog_admin == 'y') {
		$appmenu[]=array("link"=>"tiki-list_posts.php","text"=>tra("Admin posts"),"menu"=>"blogmenu2");
	}
 }
# Forums
 if ($feature_forums == 'y') {
	$appmenu[]=array("link"=>"tiki-forums.php","text"=>tra("Forums"),"menu"=>"formenu2");
	if ($tiki_p_forum_read == 'y') {
		$appmenu[]=array("link"=>"tiki-forums.php","text"=>tra("List forums"),"menu"=>"formenu2");
	}
	if (($feature_forum_rankings == 'y') and ($tiki_p_forum_read == 'y')) {
		$appmenu[]=array("link"=>"tiki-forum_rankings.php","text"=>tra("Rankings"),"menu"=>"formenu2");
	}
	if ($tiki_p_admin_forum == 'y') {
		$appmenu[]=array("link"=>"tiki-admin_forums.php","text"=>tra("Admin forums"),"menu"=>"formenu2");
	}
 }

# Directories
 if ($feature_directory == 'y') {
	$appmenu[]=array("link"=>"tiki-directory_browse.php","text"=>tra("Directory"),"menu"=>"dirmenu2");
        if ($tiki_p_submit_link == 'y') {
		$appmenu[]=array("link"=>"tiki-directory_add_site.php","text"=>tra("Submit a new link"),"menu"=>"dirmenu2");
	}
	if ($tiki_p_view_directory == 'y') {
		$appmenu[]=array("link"=>"tiki-directory_browse.php","text"=>tra("Browse directory"),"menu"=>"dirmenu2");
	}
	if ($tiki_p_admin_directory_cats == 'y' or $tiki_p_admin_directory_sites == 'y' or $tiki_p_validate_links == 'y') {
		$appmenu[]=array("link"=>"tiki-directory_admin.php","text"=>tra("Admin directory"),"menu"=>"dirmenu2");
	}
 }

# File Galleries
 if ($feature_file_galleries == 'y') {
	$appmenu[]=array("link"=>"tiki-file_galleries.php","text"=>tra("File Galleries"),"menu"=>"filegalmenu2");
	if ($tiki_p_view_file_gallery == 'y') {
		$appmenu[]=array("link"=>"tiki-file_galleries.php","text"=>tra("List galleries"),"menu"=>"filegalmenu2");
	}
	if ($feature_file_galleries_rankings == 'y' and $tiki_p_view_file_gallery == 'y') {
		$appmenu[]=array("link"=>"tiki-file_galleries_rankings.php","text"=>tra("Rankings"),"menu"=>"filegalmenu2");
	}
	if ($tiki_p_upload_files == 'y') {
		$appmenu[]=array("link"=>"tiki-upload_file.php","text"=>tra("Upload file"),"menu"=>"filegalmenu2");
	}
 }

# FAQs
 if ($feature_faqs == 'y') {
	$appmenu[]=array("link"=>"tiki-list_faqs.php","text"=>tra("FAQs"),"menu"=>"faqsmenu2");
	if ($tiki_p_view_faqs == 'y') {
		$appmenu[]=array("link"=>"tiki-list_faqs.php","text"=>tra("List FAQs"),"menu"=>"faqsmenu2");
	}
	if ($tiki_p_admin_faqs == 'y') {
		$appmenu[]=array("link"=>"tiki-list_faqs.php","text"=>tra("Admin FAQs"),"menu"=>"faqsmenu2");
	}
 }

# Maps
 if ($feature_maps == 'y') {
	$appmenu[]=array("link"=>"tiki-map.phtml","text"=>tra("Maps"),"menu"=>"mapsmenu2");
	if ($tiki_p_map_view == 'y') {
		$appmenu[]=array("link"=>"tiki-map_edit.php","text"=>tra("Mapfiles"),"menu"=>"mapsmenu2");
	}
	if ($tiki_p_map_edit == 'y') {
		$appmenu[]=array("link"=>"tiki-map_upload.php","text"=>tra("Layer management"),"menu"=>"mapsmenu2");
	}
 }

# Quizzes
 if ($feature_quizzes == 'y') {
	$appmenu[]=array("link"=>"tiki-list_quizzes.php","text"=>tra("Quizzes"),"menu"=>"quizmenu2");
	$appmenu[]=array("link"=>"tiki-list_quizzes.php","text"=>tra("List quizzes"),"menu"=>"quizmenu2");
	if ($tiki_p_view_quiz_stats == 'y') {
		$appmenu[]=array("link"=>"tiki-quiz_stats.php","text"=>tra("Quiz stats"),"menu"=>"quizmenu2");
	}
	if ($tiki_p_admin_quizzes == 'y') {
		$appmenu[]=array("link"=>"tiki-edit_quiz.php","text"=>tra("Admin quiz"),"menu"=>"quizmenu2");
	}
 }

# Trackers
 if ($feature_trackers == 'y') {
	$appmenu[]=array("link"=>"tiki-list_trackers.php","text"=>tra("Trackers"),"menu"=>"trkmenu2");
	$appmenu[]=array("link"=>"tiki-list_trackers.php","text"=>tra("List trackers"),"menu"=>"trkmenu2");
	if ($tiki_p_admin_trackers == 'y') {
		$appmenu[]=array("link"=>"tiki-admin_trackers.php","text"=>tra("Admin trackers"),"menu"=>"trkmenu2");
	}
 }

# Surveys
 if ($feature_surveys == 'y') {
	$appmenu[]=array("link"=>"tiki-list_surveys.php","text"=>tra("Surveys"),"menu"=>"srvmenu2");
	$appmenu[]=array("link"=>"tiki-list_surveys.php","text"=>tra("List surveys"),"menu"=>"srvmenu2");
	if ($tiki_p_view_survey_stats == 'y') {
		$appmenu[]=array("link"=>"tiki-survey_stats.php","text"=>tra("Stats"),"menu"=>"srvmenu2");
	}
	if ($tiki_p_admin_surveys == 'y') {
		$appmenu[]=array("link"=>"tiki-admin_surveys.php","text"=>tra("Admin surveys"),"menu"=>"srvmenu");
	}
 }

# Newsletters
 if ($feature_newsletters == 'y') {
	$appmenu[]=array("link"=>"tiki-newsletters.php","text"=>tra("Newsletters"),"menu"=>"nlmenu2");
	if ($tiki_p_admin_newsletters == 'y') {
		$appmenu[]=array("link"=>"tiki-send_newsletters.php","text"=>tra("Send newsletters"),"menu"=>"nlmenu2");
		$appmenu[]=array("link"=>"tiki-admin_newsletters.php","text"=>tra("Admin newsletters"),"menu"=>"nlmenu2");
	}
 }

# Ephemerides
 if ($feature_eph == 'y') {
	$appmenu[]=array("link"=>"tiki-eph.php","text"=>tra("Ephemerides"),"menu"=>"ephmenu");
	if ($tiki_p_eph_admin == 'y') {
		$appmenu[]=array("link"=>"tiki-eph_admin.php","text"=>tra("Admin"),"menu"=>"ephmenu");
	}
 }

# Charts
 if ($feature_charts == 'y') {
	$appmenu[]=array("link"=>"tiki-charts.php","text"=>tra("Charts"),"menu"=>"chartmenu");
	if ($tiki_p_admin_charts == 'y') {
		$appmenu[]=array("link"=>"tiki-admin_charts.php","text"=>tra("Admin"),"menu"=>"chartmenu");
	}
 }

# Admin menu
 if (($tiki_p_admin == 'y') or  
  ($tiki_p_admin_chat == 'y') or
  ($tiki_p_admin_categories == 'y') or
  ($tiki_p_admin_banners == 'y') or
  ($tiki_p_edit_templates == 'y') or
  ($tiki_p_admin_dynamic == 'y') or
  ($tiki_p_admin_dynamic == 'y') or
  ($tiki_p_admin_mailin == 'y') or
  ($tiki_p_edit_content_templates == 'y') or
  ($tiki_p_edit_html_pages == 'y') or
  ($tiki_p_view_referer_stats == 'y') or
  ($tiki_p_admin_drawings == 'y') or
  ($tiki_p_admin_shoutbox == 'y') or
  ($tiki_p_admin_live_support == 'y') or
  ($user_is_operator == 'y')) {
	$appmenu[]=array("link"=>"tiki-admin.php","text"=>tra("Admin (click!)"),"menu"=>"admmnu2");

        if ($feature_live_support == 'y' and ($tiki_p_live_support_admin == 'y' or $user_is_operator == 'y')) {
		$appmenu[]=array("link"=>"tiki-live_support_admin.php","text"=>tra("Live support"),"menu"=>"admmnu2");
	}
        if ($feature_banning == 'y' and ($tiki_p_admin_banning == 'y')) {
		$appmenu[]=array("link"=>"tiki-admin_banning.php","text"=>tra("Banning"),"menu"=>"admmnu2");
	}
        if ($feature_calendar == 'y' and ($tiki_p_admin_calendar == 'y')) {
		$appmenu[]=array("link"=>"tiki-admin_calendars.php","text"=>tra("Calendar"),"menu"=>"admmnu2");
	}
	if ($tiki_p_admin == 'y') {
		$appmenu[]=array("link"=>"tiki-adminusers.php","text"=>tra("Users"),"menu"=>"admmnu2");
		$appmenu[]=array("link"=>"tiki-admingroups.php","text"=>tra("Groups"),"menu"=>"admmnu2");
		$appmenu[]=array("link"=>"tiki-list_cache.php","text"=>tra("Cache"),"menu"=>"admmnu2");
		$appmenu[]=array("link"=>"tiki-admin_modules.php","text"=>tra("Modules"),"menu"=>"admmnu2");
		$appmenu[]=array("link"=>"tiki-admin_links.php","text"=>tra("Links"),"menu"=>"admmnu2");
		$appmenu[]=array("link"=>"tiki-admin_hotwords.php","text"=>tra("Hotwords"),"menu"=>"admmnu2");
		$appmenu[]=array("link"=>"tiki-admin_rssmodules.php","text"=>tra("RSS modules"),"menu"=>"admmnu2");
		$appmenu[]=array("link"=>"tiki-admin_menus.php","text"=>tra("Menus"),"menu"=>"admmnu2");
		$appmenu[]=array("link"=>"tiki-admin_polls.php","text"=>tra("Polls"),"menu"=>"admmnu2");
		$appmenu[]=array("link"=>"tiki-backup.php","text"=>tra("Backups"),"menu"=>"admmnu2");
		$appmenu[]=array("link"=>"tiki-admin_notifications.php","text"=>tra("Mail notifications"),"menu"=>"admmnu2");
		$appmenu[]=array("link"=>"tiki-search_stats.php","text"=>tra("Search stats"),"menu"=>"admmnu2");
		$appmenu[]=array("link"=>"tiki-theme_control.php","text"=>tra("Theme control"),"menu"=>"admmnu2");
		$appmenu[]=array("link"=>"tiki-admin_quicktags.php","text"=>tra("QuickTags"),"menu"=>"admmnu2");
	}
	if ($tiki_p_admin_chat == 'y') {
		$appmenu[]=array("link"=>"tiki-admin_chat.php","text"=>tra("Chat"),"menu"=>"admmnu2");
	}
	if ($tiki_p_admin_categories == 'y') {
		$appmenu[]=array("link"=>"tiki-admin_categories.php","text"=>tra("Categories"),"menu"=>"admmnu2");
	}
	if ($tiki_p_admin_banners == 'y') {
		$appmenu[]=array("link"=>"tiki-list_banners.php","text"=>tra("Banners"),"menu"=>"admmnu2");
	}
	if ($tiki_p_edit_templates == 'y') {
		$appmenu[]=array("link"=>"tiki-edit_templates.php","text"=>tra("Edit templates"),"menu"=>"admmnu2");
	}
	if ($tiki_p_admin_drawings == 'y') {
		$appmenu[]=array("link"=>"tiki-admin_drawings.php","text"=>tra("Drawings"),"menu"=>"admmnu2");
	}
	if ($tiki_p_admin_dynamic == 'y') {
		$appmenu[]=array("link"=>"tiki-list_contents.php","text"=>tra("Dynamic content"),"menu"=>"admmnu2");
	}
	if ($tiki_p_edit_cookies == 'y') {
		$appmenu[]=array("link"=>"tiki-admin_cookies.php","text"=>tra("Cookies"),"menu"=>"admmnu2");
	}
	if ($tiki_p_admin_mailin == 'y') {
		$appmenu[]=array("link"=>"tiki-admin_mailin.php","text"=>tra("Mail-in"),"menu"=>"admmnu2");
	}
	if ($tiki_p_edit_content_templates == 'y') {
		$appmenu[]=array("link"=>"tiki-admin_content_templates.php","text"=>tra("Content templates"),"menu"=>"admmnu2");
	}
	if ($tiki_p_edit_html_pages == 'y') {
		$appmenu[]=array("link"=>"tiki-admin_html_pages.php","text"=>tra("HTML pages"),"menu"=>"admmnu2");
	}
	if ($tiki_p_admin_shoutbox == 'y') {
		$appmenu[]=array("link"=>"tiki-shoutbox.php","text"=>tra("Shoutbox"),"menu"=>"admmnu2");
	}
	if ($tiki_p_view_referer_stats == 'y') {
		$appmenu[]=array("link"=>"tiki-referer_stats.php","text"=>tra("Referer stats"),"menu"=>"admmnu2");
	}
	if ($tiki_p_edit_languages == 'y' && $lang_use_db == 'y') {
		$appmenu[]=array("link"=>"tiki-edit_languages.php","text"=>tra("Edit languages"),"menu"=>"admmnu2");
	}
	if ($tiki_p_admin_integrator == 'y' && $feature_integrator == 'y') {
		$appmenu[]=array("link"=>"tiki-admin_integrator.php","text"=>tra("Integrator"),"menu"=>"admmnu2");
	}
	if ($tiki_p_admin == 'y') {
		$appmenu[]=array("link"=>"tiki-import_phpwiki.php","text"=>tra("Import PHPWiki Dump"),"menu"=>"admmnu2");
		$appmenu[]=array("link"=>"tiki-phpinfo.php","text"=>tra("phpinfo"),"menu"=>"admmnu2");
		$appmenu[]=array("link"=>"tiki-admin_dsn.php","text"=>tra("DSN"),"menu"=>"admmnu2");
		$appmenu[]=array("link"=>"tiki-admin_external_wikis.php","text"=>tra("External wikis"),"menu"=>"admmnu2");
	}
}
if ($feature_usermenu == 'y') {
	$appmenu[]=array("link"=>"tiki-usermenu.php","text"=>tra("User Menu"),"menu"=>"usrmenu2");
}

$smarty->assign_by_ref('appmenu', $appmenu);
?>
