<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


function module_user_blogs_info() {
	return array(
		'name' => tra('User Blogs'),
		'description' => tra('Displays to registered users their blogs.'),
		'prefs' => array( 'feature_blogs' ),
		'params' => array(),
		'common_params' => array("nonums")
	);
}

function module_user_blogs( $mod_reference, $module_params ) {
	global $user, $tikilib, $smarty;
	if ($user) {
		$ranking = $tikilib->list_user_blogs($user, false);
		
		$smarty->assign('modUserBlogs', $ranking);
		$smarty->assign('tpl_module_title', tra("My blogs"));
	}
}
