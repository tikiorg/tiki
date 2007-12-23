<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin.php,v 1.128.2.3 2007-12-23 17:11:21 mose Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/admin/adminlib.php');

$tikifeedback = array();

if ($tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

function simple_set_toggle($feature) {
	global $_REQUEST, $tikilib, $smarty, $tikifeedback, $prefs;

	if (isset($_REQUEST[$feature]) && $_REQUEST[$feature] == "on") {
		if ((!isset($prefs[$feature]) || $prefs[$feature] != 'y')) {
			// not yet set at all or not set to y
			$tikilib->set_preference($feature, 'y');
			$tikifeedback[] = array('num'=>1,'mes'=>sprintf(tra("%s enabled"),$feature));
		}
	} else {
		if ((!isset($prefs[$feature]) || $prefs[$feature] != 'n')) {
			// not yet set at all or not set to n
			$tikilib->set_preference($feature, 'n');
			$tikifeedback[] = array('num'=>1,'mes'=>sprintf(tra("%s disabled"),$feature));
		}
	}
}

function simple_set_value($feature, $pref = '') {
	global $_REQUEST, $tikilib ,$prefs;
	if (isset($_REQUEST[$feature])) {
		if ( $pref != '' ) {
			$tikilib->set_preference($pref, $_REQUEST[$feature]);
			$prefs[$feature] = $_REQUEST[$feature];
		} else {
			$tikilib->set_preference($feature, $_REQUEST[$feature]);
		}
	}
}

function simple_set_int($feature) {
        global $_REQUEST, $tikilib, $smarty;
	if (isset($_REQUEST[$feature]) && is_numeric($_REQUEST[$feature])) {
		$tikilib->set_preference($feature, $_REQUEST[$feature]);
	}
}

function byref_set_value($feature, $pref = "") {
	global $_REQUEST, $tikilib, $smarty;
	simple_set_value($feature, $pref);
}

$crumbs[] = new Breadcrumb(tra('Administration'),
                              tra('Sections'),
                              'tiki-admin.php',
                              'Admin+Home',
                              tra('Help on Configuration Sections'));

$adminPage = '';
if (isset($_REQUEST["page"])) {
	$adminPage = $_REQUEST["page"];
	$helpUrl='';
	if ($adminPage == "features") {
		$admintitle = "Features"; //get_strings tra("Features")
		$description = "Enable/disable Tiki features here, but configure them elsewhere"; //get_strings tra("Enable/disable Tiki features here, but configure them elsewhere") TODO FOR EACH DESCRIPTION
		$helpUrl = "Features Admin";
		include_once ('tiki-admin_include_features.php');
	} else if ($adminPage == "general") {
		$admintitle = "General";//get_strings tra("General")
		$description = "General preferences and settings";//get_strings tra("General preferences and settings")
		$helpUrl = "General Admin";
		include_once ('tiki-admin_include_general.php');
	} else if ($adminPage == "login") {
		$admintitle = "Login";//get_strings tra("Login")
		$description = "User registration, login and authentication";//get_strings tra("User registration, login and authentication")
		$helpUrl = "Login Config";
		include_once ('tiki-admin_include_login.php');
	} else if ($adminPage == "wiki") {
		$admintitle = "Wiki";//get_strings tra("Wiki")
		$description = "Wiki settings";//get_strings tra("Wiki settings")
		include_once ('tiki-admin_include_wiki.php');
	} else if ($adminPage == "wikiatt") {
		$admintitle = "Wiki Attachments";//get_strings tra("Wiki Attachments")
		$description = "Wiki attachments";//get_strings tra("Wiki attachments")
		include_once ('tiki-admin_include_wikiatt.php');
	} else if ($adminPage == "gal") {
		$admintitle = "Image Galleries";//get_strings tra("Image Galleries")
		$helpUrl = "Image+Galleries+Config";
		$description = "Image galleries";//get_strings tra("Image galleries")
		include_once ('tiki-admin_include_gal.php');
	} else if ($adminPage == "fgal") {
		$admintitle = "File Galleries";//get_strings tra("File Galleries")
		$helpUrl = "File+Galleries+Config";
		$description = "File galleries";//get_strings tra("File galleries")
		include_once ('tiki-admin_include_fgal.php');
	} else if ($adminPage == "cms") {
		$admintitle = "Articles";//get_strings tra("Articles")
		$helpUrl = "Articles+Config";
		$description = "Article/CMS settings";//get_strings tra("Article/CMS settings")
		include_once ('tiki-admin_include_cms.php');
	} else if ($adminPage == "polls") {
		$admintitle = "Polls";//get_strings tra("Polls")
		$description = "Poll comments settings";//get_strings tra("Poll comments settings")
		include_once ('tiki-admin_include_polls.php');
	} else if ($adminPage == "blogs") {
		$admintitle = "Blogs";//get_strings tra("Blogs")
		$description = "Configuration options for all blogs on your site";//get_strings tra("Configuration options for all blogs on your site")
		include_once ('tiki-admin_include_blogs.php');
	} else if ($adminPage == "forums") {
		$admintitle = "Forums";//get_strings tra("Forums")
		$description = "Forums settings";//get_strings tra("Forums settings")
		include_once ('tiki-admin_include_forums.php');
	} else if ($adminPage == "faqs") {
		$admintitle = "FAQs";//get_strings tra("FAQs")
		$description = "FAQ comments settings";//get_strings tra("FAQ comments settings")
		include_once ('tiki-admin_include_faqs.php');
	} else if ($adminPage == "trackers") {
		$admintitle = "Trackers";//get_strings tra("Trackers")
		$description = "Trackers settings";//get_strings tra("Trackers settings")
		include_once ('tiki-admin_include_trackers.php');
	} else if ($adminPage == "webmail") {
		$admintitle = "Webmail";//get_strings tra("Webmail")
		$description = "Webmail";
		include_once ('tiki-admin_include_webmail.php');
	} else if ($adminPage == "rss") {
		$admintitle = "RSS feeds";//get_strings tra("RSS feeds")
		$description = "RSS settings";//get_strings tra("RSS settings")
		include_once ('tiki-admin_include_rss.php');
	} else if ($adminPage == "directory") {
		$admintitle = "Directory";//get_strings tra("Directory")
		$description = "Directory settings";//get_strings tra("Directory settings")
		include_once ('tiki-admin_include_directory.php');
	} else if ($adminPage == "userfiles") {
		$admintitle = "User Files";//get_strings tra("User files")
		$helpUrl = "User+Fles+Config";
		$description = "User files";//get_strings tra("User files")
		include_once ('tiki-admin_include_userfiles.php');
	} else if ($adminPage == "maps") {
		$admintitle = "Maps";//get_strings tra("Maps")
		$description = "Maps configuration";//get_strings tra("Maps configuration")
		include_once ('tiki-admin_include_maps.php');
	} else if ($adminPage == "metatags") {
		$admintitle = "Meta Tags";//get_strings tra("Meta Tags")
		$helpUrl = "Meta+Tags+Config";
		$description = "Meta Tags settings";//get_strings tra("Meta Tags settings")
		include_once ('tiki-admin_include_metatags.php');
	} else if ($adminPage == "search") {
		$admintitle = "Search";//get_strings tra("Search")
		$description = "Search settings";//get_strings tra("Search settings")
		include_once ('tiki-admin_include_search.php');
	} else if ($adminPage == "score") {
		$admintitle = "Score";//get_strings tra("Score")
		$description = "Score settings";//get_strings tra("Score settings")
		include_once ('tiki-admin_include_score.php');
	} else if ($adminPage == "projects") {
		$admintitle = "Projects";//get_strings tra("Projects")
		$description = "Projects configuration";//get_strings tra("Projects configuration")
		include_once ('tiki-admin_include_projects.php');
	} else if ($adminPage == "community") {
		$admintitle = "Community";//get_strings tra("Community")
		$description = "Community settings";//get_strings tra("Community settings")
		include_once ('tiki-admin_include_community.php');
	} else if ($adminPage == "siteid") {
		$admintitle = "Site Identity";//get_strings tra("Site Identity")
		$helpUrl = "Site+Logo+and+Identity+Config";
		$description = "Site Identity features";//get_strings tra("Site Identity features")
		include_once ('tiki-admin_include_siteid.php');
	} else if ($adminPage == "calendar") {
		$admintitle = "Calendar";//get_strings tra("Calendar")
		$description = "Calendar settings";//get_strings tra("Calendar settings")
		include_once ('tiki-admin_include_calendar.php');
	} else if ($adminPage == "intertiki") {
		$admintitle = "Intertiki";//get_strings tra("Intertiki")
		$description = "Intertiki settings";//get_strings tra("Intertiki settings")
		include_once ('tiki-admin_include_intertiki.php');
	} else if ($adminPage == "freetags") {
		$admintitle = "Freetags";//get_strings tra("Freetags")
		$description = "Freetags settings";//get_strings tra("Freetags settings")
		include_once ('tiki-admin_include_freetags.php');
	} else if ($adminPage == "gmap") {
		$admintitle = "Google Maps";//get_strings tra("Google Maps")
		$description = "Google Maps";//get_strings tra("Google Maps")
		$helpUrl = "gmap";
		include_once ('tiki-admin_include_gmap.php');
	} else if ($adminPage == "i18n") {
		$admintitle = "i18n";//get_strings tra("i18n")
		$description = "Internationalization";//get_strings tra("i18n")
		$helpUrl = "Ii18h";
		include_once ('tiki-admin_include_i18n.php');		
	} else if ($adminPage == "wysiwyg") {
		$admintitle = "wysiwyg";//get_strings tra("i18n")
		$description = "Wysiwyg editor";//get_strings tra("i18n")
		$helpUrl = "Wysiwyg Editor Admin";
		include_once ('tiki-admin_include_wysiwyg.php');		
	} else if ($adminPage == "copyright") {
		$admintitle = "Copyright";//get_strings tra("i18n")
		$description = "Copyright management";//get_strings tra("i18n")
		$helpUrl = "Copyright";
		include_once ('tiki-admin_include_copyright.php');		
	} else if ($adminPage == "category") {
		$admintitle = "Category";//get_strings tra("Category")
		$description = "Category";//get_strings tra("Category")
		$helpUrl = "Category";
		include_once ('tiki-admin_include_category.php');
	} else if ($adminPage == "module") {
		$admintitle = "Module";//get_strings tra("Module")
		$description = "Module";//get_strings tra("Module")
		$helpUrl = "Module";
		include_once ('tiki-admin_include_module.php');
	} else if ($adminPage == "look") {
		$admintitle = "Look &amp; Feel";//get_strings tra("Look &amp Feel")
		$description = "Customize look and feel of your Tiki";//get_strings tra("Customize look and feel of your Tiki")
		$helpUrl = "Look and Feel";
		include_once ('tiki-admin_include_look.php');
	} else if ($adminPage == "textarea") {
		$admintitle = "Text area";//get_strings tra("Text area")
		$description = "Text area";//get_strings tra("Text area")
		$helpUrl = "Text area";
		include_once ('tiki-admin_include_textarea.php');
	} else if ($adminPage == "multimedia") {
		$admintitle = "Multimedia";//get_strings tra("Multimedia")
		$description = "Multimedia";//get_strings tra("Multimedia")
		$helpUrl = "Multimedia";
		include_once ('tiki-admin_include_multimedia.php');
	}
	
	$url = 'tiki-admin.php'.'?page='.$adminPage;
	if (!$helpUrl) {$helpUrl = ucfirst($adminPage)."+Config";}
	$helpDescription = "Help on $admintitle Config";//get_strings tra("Help on $admintitle Config")
} else {
  $smarty->assign('headtitle', breadcrumb_buildHeadTitle($crumbs));
  $smarty->assign('description', $crumbs[0]->description);
	$headerlib->add_cssfile('css/admin.css');
}

if(isset($admintitle)) {
  $admintitle = tra($admintitle);
  $crumbs[] = new Breadcrumb($admintitle,
                              $description,
                              $url,
                              $helpUrl,
                              $helpDescription);

  $smarty->assign_by_ref('admintitle', $admintitle);
  $headtitle = breadcrumb_buildHeadTitle($crumbs);
  $smarty->assign_by_ref('headtitle', $headtitle);
}

if (!empty($_GET['forcecheck'])) {
  $tiki_release = $adminlib->get_last_version($tiki_branch);
  if ($tiki_release != $tiki_version) {
    $prefs['tiki_needs_upgrade'] = 'y';
  } else {
    $prefs['tiki_needs_upgrade'] = 'n';
    $tikifeedback[] = array('num'=>1,'mes'=>sprintf(tra("Current version is up to date : %s"),$tiki_version));
  }
  if ($feature_version_checks == 'y') {
    $tikilib->set_preference('tiki_needs_upgrade', $prefs['tiki_needs_upgrade']);
    $tikilib->set_preference('tiki_release', $tiki_release);
  }
}

if ($prefs['feature_version_checks'] == 'y') {
  if ($prefs['tiki_needs_upgrade'] == 'y' && $prefs['tiki_release'] == $tiki_version) {
    // need to reset $tiki_needs_upgrade in case new upgrades do not update $tiki_needs_upgrade in prefs
    $prefs['tiki_needs_upgrade'] = 'n';
    $tikilib->set_preference('tiki_needs_upgrade', 'n');
  }
  if ($prefs['tiki_needs_upgrade'] != 'y' and $tikilib->now > ($prefs['tiki_version_last_check'] + $prefs['tiki_version_check_frequency'])) {

    $tikilib->set_preference('tiki_version_last_check',$tikilib->now);

    $tiki_release = $adminlib->get_last_version($tiki_branch);
    $tikilib->set_preference('tiki_release',$tiki_release);

    if ($tiki_release != $tiki_version) {
      $prefs['tiki_needs_upgrade'] = 'y';
      $tikilib->set_preference('tiki_needs_upgrade', 'y');
    }
  }
}

$smarty->assign_by_ref('tikifeedback', $tikifeedback);

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('adminpage', $adminPage);
$smarty->assign('mid', 'tiki-admin.tpl');
if(isset($helpUrl)) $smarty->assign_by_ref('sectionhelp', $helpUrl);
if(isset($description)) $smarty->assign('description', $description);
$smarty->assign('trail', $crumbs);
$smarty->assign('crumb', count($crumbs)-1);
$smarty->display("tiki.tpl");

?>
