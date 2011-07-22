<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// If you put some traces in this script, and can't see them
// because the script automatically forwards to another URL
// with a call to header(), then you will not see the traces
// If you want to see the traces, set value below to true.
// WARNING: DO NOT COMMIT WITH TRUE!!!!
$dieInsteadOfForwardingWithHeader = false;
$tracesOn = false;


$inputConfiguration = array(
	array( 'staticKeyFilters' => array(
		'page' => 'pagename',
		'returnto' => 'pagename',
		'watch' => 'digits',
	) ),
	array( 'staticKeyUnset' => array(
		'edit',
	) ),
);

$section = "wiki page";
$section_class = "tiki_wiki_page manage";	// This will be body class instead of $section
require_once ('tiki-setup.php');
include_once ('lib/wiki/wikilib.php');
include_once ('lib/structures/structlib.php');
include_once ('lib/notifications/notificationlib.php');
require_once ("lib/wiki/editlib.php");

function guess_new_page_attributes_from_parent_pages($page, $page_info) {
	global $editlib, $smarty, $_REQUEST, $tikilib, $prefs, $need_lang;
	if (!$page_info) {
		//
		// This is a new page being created. See if we can guess some of its attributes
		// (ex: language) based on those of its parent pages.
		//
		$new_page_inherited_attributes = 
			$editlib->get_new_page_attributes_from_parent_pages($page, $page_info);
		if ($editlib->user_needs_to_specify_language_of_page_to_be_created($page, $page_info)
		    && isset($new_page_inherited_attributes['lang'])) {
		    // 
		    // Language is not set yet, but it COULD be guessed from parent pages.
		    // So, set it.
		    //
		    $_REQUEST['lang'] = $new_page_inherited_attributes['lang'];
		} 
		if ($editlib->user_needs_to_specify_language_of_page_to_be_created($page, $page_info, $new_page_inherited_attributes)) {
			// 
			// Language of new page was not defined, and could not be guessed from the
			// parent pages. User will have to specify it explicitly.
			//
			$languages = $tikilib->list_languages(false, true);
			$smarty->assign('languages', $languages);
			$smarty->assign('default_lang', $prefs['language']);
			$need_lang = true;
			$smarty->assign('_REQUEST', $_REQUEST);
		}
	}
}

function translationsToThisPageAreInProgress($page_id) {
	global $multilinguallib;

	include_once("lib/multilingual/multilinguallib.php");

	$translations_in_progress = $multilinguallib->getTranslationsInProgressFlags($page_id);
	$answer = count($translations_in_progress) > 0;
	return $answer;

}

function execute_module_translation() { 
	global $smarty;
// will show the language of the available translations. Chnage to 'n' to show the page name
	$params['show_language'] = 'y';
// flag to indicate that the module is appearing within the notification area of the edit page
	$params['from_edit_page'] = 'y';
	$params['nobox'] = 'y';
	$module_reference = array(
		'name' => 'translation',
			'params' => array( 'show_language' => $params['show_language'], 'from_edit_page' => $params['from_edit_page'], 'nobox' => $params['nobox'] )
	);

	global $modlib; require_once 'lib/modules/modlib.php';	

	$out = $modlib->execute_module( $module_reference );
	$smarty->assign('content_of_update_translation_section', $out);
}

// Define all templates files that may be used with the 'zoom' feature
$zoom_templates = array('wiki_edit', 'tiki-editpage');

$access->check_feature('feature_wiki');

if ($editlib->isNewTranslationMode() || $editlib->isUpdateTranslationMode()) {
	$translation_mode = 'y';
	include_once("lib/multilingual/multilinguallib.php");
} else {
	$translation_mode = 'n';
}
$smarty->assign('translation_mode', $translation_mode);

// If page is blank (from quickedit module or wherever) tell user -- instead of editing the default page
// Dont get the page from default HomePage if not set (surely this would always be an error?)
if (empty($_REQUEST["page"])) { 
	$smarty->assign('msg', tra("You must specify a page name, it will be created if it doesn't exist."));
	$smarty->display("error.tpl");
	die;
}

$page = $_REQUEST["page"];
$smarty->assign('page', $page);
$info = $tikilib->get_page_info($page);

// String use to lock the page currently edit.
$editLockPageId = 'edit_lock_' . (isset($info['page_id']) ? (int) $info['page_id'] : 0);

// 2010-01-26: Keep in active until translation refactoring is done.
 if ($editlib->isNewTranslationMode() || $editlib->isUpdateTranslationMode()) {
 	$editlib->prepareTranslationData();
 }
$editlib->make_sure_page_to_be_created_is_not_an_alias($page, $info);
guess_new_page_attributes_from_parent_pages($page, $info);
 
if ($translation_mode === 'n' && translationsToThisPageAreInProgress($info['page_id'])) {
	$smarty->assign('prompt_for_edit_or_translate', 'y');
	include_once('modules/mod-func-translation.php');
	execute_module_translation();	
} else {
	$smarty->assign('prompt_for_edit_or_translate', 'n');
}

// wysiwyg decision
include 'lib/setup/editmode.php';

$auto_query_args = array('wysiwyg','page_id','page', 'returnto', 'lang', 'hdr');

$smarty->assign_by_ref('page', $_REQUEST["page"]);
// Permissions
$tikilib->get_perm_object($page, 'wiki page', $info, true);
if ($tiki_p_edit !== 'y') {
	if (empty($user)) {
		global $cachelib; include_once('lib/cache/cachelib.php');
		$cacheName = $tikilib->get_ip_address().$tikilib->now;
		$cachelib->cacheItem($cacheName, http_build_query($_REQUEST, '', '&'), 'edit');
		$smarty->assign('urllogin', "tiki-editpage.php?cache=$cacheName");
	}
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to edit this page."));
	$smarty->display("error.tpl");
	die;
}
// Anti-bot feature: if enabled, anon user must type in a code displayed in an image
if (isset($_REQUEST['save']) && (!$user || $user === 'anonymous') && $prefs['feature_antibot'] === 'y') {
	if(!$captchalib->validate()) {
		$smarty->assign('msg', $captchalib->getErrors());
		$smarty->display("error.tpl");
		die;
	}
}

$page_ref_id = '';
if (isset($_REQUEST["page_ref_id"])) {
	$page_ref_id = $_REQUEST["page_ref_id"];
}

$smarty->assign('page_ref_id',$page_ref_id);
//Is new page to be inserted into structure?
if (isset($_REQUEST["current_page_id"])) {
	if (empty($_REQUEST['page'])) {
		$smarty->assign('msg', tra("You must specify a page name, it will be created if it doesn't exist."));
		$smarty->display("error.tpl");
		die;
	}

	$structure_info = $structlib->s_get_structure_info($_REQUEST['current_page_id']);
	if ( ($tiki_p_edit != 'y' && !$tikilib->user_has_perm_on_object($user,$structure_info["pageName"],'wiki page','tiki_p_edit')) || (($tiki_p_edit_structures != 'y' && !$tikilib->user_has_perm_on_object($user,$structure_info["pageName"],'wiki page','tiki_p_edit_structures')) ) ) {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("You do not have permission to edit this page."));
		$smarty->display("error.tpl");
		die;
	}

	$smarty->assign('current_page_id',$_REQUEST["current_page_id"]);
	if (isset($_REQUEST["add_child"])) {
		$smarty->assign('add_child', "true");
	}
} else {
	$smarty->assign('current_page_id',0);
	$smarty->assign('add_child', false);
}

function compare_import_versions($a1, $a2) {
	return $a1["version"] - $a2["version"];
}

if (isset($_REQUEST['cancel_edit'])) {
	$tikilib->semaphore_unset($page, $_SESSION[$editLockPageId]);
	if (!empty($_REQUEST['returnto'])) {	// came from wikiplugin_include.php edit button
		$url = "location:".$wikilib->sefurl($_REQUEST['returnto']);
	} else {
		$url = "location:".$wikilib->sefurl($page);
		if (!empty($_REQUEST['page_ref_id'])) {
			$url .= '&page_ref_id='.$_REQUEST['page_ref_id'];
		}
	}	

	if ($prefs['feature_multilingual'] === 'y' && $prefs['feature_best_language'] === 'y' && isset($info['lang']) && $info['lang'] !== $prefs['language']) {
		$url .= '&no_bl=y';
	}

	if ($dieInsteadOfForwardingWithHeader) die ("-- tiki-editpage: Dying before first call to header(), so we can see traces. Forwarding to: \$url='$url'");
	header($url);
	die;
}
if (isset($_REQUEST['minor'])) {
	$_REQUEST['isminor'] = 'on';
	$_REQUEST['save'] = true;
}

if( $user && $prefs['feature_user_watches'] === 'y' ) {
	$isFormSubmit = isset($jitRequest['edit']);
	if( $tikilib->page_exists($page) ) {
		$currentlyWatching = (bool) $tikilib->user_watches( $user, 'wiki_page_changed', $page, 'wiki page' );
	} else {
		// New pages get default watch checked for authors
		$currentlyWatching = ($prefs['wiki_watch_author'] === 'y');
	}

	$requestedWatch = isset($_REQUEST['watch']) && $isFormSubmit;
	$smarty->assign( 'show_watch', 'y' );
	$smarty->assign( 'watch_checked', ( ($currentlyWatching && !$isFormSubmit) || $requestedWatch) ? 'y' : 'n' );
} else {
	$currentlyWatching = false;
	$requestedWatch = false;
	$smarty->assign( 'show_watch_controls', 'n' );
}

if (isset($_REQUEST['partial_save'])) {
	$_REQUEST['save'] = true;
}

if (isset($_REQUEST['hdr'])) {
	$smarty->assign('hdr', $_REQUEST['hdr']);
}

if (isset($_REQUEST['pos'])) {
	$smarty->assign('pos', $_REQUEST['pos']);
}

if (isset($_REQUEST['cell'])) {
	$smarty->assign('cell', $_REQUEST['cell']);
}

// We set empty wiki page name as default here if not set (before including Tiki modules)
if ($prefs['feature_warn_on_edit'] === 'y') {
	$editpageconflict = 'n';
	$beingEdited = 'n';
	$semUser = '';
	$u = $user? $user: 'anonymous';
	if (!empty($page) && ($page !== 'sandbox' || $page === 'sandbox' && $tiki_p_admin === 'y')) {
		if (!isset($_REQUEST['save'])) {
			if ($tikilib->semaphore_is_set($page, $prefs['warn_on_edit_time'] * 60) && $tikilib->get_semaphore_user($page) !== $u) {
				$editpageconflict = 'y';
			} elseif ($tiki_p_edit === 'y') {
				$_SESSION[$editLockPageId] = $tikilib->semaphore_set($page);
			}
			$semUser = $tikilib->get_semaphore_user($page);
			$beingedited = 'y';
		} else {
			if (!empty($_SESSION[$editLockPageId])) {
				$tikilib->semaphore_unset($page, $_SESSION[$editLockPageId]);
			}
		}
	}
	if ($editpageconflict === 'y' && !isset($_REQUEST["conflictoverride"]) ) {
		include_once('lib/smarty_tiki/modifier.userlink.php');
		$msg = tra("This page is being edited by ") .
			smarty_modifier_userlink($semUser) . ". " . 
			tra("Please check with the user before editing the page,
			otherwise the changes will be stored as two separate versions in the history and
			you will have to manually merge them later. ") ;
		$msg .= '<br /><br /><a href="tiki-editpage.php?page=';
		$msg .= urlencode($page);
		$msg .= '&conflictoverride=y">' . tra('Override lock and carry on with edit') . '</a>';
		$smarty->assign('msg',$msg);
		$smarty->assign('errortitle',tra('Page is currently being edited'));
		$smarty->display("error.tpl");
		die;
	}
}
$category_needed = false;
$contribution_needed = false;
if (isset($_REQUEST['lock_it']) && $_REQUEST['lock_it'] === 'on') {
	$lock_it = 'y';
} else {
	$lock_it = 'n';
}
if (isset($_REQUEST['comments_enabled']) && $_REQUEST['comments_enabled'] === 'on') {
	$comments_enabled = 'y';
} else {
	$comments_enabled = 'n';
}
$hash = array();
$hash['lock_it'] = $lock_it;
$hash['comments_enabled'] = $comments_enabled;
if (!empty($_REQUEST['contributions'])) {
	$hash['contributions'] = $_REQUEST['contributions'];
}
if (!empty($_REQUEST['contributors'])) {
	$hash['contributors'] = $_REQUEST['contributors'];
}
if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
	check_ticket('edit-page');
	require ("lib/mail/mimelib.php");
	$fp = fopen($_FILES['userfile1']['tmp_name'], "rb");
	$data = '';
	while (!feof($fp)) {
		$data .= fread($fp, 8192 * 16);
	}
	fclose ($fp);
	$name = $_FILES['userfile1']['name'];
	$output = mime::decode($data);
	$parts = array();
	parse_output($output, $parts, 0);
	$last_part = '';
	$last_part_ver = 0;
	usort($parts, 'compare_import_versions');
	foreach ($parts as $part) {
		if ($part["version"] > $last_part_ver) {
			$last_part_ver = $part["version"];
			$last_part = $part["body"];
		}
		if (isset($part["pagename"])) {
			$pagename = urldecode($part["pagename"]);
			$version = urldecode($part["version"]);
			$author = urldecode($part["author"]);
			$lastmodified = $part["lastmodified"];
			if (isset($part["description"])) {
				$description = $part["description"];
			} else {
				$description = '';
			}
			$pageLang = isset($part["lang"])? $part["lang"]: "";
			$authorid = urldecode($part["author_id"]);
			if (isset($part["hits"]))
				$hits = urldecode($part["hits"]);
			else
				$hits = 0;
			$ex = substr($part["body"], 0, 25);
			//print(strlen($part["body"]));
			$msg = '';
			if (isset($_REQUEST['save']) && $prefs['feature_contribution'] === 'y' && $prefs['feature_contribution_mandatory'] === 'y' && (empty($_REQUEST['contributions']) || count($_REQUEST['contributions']) <= 0)) {
				$contribution_needed = true;
				$smarty->assign('contribution_needed', 'y');
			} else {
				$contribution_needed = false;
			}
			if (isset($_REQUEST['save']) && $prefs['feature_categories'] === 'y' && $prefs['feature_wiki_mandatory_category'] >=0 && (empty($_REQUEST['cat_categories']) || count($_REQUEST['cat_categories']) <= 0)) {
				$category_needed = true;
				$smarty->assign('category_needed', 'y');
			} else {
				$category_needed = false;
			}
			if (isset($_REQUEST["save"]) && !$category_needed && !$contribution_needed) {
				if (strtolower($pagename) !== 'sandbox' || $tiki_p_admin === 'y') {
					$description = TikiFilter::get('striptags')->filter($description);
					if ($tikilib->page_exists($pagename)) {
						if ($prefs['feature_multilingual'] === 'y') {
							$info = $tikilib->get_page_info($pagename);
							if ($info['lang'] !== $pageLang) {
								include_once("lib/multilingual/multilinguallib.php");
								if ($multilinguallib->updateObjectLang('wiki page', $info['page_id'], $pageLang, true)){
									$pageLang = $info['lang'];
									$smarty->assign('msg', tra("The language can't be changed as its set of translations has already this language"));
									$smarty->display("error.tpl");
									die;
								}
							}
						}

						$tikilib->update_page($pagename, $part["body"], tra('page imported'), $author, $authorid, $description, 0, $pageLang, false, $hash);
					} else {
						$tikilib->create_page($pagename, $hits, $part["body"], $lastmodified, tra('created from import'), $author, $authorid, $description, $pageLang, false, $hash);
					}

					// Handle the translation bits after actual creation/update
					// This path is never used by minor updates
					if ($prefs['feature_multilingual'] === 'y') {
						include_once("lib/multilingual/multilinguallib.php");
						unset( $tikilib->cache_page_info );

						if( $editlib->isNewTranslationMode() ) {
							if ($editlib->aTranslationWasSavedAs('complete')) {
								$editlib->saveCompleteTranslation();
							} else if ($editlib->aTranslationWasSavedAs('partial')) {
								$editlib->savePartialTranslation();
							}
						} elseif( $editlib->isUpdateTranslationMode() ) {
							if ($editlib->aTranslationWasSavedAs('complete')) {
								$editlib->saveCompleteTranslation();
							} else if ($editlib->aTranslationWasSavedAs('partial')) {
								$editlib->savePartialTranslation();
							}
						} else {
							$info = $tikilib->get_page_info( $pagename );
							$flags = array();
							if( isset( $_REQUEST['translation_critical'] ) ) {
								$flags[] = 'critical';
							}
							$multilinguallib->createTranslationBit( 'wiki page', $info['page_id'], $info['version'], $flags );
						}
					}
				}
			} else {
				$_REQUEST["edit"] = $last_part;
			}
		}
	}

	// If the watch state is not the same
	if( $requestedWatch !== $currentlyWatching ) {
		if( $requestedWatch ) {
			$tikilib->add_user_watch( $user, 'wiki_page_changed', $page, 'wiki page', $page, $wikilib->sefurl($page) );
		} else {
			$tikilib->remove_user_watch( $user, 'wiki_page_changed', $page, 'wiki page' );
		}
	}

	if (isset($_REQUEST["save"])) {					// jb tiki 6 - this block of code seems to be redundant and unused - TOKIL
		unset ($_REQUEST["save"]);
		if ($page_ref_id) {
			$url = "tiki-index.php?page_ref_id=$page_ref_id";
		} else {
			$url = $wikilib->sefurl($page);
		}
		if ($prefs['feature_best_language'] === 'y') {
			$url .= '&no_bl=y';
		}


		if ($tiki_p_wiki_approve == 'y' && $prefs['flaggedrev_approval'] == 'y') {
			global $flaggedrevisionlib; require_once 'lib/wiki/flaggedrevisionlib.php';

			if ($flaggedrevisionlib->page_requires_approval($page)) {
				$url .= '&latest=1';
			}
		}
		if ($dieInsteadOfForwardingWithHeader) die ("-- tiki-editpage: Dying before second call to header(), so we can see traces. Forwarding to: '$url'");
		header("location: $url");
		die;
	}
}

$smarty->assign('category_needed',$category_needed);
$smarty->assign('contribution_needed',$contribution_needed);
$wiki_up = "img/wiki_up";
if ($tikidomain) { $wiki_up.= "/$tikidomain"; }
// Upload pictures here
if (($prefs['feature_wiki_pictures'] === 'y') && (isset($tiki_p_upload_picture)) && ($tiki_p_upload_picture === 'y')) {
	$i = 1;
	while ( isset($_FILES['picfile'.$i]) ) {
		if ( is_uploaded_file($_FILES['picfile'.$i]['tmp_name']) ) {
			$picname = $_FILES['picfile'.$i]['name'];
			if ( preg_match('/\.(gif|png|jpe?g)$/i',$picname) ) {
				if (@getimagesize($_FILES['picfile'.$i]['tmp_name'])) {
					move_uploaded_file($_FILES['picfile'.$i]['tmp_name'], "$wiki_up/$picname");
					chmod("$wiki_up/$picname", 0644); // seems necessary on some system (see move_uploaded_file doc on php.net)
				}
			}
		}
		$i++;
	}
}
if ($prefs['feature_wiki_attachments'] === 'y' && isset($_REQUEST["attach"]) && ($tiki_p_wiki_attach_files === 'y' || $tiki_p_wiki_admin_attachments === 'y')) {
	if (isset($_FILES['userfile2']) && is_uploaded_file($_FILES['userfile2']['tmp_name'])) {
		$ret = $tikilib->attach_file($_FILES['userfile2']['name'], $_FILES['userfile2']['tmp_name'], $prefs['w_use_db'] === 'y'? 'db': 'dir');
		if ($ret['ok']) {
			$wikilib->wiki_attach_file($page, $_FILES['userfile2']['name'], $_FILES['userfile2']['type'], $_FILES['userfile2']['size'], ($prefs['w_use_db'] === 'dir')?'': $ret['data'], $_REQUEST["attach_comment"], $user, $ret['fhash']);
		} else {
				$smarty->assign('msg', $ret['error']);
				$smarty->display("error.tpl");
				die();
		}
	}
}


// Suck another page and append to the end of current
$suck_url = isset($_REQUEST["suck_url"]) ? $_REQUEST["suck_url"] : '';
$parsehtml = isset ($_REQUEST["parsehtml"]) ? ($_REQUEST["parsehtml"] === 'on' ? 'y' : 'n') : ($info['is_html'] ? 'n' : 'y');
$smarty->assign('parsehtml', $parsehtml);
if (isset($_REQUEST['do_suck']) && strlen($suck_url) > 0)
{
	// \note by zaufi
	//   This is ugly implementation of wiki HTML import.
	//   I think it should be plugable import/export converters with ability
	//   to choose from edit form what converter to use for operation.
	//   In case of import converter, it can try to guess what source
	//   file is (using mime type from remote server response).
	//   Of couse converters may have itsown configuration panel what should be
	//   pluged into wiki page edit form too... (like HTML importer may have
	//   flags 'strip HTML tags' and 'try to convert HTML to wiki' :)
	//   At least one export filter for wiki already coded :) -- PDF exporter...
	$sdta = $tikilib->httprequest($suck_url);
	if (isset($php_errormsg) && strlen($php_errormsg))
	{
		$smarty->assign('msg', tra("Can't import remote HTML page"));
		$smarty->display("error.tpl");
		die;
	}
	// Need to parse HTML?
	if ($parsehtml === 'y') {
		$sdta = $editlib->parse_html($sdta);
	}
	$_REQUEST['edit'] = $jitRequest['edit'] . $sdta;
}
// if "UserPage" complete with the user name
if ($prefs['feature_wiki_userpage'] === 'y' && $tiki_p_admin !== 'y' && $page === $prefs['feature_wiki_userpage_prefix']) {
	$page .= $user;
	$_REQUEST['page'] = $page;
}

if (strtolower($_REQUEST["page"]) === 'sandbox' && $prefs['feature_sandbox'] !== 'y') {
	$smarty->assign('msg', tra("The SandBox is disabled"));
	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["comment"])) {
	$_REQUEST["comment"] = '';
}

// Get page data
if(isset($info['wiki_cache'])) {
	$prefs['wiki_cache'] = $info['wiki_cache'];
	$smarty->assign('wiki_cache',$prefs['wiki_cache']);
}

if ($info["flag"] === 'L' && !$wikilib->is_editable($page, $user, $info)) {
	$smarty->assign('msg', tra("Cannot edit page because it is locked"));
	$smarty->display("error.tpl");
	die;
}

$smarty->assign('editable','y');
$smarty->assign('show_page','n');
$smarty->assign('comments_show','n');

$smarty->assign_by_ref('data', $info);
$smarty->assign('footnote', '');
$smarty->assign('has_footnote', 'n');
if ($prefs['feature_wiki_footnotes'] === 'y') {
	if ($user) {
		$x = $wikilib->get_footnote($user, $page);
		$footnote = $wikilib->get_footnote($user, $page);
		$smarty->assign('footnote', $footnote);
		if ($footnote)
			$smarty->assign('has_footnote', 'y');
		$smarty->assign('parsed_footnote', $tikilib->parse_data($footnote));
		if (isset($_REQUEST['footnote'])) {
			check_ticket('edit-page');
			$smarty->assign('parsed_footnote', $tikilib->parse_data($_REQUEST['footnote']));
			$smarty->assign('footnote', $_REQUEST['footnote']);
			$smarty->assign('has_footnote', 'y');
			if (empty($_REQUEST['footnote'])) {
				$wikilib->remove_footnote($user, $page);
			} else {
				$wikilib->replace_footnote($user, $page, $_REQUEST['footnote']);
			}
		}
	}
}
if ( isset($_REQUEST["customTip"]) && !isset($_REQUEST['preview']) && !isset($_REQUEST['save'])) {
	$smarty->assign('customTip', $_REQUEST['customTip']);
	if ( isset($_REQUEST["customTipTitle"]) ) {
		$smarty->assign('customTipTitle', tra($_REQUEST["customTipTitle"]));
	} else {
		$smarty->assign('customTipTitle', tra('Tip'));
	}
}
if ( isset($_REQUEST["wikiHeaderTpl"]) && !isset($_REQUEST['preview']) && !isset($_REQUEST['save'])) {
	$smarty->assign('wikiHeaderTpl', $_REQUEST['wikiHeaderTpl']);
}
if ((isset($_REQUEST["template_name"]) || isset($_REQUEST["templateId"])) && !isset($_REQUEST['preview']) && !isset($_REQUEST['save'])) {
	global $templateslib; require_once 'lib/templates/templateslib.php';
	$templateLang = isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : null;

	if (isset($_REQUEST["templateId"]))  {
		$templateId = $_REQUEST["templateId"];
	} else {
		include_once ('lib/multilingual/multilinguallib.php');
		$templateId = $multilinguallib->getTemplateIDInLanguage('wiki', $_REQUEST["template_name"], $templateLang);
	}
	$template_data = $templateslib->get_template($templateId, $templateLang);
	$_REQUEST["edit"] = $template_data["content"]."\n".$_REQUEST["edit"];
	$smarty->assign("templateId", $templateId);
}

if (isset($_REQUEST["ratingId"]) && $_REQUEST["ratingId"] > 0) {
	$smarty->assign("poll_template",$_REQUEST["ratingId"]);
} else {
	$smarty->assign("poll_template",0);
}

if(isset($_REQUEST["edit"])) {
	$edit_data = $_REQUEST["edit"];
} else {
	if (isset($info['draft'])) {
		$edit_data = $info['draft']['data'];
	} elseif (isset($info["data"])) {
		if ((isset($_REQUEST['hdr']) || (!empty($_REQUEST['pos']) && isset($_REQUEST['cell']))) && $prefs['wiki_edit_section'] === 'y') {
			if (isset($_REQUEST['hdr'])) {
				if ($_REQUEST['hdr'] === 0) {
					list($real_start, $real_len) = $tikilib->get_wiki_section($info['data'], 1);
					$real_len = $real_start;
					$real_start = 0;
				} else {
					list($real_start, $real_len) = $tikilib->get_wiki_section($info['data'], $_REQUEST['hdr']);
				}
			} else {
				include_once('lib/wiki-plugins/wikiplugin_split.php');
				list($real_start, $real_len) = wikiplugin_split_cell($info['data'], $_REQUEST['pos'], $_REQUEST['cell']);
			}
			$edit_data = substr($info['data'], $real_start, $real_len);
		} else {
			$edit_data = $info['data'];
		}
	} else {
		$edit_data = '';
	}
}

$likepages = '';
$smarty->assign_by_ref('likepages', $likepages);
if ($prefs['feature_likePages'] === 'y' and $edit_data === '' && !$tikilib->page_exists($page)) {
	$likepages = $wikilib->get_like_pages($page);
}
	
if (isset($prefs['wiki_feature_copyrights']) && $prefs['wiki_feature_copyrights'] === 'y') {
	if (isset($_REQUEST['copyrightTitle'])) {
		$smarty->assign('copyrightTitle', $_REQUEST["copyrightTitle"]);
	}
	if (isset($_REQUEST['copyrightYear'])) {
		$smarty->assign('copyrightYear', $_REQUEST["copyrightYear"]);
	}
	if (isset($_REQUEST['copyrightAuthors'])) {
		$smarty->assign('copyrightAuthors', $_REQUEST["copyrightAuthors"]);
	}
}

if (isset($_REQUEST["comment"])) {
	$smarty->assign_by_ref('commentdata', $_REQUEST["comment"]);
} elseif (isset($info['draft'])) {
	$smarty->assign_by_ref('commentdata',$info['draft']['data']);
} else {
	$smarty->assign('commentdata', '');
}
if (isset($info["description"])) {
	if (isset($info['draft'])) {
		$info['description'] = $info['draft']['description'];
	}
	$smarty->assign('description', $info["description"]);
	$description = $info["description"];
} else {
	$smarty->assign('description', '');
	$description = '';
}
if(isset($_REQUEST["description"])) {
	$smarty->assign_by_ref('description',$_REQUEST["description"]);
	$description = $_REQUEST["description"];
}

$wiki_authors_style = '';
if ( $prefs['wiki_authors_style_by_page'] === 'y' ) {
	if ( isset($_REQUEST['wiki_authors_style']) && $tiki_p_admin_wiki === 'y' ) {
		$wiki_authors_style = $_REQUEST['wiki_authors_style'];
	} elseif ( isset($info['wiki_authors_style']) ) {
		$wiki_authors_style = $info['wiki_authors_style'];
	}
	$smarty->assign('wiki_authors_style', $wiki_authors_style);
}

if($is_html) {
	$smarty->assign('allowhtml','y');
} else {
	$edit_data = str_replace( '<x>', '', $edit_data );
	$smarty->assign('allowhtml','n');
}
if (empty($_REQUEST['lock_it']) && !empty($info['flag']) && $info['flag'] === 'L') {
	$lock_it = 'y';
}
$smarty->assign_by_ref('lock_it', $lock_it);
if ($prefs['wiki_comments_allow_per_page'] !== 'n') {
	if (!isset($_REQUEST['save']) && !isset($_REQUEST['preview'])) {
		if (!empty($info) && !empty($info['comments_enabled'])) {
			$comments_enabled =  $info['comments_enabled'];
		} else {
			if ($prefs['wiki_comments_allow_per_page'] === 'y') {
				$comments_enabled = 'y';
			} else {
				$comments_enabled = 'n';
			}
		}
	}
	$smarty->assign_by_ref('comments_enabled', $comments_enabled);
}
if (isset($_REQUEST["lang"])) {
	if ($prefs['feature_multilingual'] === 'y' && isset($info["lang"]) && $info['lang'] !== $_REQUEST["lang"]) {
		include_once("lib/multilingual/multilinguallib.php");
		if ($multilinguallib->updateObjectLang('wiki page', $info['page_id'], $_REQUEST["lang"], true)) {
			$pageLang = $info['lang'];
			$smarty->assign('msg', tra("The language can't be changed as its set of translations has already this language"));
			$smarty->display("error.tpl");
			die;
		}
	}
	$pageLang = $_REQUEST["lang"];
} elseif (isset($info["lang"])) {
	$pageLang = $info["lang"];
} elseif (isset($edit_lang)) {
	$pageLang = $edit_lang;
} else {
	$pageLang = "";
}

$smarty->assign('lang', $pageLang);
if( $prefs['feature_urgent_translation'] === 'y' ) {
	$urgent_allowed = true;
	$smarty->assign( 'urgent_allowed', $urgent_allowed );
}
if( isset( $_REQUEST['translation_critical'] ) ) {
	$smarty->assign( 'translation_critical', 1 );
} else {
	$smarty->assign( 'translation_critical', 0 );
}

// Parse (or not) $edit_data into $parsed
// Handles switching editor modes
if ( !isset($_REQUEST['preview']) && !isset($_REQUEST['save']) ) {
	if (isset($_REQUEST['mode_normal']) && $_REQUEST['mode_normal'] ==='y') {
		// Parsing page data as first time seeing html page in normal editor
		$smarty->assign('msg', "Parsing html to wiki");
		$parsed = $editlib->parseToWiki($edit_data);
		$is_html = false;
		$info['is_html'] = false;
		$info['wysiwyg'] = false;
		$smarty->assign('allowhtml','n');
	} elseif (isset($_REQUEST['mode_wysiwyg']) && $_REQUEST['mode_wysiwyg'] === 'y') {
		// Parsing page data as first time seeing wiki page in wysiwyg editor
		$smarty->assign('msg', "Parsing wiki to html");
		$parsed = $editlib->parseToWysiwyg($edit_data, true);
		if ($prefs['wysiwyg_htmltowiki'] === 'y') {
			$is_html = false;
			$info['is_html'] = false;
		} else {
			$is_html = true;
			$info['is_html'] = true;
		}
		$info['wysiwyg'] = true;
		$smarty->assign('allowhtml','y');
	} elseif ($_SESSION['wysiwyg'] === 'y') {
		if ($prefs['wysiwyg_htmltowiki'] === 'y') {
			if ($edit_data == 'ajax error') {
				unset($_REQUEST['save']);	// don't save an ajax error
			}
		} else {
		 	$parsed = $tikilib->parse_data( $edit_data, array( 'absolute_links'=>true, 'noheaderinc'=>true, 'suppress_icons' => true, 'ck_editor' => true, 'is_html' => true, 'process_wiki_paragraphs' => false));
		}
	}
}

if (empty($parsed)) {
	if ( ! isset($_REQUEST['edit']) && ! $is_html ) {
		// When we get data from database (i.e. we are not in preview mode) and if we don't allow HTML,
		//   then we need to convert database's HTML entities into their "normal chars" equivalents
		$parsed = TikiLib::htmldecode($edit_data);
	} else {
		$parsed = $edit_data;
	}
}
$smarty->assign('pagedata', $parsed);

// apply the optional post edit filters before preview
if (isset($_REQUEST["preview"])) {

	$parsed = $tikilib->apply_postedit_handlers($parsed);
	
	if ($_SESSION['wysiwyg'] === 'y' && $prefs['wysiwyg_wiki_parsed'] === 'y') {
		$parsed = $editlib->partialParseWysiwygToWiki( $parsed );
		$parsed = $tikilib->parse_data( $parsed, array( 'absolute_links'=>true, 'noheaderinc'=>true, 'suppress_icons' => true, 'preview_mode'=>true, 'is_html' => $is_html));
	} else {
		$parsed = $tikilib->parse_data($parsed, array('is_html' => $is_html, 'preview_mode'=>true));
	}
	// If we are in preview mode then preview it!
	$smarty->assign('preview',1);
} else {
	$parsed = '';
	$smarty->assign('preview',0);
}

$smarty->assign_by_ref('parsed', $parsed);

function parse_output(&$obj, &$parts,$i) {
	if(!empty($obj['parts'])) {
		foreach( $obj['parts'] as $index => $part ) {
			parse_output($part, $parts,$index);
		}
	}elseif( $obj['type'] === 'application/x-tikiwiki' ) {
		$aux["body"] = $obj['body'];
		$ccc=$obj['header']["content-type"];
		$items = explode(';',$ccc);
		foreach($items as $item) {
			$portions = explode('=',$item);
			if(isset($portions[0])&&isset($portions[1])) {
				$aux[trim($portions[0])]=trim($portions[1]);
			}
		}
		$parts[]=$aux;
	}
}
// Pro
// Check if the page has changed
$pageAlias = '';
$cat_type='wiki page';
$cat_objid = $_REQUEST["page"];
if (isset($_REQUEST['save']) && $prefs['feature_contribution'] === 'y' && $prefs['feature_contribution_mandatory'] === 'y' && (empty($_REQUEST['contributions']) || count($_REQUEST['contributions']) <= 0)) {
	$contribution_needed = true;
	$smarty->assign('contribution_needed', 'y');
} else {
	$contribution_needed = false;
}
if (isset($_REQUEST['save']) && $prefs['feature_categories'] === 'y' && $prefs['feature_wiki_mandatory_category'] >=0 && (empty($_REQUEST['cat_categories']) || count($_REQUEST['cat_categories']) <= 0)) {
	$category_needed = true;
	$smarty->assign('category_needed', 'y');
} else {
	$category_needed = false;
}
if (isset($_REQUEST['save']) && $prefs['wiki_mandatory_edit_summary'] === 'y' && empty($_REQUEST['comment']) ) {
	$summary_needed = true;
	$smarty->assign( 'summary_needed', 'y' );
} else {
	$summary_needed = false;
}
if ($prefs['wiki_mandatory_edit_summary'] === 'y') {
	$headerlib->add_jq_onready('
$("input[name=save],input[name=minor]").click(function(){
	if (!$("#comment").val()) {
		var s = prompt("'.tra('Describe the change you made').'");
		if (!s) return false;
		$("#comment").val(s);
	}
	return true;
})
');
}

if (isset($_REQUEST["save"]) && (strtolower($_REQUEST['page']) !== 'sandbox' || $tiki_p_admin === 'y') && !$category_needed && !$contribution_needed && ! $summary_needed) {
	check_ticket('edit-page');
	// Check if all Request values are delivered, and if not, set them
	// to avoid error messages. This can happen if some features are
	// disabled
	if(!isset($_REQUEST["description"])) $_REQUEST["description"]='';
	if(!isset($_REQUEST["wiki_authors_style"])) $_REQUEST["wiki_authors_style"]='';
	if(!isset($_REQUEST["comment"])) $_REQUEST["comment"]='';
	if(!isset($_REQUEST["lang"])) $_REQUEST["lang"]='';
	if(!isset($_REQUEST['wysiwyg'])) $_REQUEST['wysiwyg'] = 'n';
	if(isset($_REQUEST['wiki_cache'])) {
		$wikilib->set_page_cache($_REQUEST['page'],$_REQUEST['wiki_cache']);
	}
	include_once("lib/imagegals/imagegallib.php");
	$cat_desc = ($prefs['feature_wiki_description'] === 'y') ? substr($_REQUEST["description"],0,200) : '';
	$cat_name = $_REQUEST["page"];
	$cat_href="tiki-index.php?page=".urlencode($cat_objid);
	$cat_lang = $_REQUEST['lang'];
	$cat_object_exists = $tikilib->page_exists( $_REQUEST['page'] );
	include_once("categorize.php");
	include_once("poll_categorize.php");
	include_once("freetag_apply.php");
	$page = $_REQUEST["page"];
	if($is_html) {
		$edit = $_REQUEST["edit"];
	} else {
		$edit = htmlspecialchars($_REQUEST['edit']);
	}
	// add permisions here otherwise return error!
	if(
		isset($prefs['wiki_feature_copyrights']) 
		&& $prefs['wiki_feature_copyrights'] === 'y'
		&& isset($_REQUEST['copyrightTitle'])
		&& isset($_REQUEST['copyrightYear'])
		&& isset($_REQUEST['copyrightAuthors'])
		&& !empty($_REQUEST['copyrightYear'])
		&& !empty($_REQUEST['copyrightTitle']) 
	){

		include_once("lib/copyrights/copyrightslib.php");
		$copyrightslib = new CopyrightsLib;
		$copyrightYear = $_REQUEST['copyrightYear'];
		$copyrightTitle = $_REQUEST['copyrightTitle'];
		$copyrightAuthors = $_REQUEST['copyrightAuthors'];
		$copyrightslib->add_copyright($page,$copyrightTitle,$copyrightYear,$copyrightAuthors,$user);
	}

	// Parse $edit and eliminate image references to external URIs (make them internal)
	$edit = $imagegallib->capture_images($edit);
	// apply the optional page edit filters before data storage
	$edit = $tikilib->apply_postedit_handlers($edit);
	$exist = $tikilib->page_exists($_REQUEST['page']);
	// If page exists
	if(!$exist) {
		// Extract links and update the page
		$links = $tikilib->get_links($_REQUEST["edit"]);
		/*
		   $notcachedlinks = $tikilib->get_links_nocache($_REQUEST["edit"]);
		   $cachedlinks = array_diff($links, $notcachedlinks);
		   $tikilib->cache_links($cachedlinks);
		 */
		$tikilib->create_page($_REQUEST["page"], 0, $edit, $tikilib->now, $_REQUEST["comment"],$user,$tikilib->get_ip_address(),$description, $pageLang, $is_html, $hash, $_REQUEST['wysiwyg'], $wiki_authors_style);

		$info_new = $tikilib->get_page_info($page);

		if( $editlib->isNewTranslationMode() && ! empty( $pageLang ) )
		{
			include_once("lib/multilingual/multilinguallib.php");
			$infoSource = $tikilib->get_page_info($editlib->sourcePageName);
			$infoCurrent = $tikilib->get_page_info($editlib->targetPageName);
			if ($multilinguallib->insertTranslation('wiki page', $infoSource['page_id'], $infoSource['lang'], $infoCurrent['page_id'], $pageLang)){
				$pageLang = $info['lang'];
				$smarty->assign('msg', tra("The language can't be changed as its set of translations has already this language"));
				$smarty->display("error.tpl");
				die;
			}
		}

		if ($prefs['feature_multilingual'] === 'y') {
			include_once("lib/multilingual/multilinguallib.php");

			unset( $tikilib->cache_page_info );
			if( $editlib->isNewTranslationMode() ) {
				if ($editlib->aTranslationWasSavedAs('complete')) {
					$editlib->saveCompleteTranslation();
				} else if ($editlib->aTranslationWasSavedAs('partial')) {
					$editlib->savePartialTranslation();
				}
			} else {
				$info = $tikilib->get_page_info( $_REQUEST['page'] );
				$multilinguallib->createTranslationBit( 'wiki page', $info['page_id'], 1 );
			}
		}
	} else {
		$links = $tikilib->get_links($edit);
		/*
		   $tikilib->cache_links($links);
		 */
		$minor=(isset($_REQUEST['isminor'])&&$_REQUEST['isminor'] === 'on') ? 1 : 0;

		if ((isset($_REQUEST['hdr']) || (!empty($_REQUEST['pos']) && isset($_REQUEST['cell']))) && $prefs['wiki_edit_section'] === 'y') {
			if (isset($_REQUEST['hdr'])) {
				if ($_REQUEST['hdr'] == 0) {
					list($real_start, $real_len) = $tikilib->get_wiki_section($info['data'], 1);
					$real_len = $real_start;
					$real_start = 0;
				} else {
					list($real_start, $real_len) = $tikilib->get_wiki_section($info['data'], $_REQUEST['hdr']);
				}
			} else {
				include_once('lib/wiki-plugins/wikiplugin_split.php');
				list($real_start, $real_len) = wikiplugin_split_cell($info['data'], $_REQUEST['pos'], $_REQUEST['cell']);
			}
			if ($edit[strlen($edit) - 1] !== "\n")
				$edit .= "\r\n";
			$edit = substr($info['data'], 0, $real_start).$edit.substr($info['data'], $real_start + $real_len);
		}
		if ($_SESSION['wysiwyg'] === 'y' && $prefs['wysiwyg_wiki_parsed'] === 'y' && $prefs['wysiwyg_ckeditor'] === 'y') {
			$edit = $editlib->partialParseWysiwygToWiki( $edit );
		}
		$tikilib->update_page($_REQUEST["page"],$edit,$_REQUEST["comment"],$user,$tikilib->get_ip_address(),$description,$minor,$pageLang, $is_html, $hash, null, $_REQUEST['wysiwyg'], $wiki_authors_style);
		$info_new = $tikilib->get_page_info($page);

		// Handle translation bits
		if ($prefs['feature_multilingual'] === 'y' && !$minor) {
			global $multilinguallib; include_once("lib/multilingual/multilinguallib.php");
			unset( $tikilib->cache_page_info );

			if( $editlib->isUpdateTranslationMode() ) {
				if ($editlib->aTranslationWasSavedAs('complete')) {
					$editlib->saveCompleteTranslation();
				} else if ($editlib->aTranslationWasSavedAs('partial')) {
					$editlib->savePartialTranslation();
				}
			} else {
				$info = $tikilib->get_page_info( $_REQUEST['page'] );
				$flags = array();
				if( isset( $_REQUEST['translation_critical'] ) ) {
					$flags[] = 'critical';
				}
				$multilinguallib->createTranslationBit( 'wiki page', $info['page_id'], $info['version'], $flags );
			}
		}
	}
	//Page may have been inserted from a structure page view
	if (isset($_REQUEST['current_page_id']) ) {
		$page_info = $structlib->s_get_page_info($_REQUEST['current_page_id']);
		$pageAlias = $page_info['page_alias'];
		if (isset($_REQUEST["add_child"]) ) {
			//Insert page after last child of current page
			$subpages = $structlib->s_get_pages($_REQUEST["current_page_id"]);
			$max = count($subpages);
			$last_child_ref_id = null;
			if ($max !== 0) {
				$last_child = $subpages[$max - 1];
				$last_child_ref_id = $last_child["page_ref_id"];
			}
			$page_ref_id = $structlib->s_create_page($_REQUEST['current_page_id'], $last_child_ref_id, $_REQUEST["page"], '', $page_info['structure_id']);
		} else {
			//Insert page after current page
			$page_ref_id = $structlib->s_create_page($page_info["parent_id"], $_REQUEST['current_page_id'], $_REQUEST["page"], '', $page_info['structure_id']);
		}
		//Criss Holman added the if containing this code of which I don't know the use, but a check before the permissions copy
		//is definitely needed in case someone has tiki_p_edit/tiki_p_admin_wiki in a page belonging to a structure. chealer
		if ($tikilib->user_has_perm_on_object($user, $_REQUEST["page"],'wiki page', 'tiki_p_admin_wiki', 'tiki_p_admin_categories'))
			$userlib->copy_object_permissions($page_info["pageName"], $_REQUEST["page"],'wiki page');
	} 

	// If the watch state is not the same
	if( $requestedWatch !== $currentlyWatching ) {
		if( $requestedWatch ) {
			$tikilib->add_user_watch( $user, 'wiki_page_changed', $page, 'wiki page', $page, $wikilib->sefurl($page) );
		} else {
			$tikilib->remove_user_watch( $user, 'wiki_page_changed', $page, 'wiki page' );
		}
	}

	if ($prefs['geo_locate_wiki'] == 'y' && ! empty($_REQUEST['geolocation'])) {
		TikiLib::lib('geo')->set_coordinates('wiki page', $page, $_REQUEST['geolocation']);
	}

	if (!empty($_REQUEST['returnto'])) {	// came from wikiplugin_include.php edit button
		$url = $wikilib->sefurl($_REQUEST['returnto']);
	} else if ($page_ref_id) {
		$url = "tiki-index.php?page_ref_id=$page_ref_id";
	} else {
		$url = $wikilib->sefurl($page);
	}
	if ($prefs['feature_multilingual'] === 'y' && $prefs['feature_best_language'] === 'y' && isset($info['lang']) && $info['lang'] !== $prefs['language']) {
		$url .= '&no_bl=y';
	}

	if ($tiki_p_wiki_approve == 'y' && $prefs['flaggedrev_approval'] == 'y') {
		global $flaggedrevisionlib; require_once 'lib/wiki/flaggedrevisionlib.php';

		if ($flaggedrevisionlib->page_requires_approval($page)) {
			$url .= '&latest=1';
		}
	}

	$_SESSION['saved_msg'] = $_REQUEST["page"];

	if (!empty($_REQUEST['hdr'])) {
		$tmp = $tikilib->parse_data($edit);			// fills $anch[] so page refreshes at the section being edited
		$url .= "#".$anch[$_REQUEST['hdr']-1]['id'];
	}
	
	if ($dieInsteadOfForwardingWithHeader) die ("-- tiki-editpage: Dying before third call to header(), so we can see traces. Forwarding to: '$url'");
	header("location: $url");
	die;
} //save
$smarty->assign('pageAlias',$pageAlias);
if ($prefs['feature_wiki_templates'] === 'y' && $tiki_p_use_content_templates === 'y') {
	global $templateslib; require_once 'lib/templates/templateslib.php';
	$templates = $templateslib->list_templates('wiki', 0, -1, 'name_asc', '');
	$smarty->assign_by_ref('templates', $templates["data"]);
}
if ($prefs['feature_polls'] ==='y' and $prefs['feature_wiki_ratings'] === 'y' && $tiki_p_wiki_admin_ratings === 'y') {
	if (!isset($polllib) or !is_object($polllib)) include("lib/polls/polllib_shared.php");
	if (!isset($categlib) or !is_object($categlib)) include("lib/categories/categlib.php");
	if (isset($_REQUEST['removepoll'])) {
		$catObjectId = $categlib->is_categorized($cat_type,$cat_objid);
		$polllib->remove_object_poll( $cat_type, $cat_objid, $_REQUEST['removepoll'] );
	}
	$polls_templates = $polllib->get_polls('t');
	$smarty->assign('polls_templates',$polls_templates['data']);
	$poll_rated = $polllib->get_ratings($cat_type,$cat_objid);
	$smarty->assign('poll_rated',$poll_rated);
	if (isset($_REQUEST['poll_template'])) {
		$smarty->assign('poll_template',$_REQUEST['poll_template']);
	}
}

if ($prefs['feature_multilingual'] === 'y') {
	$languages = array();
	$languages = $tikilib->list_languages();
	$smarty->assign_by_ref('languages', $languages);

	if( $editlib->isNewTranslationMode() ) {
		$smarty->assign( 'translationOf', $editlib->sourcePageName );

		if( $tikilib->page_exists( $page ) ) {
			// Display an error if the page already exists
			$smarty->assign('msg',
								tra("Page already exists. Go back and choose a different name.")."<P>".
								tra("Page name is").": '$page'");
			$smarty->display("error.tpl");
			die;
		}

		global $multilinguallib; include_once("lib/multilingual/multilinguallib.php");
		$sourceInfo = $tikilib->get_page_info( $editlib->sourcePageName );
		if( $multilinguallib->getTranslation('wiki page', $sourceInfo['page_id'], $_REQUEST['lang'] ) ) {
			// Display an error if the page already exists
			$smarty->assign('msg',tra("The translation set already contains a page in this language."));
			$smarty->display("error.tpl");
			die;
		}
	}

	if( $editlib->isTranslationMode() ) {
		include_once('lib/wiki/histlib.php');
		histlib_helper_setup_diff( $editlib->sourcePageName, $editlib->oldSourceVersion, $editlib->newSourceVersion );
		$smarty->assign( 'diff_oldver', (int) $editlib->oldSourceVersion );
		$smarty->assign( 'diff_newver', (int) $editlib->newSourceVersion );
		$smarty->assign('update_translation', 'y');
	}
}
$cat_type = 'wiki page';
$cat_objid = $_REQUEST["page"];
$cat_lang = $pageLang;
$cat_object_exists = $tikilib->page_exists( $_REQUEST['page'] );
if (!$cat_object_exists)
	$cookietab = 1;

$smarty->assign('section',$section);
include_once ('tiki-section_options.php');
if ($prefs['feature_freetags'] === 'y') {
	include_once ('freetag_list.php');
	// if given in the request, set the freetag list (used for preview mode, when coming back from zoom mode, ...)
	if ( isset($_REQUEST['freetag_string']) ) {
		$smarty->assign('taglist', $_REQUEST['freetag_string']);
	} elseif( $editlib->isNewTranslationMode() ) {
		$tags = $freetaglib->get_all_tags_on_object_for_language($editlib->sourcePageName, 'wiki page', $pageLang);
		$smarty->assign( 'taglist', implode( ' ', $tags ) );
	}
}
if ($prefs['feature_categories'] === 'y') {
	include_once ("categorize_list.php");
	
	if (isset($_REQUEST["current_page_id"]) && $prefs['feature_wiki_categorize_structure'] === 'y' && $categlib->is_categorized('wiki page', $structure_info["pageName"])) {
		$categIds = $categlib->get_object_categories('wiki page', $structure_info["pageName"]);
		$smarty->assign('categIds',$categIds);
	} else {
		$smarty->assign('categIds',array());
	}
	if (isset($_SERVER['HTTP_REFERER']) && strstr($_SERVER['HTTP_REFERER'], 'tiki-index.php') && !$tikilib->page_exists($_REQUEST["page"])) { // default the categs the page you come from for a new page
		if (preg_match('/page=([^\&]+)/', $_SERVER['HTTP_REFERER'], $ms))
			$p = $ms[1];
		else
			$p = $wikilib->get_default_wiki_page();
		$cs = $categlib->get_object_categories('wiki page', $p);
		for ($i = count($categories) - 1; $i >= 0; --$i) {
			if (in_array($categories[$i]['categId'], $cs))
				$categories[$i]['incat'] = 'y';
		}
	}
}

$page_name = $page;

if ($wikilib->contains_badchars($page) && ! $tikilib->page_exists($page) ) {
	$smarty->assign('page_badchars_display', $wikilib->get_badchars());
}

$plugins = $wikilib->list_plugins(true, 'editwiki');

$smarty->assign_by_ref('plugins', $plugins);
$smarty->assign('showstructs', array());
if ($structlib->page_is_in_structure($_REQUEST["page"])) {
	$structs = $structlib->get_page_structures($_REQUEST["page"]);
	$smarty->assign('showstructs', $structs);
}
// Flag for 'page bar' that currently 'Edit' mode active
// so no need to show comments & attachments, but need
// to show 'wiki quick help'
$smarty->assign('edit_page', 'y');
if ($prefs['wiki_feature_copyrights'] === 'y' && $tiki_p_edit_copyrights === 'y') {
	include_once ('lib/copyrights/copyrightslib.php');
	$copyrightslib = new CopyrightsLib;
	$copyrights = $copyrightslib->list_copyrights($_REQUEST["page"]);
	if ($copyrights['cant'])
	$smarty->assign_by_ref('copyrights', $copyrights['data']);
}
$defaultRows = $prefs['default_rows_textarea_wiki'];
include_once ('lib/toolbars/toolbarslib.php');
if (!$user or $user === 'anonymous') {
	$smarty->assign('anon_user', 'y');
}
if ($prefs['feature_contribution'] === 'y') {
	include_once('contribution.php');
}

if( $prefs['geo_locate_wiki'] == 'y' ) {
	$smarty->assign('geolocation_string', TikiLib::lib('geo')->get_coordinates_string('wiki page', $page));
}

if( $prefs['feature_multilingual'] === 'y' ) {
	global $multilinguallib; include_once('lib/multilingual/multilinguallib.php');
	$trads = $multilinguallib->getTranslations('wiki page', $info['page_id'], $page, $info['lang']);
	$smarty->assign('trads', $trads);
}

// setup properties tab visibility
if (($prefs['feature_wiki_templates'] === 'y' && $tiki_p_use_content_templates === 'y') ||
	($prefs['feature_wiki_usrlock'] === 'y' && ($tiki_p_lock === 'y' || $tiki_p_admin_wiki === 'y')) ||
	($prefs['feature_wiki_replace'] === 'y' && $_SESSION['wysiwyg'] !== 'y') ||
	($prefs['feature_wiki_allowhtml'] === 'y' && $tiki_p_use_HTML === 'y' && $_SESSION['wysiwyg'] !== 'y') ||
	$prefs['feature_wiki_import_html'] === 'y' ||
	$prefs['wiki_comments_allow_per_page'] !== 'n' ||
	($tiki_p_admin_wiki === 'y' && $prefs['feature_wiki_import_page'] === 'y') ||
	($_SESSION['wysiwyg'] !== 'y' && ($prefs['feature_wiki_attachments'] === 'y' && ($tiki_p_wiki_attach_files === 'y' && $tiki_p_wiki_admin_attachments === 'y'))) ||
	strtolower($page) !== 'sandbox' &&
			($prefs['wiki_feature_copyrights']  === 'y' ||
			($prefs['feature_freetags'] === 'y' && $tiki_p_freetags_tag === 'y') ||
			$prefs['feature_wiki_icache'] === 'y' ||
			$prefs['feature_contribution'] === 'y' ||
			$prefs['feature_wiki_structure'] === 'y' ||
			$prefs['wiki_feature_copyrights']  === 'y' ||
			($tiki_p_admin_wiki === 'y' && $prefs['wiki_authors_style_by_page'] === 'y')) ||	// end not sandbox
		($prefs['feature_wiki_description'] === 'y' || $prefs['metatag_pagedesc'] === 'y') ||
		$prefs['feature_wiki_footnotes'] === 'y' ||
		($prefs['feature_wiki_ratings'] === 'y' && $tiki_p_wiki_admin_ratings ==='y') ||
		$prefs['feature_multilingual'] === 'y' ||
		$prefs['geo_locate_wiki'] === 'y') {
	
	$smarty->assign('showPropertiesTab', 'y');
}

ask_ticket('edit-page');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the Edit Template or language check
if ($need_lang) {
	$smarty->assign('mid', 'tiki-choose_page_language.tpl');
} else {
	$smarty->assign('mid', 'tiki-editpage.tpl');
}
$smarty->assign('showtags', 'n');
$smarty->assign('qtnum', '1');
$smarty->assign('qtcycle', '');
$smarty->display("tiki.tpl");

