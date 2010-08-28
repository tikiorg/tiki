<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'wiki page';
$section_class = "tiki_wiki_page manage";	// This will be body class instead of $section

require_once ('tiki-setup.php');
include_once ('lib/wiki/wikilib.php');

$access->check_feature('feature_wiki');

// Get the page from the request var or default it to HomePage
if (!isset($_REQUEST["page"])) {
	$smarty->assign('msg', tra("No page indicated"));
	$smarty->display("error.tpl");
	die;
} else {
	$page = $_REQUEST["page"];
	$smarty->assign_by_ref('page', $_REQUEST["page"]);
	$smarty->assign('newname', $_REQUEST["page"]);
}
if ( $tikilib->get_approved_page( $page ) ) {
	$smarty->assign('msg', tra("You cannot rename staging pages. Please rename the approved page instead."));
	$smarty->display("error.tpl");
	die;
}
if (!($info = $tikilib->get_page_info($page))) {
	$smarty->assign('msg', tra('Page cannot be found'));
	$smarty->display('error.tpl');
	die;
}
// Now check permissions to rename this page
$tikilib->get_perm_object($page, 'wiki page', $info);
$access->check_permission( array('tiki_p_view', 'tiki_p_rename') );

if (isset($_REQUEST["rename"]) || isset($_REQUEST["confirm"])) {
	check_ticket('rename-page');
	// If the new pagename does match userpage prefix then display an error
	$newName = isset($_REQUEST["confirm"]) ? $_REQUEST['badname'] : $_REQUEST['newpage'];
	if (stristr($newName, $prefs['feature_wiki_userpage_prefix']) == $newName) {
		$smarty->assign('msg', tra("Cannot rename page because the new name begins with reserved prefix") . ' (' . $prefs['feature_wiki_userpage_prefix'] . ').');
		$smarty->display("error.tpl");
		die;
	}

	$smarty->assign('newname', $newName);
	$result = false;
	if (!isset($_REQUEST["confirm"]) && $wikilib->contains_badchars($newName)) {
		$smarty->assign('page_badchars_display', $wikilib->get_badchars());
	} else {
		try {
			$result = $wikilib->wiki_rename_page($page, $newName);
		} catch (Exception $e) {
			switch($e->getCode()) {
			case 1:
				$smarty->assign('page_badchars_display', $wikilib->get_badchars());
				break;
			case 2:
				$smarty->assign('msg', tra("Page already exists"));
				break;
			}
		}
	}

	if ($result) {
		if ($prefs['feature_wikiapproval'] == 'y') {
			$stagingPageName = $prefs['wikiapproval_prefix'] . $page;
			if ($tikilib->page_exists($stagingPageName)) {
				$newStagingPageName = $prefs['wikiapproval_prefix'] . $newName;
				if (!$wikilib->wiki_rename_page($stagingPageName, $newStagingPageName)) {
					$smarty->assign('msg', tra("Cannot rename page because maybe new staging page name already exists"));
					$smarty->display("error.tpl");
					die;
				}
			}
		}
		global $perspectivelib; require_once 'lib/perspectivelib.php';
		$perspectivelib->replace_preference ('wsHomepage', $page, $newName ) ;
		if ($prefs['feature_sefurl'] == 'y') {
			include_once('tiki-sefurl.php');
			header('location: '. urlencode(filter_out_sefurl("tiki-index.php?page=$newName", $smarty, 'wiki')));
		} else {
			header('location: tiki-index.php?page=' . urlencode($newName));
		}
	}
}
ask_ticket('rename-page');
include_once ('tiki-section_options.php');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-rename_page.tpl');
$smarty->display("tiki.tpl");
