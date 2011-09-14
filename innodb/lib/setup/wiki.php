<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

// Wiki pagename regexp

if ( $prefs['wiki_page_regex'] == 'strict' ) $page_regex = '([A-Za-z0-9_])([\.: A-Za-z0-9_\-])*([A-Za-z0-9_])';
elseif ( $prefs['wiki_page_regex'] == 'full' ) $page_regex = '([A-Za-z0-9_]|[\x80-\xFF])([\.: A-Za-z0-9_\-]|[\x80-\xFF])*([A-Za-z0-9_]|[\x80-\xFF])';
else $page_regex = '([^\n|\(\)])((?!(\)\)|\||\n)).)*?';

// Wiki dump

$wiki_dump_exists = 'n';
$dump_path = 'dump';

if ( $tikidomain ) {
	$dump_path .= "/$tikidomain";
}
if ( file_exists($dump_path.'/new.tar') ) {
	$wiki_dump_exists = 'y';
}
$smarty->assign('wiki_dump_exists', $wiki_dump_exists);

// find out the page name if url=tiki-index_x.php (can be needed in module)
if (strstr($_SERVER['SCRIPT_NAME'], 'tiki-index.php') || strstr($_SERVER['SCRIPT_NAME'], 'tiki-index_p.php') || strstr($_SERVER['SCRIPT_NAME'], 'tiki-index_raw.php')) {
	$check = false;
	if (!isset($_REQUEST['page']) && !isset($_REQUEST['page_ref_id']) && !isset($_REQUEST['page_id'])) {
		$_REQUEST['page'] = $userlib->get_user_default_homepage2($user);
		$check = true;
	}
		
	if ( $prefs['feature_multilingual'] == 'y' && (isset($_REQUEST['page']) || isset($_REQUEST['page_ref_id']) || isset($_REQUEST['page_id']))) { // perhaps we have to go to an another page
		
		global $multilinguallib; include_once('lib/multilingual/multilinguallib.php');
		if ( $multilinguallib->useBestLanguage() || isset($_REQUEST['switchLang'])) {
			
			if (!empty($_REQUEST['page_id'])) {
				if (isset($_REQUEST['switchLang'])) {
					$info = get_page_info_from_id($page_id);
				}
			} elseif (!empty($_REQUEST['page'])) {
				$info = $tikilib->get_page_info($_REQUEST['page']);
				$_REQUEST['page_id'] = $info['page_id'];
			} elseif (!empty($_REQUEST['page_ref_id'])) {
				global $structlib; include_once('lib/structures/structlib.php');
				$info = $structlib->s_get_page_info($_REQUEST['page_ref_id']);
				$_REQUEST['page_id'] = $info['page_id'];
			}
			if (!empty($_REQUEST['page_id'])) {
				if (isset($_REQUEST['switchLang']) && $info['lang'] != $_REQUEST['switchLang']) {
					$_REQUEST['page_id'] = $multilinguallib->selectLangObj('wiki page', $_REQUEST['page_id'], $_REQUEST['switchLang']);
				} elseif ( $multilinguallib->useBestLanguage() ) {
					$_REQUEST['page_id'] = $multilinguallib->selectLangObj('wiki page', $_REQUEST['page_id']);
				} 
				if (!empty($_REQUEST['page_id'])) {
					$check = false;
				}
			}

		}

	}
	
	// If the HomePage does not exist, create it
	if ($check && !$tikilib->page_exists($_REQUEST['page'])) {

		// Get the translated HomePage content
		$homePageLang = $prefs['language'];
		$homePageTranslationKey = '_HOMEPAGE_CONTENT_'; //get_strings tra("_HOMEPAGE_CONTENT_")
		$translatedHomePageContent = tra( $homePageTranslationKey );

		// If the HomePage has not been translated yet, fallback to the 'en' translation
		if ( $translatedHomePageContent == $homePageTranslationKey ) {
			$homePageLang = 'en';
			$translatedHomePageContent = tra( $homePageTranslationKey, $homePageLang );
		}

		$tikilib->create_page( $_REQUEST['page'], 0, $translatedHomePageContent, $tikilib->now, 'Tiki initialization', 'admin', '0.0.0.0', '', $homePageLang, false, null, 'n', '' );
		unset( $homePageTranslationKey, $translatedHomePageContent, $homePageLang );
	}
}
