<?php
function wikiplugin_topfriends_help() {
	return tra("List top scoring users").":<br />~np~{TOPFRIENDS(limit=>5,public=>y)}{TOPFRIENDS}~/np~";
}

function wikiplugin_topfriends($data, $params) {
	global $smarty;
	global $feature_friends;
	global $tiki_p_list_users;
	global $tikilib;
	
	/* Check we can be called */
	if($feature_friends != 'y') {
		return ' ';  
	}
	extract ($params);

	if(!(isset($limit) && $limit <> '')) {
		$limit = 5;
	}

	if((isset($public) && $public != 'y') && ($tiki_p_list_users != 'y')) {
		// Access denied
		return ' ';
	}

	$sort_mode = 'score_desc'; $find = '';
	$listusers = $tikilib->list_users(0 , $limit, $sort_mode, $find);
	$smarty->assign_by_ref('listusers', $listusers["data"]);

	return $smarty->fetch('plugins/plugin-topfriends.tpl');
}

?>

