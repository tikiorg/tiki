<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-setup_base.php,v 1.63 2004-04-03 09:36:49 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

//print("tiki-setup_base 1: before include setup.php: ".$tiki_timer->elapsed()."<br />");
# switch smarty with commenting either line
#require_once("setup.php"); // smarty 2.4.1
require_once("setup_smarty.php"); // smarty 2.6.0rc1

require_once("db/tiki-db.php"); // smarty 2.6.0rc1
//print("tiki-setup_base 2: before include tikilib.php: ".$tiki_timer->elapsed()."<br />");
require_once("lib/tikilib.php");
require_once("lib/cache/cachelib.php");
require_once("lib/logs/logslib.php");

//print("tiki-setup_base 3: before rest of tiki-setup_base: ".$tiki_timer->elapsed()."<br />");
$tikilib = new TikiLib($dbTiki);
require_once("lib/userslib.php");
$userlib = new UsersLib($dbTiki);

// set session lifetime
$session_lifetime = $tikilib->get_preference('session_lifetime','0');
if ($session_lifetime > 0) {
    ini_set('session.gc_maxlifetime',$session_lifetime*60);
}

// is session data  stored in DB or in filesystem?
$session_db = $tikilib->get_preference('session_db','n');
if ($session_db == 'y') {
    include('db/local.php');
    $ADODB_SESSION_DRIVER=$db_tiki;
    $ADODB_SESSION_CONNECT=$host_tiki;
    $ADODB_SESSION_USER=$user_tiki;
    $ADODB_SESSION_PWD=$pass_tiki;
    $ADODB_SESSION_DB=$dbs_tiki;
    unset($db_tiki);
    unset($host_tiki);
    unset($user_tiki);
    unset($pass_tiki);
    unset($dbs_tiki);
    ini_set('session.save_handler','user');
    include('session/adodb-session.php');
}

if ( $tikilib->get_preference('sessions_onlycookie','disabled')=='enabled' ) {
    ini_set('url_rewriter.tags', '');  // stop URL session handling rewrites because session.use_trans_sid cannot be reset from code and next line doesn't stop rewrites
    ini_set('session.use_only_cookies', true);  // URL session handling is not safe or pretty - better to have none.
}

if ( $tikilib->get_preference('sessions_silent','disabled')=='disabled' or !empty($_COOKIE) ) {
    // enabing silent sessions mean a session is only started when a cookie is presented
    session_start();
}

// in the case of tikis on same domain we have to distinguish the realm
// changed cookie and session variable name by a name made with siteTitle 
$cookie_site = ereg_replace("[^a-zA-Z0-9]", "",
			$tikilib->get_preference('siteTitle','tikiwiki'));
$user_cookie_site = 'tiki-user-'.$cookie_site;

// check if the remember me feature is enabled
$rememberme = $tikilib->get_preference('rememberme', 'disabled');

// if remember me is enabled, check for cookie where auth hash is stored
// user gets logged in as the first user in the db with a matching hash
if ($rememberme != 'disabled') {
    if (isset($_COOKIE["$user_cookie_site"])) {
        if (!isset($user)and !isset($_SESSION["$user_cookie_site"])) {
            $user = $userlib->get_user_by_hash($_COOKIE["$user_cookie_site"]);
            $_SESSION["$user_cookie_site"] = $user;
        }
    }
}

// check what auth metod is selected. default is for the 'tiki' to auth users
$auth_method = $tikilib->get_preference('auth_method', 'tiki');

// if the auth method is 'web site', look for the username in $_SERVER

// if the server is IIS and they are using integrated login, the users set up by the admin might not be the same string as what IIS sends
// to solve this, we try a couple of variations in order, from most secure to least, to see if one matches.
if ($auth_method == 'ws') {
    if (isset($_SERVER['REMOTE_USER'])) {
        if ($userlib->user_exists($_SERVER['REMOTE_USER'])) {
            $_SESSION["$user_cookie_site"] = $_SERVER['REMOTE_USER'];
        }elseif	($userlib->user_exists(str_replace("\\\\", "\\",$_SERVER['REMOTE_USER']))) {
        	// Check for the domain\username with just one backslash
        	$_SESSION["$user_cookie_site"] = str_replace("\\\\", "\\",$_SERVER['REMOTE_USER']);
        }elseif ($userlib->user_exists(substr($_SERVER['REMOTE_USER'], strpos($_SERVER['REMOTE_USER'], "\\") + 2))){
        	// Check for the username without the domain name
        	$_SESSION["$user_cookie_site"] = substr($_SERVER['REMOTE_USER'], strpos($_SERVER['REMOTE_USER'], "\\") + 2);
        }
    }
}

// if the username is already saved in the session, pull it from there
if (isset($_SESSION["$user_cookie_site"])) {
    $user = $_SESSION["$user_cookie_site"];
} else {
    $user = NULL;
}

// ------------------------------------------------------
// DEAL WITH XSS-TYPE ATTACKS AND OTHER REQUEST ISSUES

// helper functions
function make_clean(&$var) {
	if ( is_array($var) ) {
		foreach ( $var as $key=>$val ) {
			make_clean($var[$key]);
		}
	} else {
//		$var = htmlspecialchars($var, ENT_QUOTES);
		$var = htmlspecialchars($var); // ideally use ENT_QUOTES but this is too aggressive for names like o'doyle etc.
	}
}

// call this from anywhere to restore a variable passed in $_GET
function get_unclean($var) {
	if ( is_array($var) ) {
		foreach ( $var as $key=>$val ) {
			$ret[$key] = get_unclean($val);
		}
	} else {
//		$ret = strtr($encoded,array_flip(get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES)));
		$ret = strtr($encoded,array_flip(get_html_translation_table(HTML_SPECIALCHARS))); // ENT_QUOTES needs to match make_clean
	}
	return $ret;
}

// deal with register_globals
if ( ini_get('register_globals') ) {
	foreach ( array($_ENV, $_GET, $_POST, $_COOKIE, $_SERVER) as $superglob ) {
		foreach ( $superglob as $key=>$val ) {
			if ( isset($GLOBALS[$key]) && $GLOBALS[$key]==$val ) { // if global has been set some other way
				// that is OK (prevents munging of $_SERVER with ?_SERVER=rubbish etc.)
				unset($GLOBALS[$key]);
			}
		}
	}
}

// deal with attempted <script> attacks and any other trash in URI
// note that embedded tags in post, post files and cookie must be handled
// specifically by code as they might be valid!
make_clean($_GET);
make_clean($_SERVER['QUERY_STRING']);
make_clean($_SERVER['REQUEST_URI']);

// rebuild in a safe order
$_REQUEST = array_merge($_COOKIE, $_POST, $_GET, $_ENV, $_SERVER);

// deal with old request globals
// Tiki uses them (admin for instance) so compatibility is required
if ( false ) { // if pre-PHP 4.1 compatibility is not required
	unset($GLOBALS['HTTP_GET_VARS']);
	unset($GLOBALS['HTTP_POST_VARS']);
	unset($GLOBALS['HTTP_COOKIE_VARS']);
	unset($GLOBALS['HTTP_ENV_VARS']);
	unset($GLOBALS['HTTP_SERVER_VARS']);
	unset($GLOBALS['HTTP_SESSION_VARS']);
	unset($GLOBALS['HTTP_POST_FILES']);
} else {
	$GLOBALS['HTTP_GET_VARS'] =& $_GET;
	$GLOBALS['HTTP_POST_VARS'] =& $_POST;
	$GLOBALS['HTTP_COOKIE_VARS'] =& $_COOKIE;
}

// mose : simulate strong var type checking for http vars
$patterns['int']   = "/^[0-9]*$/"; // *Id, offset,
$patterns['char']  = "/^[-_a-zA-Z0-9]*$/"; // sort_mode, 
$patterns['string']  = "/^[^<>\";&#]*$/"; // find, and such extended chars

$patterns['vars']  = "/^[-_a-zA-Z0-9]*$/"; // for variable keys

$vartype['offset'] = 'int';
$vartype['thresold'] = 'int';
$vartype['sort_mode'] = 'char';
$vartype['comments_offset'] = 'int';
$vartype['comments_thresold'] = 'int';
$vartype['comments_sort_mode'] = 'char';
$vartype['priority'] = 'int';
$vartype['theme'] = 'string';
$vartype['flag'] = 'char';
$vartype['lang'] = 'char';
$vartype['page'] = 'string';
$vartype['edit_mode'] = 'char';

function varcheck($array) {
  global $patterns,$vartype;
  if (isset($array) and is_array($array)) {
    foreach ($array as $rq=>$rv) {
      if (!preg_match($patterns['vars'],$rq)) {
        die(tra("Invalid variable name : "). htmlspecialchars($rq));
      } else {
        if (is_array($rv)) {
          varcheck($rv);
        } elseif (((substr($rq,-2,2) == 'Id' or (isset($vartype["$rq"]) and $vartype["$rq"] == 'int')) and !preg_match($patterns['int'],$rv))
          or ((isset($vartype["$rq"]) and $vartype["$rq"] == 'char') and  !preg_match($patterns['char'],$rv))
          or ((isset($vartype["$rq"]) and $vartype["$rq"] == 'string') and  !preg_match($patterns['string'],$rv))) {
          die(tra("Invalid variable value : "). "$rq = ". htmlspecialchars($rv));
        }
      }
    }
  }
}
varcheck($_REQUEST);
varcheck($_POST);
varcheck($_GET);
varcheck($_COOKIE);

// --------------------------------------------------------------

/** translate a English string
 * @param $content - English string
 * @param $lg - language - if not specify = global current language
 */
function tra($content) {
    global $lang_use_db;
    global $language;

    if ($lang_use_db != 'y') {
        if ($lg == "" || $lg == $language) {
           global $lang;
		if (file_exists("lang/$language/language_tra.php")) {
			include_once("lang/$language/language_tra.php");
		}
		else {
		      include_once("lang/$language/language.php");
		}
        }
        else
           include ("lang/$lg/language.php");
        if ($content) {
            if (isset($lang[$content])) {
                return $lang[$content];
            } else {
                return $content;
            }
        }
    } else {
        global $tikilib;

        $query = "select `tran` from `tiki_language` where `source`=? and `lang`=?";
        $result = $tikilib->query($query, array($content,$lg == ""? $language: $lg));
        $res = $result->fetchRow();

        if (!$res)
            return $content;

        if (!isset($res["tran"])) {
            global $record_untranslated;

            if ($record_untranslated == 'y') {
                $query = "insert into `tiki_untranslated` (`source`,`lang`) values (?,?)";

                //No eror checking here
                $tikilib->query($query, array($content,$language),-1,-1,false);
            }

            return $content;
        }

        return $res["tran"];
    }
}
/* \brief  substr with a utf8 string - works only with $start and $length positive or nuls
 * This function is the same as substr but works with multibyte
 * In a multybyte sequence, the first byte of a multibyte sequence that represents a non-ASCII character is always in the range 0xC0 to 0xFD
 * and it indicates how many bytes follow for this character.
 * All further bytes in a multibyte sequence are in the range 0x80 to 0xBF.
 */
if (function_exists('mb_substr')) {
    mb_internal_encoding("UTF-8");
}
else {
    function mb_substr($str, $start, $len = '', $encoding="UTF-8"){
        $limit = strlen($str);
        for ($s = 0; $start > 0;--$start) {// found the real start
            if ($s >= $limit)
                break;
            if ($str[$s] <= "\x7F")
                ++$s;
            else {
                ++$s; // skip length
                while ($str[$s] >= "\x80" && $str[$s] <= "\xBF")
                    ++$s;
            }
        }
        if ($len == '')
            return substr($str, $s);
        else
            for ($e = $s; $len > 0; --$len) {//found the real end
                if ($e >= $limit)
                    break;
                if ($str[$e] <= "\x7F")
                    ++$e;
                else {
                    ++$e;//skip length
                    while ($str[$e] >= "\x80" && $str[$e] <= "\xBF" && $e < $limit)
                        ++$e;
                       }
            }
        return substr($str, $s, $e - $s);
    }
}


// We might need to cache this on a per-user basis
// Cache cache
// function user_has_permission($user,$perm) 
if(!$cachelib->isCached("allperms")) {
	$allperms = $userlib->get_permissions(0, -1, 'permName_desc', '', '');
	$cachelib->cacheItem("allperms",serialize($allperms));
} else {
	$allperms = unserialize($cachelib->getCached("allperms"));
}
$allperms = $allperms["data"];

//Initializes permissions
foreach ($allperms as $vperm) {
	$perm = $vperm["permName"];
	$$perm = 'n';

	$smarty->assign("$perm", 'n');
}

// Permissions
// Get group permissions here
$perms = $userlib->get_user_permissions($user);
foreach ($perms as $perm) {
    //print("Asignando permiso global : $perm<br/>");
    $smarty->assign("$perm", 'y');

    $$perm = 'y';
}

// If the user can admin file galleries then assign all the file galleries permissions
if ($tiki_p_admin_file_galleries == 'y') {
    $perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'file galleries');

    foreach ($perms["data"] as $perm) {
        $perm = $perm["permName"];

        $smarty->assign("$perm", 'y');
        $$perm = 'y';
    }
}

if ($tiki_p_admin_workflow == 'y') {
    $perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'workflow');

    foreach ($perms["data"] as $perm) {
        $perm = $perm["permName"];

        $smarty->assign("$perm", 'y');
        $$perm = 'y';
    }
}

if ($tiki_p_admin_directory == 'y') {
    $perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'directory');

    foreach ($perms["data"] as $perm) {
        $perm = $perm["permName"];

        $smarty->assign("$perm", 'y');
        $$perm = 'y';
    }
}

if ($tiki_p_admin_charts == 'y') {
    $perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'charts');

    foreach ($perms["data"] as $perm) {
        $perm = $perm["permName"];

        $smarty->assign("$perm", 'y');
        $$perm = 'y';
    }
}

if ($tiki_p_blog_admin == 'y') {
    $perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'blogs');

    foreach ($perms["data"] as $perm) {
        $perm = $perm["permName"];

        $smarty->assign("$perm", 'y');
        $$perm = 'y';
    }
}

if ($tiki_p_admin_trackers == 'y') {
    $perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'trackers');

    foreach ($perms["data"] as $perm) {
        $perm = $perm["permName"];

        $smarty->assign("$perm", 'y');
        $$perm = 'y';
    }
}

if ($tiki_p_admin_galleries == 'y') {
    $perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'image galleries');

    foreach ($perms["data"] as $perm) {
        $perm = $perm["permName"];

        $smarty->assign("$perm", 'y');
        $$perm = 'y';
    }
}

if ($tiki_p_admin_forum == 'y') {
    $perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'forums');

    foreach ($perms["data"] as $perm) {
        $perm = $perm["permName"];

        $smarty->assign("$perm", 'y');
        $$perm = 'y';
    }
}

if ($tiki_p_admin_wiki == 'y') {
    $perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'wiki');

    foreach ($perms["data"] as $perm) {
        $perm = $perm["permName"];

        $smarty->assign("$perm", 'y');
        $$perm = 'y';
    }
}

if ($tiki_p_admin_faqs == 'y') {
    $perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'faqs');

    foreach ($perms["data"] as $perm) {
        $perm = $perm["permName"];

        $smarty->assign("$perm", 'y');
        $$perm = 'y';
    }
}

if ($tiki_p_admin_shoutbox == 'y') {
    $perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'shoutbox');

    foreach ($perms["data"] as $perm) {
        $perm = $perm["permName"];

        $smarty->assign("$perm", 'y');
        $$perm = 'y';
    }
}

if ($tiki_p_admin_quizzes == 'y') {
    $perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'quizzes');

    foreach ($perms["data"] as $perm) {
        $perm = $perm["permName"];

        $smarty->assign("$perm", 'y');
        $$perm = 'y';
    }
}

if ($tiki_p_admin_cms == 'y') {
    $perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'cms');

    foreach ($perms["data"] as $perm) {
        $perm = $perm["permName"];

        $smarty->assign("$perm", 'y');
        $$perm = 'y';
    }

    $perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'topics');

    foreach ($perms["data"] as $perm) {
        $perm = $perm["permName"];

        $smarty->assign("$perm", 'y');
        $$perm = 'y';
    }
}

//Gives admins all permissions
if ($user == 'admin' || ($user && $userlib->user_has_permission($user, 'tiki_p_admin'))) {
	foreach ($allperms as $vperm) {
		$perm = $vperm["permName"];
		$$perm = 'y';

		$smarty->assign("$perm", 'y');
	}
}

unset($allperms);

$tikiIndex = $tikilib->get_preference("tikiIndex", 'tiki-index.php');

$style = $tikilib->get_preference("style", 'moreneat.css');
$smarty->assign('style', $style);

$icon_style = $tikilib->get_preference("icon_style", 'default');
//$smarty->assign('icon_style', $icon_style); //btodoroff: I see no reason to need this
$icon_style_base=$icon_style;

$slide_style = $tikilib->get_preference("slide_style", 'slidestyle.css');
$smarty->assign('slide_style', $slide_style);

$feature_userPreferences = $tikilib->get_preference("feature_userPreferences", 'n');
$change_language = $tikilib->get_preference("change_language", 'y');
$change_theme = $tikilib->get_preference("change_theme", 'y');

$language = $tikilib->get_preference('language', 'en');

// Fix IIS servers not setting what they should set (ay ay IIS, ay ay)
if (!isset($_SERVER['QUERY_STRING']))
    $_SERVER['QUERY_STRING'] = '';

if (!isset($_SERVER['REQUEST_URI']) || empty($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] . '/' . $_SERVER['QUERY_STRING'];
}

// added for wirtual hosting suport
if (!isset($tikidomain)) {
    $tikidomain = "";
} else {
    $tikidomain .= "/";
}

$smarty->assign("tikidomain", $tikidomain);

// Debug console open/close
$smarty->assign('debugconsole_style',
    isset($_COOKIE["debugconsole"]) && ($_COOKIE["debugconsole"] == 'o') ? 'display:block;' : 'display:none;');

?>
