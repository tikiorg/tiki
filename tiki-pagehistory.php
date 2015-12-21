<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'wiki page';
$section_class = "tiki_wiki_page manage";	// This will be body class instead of $section
require_once ('tiki-setup.php');
$histlib = TikiLib::lib('hist');
require_once ('lib/wiki/renderlib.php');

$access->check_feature('feature_wiki');

if (!isset($_REQUEST["source"])) {
	$access->check_feature('feature_history');
} else {
	$access->check_feature('feature_source');
}
// Get the page from the request var or default it to HomePage
if (!isset($_REQUEST["page"])) {
	$smarty->assign('msg', tra("No page indicated"));
	$smarty->display("error.tpl");
	die;
} else {
	$page = $_REQUEST["page"];
	$smarty->assign_by_ref('page', $_REQUEST["page"]);
}

$real_compare = !empty($_REQUEST['compare']);
$auto_query_args = array('page', 'oldver', 'newver', 'show_all_versions');
foreach ($auto_query_args as $key => $value ) {
	if(isset($_GET[$value])){
		if($value != 'page'){
			$_REQUEST["compare"]="Compare";
			$_REQUEST["diff_style"]=(isset($_REQUEST["diff_style"]))?$_REQUEST["diff_style"]:"sidediff";
		}
	}
}

// Now check permissions to access this page
if (!isset($_REQUEST["source"])) {
	$access->check_permission('tiki_p_wiki_view_history', '', 'wiki page', $_REQUEST['page']);
} else {
	$access->check_permission('tiki_p_wiki_view_source', '', 'wiki page', $_REQUEST['page']);
}
$info = $tikilib->get_page_info($page);
if (empty($info)) {
	$smarty->assign('msg', tra('No page indicated'));
	$smarty->display('error.tpl');
	die;
}

$tikilib->get_perm_object($_REQUEST['page'], 'wiki page', $info);

if (isset($_REQUEST['preview'], $_REQUEST['flaggedrev'], $_REQUEST['page']) && $prefs['flaggedrev_approval'] == 'y' && $tiki_p_wiki_approve == 'y') {
	$targetFlag = null;

	if (isset($_REQUEST['approve'])) {
		$targetFlag = 'OK';
		$targetVersion = (int) $_REQUEST['approve'];
	} elseif (isset($_REQUEST['unapprove'])) {
		$targetFlag = 'REJECT';
		$targetVersion = (int) $_REQUEST['unapprove'];
	}

	if ($targetFlag) {
		$flaggedrevisionlib = TikiLib::lib('flaggedrevision');

		$flaggedrevisionlib->flag_revision($info['pageName'], $targetVersion, 'moderation', $targetFlag);
	}
}

$smarty->assign_by_ref('info', $info);
// If the page doesn't exist then display an error
//check_page_exits($page);
if (isset($_REQUEST["confirmAction"]) && $_REQUEST["confirmAction"] === 'delete' && isset($_REQUEST["checked"]) && $info["flag"] != 'L') {
	check_ticket('page-history');
	foreach ($_REQUEST["checked"] as $version) {
		$histlib->remove_version($_REQUEST["page"], $version);
	}
}
if ($prefs['feature_contribution'] == 'y') {
	$contributionlib = TikiLib::lib('contribution');
	$contributions = $contributionlib->get_assigned_contributions($page, 'wiki page');
	$smarty->assign_by_ref('contributions', $contributions);
	if ($prefs['feature_contributor_wiki'] == 'y') {
		$contributors = $logslib->get_wiki_contributors($info);
		$smarty->assign_by_ref('contributors', $contributors);
	}
}

$paginate = !isset($_REQUEST['source']) && !isset($_REQUEST['source_idx'])  && !$real_compare
		&& !isset($_REQUEST['bothver_idx']) && ((isset($_REQUEST['paginate']) && $_REQUEST['paginate'] == 'on')
		|| !isset($_REQUEST['paginate']));
$smarty->assign('paginate', $paginate);

if (isset($_REQUEST['history_offset']) && $paginate) {
	$history_offset = $_REQUEST['history_offset'];
} else {
	$history_offset = 1;
}
$smarty->assign('history_offset', $history_offset);

if (isset($_REQUEST['history_pagesize']) && $paginate) {
	$history_pagesize = $_REQUEST['history_pagesize'];
} else {
	$history_pagesize = $prefs['maxRecords'];
}
$smarty->assign('history_pagesize', $history_pagesize);

// fetch page history, but omit the actual page content (to save memory)
$history = $histlib->get_page_history($page, false, $history_offset, $paginate ? $history_pagesize : -1);
// To avoid duplicate current version
if(!$paginate) {
	unset($history[0]);
}
$smarty->assign('history_cant', $histlib->get_nb_history($page) - 1);

if ($prefs['flaggedrev_approval'] == 'y') {
	$flaggedrevisionlib = TikiLib::lib('flaggedrevision');

	if ($flaggedrevisionlib->page_requires_approval($page)) {
		$approved_versions = $flaggedrevisionlib->get_versions_with($page, 'moderation', 'OK');

		$smarty->assign('flaggedrev_approval', true);

		$info['approved'] = in_array($info['version'], $approved_versions);

		$new_history = array();

		foreach ($history as $version) {
			$version['approved'] = in_array($version['version'], $approved_versions);
			if ($tiki_p_wiki_view_latest == 'y' || $version['approved']) {
				$new_history[] = $version;
			}
		}

		$history = $new_history;
	}
}

if (!isset($_REQUEST['show_all_versions'])) {
	$_REQUEST['show_all_versions'] = "y";
}
$sessions = array();
if (count($history) > 0) {
	$lastuser = '';		// calculate edit session info
	$lasttime = 0;		// secs
	$idletime = 1800; 	// max gap between edits in sessions 30 mins? Maybe should use a pref?
	for ($i = 0, $cnt = count($history); $i < $cnt; $i++) {
		if ((isset($history[$i]['user']) && $history[$i]['user'] != $lastuser) || (isset($history[$i]['lastModif']) && $lasttime - $history[$i]['lastModif'] > $idletime)) {
			$sessions[] = $history[$i];
			//$history[$i]['session'] = $history[$i]['version'];
		} else if (count($sessions) > 0) {
			$history[$i]['session'] = $sessions[count($sessions)-1]['version'];
		}
		if(isset($history[$i]['user'])){
			$lastuser = $history[$i]['user'];
		}
		else {
			$lastuser = '';
		}
		if(isset($history[$i]['lastModif'])){
			$lasttime = $history[$i]['lastModif'];
		} else {
			$lasttime = 0;
		}
	}
	$csesh = count($sessions) + 1;
	foreach ($history as &$h) {	// move ending 'version' into starting 'session'
		if (!empty($h['session'])) {
			foreach ($history as &$h2) {
				if ($h2['version'] == $h['session']) {
					$h2['session'] = $h['version'];
				}
			}
			$h['session'] = '';
		}
	}
	if ($_REQUEST['show_all_versions'] == "n") {
		for ($i = 0, $cnt = count($history); $i < $cnt; $i++) {	// remove versions inside sessions
			if (!empty($history[$i]['session']) && $i < $cnt - 1) {
				$seshend = $history[$i]['session'];
				$i++;
				for ($i; $i < $cnt; $i++) {
					if ($history[$i]['version'] >= $seshend) {
						unset($history[$i]);
					} else {
						$i--;
						break;
					}
				}
			}
		}
	}
}
$smarty->assign('show_all_versions', $_REQUEST['show_all_versions']);
$history_versions = array();
$history_sessions = array();
reset($history);
foreach ($history as &$h) {	// as $h has been used by reference before it needs to be so again (it seems)
	$history_versions[] = (int)$h['version'];
	$history_sessions[] = isset($h['session']) ? (int)$h['session'] : 0;
}
$history_versions = array_reverse($history_versions);
$history_sessions = array_reverse($history_sessions);
$history_versions[] = $info["version"];	// current is last one
$history_sessions[] = 0;
$smarty->assign_by_ref('history', $history);

// for pagination
$smarty->assign('ver_cant', count($history_versions));

if (isset($_REQUEST['clear_versions'])) {
	unset($_REQUEST['clear_versions']);
	unset($_REQUEST['newver']);
	unset($_REQUEST['newver_idx']);
	unset($_REQUEST['oldver']);
	unset($_REQUEST['oldver_idx']);
	unset($_REQUEST['compare']);
	unset($_REQUEST['diff_style']);
}
// calculate version and offset
if (isset($_REQUEST['bothver_idx'])) {
	if ($_REQUEST['bothver_idx'] == 0) {
		$_REQUEST['bothver_idx'] = 1;
	}
	$_REQUEST['oldver_idx'] = $_REQUEST['bothver_idx'] - 1;
	$_REQUEST['newver_idx'] = $_REQUEST['bothver_idx'];
	if ($_REQUEST['show_all_versions'] == 'n' && !empty($history_sessions[$_REQUEST['bothver_idx']])) {
		$_REQUEST['oldver_idx'] = $_REQUEST['bothver_idx'];
	}
}
if (isset($_REQUEST['newver_idx'])) {
	$newver = $history_versions[$_REQUEST['newver_idx']];
} else {
	if (isset($_REQUEST['newver']) && $_REQUEST['newver'] > 0) {
		$newver = (int)$_REQUEST["newver"];
		if (in_array($newver, $history_versions)) {
			$_REQUEST['newver_idx'] = array_search($newver, $history_versions);
		} else {
			$_REQUEST['newver_idx'] = array_search($newver, $history_sessions);
		}
	} else {
		$newver = $history_versions[count($history_versions)-1];
		$_REQUEST['newver_idx'] = count($history_versions)-1;
	}
}
if (isset($_REQUEST['oldver_idx'])) {
	$oldver = $history_versions[$_REQUEST['oldver_idx']];
	if ($_REQUEST['show_all_versions'] == 'n' && !empty($history_sessions[$_REQUEST['oldver_idx']])) {
		$oldver = $history_sessions[$_REQUEST['oldver_idx']];
	}
} else {
	if (isset($_REQUEST['oldver']) && $_REQUEST['oldver'] > 0) {
		$oldver = (int)$_REQUEST["oldver"];
		if (in_array($oldver, $history_versions)) {
			$_REQUEST['oldver_idx'] = array_search($oldver, $history_versions);
		} else {
			$_REQUEST['oldver_idx'] = array_search($oldver, $history_sessions);
		}
	} else {
		$oldver = $history_versions[count($history_versions)-1];
		$_REQUEST['oldver_idx'] = count($history_versions)-1;
	}
}
if ($_REQUEST['oldver_idx'] + 1 == $_REQUEST['newver_idx']) {
	$_REQUEST['bothver_idx'] = $_REQUEST['newver_idx'];
}
// source view
if (isset($_REQUEST['source_idx'])) {
	$source = $history_versions[$_REQUEST['source_idx']];
} else {
	if (isset($_REQUEST['source'])) {
		$source = (int)$_REQUEST["source"];
		if ($source > 0) {
			$_REQUEST['source_idx'] = array_search($source, $history_versions);
		} else {
			$_REQUEST['source_idx'] = count($history_versions) - 1;
			$smarty->assign('noHistory', true);
		}
	}
}
if (isset($_REQUEST['preview_idx'])) {
	$preview = $history_versions[$_REQUEST['preview_idx']];
} else {
	if (isset($_REQUEST['preview_date'])) {
		$_REQUEST['preview'] = (int)$histlib->get_version_by_time($page, $_REQUEST["preview_date"]);
	}

	if (isset($_REQUEST['preview'])) {
		$preview = (int)$_REQUEST["preview"];
		if ($_REQUEST['preview'] > 0) {
			$_REQUEST['preview_idx'] = array_search($preview, $history_versions);
		} else {
			$_REQUEST['preview_idx'] = count($history_versions) - 1;
			$smarty->assign('noHistory', true);
		}
	}
}

if (isset($_REQUEST['version'])) $rversion = $_REQUEST['version'];

$smarty->assign('source', false);
if (isset($source)) {
	if ($source == '' && isset($rversion)) {
		$source = $rversion;
	}
	if ($source == $info["version"] || $source == 0) {
		$smarty->assign('sourced', $info["data"]);
		$smarty->assign('source', $info['version']);
	} else {
		$version = $histlib->get_version($page, $source);
		if ($version) {
			$smarty->assign('sourced', $version["data"]);
			$smarty->assign('source', $source);
		}
	}
}
$smarty->assign('preview', false);
if (isset($preview)) {
	if ($preview == '' && isset($rversion)) {
		$preview = $rversion;
	}
	if ($preview == $info['version'] || $preview == 0) {
        $previewd = (new WikiLibOutput($info, $info['data'], array('preview_mode' => true, 'is_html' => $info['is_html'])))->parsedValue;
		$smarty->assign('previewd', $previewd);
		$smarty->assign('preview', $info['version']);
	} else {
		$version = $histlib->get_version($page, $preview);
		if ($version) {
            $previewd = (new WikiLibOutput($version, $version['data'], array('preview_mode' => true, 'is_html' => $version['is_html'])))->parsedValue;
			$smarty->assign('previewd', $previewd);
			$smarty->assign('preview', $preview);
		}
	}

	$smarty->assign('flaggedrev_preview_approved', isset($approved_versions) && in_array($preview, $approved_versions));
}
if (isset($preview)) {
	$smarty->assign('current', $preview);
} else if (isset($source)) {
	$smarty->assign('current', $source);
} else if ($newver) {
	$smarty->assign('current', $newver);
} else if ($oldver) {
	$smarty->assign('current', $oldver);
} else {
	$smarty->assign('current', 0);
}
if ($prefs['feature_multilingual'] == 'y' && isset($_REQUEST['show_translation_history'])) {
	$multilinguallib = TikiLib::lib('multilingual');
	$smarty->assign('show_translation_history', 1);
	$sources = $multilinguallib->getSourceHistory($info['page_id']);
	$targets = $multilinguallib->getTargetHistory($info['page_id']);
} else {
	$sources = array();
	$targets = array();
}
$smarty->assign_by_ref('translation_sources', $sources);
$smarty->assign_by_ref('translation_targets', $targets);
if (isset($_REQUEST["diff2"])) { // previous compatibility
	if ($_REQUEST["diff2"] == '' && isset($rversion)) {
		$_REQUEST["diff2"] = $rversion;
	}
	$_REQUEST["compare"] = "y";
	$oldver = (int)$_REQUEST["diff2"];
}
if (!isset($newver)) {
	$newver = 0;
}
if ($prefs['feature_multilingual'] == 'y') {
	$multilinguallib = TikiLib::lib('multilingual');
	$langLib = TikiLib::lib('language');
	$languages = $langLib->list_languages();
	$smarty->assign_by_ref('languages', $languages);
	if (isset($_REQUEST["update_translation"])) {
		// Update translation button clicked. Forward request to edit page of translation.
		if (isset($_REQUEST['tra_lang'])) {
			$target = $_REQUEST['tra_lang'];
		} else {
			die('Invalid call to this page. Specify tra_lang');
		}
		// Find appropriate translation page
		$langs = $multilinguallib->getTranslations('wiki page', $info['page_id'], $info['pageName'], true);
		$pageName = '';
		foreach ($langs as $pageInfo) if ($target == (string)$pageInfo['lang']) {
			$pageName = $pageInfo['objName'];
		}
		// Build URI / Redirect
		$diff_style = isset($_REQUEST['diff_style']) ? rawurlencode($_REQUEST['diff_style']) : rawurlencode($info['is_html'] === '1' ? 'htmldiff' : $prefs['default_wiki_diff_style']);
		$comment = rawurlencode("Updating from $page at version {$info['version']}");
		if ($newver == 0) {
			$newver = $info['version'];
		}
		if ($pageName) {
			$uri = "tiki-editpage.php?page=$pageName&source_page=$page&diff_style=$diff_style&oldver=$oldver&newver=$newver&comment=$comment";
		} else {
			$uri = "tiki-edit_translation.php?page=$page";
		}
		header("Location: $uri");
		exit;
	}
}
$current_version = $info["version"];
$not_comparing = empty($_REQUEST['compare']) ? 'true' : 'false';

$headerlib->add_jq_onready(
<<<JS
\$("input[name=oldver], input[name=newver]").change(function () {
	var ver = parseInt(\$(this).val(), 10), ver2;
	if (ver == 0) { ver = $current_version; }
	if (\$(this).attr("name") == "oldver") {
		\$("input[name=newver]").each(function () {
			ver2 = parseInt(\$(this).val(), 10);
			if (ver2 == 0) { ver2 = $current_version; }
			\$(this).attr("disabled", (ver2 <= ver));
		});
	} else if (\$(this).attr("name") == "newver") {
		\$("input[name=oldver]").each(function () {
			ver2 = parseInt(\$(this).val(), 10);
			if (ver2 == 0) { ver2 = $current_version; }
			\$(this).attr("disabled", (ver2 >= ver));
		});
	}
});
if (\$("input[name=newver][checked=checked]").length) {
	\$("input[name=newver][checked=checked]").change();
	\$("input[name=oldver][checked=checked]").change();
} else if ($not_comparing) {
	\$("input[name=newver]:eq(0)").prop("checked", "checked").change();
	\$("input[name=oldver]:eq(1)").prop("checked", "checked").change();
}
JS
);
if (isset($_REQUEST["compare"])) {
	histlib_helper_setup_diff($page, $oldver, $newver);

	if (isset($approved_versions)) {
		$smarty->assign('flaggedrev_compare_approve', ! in_array($newver, $approved_versions));
	}
} else {
	$smarty->assign('diff_style', $info['is_html'] === '1' ? 'htmldiff' : $prefs['default_wiki_diff_style']);
}

if ($info["flag"] == 'L') {
	$smarty->assign('lock', true);
} else {
	$smarty->assign('lock', false);
}

if (isset($_REQUEST['nohistory'])) {
	$smarty->assign('noHistory', true);
}

ask_ticket('page-history');

TikiLib::events()->trigger(
	'tiki.wiki.view',
	array_merge(
		array(
			'type' => 'wiki page',
			'object' => $page,
			'user' => $GLOBALS['user'],
		),
		$info
	)
);

// disallow robots to index page:
$smarty->assign('page_user', $info['user']);
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

include_once ('tiki-section_options.php');
// Display the template
$smarty->assign('mid', 'tiki-pagehistory.tpl');
$smarty->display("tiki.tpl");

