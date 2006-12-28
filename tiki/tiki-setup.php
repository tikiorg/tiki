<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-setup.php,v 1.376 2006-12-28 09:55:16 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//xdebug_start_profiling();

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

// see http://tikiwiki.org/tiki-index.php?page=CharacterEncodingTrouble
//header('Content-Type: text/html; charset=utf-8');

$phpErrors = array();
// include_once("lib/init/setup_inc.php");
include_once('lib/init/initlib.php');

class TikiSetup extends TikiInit {

    /*!
        Check that everything is set up properly

        \static
    */
    function check($tikidomain='') {
        static $checked;

        if ($checked) {
            return;
        }

        $checked = true;

        $errors = '';
        
        if (strpos($_SERVER['SERVER_SOFTWARE'],'IIS')==TRUE){
		if (array_key_exists('PATH_TRANSLATED', $_SERVER)) {
        	$docroot = dirname($_SERVER['PATH_TRANSLATED']);
		} else {
			$docroot = getcwd();
		}
        }
        else{
        	$docroot = getcwd();
        }

        if (ini_get('session.save_handler') == 'files') {
            $save_path = ini_get('session.save_path');
            // check if we can check it. The session.save_path can be outside
	    // the open_basedir paths.
	    $open_basedir=ini_get('open_basedir');
	    if (empty($open_basedir)) {
                if (!is_dir($save_path)) {
                    $errors .= "The directory '$save_path' does not exist or PHP is not allowed to access it (check open_basedir entry in php.ini).\n";
                } else if (!is_writeable($save_path)) {
                    $errors .= "The directory '$save_path' is not writeable.\n";
                }
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
            if (!is_dir("$docroot/$dir/$tikidomain")) {
                $errors .= "The directory '$docroot/$dir/$tikidomain' does not exist.\n";
            } else if (!is_writeable("$docroot/$dir/$tikidomain")) {
                $errors .= "The directory '$docroot/$dir/$tikidomain' is not writeable by $wwwuser.\n";
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
						if ($tikidomain) {
							$install_link = '?multi='.urlencode($tikidomain);
						}
            if (!TikiSetup::isWindows()) {
                print "You may either chmod the directories above manually to 777, or run one of the sets of commands below.
<b><a href='tiki-install.php$install_link'>Proceed to the Tiki installer</a></b> after you run the commands below.

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

TikiSetup::prependIncludePath('lib');
TikiSetup::prependIncludePath('lib/pear');

$tmpDir = TikiInit::tempdir();

class timer {
    function parseMicro($micro) {
        list($micro, $sec) = explode(' ', microtime());

        return $sec + $micro;
    }

    function start($timer = 'default', $restart = FALSE) {
        if (isset($this->timer[$timer]) && !$restart) {
            // report error - timer already exists
        }
        $this->timer[$timer] = $this->parseMicro(microtime());
    }

    function stop($timer = 'default') {
        $result = $this->elapsed($timer);
        unset ($this->timer[$timer]);
        return $result;
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
global $elapsed_in_db;
$num_queries=0;
$elapsed_in_db=0.0;

$tikifeedback = array();

$feature_referer_highlight = 'n';

include_once ('tiki-setup_base.php');
TikiSetup::check($tikidomain);
//print("tiki-setup: before rest of tiki-setup:".$tiki_timer->elapsed()."<br />");

include_once ('lib/headerlib.php');
$smarty->assign_by_ref('headerlib',$headerlib);

// patch for Case-sensitivity perm issue
$case_patched = $tikilib->get_preference('case_patched','n');
if ($case_patched == 'n') {
	include_once 'db/case_patch.php';
	$tikilib->set_preference('case_patched','y');
}
// end of patch

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
} else {
	$smarty->assign('server_load','?');
}

// The votes array stores the votes the user has made
if (!isset($_SESSION['votes'])) {
    $votes = array();

    //session_register("votes");
    $_SESSION['votes'] = $votes;
}

$appname = 'tiki';

if (!isset($_SESSION['appname'])) {
	$_SESSION['appname'] = $appname;
}
$smarty->assign('appname', $appname);

if (isset($_REQUEST['PHPSESSID'])) {
    $tikilib->update_session($_REQUEST['PHPSESSID']);
} elseif (function_exists('session_id')) {
    $tikilib->update_session(session_id());
}

/* UPGRADE temporary for wysiwyg prefs. TODO REMOVE from release*/
/* ------------------------------------------------------------- */

$wys = $tikilib->get_preference('feature_wysiwyg','n');
if ($wys == 'no' or $wys == 'optional' or $wys == 'default') {
	$par = $tikilib->get_preference('wiki_wikisyntax_in_html','');
	$def = $tikilib->get_preference('wysiwyg_default','y');
	if ($wys == 'optional') {
		$tikilib->set_preference('feature_wysiwyg','y');
		$tikilib->set_preference('wysiwyg_optional','y');
		if ($def == 'y') {
			$tikilib->set_preference('wysiwyg_default','y');
		}
	} elseif ($wys == 'default') {
		$tikilib->set_preference('feature_wysiwyg','y');
		$tikilib->set_preference('wysiwyg_optional','n');
		$tikilib->set_preference('wysiwyg_default','y');
	} else {
		$tikilib->set_preference('feature_wysiwyg','n');
	}
	if ($par == 'full') {
		$tikilib->set_preference('wysiwyg_wiki_parsed','y');
		$tikilib->set_preference('wysiwyg_wiki_semi_parsed','n');
	} elseif ($par == 'partial') {
		$tikilib->set_preference('wysiwyg_wiki_parsed','y');
		$tikilib->set_preference('wysiwyg_wiki_semi_parsed','y');
	} elseif ($par == 'none') {
		$tikilib->set_preference('wysiwyg_wiki_parsed','n');
		$tikilib->set_preference('wysiwyg_wiki_semi_parsed','n');
	}
}

/* ------------------------------------------------------------- */
/* END of UPGRADE wysiwyg */

# wiki
$sections['wiki page']['feature'] = 'feature_wiki';
$sections['wiki page']['key'] = 'page';
$sections['wiki page']['itemkey'] = '';
$pref['feature_wiki'] = 'y';
$pref['default_wiki_diff_style'] = 'minsidediff';
$pref['feature_backlinks'] = 'y';
$pref['feature_dump'] = 'y';
$pref['feature_history'] = 'y';
$pref['feature_lastChanges'] = 'y';
$pref['feature_likePages'] = 'y';
$pref['feature_listPages'] = 'y';
$pref['feature_page_title'] = 'y';
$pref['feature_sandbox'] = 'y';
$pref['feature_warn_on_edit'] = 'n';
$pref['feature_wiki_1like_redirection'] = 'y';
$pref['feature_wiki_allowhtml'] = 'n';
$pref['feature_wiki_attachments'] = 'n';
$pref['feature_wiki_comments'] = 'n';
$pref['feature_wiki_description'] = 'n';
$pref['feature_wiki_discuss'] = 'n';
$pref['feature_wiki_export'] = 'y';
$pref['feature_wiki_footnotes'] = 'n';
$pref['feature_wiki_icache'] = 'n';
$pref['feature_wiki_import_html'] = 'n';
$pref['feature_wiki_mandatory_category'] = '-1';
$pref['feature_wiki_monosp'] = 'y';
$pref['feature_wiki_multiprint'] = 'n';
$pref['feature_wiki_notepad'] = 'n';
$pref['feature_wiki_open_as_structure'] = 'n';
$pref['feature_wiki_pageid'] = 'n';
$pref['feature_wiki_paragraph_formatting'] = 'n';
$pref['feature_wiki_pdf'] = 'n';
$pref['feature_wiki_pictures'] = 'n';
$pref['feature_wiki_plurals'] = 'y';
$pref['feature_wiki_print'] = 'y';
$pref['feature_wiki_protect_email'] = 'n';
$pref['feature_wiki_rankings'] = 'y';
$pref['feature_wiki_ratings'] = 'n';
$pref['feature_wiki_replace'] = 'n';
$pref['feature_wiki_show_hide_before'] = 'n';
$pref['feature_wiki_tables'] = 'new';
$pref['feature_wiki_templates'] = 'n';
$pref['feature_wiki_undo'] = 'n';
$pref['feature_wiki_userpage'] = 'y';
$pref['feature_wiki_userpage_prefix'] = 'UserPage';
$pref['feature_wiki_usrlock'] = 'n';
$pref['feature_wikiwords'] = 'y';
$pref['feature_wikiwords_usedash'] = 'y';
$pref['mailin_autocheck'] = 'n';
$pref['mailin_autocheckFreq'] = '0';
$pref['mailin_autocheckLast'] = 0;
$pref['warn_on_edit_time'] = 2;
$pref['wikiHomePage'] = 'HomePage';
$pref['wikiLicensePage'] = '';
$pref['wikiSubmitNotice'] = '';
$pref['wiki_authors_style'] = 'classic';
$pref['wiki_bot_bar'] = 'n';
$pref['wiki_cache'] = 0;
$pref['wiki_comments_default_ordering'] = 'points_desc';
$pref['wiki_comments_per_page'] = 10;
$pref['wiki_creator_admin'] = 'n';
$pref['wiki_feature_copyrights'] = 'n';
$pref['wiki_forum_id'] = '';
$pref['wiki_left_column'] = 'y';
$pref['wiki_list_backlinks'] = 'y';
$pref['wiki_list_comment'] = 'y';
$pref['wiki_list_creator'] = 'y';
$pref['wiki_list_hits'] = 'y';
$pref['wiki_list_lastmodif'] = 'y';
$pref['wiki_list_lastver'] = 'y';
$pref['wiki_list_links'] = 'y';
$pref['wiki_list_name'] = 'y';
$pref['wiki_list_name_len'] = '40';
$pref['wiki_list_size'] = 'y';
$pref['wiki_list_status'] = 'y';
$pref['wiki_list_user'] = 'y';
$pref['wiki_list_versions'] = 'y';
$pref['wiki_page_regex'] = 'strict';
$pref['wiki_page_separator'] = '...page...';
$pref['wiki_pagename_strip'] = '';
$pref['wiki_right_column'] = 'y';
$pref['wiki_top_bar'] = 'y';
$pref['wiki_uses_slides'] = 'n';
$pref['wiki_watch_author'] = 'n';
$pref['wiki_watch_comments'] = 'y';
$pref['wiki_watch_editor'] = 'n';
$pref['feature_wiki_history_full'] = 'n';

# wysiwyg
$pref['feature_wysiwyg'] = 'n';
$pref['wysiwyg_optional'] = 'y';
$pref['wysiwyg_default'] = 'y';
$pref['wysiwyg_wiki_parsed'] = 'y';
$pref['wysiwyg_wiki_semi_parsed'] = 'y';
$pref['wysiwyg_toolbar_skin'] = 'default';
$pref['wysiwyg_toolbar'] ="FitWindow,Templates,-,Cut,Copy,Paste,PasteWord,Print,SpellCheck
Undo,Redo,-,Replace,RemoveFormat,-,Image,Table,Rule,SpecialChar,PageBreak,UniversalKey
/
JustifyLeft,JustifyCenter,JustifyRight,JustifyFull,-,OrderedList,UnorderedList,Outdent,Indent
Bold,Italic,Underline,StrikeThrough,-,Subscript,Superscript,-,Link,Unlink,Anchor,-,tikilink
/
Style,FontName,FontSize,-,TextColor,BGColor";

# wiki3d
$pref['wiki_feature_3d'] = 'n';
$pref['wiki_3d_width'] = 500;
$pref['wiki_3d_height'] = 500;
$pref['wiki_3d_navigation_depth'] = 1;
$pref['wiki_3d_feed_animation_interval'] = 500;
$pref['wiki_3d_existing_page_color'] = '#00CC55';
$pref['wiki_3d_missing_page_color'] = '#FF5555';

# blogs
$sections['blogs']['feature'] = 'feature_blogs';
$sections['blogs']['key'] = 'blogId';
$sections['blogs']['itemkey'] = 'postId';
$pref['feature_blogs'] = 'n';
$pref['blog_list_order'] = 'created_desc';
$pref['home_blog'] = 0;
$pref['feature_blog_rankings'] = 'y';
$pref['feature_blog_comments'] = 'n';
$pref['blog_comments_default_ordering'] = 'points_desc';
$pref['blog_comments_per_page'] = 10;
$pref['feature_blogposts_comments'] = 'n';
$pref['blog_list_user'] = 'text';
$pref['blog_list_title'] = 'y';
$pref['blog_list_title_len'] = '35';
$pref['blog_list_description'] = 'y';
$pref['blog_list_created'] = 'y';
$pref['blog_list_lastmodif'] = 'y';
$pref['blog_list_posts'] = 'y';
$pref['blog_list_visits'] = 'y';
$pref['blog_list_activity'] = 'y';
$pref['feature_blog_mandatory_category'] = '-1';
$pref['feature_blog_heading'] = 'y';

# filegals
$sections['file_galleries']['feature'] = 'feature_file_galleries';
$sections['file_galleries']['key'] = 'page';
$sections['file_galleries']['itemkey'] = 'fileId';
$pref['feature_file_galleries'] = 'n';
$pref['home_file_gallery'] = 0;
$pref['fgal_use_db'] = 'y';
$pref['fgal_batch_dir'] = '';
$pref['fgal_match_regex'] = '';
$pref['fgal_nmatch_regex'] = '';
$pref['fgal_use_dir'] = '';
$pref['fgal_podcast_dir'] = 'files';
$pref['feature_file_galleries_comments'] = 'n';
$pref['file_galleries_comments_default_ordering'] = 'points_desc';
$pref['file_galleries_comments_per_page'] = 10;
$pref['feature_file_galleries_batch'] = 'n';
$pref['feature_file_galleries_rankings'] = 'n';
$pref['fgal_list_name'] = 'y';
$pref['fgal_list_description'] = 'y';
$pref['fgal_list_created'] = 'y';
$pref['fgal_list_lastmodif'] = 'y';
$pref['fgal_list_user'] = 'y';
$pref['fgal_list_files'] = 'y';
$pref['fgal_list_hits'] = 'y';
$pref['fgal_enable_auto_indexing'] = 'y';
$pref['fgal_allow_duplicates'] = 'n';
$pref['fgal_list_parent'] = 'n';
$pref['fgal_list_type'] = 'n';
$pref['fgal_sort_mode'] = 'created_desc';
$pref['feature_file_galleries_author'] = 'n';


# imagegals
$sections['galleries']['feature'] = 'feature_galleries';
$sections['galleries']['key'] = 'galleryId';
$sections['galleries']['itemkey'] = 'imageId';
$pref['feature_galleries'] = 'n';
$pref['feature_gal_batch'] = 'n';
$pref['feature_gal_slideshow'] = 'n';
$pref['feature_gal_imgcache'] = 'n';
$pref['home_gallery'] = 0;
$pref['gal_use_db'] = 'y';
$pref['gal_use_lib'] = 'gd';
$pref['gal_match_regex'] = '';
$pref['gal_nmatch_regex'] = '';
$pref['gal_use_dir'] = '';
$pref['gal_batch_dir'] = '';
$pref['feature_gal_rankings'] = 'y';
$pref['feature_image_galleries_comments'] = 'n';
$pref['image_galleries_comments_default_order'] = 'points_desc';
$pref['image_galleries_comments_per_page'] = 10;
$pref['gal_list_name'] = 'y';
$pref['gal_list_description'] = 'y';
$pref['gal_list_created'] = 'y';
$pref['gal_list_lastmodif'] = 'y';
$pref['gal_list_user'] = 'y';
$pref['gal_list_imgs'] = 'y';
$pref['gal_list_visits'] = 'y';
$pref['feature_image_gallery_mandatory_category'] = '-1';
$pref['gal_imgcache_dir'] = 'temp/cache';

# spellcheck
if (file_exists('lib/bablotron.php')) {
	$pref['lib_spellcheck'] = 'y';
	$pref['wiki_spellcheck'] = 'n';
	$pref['cms_spellcheck'] = 'n';
	$pref['blog_spellcheck'] = 'n';
}

# forums
$sections['forums']['feature'] = 'feature_forums';
$sections['forums']['key'] = 'forumId';
$sections['forums']['itemkey'] = 'postId';
$pref['feature_forums'] = 'n';
$pref['home_forum'] = 0;
$pref['feature_forum_rankings'] = 'y';
$pref['feature_forum_parse'] = 'n';
$pref['feature_forum_quickjump'] = 'n';
$pref['feature_forum_topicd'] = 'y';
$pref['forums_ordering'] = 'created_desc';
$pref['forums_comments_per_page'] = 10;
$pref['forum_list_topics'] =  'y';
$pref['forum_list_posts'] =  'y';
$pref['forum_list_ppd'] =  'y';
$pref['forum_list_lastpost'] =  'y';
$pref['forum_list_visits'] =  'y';
$pref['forum_list_desc'] =  'y';

# articles
$sections['cms']['feature'] = 'feature_articles';
$sections['cms']['key'] = 'topicId';
$sections['cms']['itemkey'] = 'articleId';
$pref['feature_articles'] = 'n';
$pref['feature_submissions'] = 'n';
$pref['feature_cms_rankings'] = 'y';
$pref['art_list_title'] = 'y';
$pref['art_list_title_len'] = '20';
$pref['art_list_topic'] = 'y';
$pref['art_list_date'] = 'y';
$pref['art_list_author'] = 'y';
$pref['art_list_reads'] = 'y';
$pref['art_list_size'] = 'y';
$pref['art_list_expire'] = 'y';
$pref['art_list_img'] = 'y';
$pref['art_list_type'] = 'y';
$pref['art_list_visible'] = 'y';
$pref['art_view_type'] = 'y';
$pref['art_view_title'] = 'y';
$pref['art_view_topic'] = 'y';
$pref['art_view_date'] = 'y';
$pref['art_view_author'] = 'y';
$pref['art_view_reads'] = 'y';
$pref['art_view_size'] = 'y';
$pref['art_view_img'] = 'y';
$pref['feature_article_comments'] = 'n';
$pref['article_comments_default_ordering'] = 'points_desc';
$pref['article_comments_per_page'] = 10;
$pref['feature_cms_templates'] = 'n';
$pref['cms_bot_bar'] = 'y';
$pref['cms_left_column'] = 'y';
$pref['cms_right_column'] = 'y';
$pref['cms_top_bar'] = 'n';
$pref['cms_spellcheck'] = 'n';

# trackers
$sections['trackers']['feature'] = 'feature_trackers';
$sections['trackers']['key'] = 'trackerId';
$sections['trackers']['itemkey'] = 'itemId';
$pref['feature_trackers'] = 'n';
$pref['t_use_db'] = 'y';
$pref['t_use_dir'] = '';
$pref['groupTracker'] = 'n';
$pref['userTracker'] = 'n';
$pref['trk_with_mirror_tables'] = 'n';

# user
$sections['mytiki']['feature'] = '';
$sections['mytiki']['key'] = 'user';
$sections['mytiki']['itemkey'] = '';
$pref['userbreadCrumb'] = 4;
$pref['user_assigned_modules'] = 'n';
$pref['user_flip_modules'] = 'module';
$pref['feature_userPreferences'] = 'n';
$pref['feature_userVersions'] = 'y';
$pref['feature_user_bookmarks'] = 'n';
$pref['feature_tasks'] = 'n';
$pref['w_use_db'] = 'y';
$pref['w_use_dir'] = '';
$pref['uf_use_db'] = 'y';
$pref['uf_use_dir'] = '';
$pref['userfiles_quota'] = 30;
$pref['feature_usermenu'] = 'n';
$pref['feature_minical'] = 'n';
$pref['feature_notepad'] = 'n';
$pref['feature_userfiles'] = 'n';
$pref['feature_community_mouseover'] = 'n';
$pref['feature_community_mouseover_name'] = 'y';
$pref['feature_community_mouseover_picture'] = 'y';
$pref['feature_community_mouseover_friends'] = 'y';
$pref['feature_community_mouseover_score'] = 'y';
$pref['feature_community_mouseover_country'] = 'y';
$pref['feature_community_mouseover_email'] = 'y';
$pref['feature_community_mouseover_lastlogin'] = 'y';
$pref['feature_community_mouseover_distance'] = 'y';
$pref['feature_community_friends_permission'] = 'n';
$pref['feature_community_friends_permission_dep'] = '2';
$pref['change_language'] = 'y';
$pref['change_theme'] = 'y';
$pref['login_is_email'] = 'n';
$pref['validateUsers'] = 'n';
$pref['forgotPass'] = 'n';
$pref['change_password'] = 'y';
$pref['available_languages'] = 'a:0:{}'; 
$pref['available_styles'] = 'a:0:{}'; 
$pref['lowercase_username'] = 'n'; 
$pref['max_username_length'] = '50'; 
$pref['min_username_length'] = '1'; 
$pref['users_prefs_allowMsgs'] = 'n'; 
$pref['users_prefs_country'] = ''; 
$pref['users_prefs_diff_versions'] = 'n'; 
$pref['users_prefs_display_timezone'] = 'Local'; 
$pref['users_prefs_email_is_public'] = 'n'; 
$pref['users_prefs_homePage'] = ''; 
$pref['users_prefs_lat'] = ''; 
$pref['users_prefs_lon'] = ''; 
$pref['users_prefs_mess_archiveAfter'] = '0'; 
$pref['users_prefs_mess_maxRecords'] = '10'; 
$pref['users_prefs_mess_sendReadStatus'] = 'n'; 
$pref['users_prefs_minPrio'] = '1'; 
$pref['users_prefs_mytiki_blogs'] = 'y'; 
$pref['users_prefs_mytiki_gals'] = 'y'; 
$pref['users_prefs_mytiki_items'] = 'y'; 
$pref['users_prefs_mytiki_msgs'] = 'y'; 
$pref['users_prefs_mytiki_pages'] = 'y'; 
$pref['users_prefs_mytiki_tasks'] = 'y'; 
$pref['users_prefs_mytiki_workflow'] = 'y'; 
$pref['users_prefs_realName'] = ''; 
$pref['users_prefs_show_mouseover_user_info'] = 'y'; 
$pref['users_prefs_tasks_maxRecords'] = '10'; 
$pref['users_prefs_user_dbl'] = 'n'; 
$pref['users_prefs_user_information'] = 'public'; 
$pref['users_prefs_userbreadCrumb'] = '4'; 
$pref['validateRegistration'] = 'n'; 

# user messages
$sections['user_messages']['feature'] = 'feature_messages';
$sections['user_messages']['key'] = 'msgId';
$sections['user_messages']['itemkey'] = '';
$pref['feature_messages'] = 'n';
$pref['messu_mailbox_size'] = '0';
$pref['messu_archive_size'] = '200';
$pref['messu_sent_size'] = '200';
$pref['allowmsg_by_default'] = 'n';
$pref['allowmsg_is_optional'] = 'y';

# newsreader
$sections['newsreader']['feature'] = 'feature_newsreader';
$sections['newsreader']['key'] = 'serverId';
$sections['newsreader']['itemkey'] = 'id';
$pref['feature_newsreader'] = 'n';

# mytiki
$sections['mytiki']['feature'] = '';


# freetags
$pref['feature_freetags'] = 'n';
$pref['freetags_browse_show_cloud'] = 'y';
$pref['freetags_browse_amount_tags_in_cloud'] = '100';
$pref['freetags_feature_3d'] = 'n';
$pref['freetags_3d_width'] = 500;
$pref['freetags_3d_height'] = 500;
$pref['freetags_3d_navigation_depth'] = 1;
$pref['freetags_3d_feed_animation_interval'] = 500;
$pref['freetags_3d_existing_page_color'] = '#00CC55';
$pref['freetags_3d_missing_page_color'] = '#FF5555';

# search
$pref['feature_search_stats'] = 'n';
$pref['feature_search'] = 'y';
$pref['feature_search_fulltext'] = 'y';
$pref['feature_search_show_forbidden_obj'] = 'n';
$pref['feature_search_show_forbidden_cat'] = 'n';

# chat
$sections['chat']['feature'] = 'feature_chat';
$sections['chat']['key'] = '';
$sections['chat']['itemkey'] = '';
$pref['feature_chat'] = 'n';

# webmail
$sections['webmail']['feature'] = 'feature_webmail';
$sections['webmail']['key'] = 'msgId';
$sections['webmail']['itemkey'] = '';
$pref['feature_webmail'] = 'n';
$pref['webmail_max_attachment'] = 1500000;
$pref['webmail_view_html'] = 'y';

# contaacts
$sections['contacts']['feature'] = 'feature_contacts';
$sections['contacts']['key'] = 'contactId';
$sections['contacts']['itemkey'] = '';
$pref['feature_contacts'] = 'n';

# faq
$sections['faqs']['feature'] = 'feature_faqs';
$sections['faqs']['key'] = 'faqId';
$sections['faqs']['itemkey'] = '';
$pref['feature_faqs'] = 'n';
$pref['feature_faq_comments'] = 'y';
$pref['faq_comments_default_ordering'] = 'points_desc';
$pref['faq_comments_per_page'] = 10;

# quizzes
$sections['quizzes']['feature'] = 'feature_quizzes';
$sections['quizzes']['key'] = 'quizId';
$sections['quizzes']['itemkey'] = '';
$pref['feature_quizzes'] = 'n';

# polls
$sections['poll']['feature'] = 'feature_polls';
$sections['poll']['key'] = 'pollId';
$sections['poll']['itemkey'] = '';
$pref['feature_polls'] = 'n';
$pref['feature_poll_comments'] = 'n';
$pref['feature_poll_anonymous'] = 'n';
$pref['poll_comments_default_ordering'] = 'points_desc';
$pref['poll_comments_per_page'] = 10;

# surveys
$sections['surveys']['feature'] = 'feature_surveys';
$sections['surveys']['key'] = 'surveyId';
$sections['surveys']['itemkey'] = '';
$pref['feature_surveys'] = 'n';

# featured links
$sections['featured_links']['feature'] = 'feature_featuredLinks';
$sections['featured_links']['key'] = 'url';
$sections['featured_links']['itemkey'] = '';
$pref['feature_featuredLinks'] = 'n';

# directories
$sections['directory']['feature'] = 'feature_directory';
$sections['directory']['key'] = 'directoryId';
$sections['directory']['itemkey'] = '';
$pref['feature_directory'] = 'n';
$pref['directory_columns'] = 3;
$pref['directory_links_per_page'] = 20;
$pref['directory_open_links'] = 'n';
$pref['directory_validate_urls'] = 'n';
$pref['directory_cool_sites'] = 'y';

# calendar
$sections['calendar']['feature'] = 'feature_calendar';
$sections['calendar']['key'] = 'calendarId';
$sections['calendar']['itemkey'] = 'calitmId';
$pref['feature_calendar'] = 'n';
$pref['calendar_sticky_popup'] = 'n';
$pref['calendar_view_mode'] = 'week';
$pref['calendar_view_tab'] = 'n';
$pref['calendar_firstDayofWeek'] = 'user';
$pref['calendar_timespan'] = '5';
$pref['feature_cal_manual_time'] = '0';
$pref['feature_jscalendar'] = 'n';
$pref['feature_action_calendar'] = 'n';

# dates
$pref['display_timezone'] = 'EST';
$pref['long_date_format'] = '%A %d of %B, %Y';
$pref['long_time_format'] = '%H:%M:%S %Z';
$pref['short_date_format'] = '%a %d of %b, %Y';
$pref['short_time_format'] = '%H:%M %Z';
$pref['display_field_order'] = 'MDY';

# workflow
$sections['workflow']['feature'] = 'feature_workflow';
$sections['workflow']['key'] = '';
$sections['workflow']['itemkey'] = '';

# charts
$sections['charts']['feature'] = 'feature_charts';
$sections['charts']['key'] = '';
$sections['charts']['itemkey'] = '';
$pref['feature_charts'] = 'n';

# rss
$pref['rss_forums'] = 'y';
$pref['rss_forum'] = 'y';
$pref['rss_directories'] = 'y';
$pref['rss_articles'] = 'y';
$pref['rss_blogs'] = 'y';
$pref['rss_image_galleries'] = 'y';
$pref['rss_file_galleries'] = 'y';
$pref['rss_wiki'] = 'y';
$pref['rss_image_gallery'] = 'n';
$pref['rss_file_gallery'] = 'n';
$pref['rss_blog'] = 'n';
$pref['rss_tracker'] = 'n';
$pref['rss_trackers'] = 'n';
$pref['rss_calendar'] = 'n';
$pref['rss_mapfiles'] = 'n';
$pref['rss_cache_time'] = '0'; // 0 = disabled (default)
$pref['max_rss_forums'] = 10;
$pref['max_rss_forum'] = 10;
$pref['max_rss_directories'] = 10;
$pref['max_rss_articles'] = 10;
$pref['max_rss_blogs'] = 10;
$pref['max_rss_image_galleries'] = 10;
$pref['max_rss_file_galleries'] = 10;
$pref['max_rss_wiki'] = 10;
$pref['max_rss_image_gallery'] = 10;
$pref['max_rss_file_gallery'] = 10;
$pref['max_rss_blog'] = 10;
$pref['max_rss_mapfiles'] = 10;
$pref['max_rss_tracker'] = 10;
$pref['max_rss_trackers'] = 10;
$pref['max_rss_calendar'] = 10;
$pref['rssfeed_default_version'] = '2';
$pref['rssfeed_language'] =  'en-us';
$pref['rssfeed_editor'] = '';
$pref['rssfeed_webmaster'] = '';
$pref['rssfeed_creator'] = '';
$pref['rssfeed_css'] = 'y';
$pref['rssfeed_publisher'] = '';


# maps
$sections['maps']['feature'] = 'feature_maps';
$sections['maps']['key'] = 'mapId';
$sections['maps']['itemkey'] = '';
$pref['feature_maps'] = 'n';
$pref['map_path'] = '';
$pref['default_map'] = '';
$pref['map_help'] = 'MapsHelp';
$pref['map_comments'] = 'MapsComments';
$pref['gdaltindex'] = '';
$pref['ogr2ogr'] = '';
$pref['mapzone'] = '';

# gmap
$sections['gmaps']['feature'] = 'feature_gmap';
$sections['gmaps']['key'] = '';
$sections['gmaps']['itemkey'] = '';
$pref['feature_gmap'] = 'n';
$pref['gmap_defaultx'] = '0';
$pref['gmap_defaulty'] = '0';
$pref['gmap_defaultz'] = '17';
$pref['gmap_key'] = '';


# auth
$pref['allowRegister'] = 'n';
$pref['eponymousGroups'] = 'n';
$pref['useRegisterPasscode'] = 'n';
$pref['registerPasscode'] = '';
$pref['rememberme'] = 'disabled';
$pref['remembertime'] = 7200;
$pref['feature_clear_passwords'] = 'n';
$pref['feature_crypt_passwords'] = 'tikihash';
$pref['feature_challenge'] = 'n';
$pref['min_user_length'] = 1;
$pref['min_pass_length'] = 1;
$pref['pass_chr_num'] = 'n';
$pref['pass_due'] = 999;
$pref['rnd_num_reg'] = 'n';
$pref['auth_method'] = 'tiki';
$pref['auth_pear'] = 'tiki';
$pref['auth_create_user_tiki'] = 'n';
$pref['auth_create_user_auth'] = 'n';
$pref['auth_skip_admin'] = 'y';
$pref['auth_ldap_url'] = '';
$pref['auth_pear_host'] = "localhost";
$pref['auth_pear_port'] = "389";
$pref['auth_ldap_scope'] = "sub";
$pref['auth_ldap_basedn'] = '';
$pref['auth_ldap_userdn'] = '';
$pref['auth_ldap_userattr'] = 'uid';
$pref['auth_ldap_useroc'] = 'inetOrgPerson';
$pref['auth_ldap_groupdn'] = '';
$pref['auth_ldap_groupattr'] = 'cn';
$pref['auth_ldap_groupoc'] = 'groupOfUniqueNames';
$pref['auth_ldap_memberattr'] = 'uniqueMember';
$pref['auth_ldap_memberisdn'] = 'y';
$pref['auth_ldap_adminuser'] = '';
$pref['auth_ldap_adminpass'] = '';
$pref['auth_ldap_version'] = 3;
$pref['https'] = 'auto';
$pref['https_login'] = 'n';
$pref['https_login_required'] = 'n';
$pref['https_mode'] = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
if ($pref['https_mode']) {
	$pref['http_port'] = 80;
	$pref['https_port'] = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 443;
} else {
	$pref['http_port'] = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80;
	$pref['https_port'] = 443;
}
$pref['http_domain'] = '';
$pref['http_prefix'] = '/';
$pref['https_domain'] = '';
$pref['https_prefix'] = '/';
$pref['base_url'] = 'http://' . $default['feature_server_name'];
$pref['login_url'] = 'tiki-login.php';
$pref['login_scr'] = 'tiki-login_scr.php';
$pref['register_url'] = 'tiki-register.php';
$pref['error_url'] = 'tiki-error.php';
$pref['highlight_group'] = '';
$pref['cookie_path'] = '/';
$pref['cookie_domain'] = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
$pref['cookie_name'] = 'tikiwiki';
if ($phpcas_enabled == 'y') {
	$pref['cas_create_user_tiki'] = 'n';
	$pref['cas_skip_admin'] = 'n';
	$pref['cas_version'] = '';
	$pref['cas_hostname'] = '';
	$pref['cas_port'] = '';
	$pref['cas_path'] = '';
}

# intertiki
$pref['feature_intertiki'] = 'n';
$pref['feature_intertiki_server'] = 'n';
$pref['feature_intertiki_slavemode'] = 'n';
$pref['interlist'] = serialize(array(''));
$pref['feature_intertiki_mymaster'] = '';
$pref['feature_intertiki_import_preferences'] = 'n';
$pref['feature_intertiki_import_groups'] = 'n';
$pref['known_hosts'] = serialize(array(''));
$pref['tiki_key'] = '';
$pref['intertiki_logfile'] = '';
$pref['intertiki_errfile'] = '';

# search
$pref['search_lru_length'] = '100';
$pref['search_lru_purge_rate'] = '5';
$pref['search_max_syllwords'] = '100';
$pref['search_min_wordlength'] = '3';
$pref['search_refresh_rate'] = '5';
$pref['search_syll_age'] = '48';

# categories
$sections['categories']['feature'] = 'feature_categories';
$sections['categories']['key'] = 'categId';
$sections['categories']['itemkey'] = '';
$pref['feature_categories'] = 'n';
$pref['feature_categoryobjects'] = 'n';
$pref['feature_categorypath'] = 'n';

# games
$sections['games']['feature'] = 'feature_games';
$sections['games']['key'] = 'gameId';
$sections['games']['itemkey'] = '';
$pref['feature_games'] = 'n';

# html pages
$sections['html_pages']['feature'] = 'feature_html_pages';
$sections['html_pages']['key'] = 'pageId';
$sections['html_pages']['itemkey'] = '';
$pref['feature_html_pages'] = 'n';

# contact & mail
$pref['feature_contact'] = 'n';
$pref['contact_user'] = 'admin';
$pref['contact_anon'] = 'n';
$pref['mail_crlf'] = 'LF';

# i18n
$pref['feature_detect_language'] = 'n';
$pref['feature_homePage_if_bl_missing'] = 'n';
$pref['record_untranslated'] = 'n';
$pref['feature_best_language'] = 'n';
$pref['lang_use_db'] = 'n';
$pref['language'] = 'en';
$pref['feature_babelfish'] = 'n';
$pref['feature_babelfish_logo'] = 'n';

# html header
$pref['metatag_keywords'] = '';
$pref['metatag_description'] = '';
$pref['metatag_author'] = '';
$pref['metatag_geoposition'] = '';
$pref['metatag_georegion'] = '';
$pref['metatag_geoplacename'] = '';
$pref['metatag_robots'] = '';
$pref['metatag_revisitafter'] = '';
$pref['head_extra_js'] = array();
$pref['keep_versions'] = 1;
$pref['feature_custom_home'] = 'n';

# site identity
$pref['feature_siteidentity'] = 'n';
$pref['site_crumb_seper'] = '>';
$pref['site_nav_seper'] = '|';
$pref['feature_sitemycode'] = 'n';
$pref['sitemycode'] = '<div align="center"><b>{tr}Here you can (as an admin) place a piece of custom XHTML and/or Smarty code. Be careful and properly close all the tags before you choose to publish ! (Javascript, applets and object tags are stripped out.){/tr}</b></div>'; // must be max. 250 chars now unless it'll change in tiki_prefs db table field value from VARCHAR(250) to BLOB by default
$pref['sitemycode_publish'] = 'n';
$pref['feature_sitelogo'] = 'y';
$pref['sitelogo_bgcolor'] = '';
$pref['sitelogo_title'] = 'TikiWiki powered site';
$pref['sitelogo_src'] = 'img/tiki/tikilogo.png';
$pref['sitelogo_alt'] = 'Site Logo';
$pref['feature_siteloc'] = 'y';
$pref['feature_sitenav'] = 'n';
$pref['sitenav'] = '{tr}Navigation : {/tr}<a href="tiki-contact.php" accesskey="10" title="">{tr}Contact Us{/tr}</a>';
$pref['feature_sitead'] = 'y';
$pref['sitead'] = '';
$pref['sitead_publish'] = 'n';
$pref['feature_breadcrumbs'] = 'n';
$pref['feature_siteloclabel'] = 'y';
$pref['feature_sitesearch'] = 'y';
$pref['feature_sitemenu'] = 'n';
$pref['feature_sitetitle'] = 'y';
$pref['feature_sitedesc'] = 'n';

# layout
$pref['feature_left_column'] = 'y';
$pref['feature_right_column'] = 'y';
$pref['feature_top_bar'] = 'y';
$pref['feature_bot_bar'] = 'y';
$pref['feature_bot_bar_icons'] = 'y';
$pref['feature_bot_bar_debug'] = 'y';
$pref['title'] = '';
$pref['maxRecords'] = 10;
$pref['maxArticles'] = 10;
$pref['maxVersions'] = 0;
$pref['feature_view_tpl'] = 'n';
$pref['slide_style'] = 'slidestyle.css';
$pref['site_favicon'] = 'favicon.png';
$pref['style'] = 'tikineat.css';

# mods
$pref['feature_mods_provider'] = 'n';
$pref['mods_dir'] = 'mods';
$pref['mods_server'] = 'http://tikiwiki.org/mods';

# dev
$pref['feature_experimental'] = 'n'; 

# admin
$pref['feature_actionlog'] = 'y';
$pref['siteTitle'] = '';
$pref['tmpDir'] = 'temp';

# unsorted features
$pref['anonCanEdit'] = 'n';
$pref['cacheimages'] = 'n';
$pref['cachepages'] = 'n';
$pref['count_admin_pvs'] = 'y';
$pref['dblclickedit'] =  'n';
$pref['default_mail_charset'] ='utf-8';
$pref['direct_pagination'] = 'n';
$pref['error_reporting_adminonly'] = 'y';
$pref['error_reporting_level'] = 0;
$pref['feature_ajax'] = 'n';
$pref['feature_antibot'] = 'n';
$pref['feature_autolinks'] = 'y';
$pref['feature_banners'] = 'n';
$pref['feature_banning'] = 'n';
$pref['feature_comm'] = 'n';
$pref['feature_contribution'] = 'n';
$pref['feature_contribution_display_in_comment'] = 'y';
$pref['feature_contribution_mandatory'] = 'y';
$pref['feature_contribution_mandatory_blog'] = 'n';
$pref['feature_contribution_mandatory_comment'] = 'n';
$pref['feature_contribution_mandatory_forum'] = 'n';
$pref['feature_debug_console'] = 'n';
$pref['feature_debugger_console'] = 'n';
$pref['feature_display_my_to_others'] = 'n';
$pref['feature_drawings'] = 'n';
$pref['feature_dynamic_content'] = 'n';
$pref['feature_edit_templates'] = 'n';
$pref['feature_editcss'] = 'n';
$pref['feature_eph'] = 'n';
$pref['feature_events'] = 'n';
$pref['feature_friends'] = 'n';
$pref['feature_fullscreen'] = 'n';
$pref['feature_help'] = 'y';
$pref['feature_hotwords'] = 'y';
$pref['feature_hotwords_nw'] = 'n';
$pref['feature_integrator'] = 'n';
$pref['feature_live_support'] = 'n';
$pref['feature_mailin'] = 'n';
$pref['feature_menusfolderstyle'] = 'y';
$pref['feature_mobile'] = 'n';
$pref['feature_modulecontrols'] = 'n';
$pref['feature_morcego'] = 'n';
$pref['feature_multilingual'] = 'y';
$pref['feature_newsletters'] = 'n';
$pref['feature_obzip'] = 'n';
$pref['feature_phplayers'] = 'n';
$pref['feature_projects'] = 'n';
$pref['feature_ranking'] = 'n';
$pref['feature_redirect_on_error'] = 'n';
$pref['feature_referer_highlight'] = 'n';
$pref['feature_referer_stats'] = 'n';
$pref['feature_score'] = 'n';
$pref['feature_server_name'] = (isset($_SERVER['SERVER_NAME'])) ? $_SERVER['SERVER_NAME']  : $_SERVER['HTTP_HOST'];
$pref['feature_sheet'] = 'n';
$pref['feature_shoutbox'] = 'n';
$pref['feature_smileys'] = 'y';
$pref['feature_source'] = 'y';
$pref['feature_stats'] = 'n';
$pref['feature_tabs'] = 'n';
$pref['feature_theme_control'] = 'n';
$pref['feature_ticketlib'] = 'n';
$pref['feature_ticketlib2'] = 'y';
$pref['feature_top_banner'] = 'n';
$pref['feature_usability'] = 'n';
$pref['feature_use_quoteplugin'] = 'n';
$pref['feature_user_watches'] = 'n';
$pref['feature_user_watches_translations'] = 'y';
$pref['feature_workflow'] = 'n';
$pref['feature_xmlrpc'] = 'n';
$pref['helpurl'] = "http://doc.tikiwiki.org/tiki-index.php?best_lang&amp;page=";
$pref['layout_section'] = 'n';
$pref['limitedGoGroupHome'] = 'n';
$pref['minical_reminders'] = $tikilib->get_user_preference($user, 'minical_reminders', 0);
$pref['modallgroups'] = 'y';
$pref['modseparateanon'] = 'n';
$pref['php_docroot'] = 'http://php.net/';
$pref['popupLinks'] = 'n';
$pref['proxy_host'] = '';
$pref['proxy_port'] = '';
$pref['sender_email'] = $userlib->get_admin_email();
$pref['session_db'] = 'n';
$pref['session_lifetime'] = 0;
$pref['shoutbox_autolink'] = 'n';
$pref['show_comzone'] = 'n';
$pref['system_os'] = TikiSetup::os();
$pref['tikiIndex'] = 'tiki-index.php';
$pref['urlIndex'] = '';
$pref['useGroupHome'] = 'n';
$pref['useUrlIndex'] = 'n';
$pref['use_proxy'] = 'n';
$pref['user_list_order'] = 'score_desc';
$pref['webserverauth'] = 'n';

// ******************************************************************************************
// First we populate with default values
foreach ($pref as $defpref=>$defval) {
	$$defpref = $defval;
	$smarty->assign("$defpref", $defval);
}

// ******************************************************************************************
// start of replacement : get all prefs from db once
$tikilib->get_all_preferences();
foreach ($preferences as $name => $val) {
	$$name = $val;
	$smarty->assign("$name", $val);
}
// ******************************************************************************************
$sections_enabled = array();
foreach ($sections as $sec=>$dat) {
	$feat = $dat['feature'];
	if ($feat === '' or (isset($$feat) and $$feat == 'y')) {
		$sections_enabled[$sec] = $dat;
	}
}
ksort($sections_enabled);
// ******************************************************************************************

$area = 'tiki';
$fullscreen = 'n';
$cookielist = array();

$smarty->assign('lastup', '');
$smarty->assign('edit_page', 'n');
$smarty->assign('forum_mode', 'n');
$smarty->assign('msgError', '');
$smarty->assign('tmpDir', $tmpDir);
$smarty->assign('module_error', '');
$smarty->assign('uses_tabs', 'n');
$smarty->assign('uses_jscalendar', 'n');
$smarty->assign('uses_phplayers', 'n');
$smarty->assign('fullscreen', 'n');
$smarty->assign('semUser', '');
if (!empty($section)) {
	$smarty->assign('section', $section);
}
ini_set('docref_root',$php_docroot);

$tikipath = dirname($_SERVER['SCRIPT_FILENAME']);
if (substr($tikipath,-1,1) != '/') $tikipath.= '/';
$tikiroot = dirname($_SERVER['PHP_SELF']);
if (substr($tikiroot,-1,1) != '/') $tikiroot.= '/';
$smarty->assign('tikipath',$tikipath);
$smarty->assign('tikiroot',$tikiroot);

if (isset($_SESSION['tiki_cookie_jar'])) {
	foreach ($_SESSION['tiki_cookie_jar'] as $nn=>$vv) {
		$cookielist[] = "$nn: '". addslashes($vv)."'";
	}
	if (count($cookielist)) {
		$headerlib->add_js("var tiki_cookie_jar=new Array();\ntiki_cookie_jar={\n". implode(",\n\t",$cookielist)."\n};",80);
	}
}

if (!empty($_SESSION['language']))
	$saveLanguage = $_SESSION['language']; // if register_globals is on variable and _SESSION are the same
if (isset($_SESSION['style']))
	$style = $_SESSION['style'];

if (isset($_COOKIE['tiki-theme']) ) {
	$style = $_COOKIE['tiki-theme'];
}

if ($error_reporting_level == 1) {
	$error_reporting_level = ($tiki_p_admin == 'y') ? E_ALL: 0;
} elseif ($error_reporting_adminonly == 'y' and $tiki_p_admin != 'y') {
	$error_reporting_level = 0;
}
error_reporting($error_reporting_level);


$smarty->assign('wiki_extras', 'n');

if (!isset($feature_bidi)) { $feature_bidi = 'n'; }
$smarty->assign('feature_bidi', $feature_bidi);


if ($https_login == 'y' || $https_login_required == 'y') {
	$http_login_url = 'http://' . $http_domain;
	if ($http_port != 80) {
		$http_login_url .= ':' . $http_port;
	}
	$http_login_url .= $http_prefix . $tikiIndex;
	if (SID) {
		$http_login_url .= '?' . SID;
	}
	$edit_data = htmlentities(isset($_REQUEST['edit']) ? $_REQUEST['edit'] : '', ENT_QUOTES);
	$https_login_url = 'https://' . $https_domain;
	if ($https_port != 443) {
		$https_login_url .= ':' . $https_port;
	}
	$https_login_url .= $https_prefix . $tikiIndex;
	if (SID) {
		$https_login_url .= '?' . SID;
	}
	$stay_in_ssl_mode = isset($_REQUEST['stay_in_ssl_mode']) ? $_REQUEST['stay_in_ssl_mode'] : '';
	if ($https_login_required == 'y') {
		$show_stay_in_ssl_mode = !$https_mode ? 'y' : 'n';
		$smarty->assign('show_stay_in_ssl_mode', $show_stay_in_ssl_mode);
		if (!$https_mode) {
			$https_login_url = 'https://' . $https_domain;
			if ($https_port != 443) {
				$https_login_url .= ':' . $https_port;
			}
			$https_login_url .= $https_prefix . $login_url;
			if (SID) {
				$https_login_url .= '?' . SID;
			}
			$smarty->assign('login_url', $https_login_url);
		} else {
			$stay_in_ssl_mode = 'on';
		}
	} else {
		$smarty->assign('http_login_url', $http_login_url);
		$smarty->assign('https_login_url', $https_login_url);
		$show_stay_in_ssl_mode = $https_mode ? 'y' : 'n';
	}
	$smarty->assign('show_stay_in_ssl_mode', $show_stay_in_ssl_mode);
	$smarty->assign('stay_in_ssl_mode', $stay_in_ssl_mode);
}

if ($wiki_page_regex == 'strict') {
	$page_regex = '([A-Za-z0-9_])([\.: A-Za-z0-9_\-])*([A-Za-z0-9_])';
} elseif ($wiki_page_regex == 'full') {
	$page_regex = '([A-Za-z0-9_]|[\x80-\xFF])([\.: A-Za-z0-9_\-]|[\x80-\xFF])*([A-Za-z0-9_]|[\x80-\xFF])';
} else {
	$page_regex = '([^\n|\(\)])([^\n|\(\)](?!\)\)))*?([^\n|\(\)])';
}

$wiki_dump_exists = 'n';
$dump_path = 'dump';
if ($tikidomain) {
	$dump_path.= "/$tikidomain";
}
if (file_exists($dump_path.'/new.tar')){
	$wiki_dump_exists = 'y';
};
$smarty->assign('wiki_dump_exists', $wiki_dump_exists);

$interlist = unserialize($interlist);

if ($feature_polls == 'y' and isset($_REQUEST['pollVote'])) {
	if ($tiki_p_vote_poll == 'y' && isset($_REQUEST['polls_optionId'])) {
		if( $feature_poll_anonymous == 'y' || $user ) {
			if (!isset($polllib) or !is_object($polllib)) { 
				include_once('lib/polls/polllib_shared.php'); 
			}
			$polllib->poll_vote($user, $_REQUEST['polls_pollId'], $_REQUEST['polls_optionId']);
			// Poll vote must go first, or the new vote will be seen as the previous one.
			$tikilib->register_user_vote($user, 'poll' . $_REQUEST['polls_pollId'], $_REQUEST['polls_optionId']);
		}
	}
	$pollId = $_REQUEST['polls_pollId'];
	if (!isset($_REQUEST['wikipoll'])) {
		header ("location: tiki-poll_results.php?pollId=$pollId");
	}
}

if ($feature_mailin == 'y' && $mailin_autocheck == 'y') {
  if ((time() - $mailin_autocheckLast)/60 > $mailin_autocheckFreq) {
    $tikilib->set_preference('mailin_autocheckLast', time());
    include_once('tiki-mailin-code.php');
  }
}

if ($feature_detect_language == 'y') {
	$browser_language = detect_browser_language();
	if (!empty($browser_language)) {
		$language = $browser_language;
		$smarty->assign('language', $language);
	}
}

$group = '';

$group = $userlib->get_user_default_group($user);
if($useGroupHome == 'y') {
	$groupHome = $userlib->get_user_default_homepage($user);
	if ($groupHome) {
		if (preg_match('#^https?:#', $groupHome)) {
			$tikiIndex = $groupHome;
		} else {
			$tikiIndex = 'tiki-index.php?page='.$groupHome;
			$wikiHomePage = $groupHome;
			$smarty->assign('wikiHomePage',$wikiHomePage);
		}
	}
}

$smarty->assign('default_group',$group);

$user_dbl = 'y';
$diff_versions = 'n';

$user_style = $site_style = $style;

if( isset($_COOKIE['tiki-theme']) ) {
	$user_style = $_COOKIE['tiki-theme'];
}
if (isset($_REQUEST['switchLang'])) {
	if ($change_language != 'y'
		|| !preg_match("/[a-zA-Z-_]*$/", $_REQUEST['switchLang'])
		|| !file_exists('lang/'.$_REQUEST['switchLang'].'/language.php')
		|| ($available_languages && !in_array($_REQUEST['switchLang'], unserialize($available_languages))) ) {
			unset($_REQUEST['switchLang']);
	}
}

if (isset($_REQUEST['switchLang'])) {
	if ($change_language != 'y'
		|| !preg_match("/[a-zA-Z-_]*$/", $_REQUEST['switchLang'])
		|| !file_exists('lang/'.$_REQUEST['switchLang'].'/language.php'))
		unset($_REQUEST['switchLang']);
	elseif ($available_languages) {
		$a = unserialize($available_languages);
		if (count($a) >= 1 && !in_array($_REQUEST['switchLang'], $a))
			unset($_REQUEST['switchLang']);
	}
}

if ($feature_userPreferences == 'y') {
	if ($user) {
		$user_dbl = $tikilib->get_user_preference($user, 'user_dbl', 'y');
		$diff_versions = $tikilib->get_user_preference($user, 'diff_versions', 'n');
		if ($change_theme == 'y') {
			$user_style = $tikilib->get_user_preference($user, 'theme', $style);
			if ($user_style and (is_file("styles/$user_style") or is_file("styles/$tikidomain/$user_style"))) {
				$style = $user_style;
			}
		}
		if ($change_language == 'y') {
			if (isset($_REQUEST['switchLang'])) {
				$language = $_REQUEST['switchLang'];
				$tikilib->set_user_preference($user, 'language', $language);
			} else {
				$user_language = $tikilib->get_user_preference($user, 'language', $language);
				if ($user_language) {
					$language = $user_language;
				}
			}
		}
	} else {
		$style = $user_style;
	}
	$smarty->assign('language', $language);
} else {
	$style = $user_style;
}

if (!is_file("styles/$style") or !is_file("styles/$tikidomain/$style")) {
	$style = 'tikineat.css';
}
if ($tikidomain and is_file("styles/$tikidomain/$style")) {
	$style = "$tikidomain/$style";
}
$smarty->assign('style', $style);
$smarty->assign('site_style', $site_style);
$smarty->assign('user_style', $user_style);
include_once("csslib.php");
$transition_style = $csslib->transition_css('styles/'.$style);
$headerlib->add_cssfile('styles/transitions/'.$transition_style,50);
$headerlib->add_cssfile('styles/'.$style,51);


if (!$user) {
	if (isset($_REQUEST['switchLang'])) {
		$language = $_REQUEST['switchLang'];
		$_SESSION['language'] = $language;
		$smarty->assign('language', $language);
	} elseif  (!empty($saveLanguage)) { // users not logged that change the preference
		$language = $saveLanguage;
		$smarty->assign('language', $language);
	}
}

$stlstl = split("-|\.", $style);
$style_base = $stlstl[0];

if ($lang_use_db != 'y') {
    // check if needed!!!
    global $lang;
}

if ($feature_babelfish == 'y') {
    require_once('lib/Babelfish.php');
    $smarty->assign('babelfish_links', Babelfish::links($language));
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
if (isset($_SERVER['REMOTE_ADDR'])) {
	$IP = $_SERVER['REMOTE_ADDR'];
	$smarty->assign('IP', $IP);
}
$smarty->assign('lock', false);
$smarty->assign('title', $title);
$smarty->assign('maxRecords', $maxRecords);

// If we are processing a login then do not generate the challenge
// if we are in any other case then yes.
if (!strstr($_SERVER['REQUEST_URI'], 'tiki-login')) {
    if ($feature_challenge == 'y') {
        $chall = $userlib->generate_challenge();

        $_SESSION['challenge'] = $chall;
        $smarty->assign('challenge', $chall);
    }
}

setDisplayMenu('nlmenu');
setDisplayMenu('evmenu');
setDisplayMenu('chartmenu');
setDisplayMenu('ephmenu');
setDisplayMenu('mymenu');
setDisplayMenu('wfmenu');
setDisplayMenu('usrmenu');
setDisplayMenu('friendsmenu');
setDisplayMenu('wikimenu');
setDisplayMenu('homeworkmenu');
setDisplayMenu('srvmenu');
setDisplayMenu('trkmenu');
setDisplayMenu('jukeboxmenu');
setDisplayMenu('quizmenu');
setDisplayMenu('formenu');
setDisplayMenu('dirmenu');
setDisplayMenu('admmnu');
setDisplayMenu('faqsmenu');
setDisplayMenu('galmenu');
setDisplayMenu('cmsmenu');
setDisplayMenu('blogmenu');
setDisplayMenu('filegalmenu');
setDisplayMenu('mapsmenu');
setDisplayMenu('layermenu');
setDisplayMenu('shtmenu');
setDisplayMenu('prjmenu');

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

$ownurl = $tikilib->httpPrefix(). $_SERVER['REQUEST_URI'];
$parsed = parse_url($_SERVER['REQUEST_URI']);

if (!isset($parsed['query'])) {
    $parsed['query'] = '';
}

parse_str($parsed['query'], $query);
$father = $tikilib->httpPrefix(). $parsed['path'];

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
$smarty->assign('ownurl', $ownurl);

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
	$tasks_maxRecords = $tikilib->get_user_preference($user, 'tasks_maxRecords');
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
    if (isset($_SERVER['HTTP_REFERER'])) {
        $pref = parse_url($_SERVER['HTTP_REFERER']);
        if (isset($pref['host']) && !strstr($_SERVER['SERVER_NAME'], $pref['host'])) {
            $tikilib->register_referer($pref['host']);
        }
    }
}

//Check for an update of dynamic vars
if(isset($tiki_p_edit_dynvar) && $tiki_p_edit_dynvar == 'y') {
    if(isset($_REQUEST['_dyn_update'])) { 
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
				if (!isset($section) or ($section != 'chat' and $section != 'livesupport')) { 
            $tikilib->add_pageview();
        }
    }
}

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
	    ob_start ('ob_gzhandler');
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
$smarty->assign_by_ref('elapsed_in_db',$elapsed_in_db);

$favicon = $tikilib->get_preference('site_favicon','favicon.png');
$favicon_type = $tikilib->get_preference('site_favicon_type','image/png');
if (is_file("styles/$tikidomain/$favicon")) {
	$smarty->assign('favicon',"styles/$tikidomain/$favicon");
	$smarty->assign('favicon_type',$favicon_type);
} elseif (is_file($favicon)) {
	$smarty->assign('favicon',$favicon);
	$smarty->assign('favicon_type',$favicon_type);
} else {
	$smarty->assign('favicon',false);
}



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
				$headerlib->add_cssfile($integrator->get_rep_css($_REQUEST['repID']),20);
    }
}

/*
 * Register the search refresh function
 */

# Don't waste time refreshing if we're using full text search.
if ($feature_search == 'y' && $feature_search_fulltext != 'y' ) {
  include_once('lib/search/refresh.php');
  register_shutdown_function('refresh_search_index');
}

/*
 * Whether to show comments zone on page load by default
 */
if (isset($_REQUEST['comzone'])) {
	$comzone=$_REQUEST['comzone'];
	if ($comzone=='show') {
		if (strstr($_SERVER['REQUEST_URI'], 'tiki-read_article') and $feature_article_comments=='y') $show_comzone='y';
		if (strstr($_SERVER['REQUEST_URI'], 'tiki-poll_results') and $feature_poll_comments=='y') $show_comzone='y';
		if (strstr($_SERVER['REQUEST_URI'], 'tiki-index') and $feature_wiki_comments=='y') $show_comzone='y';
		if (strstr($_SERVER['REQUEST_URI'], 'tiki-view_faq') and $feature_faq_comments=='y') $show_comzone='y';
		if (strstr($_SERVER['REQUEST_URI'], 'tiki-browse_gallery') and $feature_image_galleries_comments=='y') $show_comzone='y';
		if (strstr($_SERVER['REQUEST_URI'], 'tiki-list_file_gallery') and $feature_file_galleries_comments=='y') $show_comzone='y';
		if (strstr($_SERVER['REQUEST_URI'], 'tiki-view_blog') and $feature_blog_comments=='y') $show_comzone='y';
		if (strstr($_SERVER['REQUEST_URI'], 'tiki-view_blog_post') and $feature_blogposts_comments=='y') $show_comzone='y';
		if (strstr($_SERVER['REQUEST_URI'], 'tiki-map') and $feature_map_comments=='y') $show_comzone='y';
		if ($show_comzone=='y') $smarty->assign('show_comzone', 'y');
	}
}

/* trick for use with doc/devtools/cvsup.sh */
if (is_file('.lastup') and is_readable('.lastup')) {
	$lastup = file('.lastup');
	$smarty->assign('lastup',$lastup[0]);
}

if ($feature_wiki_discuss == 'y') {
	$wiki_discussion_string = tra ("Use this thread to discuss the page:", $tikilib->get_preference('language', 'en'));
	$smarty->assign('wiki_discussion_string', $wiki_discussion_string);
}
// ------------------------------------------------------
// setup initial breadcrumb
$crumbs = array();
$crumbs[] = new Breadcrumb($siteTitle,'Home',$tikiIndex);
$smarty->assign_by_ref('crumbs', $crumbs);


function getCookie($name, $section=null, $default=null) {
	if (isset($feature_no_cookie) && $feature_no_cookie == 'y') {
		if (isset($_SESSION['tiki_cookie_jar'])) {// if cookie jar doesn't work
			if (isset($_SESSION['tiki_cookie_jar'][$name]))
				return $_SESSION['tiki_cookie_jar'][$name];
			else
				return $default;
		}
	}
	else if ($section){
		if (isset($_COOKIE[$section])) {
			if (preg_match("/@".$name."\:([^@;]*)/", $_COOKIE[$section], $matches))
				return $matches[1];
			else
				return $default;
		}
		else
			return $default;
	}
	else {
		if (isset($_COOKIE[$name]))
			return $_COOKIE[$name];
		else
			return $default;
	}
}
function setDisplayMenu($name) {
	global $smarty;
	if (getCookie($name, 'menu',
			isset($_COOKIE['menu']) ? null : 'o') == 'o') {
		$smarty->assign('mnu_'.$name, 'display:block;');
		$smarty->assign('icn_'.$name, 'o');
}
	else
		$smarty->assign('mnu_'.$name, 'display:none;');
}

/*
 * Some languages needs BiDi support. Add their code names here ...
 */
if ($language == 'ar' || $language == 'he' || $language == 'fa') {
	$feature_bidi='y';
	$smarty->assign('feature_bidi', $feature_bidi);
}

if (!empty($_SESSION['interactive_translation_mode'])&&($_SESSION['interactive_translation_mode']=='on')) {
	include_once("lib/multilingual/multilinguallib.php");
	$cachelib->empty_full_cache();
}
if ($feature_freetags == 'y' and isset($section) and isset($sections[$section])) {
  include_once ('lib/freetag/freetaglib.php');
	$here = $sections[$section];
	if (isset($_POST['addtags']) && trim($_POST['addtags']) != "" && $tiki_p_freetags_tag == 'y') {
		if (!isset($user)) {
			$userid = 0;
		} else {
			$userid = $userlib->get_user_id($user);
		}
		if (isset($here['itemkey']) and isset($_REQUEST[$here['itemkey']])) {
			$freetaglib->tag_object($userid, $_REQUEST[$here['itemkey']], "$section ".$_REQUEST[$here['key']], $_POST['addtags']);
		} elseif (isset($here['key']) and isset($_REQUEST[$here['key']])) {
			$freetaglib->tag_object($userid, $_REQUEST[$here['key']], $section, $_POST['addtags']);
		}
	}
	if (isset($here['itemkey']) and isset($_REQUEST[$here['itemkey']])) {
		$tags = $freetaglib->get_tags_on_object($_REQUEST[$here['itemkey']], "$section ".$_REQUEST[$here['key']]);
	} elseif (isset($here['key']) and isset($_REQUEST[$here['key']])) {
		$tags = $freetaglib->get_tags_on_object($_REQUEST[$here['key']], $section);
	} else {
		$tags = array();
	}
	$smarty->assign('freetags',$tags);
	$headerlib->add_cssfile('css/freetags.css');
}

if ($feature_fullscreen == 'y') {
	$smarty->assign('fsquery',preg_replace('/(\?|&(amp;)?)fullscreen=(n|y)/','',$_SERVER['QUERY_STRING']));
	if (isset($_GET['fullscreen'])) {
		if ($_GET['fullscreen'] == 'y') {
			$_SESSION['fullscreen'] = 'y';
		} else {
			$_SESSION['fullscreen'] = 'n';
		}
	}
	if (!isset($_SESSION['fullscreen'])) {
		$_SESSION['fullscreen'] = 'n';
	}
}
if (!isset($_SESSION['wysiwyg'])) {
  $_SESSION['wysiwyg'] = 'n';
}
$smarty->assign_by_ref('wysiwyg',$_SESSION['wysiwyg']);

$smarty->assign_by_ref('phpErrors',$phpErrors);
?>
