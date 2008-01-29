<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if( $prefs['feature_multilingual'] == 'y' && ! empty( $page ) ) {
	$smarty->assign( 'show_translation_module', true );
	global $multilinguallib;
	include_once('lib/multilingual/multilinguallib.php');

	$langs = $multilinguallib->preferedLangs();

	$info = $tikilib->get_page_info( $page );

	$better = $multilinguallib->getBetterPages( $info['page_id'] );
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

	$worst = $multilinguallib->getWorstPages( $info['page_id'] );
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
	$smarty->assign( 'pageVersion', $info['version'] );
}

?>
