<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-setup_base.php,v 1.47 2003-12-24 01:17:23 redflo Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//print("tiki-setup_base 1: before include setup.php: ".$tiki_timer->elapsed()."<br />");
# switch smarty with commenting either line
#require_once("setup.php"); // smarty 2.4.1
require_once("setup_smarty.php"); // smarty 2.6.0rc1

//print("tiki-setup_base 2: before include tikilib.php: ".$tiki_timer->elapsed()."<br />");
require_once("lib/tikilib.php");
require_once("lib/cache/cachelib.php");

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
session_start();

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
if ($auth_method == 'ws') {
    if (isset($_SERVER['REMOTE_USER'])) {
        if ($userlib->user_exists($_SERVER['REMOTE_USER'])) {
            $_SESSION["$user_cookie_site"] = $_SERVER['REMOTE_USER'];
        }
    }
}

// if the username is already saved in the session, pull it from there
if (isset($_SESSION["$user_cookie_site"])) {
    $user = $_SESSION["$user_cookie_site"];
} else {
    $user = NULL;
}

function tra($content) {
    global $lang_use_db;

    if ($lang_use_db != 'y') {
        global $lang;

        if ($content) {
            if (isset($lang[$content])) {
                return $lang[$content];
            } else {
                return $content;
            }
        }
    } else {
        global $tikilib;

        global $language;
        $query = "select `tran` from `tiki_language` where `source`=? and `lang`=?";
        $result = $tikilib->query($query, array($content,$language));
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


foreach ($allperms as $vperm) {
    $perm = $vperm["permName"];

    if ($user != 'admin' && (!$user || !$userlib->user_has_permission($user, 'tiki_p_admin'))) {
        $$perm = 'n';

        $smarty->assign("$perm", 'n');
    } else {
        $$perm = 'y';

        $smarty->assign("$perm", 'y');
    }
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
if ($tiki_p_mantis_admin == 'y') {
    $perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'mantis');

    foreach ($perms["data"] as $perm) {
        $perm = $perm["permName"];

        $smarty->assign("$perm", 'y');
        $$perm = 'y';
    }
}

$tikiIndex = $tikilib->get_preference("tikiIndex", 'tiki-index.php');

$style = $tikilib->get_preference("style", 'moreneat.css');
$smarty->assign('style', $style);

$slide_style = $tikilib->get_preference("slide_style", 'slidestyle.css');
$smarty->assign('slide_style', $slide_style);

$feature_userPreferences = $tikilib->get_preference("feature_userPreferences", 'n');
$change_language = $tikilib->get_preference("change_language", 'y');
$change_theme = $tikilib->get_preference("change_theme", 'y');

$language = $tikilib->get_preference('language', 'en');

/* This seems to be done in tiki-setup.php
if ($feature_userPreferences == 'y') {
    // Check for FEATURES for the user
    $user_style = $tikilib->get_preference("style", 'moreneat.css');

    if ($user) {
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

    $smarty->assign('style', $style);
    $smarty->assign('language', $language);
}

$stlstl = explode('.', $style);
$style_base = $stlstl[0];
*/

// Fix IIS servers not setting what they should set (ay ay IIS, ay ay)
if (!isset($_SERVER['QUERY_STRING']))
    $_SERVER['QUERY_STRING'] = '';

if (!isset($_SERVER['REQUEST_URI']) || empty($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] . '/' . $_SERVER['QUERY_STRING'];
}

//really needed? (todo: check)
//if ($lang_use_db!='y') {
global $lang;
include_once('lang/' . $language . '/language.php');

//}

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
