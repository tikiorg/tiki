<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_translation_info() {
	return array(
		'name' => tra('Translate Updates'),
		'description' => tra('Links to versions of the wiki page being viewed in other languages, distinguishing between better, equivalent or worse translations. Optionally displays the up-to-dateness of the translation being viewed.'),
		'prefs' => array("feature_translation"),
		'params' => array(
			'pivot_language' => array(
				'name' => tra('Reference language'),
				'description' => tra('If set to a language code, restricts the localized pages shown to the reference page, unless that page is being displayed.') . " " . tra('Example values:') . ' en, fr.' . " " . tra('Not set by default.')
			),
			'show_language' => array(
				'name' => tra('Show language'),
				'description' => tra('If "y" the page language will be shown instead of the page name.') . tra('Default = "y".')
			)
		)
	);
}

// Filter localized pages according to the reference language
function filter_languages_from_pivot( $langInfo ) {
	global $pivotLanguage;
	global $pageLang;

	return empty( $pivotLanguage )
		|| $pageLang == $pivotLanguage
		|| $langInfo['lang'] == $pivotLanguage;
}

function module_translation( $mod_reference, $module_params ) {
	global $pivotLanguage, $tikilib, $smarty, $prefs, $page, $_REQUEST;
	
	
//are we arriving from the edit page?	
		if ( isset($module_params['from_edit_page']) && $module_params['from_edit_page'] == 'y') {
			$smarty->assign( 'from_edit_page', 'y');
		} else {
			$smarty->assign( 'from_edit_page', 'n');
		}
	
	
	
	if ((!$page or $page == '') and isset($_REQUEST['page'])) {
		$page = $_REQUEST['page'];
	}
	$smarty->assign('page', $page);
	$smarty->assign( 'show_translation_module', false);

	if( ! empty( $page ) && is_string($page) ) {
	
		global $multilinguallib;
		include_once('lib/multilingual/multilinguallib.php');

		if ( isset($module_params['show_language']) && $module_params['show_language'] == 'n') {
			$smarty->assign( 'show_language', 'n');
		} else {
			$smarty->assign( 'show_language', 'y');
		}
		
		$pivotLanguage = isset( $module_params['pivot_language'] ) ? $module_params['pivot_language'] : '';
		$langs = $multilinguallib->preferredLangs();
		if( isset( $GLOBALS['pageLang'] ) )
			$pageLang = $GLOBALS['pageLang'];
		else
			$pageLang = '';
	
		if ($prefs['feature_wikiapproval'] == 'y' && $tikilib->page_exists($prefs['wikiapproval_prefix'] . $page)) {
		// temporary fix: simply use info of staging page
		// TODO: better system of dealing with translations with approval
			$stagingPageName = $prefs['wikiapproval_prefix'] . $page;
			$smarty->assign('stagingPageName', $stagingPageName);
			$smarty->assign('hasStaging', 'y');
			$transinfo = $tikilib->get_page_info( $stagingPageName );	
		} else {
			$transinfo = $tikilib->get_page_info( $page );
		}
	
		$tempList = $multilinguallib->getTranslations( 'wiki page', $transinfo['page_id'] );
		$completeList = array();
		foreach( $tempList as $row ) {
			$t_id = $row['objId'];
			$t_page = $row['objName'];
			$t_lang = $row['lang'];
			$completeList[$t_id] = array( 'page' => $t_page, 'lang' => $t_lang );
		}
	
		unset( $completeList[$transinfo['page_id']] );
		
		$smarty->assign( 'show_translation_module', ! empty( $completeList ) );
		if (empty( $completeList )) {
			return;
		}
	
		$origBetter = $better = $multilinguallib->getBetterPages( $transinfo['page_id'] );
		$better = array_filter( $better, 'filter_languages_from_pivot' );
		$known = array();
		$other = array();
	
		foreach( $better as $pageOption )
		{
			if( in_array( $pageOption['lang'], $langs ) )
				$known[] = $pageOption;
			else
				$other[] = $pageOption;
		}
	
		$smarty->assign( 'mod_translation_better_known', $known );
		$smarty->assign( 'mod_translation_better_other', $other );
	
		$origWorst = $worst = $multilinguallib->getWorstPages( $transinfo['page_id'] );
		$worst = array_filter( $worst, 'filter_languages_from_pivot' );
		$known = array();
		$other = array();
	
		foreach( $worst as $pageOption )
		{
			if( in_array( $pageOption['lang'], $langs ) )
				$known[] = $pageOption;
			else
				$other[] = $pageOption;
		}
	
		$smarty->assign( 'mod_translation_worst_known', $known );
		$smarty->assign( 'mod_translation_worst_other', $other );
		$smarty->assign( 'pageVersion', $transinfo['version'] );
		
		foreach( $origBetter as $row ) {
			$id = $row['page_id'];
			unset($completeList[$id]);
		}
		foreach( $origWorst as $row ) {
			$id = $row['page_id'];
			unset($completeList[$id]);
		}
	
		$known = array();
		$other = array();
		foreach( $completeList as $pageOption )
		{
			if( in_array( $pageOption['lang'], $langs ) )
				$known[] = $pageOption;
			else
				$other[] = $pageOption;
		}
	
		$smarty->assign( 'mod_translation_equivalent_known', $known );
		$smarty->assign( 'mod_translation_equivalent_other', $other );
	
		if( $prefs['quantify_changes'] == 'y' ) {
			global $quantifylib;
			include_once 'lib/wiki/quantifylib.php';
			include_once 'lib/wiki-plugins/wikiplugin_gauge.php';
			$numeric = $quantifylib->getCompleteness( $transinfo['page_id'] );
			$smarty->assign( 'mod_translation_quantification', $numeric );
			$smarty->assign( 'mod_translation_gauge', wikiplugin_gauge( '', array(
				'value' => $numeric,
				'max' => 100,
				'size' => '100%',
				'color' => 'green',
				'bgcolor' => 'gray',
				'showvalue' => false,
			) ) );
		}
	}
}
