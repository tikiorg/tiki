<?php

//xdebug_start_profiling();

#error_reporting(E_ALL);

class TikiSetup {
	function os() {
		static $os;
		
		if (!isset($os)) {
			if (substr(PHP_OS, 0, 3) == 'WIN') {
				$os = 'windows';
			} else {
				$os = 'unix';
			}
		}
		
		return $os;
	}

	function isWindows() {
		static $windows;
		
		if (!isset($windows)) {
			$windows = substr(PHP_OS, 0, 3) == 'WIN';
		}
		
		return $windows;
	}
	
	function tempdir() {
		static $tempdir;
		
		if (!$tempdir) {
			$tempdir = dirname(tempnam(false, ''));
		}
		
		return $tempdir;
	}
	
	function check() {
		static $checked;
		
		if ($checked) {
			return;
		}
			
		$checked = true;
		
		$errors = '';

		$docroot = dirname($_SERVER['SCRIPT_FILENAME']);

		if (ini_get('session.save_handler') == 'files') {
			$save_path = ini_get('session.save_path');

			if (!is_dir($save_path)) {
				$errors .= "The directory '$save_path' does not exist.\n";
			} else 
			if (!is_writeable($save_path)) {
				$errors .= "The directory '$save_path' is not writeable.\n";
			}

			if ($errors) {
				$save_path = TikiSetup::tempdir();
				if (is_dir($save_path) && is_writeable($save_path)) {
					ini_set('session.save_path', $save_path);
					$errors = '';
				}
			}
		}

		$wwwuser	= '';
		$wwwgroup	= '';
		
		if (TikiSetup::isWindows()) {
			$wwwuser	= 'SYSTEM';
			$wwwgroup	= 'SYSTEM';
		}

		if (function_exists('posix_getuid')) {
			$user		= @posix_getpwuid(@posix_getuid());
			$group		= @posix_getpwuid(@posix_getgid());
			$wwwuser	= $user ? $user['name'] : false;
			$wwwgroup	= $group ? $group['name'] : false;
		}

		if (!$wwwuser) {
			$wwwuser = 'nobody (or the user account the web server is running under)';
		}
		if (!$wwwgroup) {
			$wwwgroup = 'nobody (or the group account the web server is running under)';
		}
     
		static $dirs = array(
			'backups',
			'dump',
			'img/wiki',
			'img/wiki_up',
			'modules/cache',
			'temp',
			'templates_c', 
		);
		
		foreach ($dirs as $dir) {
			if (!is_dir($dir)) {
				$errors .= "The directory '$docroot/$dir' does not exist.\n";
			} else 
			if (!is_writeable($dir)) {
				$errors .= "The directory '$docroot/$dir' is not writeable by $wwwuser.\n";
			}
		}
		
		if ($errors) {
			print "
<pre>
Your tiki is not properly set up:

$errors
";
		}

		if (!TikiSetup::isWindows()) {
			print "
To set up your tiki, log in to the system running tiki,
and type the following commands:

\$ bash
\$ cd $docroot
\$ chmod +x setup.sh
\$ su -c './setup.sh $wwwuser'

or if you can't become root, but are a member of the group $wwwgroup:

\$ bash
\$ cd $docroot
\$ chmod +x setup.sh
\$ ./setup.sh mylogin $wwwgroup

Once you have executed these commands, this message will disappear!

	";
		}
		
		if ($errors) {
			exit;
		}
	}
}

TikiSetup::check();

$tmpDir = TikiSetup::tempdir();

class timer
        {
        function parseMicro($micro)
                {list($micro,$sec)=explode(' ',microtime()); return $sec+$micro;}
        function start($timer='default')
                {$this->timer[$timer]=$this->parseMicro(microtime());}
        function stop($timer='default')
                {return $this->current($timer);}
        function elapsed($timer='default')
                {return $this->parseMicro(microtime()) - $this->timer[$timer];}
        }

$tiki_timer = new timer();
$tiki_timer->start();


include_once("tiki-setup_base.php");
//print("profile: include tiki-setup_base:".$tiki_timer->elapsed()."<br/>");

// The votes array stores the votes the user has made
if(!isset($_SESSION["votes"])) {
  $votes=Array();
  //session_register("votes");
  $_SESSION["votes"]=$votes;
}
  
$appname="tiki";
if(!isset($_SESSION["appname"])) {
  //session_register("appname");
  $_SESSION["appname"]=$appname;
}
$smarty->assign("appname","tiki");

if(isset($_REQUEST["PHPSESSID"])) {
  $tikilib->update_session($_REQUEST["PHPSESSID"]);}
else if(function_exists("session_id")) {
  $tikilib->update_session(session_id());
}

if(!isset($_SESSION["last_forum_visit"])) {
  $now = date("U");
  if($user) {
    $last_forum_visit = $tikilib->get_user_preference($user,'last_forum_visit',0);
    $tikilib->set_user_preference($user,'last_forum_visit',$now);
  } else {
    $last_forum_visit = $now;
  }
  $_SESSION["last_forum_visit"]=$last_forum_visit;
}

$userbreadCrumb = 4;
$wiki_spellcheck = 'n';
$cms_spellcheck = 'n';
$blog_spellcheck = 'n';
$blog_list_order = 'created_desc';
$home_blog = 0;
$home_gallery = 0;
$home_file_gallery = 0;
$home_forum = 0;
$fgal_use_db = 'y';
$gal_use_db = 'y';
$gal_use_lib = 'gd';
$fgal_match_regex = '';
$fgal_nmatch_regex = '';
$gal_match_regex = '';
$gal_nmatch_regex = '';
$fgal_use_dir = '';
$gal_use_dir = '';
$feature_xmlrpc = 'n';
$feature_drawings = 'n';
$layout_section = 'n';
$feature_html_pages = 'n';
$feature_search_stats = 'n';
$feature_referer_stats = 'n';
$feature_smileys = 'y';
$feature_quizzes = 'n';
$feature_comm = 'n';
$feature_categories = 'n';
$feature_faqs = 'n';
$feature_shoutbox = 'n';
$feature_stats = 'n';
$feature_games = 'n';
$user_assigned_modules = 'n';
$feature_user_bookmarks = 'n';
$feature_blog_rankings = 'y';
$feature_cms_rankings = 'y';
$feature_gal_rankings = 'y';
$feature_wiki_rankings = 'y';
$feature_wiki_undo = 'n';
$feature_wiki_multiprint = 'n';
$feature_forum_rankings = 'y';
$feature_forum_parse = 'n';
$feature_forum_quickjump = 'n';
$feature_forum_topicd = 'y';
$feature_lastChanges =  'y';
$feature_dump =  'y';
$feature_ranking = 'y';
$feature_listPages = 'y';
$feature_history = 'y';
$feature_backlinks = 'y';
$feature_likePages = 'y';
$feature_search = 'y';
$feature_search_fulltext = 'y';
$feature_sandbox = 'y';
$feature_userPreferences = 'n';
$feature_userVersions = 'y';
$feature_galleries = 'y';
$feature_featuredLinks = 'y';
$feature_hotwords = 'y';
$feature_hotwords_nw = 'n';
$feature_banners = 'n';
$feature_top_banner = 'n';
$feature_wiki = 'y';
$feature_articles = 'n';
$feature_submissions = 'n';
$feature_blogs = 'n';
$feature_edit_templates = 'n';
$feature_dynamic_content = 'n';
$feature_chat = 'n';
$feature_polls = 'n';

$wiki_creator_admin = 'n';
$smarty->assign('wiki_creator_admin',$wiki_creator_admin);

$smarty->assign('wiki_list_name','y');
$smarty->assign('wiki_list_hits','y');
$smarty->assign('wiki_list_lastmodif','y');
$smarty->assign('wiki_list_creator','y');
$smarty->assign('wiki_list_user','y');
$smarty->assign('wiki_list_lastver','y');
$smarty->assign('wiki_list_comment','y');
$smarty->assign('wiki_list_status','y');
$smarty->assign('wiki_list_versions','y');
$smarty->assign('wiki_list_links','y');
$smarty->assign('wiki_list_backlinks','y');
$smarty->assign('wiki_list_size','y');

$feature_wiki_comments = 'n';
$wiki_comments_default_ordering = 'points_desc';
$wiki_comments_per_page = 10;

$feature_faq_comments = 'y';
$faq_comments_default_ordering = 'points_desc';
$faq_comments_per_page = 10;

$feature_forums = 'n';
$forums_ordering = 'created_desc';
$forums_comments_per_page = 10;


$feature_image_galleries_comments = 'n';
$image_galleries_comments_default_ordering = 'points_desc';
$image_galleries_comments_per_page = 10;

$feature_file_galleries_comments = 'n';
$file_galleries_comments_default_ordering = 'points_desc';
$file_galleries_comments_per_page = 10;


$feature_poll_comments = 'n';
$poll_comments_default_ordering = 'points_desc';
$poll_comments_per_page = 10;


$feature_blog_comments = 'n';
$blog_comments_default_ordering = 'points_desc';
$blog_comments_per_page = 10;

$feature_article_comments = 'n';
$article_comments_default_ordering = 'points_desc';
$article_comments_per_page = 10;

$feature_wiki_templates = 'n';
$feature_cms_templates = 'n';

$feature_warn_on_edit ='n';
$warn_on_edit_time =2;
$wiki_cache = 0;
$smarty->assign('wiki_cache',$wiki_cache);
$feature_file_galleries = 'n';
$feature_file_galleries_rankings = 'n';
$language = 'en';
$lang_use_db = 'n';

$feature_left_column = 'y';
$feature_right_column = 'y';
$feature_top_bar = 'y';
$feature_bot_bar = 'y';

$feature_blogposts_comments = 'n';
$smarty->assign('feature_blogposts_comments',$feature_blogposts_comments);

$feature_messages = 'n';
$smarty->assign('feature_messages',$feature_messages);
$feature_tasks = 'n';
$smarty->assign('feature_tasks',$feature_tasks);
$feature_newsreader = 'n';
$smarty->assign('feature_newsreader',$feature_newsreader);
$feature_wiki_footnotes = 'n';
$smarty->assign('feature_wiki_footnotes',$feature_wiki_footnotes);

$system_os = $tikilib->get_preference('system_os',TikiSetup::os());
$smarty->assign('system_os',$system_os);

$rememberme = $tikilib->get_preference('rememberme','disabled');
$smarty->assign('rememberme',$rememberme);
$remembertime = $tikilib->get_preference('remembertime',7200);
$smarty->assign('remembertime',$remembertime);

$feature_wiki_description = 'n';
$smarty->assign('feature_wiki_description',$feature_wiki_description);
$feature_wiki_pictures = 'n';
$smarty->assign('feature_wiki_pictures',$feature_wiki_pictures);
$feature_surveys = 'n';
$smarty->assign('feature_surveys',$feature_surveys);
$feature_newsletters = 'n';
$smarty->assign('feature_newsletters',$feature_newsletters);
$feature_webmail = 'n';
$smarty->assign('feature_webmail',$feature_webmail);
$feature_obzip = 'n';
$smarty->assign('feature_obzip',$feature_obzip);
$direct_pagination = 'n';
$smarty->assign('direct_pagination',$direct_pagination);

$rss_forums = 'y';
$rss_forum = 'y';
$rss_articles = 'y';
$rss_blogs = 'y';
$rss_image_galleries = 'y';
$rss_file_galleries = 'y';
$rss_wiki = 'y';
$rss_image_gallery = 'n';
$rss_file_gallery = 'n';
$rss_blog = 'n';

$count_admin_pvs='y';

$directory_columns = 3;
$directory_links_per_page = 20;
$directory_open_links='n';
$directory_validate_urls = 'n';
$smarty->assign('directory_validate_urls',$directory_validate_urls);
$smarty->assign('directory_columns',$directory_columns);
$smarty->assign('directory_links_per_page',$directory_links_per_page);
$smarty->assign('directory_open_links',$directory_open_links);

$max_rss_forums = 10;
$max_rss_forum = 10;
$max_rss_articles = 10;
$max_rss_blogs = 10;
$max_rss_image_galleries = 10;
$max_rss_file_galleries = 10;
$max_rss_wiki = 10;
$max_rss_image_gallery = 10;
$max_rss_file_gallery = 10;
$max_rss_blog = 10;

$keep_versions = 1;

$feature_custom_home = 'n';

$w_use_db = 'y';
$w_use_dir = '';
$uf_use_db = 'y';
$uf_use_dir = '';
$smarty->assign('uf_use_db',$uf_use_db);
$smarty->assign('uf_use_dir',$uf_use_dir);
$userfiles_quota = 30;
$smarty->assign('userfiles_quota',$userfiles_quota);

$feature_wiki_attachments = 'n';
$feature_page_title = 'y';


$t_use_db = 'y';
$t_use_dir = '';
$smarty->assign('t_use_db',$t_use_db);
$smarty->assign('t_use_dir',$t_use_dir);
$feature_trackers = 'n';
$smarty->assign('feature_trackers',$feature_trackers);

$feature_directory = 'n';
$smarty->assign('feature_directory',$feature_directory);

$feature_usermenu = 'n';
$smarty->assign('feature_usermenu',$feature_usermenu);

/*
$feature_wiki_notepad = 'n';
$smarty->assign('feature_wiki_notepad',$feature_wiki_notepad);
*/

$feature_minical = 'n';
$smarty->assign('feature_minical',$feature_minical);

$feature_notepad = 'n';
$smarty->assign('feature_notepad',$feature_notepad);
$feature_userfiles = 'n';
$smarty->assign('feature_userfiles',$feature_userfiles);
$feature_theme_control = 'n';
$smarty->assign('feature_theme_control',$feature_theme_control);
$feature_workflow = 'n';
$smarty->assign('feature_workflow',$feature_workflow);
$feature_charts = 'n';
$feature_user_watches = 'n';
$smarty->assign('feature_user_watches',$feature_user_watches);

$smarty->assign('feature_charts',$feature_charts);
$feature_phpopentracker = 'n';
$smarty->assign('feature_phpopentracker',$feature_phpopentracker);

$feature_eph = 'n';
$smarty->assign('feature_eph',$feature_eph);

$feature_live_support = 'n';
$smarty->assign('feature_live_support',$feature_live_support);

$feature_banning = 'n';
$smarty->assign('feature_banning',$feature_banning);


$feature_wiki_usrlock = 'n';
$smarty->assign('feature_wiki_usrlock',$feature_wiki_usrlock);


$minical_reminders = $tikilib->get_user_preference($user,'minical_reminders',0);
$smarty->assign('minical_reminders',$minical_reminders);



$feature_contact = 'n';
$smarty->assign('feature_contact',$feature_contact);
$contact_user = $tikilib->get_preference('contact_user','admin');
$smarty->assign('contact_user',$contact_user);

$webmail_view_html = 'y';
$smarty->assign('webmail_view_html',$webmail_view_html);
$webmail_max_attachment = 1500000;
$smarty->assign('webmail_max_attachment',$webmail_max_attachment);

$feature_clear_passwords = 'y';
$smarty->assign('feature_clear_passwords','n');
$feature_challenge = 'n';
$smarty->assign('feature_challenge','n');
$min_pass_length=1;
$smarty->assign('min_pass_length',$min_pass_length);
$pass_chr_num='n';
$smarty->assign('pass_chr_num',$pass_chr_num);
$pass_due=999;
$smarty->assign('pass_due',$pass_due);

$smarty->assign('feature_page_title',$feature_page_title);
$smarty->assign('w_use_db',$w_use_db);
$smarty->assign('w_use_dir',$w_use_dir);
$smarty->assign('feature_wiki_attachments',$feature_wiki_attachments);

$smarty->assign('dblclickedit','n');

$smarty->assign('feature_custom_home',$feature_custom_home);

$smarty->assign('keep_versions',$keep_versions);

$smarty->assign('count_admin_pvs',$count_admin_pvs);

$smarty->assign('wiki_spellcheck',$wiki_spellcheck);
$smarty->assign('cms_spellcheck',$cms_spellcheck);
$smarty->assign('blog_spellcheck',$blog_spellcheck);
$smarty->assign('blog_list_order',$blog_list_order);

$blog_list_user = 'text';
$smarty->assign('blog_list_user',$blog_list_user);

$smarty->assign('forum_list_topics','y');
$smarty->assign('forum_list_posts','y');
$smarty->assign('forum_list_ppd','y');
$smarty->assign('forum_list_lastpost','y');
$smarty->assign('forum_list_visits','y');
$smarty->assign('forum_list_desc','y');

$smarty->assign('gal_list_name','y');
$smarty->assign('gal_list_description','y');
$smarty->assign('gal_list_created','y');
$smarty->assign('gal_list_lastmodif','y');
$smarty->assign('gal_list_user','y');
$smarty->assign('gal_list_imgs','y');
$smarty->assign('gal_list_visits','y');

$smarty->assign('fgal_list_name','y');
$smarty->assign('fgal_list_description','y');
$smarty->assign('fgal_list_created','y');
$smarty->assign('fgal_list_lastmodif','y');
$smarty->assign('fgal_list_user','y');
$smarty->assign('fgal_list_files','y');
$smarty->assign('fgal_list_hits','y');


$blog_list_title = 'y';
$blog_list_description = 'y';
$blog_list_created = 'y';
$blog_list_lastmodif = 'y';
$blog_list_user = 'y';
$blog_list_posts = 'y';
$blog_list_visits = 'y';
$blog_list_activity = 'y';
$smarty->assign('blog_list_title',$blog_list_title);
$smarty->assign('blog_list_description',$blog_list_description);
$smarty->assign('blog_list_created',$blog_list_created);
$smarty->assign('blog_list_lastmodif',$blog_list_lastmodif);
$smarty->assign('blog_list_user',$blog_list_user);
$smarty->assign('blog_list_posts',$blog_list_posts);
$smarty->assign('blog_list_visits',$blog_list_visits);
$smarty->assign('blog_list_activity',$blog_list_activity);
$smarty->assign('trl','');

$smarty->assign('userbreadCrumb',$userbreadCrumb);
$smarty->assign('feature_polls',$feature_polls);
$smarty->assign('feature_quizzes',$feature_quizzes);
$smarty->assign('feature_chat',$feature_chat);
$smarty->assign('rss_articles',$rss_articles);
$smarty->assign('rss_forum',$rss_forum);
$smarty->assign('rss_forums',$rss_forums);
$smarty->assign('rss_blogs',$rss_blogs);
$smarty->assign('rss_image_galleries',$rss_image_galleries);
$smarty->assign('rss_file_galleries',$rss_file_galleries);
$smarty->assign('rss_wiki',$rss_wiki);
$smarty->assign('rss_image_gallery',$rss_image_gallery);
$smarty->assign('rss_file_gallery',$rss_file_gallery);
$smarty->assign('rss_blog',$rss_blog);


$smarty->assign('max_rss_articles',$max_rss_articles);
$smarty->assign('max_rss_blogs',$max_rss_blogs);
$smarty->assign('max_rss_image_galleries',$max_rss_image_galleries);
$smarty->assign('max_rss_file_galleries',$max_rss_file_galleries);
$smarty->assign('max_rss_wiki',$max_rss_wiki);
$smarty->assign('max_rss_image_gallery',$max_rss_image_gallery);
$smarty->assign('max_rss_file_gallery',$max_rss_file_gallery);
$smarty->assign('max_rss_blog',$max_rss_blog);

$smarty->assign('fgal_use_db',$fgal_use_db);
$smarty->assign('fgal_use_dir',$fgal_use_dir);
$smarty->assign('gal_use_db',$gal_use_db);
$smarty->assign('gal_use_lib',$gal_use_lib);
$smarty->assign('gal_use_dir',$gal_use_dir);
$smarty->assign('fgal_match_regex',$fgal_match_regex);
$smarty->assign('fgal_nmatch_regex',$fgal_nmatch_regex);
$smarty->assign('gal_match_regex',$gal_match_regex);
$smarty->assign('gal_nmatch_regex',$gal_nmatch_regex);


$smarty->assign('feature_left_column',$feature_left_column);
$smarty->assign('feature_right_column',$feature_right_column);
$smarty->assign('feature_top_bar',$feature_top_bar);
$smarty->assign('feature_bot_bar',$feature_bot_bar);




$smarty->assign('feature_file_galleries',$feature_file_galleries);
$smarty->assign('feature_file_galleries_rankings',$feature_file_galleries_rankings);
$smarty->assign('language',$language);
$smarty->assign('lang_use_db',$lang_use_db);
$smarty->assign('tmpDir',$tmpDir);
$smarty->assign('home_blog',$home_blog);
$smarty->assign('home_forum',$home_forum);
$smarty->assign('home_gallery',$home_gallery);
$smarty->assign('home_file_gallery',$home_file_gallery);
$smarty->assign('feature_dynamic_content',$feature_dynamic_content);
$smarty->assign('feature_edit_templates',$feature_edit_templates);
$smarty->assign('feature_top_banner',$feature_top_banner);
$smarty->assign('feature_banners',$feature_banners);
$smarty->assign('feature_xmlrpc',$feature_xmlrpc);
$smarty->assign('feature_drawings',$feature_drawings);
$smarty->assign('layout_section',$layout_section);
$smarty->assign('feature_html_pages',$feature_html_pages);
$smarty->assign('feature_search_stats',$feature_search_stats);
$smarty->assign('feature_referer_stats',$feature_referer_stats);
$smarty->assign('feature_smileys',$feature_smileys);
$smarty->assign('feature_comm',$feature_comm);
$smarty->assign('feature_cms_rankings',$feature_cms_rankings);
$smarty->assign('feature_blog_rankings',$feature_blog_rankings);
$smarty->assign('feature_gal_rankings',$feature_gal_rankings);
$smarty->assign('feature_wiki_rankings',$feature_wiki_rankings);
$smarty->assign('feature_wiki_undo',$feature_wiki_undo);

$smarty->assign('feature_wiki_templates',$feature_wiki_templates);
$smarty->assign('feature_cms_templates',$feature_cms_templates);

$smarty->assign('feature_forum_rankings',$feature_forum_rankings);
$smarty->assign('feature_forum_parse',$feature_forum_parse);
$smarty->assign('feature_forum_quickjump',$feature_forum_quickjump);
$smarty->assign('feature_forum_topicd',$feature_forum_topicd);
$smarty->assign('feature_hotwords',$feature_hotwords);
$smarty->assign('feature_hotwords_nw',$feature_hotwords_nw);
$smarty->assign('feature_lastChanges',$feature_lastChanges);
$smarty->assign('feature_dump',$feature_dump);
$smarty->assign('feature_categories',$feature_categories);
$smarty->assign('feature_faqs',$feature_faqs);
$smarty->assign('feature_shoutbox',$feature_shoutbox);
$smarty->assign('feature_stats',$feature_stats);
$smarty->assign('feature_games',$feature_games);
$smarty->assign('user_assigned_modules',$user_assigned_modules);
$smarty->assign('feature_user_bookmarks',$feature_user_bookmarks);
$smarty->assign('feature_ranking',$feature_ranking);
$smarty->assign('feature_listPages', $feature_listPages);
$smarty->assign('feature_history', $feature_history);
$smarty->assign('feature_backlinks', $feature_backlinks);
$smarty->assign('feature_likePages', $feature_likePages);
$smarty->assign('feature_search', $feature_search);
$smarty->assign('feature_search_fulltext', $feature_search_fulltext);
$smarty->assign('feature_sandbox', $feature_sandbox);
$smarty->assign('feature_userPreferences', $feature_userPreferences);
$smarty->assign('feature_userVersions', $feature_userVersions);
$smarty->assign('feature_galleries',$feature_galleries);
$smarty->assign('feature_featuredLinks',$feature_featuredLinks);
$smarty->assign('feature_wiki',$feature_wiki);
$smarty->assign('feature_articles',$feature_articles);
$smarty->assign('feature_submissions',$feature_submissions);
$smarty->assign('feature_blogs',$feature_blogs);

$smarty->assign('feature_wiki_comments',$feature_wiki_comments);
$smarty->assign('wiki_comments_default_ordering',$wiki_comments_default_ordering);
$smarty->assign('wiki_comments_per_page',$wiki_comments_per_page);

$smarty->assign('feature_faq_comments',$feature_faq_comments);
$smarty->assign('faq_comments_default_ordering',$faq_comments_default_ordering);
$smarty->assign('faq_comments_per_page',$faq_comments_per_page);


$smarty->assign('feature_forums',$feature_forums);
$smarty->assign('forums_ordering',$forums_ordering);
$smarty->assign('forums_comments_per_page',$forums_comments_per_page);


$smarty->assign('feature_image_galleries_comments',$feature_image_galleries_comments);
$smarty->assign('image_galleries_comments_default_ordering',$image_galleries_comments_default_ordering);
$smarty->assign('image_galleries_comments_per_page',$image_galleries_comments_per_page);

$smarty->assign('feature_file_galleries_comments',$feature_file_galleries_comments);
$smarty->assign('file_galleries_comments_default_ordering',$file_galleries_comments_default_ordering);
$smarty->assign('file_galleries_comments_per_page',$file_galleries_comments_per_page);


$smarty->assign('feature_poll_comments',$feature_poll_comments);
$smarty->assign('poll_comments_default_ordering',$poll_comments_default_ordering);
$smarty->assign('poll_comments_per_page',$poll_comments_per_page);


$smarty->assign('feature_blog_comments',$feature_blog_comments);
$smarty->assign('blog_comments_default_ordering',$blog_comments_default_ordering);
$smarty->assign('blog_comments_per_page',$blog_comments_per_page);

$smarty->assign('feature_article_comments',$feature_article_comments);
$smarty->assign('article_comments_default_ordering',$article_comments_default_ordering);
$smarty->assign('article_comments_per_page',$article_comments_per_page);


$smarty->assign('feature_warn_on_edit',$feature_warn_on_edit);
$smarty->assign('warn_on_edit_time',$warn_on_edit_time);

// Other preferences
$popupLinks = $tikilib->get_preference("popupLinks",'n');
$anonCanEdit = $tikilib->get_preference("anonCanEdit",'n');
$modallgroups = $tikilib->get_preference("modallgroups",'y');
$change_language = $tikilib->get_preference("change_language",'y');
$change_theme = $tikilib->get_preference("change_theme",'y');
$tikiIndex = $tikilib->get_preference("tikiIndex",'tiki-index.php');
$cachepages = $tikilib->get_preference("cachepages",'y');
$cacheimages = $tikilib->get_preference("cacheimages",'y');
$allowRegister = $tikilib->get_preference("allowRegister",'n');
$useRegisterPasscode = $tikilib->get_preference("useRegisterPasscode",'n');
$registerPasscode = $tikilib->get_preference("registerPasscode",'');
$useUrlIndex = $tikilib->get_preference("useUrlIndex",'n');
$urlIndex = $tikilib->get_preference("useUrlIndex",'');
$wikiHomePage = $tikilib->get_preference("wikiHomePage",'HomePage');
$smarty->assign('wikiHomePage',$wikiHomePage);

$wiki_page_regex = $tikilib->get_preference('wiki_page_regex','strict');
$smarty->assign('wiki_page_regex',$wiki_page_regex);
// Please DO NOT modify any of the brackets in the regex(s).
// It may seem redundent but, really, they are ALL REQUIRED.
if($wiki_page_regex == 'strict') {
  $page_regex = '([A-Za-z0-9_])([\.: A-Za-z0-9_\-])*([A-Za-z0-9_])';	
} else {
  $page_regex = '([A-Za-z0-9_]|[\x80-\xFF])([\.: A-Za-z0-9_\-]|[\x80-\xFF])*([A-Za-z0-9_]|[\x80-\xFF])';	
}

// PEAR::Auth support
$auth_method = "tiki";
$smarty->assign('auth_method',$auth_method);
$auth_pear = "tiki";
$smarty->assign('auth_pear',$auth_pear);
$auth_create_user_tiki = "n";
$smarty->assign('auth_create_user_tiki',$auth_create_user_tiki);
$auth_create_user_auth = "n";
$smarty->assign('auth_create_user_auth',$auth_create_user_auth);
$auth_skip_admin = "y";
$smarty->assign('auth_skip_admin',$auth_skip_admin);
$auth_ldap_host = "localhost";
$smarty->assign('auth_ldap_host',$auth_ldap_host);
$auth_ldap_port = "389";
$smarty->assign('auth_ldap_port',$auth_ldap_port);
$auth_ldap_scope = "sub";
$smarty->assign('auth_ldap_scope',$auth_ldap_scope);
$auth_ldap_basedn = "";
$smarty->assign('auth_ldap_basedn',$auth_ldap_basedn);
$auth_ldap_userdn = "";
$smarty->assign('auth_ldap_userdn',$auth_ldap_userdn);
$auth_ldap_userattr = "uid";
$smarty->assign('auth_ldap_userattr',$auth_ldap_userattr);
$auth_ldap_useroc = "inetOrgPerson";
$smarty->assign('auth_ldap_useroc',$auth_ldap_useroc);
$auth_ldap_groupdn = "";
$smarty->assign('auth_ldap_groupdn',$auth_ldap_groupdn);
$auth_ldap_groupattr = "cn";
$smarty->assign('auth_ldap_groupattr',$auth_ldap_groupattr);
$auth_ldap_groupoc = "groupOfUniqueNames";
$smarty->assign('auth_ldap_groupoc',$auth_ldap_groupoc);
$auth_ldap_memberattr = "uniqueMember";
$smarty->assign('auth_ldap_memberattr',$auth_ldap_memberattr);
$auth_ldap_memberisdn = "y";
$smarty->assign('auth_ldap_memberisdn',$auth_ldap_memberisdn);
$auth_ldap_adminuser = "";
$smarty->assign('auth_ldap_adminuser',$auth_ldap_adminuser);
$auth_ldap_adminpass = "";
$smarty->assign('auth_ldap_adminpass',$auth_ldap_adminpass);

$validateUsers = $tikilib->get_preference("validateUsers",'n');
$forgotPass = $tikilib->get_preference("forgotPass",'n');
$title = $tikilib->get_preference("title","");
$maxRecords = $tikilib->get_preference("maxRecords",10);
$maxArticles = $tikilib->get_preference("maxArticles",10);

$smarty->assign('useUrlIndex',$useUrlIndex);
$smarty->assign('urlIndex',$urlIndex);
$smarty->assign('registerPasscode',$registerPasscode);
$smarty->assign('useRegisterPasscode',$useRegisterPasscode);

$smarty->assign('tikiIndex',$tikiIndex);
$smarty->assign('maxArticles',$maxArticles);
$smarty->assign('popupLinks',$popupLinks);
$smarty->assign('modallgroups',$modallgroups);
$smarty->assign('change_theme',$change_theme);
$smarty->assign('change_language',$change_language);
$smarty->assign('anonCanEdit',$anonCanEdit);
$smarty->assign('allowRegister',$allowRegister);
$smarty->assign('cachepages',$cachepages);
$smarty->assign('cacheimages',$cacheimages);

$smarty->assign('wiki_extras','n');

$feature_server_name=$tikilib->get_preference('feature_server_name',$_SERVER["SERVER_NAME"]);

//print($_SERVER["REQUEST_URI"]);

$smarty->assign('feature_server_name',$feature_server_name);
$_SERVER["SERVER_NAME"] = $feature_server_name;



// Fix IIS servers not setting what they should set (ay ay IIS, ay ay)
if(!isset($_SERVER['QUERY_STRING'])) $_SERVER['QUERY_STRING']='';
if(!isset($_SERVER['REQUEST_URI'])||empty($_SERVER['REQUEST_URI'])) {
  $_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] . '/' . $_SERVER['QUERY_STRING'];
}

if (!isset($feature_bidi)) {
	$feature_bidi='n';
}
$smarty->assign('feature_bidi',$feature_bidi);

/* # not implemented
$http_basic_auth = $tikilib->get_preference('http_basic_auth', '/');
$smarty->assign('http_basic_auth',$http_basic_auth);
*/

$https_login = $tikilib->get_preference('https_login', 'n');
$smarty->assign('https_login',$https_login);
$https_login_required = $tikilib->get_preference('https_login_required', 'n');
$smarty->assign('https_login_required',$https_login_required);

$https_mode = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
if ($https_mode) {
  $http_port  = 80;
  $https_port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 443;
} else {
  $http_port  = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80;
  $https_port = 443;
}

$http_domain = $tikilib->get_preference('http_domain', '');
$smarty->assign('http_domain',$http_domain);
$http_port = $tikilib->get_preference('http_port', $http_port);
$smarty->assign('http_port',$http_port);
$http_prefix = $tikilib->get_preference('http_prefix', '/');
$smarty->assign('http_prefix',$http_prefix);

$https_domain = $tikilib->get_preference('https_domain', '');
$smarty->assign('https_domain',$https_domain);
$https_port = $tikilib->get_preference('https_port', $https_port);
$smarty->assign('https_port',$https_port);
$https_prefix = $tikilib->get_preference('https_prefix', '/');
$smarty->assign('https_prefix',$https_prefix);

$login_url = 'tiki-login.php';
$smarty->assign('login_url', $login_url);

if ($https_login == 'y' || $https_login_required == 'y') {
	$http_login_url = 'http://' . $http_domain;
	if ($http_port != 80)
		$http_login_url .= ':' . $http_port;
	$http_login_url .= $http_prefix . $tikiIndex;
	if (SID)
		$http_login_url .= '?' . SID;
		
	$edit_data = htmlentities(isset($_REQUEST["edit"]) ? $_REQUEST["edit"] : '', ENT_QUOTES);

	$https_login_url = 'https://' . $https_domain;
	if ($https_port != 443)
		$https_login_url .= ':' . $https_port;
	$https_login_url .= $https_prefix . $tikiIndex;
	if (SID)
		$https_login_url .= '?' . SID;

	$stay_in_ssl_mode = isset($_REQUEST['stay_in_ssl_mode']) ? $_REQUEST['stay_in_ssl_mode'] : '';
	
	if ($https_login_required == 'y') {
		# only show "Stay in SSL checkbox if we're not already in HTTPS mode
		$show_stay_in_ssl_mode = !$https_mode ? 'y' : 'n';
		$smarty->assign('show_stay_in_ssl_mode', $show_stay_in_ssl_mode);
		if (!$https_mode) {
			$https_login_url = 'https://' . $https_domain;
			if ($https_port != 443)
				$https_login_url .= ':' . $https_port;
			$https_login_url .= $https_prefix . $login_url;
			if (SID)
				$https_login_url .= '?' . SID;
			$smarty->assign('login_url', $https_login_url);
		} else {
			# We're already in HTTPS mode, so let's stay there
			$stay_in_ssl_mode = 'on';
		}
	} else {
		$smarty->assign('http_login_url',$http_login_url);
		$smarty->assign('https_login_url',$https_login_url);
		# only show "Stay in SSL checkbox if we're not already in HTTPS mode
		$show_stay_in_ssl_mode = $https_mode ? 'y' : 'n';
	}
	$smarty->assign('show_stay_in_ssl_mode', $show_stay_in_ssl_mode);
	$smarty->assign('stay_in_ssl_mode', $stay_in_ssl_mode);
}


if(!file_exists('templates_c/preferences.php')) {
  $prefs = $tikilib->get_all_preferences();
  $fw=@fopen('templates_c/preferences.php',"wb");
  if (!$fw) {
  	if (isset($php_errormsg)) {
  		die($php_errormsg);
  	}
  	fopen('templates_c/preferences.php',"wb");
  	die;
  }
  fwrite($fw,'<?php'."\n");
  foreach($prefs as $name => $val) {
    $$name = $val;
    fwrite($fw,'$'.$name."=\"".$val."\";");
    fwrite($fw,'$smarty->assign("'.$name.'","'.'$'.$name.'");');
    fwrite($fw,"\n");
    $smarty->assign("$name",$val);
  }
  fwrite($fw,'?>');
  fclose($fw);
} else {
  include_once('templates_c/preferences.php');
}

$user_dbl='y';
if($feature_userPreferences == 'y') {
  // Check for FEATURES for the user
  $user_style = $tikilib->get_preference("style", 'elegant.css');
  if($user) {
    $user_dbl=$tikilib->get_user_preference($user,'user_dbl','y'); 
    if($change_theme == 'y') {
      $user_style = $tikilib->get_user_preference($user,'theme',$style);
      if($user_style) {
        $style = $user_style;
      }
    }
    
    if($change_language == 'y') {
      $user_language = $tikilib->get_user_preference($user,'language',$language);
      if($user_language) {
        $language = $user_language;
      }
    }
  }
  $smarty->assign('style',$style);
  $smarty->assign('language',$language);
}
if ($lang_use_db!='y') {
  // check if needed!!!
  global $lang;
  include_once('lang/'.$language.'/language.php');
}

$smarty->assign('user_dbl',$user_dbl);

$stlstl=explode('.',$style);
$style_base = $stlstl[0];




$smarty->assign('user',$user);
$smarty->assign('lock',false);
$smarty->assign('title',$title);
$smarty->assign('maxRecords',$maxRecords);

// If we are processing a login then do not generate the challenge
// if we are in any other case then yes.
if(!strstr($_SERVER["REQUEST_URI"],'tiki-login')) {
  if($feature_challenge == 'y') {
    $chall=$userlib->generate_challenge();
    $_SESSION["challenge"]=$chall;
    $smarty->assign('challenge',$chall);
  }  
}


$smarty->assign('mnu_dirmenu','display:none;');
if(isset($_COOKIE["dirmenu"])) {
  if($_COOKIE["dirmenu"]=='o') {
    $smarty->assign('mnu_dirmenu','display:block;');
  }	
}

$smarty->assign('mnu_nlmenu','display:none;');
if(isset($_COOKIE["nlmenu"])) {
  if($_COOKIE["nlmenu"]=='o') {
    $smarty->assign('mnu_nlmenu','display:block;');
  }	
}

$smarty->assign('mnu_chartmenu','display:none;');
if(isset($_COOKIE["chartmenu"])) {
  if($_COOKIE["chartmenu"]=='o') {
    $smarty->assign('mnu_chartmenu','display:block;');
  }	
}


$smarty->assign('mnu_ephmenu','display:none;');
if(isset($_COOKIE["ephmenu"])) {
  if($_COOKIE["ephmenu"]=='o') {
    $smarty->assign('mnu_ephmenu','display:block;');
  }	
}

$smarty->assign('mnu_mymenu','display:none;');
if(isset($_COOKIE["mymenu"])) {
  if($_COOKIE["mymenu"]=='o') {
    $smarty->assign('mnu_mymenu','display:block;');
  }	
}

$smarty->assign('mnu_workflow','display:none;');
if(isset($_COOKIE["wfmenu"])) {
  if($_COOKIE["wfmenu"]=='o') {
    $smarty->assign('mnu_workflow','display:block;');
  }	
}


$smarty->assign('mnu_usrmenu','display:none;');
if(isset($_COOKIE["usrmenu"])) {
  if($_COOKIE["usrmenu"]=='o') {
    $smarty->assign('mnu_usrmenu','display:block;');
  }	
}


$smarty->assign('mnu_wikimenu','display:none;');
if(isset($_COOKIE["wikimenu"])) {
  if($_COOKIE["wikimenu"]=='o') {
    $smarty->assign('mnu_wikimenu','display:block;');
  }	
}
$smarty->assign('mnu_srvmenu','display:none;');
if(isset($_COOKIE["srvmenu"])) {
  if($_COOKIE["srvmenu"]=='o') {
    $smarty->assign('mnu_srvmenu','display:block;');
  }	
}
$smarty->assign('mnu_trkmenu','display:none;');
if(isset($_COOKIE["trkmenu"])) {
  if($_COOKIE["trkmenu"]=='o') {
    $smarty->assign('mnu_trkmenu','display:block;');
  }	
}
$smarty->assign('mnu_quizmenu','display:none;');
if(isset($_COOKIE["quizmenu"])) {
  if($_COOKIE["quizmenu"]=='o') {
    $smarty->assign('mnu_quizmenu','display:block;');
  }	
}
$smarty->assign('mnu_formenu','display:none;');
if(isset($_COOKIE["formenu"])) {
  if($_COOKIE["formenu"]=='o') {
    $smarty->assign('mnu_formenu','display:block;');
  }	
}
$smarty->assign('mnu_admmnu','display:none;');
if(isset($_COOKIE["admmnu"])) {
  if($_COOKIE["admmnu"]=='o') {
    $smarty->assign('mnu_admmnu','display:block;');
  }	
}
$smarty->assign('mnu_faqsmenu','display:none;');
if(isset($_COOKIE["faqsmenu"])) {
  if($_COOKIE["faqsmenu"]=='o') {
    $smarty->assign('mnu_faqsmenu','display:block;');
  }	
}
$smarty->assign('mnu_galmenu','display:none;');
if(isset($_COOKIE["galmenu"])) {
  if($_COOKIE["galmenu"]=='o') {
    $smarty->assign('mnu_galmenu','display:block;');
  }	
}
$smarty->assign('mnu_cmsmenu','display:none;');
if(isset($_COOKIE["cmsmenu"])) {
  if($_COOKIE["cmsmenu"]=='o') {
    $smarty->assign('mnu_cmsmenu','display:block;');
  }	
}
$smarty->assign('mnu_blogmenu','display:none;');
if(isset($_COOKIE["blogmenu"])) {
  if($_COOKIE["blogmenu"]=='o') {
    $smarty->assign('mnu_blogmenu','display:block;');
  }	
}
$smarty->assign('mnu_filegalmenu','display:none;');
if(isset($_COOKIE["filegalmenu"])) {
  if($_COOKIE["filegalmenu"]=='o') {
    $smarty->assign('mnu_filegalmenu','display:block;');
  }	
}





if($user && $feature_usermenu == 'y') {
  if(!isset($_SESSION['usermenu'])) {
	  include_once('lib/usermenu/usermenulib.php');
	  $user_menus = $usermenulib->list_usermenus($user,0,-1,'position_asc','');
	  $smarty->assign('usr_user_menus',$user_menus['data']);
	  $_SESSION['usermenu']=$user_menus['data'];
  } else {
  	  $user_menus = $_SESSION['usermenu'];
	  $smarty->assign('usr_user_menus',$user_menus);
  }
}


include_once("tiki-modules.php");
$smarty->assign('beingEdited','n');

if($feature_warn_on_edit == 'y') {
  // Check if the page is being edited
  if(isset($_REQUEST["page"])) {
   $chkpage = $_REQUEST["page"];
  } else {
   $chkpage = 'HomePage';
  }
  // Notice if a page is being edited or if it was being edited and not anymore
  //print($GLOBALS["HTTP_REFERER"]);
  // IF isset the referer and if the referer is editpage then unset taking the pagename from the
  // query or homepage if not query
  if(isset($HTTP_SERVER_VARS["HTTP_REFERER"])) {
    if(strstr($HTTP_SERVER_VARS["HTTP_REFERER"],'tiki-editpage')) {
      $purl = parse_url($HTTP_SERVER_VARS["HTTP_REFERER"]);
      if(!isset($purl["query"])) {
        $purl["query"]='';
      }
      parse_str($purl["query"],$purlquery);
      if(!isset($purlquery["page"])) {
        $purlquery["page"]='HomePage';
      }
      if(isset($_SESSION["edit_lock"])) {
            $tikilib->semaphore_unset($purlquery["page"],$_SESSION["edit_lock"]);
      }
    }
  }
  if(strstr($_SERVER["REQUEST_URI"],'tiki-editpage')) {
    $purl = parse_url($_SERVER["REQUEST_URI"]);
    if(!isset($purl["query"])) {
      $purl["query"]='';
    }
    parse_str($purl["query"],$purlquery);
    if(!isset($purlquery["page"])) {
      $purlquery["page"]='HomePage';
    }
    $_SESSION["edit_lock"]=$tikilib->semaphore_set($purlquery["page"]);
  } 
  
  
  if($tikilib->semaphore_is_set($chkpage,$warn_on_edit_time*60)) {
    $smarty->assign('semUser',$tikilib->get_semaphore_user($chkpage));
    $smarty->assign('beingEdited','y');
    $beingedited='y';
  } else {
    $smarty->assign('beingEdited','n');
    $beingedited='n';
  }
  
}

if(isset($_REQUEST["pollVote"])) {
  if($tiki_p_vote_poll == 'y' && !$tikilib->user_has_voted($user,'poll'.$_REQUEST["polls_pollId"]) && isset($_REQUEST["polls_optionId"])) {
    $tikilib->register_user_vote($user,'poll'.$_REQUEST["polls_pollId"]);
    $tikilib->poll_vote($_REQUEST["polls_pollId"],$_REQUEST["polls_optionId"]);
  }
  $pollId=$_REQUEST["polls_pollId"];
  header("location: tiki-poll_results.php?pollId=$pollId");
}

$ownurl = httpPrefix().$_SERVER["REQUEST_URI"];
$parsed=parse_url($_SERVER["REQUEST_URI"]);
if(!isset($parsed["query"])) {
  $parsed["query"]='';
}
parse_str($parsed["query"],$query);
$father=httpPrefix().$parsed["path"];
if(count($query)>0) {
  $first=1;
  foreach($query as $name => $val) {
    if($first) {
      $first=false;
      $father.='?'.$name.'='.$val;
    } else {
      $father.='&amp;'.$name.'='.$val;
    }
  }
  $father.='&amp;';
} else {
  $father.='?';
}
$ownurl_father=$father;
$smarty->assign('ownurl',httpPrefix().$_SERVER["REQUEST_URI"]);

$allowMsgs = 'n';
if($user) {
  $allowMsgs = $tikilib->get_user_preference($user,'allowMsgs','y');
  $tasks_useDates = $tikilib->get_user_preference($user,'tasks_useDates');
  $tasks_maxRecords = $tikilib->get_user_preference($user,'tasks_maxRecords');
  $smarty->assign('tasks_useDates',$tasks_useDates);
  $smarty->assign('tasks_maxRecords',$tasks_maxRecords);
  $smarty->assign('allowMsgs',$allowMsgs);
} 

if($feature_live_support == 'y') {
	$smarty->assign('user_is_operator','n');
	if($user) {
		include_once('lib/live_support/lsadminlib.php');
		if($lsadminlib->is_operator($user)) {
			$smarty->assign('user_is_operator','y');
		}
	}
}

if($feature_referer_stats == 'y') {
// Referer tracking
if(isset($HTTP_SERVER_VARS["HTTP_REFERER"])) {
  $pref = parse_url($HTTP_SERVER_VARS["HTTP_REFERER"]);
  if(!strstr($_SERVER["SERVER_NAME"],$pref["host"])) {
    $tikilib->register_referer($pref["host"]);
  }
}
}

// Stats
if($feature_stats == 'y') {
  if($count_admin_pvs == 'y' || $user!='admin') {	
    if(!strstr($_SERVER["REQUEST_URI"],'chat')) {
      $tikilib->add_pageview();
    }
  }
}

/*
if($feature_phpopentracker == 'y') {

	include_once('phpOpenTracker.php');
	// log access
	phpOpenTracker::log();
}
*/

$smarty->assign('uses_tabs','n');

$user_preferences=Array();


include_once('tiki-handlers.php');




if($feature_obzip == 'y') {
  ob_start("ob_gzhandler");
}

?>
