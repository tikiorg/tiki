<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'wiki page';
$section_class = "tiki_wiki_page manage";	// This will be body class instead of $section

require_once ('tiki-setup.php');

$wikilib = TikiLib::lib('wiki');

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
if (!($info = $tikilib->get_page_info($page))) {
	$smarty->assign('msg', tra('Page cannot be found'));
	$smarty->display('error.tpl');
	die;
}
// Now check permissions to rename this page
$access->check_permission(array('view', 'rename'), tr('Rename wiki page'), 'wiki page', $page);

if ((isset($_REQUEST["rename"]) || isset($_REQUEST["confirm"])) && $access->checkOrigin()) {
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
			default:
				throw $e;
			}
		}
	}

	if ($result) {
		$perspectivelib = TikiLib::lib('perspective');
		$perspectivelib->replace_preference('wsHomepage', $page, $newName);

    // If user has checked "semantic_alias" (Use Semantic Alias to redirect the old page to the new one?)
		// We'll append on the renamed page's content the following string, where $page is the original name
		// "\r\n~tc~(alias($page))~/tc~"
		// We use the ~tc~ so that it doesn't make the page look ugly
		if (isset($_REQUEST["semantic_alias"]) && $_REQUEST["semantic_alias"]=="y") {
      if (isset($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
				$remoteIp = $_SERVER['REMOTE_ADDR'];
			} else {
				$remoteIp = '0.0.0.0';
			}

      // Obtain info on the page being renamed
      $infoRenamedPage = $tikilib->get_page_info($newName);
      $edit_data = $infoRenamedPage["data"];
			$pageHyphensForSpaces = str_replace(" ","-",$page); // Otherwise pages with spaces won't work
			$edit_data = $edit_data."\r\n~tc~ (alias($pageHyphensForSpaces)) ~/tc~";
			$edit_comment = tr('Page renamed from %0 to %1. Semantic alias created',$page,$newName);
			$res = TikiLib::lib('tiki')->update_page($newName, $edit_data, $edit_comment, $infoRenamedPage["user"], $remoteIp);
		}

		$access->redirect($wikilib->sefurl($newName));
	}
}
ask_ticket('rename-page');
include_once ('tiki-section_options.php');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-rename_page.tpl');
$smarty->display("tiki.tpl");
