<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$inputConfiguration = array(
	array( 'staticKeyFilters' => array(
				'searchtext' => 'xss',
				'replacetext' => 'xss',
				'maxRecords' => 'digits',
				'offset' => 'digits',
				'paddingLength' => 'digits',
		)
	)
);

require_once ('tiki-setup.php');

$wikilib = TikiLib::lib('wiki');

$access->check_feature(array('feature_wiki'));
$access->check_permission(array('tiki_p_admin_wiki'));

// Search input
$searchtext = '';
$replacetext = '';
if (!empty($_REQUEST['replacetext'])) {
	$replacetext = $_REQUEST['replacetext'];	
} else {
	$replacetext = '';
}
if (!empty($_REQUEST['searchtext'])) {
	$searchtext = $_REQUEST['searchtext'];
}
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign('offset', $offset);

if (!empty($_REQUEST['categId'])) {
	$categFilter = array($_REQUEST['categId']);
	$smarty->assign('find_categId', $_REQUEST['categId']);
} else {
	$categFilter = array();
	$smarty->assign('find_categId', '');
}
if ($prefs['feature_categories'] == 'y') {
	$categlib = TikiLib::lib('categ');
	$categories = $categlib->getCategories(NULL, true, false);
	$smarty->assign('categories', $categories);
}
if (isset($_REQUEST["maxRecords"])) {
	$maxRecords = $_REQUEST["maxRecords"];
}
$smarty->assign('maxRecords', $maxRecords);

if (!isset($_REQUEST["paddingLength"])) {
	$paddingLength = 50;
} else {
	$paddingLength = $_REQUEST["paddingLength"];
}
$smarty->assign('paddingLength', $paddingLength);
if (isset($_REQUEST["casesensitive"]) && $_REQUEST["casesensitive"] == 'y') {
	$casesensitive = 'y';
} else {
	$casesensitive = 'n';
}
$smarty->assign('casesensitive', $casesensitive);

if (isset($_REQUEST['search']) && $searchtext) {
	$results = $wikilib->get_pages_contains($searchtext, $offset, $maxRecords, 'pageName_asc', $categFilter);
	$searchtextLength = strlen($searchtext);
	foreach ($results["data"] as &$r) {
		$pageLength = strlen($r["data"]);
		$curpos = -1;
		while ($curpos < $pageLength) {
			if ($casesensitive == 'y') {
				$curpos = strpos($r["data"], $searchtext, $curpos + 1);
			} else {
				$curpos = stripos($r["data"], $searchtext, $curpos + 1);
			}
			if ($curpos === false) {
				$r["beforeSnippet"][] = tra('This match was not case sensitive');
				$r["afterSnippet"][] = tra('This match was not case sensitive');
				$r["searchreplace"][] = '0:0:0'; 
				break;	
			}
			// can't use str_replace because it replaces all: we need to be more precise
			$snippetStart = max(0, $curpos - $paddingLength);
			$leftpartLength = $curpos - $snippetStart;
			$rightpartLength = min($paddingLength, $pageLength - $curpos - $searchtextLength); 
			$rightpartStart = $curpos + $searchtextLength;
			$foundtext = substr($r["data"], $curpos, $searchtextLength);
			
			$lefthash = md5($r["page_id"] . $r["version"] . $searchtext . $curpos);
			$righthash = md5($curpos . $searchtext . $r["version"] . $r["page_id"]);
			
			$beforeSnippet = substr($r["data"], $snippetStart, $leftpartLength) . $lefthash . $foundtext . $righthash . substr($r["data"], $rightpartStart, $rightpartLength);
			$beforeSnippet = htmlentities($beforeSnippet);
			$beforeSnippet = str_replace($lefthash, '<strong>', $beforeSnippet);
			$beforeSnippet = str_replace($righthash, '</strong>', $beforeSnippet);
			
			$afterSnippet = substr($r["data"], $snippetStart, $leftpartLength) . $lefthash . $replacetext . $righthash . substr($r["data"], $rightpartStart, $rightpartLength);
			$afterSnippet = htmlentities($afterSnippet);
			$afterSnippet = str_replace($lefthash, '<strong>', $afterSnippet);
			$afterSnippet = str_replace($righthash, '</strong>', $afterSnippet);
			
			$r["beforeSnippet"][] = $beforeSnippet;
			$r["afterSnippet"][] = $afterSnippet;
			$r["searchreplace"][] = ($r["page_id"] . ":" . $r["version"] . ":" . $curpos);
		}
	}
	$smarty->assign('cant', $results['cant']);
	$smarty->assign('results', $results['data']);
}

if (isset($_REQUEST['replace']) && $searchtext) {
	if (empty($_REQUEST['checked'])) {
		$message = tra('No items selected');
	} else {
		$last_page_id = 0;
		foreach ($_REQUEST['checked'] as $c) {
			list($page_id, $version, $curpos) = explode(":", $c);
			if ($last_page_id && $page_id == $last_page_id) {
				$version = $version + $versionadjuster;
				$curpos = $curpos + $curposadjuster;
			} else {
				$last_page_id = 0;
				$curposadjuster = 0;
				$versionadjuster = 0;
			}
			$page_info = $tikilib->get_page_info_from_id($page_id);
			if (!$page_info) {
				$message .= tra("Page cannot be found: ") . $page_id . "<br />";
				continue;
			}
			if ($page_info["version"] != $version) {
				$message .= tra("Page has changed since preview: ") . htmlentities($page_info["pageName"]) . "<br />";
				continue;
			}
			// do replacing
			$searchtextLength = strlen($searchtext);
			$data = $page_info["data"];
			$firstpart = substr($data, 0, $curpos);
			$lastpart = substr($data, $curpos + $searchtextLength);
			if (strtolower(substr($data, $curpos, $searchtextLength)) != strtolower($searchtext)) {
				$message .= tra("Page not updated due to error in replacing: ") . htmlentities($page_info["pageName"]) . "<br />";
				continue;	
			}
			$data = $firstpart . $replacetext . $lastpart;
			$tikilib->update_page($page_info["pageName"], $data, tra('Mass search and replace'), $user, $tikilib->get_ip_address());
			$message .= tra("Page updated: ") . htmlentities($page_info["pageName"]) . "<br />";
			$versionadjuster++;
			$curposadjuster = $curposadjuster + strlen($replacetext) - $searchtextLength;
			$last_page_id = $page_id;
		}
	}
	$smarty->assign('message', $message);
	
}

$smarty->assign('searchtext', $searchtext);
$smarty->assign('replacetext', $replacetext);

$smarty->assign('mid', 'tiki-search_replace.tpl');
$smarty->display("tiki.tpl");
