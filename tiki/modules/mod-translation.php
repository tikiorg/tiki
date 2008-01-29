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

	$smarty->assign( 'pageName', $page );
	$langs = $multilinguallib->preferedLangs();

	$info = $tikilib->get_page_info( $page );

	$better = $multilinguallib->getBetterPages( $info['page_id'] );
	$known = array();
	$other = array();

	foreach( $better as $page )
	{
		if( in_array( $page['lang'], $langs ) )
			$known[] = $page;
		else
			$other[] = $page;
	}

	$smarty->assign_by_ref( 'mod_translation_better_known', $known );
	$smarty->assign_by_ref( 'mod_translation_better_other', $other );
}
?>
