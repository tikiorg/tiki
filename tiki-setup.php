<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-setup.php,v 1.227 2004-05-30 20:57:40 sylvieg Exp $


// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//xdebug_start_profiling();


/*!
    \static
*/

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],"tiki-setup.php")!=FALSE) {
  header("location: index.php");
}

// include_once("lib/init/setup_inc.php");
include_once("lib/init/initlib.php");

class TikiSetup extends TikiInit {

    /*!
        Check that everything is set up properly

        \static
    */
    function check() {
        static $checked;

        if ($checked) {
            return;
        }

        $checked = true;

        $errors = '';
        
        if (strpos($_SERVER["SERVER_SOFTWARE"],"IIS")==TRUE){
        	$docroot = dirname($_SERVER['PATH_TRANSLATED']);
        }
        else{
        	$docroot = getcwd();
        }

        if (ini_get('session.save_handler') == 'files') {
            $save_path = ini_get('session.save_path');

            if (!is_dir($save_path)) {
                $errors .= "The directory '$save_path' does not exist or PHP is not allowed to access it (check open_basedir entry in php.ini).\n";
            } else if (!is_writeable($save_path)) {
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

        $wwwuser = '';
        $wwwgroup = '';

        if (TikiSetup::isWindows()) {
            $wwwuser = 'SYSTEM';

            $wwwgroup = 'SYSTEM';
        }

        if (function_exists('posix_getuid')) {
            $user = @posix_getpwuid(@posix_getuid());

            $group = @posix_getpwuid(@posix_getgid());
            $wwwuser = $user ? $user['name'] : false;
            $wwwgroup = $group ? $group['name'] : false;
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
        # 'var',
        # 'var/log',
        # 'var/log/irc',
        );
        foreach ($dirs as $dir) {
            if (!is_dir("$docroot/$dir")) {
                $errors .= "The directory '$docroot/$dir' does not exist.\n";
            } else if (!is_writeable("$docroot/$dir")) {
                $errors .= "The directory '$docroot/$dir' is not writeable by $wwwuser.\n";
            }
        }

        if ($errors) {
            $PHP_CONFIG_FILE_PATH = PHP_CONFIG_FILE_PATH;

            ob_start();
            phpinfo (INFO_MODULES);
            $httpd_conf = 'httpd.conf';

            if (preg_match('/Server Root<\/b><\/td><td\s+align="left">([^<]*)</', ob_get_contents(), $m)) {
                $httpd_conf = $m[1] . '/' . $httpd_conf;
            }

            ob_end_clean();

            print "
<html><body>
<h2><font color='red'>TikiWiki is not properly set up:</font></h1>
<pre>
$errors
";

            if (!TikiSetup::isWindows()) {
                print "You may either chmod the directories above manually to 777, or run one of the sets of commands below.
<b><a href='tiki-install.php'>Proceed to the Tiki installer</a></b> after you run the commands below.

If you cannot become root, and are NOT part of the group $wwwgroup:
    \$ bash
    \$ cd $docroot
    \$ chmod +x setup.sh
    \$ ./setup.sh yourlogin yourgroup 02777
    Tip: You can find your group using the command 'id'.

If you cannot become root, but are a member of the group $wwwgroup:
    \$ bash
    \$ cd $docroot
    \$ chmod +x setup.sh
    \$ ./setup.sh mylogin $wwwgroup</i>

If you can become root:
    \$ bash
    \$ cd $docroot
    \$ chmod +x setup.sh
    \$ su -c './setup.sh $wwwuser'

If you have problems accessing a directory, check the open_basedir entry in
$PHP_CONFIG_FILE_PATH/php.ini or $httpd_conf.

Once you have executed these commands, this message will disappear!

Note: If you cannot become root, you will not be able to delete certain
files created by apache, and will need to ask your system administrator
to delete them for you if needed.

<a href='http://tikiwiki.org/InstallTiki' target='_blank'>Consult the tikiwiki.org installation guide</a> if you need more help.

<b><a href='tiki-install.php'>Proceed to the Tiki installer</a></b> if you've completed the steps above.
</pre></body></html>";
            }

            exit;
        }
		
	
    }
}

TikiSetup::check();
TikiSetup::prependIncludePath('lib');
TikiSetup::prependIncludePath('lib/pear');

$tmpDir = TikiInit::tempdir();

class timer {
    function parseMicro($micro) {
        list($micro, $sec) = explode(' ', microtime());

        return $sec + $micro;
    }

    function start($timer = 'default') {
        $this->timer[$timer] = $this->parseMicro(microtime());
    }

    function stop($timer = 'default') {
        return $this->current($timer);
    }

    function elapsed($timer = 'default') {
        return $this->parseMicro(microtime()) - $this->timer[$timer];
    }
}

$tiki_timer = new timer();
$tiki_timer->start();

// for PHP<4.2.0
if (!function_exists('array_fill')) {
  require_once('lib/compat/array_fill.func.php');
}

//num queries has to be global
global $num_queries;
$num_queries=0;

include_once ("tiki-setup_base.php");
//print("tiki-setup: before rest of tiki-setup:".$tiki_timer->elapsed()."<br />");

if ( $user ) { // load users_users and user_preferences cache for logged-in user
    $tikilib->load_user_cache($user, 'all');
}

//check to see if admin has closed the site
$site_closed = $tikilib->get_preference('site_closed','n');
if ($site_closed == 'y' and $tiki_p_access_closed_site != 'y' and !isset($bypass_siteclose_check)) {
    $site_closed_msg = $tikilib->get_preference('site_closed_msg','Site is closed for maintainance; please come back later.');
    $url = 'tiki-error_simple.php?error=' . urlencode("$site_closed_msg");
    header('location: ' . $url);
    exit;
}

//check to see if max server load threshold is enabled
$use_load_threshold = $tikilib->get_preference('use_load_threshold','n');
// get average server load in the last minute
if ($load = @file('/proc/loadavg')) {
    list($server_load) = explode(' ', $load[0]);
    $smarty->assign('server_load',$server_load);
    if ($use_load_threshold == 'y' and $tiki_p_access_closed_site != 'y' and !isset($bypass_siteclose_check)) {
        $load_threshold = $tikilib->get_preference('load_threshold',3);
        if ($server_load > $load_threshold) {
            $site_busy_msg = $tikilib->get_preference('site_busy_msg','Server is currently too busy; please come back later.');
            $url = 'tiki-error_simple.php?error=' . urlencode($site_busy_msg);
            header('location: ' . $url);
            exit;
        }
    }
}


// The votes array stores the votes the user has made
if (!isset($_SESSION["votes"])) {
    $votes = array();

    //session_register("votes");
    $_SESSION["votes"] = $votes;
}

$appname = "tiki";

if (!isset($_SESSION["appname"])) {
    //session_register("appname");
    $_SESSION["appname"] = $appname;
}

$smarty->assign("appname", $appname);

if (isset($_REQUEST["PHPSESSID"])) {
    $tikilib->update_session($_REQUEST["PHPSESSID"]);
} else if (function_exists("session_id")) {
    $tikilib->update_session(session_id());
}

/* Commented on revision 1.208 because not used, log message said more
if (!isset($_SESSION["last_forum_visit"])) {
    $now = date("U");

    if ($user) {
        $last_forum_visit = $tikilib->get_user_preference($user, 'last_forum_visit', 0);

        $tikilib->set_user_preference($user, 'last_forum_visit', $now);
    } else {
        $last_forum_visit = $now;
    }

    $_SESSION["last_forum_visit"] = $last_forum_visit;
}
*/

if (file_exists('lib/bablotron.php')) {
	$lib_spellcheck = 'y';
	$wiki_spellcheck = 'n';
	$cms_spellcheck = 'n';
	$blog_spellcheck = 'n';
	$smarty->assign('lib_spellcheck', $lib_spellcheck);
	$smarty->assign('wiki_spellcheck', $wiki_spellcheck);
	$smarty->assign('cms_spellcheck', $cms_spellcheck);
	$smarty->assign('blog_spellcheck', $blog_spellcheck);
}

if (isset($_REQUEST['page'])) { $_REQUEST['page'] = strip_tags($_REQUEST['page']); }

$area = 'tiki';
$userbreadCrumb = 4;
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
$feature_integrator = 'n';
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
$feature_categorypath = 'n';
$feature_categoryobjects = 'n';
$feature_faqs = 'n';
$feature_shoutbox = 'n';
$shoutbox_autolink = 'n';
$feature_stats = 'n';
$feature_games = 'n';
$user_assigned_modules = 'n';
$feature_user_bookmarks = 'n';
$feature_blog_rankings = 'y';
$feature_cms_rankings = 'y';
$feature_gal_rankings = 'y';
$feature_wiki_rankings = 'y';
$feature_wiki_icache = 'n';
$feature_wiki_undo = 'n';
$feature_wiki_multiprint = 'n';
$feature_wiki_pdf = 'n';
$feature_forum_rankings = 'y';
$feature_forum_parse = 'n';
$feature_forum_quickjump = 'n';
$feature_forum_topicd = 'y';
$feature_lastChanges = 'y';
$feature_dump = 'y';
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
$feature_autolinks = 'y';
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
$feature_menusfolderstyle = 'n';
$feature_calendar = 'n';
$feature_editcss = 'n';
$feature_wiki_monosp = 'y';
$feature_maps = 'n';
$feature_modulecontrols = 'n';
$smarty->assign('feature_modulecontrols', $feature_modulecontrols);
$feature_phplayers = 'n';
$smarty->assign('feature_phplayers', $feature_phplayers);
$feature_jscalendar = 'n';
$smarty->assign('feature_jscalendar', $feature_jscalendar);
$feature_tabs = 'n';
$smarty->assign('feature_tabs', $feature_tabs);

$feature_ticketlib = 'n';
$smarty->assign('feature_ticketlib',$feature_ticketlib);

$feature_ticketlib2 = 'y';
$smarty->assign('feature_ticketlib2',$feature_ticketlib2);

$wiki_uses_slides = 'n';
$smarty->assign('wiki_uses_slides', $wiki_uses_slides);

$feature_wiki_allowhtml = 'n';
$smarty->assign('feature_wiki_allowhtml ', $feature_wiki_allowhtml );

$feature_help = 'y';
$smarty->assign('feature_help', $feature_help);

$wiki_feature_copyrights = 'n';
$wiki_creator_admin = 'n';
$smarty->assign('wiki_creator_admin', $wiki_creator_admin);

$wiki_watch_author = 'n';
$smarty->assign('wiki_watch_author',$wiki_watch_author);
$wiki_watch_comments = 'y';
$smarty->assign('wiki_watch_comments',$wiki_watch_comments);
$wiki_watch_editor = 'n';
$smarty->assign('wiki_watch_editor',$wiki_watch_editor);

$smarty->assign('art_list_title','y');
$smarty->assign('art_list_topic','y');
$smarty->assign('art_list_date','y');
$smarty->assign('art_list_author','y');
$smarty->assign('art_list_reads','y');
$smarty->assign('art_list_size','y');
$smarty->assign('art_list_img','y');
$smarty->assign('art_view_title','y');
$smarty->assign('art_view_topic','y');
$smarty->assign('art_view_date','y');
$smarty->assign('art_view_author','y');
$smarty->assign('art_view_reads','y');
$smarty->assign('art_view_size','y');
$smarty->assign('art_view_img','y');

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

//default wiki mailin feature values
$feature_mailin = 'n';
$mailin_autocheck = 'n';
$mailin_autocheckFreq = '0';
$mailin_autocheckLast = 0;

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
$image_galleries_comments_default_order = 'points_desc';
$image_galleries_comments_per_page = 10;

$feature_file_galleries_comments = 'n';
$file_galleries_comments_default_ordering = 'points_desc';
$file_galleries_comments_per_page = 10;

$feature_poll_comments = 'n';
$feature_poll_anonymous = 'n';
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

$feature_warn_on_edit = 'n';
$warn_on_edit_time = 2;
$wiki_cache = 0;
$smarty->assign('wiki_cache', $wiki_cache);
$feature_file_galleries = 'n';
$feature_file_galleries_rankings = 'n';
$language = 'en';
$lang_use_db = 'n';

$feature_left_column = 'y';
$feature_right_column = 'y';
$feature_top_bar = 'y';
$feature_bot_bar = 'y';

$feature_blogposts_comments = 'n';
$smarty->assign('feature_blogposts_comments', $feature_blogposts_comments);

$feature_messages = 'n';
$smarty->assign('feature_messages', $feature_messages);
$feature_tasks = 'n';
$smarty->assign('feature_tasks', $feature_tasks);
$feature_newsreader = 'n';
$smarty->assign('feature_newsreader', $feature_newsreader);
$feature_wiki_footnotes = 'n';
$smarty->assign('feature_wiki_footnotes', $feature_wiki_footnotes);
$feature_wiki_monosp = 'y';
$smarty->assign('feature_wiki_monosp', $feature_wiki_monosp);

$system_os = $tikilib->get_preference('system_os', TikiSetup::os());
$smarty->assign('system_os', $system_os);

$error_reporting_level = (int)($tikilib->get_preference('error_reporting_level', E_ALL));
if ($error_reporting_level == 1)
	$error_reporting_level = ($tiki_p_admin == "y") ? E_ALL: 0;
error_reporting($error_reporting_level);

$rememberme = $tikilib->get_preference('rememberme', 'disabled');
$smarty->assign('rememberme', $rememberme);
$remembertime = $tikilib->get_preference('remembertime', 7200);
$smarty->assign('remembertime', $remembertime);

$feature_wiki_description = 'n';
$smarty->assign('feature_wiki_description', $feature_wiki_description);
$feature_wiki_pictures = 'n';
$smarty->assign('feature_wiki_pictures', $feature_wiki_pictures);
$feature_wiki_pictures_new = 'n';
$smarty->assign('feature_wiki_pictures_new', $feature_wiki_pictures_new);
$feature_wikiwords = 'y';
$smarty->assign('feature_wikiwords', $feature_wikiwords);
$feature_wikiwords_usedash = 'y';
$smarty->assign('feature_wikiwords_usedash', $feature_wikiwords_usedash);
$feature_wiki_plurals = 'y';
$smarty->assign('feature_wiki_plurals', $feature_wiki_plurals);
$feature_wiki_paragraph_formatting = 'n';
$smarty->assign('feature_wiki_paragraph_formatting', $feature_wiki_paragraph_formatting);
$feature_surveys = 'n';
$smarty->assign('feature_surveys', $feature_surveys);
$feature_newsletters = 'n';
$smarty->assign('feature_newsletters', $feature_newsletters);
$feature_webmail = 'n';
$smarty->assign('feature_webmail', $feature_webmail);
$feature_obzip = 'n';
$smarty->assign('feature_obzip', $feature_obzip);
$direct_pagination = 'n';
$smarty->assign('direct_pagination', $direct_pagination);
$feature_sheet = 'n';
$smarty->assign('feature_sheet', $feature_sheet);
$feature_multilingual = 'y';
$smarty->assign('feature_multilingual', $feature_multilingual);

$rss_forums = 'y';
$rss_forum = 'y';
$rss_directories = 'y';
$rss_articles = 'y';
$rss_blogs = 'y';
$rss_image_galleries = 'y';
$rss_file_galleries = 'y';
$rss_wiki = 'y';
$rss_image_gallery = 'n';
$rss_file_gallery = 'n';
$rss_blog = 'n';

$count_admin_pvs = 'y';

$directory_columns = 3;
$directory_links_per_page = 20;
$directory_open_links = 'n';
$directory_validate_urls = 'n';
$smarty->assign('directory_validate_urls', $directory_validate_urls);
$smarty->assign('directory_columns', $directory_columns);
$smarty->assign('directory_links_per_page', $directory_links_per_page);
$smarty->assign('directory_open_links', $directory_open_links);

$max_rss_forums = 10;
$max_rss_forum = 10;
$max_rss_directories = 10;
$max_rss_articles = 10;
$max_rss_blogs = 10;
$max_rss_image_galleries = 10;
$max_rss_file_galleries = 10;
$max_rss_wiki = 10;
$max_rss_image_gallery = 10;
$max_rss_file_gallery = 10;
$max_rss_blog = 10;
$max_rss_mapfiles = 10;

$metatag_keywords = '';
$metatag_description = '';
$metatag_author = '';
$metatag_geoposition = '';
$metatag_georegion = '';
$metatag_geoplacename = '';
$metatag_robots = '';
$metatag_revisitafter = '';

$keep_versions = 1;

$feature_custom_home = 'n';

$w_use_db = 'y';
$w_use_dir = '';
$uf_use_db = 'y';
$uf_use_dir = '';
$smarty->assign('uf_use_db', $uf_use_db);
$smarty->assign('uf_use_dir', $uf_use_dir);
$userfiles_quota = 30;
$smarty->assign('userfiles_quota', $userfiles_quota);

$feature_wiki_attachments = 'n';
$feature_page_title = 'y';

$t_use_db = 'y';
$t_use_dir = '';
$smarty->assign('t_use_db', $t_use_db);
$smarty->assign('t_use_dir', $t_use_dir);
$groupTracker = 'n';
$smarty->assign('groupTracker', $groupTracker);
$userTracker = 'n';
$smarty->assign('userTracker', $userTracker);
$feature_trackers = 'n';
$smarty->assign('feature_trackers', $feature_trackers);

$feature_directory = 'n';
$smarty->assign('feature_directory', $feature_directory);

$feature_usermenu = 'n';
$smarty->assign('feature_usermenu', $feature_usermenu);

/*
$feature_wiki_notepad = 'n';
$smarty->assign('feature_wiki_notepad',$feature_wiki_notepad);
*/
$feature_minical = 'n';
$smarty->assign('feature_minical', $feature_minical);
$feature_jukebox = 'n';
$smarty->assign('feature_jukebox', $feature_jukebox);

$feature_notepad = 'n';
$smarty->assign('feature_notepad', $feature_notepad);
$feature_userfiles = 'n';
$smarty->assign('feature_userfiles', $feature_userfiles);
$feature_theme_control = 'n';
$smarty->assign('feature_theme_control', $feature_theme_control);
$feature_workflow = 'n';
$smarty->assign('feature_workflow', $feature_workflow);
$feature_charts = 'n';
$smarty->assign('feature_charts', $feature_charts);
$feature_user_watches = 'n';
$smarty->assign('feature_user_watches', $feature_user_watches);

$feature_eph = 'n';
$smarty->assign('feature_eph', $feature_eph);

$feature_live_support = 'n';
$smarty->assign('feature_live_support', $feature_live_support);

$webserverauth = 'n';
$smarty->assign('webserverauth', $webserverauth);

$feature_banning = 'n';
$smarty->assign('feature_banning', $feature_banning);

$feature_wiki_usrlock = 'n';
$smarty->assign('feature_wiki_usrlock', $feature_wiki_usrlock);

$minical_reminders = $tikilib->get_user_preference($user, 'minical_reminders', 0);
$smarty->assign('minical_reminders', $minical_reminders);

$feature_contact = 'n';
$smarty->assign('feature_contact', $feature_contact);
$contact_user = $tikilib->get_preference('contact_user', 'admin');
$smarty->assign('contact_user', $contact_user);
$default_sender_email = $tikilib->get_user_email($contact_user);
$sender_email = $tikilib->get_preference('sender_email', $default_sender_email);
$smarty->assign('sender_email', $sender_email);

$webmail_view_html = 'y';
$smarty->assign('webmail_view_html', $webmail_view_html);
$webmail_max_attachment = 1500000;
$smarty->assign('webmail_max_attachment', $webmail_max_attachment);

$feature_clear_passwords = 'n';
$smarty->assign('feature_clear_passwords', 'n');
$feature_challenge = 'n';
$smarty->assign('feature_challenge', 'n');
$min_pass_length = 1;
$smarty->assign('min_pass_length', $min_pass_length);
$pass_chr_num = 'n';
$smarty->assign('pass_chr_num', $pass_chr_num);
$pass_due = 999;
$smarty->assign('pass_due', $pass_due);
$rnd_num_reg = 'n';
$smarty->assign('rnd_num_reg', $rnd_num_reg);

$smarty->assign('feature_page_title', $feature_page_title);
$smarty->assign('w_use_db', $w_use_db);
$smarty->assign('w_use_dir', $w_use_dir);
$smarty->assign('feature_wiki_attachments', $feature_wiki_attachments);

$smarty->assign('dblclickedit', 'n');

$smarty->assign('feature_custom_home', $feature_custom_home);

$smarty->assign('keep_versions', $keep_versions);

$smarty->assign('count_admin_pvs', $count_admin_pvs);

$smarty->assign('blog_list_order', $blog_list_order);

$blog_list_user = 'text';
$smarty->assign('blog_list_user', $blog_list_user);

$smarty->assign('forum_list_topics', 'y');
$smarty->assign('forum_list_posts', 'y');
$smarty->assign('forum_list_ppd', 'y');
$smarty->assign('forum_list_lastpost', 'y');
$smarty->assign('forum_list_visits', 'y');
$smarty->assign('forum_list_desc', 'y');

$smarty->assign('gal_list_name', 'y');
$smarty->assign('gal_list_description', 'y');
$smarty->assign('gal_list_created', 'y');
$smarty->assign('gal_list_lastmodif', 'y');
$smarty->assign('gal_list_user', 'y');
$smarty->assign('gal_list_imgs', 'y');
$smarty->assign('gal_list_visits', 'y');

$smarty->assign('fgal_list_name', 'y');
$smarty->assign('fgal_list_description', 'y');
$smarty->assign('fgal_list_created', 'y');
$smarty->assign('fgal_list_lastmodif', 'y');
$smarty->assign('fgal_list_user', 'y');
$smarty->assign('fgal_list_files', 'y');
$smarty->assign('fgal_list_hits', 'y');

$blog_list_title = 'y';
$blog_list_description = 'y';
$blog_list_created = 'y';
$blog_list_lastmodif = 'y';
$blog_list_user = 'y';
$blog_list_posts = 'y';
$blog_list_visits = 'y';
$blog_list_activity = 'y';
$smarty->assign('blog_list_title', $blog_list_title);
$smarty->assign('blog_list_description', $blog_list_description);
$smarty->assign('blog_list_created', $blog_list_created);
$smarty->assign('blog_list_lastmodif', $blog_list_lastmodif);
$smarty->assign('blog_list_user', $blog_list_user);
$smarty->assign('blog_list_posts', $blog_list_posts);
$smarty->assign('blog_list_visits', $blog_list_visits);
$smarty->assign('blog_list_activity', $blog_list_activity);
$smarty->assign('trl', '');

$smarty->assign('userbreadCrumb', $userbreadCrumb);
$smarty->assign('feature_polls', $feature_polls);
$smarty->assign('feature_quizzes', $feature_quizzes);
$smarty->assign('feature_chat', $feature_chat);
$smarty->assign('rss_directories', $rss_directories);
$smarty->assign('rss_articles', $rss_articles);
$smarty->assign('rss_forum', $rss_forum);
$smarty->assign('rss_forums', $rss_forums);
$smarty->assign('rss_blogs', $rss_blogs);
$smarty->assign('rss_image_galleries', $rss_image_galleries);
$smarty->assign('rss_file_galleries', $rss_file_galleries);
$smarty->assign('rss_wiki', $rss_wiki);
$smarty->assign('rss_image_gallery', $rss_image_gallery);
$smarty->assign('rss_file_gallery', $rss_file_gallery);
$smarty->assign('rss_blog', $rss_blog);

$smarty->assign('max_rss_directories', $max_rss_directories);
$smarty->assign('max_rss_articles', $max_rss_articles);
$smarty->assign('max_rss_blogs', $max_rss_blogs);
$smarty->assign('max_rss_image_galleries', $max_rss_image_galleries);
$smarty->assign('max_rss_file_galleries', $max_rss_file_galleries);
$smarty->assign('max_rss_wiki', $max_rss_wiki);
$smarty->assign('max_rss_image_gallery', $max_rss_image_gallery);
$smarty->assign('max_rss_file_gallery', $max_rss_file_gallery);
$smarty->assign('max_rss_blog', $max_rss_blog);

$smarty->assign('metatag_keywords', $metatag_keywords);
$smarty->assign('metatag_description', $metatag_description);
$smarty->assign('metatag_author', $metatag_author);
$smarty->assign('metatag_geoposition', $metatag_geoposition);
$smarty->assign('metatag_georegion', $metatag_georegion);
$smarty->assign('metatag_geoplacename', $metatag_geoplacename);
$smarty->assign('metatag_robots', $metatag_robots);
$smarty->assign('metatag_revisitafter', $metatag_revisitafter);

$smarty->assign("rssfeed_default_version", $tikilib->get_preference("rssfeed_default_version","2"));
$smarty->assign("rssfeed_language", $tikilib->get_preference("rssfeed_language","en-us"));
$smarty->assign("rssfeed_editor", $tikilib->get_preference("rssfeed_editor",""));
$smarty->assign("rssfeed_publisher", $tikilib->get_preference("rssfeed_publisher",""));
$smarty->assign("rssfeed_webmaster", $tikilib->get_preference("rssfeed_webmaster",""));
$smarty->assign("rssfeed_creator", $tikilib->get_preference("rssfeed_creator",""));

$smarty->assign("rssfeed_cssparam", "&amp;css=y");
if ($tikilib->get_preference("rssfeed_css","y") <> "y") {
    $smarty->assign("rssfeed_cssparam", "");
}

$smarty->assign('fgal_use_db', $fgal_use_db);
$smarty->assign('fgal_use_dir', $fgal_use_dir);
$smarty->assign('gal_use_db', $gal_use_db);
$smarty->assign('gal_use_lib', $gal_use_lib);
$smarty->assign('gal_use_dir', $gal_use_dir);
$smarty->assign('fgal_match_regex', $fgal_match_regex);
$smarty->assign('fgal_nmatch_regex', $fgal_nmatch_regex);
$smarty->assign('gal_match_regex', $gal_match_regex);
$smarty->assign('gal_nmatch_regex', $gal_nmatch_regex);

$smarty->assign('feature_left_column', $feature_left_column);
$smarty->assign('feature_right_column', $feature_right_column);
$smarty->assign('feature_top_bar', $feature_top_bar);
$smarty->assign('feature_bot_bar', $feature_bot_bar);

$smarty->assign('feature_file_galleries', $feature_file_galleries);
$smarty->assign('feature_file_galleries_rankings', $feature_file_galleries_rankings);
$smarty->assign('language', $language);
$smarty->assign('lang_use_db', $lang_use_db);
$smarty->assign('tmpDir', $tmpDir);
$smarty->assign('home_blog', $home_blog);
$smarty->assign('home_forum', $home_forum);
$smarty->assign('home_gallery', $home_gallery);
$smarty->assign('home_file_gallery', $home_file_gallery);
$smarty->assign('feature_dynamic_content', $feature_dynamic_content);
$smarty->assign('feature_edit_templates', $feature_edit_templates);
$smarty->assign('feature_top_banner', $feature_top_banner);
$smarty->assign('feature_banners', $feature_banners);
$smarty->assign('feature_xmlrpc', $feature_xmlrpc);
$smarty->assign('feature_drawings', $feature_drawings);
$smarty->assign('layout_section', $layout_section);
$smarty->assign('feature_html_pages', $feature_html_pages);
$smarty->assign('feature_search_stats', $feature_search_stats);
$smarty->assign('feature_referer_stats', $feature_referer_stats);
$smarty->assign('feature_smileys', $feature_smileys);
$smarty->assign('feature_comm', $feature_comm);
$smarty->assign('feature_cms_rankings', $feature_cms_rankings);
$smarty->assign('feature_blog_rankings', $feature_blog_rankings);
$smarty->assign('feature_gal_rankings', $feature_gal_rankings);
$smarty->assign('feature_wiki_rankings', $feature_wiki_rankings);
$smarty->assign('feature_wiki_undo', $feature_wiki_undo);
$smarty->assign('feature_wiki_icache', $feature_wiki_icache);
$smarty->assign('feature_menusfolderstyle', $feature_menusfolderstyle);
$smarty->assign('feature_calendar', $feature_calendar);
$smarty->assign('feature_editcss', $feature_editcss);
$smarty->assign('feature_wiki_monosp', $feature_wiki_monosp);
$smarty->assign('wiki_feature_copyrights', $wiki_feature_copyrights);

$smarty->assign('feature_wiki_templates', $feature_wiki_templates);
$smarty->assign('feature_cms_templates', $feature_cms_templates);

$smarty->assign('feature_forum_rankings', $feature_forum_rankings);
$smarty->assign('feature_forum_parse', $feature_forum_parse);
$smarty->assign('feature_forum_quickjump', $feature_forum_quickjump);
$smarty->assign('feature_forum_topicd', $feature_forum_topicd);
$smarty->assign('feature_hotwords', $feature_hotwords);
$smarty->assign('feature_hotwords_nw', $feature_hotwords_nw);
$smarty->assign('feature_autolinks', $feature_autolinks);
$smarty->assign('feature_lastChanges', $feature_lastChanges);
$smarty->assign('feature_dump', $feature_dump);
$smarty->assign('feature_categories', $feature_categories);
$smarty->assign('feature_categorypath', $feature_categorypath);
$smarty->assign('feature_categoryobjects', $feature_categoryobjects);
$smarty->assign('feature_faqs', $feature_faqs);
$smarty->assign('feature_shoutbox', $feature_shoutbox);
$smarty->assign('shoutbox_autolink', $shoutbox_autolink);
$smarty->assign('feature_stats', $feature_stats);
$smarty->assign('feature_games', $feature_games);
$smarty->assign('user_assigned_modules', $user_assigned_modules);
$smarty->assign('feature_user_bookmarks', $feature_user_bookmarks);
$smarty->assign('feature_ranking', $feature_ranking);
$smarty->assign('feature_listPages', $feature_listPages);
$smarty->assign('feature_history', $feature_history);
$smarty->assign('feature_backlinks', $feature_backlinks);
$smarty->assign('feature_likePages', $feature_likePages);
$smarty->assign('feature_search', $feature_search);
$smarty->assign('feature_search_fulltext', $feature_search_fulltext);
$smarty->assign('feature_sandbox', $feature_sandbox);
$smarty->assign('feature_userPreferences', $feature_userPreferences);
$smarty->assign('feature_userVersions', $feature_userVersions);
$smarty->assign('feature_galleries', $feature_galleries);
$smarty->assign('feature_featuredLinks', $feature_featuredLinks);
$smarty->assign('feature_wiki', $feature_wiki);
$smarty->assign('feature_articles', $feature_articles);
$smarty->assign('feature_submissions', $feature_submissions);
$smarty->assign('feature_blogs', $feature_blogs);

$smarty->assign('feature_maps',$feature_maps);
$map_path = $tikilib->get_preference("map_path",'');
$default_map = $tikilib->get_preference("default_map",'');
$map_help = $tikilib->get_preference("map_help",'MapsHelp');
$map_comments = $tikilib->get_preference("map_comments",'MapsComments');
$gdaltindex = $tikilib->get_preference("gdaltindex",'');

$smarty->assign('feature_wiki_comments', $feature_wiki_comments);
$smarty->assign('wiki_comments_default_ordering', $wiki_comments_default_ordering);
$smarty->assign('wiki_comments_per_page', $wiki_comments_per_page);

$smarty->assign('feature_faq_comments', $feature_faq_comments);
$smarty->assign('faq_comments_default_ordering', $faq_comments_default_ordering);
$smarty->assign('faq_comments_per_page', $faq_comments_per_page);

$smarty->assign('feature_forums', $feature_forums);
$smarty->assign('forums_ordering', $forums_ordering);
$smarty->assign('forums_comments_per_page', $forums_comments_per_page);

$smarty->assign('feature_image_galleries_comments', $feature_image_galleries_comments);
$smarty->assign('image_galleries_comments_default_order', $image_galleries_comments_default_order);
$smarty->assign('image_galleries_comments_per_page', $image_galleries_comments_per_page);

$smarty->assign('feature_file_galleries_comments', $feature_file_galleries_comments);
$smarty->assign('file_galleries_comments_default_ordering', $file_galleries_comments_default_ordering);
$smarty->assign('file_galleries_comments_per_page', $file_galleries_comments_per_page);

$smarty->assign('feature_poll_comments', $feature_poll_comments);
$smarty->assign('poll_comments_default_ordering', $poll_comments_default_ordering);
$smarty->assign('poll_comments_per_page', $poll_comments_per_page);

$smarty->assign('feature_blog_comments', $feature_blog_comments);
$smarty->assign('blog_comments_default_ordering', $blog_comments_default_ordering);
$smarty->assign('blog_comments_per_page', $blog_comments_per_page);

$smarty->assign('feature_article_comments', $feature_article_comments);
$smarty->assign('article_comments_default_ordering', $article_comments_default_ordering);
$smarty->assign('article_comments_per_page', $article_comments_per_page);

$smarty->assign('feature_warn_on_edit', $feature_warn_on_edit);
$smarty->assign('warn_on_edit_time', $warn_on_edit_time);

$wiki_feature_3d = 'n';
$wiki_3d_width = 500;
$wiki_3d_height = 500;
$wiki_3d_navigation_depth = 1;
$wiki_3d_feed_animation_interval = 500;
$wiki_3d_existing_page_color = "#00CC55";
$wiki_3d_missing_page_color = "#FF5555";

$smarty->assign('wiki_feature_3d', $wiki_feature_3d);
$smarty->assign('wiki_3d_width', $wiki_3d_width);
$smarty->assign('wiki_3d_height', $wiki_3d_height);
$smarty->assign('wiki_3d_navigation_depth', $wiki_3d_navigation_depth);
$smarty->assign('wiki_3d_feed_animation_interval', $wiki_3d_feed_animation_interval);
$smarty->assign('wiki_3d_existing_page_color', $wiki_3d_existing_page_color);
$smarty->assign('wiki_3d_missing_page_color', $wiki_3d_missing_page_color);

// Other preferences
$popupLinks = $tikilib->get_preference("popupLinks", 'n');
$anonCanEdit = $tikilib->get_preference("anonCanEdit", 'n');
$modallgroups = $tikilib->get_preference("modallgroups", 'y');
$modseparateanon = $tikilib->get_preference("modseparateanon", 'n');
$change_language = $tikilib->get_preference("change_language", 'y');
$change_theme = $tikilib->get_preference("change_theme", 'y');
//$tikiIndex = $tikilib->get_preference("tikiIndex", 'tiki-index.php');
$cachepages = $tikilib->get_preference("cachepages", 'y');
$cacheimages = $tikilib->get_preference("cacheimages", 'y');
$allowRegister = $tikilib->get_preference("allowRegister", 'n');
$eponymousGroups = $tikilib->get_preference("eponymousGroups", 'n');
$useRegisterPasscode = $tikilib->get_preference("useRegisterPasscode", 'n');
$registerPasscode = $tikilib->get_preference("registerPasscode", '');
$useUrlIndex = $tikilib->get_preference("useUrlIndex", 'n');
$urlIndex = $tikilib->get_preference("urlIndex", '');
$use_proxy = $tikilib->get_preference("use_proxy", 'n');
$proxy_host = $tikilib->get_preference("proxy_host", '');
$proxy_port = $tikilib->get_preference("proxy_port", '');
$session_db = $tikilib->get_preference("session_db", 'n');
$session_lifetime = $tikilib->get_preference("session_lifetime", 0);
$wikiHomePage = $tikilib->get_preference("wikiHomePage", 'HomePage');
$smarty->assign('wikiHomePage', $wikiHomePage);

$wiki_page_regex = $tikilib->get_preference('wiki_page_regex', 'strict');
$smarty->assign('wiki_page_regex', $wiki_page_regex);

// Please DO NOT modify any of the brackets in the regex(s).
// It may seem redundent but, really, they are ALL REQUIRED.
if ($wiki_page_regex == 'strict') {
    $page_regex = '([A-Za-z0-9_])([\.: A-Za-z0-9_\-])*([A-Za-z0-9_])';
} elseif ($wiki_page_regex == 'full') {
    $page_regex = '([A-Za-z0-9_]|[\x80-\xFF])([\.: A-Za-z0-9_\-]|[\x80-\xFF])*([A-Za-z0-9_]|[\x80-\xFF])';
} else {
    // This is just evil.  The middle section means "anything, as long
    // as it's not a | and isn't followed by ))".  -rlpowell
    $page_regex = '([^|\(\)])([^|](?!\)\)))*?([^|\(\)])';
}

// PEAR::Auth support
$auth_method = "tiki";
$smarty->assign('auth_method', $auth_method);
$auth_pear = "tiki";
$smarty->assign('auth_pear', $auth_pear);
$auth_create_user_tiki = "n";
$smarty->assign('auth_create_user_tiki', $auth_create_user_tiki);
$auth_create_user_auth = "n";
$smarty->assign('auth_create_user_auth', $auth_create_user_auth);
$auth_skip_admin = "y";
$smarty->assign('auth_skip_admin', $auth_skip_admin);
$auth_ldap_host = "localhost";
$smarty->assign('auth_ldap_host', $auth_ldap_host);
$auth_ldap_port = "389";
$smarty->assign('auth_ldap_port', $auth_ldap_port);
$auth_ldap_scope = "sub";
$smarty->assign('auth_ldap_scope', $auth_ldap_scope);
$auth_ldap_basedn = "";
$smarty->assign('auth_ldap_basedn', $auth_ldap_basedn);
$auth_ldap_userdn = "";
$smarty->assign('auth_ldap_userdn', $auth_ldap_userdn);
$auth_ldap_userattr = "uid";
$smarty->assign('auth_ldap_userattr', $auth_ldap_userattr);
$auth_ldap_useroc = "inetOrgPerson";
$smarty->assign('auth_ldap_useroc', $auth_ldap_useroc);
$auth_ldap_groupdn = "";
$smarty->assign('auth_ldap_groupdn', $auth_ldap_groupdn);
$auth_ldap_groupattr = "cn";
$smarty->assign('auth_ldap_groupattr', $auth_ldap_groupattr);
$auth_ldap_groupoc = "groupOfUniqueNames";
$smarty->assign('auth_ldap_groupoc', $auth_ldap_groupoc);
$auth_ldap_memberattr = "uniqueMember";
$smarty->assign('auth_ldap_memberattr', $auth_ldap_memberattr);
$auth_ldap_memberisdn = "y";
$smarty->assign('auth_ldap_memberisdn', $auth_ldap_memberisdn);
$auth_ldap_adminuser = "";
$smarty->assign('auth_ldap_adminuser', $auth_ldap_adminuser);
$auth_ldap_adminpass = "";
$smarty->assign('auth_ldap_adminpass', $auth_ldap_adminpass);

$validateUsers = $tikilib->get_preference("validateUsers", 'n');
$forgotPass = $tikilib->get_preference("forgotPass", 'n');
$title = $tikilib->get_preference("title", "");
$maxRecords = $tikilib->get_preference("maxRecords", 10);
$maxArticles = $tikilib->get_preference("maxArticles", 10);

$smarty->assign('useUrlIndex', $useUrlIndex);
$smarty->assign('urlIndex', $urlIndex);
$smarty->assign('use_proxy', $use_proxy);
$smarty->assign('proxy_host', $proxy_host);
$smarty->assign('proxy_port', $proxy_port);
$smarty->assign('registerPasscode', $registerPasscode);
$smarty->assign('useRegisterPasscode', $useRegisterPasscode);

//$smarty->assign('tikiIndex', $tikiIndex);
$smarty->assign('maxArticles', $maxArticles);
$smarty->assign('popupLinks', $popupLinks);
$smarty->assign('modallgroups', $modallgroups);
$smarty->assign('modseparateanon', $modseparateanon);
$smarty->assign('change_theme', $change_theme);
$smarty->assign('change_language', $change_language);
$smarty->assign('anonCanEdit', $anonCanEdit);
$smarty->assign('allowRegister', $allowRegister);
$smarty->assign('eponymousGroups', $eponymousGroups);
$smarty->assign('cachepages', $cachepages);
$smarty->assign('cacheimages', $cacheimages);

$smarty->assign('wiki_extras', 'n');

$feature_server_name = $tikilib->get_preference('feature_server_name', $_SERVER["SERVER_NAME"]);

//print($_SERVER["REQUEST_URI"]);
$smarty->assign('feature_server_name', $feature_server_name);
$_SERVER["SERVER_NAME"] = $feature_server_name;

// Fix IIS servers not setting what they should set (ay ay IIS, ay ay)
if (!isset($_SERVER['QUERY_STRING']))
    $_SERVER['QUERY_STRING'] = '';

if (!isset($_SERVER['REQUEST_URI']) || empty($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] . '/' . $_SERVER['QUERY_STRING'];
}

if (!isset($feature_bidi)) {
    $feature_bidi = 'n';
}

$smarty->assign('feature_bidi', $feature_bidi);

/* # not implemented
$http_basic_auth = $tikilib->get_preference('http_basic_auth', '/');
$smarty->assign('http_basic_auth',$http_basic_auth);
*/
$https_login = $tikilib->get_preference('https_login', 'n');
$smarty->assign('https_login', $https_login);
$https_login_required = $tikilib->get_preference('https_login_required', 'n');
$smarty->assign('https_login_required', $https_login_required);

$https_mode = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';

if ($https_mode) {
    $http_port = 80;

    $https_port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 443;
} else {
    $http_port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80;

    $https_port = 443;
}

$http_domain = $tikilib->get_preference('http_domain', '');
$smarty->assign('http_domain', $http_domain);
$http_port = $tikilib->get_preference('http_port', $http_port);
$smarty->assign('http_port', $http_port);
$http_prefix = $tikilib->get_preference('http_prefix', '/');
$smarty->assign('http_prefix', $http_prefix);

$https_domain = $tikilib->get_preference('https_domain', '');
$smarty->assign('https_domain', $https_domain);
$https_port = $tikilib->get_preference('https_port', $https_port);
$smarty->assign('https_port', $https_port);
$https_prefix = $tikilib->get_preference('https_prefix', '/');
$smarty->assign('https_prefix', $https_prefix);

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
        # only show "Stay in SSL checkbox if we're not already in HTTPS mode"
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
        $smarty->assign('http_login_url', $http_login_url);

        $smarty->assign('https_login_url', $https_login_url);
        # only show "Stay in SSL checkbox if we're not already in HTTPS mode"
        $show_stay_in_ssl_mode = $https_mode ? 'y' : 'n';
    }

    $smarty->assign('show_stay_in_ssl_mode', $show_stay_in_ssl_mode);
    $smarty->assign('stay_in_ssl_mode', $stay_in_ssl_mode);
}

// removal of preferences.php file suggested and patched by Lee Essen
/* 
if (!file_exists("templates_c/" . $tikidomain . "preferences.php")) {
    $prefs = $tikilib->get_all_preferences();

    $fw = @fopen("templates_c/$tikidomain/preferences.php", "wb");

    if (!$fw) {
        if (isset($php_errormsg)) {
            die ($php_errormsg);
        }

        fopen("templates_c/" . $tikidomain . "preferences.php", "wb");
        die;
    }

    fwrite($fw, '<?php' . "\n");

    foreach ($prefs as $name => $val) {
        $$name = $val;

        fwrite($fw, '$' . $name . "=\"" . $val . "\";");
        fwrite($fw, '$smarty->assign("' . $name . '","' . '$' . $name . '");');
        fwrite($fw, "\n");
        $smarty->assign("$name", $val);
    }

    fwrite($fw, '?>');
    fclose ($fw);
} else {
    include_once ("templates_c/" . $tikidomain . "preferences.php");
}
*/
// end of preferences.php removal
// start of replacement : get all prefs from db once
$prefs = $tikilib->get_all_preferences();
foreach ($prefs as $name => $val) {
	$$name = $val;
	$smarty->assign("$name", $val);
}

//after prefs update, must check if mailin_autocheck time is elapsed
if($feature_mailin == 'y' && $mailin_autocheck == 'y')
{
  if((time() - $mailin_autocheckLast)/60 > $mailin_autocheckFreq){
    $tikilib->set_preference("mailin_autocheckLast", time());
    include_once("tiki-mailin-code.php");
  }
}

if ($feature_detect_language == "y") {
    $browser_language = detect_browser_language();
    if (!empty($browser_language)) {
	$language = $browser_language;
	$smarty->assign('language', $language);
    }
}

$useGroupHome = $tikilib->get_preference("useGroupHome",'n');
$tikiIndex = $tikilib->get_preference("tikiIndex",'tiki-index.php');
$group = '';

if (isset($user)) {
	$group = $userlib->get_user_default_group($user);
	if($useGroupHome == 'y') {
    $groupHome = $userlib->get_group_home($group);
    if ($groupHome) {
        $tikiIndex = strpos($groupHome,'http://')===0 ? $groupHome : "tiki-index.php?page=".$groupHome;
		}
	}
}
$smarty->assign('tikiIndex',$tikiIndex);

$user_dbl = 'y';

if ($feature_userPreferences == 'y') {
    // Check for FEATURES for the user
    $user_style = $site_style = $tikilib->get_preference("style", 'moreneat.css');

    if ($user) {
        $user_dbl = $tikilib->get_user_preference($user, 'user_dbl', 'y');

        if ($change_theme == 'y') {
            $user_style = $tikilib->get_user_preference($user, 'theme', $style);

            if ($user_style) {
									$style = $user_style;
            }
        }

        if ($change_language == 'y') {
            $user_language = $tikilib->get_user_preference($user, 'language', $language);

            if ($user_language) {
                $language = $user_language;
            }
        }
    }
    $smarty->assign('language', $language);
}
if (!isset($user) && isset($_SESSION['language']) ) { // users not logged that change the preference
	$language = $_SESSION['language'];
	$smarty->assign('language', $language);
}

$stlstl = split("-|\.", $style);
$style_base = $stlstl[0];

if ($tikidomain and is_file("styles/$tikidomain/$style")) {
	$style = "$tikidomain/$style";
}
$smarty->assign('style', $style);

$feature_babelfish = $tikilib->get_preference('feature_babelfish', 'y');
$feature_babelfish_logo = $tikilib->get_preference('feature_babelfish_logo', 'n');

 /* \todo if this page is not viewable by anonymous, then don't display the babelfish stuff */
if (0) {
    $feature_babelfish = 'n';
    $feature_babelfish_logo = 'n';
}

if ($feature_babelfish == 'y') {
    require_once('lib/Babelfish.php');
    $smarty->assign_by_ref('babelfish_links', Babelfish::links($language));
} else {
    $smarty->assign('babelfish_links', '');
}

if ($feature_babelfish_logo == 'y') {
    require_once('lib/Babelfish.php');
    $smarty->assign('babelfish_logo', Babelfish::logo($language));
} else {
    $smarty->assign('babelfish_logo', '');
}

$smarty->assign('user_dbl', $user_dbl);

$smarty->assign('user', $user);
$smarty->assign('group', $group);
$smarty->assign('lock', false);
$smarty->assign('title', $title);
$smarty->assign('maxRecords', $maxRecords);

// If we are processing a login then do not generate the challenge
// if we are in any other case then yes.
if (!strstr($_SERVER["REQUEST_URI"], 'tiki-login')) {
    if ($feature_challenge == 'y') {
        $chall = $userlib->generate_challenge();

        $_SESSION["challenge"] = $chall;
        $smarty->assign('challenge', $chall);
    }
}

$smarty->assign('mnu_dirmenu', 'display:none;');

if (isset($_COOKIE["dirmenu"])) {
    if ($_COOKIE["dirmenu"] == 'o') {
        $smarty->assign('mnu_dirmenu', 'display:block;');
    }
}

$smarty->assign('mnu_nlmenu', 'display:none;');

if (isset($_COOKIE["nlmenu"])) {
    if ($_COOKIE["nlmenu"] == 'o') {
        $smarty->assign('mnu_nlmenu', 'display:block;');
    }
}

$smarty->assign('mnu_chartmenu', 'display:none;');

if (isset($_COOKIE["chartmenu"])) {
    if ($_COOKIE["chartmenu"] == 'o') {
        $smarty->assign('mnu_chartmenu', 'display:block;');
    }
}

$smarty->assign('mnu_ephmenu', 'display:none;');

if (isset($_COOKIE["ephmenu"])) {
    if ($_COOKIE["ephmenu"] == 'o') {
        $smarty->assign('mnu_ephmenu', 'display:block;');
    }
}

$smarty->assign('mnu_mymenu', 'display:none;');

if (isset($_COOKIE["mymenu"])) {
    if ($_COOKIE["mymenu"] == 'o') {
        $smarty->assign('mnu_mymenu', 'display:block;');
    }
}

$smarty->assign('mnu_workflow', 'display:none;');

if (isset($_COOKIE["wfmenu"])) {
    if ($_COOKIE["wfmenu"] == 'o') {
        $smarty->assign('mnu_workflow', 'display:block;');
    }
}

$smarty->assign('mnu_usrmenu', 'display:none;');

if (isset($_COOKIE["usrmenu"])) {
    if ($_COOKIE["usrmenu"] == 'o') {
        $smarty->assign('mnu_usrmenu', 'display:block;');
    }
}

$smarty->assign('mnu_wikimenu', 'display:none;');

if (isset($_COOKIE["wikimenu"])) {
    if ($_COOKIE["wikimenu"] == 'o') {
        $smarty->assign('mnu_wikimenu', 'display:block;');
    }
}

$smarty->assign('mnu_homeworkmenu', 'display:none;');

if (isset($_COOKIE["homeworkmenu"])) {
    if ($_COOKIE["homeworkmenu"] == 'o') {
        $smarty->assign('mnu_homeworkmenu', 'display:block;');
    }
}

$smarty->assign('mnu_srvmenu', 'display:none;');

if (isset($_COOKIE["srvmenu"])) {
    if ($_COOKIE["srvmenu"] == 'o') {
        $smarty->assign('mnu_srvmenu', 'display:block;');
    }
}

$smarty->assign('mnu_trkmenu', 'display:none;');

if (isset($_COOKIE["trkmenu"])) {
    if ($_COOKIE["trkmenu"] == 'o') {
        $smarty->assign('mnu_trkmenu', 'display:block;');
    }
}

// Jukebox menu added 18-jan-04 damian aka damosoft
$smarty->assign('mnu_jukeboxmenu', 'display:none;');

if (isset($_COOKIE["jukeboxmenu"])) {
    if ($_COOKIE["jukeboxmenu"] == 'o') {
        $smarty->assign('mnu_jukeboxmenu', 'display:block;');
    }
}

$smarty->assign('mnu_quizmenu', 'display:none;');

if (isset($_COOKIE["quizmenu"])) {
    if ($_COOKIE["quizmenu"] == 'o') {
        $smarty->assign('mnu_quizmenu', 'display:block;');
    }
}

$smarty->assign('mnu_formenu', 'display:none;');

if (isset($_COOKIE["formenu"])) {
    if ($_COOKIE["formenu"] == 'o') {
        $smarty->assign('mnu_formenu', 'display:block;');
    }
}

$smarty->assign('mnu_admmnu', 'display:none;');

if (isset($_COOKIE["admmnu"])) {
    if ($_COOKIE["admmnu"] == 'o') {
        $smarty->assign('mnu_admmnu', 'display:block;');
    }
}

$smarty->assign('mnu_faqsmenu', 'display:none;');

if (isset($_COOKIE["faqsmenu"])) {
    if ($_COOKIE["faqsmenu"] == 'o') {
        $smarty->assign('mnu_faqsmenu', 'display:block;');
    }
}

$smarty->assign('mnu_galmenu', 'display:none;');

if (isset($_COOKIE["galmenu"])) {
    if ($_COOKIE["galmenu"] == 'o') {
        $smarty->assign('mnu_galmenu', 'display:block;');
    }
}

$smarty->assign('mnu_cmsmenu', 'display:none;');

if (isset($_COOKIE["cmsmenu"])) {
    if ($_COOKIE["cmsmenu"] == 'o') {
        $smarty->assign('mnu_cmsmenu', 'display:block;');
    }
}

$smarty->assign('mnu_blogmenu', 'display:none;');

if (isset($_COOKIE["blogmenu"])) {
    if ($_COOKIE["blogmenu"] == 'o') {
        $smarty->assign('mnu_blogmenu', 'display:block;');
    }
}

$smarty->assign('mnu_filegalmenu', 'display:none;');

if (isset($_COOKIE["filegalmenu"])) {
    if ($_COOKIE["filegalmenu"] == 'o') {
        $smarty->assign('mnu_filegalmenu', 'display:block;');
    }
}

$smarty->assign('mnu_mapsmenu','display:none;');
if(isset($_COOKIE["mapsmenu"])) {
  if($_COOKIE["mapsmenu"]=='o') {
    $smarty->assign('mnu_mapsmenu','display:block;');
  }
}

$smarty->assign('mnu_layermenu','display:none;');
if(isset($_COOKIE["layermenu"])) {
  if($_COOKIE["layermenu"]=='o') {
      $smarty->assign('mnu_layermenu','display:block;');
  }
}

if ($user && $feature_usermenu == 'y') {
    if (!isset($_SESSION['usermenu'])) {
        include_once ('lib/usermenu/usermenulib.php');

        $user_menus = $usermenulib->list_usermenus($user, 0, -1, 'position_asc', '');
        $smarty->assign('usr_user_menus', $user_menus['data']);
        $_SESSION['usermenu'] = $user_menus['data'];
    } else {
        $user_menus = $_SESSION['usermenu'];

        $smarty->assign('usr_user_menus', $user_menus);
    }
}

include_once ("tiki-modules.php");
$smarty->assign('beingEdited', 'n');

if ($feature_warn_on_edit == 'y') {
    // Check if the page is being edited
    if (isset($_REQUEST['page'])) {
        $chkpage = $_REQUEST['page'];
    } else {
        $chkpage = $wikiHomePage;
    }

    // Notice if a page is being edited or if it was being edited and not anymore
    //print($GLOBALS["HTTP_REFERER"]);
    // IF isset the referer and if the referer is editpage then unset taking the pagename from the
    // query or homepage if not query
    if (isset($_SERVER['HTTP_REFERER'])) {
        if (strstr($_SERVER['HTTP_REFERER'], 'tiki-editpage')) {
            $purl = parse_url($_SERVER['HTTP_REFERER']);

            if (!isset($purl["query"])) {
                $purl["query"] = '';
            }

            parse_str($purl["query"], $purlquery);

            if (!isset($purlquery["page"])) {
                $purlquery["page"] = $wikiHomePage;
            }

            if (isset($_SESSION["edit_lock"])) {
                $tikilib->semaphore_unset($purlquery["page"], $_SESSION["edit_lock"]);
            }
        }
    }

    if (strstr($_SERVER['REQUEST_URI'], 'tiki-editpage')) {
        $purl = parse_url($_SERVER['REQUEST_URI']);

        if (!isset($purl["query"])) {
            $purl["query"] = '';
        }

        parse_str($purl["query"], $purlquery);

        if (!isset($purlquery["page"])) {
            $purlquery["page"] = $wikiHomePage;
        }

        //When tiki-editpage.php is loading, check to see if there is an editing conflict
        if ($tikilib->semaphore_is_set($chkpage, $warn_on_edit_time * 60) && $tikilib->get_semaphore_user($chkpage) != $user) {
            $smarty->assign('editpageconflict', 'y');

            $editpageconflict = 'y';
        } else {
            if (!(isset($_REQUEST['save']))) { // Don't editlock $wikiHomePage when saving any wiki page
                $_SESSION["edit_lock"] = $tikilib->semaphore_set($purlquery["page"]);

                $smarty->assign('editpageconflict', 'n');
                $editpageconflict = 'n';
            }
        }
    }

    if ($tikilib->semaphore_is_set($chkpage, $warn_on_edit_time * 60)) {
        $smarty->assign('semUser', $tikilib->get_semaphore_user($chkpage));

        $smarty->assign('beingEdited', 'y');
        $beingedited = 'y';
    } else {
        $smarty->assign('beingEdited', 'n');

        $beingedited = 'n';
    }
}

if (isset($_REQUEST["pollVote"])) {
    if ($tiki_p_vote_poll == 'y' && isset($_REQUEST["polls_optionId"])) {
	if( $feature_poll_anonymous == 'y' || $user )
	{
	    $tikilib->poll_vote($user, $_REQUEST["polls_pollId"], $_REQUEST["polls_optionId"]);
	    // Poll vote must go first, or the new vote will be seen as the previous one.
	    $tikilib->register_user_vote($user, 'poll' . $_REQUEST["polls_pollId"], $_REQUEST["polls_optionId"]);
	}
    }

    $pollId = $_REQUEST["polls_pollId"];
    header ("location: tiki-poll_results.php?pollId=$pollId");
}

$ownurl = httpPrefix(). $_SERVER["REQUEST_URI"];
$parsed = parse_url($_SERVER["REQUEST_URI"]);

if (!isset($parsed["query"])) {
    $parsed["query"] = '';
}

parse_str($parsed["query"], $query);
$father = httpPrefix(). $parsed["path"];

if (count($query) > 0) {
    $first = 1;

    foreach ($query as $name => $val) {
	if ($first) {
	    $first = false;

	    $father .= '?' . $name . '=' . $val;
	} else {
	    $father .= '&amp;' . $name . '=' . $val;
	}
    }

    $father .= '&amp;';
} else {
    $father .= '?';
}


$ownurl_father = $father;
$smarty->assign('ownurl', httpPrefix(). $_SERVER["REQUEST_URI"]);

// load lib configs
/*
if ($libdir = opendir('lib')) {
	while (FALSE !== ($libname = readdir($libdir))) {
		$configIncFile = 'lib/'.$libname.'/setup_inc.php';
		if (is_dir( 'lib/'.$libname ) && file_exists( $configIncFile )) {
			include_once( $configIncFile );
		}
	}
}
*/
$allowMsgs = 'n';

if ($user) {
    $allowMsgs = $tikilib->get_user_preference($user, 'allowMsgs', 'y');

    $tasks_useDates = $tikilib->get_user_preference($user, 'tasks_useDates');
    $tasks_maxRecords = $tikilib->get_user_preference($user, 'tasks_maxRecords');
    $smarty->assign('tasks_useDates', $tasks_useDates);
    $smarty->assign('tasks_maxRecords', $tasks_maxRecords);
    $smarty->assign('allowMsgs', $allowMsgs);
}

if ($feature_live_support == 'y') {
    $smarty->assign('user_is_operator', 'n');

    if ($user) {
        include_once ('lib/live_support/lsadminlib.php');

        if ($lsadminlib->is_operator($user)) {
            $smarty->assign('user_is_operator', 'y');
        }
    }
}

if ($feature_referer_stats == 'y') {
    // Referer tracking
    if (isset($_SERVER['HTTP_REFERER'])) {
        $pref = parse_url($_SERVER['HTTP_REFERER']);

        if (!strstr($_SERVER["SERVER_NAME"], $pref["host"])) {
            $tikilib->register_referer($pref["host"]);
        }
    }
}

//Check for an update of dynamic vars
if(isset($tiki_p_edit_dynvar) && $tiki_p_edit_dynvar == 'y') {
    if(isset($_REQUEST['_dyn_update'])) { echo "****";
        foreach($_REQUEST as $name => $value) {
            if(substr($name,0,4)=='dyn_' and $name!='_dyn_update') {
                $tikilib->update_dynamic_variable(substr($name,4),$_REQUEST[$name]);
            }
        }
    }
}


// Stats
if ($feature_stats == 'y') {
    if ($count_admin_pvs == 'y' || $user != 'admin') {
        if (!strstr($_SERVER["REQUEST_URI"], 'chat')) {
            $tikilib->add_pageview();
        }
    }
}

$smarty->assign('uses_tabs', 'n');
$smarty->assign('uses_jscalendar', 'n');
$smarty->assign('uses_phplayers', 'n');

$user_preferences = array();

//print("tiki-setup: before include tiki-handlers.php:".$tiki_timer->elapsed()."<br />");
//tiki-handlers.php is empty right now.  uncomment the line below if you need to use it
//include_once ('tiki-handlers.php');

// no compression at all
$smarty->assign('gzip','Disabled');
$smarty->assign('gzip_handler','none');
// php compression enabled?
if (ini_get('zlib.output_compression') == 1) {
    $smarty->assign('gzip','Enabled');
    $smarty->assign('gzip_handler','php');
// if not, check if tiki compression is enabled
} elseif ($feature_obzip == 'y') {
	// tiki compression is enabled, then let activate the handler
	if ($feature_obzip == 'y') {
	    ob_start ("ob_gzhandler");
			$smarty->assign('gzip_handler','tiki');
    	$smarty->assign('gzip','Enabled');
	}
}

//print("tiki-setup: before include debugger.php:".$tiki_timer->elapsed()."<br />");
/* Include debugger class declaration. So use loggin facility in
 * php files become much easier :)
 */
if ($feature_debug_console == 'y') {
    include_once ('lib/debug/debugger.php');
}
//print("tiki-setup: after include debugger.php:".$tiki_timer->elapsed()."<br />");

$smarty->assign_by_ref('num_queries',$num_queries);

/*
 * Check location for Tiki Integrator script and setup aux CSS file if needed by repository
 */
if ($feature_integrator == 'y')
{
    include_once('lib/integrator/integrator.php');
    if ((strpos($_SERVER['REQUEST_URI'], 'tiki-integrator.php') != 0) && isset($_REQUEST['repID']))
    {
        // Create instance of integrator
        $integrator = new TikiIntegrator($dbTiki);
        $integrator_css_file = $integrator->get_rep_css($_REQUEST['repID']);
        $smarty->assign('integrator_css_file', $integrator_css_file);
    }
}

/*
 * Register the search refresh function
 */

if ($feature_search == 'y') {
  include_once("lib/search/refresh.php");
  register_shutdown_function("refresh_search_index");
}

?>
