<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * filegal_manager_url: Return the URL of the filegal manager, that goes to the list of filegalleries or inside a gallery if there is only one gallery
 */
function smarty_function_filegal_manager_url($params, &$smarty) {
	global $tikilib, $tikiroot_relative;

	$return = $tikiroot_relative . 'tiki-list_file_gallery.php?view=browse';
	$fgals = $tikilib->list_file_galleries();

	if ( $fgals['cant'] == 1 ) {
		$return .= '&galleryId=' . $fgals['data'][0]['id'] . '&filegals_manager=' . $params['area_name'];
	}
	if ( ! empty($params['area_name']) ) {
		$return .= '&filegals_manager=' . $params['area_name'];
	}

	return $return;
}    
