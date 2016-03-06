<?php
/**
 * Tiki's entry point.
 *
 * @package Tiki
 * @copyright (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project. All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * @licence Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */
// $Id$

require_once ('check_composer_exists.php');

$inputConfiguration = array(
				array(
					'staticKeyFilters' => array(
						'action' => 'word',
						//'attach_comment' => '', TODO
						// 'atts_show' => '', TODO
						'best_lang' => 'alpha',
						'bl' => 'alpha',
						//'copyrightpage' => '', TODO
						'page' => 'pagename',
						'page_id' => 'digits',
						'pagenum' => 'digits',
						'page_ref_id' => 'digits',
						'mode' => 'word',
						'removeattach' => 'digits',
						'sort_mode' => 'word',
						//'structure' => '', TODO
						'version' => 'digits',
						'watch_action' => 'word',
						'watch_event' => 'word',
						//'watch_object' => 'word', TODO
						'approve' => 'text',
						'revision' => 'digits',
					)
				),
);

// Initialization
$section = 'wiki page';
$isHomePage = (!isset($_REQUEST['page']));
require_once('tiki-setup.php');

$multilinguallib = TikiLib::lib('multilingual');

if ( $prefs['feature_wiki_structure'] == 'y' ) {
	$structlib = TikiLib::lib('struct');
}

$wikilib = TikiLib::lib('wiki');
$statslib = TikiLib::lib('stats');
require_once ('lib/wiki/renderlib.php');
require_once('lib/debug/Tracer.php');

$auto_query_args = array(
				'page',
				'no_bl',
				'page_id',
				'pagenum',
				'fullscreen',
				'page_ref_id',
				'mode',
				'sort_mode',
				'machine_translate_to_lang',
				'version',
				'date',
				'itemId',
);

if ($prefs['feature_categories'] == 'y') {
	$categlib = TikiLib::lib('categ');
}

if (!empty($_REQUEST['machine_translate_to_lang'])) {
	$smarty->assign('machine_translate_to_lang', $_REQUEST['machine_translate_to_lang']);
} else {
	$smarty->assign('machine_translate_to_lang', '');
}

$access->check_feature('feature_wiki');

if (!isset($_SESSION['thedate'])) {
	$thedate = $tikilib->now;
} else {
	$thedate = $_SESSION['thedate'];
}

// Check if a WS is active
$perspectivelib = TikiLib::lib('perspective');
$activeWS = $perspectivelib->get_current_perspective(null);

// If there's a WS active and the WS has a homepage, then load the WS homepage
if ((!empty($activeWS)) and $isHomePage) {
	$preferences = $perspectivelib->get_preferences($activeWS);
	if (!empty($preferences['wsHomepage'])) {
		$_REQUEST['page'] = $preferences['wsHomepage'];
	}
}

// If a page have been requested, then show the page.
if (isset($_REQUEST['page_id'])) {
	$_REQUEST['page'] = $tikilib->get_page_name_from_id($_REQUEST['page_id']);
	//TODO: introduce a get_info_from_id to save a sql request
}

if ((!isset($_REQUEST['page']) || $_REQUEST['page'] == '') and !isset($_REQUEST['page_ref_id'])) {
	if ($globalperms->view) {
		$access->display_error('', tra('You do not have permission to view this page.'), '401');
	} else {
		$access->display_error('', tra('No name indicated for wiki page'));
	}
}

$use_best_language = $multilinguallib->useBestLanguage();

$info = null;

$structs_with_perm = array();
$structure = 'n';
$smarty->assign('structure', $structure);

if ( $prefs['feature_wiki_structure'] == 'y' ) {
	// Feature checks made in the function for structure language
	if (!$use_best_language && isset($_REQUEST['page'])) {
		$info = $tikilib->get_page_info($_REQUEST['page']);
		$langContext = $info['lang'];
	} else {
		$langContext = null;
	}
	$structlib->use_user_language_preferences($langContext);

	if (isset($_REQUEST['page_ref_id'])) {
		// If a structure page has been requested
		$page_ref_id = $_REQUEST['page_ref_id'];
	} else {
		// else check if page is the head of a structure
		$page_ref_id = $structlib->get_struct_ref_if_head($_REQUEST['page']);
	}

	//If a structure page isnt going to be displayed
	if (empty($page_ref_id)) {
		//Check to see if its a member of any structures
		if (isset($_REQUEST['structure']) && !empty($_REQUEST['structure'])) {
			$struct=$_REQUEST['structure'];
		} else {
			$struct='';
		}
		//Get the structures this page is a member of
		$structs = $structlib->get_page_structures($_REQUEST['page'], $struct);
		$structs_with_perm = Perms::filter(
			array( 'type' => 'wiki page' ),
			'object',
			$structs,
			array( 'object' => 'pageName' ),
			'view'
		);

		//If page is only member of one structure, display if requested
		$single_struct = count($structs_with_perm) == 1;
		if ((!empty($struct) || $prefs['feature_wiki_open_as_structure'] == 'y') && $single_struct) {
			$page_ref_id=$structs_with_perm[0]['req_page_ref_id'];
			$_REQUEST['page_ref_id']=$page_ref_id;
		}

	}
} elseif (!empty($_REQUEST['page_ref_id'])) {
	$smarty->assign('msg', tra('This feature is disabled').': feature_wiki_structure');
	$smarty->display('error.tpl');
	die;
}

if (!empty($page_ref_id)) {
	$page_info = $structlib->s_get_page_info($page_ref_id);

	$info = null;
	// others still need a good set page name or they will get confused.
	// comments of home page were all visible on every structure page
	$_REQUEST['page'] = $page_info['pageName'];
} else {
	$page_ref_id = '';
	$smarty->assign('showstructs', $structs_with_perm);
	$smarty->assign('page_ref_id', $page_ref_id);
}

$page = $_REQUEST['page'];
$smarty->assign_by_ref('page', $page);

$cat_type = 'wiki page';
$cat_objid = $page;

if ($prefs['tracker_wikirelation_redirectpage'] == 'y' && !isset($_REQUEST['admin'])) {
	$relatedItems = TikiLib::lib('relation')->get_object_ids_with_relations_from( 'wiki page', $page, 'tiki.wiki.linkeditem' );
	$relatedItem = reset($relatedItems);
	if ($relatedItem) {
		$url = 'tiki-view_tracker_item.php?itemId=' . $relatedItem;
		include_once('tiki-sefurl.php');
		header('location: '. filter_out_sefurl($url, 'trackeritem'));
	}
}

// Inline Ckeditor editor
if ($prefs['wysiwyg_inline_editing'] == 'y' && $page &&
		(	($tikilib->user_has_perm_on_object($user, $_REQUEST['page'], 'wiki page', 'edit')) ||
			($tikilib->user_has_perm_on_object($user, $_REQUEST['page'], 'wiki page', 'edit_inline')) )) {

	TikiLib::lib('wysiwyg')->setUpInlineEditor($_REQUEST['page']);		// init ckeditor

} else if (getCookie('wysiwyg_inline_edit', 'preview')) {
	setCookieSection('wysiwyg_inline_edit', 0, 'preview');	// kill cookie if pref off or no perms
}

$page = $_REQUEST['page'] = $wikilib->get_page_by_slug($page);

// Process page display options
$wikilib->processPageDisplayOptions();

#Propagate the fullscreen parameter to templates
if ( isset($_REQUEST['fullscreen']) ) {
	$fullscreen = $_REQUEST['fullscreen'];
} else {
	$fullscreen = 'n';
}
$smarty->assign('fullscreen', $fullscreen);

if (!$info || isset($_REQUEST['date']) || isset($_REQUEST['version'])) {
	if ($prefs['feature_wiki_use_date'] == 'y' && isset($_REQUEST['date'])) {
		// Date is required
		$histlib = TikiLib::lib('hist');

		try {
			$page_view_date = $histlib->get_view_date($_REQUEST['date']);

			if ($page_view_date < time()) {
				// Asked date must be before now
				$_REQUEST['version'] = $histlib->get_version_by_time($page, $page_view_date);
			}

		} catch (Exception $e) {
			// Wrong date format
			$msg = tra('Invalid date format');
			$smarty->assign('msg', $msg);
			$smarty->display('error.tpl');
			die;
		}
	}

	if ($prefs['feature_wiki_use_date'] == 'y' && isset($_REQUEST['version'])) {
		// Version is required
		$histlib = TikiLib::lib('hist');

		try {
			$info = $histlib->get_page_info($page, $_REQUEST['version']);
		} catch (Exception $e) {
			// Unknown version
			$msg = tra('This version does not exist');
			$smarty->assign('msg', $msg);
			$smarty->display('error.tpl');
			die;
		}

	} else {
		$info = $tikilib->get_page_info($page);
	}
}

// If the page doesn't exist then display an error
if (empty($info) && !($user && $prefs['feature_wiki_userpage'] == 'y' && strcasecmp($prefs['feature_wiki_userpage_prefix'].$user, $page) == 0)) {
	$isprefixed = false;
	$prefixes = explode(',', $prefs['wiki_prefixalias_tokens']);
	foreach ($prefixes as $p) {
		$p = trim($p);
		if (strlen($p) > 0 && TikiLib::strtolower(substr($page, 0, strlen($p))) == TikiLib::strtolower($p)) {
			$isprefixed = true;
		}
	}

	$referencedPages = $wikilib->get_pages_by_alias($page);
	$likepages = $wikilib->get_like_pages($page);

	if ($prefs['feature_wiki_pagealias'] == 'y' && count($referencedPages) == 1) {
		$newPage = $referencedPages[0];
		$isprefixed = true;
	} else if ($prefs['feature_wiki_1like_redirection'] == 'y' && count($likepages) == 1) {
		$newPage = $likepages[0];
		$isprefixed = true;
	}

	if (!$isprefixed && !empty($prefs['url_anonymous_page_not_found']) && empty($user)) {
		$access->redirect($prefs['url_anonymous_page_not_found']);
	}

	if ($user && $prefs['feature_wiki_userpage'] == 'y' && strcasecmp($prefs['feature_wiki_userpage_prefix'], $page) == 0) {
		$url = 'tiki-index.php?page='.$prefs['feature_wiki_userpage_prefix'].$user;
		if ($prefs['feature_sefurl'] == 'y') {
			include_once('tiki-sefurl.php');
			header('location: '. urlencode(filter_out_sefurl($url, 'wiki')));
		} else {
			header("Location: $url");
		}
		die;
	}

	if ( $prefs['feature_wiki_userpage'] == 'y'
				&& strcasecmp($prefs['feature_wiki_userpage_prefix'], substr($page, 0, strlen($prefs['feature_wiki_userpage_prefix']))) == 0
	) {
		$isUserPage = true;
	} else {
		$isUserPage = false;
	}

	/* if we have exactly one match, redirect to it */
	if (isset($newPage) && !$isUserPage) {
		$url = $wikilib->sefurl($newPage);

		// Process prefix alias with itemId append for pretty tracker pages
		$prefixes = explode(',', $prefs['wiki_prefixalias_tokens']);
		foreach ($prefixes as $p) {
			$p = trim($p);
			if (strlen($p) > 0 && TikiLib::strtolower(substr($page, 0, strlen($p))) == TikiLib::strtolower($p)) {
				$suffix = trim(substr($page, strlen($p)));
				if (!ctype_digit($suffix) && $suffix) {
					// allow escaped numerics as text
					$suffix = stripslashes($suffix);
					$semanticlib = TikiLib::lib('semantic');
					$items = $semanticlib->getItemsFromTracker($newPage, $suffix);
					if (count($items) > 1) {
						$msg = tra('There is more than one item in the tracker with this title');
						foreach ($items as $i) {
							$msg .= '<br /><a href="tiki-index.php?page=' . urlencode($newPage) . '&itemId=' . $i . '">' . $i . '</a>';
						}
						$smarty->assign('msg', $msg);
						$smarty->display('error.tpl');
						die;
					} else if (count($items)) {
						$suffix = $items[0];
					} else {
						// check for a number then the item title
						$suffix = preg_replace('/(\d+).*/', '$1', $suffix);

						if (!$suffix) {
							$msg = tra('There are no items in the tracker with this title');
							$smarty->assign('msg', $msg);
							$smarty->display('error.tpl');
							die;
						}
					}
				}
				if (ctype_digit($suffix)) {
					$_REQUEST['itemId'] = $suffix;
					$_REQUEST['page'] = $newPage;
					$_GET['itemId'] = $suffix;	// \ParserLib::parse_wiki_argvariable uses $_GET
					$_GET['page'] = $newPage;
					$page = $newPage;
					$info = $tikilib->get_page_info($_REQUEST['page']);

				}
			}
		}
	} else {
		$likepages = array_unique(array_merge($likepages, $referencedPages));
	}

	$smarty->assign_by_ref('likepages', $likepages);
	//$smarty->assign('create', $isUserPage? 'n': 'y');
	//$smarty->assign('filter', array('content' => $page,));
	//$access->display_error($page, tra('Page cannot be found'), '404');
}

if ( empty($info)
			&& $user
			&& $prefs['feature_wiki_userpage'] == 'y'
			&& ( strcasecmp($prefs['feature_wiki_userpage_prefix'] . $user, $page) == 0
					|| strcasecmp($prefs['feature_wiki_userpage_prefix'], $page) == 0
			)
) {

	header('Location: tiki-editpage.php?page='.$prefs['feature_wiki_userpage_prefix'].$user);
	die;
}

if ( isset($_REQUEST['switchlang'])
			&& $_REQUEST['switchlang'] == 'y'
			&& $prefs['feature_multilingual'] == 'y'
			&& $prefs['feature_sync_language'] == 'y'
			&& !empty($info['lang'])
			&& $prefs['language'] != $info['lang']
) {
	header('Location: tiki-switch_lang.php?language=' . $info['lang']);
	die;
} elseif ( $prefs['feature_multilingual'] == 'y'
			&& $prefs['feature_sync_language'] == 'y'
			&& !empty($info['lang'])
			&& $prefs['language'] != $info['lang']
) {
	$prefs['language'] = $info['lang'];
	TikiLib::lib('multilingual')->setupBiDi();
}

$page = $info['pageName'];

//Uncomment if we decide to translate wiki markup. For now we are going
//with translating rendered html content
//$translatedWikiMarkup = '';
//if (isset($_REQUEST['machine_translate_to_lang'])) {
//	$translatedWikiMarkup = generate_machine_translated_markup($info, $_REQUEST['machine_translate_to_lang']);
//}

if (isset($_REQUEST['approve'], $_REQUEST['revision']) && $_REQUEST['revision'] <= $info['version']) {
	$flaggedrevisionlib = TikiLib::lib('flaggedrevision');

	if ($flaggedrevisionlib->page_requires_approval($page)) {
		$perms = Perms::get('wiki page', $page);

		if ($perms->wiki_approve) {
			$flaggedrevisionlib->flag_revision($page, $_REQUEST['revision'], 'moderation', 'OK');
		}
	}
	$access->redirect($wikilib->sefurl($page));
}

$pageRenderer = new WikiRenderer($info, $user);
$objectperms = $pageRenderer->applyPermissions();

if ($prefs['flaggedrev_approval'] == 'y' && isset($_REQUEST['latest']) && $objectperms->wiki_view_latest) {
	$pageRenderer->forceLatest();
}

$pageCache = Tiki_PageCache::create()
	->disableForRegistered()
	->onlyForGet()
	->requiresPreference('memcache_wiki_output')
	->addValue('role', 'wiki-page-output')
	->addValue('page', $page)
	->addValue('locale', $prefs['language'])
	->addKeys($_GET, array_keys($_GET))
	->checkMeta('wiki-page-output-meta-timestamp', array('page' => $page,))
	->applyCache();

if ( $page_ref_id ) {
	$pageRenderer->setStructureInfo($page_info);
}

// Now check permissions to access this page
if ( ! $pageRenderer->canView ) {
	$access->display_error($page, tra('You do not have permission to view this page.'), '401');
}

// Convert page to structure
if (isset($_REQUEST['convertstructure']) && isset($structs) && count($structs) == 0) {
	$page_ref_id = $structlib->s_create_page(0, null, $page);
	header('Location: tiki-index.php?page_ref_id=' . $page_ref_id);
	exit;
}

if (isset($_REQUEST['copyrightpage'])) {
	$smarty->assign_by_ref('copyrightpage', $_REQUEST['copyrightpage']);
}

// BreadCrumbNavigation here
// Remember to reverse the array when posting the array

if (!isset($_SESSION['breadCrumb'])) {
	$_SESSION['breadCrumb']=Array();
}
if (!in_array($page, $_SESSION['breadCrumb'])) {
	if (count($_SESSION['breadCrumb'])>$prefs['userbreadCrumb']) {
		array_shift($_SESSION['breadCrumb']);
	}
	array_push($_SESSION['breadCrumb'], $page);
} else {
	// If the page is in the array move to the last position
	$pos = array_search($page, $_SESSION['breadCrumb']);
	unset($_SESSION['breadCrumb'][$pos]);
	array_push($_SESSION['breadCrumb'], $page);
}

// Now increment page hits since we are visiting this page
$tikilib->add_hit($page);

// Check if we have to perform an action for this page
// for example lock/unlock
if ( $objectperms->admin_wiki
		|| ($user and $objectperms->lock and ($prefs['feature_wiki_usrlock'] == 'y'))
) {
	if ( isset($_REQUEST['action']) ) {
		check_ticket('index');
		if ( $_REQUEST['action'] == 'lock' ) {
			$wikilib->lock_page($page);
			$pageRenderer->setInfo('flag', 'L');
			$info['flag'] = 'L';
		}
	}
}

if ( $objectperms->admin_wiki
		|| ($user and ($user == $info['user']) and $objectperms->lock and ($prefs['feature_wiki_usrlock'] == 'y'))
) {
	if ( isset($_REQUEST['action']) ) {
		check_ticket('index');
		if ( $_REQUEST['action'] == 'unlock' ) {
			$wikilib->unlock_page($page);
			$pageRenderer->setInfo('flag', 'U');
			$info['flag'] = 'U';
		}
	}
}

// Save to notepad if user wants to
if ( $user
			&& $objectperms->notepad
			&& $prefs['feature_notepad'] == 'y'
			&& isset($_REQUEST['savenotepad'])
) {
	check_ticket('index');
	$tikilib->replace_note($user, 0, $page, $info['data']);
}

// Process an undo here
if ( isset($_REQUEST['undo']) ) {
	if ( $pageRenderer->canUndo() ) {
		$access->check_authenticity(tra('Are you sure you want to undo the last change?'));

		$historylib = TikiLib::lib('hist');
		$last = $historylib->get_page_latest_version($page);
		if ( $last > 1 ) {
			$historylib->use_version($page, $last);
			// Restore page information
			$info = $tikilib->get_page_info($page);
			$pageRenderer->setInfos($info);
		} else {
			TikiLib::lib('errorreport')->report(tra('There is nothing to undo'));
		}
	}
}

if (isset($_REQUEST['refresh'])) {
	check_ticket('index');
	$tikilib->invalidate_cache($page);
}

include_once('tiki-section_options.php');

if ( isset($_REQUEST['pagenum']) && $_REQUEST['pagenum'] > 0 ) {
	$pageRenderer->setPageNumber((int) $_REQUEST['pagenum']);
}

$just_saved = false;
if (isset($_SESSION['saved_msg']) && $_SESSION['saved_msg'] == $info['pageName'] && $info['user'] == $user ) {
	// Generate the 'Page has been saved...' message
	require_once('lib/smarty_tiki/modifier.userlink.php');
	$smarty->assign('saved_msg', sprintf(tra('Page saved (version %d).'), $info['version']));
	unset($_SESSION['saved_msg']);
	$just_saved = true;
}

if ( $prefs['feature_wiki_attachments'] == 'y' && $prefs['feature_use_fgal_for_wiki_attachments'] != 'y' ) {
	if (isset($_REQUEST['removeattach'])) {
		check_ticket('index');
		$owner = $wikilib->get_attachment_owner($_REQUEST['removeattach']);
		if ( ($user && ($owner == $user) ) || $objectperms->wiki_admin_attachments ) {
			$access->check_authenticity();
			$wikilib->remove_wiki_attachment($_REQUEST['removeattach']);
		}
		$pageRenderer->setShowAttachments('y');
	}
	if (isset($_REQUEST['attach']) && ( $objectperms->wiki_admin_attachments || $objectperms->wiki_attach_files )) {
		check_ticket('index');
		// Process an attachment here
		if (isset($_FILES['userfile1'])&&is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
			$ret = $tikilib->attach_file(
				$_FILES['userfile1']['name'],
				$_FILES['userfile1']['tmp_name'],
				$prefs['w_use_db']== 'y'? 'db': 'dir'
			);
			if ($ret['ok']) {
				// Set "data" field only if we're using db
				if ( $prefs['w_use_db'] == 'y' ) {
					$wikilib->wiki_attach_file(
						$page,
						$_FILES['userfile1']['name'],
						$_FILES['userfile1']['type'],
						$_FILES['userfile1']['size'],
						$ret['data'],
						$_REQUEST['attach_comment'],
						$user,
						$ret['fhash']
					);
				} else {
					$wikilib->wiki_attach_file(
						$page,
						$_FILES['userfile1']['name'],
						$_FILES['userfile1']['type'],
						$_FILES['userfile1']['size'],
						'',
						$_REQUEST['attach_comment'],
						$user,
						$ret['fhash']
					);
				}
			} else {
				$access->display_error('', $ret['error']);
			}
		}
	}

	if ( isset($_REQUEST['sort_mode']) ) {
		$pageRenderer->setSortMode($_REQUEST['sort_mode']);
	}
	if ( isset( $_REQUEST['atts_show'] ) ) {
		$pageRenderer->setShowAttachments($_REQUEST['atts_show']);
	}
}

// Watches
if ($prefs['feature_user_watches'] == 'y') {
	if ($user && isset($_REQUEST['watch_event']) && !isset($_REQUEST['watch_group'])) {
		check_ticket('index');
		if (($_REQUEST['watch_action'] == 'add_desc' || $_REQUEST['watch_action'] == 'remove_desc') && !$objectperms->watch_structure ) {
			$access->display_error($page, tra('Permission denied'), '403');
		}
		$ret = true;
		if ($_REQUEST['watch_action']=='add') {
			$ret = $tikilib->add_user_watch(
				$user,
				$_REQUEST['watch_event'],
				$_REQUEST['watch_object'],
				'wiki page',
				$page,
				"tiki-index.php?page=$page"
			);
		} elseif ($_REQUEST['watch_action'] == 'add_desc') {
			$ret = $tikilib->add_user_watch(
				$user,
				$_REQUEST['watch_event'],
				$_REQUEST['watch_object'],
				'structure',
				$page,
				"tiki-index.php?page=$page&amp;structure=" . $_REQUEST['structure']
			);
		} elseif ($_REQUEST['watch_action'] == 'remove_desc') {
			$tikilib->remove_user_watch($user, $_REQUEST['watch_event'], $_REQUEST['watch_object'], 'structure');
		} else {
			$tikilib->remove_user_watch($user, $_REQUEST['watch_event'], $_REQUEST['watch_object']);
		}
		if (!$ret) {
			$access->display_error($page, 'Invalid Email');
		}
	}
}

$sameurl_elements=Array('pageName','page');

ask_ticket('index');

//add a hit
$statslib->stats_hit($page, 'wiki');
if ($prefs['feature_actionlog'] == 'y') {
	$logslib->add_action('Viewed', $page);
}

// Detect if we have a PDF export mod installed
$smarty->assign('pdf_export', ($prefs['print_pdf_from_url'] != 'none') ? 'y' : 'n');

// Display the Index Template
$pageRenderer->runSetups();

//TRANSLATING HTML
$page_content = (string) $smarty->getTemplateVars('parsed');		// convert from Tiki_Render_Lazy to string here
if (!empty($_REQUEST['machine_translate_to_lang'])) {
	$page_content = generate_machine_translated_content($page_content, $info, $_REQUEST['machine_translate_to_lang']);
	$smarty->assign('parsed', $page_content);
}

TikiLib::events()->trigger(
	'tiki.wiki.view',
	array_merge(
		(is_array($info) ? $info : array()),
		array(
			'type' => 'wiki page',
			'object' => $page,
			'user' => $GLOBALS['user'],
		)
	)
);

if ( $prefs['feature_forums'] && $prefs['feature_wiki_discuss'] == 'y' && $prefs['wiki_discuss_visibility'] == 'above' ) {
	include_once ('lib/comments/commentslib.php');
	$commentslib = new Comments($dbTiki);
	$comments_data = tra('Use this thread to discuss the page:') . " [tiki-index.php?page=".rawurlencode($page)."|$page]";
	$threadId = $commentslib->check_for_topic($page, $comments_data);
	$comments_coms = $commentslib->get_forum_topics($prefs['wiki_forum_id'],0,-1);
	$discuss_replies_cant = 0;
	foreach( $comments_coms as $topic ) {
		if ( $topic['threadId'] == $threadId ) {
			$discuss_replies_cant = $topic['replies'];
			break;
		}
	}
	$smarty->assign('discuss_replies_cant', $discuss_replies_cant);
}

$smarty->assign('info', $info);
$smarty->assign('mid', 'tiki-show_page.tpl');

$smarty->display('tiki-show_page.tpl');

// xdebug_dump_function_profile(XDEBUG_PROFILER_CPU);
// debug: print all objects


/**
 * generate machine translation of markup
 * @param $pageInfo
 * @param $targetLang
 * @return string
 */
function generate_machine_translated_markup($pageInfo, $targetLang)
{
	make_sure_machine_translation_is_enabled();
	$pageContent = $pageInfo['data'];
	$sourceLang = $pageInfo['lang'];
	return translate_text($pageContent, $sourceLang, $targetLang);
}

/**
 * generate machine translation of content
 * @param $pageContent
 * @param $pageInfo
 * @param $targetLang
 * @return string
 */
function generate_machine_translated_content($pageContent, $pageInfo, $targetLang)
{
	make_sure_machine_translation_is_enabled();
	$sourceLang = $pageInfo['lang'];
	return translate_text($pageContent, $sourceLang, $targetLang, true);
}


/**
 * generate machine translation of text
 * @param $text
 * @param $sourceLang
 * @param $targetLang
 * @internal param bool $html
 * @return string
 */
function translate_text($text, $sourceLang, $targetLang)
{
	$provider = new Multilingual_MachineTranslation;
	$translator = $provider->getHtmlImplementation($sourceLang, $targetLang);
	$translated = $translator->translateText($text);
	return $translated;
}

/**
 * check this Tiki has the Translation feature enabled
 */
function make_sure_machine_translation_is_enabled()
{
	global $prefs;

	$access = TikiLib::lib('access');
	if ($prefs['feature_machine_translation'] != 'y' || $prefs['lang_machine_translate_wiki' != 'y']) {
		$error_msg = tra('You have requested that this page be machine translated:') .
						' <b>' .
						$_REQUEST['page'] .
						'</b><p>' .
						tra('However, the Machine Translation feature is not enabled. Please enable this feature, or ask a site admin to do it.');
		$access->display_error($_REQUEST['page'], 'Cannot machine translate this page', '', true, $error_msg);
	}
}

