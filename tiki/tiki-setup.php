<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-setup.php,v 1.463 2007-10-04 22:17:34 nyloth Exp $
// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//xdebug_start_profiling();

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

// see http://tikiwiki.org/tiki-index.php?page=CharacterEncodingTrouble
//header('Content-Type: text/html; charset=utf-8');

$phpErrors = array();
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
                    session_save_path($save_path);

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
<h2><font color='red'>Tikiwiki is not properly set up:</font></h1>
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
if(!isset($section)) $section = '';
$tikifeedback = array();

$feature_referer_highlight = 'n';

# Variable checking
$varcheck_errors="";

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

require_once('lib/setup/prefs.php');

# wiki
$sections['wiki page']['feature'] = 'feature_wiki';
$sections['wiki page']['key'] = 'page';
$sections['wiki page']['itemkey'] = '';

# blogs
$sections['blogs']['feature'] = 'feature_blogs';
$sections['blogs']['key'] = 'blogId';
$sections['blogs']['itemkey'] = 'postId';

# filegals
$sections['file_galleries']['feature'] = 'feature_file_galleries';
$sections['file_galleries']['key'] = 'page';
$sections['file_galleries']['itemkey'] = 'fileId';

# imagegals
$sections['galleries']['feature'] = 'feature_galleries';
$sections['galleries']['key'] = 'galleryId';
$sections['galleries']['itemkey'] = 'imageId';

# forums
$sections['forums']['feature'] = 'feature_forums';
$sections['forums']['key'] = 'forumId';
$sections['forums']['itemkey'] = 'postId';

# articles
$sections['cms']['feature'] = 'feature_articles';
$sections['cms']['key'] = 'topicId';
$sections['cms']['itemkey'] = 'articleId';

# trackers
$sections['trackers']['feature'] = 'feature_trackers';
$sections['trackers']['key'] = 'trackerId';
$sections['trackers']['itemkey'] = 'itemId';

# user
$sections['mytiki']['feature'] = '';
$sections['mytiki']['key'] = 'user';
$sections['mytiki']['itemkey'] = '';

# user messages
$sections['user_messages']['feature'] = 'feature_messages';
$sections['user_messages']['key'] = 'msgId';
$sections['user_messages']['itemkey'] = '';

# newsreader
$sections['newsreader']['feature'] = 'feature_newsreader';
$sections['newsreader']['key'] = 'serverId';
$sections['newsreader']['itemkey'] = 'id';

# mytiki
$sections['mytiki']['feature'] = '';

# chat
$sections['chat']['feature'] = 'feature_chat';
$sections['chat']['key'] = '';
$sections['chat']['itemkey'] = '';

# webmail
$sections['webmail']['feature'] = 'feature_webmail';
$sections['webmail']['key'] = 'msgId';
$sections['webmail']['itemkey'] = '';

# contacts
$sections['contacts']['feature'] = 'feature_contacts';
$sections['contacts']['key'] = 'contactId';
$sections['contacts']['itemkey'] = '';

# faq
$sections['faqs']['feature'] = 'feature_faqs';
$sections['faqs']['key'] = 'faqId';
$sections['faqs']['itemkey'] = '';

# quizzes
$sections['quizzes']['feature'] = 'feature_quizzes';
$sections['quizzes']['key'] = 'quizId';
$sections['quizzes']['itemkey'] = '';

# polls
$sections['poll']['feature'] = 'feature_polls';
$sections['poll']['key'] = 'pollId';
$sections['poll']['itemkey'] = '';

# surveys
$sections['surveys']['feature'] = 'feature_surveys';
$sections['surveys']['key'] = 'surveyId';
$sections['surveys']['itemkey'] = '';

# featured links
$sections['featured_links']['feature'] = 'feature_featuredLinks';
$sections['featured_links']['key'] = 'url';
$sections['featured_links']['itemkey'] = '';

# directories
$sections['directory']['feature'] = 'feature_directory';
$sections['directory']['key'] = 'directoryId';
$sections['directory']['itemkey'] = '';

# calendar
$sections['calendar']['feature'] = 'feature_calendar';
$sections['calendar']['key'] = 'calendarId';
$sections['calendar']['itemkey'] = 'calitmId';

# workflow
$sections['workflow']['feature'] = 'feature_workflow';
$sections['workflow']['key'] = '';
$sections['workflow']['itemkey'] = '';

# charts
$sections['charts']['feature'] = 'feature_charts';
$sections['charts']['key'] = '';
$sections['charts']['itemkey'] = '';

# maps
$sections['maps']['feature'] = 'feature_maps';
$sections['maps']['key'] = 'mapId';
$sections['maps']['itemkey'] = '';

# gmap
$sections['gmaps']['feature'] = 'feature_gmap';
$sections['gmaps']['key'] = '';
$sections['gmaps']['itemkey'] = '';

# categories
$sections['categories']['feature'] = 'feature_categories';
$sections['categories']['key'] = 'categId';
$sections['categories']['itemkey'] = '';

# games
$sections['games']['feature'] = 'feature_games';
$sections['games']['key'] = 'gameId';
$sections['games']['itemkey'] = '';

# html pages
$sections['html_pages']['feature'] = 'feature_html_pages';
$sections['html_pages']['key'] = 'pageId';
$sections['html_pages']['itemkey'] = '';

# swffix
$sections['swffix']['feature'] = 'feature_swffix';
$sections['workflow']['key'] = '';
$sections['workflow']['itemkey'] = '';

// *****************************************************************************
$sections_enabled = array();
foreach ($sections as $sec=>$dat) {
	$feat = $dat['feature'];
	if ($feat === '' or (isset($$feat) and $$feat == 'y')) {
		$sections_enabled[$sec] = $dat;
	}
}
ksort($sections_enabled);
// *****************************************************************************

$area = 'tiki';
$fullscreen = 'n';
$cookielist = array();

if ($user) {
	$display_timezone = $tikilib->get_user_preference($user,'display_timezone',$server_timezone);
} else {
	$display_timezone = $server_timezone;
}
$smarty->assign('display_timezone', $display_timezone);

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
if ( ! empty($section) ) $smarty->assign('section', $section);
ini_set('docref_root',$php_docroot);

/* Automatically set params used for absolute URLs - BEGIN */

$tikipath = dirname($_SERVER['SCRIPT_FILENAME']);
if ( substr($tikipath,-1,1) != '/' ) $tikipath .= '/';

$tikiroot = dirname($_SERVER['PHP_SELF']);
if ( substr($tikiroot,-1,1) != '/' ) $tikiroot .= '/';

if ( $https_port == 443 ) $https_port = '';
if ( $http_port == 80 ) $http_port = '';


// Detect if we are in HTTPS / SSL mode.
//
// Since $_SERVER['HTTPS'] will not be set on some installation, we may need to check port also.
//
// 'force_nocheck' option is used to set all absolute URI to https, but without checking if we are in https
//    This is useful in certain cases.
//    For example, this allow to have full HTTPS when using an entrance proxy that will use HTTPS connection with the client browser, but use an HTTP only connection to the server that hosts tikiwiki.
// 
$https_mode = false;
if ( ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' )
	|| ( $https_port == '' && $_SERVER['SERVER_PORT'] == 443 )
	|| ( $https_port > 0 && $_SERVER['SERVER_PORT'] == $https_port )
	|| $https_login == 'force_nocheck'
) $https_mode = true;

$url_scheme = $https_mode ? 'https' : 'http';
$url_host = (isset($_SERVER['SERVER_NAME'])) ? $_SERVER['SERVER_NAME']  : $_SERVER['HTTP_HOST'];
$url_port = $https_mode ? $https_port : $http_port;
$url_path = $tikiroot;
$base_host = $url_scheme.'://'.$url_host.(($url_port!='')?":$url_port":'');
$base_url = $url_scheme.'://'.$url_host.(($url_port!='')?":$url_port":'').$url_path;
$base_url_http = 'http://'.$url_host.(($http_port!='')?":$http_port":'').$url_path;
$base_url_https = 'https://'.$url_host.(($https_port!='')?":$https_port":'').$url_path;

$smarty->assign('tikipath', $tikipath);
$smarty->assign('tikiroot', $tikiroot);
$smarty->assign('url_scheme', $url_scheme);
$smarty->assign('url_host', $url_host);
$smarty->assign('url_port', $url_port);
$smarty->assign('url_path', $url_path);

$smarty->assign('base_host', $base_host);
$smarty->assign('base_url', $base_url);
$smarty->assign('base_url_http', $base_url_http);
$smarty->assign('base_url_https', $base_url_https);

// OBSOLETED vars (just for compatibility purposes)
$smarty->assign('http_domain', $http_domain = $url_host);
$smarty->assign('http_prefix', $http_prefix = $url_path);
$smarty->assign('http_login_url', $http_login_url = $login_url);
$smarty->assign('https_login_url', $https_login_url = $login_url);
if ( isset($https_login_required) && $https_login_required == 'y' ) {
	$tikilib->set_preference('https_login_required','');
	$smarty->assign('https_login', $https_login = 'required');
} elseif ( $https_login == 'y' ) $smarty->assign('https_login', $https_login = 'allowed');
elseif ( $https_login == 'n' ) $smarty->assign('https_login', $https_login = 'disabled');

/* Automatically set params used for absolute URLs - END */

if (isset($_SESSION['tiki_cookie_jar'])) {
	foreach ($_SESSION['tiki_cookie_jar'] as $nn=>$vv) {
		$cookielist[] = "$nn: '". addslashes($vv)."'";
	}
	if (count($cookielist)) {
		$headerlib->add_js("var tiki_cookie_jar=new Array();\ntiki_cookie_jar={\n". implode(",\n\t",$cookielist)."\n};",80);
	}
}

if (!empty($_SESSION['language'])) {
	$saveLanguage = $_SESSION['language']; // if register_globals is on variable and _SESSION are the same
}

if ($error_reporting_level == 1) {
	$error_reporting_level = ($tiki_p_admin == 'y') ? E_ALL: 0;
} elseif ($error_reporting_adminonly == 'y' and $tiki_p_admin != 'y') {
	$error_reporting_level = 0;
}
error_reporting($error_reporting_level);
if ($log_sql == 'y') {
	$dbTiki->LogSQL();
}

$smarty->assign('wiki_extras', 'n');

if (!isset($feature_bidi)) { $feature_bidi = 'n'; }
$smarty->assign('feature_bidi', $feature_bidi);

if ( isset($_REQUEST['stay_in_ssl_mode']) ) {
	// We stay in HTTPS / SSL mode if 'stay_in_ssl_mode' has an 'y' or 'on' value
	$stay_in_ssl_mode = ( $_REQUEST['stay_in_ssl_mode'] == 'y' || $_REQUEST['stay_in_ssl_mode'] == 'on' ) ? 'y' : 'n';
} else {
	// Set default value of 'stay_in_ssl_mode' to the current mode state
	$stay_in_ssl_mode = $https_mode ? 'y' : 'n';
}

// Show the 'Stay in SSL mode' checkbox only if we are already in HTTPS
$show_stay_in_ssl_mode = $https_mode ? 'y' : 'n';

$smarty->assign('login_url', $login_url);
$smarty->assign('show_stay_in_ssl_mode', $show_stay_in_ssl_mode);
$smarty->assign('stay_in_ssl_mode', $stay_in_ssl_mode);

if ($wiki_page_regex == 'strict') $page_regex = '([A-Za-z0-9_])([\.: A-Za-z0-9_\-])*([A-Za-z0-9_])';
elseif ($wiki_page_regex == 'full') $page_regex = '([A-Za-z0-9_]|[\x80-\xFF])([\.: A-Za-z0-9_\-]|[\x80-\xFF])*([A-Za-z0-9_]|[\x80-\xFF])';
else $page_regex = '([^\n|\(\)])((?!(\)\)|\||\n)).)*?';

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

if ( $useGroupHome == 'y' ) {
	$groupHome = $userlib->get_user_default_homepage($user);
	if ( $user != '' ) $groupHome = $tikilib->get_user_preference($user, 'homePage', $groupHome);
	if ( $groupHome != '' ) {
		if ( ! preg_match('/^(\/|https?:)/', $groupHome) ) {
			$wikiHomePage = $groupHome;
			$tikiIndex = 'tiki-index.php?page='.$wikiHomePage;
			$smarty->assign('wikiHomePage', $wikiHomePage);
		} else $tikiIndex = $groupHome;
	}
}

// Be absolutely sure we have a value for tikiIndex
if ( $tikiIndex == '' ) $tikiIndex = 'tiki-index.php';

$group = $userlib->get_user_default_group($user);
$smarty->assign('default_group',$group);

$user_dbl = 'y';
$diff_versions = 'n';

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

$user_style = $site_style = $style;

if (isset($_SESSION['style'])) {
	$user_style = $_SESSION['style'];
}

if ($feature_userPreferences == 'y') {
	if ($user) {
		$user_dbl = $tikilib->get_user_preference($user, 'user_dbl', 'y');
		$diff_versions = $tikilib->get_user_preference($user, 'diff_versions', 'n');
		if (isset($_REQUEST['style'])) {
			$site_style = $_REQUEST['style'];
		}
		if ($change_theme == 'y') {
			$user_style = $tikilib->get_user_preference($user, 'theme', $style);
			if ($user_style and (is_file("styles/$user_style") or is_file("styles/$tikidomain/$user_style"))) {
				$site_style = $user_style;
			}
		}
		if ($change_language == 'y') {
			if (isset($_REQUEST['switchLang'])) {
				$language = $_REQUEST['switchLang'];
				$tikilib->set_user_preference($user, 'language', $language);
			} else {
				$user_language = $tikilib->get_user_preference($user, 'language', $language);
				if ($user_language && $language != $user_language && file_exists("lang/$user_language/language.php")) {
					$language = $user_language;
				}
			}
		}
	} else {
		$site_style = $user_style;
	}
	$smarty->assign('language', $language);
} else {
	$site_style = $user_style;
}

if (!is_file("styles/$site_style") and !is_file("styles/$tikidomain/$site_style")) {
	$site_style = 'tikineat.css';
}
if ($tikidomain and is_file("styles/$tikidomain/$site_style")) {
	$site_style = "$tikidomain/$site_style";
}

# style
$smarty->assign('style', $style);           // that is the pref
$smarty->assign('site_style', $site_style); // that is the effective site style
$smarty->assign('user_style', $user_style); // that is the user-chosen style
include_once("csslib.php");
$transition_style = $csslib->transition_css('styles/'.$site_style);
if ( $transition_style != '' ) $headerlib->add_cssfile('styles/transitions/'.$transition_style,50);
$headerlib->add_cssfile('styles/'.$site_style,51);
$stlstl = split("-|\.", $site_style);
$style_base = $stlstl[0];

if($varcheck_errors!="") {
	$smarty->assign('msg',$varcheck_errors);
	$smarty->display('error.tpl');
}

if (!$user) {
	if (isset($_REQUEST['switchLang'])) {
		$language = $_REQUEST['switchLang'];
		$_SESSION['language'] = $language;
		$smarty->assign('language', $language);
	} elseif  (!empty($saveLanguage)) { // users not logged that change the preference
		$language = $saveLanguage;
		$smarty->assign('language', $language);
	}
} elseif (!empty($saveLanguage) && $feature_userPreferences != 'y' && $change_language == 'y') {
	$language = $saveLanguage;
	$smarty->assign('language', $language);
}

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

if (!empty($force_no_compression) && $force_no_compression) {
	ini_set('zlib.output_compression', 'off');
} else {
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
if ($feature_search == 'y' && $feature_search_fulltext != 'y' && $search_refresh_index_mode == 'random' ) {
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
	$wiki_discussion_string = $smarty->fetchLang($tikilib->get_preference('language', 'en'), 'wiki-discussion.tpl');
	$smarty->assign('wiki_discussion_string', $wiki_discussion_string);
}
// ------------------------------------------------------
// setup initial breadcrumb
$crumbs = array();
$crumbs[] = new Breadcrumb($siteTitle,'',$tikiIndex);
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

if ($feature_userlevels == 'y') {
	$mylevel = $tikilib->get_user_preference($user,'mylevel',1);
	if (isset($_REQUEST['level']) and isset($userlevels[$_REQUEST['level']]) and $user) {
		$tikilib->set_user_preference($user,"mylevel",$_REQUEST['level']);
		$mylevel = $_REQUEST['level'];
	}
	$smarty->assign('mylevel',$mylevel);
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
$smarty->assign_by_ref('cookie',$_SESSION['tiki_cookie_jar']);

// OpenID support
if( isset( $_SESSION['openid_userlist'] ) && isset( $_SESSION['openid_url'] ) )
{
	$smarty->assign( 'openid_url', $_SESSION['openid_url'] );
	$smarty->assign( 'openid_userlist', $_SESSION['openid_userlist'] );
}
else
{
	$smarty->assign( 'openid_url', '' );
	$smarty->assign( 'openid_userlist', array() );
}
?>
