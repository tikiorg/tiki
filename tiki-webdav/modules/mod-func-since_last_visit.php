<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_since_last_visit_info() {
	return array(
		'name' => tra('Since last visit (simple)'),
		'description' => tra('Displays to logged in users the number of new or updated objects since their last login date and time.')
	);
}

function module_since_last_visit($mod_reference, $params = null) {
	global $user, $tikilib, $smarty;
	$nvi_info = $tikilib->get_news_from_last_visit($user);

	$smarty->assign('nvi_info', $nvi_info);
	$smarty->assign('tpl_module_title', tra('Since your last visit'));
}
