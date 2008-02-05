<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $pivotLanguage;

function filter_languages_from_pivot( $langInfo )
{
	global $pivotLanguage;
	global $pageLang;

	return empty( $pivotLanguage )
		|| $pageLang == $pivotLanguage
		|| $langInfo['lang'] == $pivotLanguage;
}

if( isset( $module_params['pivot_language'] ) ) {
	$pivotLanguage = $module_params['pivot_language'];
} else {
	$pivotLanguage = '';
}

$smarty->assign( 'pivot_language', $pivotLanguage );

if( $prefs['feature_multilingual'] == 'y' && ! empty( $page ) ) {
	$smarty->assign( 'show_translation_module', true );
	global $multilinguallib;
	include_once('lib/multilingual/multilinguallib.php');

	$langs = $multilinguallib->preferedLangs();
	$pageLang = $GLOBALS['pageLang'];

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

	$better = $multilinguallib->getBetterPages( $transinfo['page_id'] );
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

	$worst = $multilinguallib->getWorstPages( $transinfo['page_id'] );
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
}

?>
