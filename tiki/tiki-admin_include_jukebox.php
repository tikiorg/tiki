<?php

// Admin file for Jukebox settings
// Damian aka Damosoft looks after this feature

if (isset($_REQUEST["filesset"]) && isset($_REQUEST["feature_jukebox_files"])) {
	check_ticket('admin-inc-jukebox');
	$tikilib->set_preference("feature_jukebox_files", $_REQUEST["feature_jukebox_files"]);
	$smarty->assign('feature_jukebox_files', $_REQUEST["feature_jukebox_files"]);
}

if (isset($_REQUEST["jukeboxfeatures"])) {
	check_ticket('admin-inc-jukebox');

	$tikilib->set_preference("jukebox_list_order", $_REQUEST["jukebox_list_order"]);
	$tikilib->set_preference("jukebox_list_user", $_REQUEST["jukebox_list_user"]);
	$smarty->assign('jukebox_list_order', $_REQUEST["jukebox_list_order"]);
	$smarty->assign('jukebox_list_user', $_REQUEST['jukebox_list_user']);
}

if (isset($_REQUEST['jukeboxalbumlistconf'])) {
	check_ticket('admin-inc-jukebox');
	if (isset($_REQUEST["jukebox_album_list_title"]) && $_REQUEST["jukebox_album_list_title"] == "on") {
		$tikilib->set_preference("jukebox_album_list_title", 'y');

		$smarty->assign("jukebox_album_list_title", 'y');
	} else {
		$tikilib->set_preference("jukebox_album_list_title", 'n');

		$smarty->assign("jukebox_album_list_title", 'n');
	}

	if (isset($_REQUEST["jukebox_album_list_description"]) && $_REQUEST["jukebox_album_list_description"] == "on") {
		$tikilib->set_preference("jukebox_album_list_description", 'y');

		$smarty->assign("jukebox_album_list_description", 'y');
	} else {
		$tikilib->set_preference("jukebox_album_list_description", 'n');

		$smarty->assign("jukebox_album_list_description", 'n');
	}

	if (isset($_REQUEST["jukebox_album_list_visits"]) && $_REQUEST["jukebox_album_list_visits"] == "on") {
		$tikilib->set_preference("jukebox_album_list_visits", 'y');

		$smarty->assign("jukebox_album_list_visits", 'y');
	} else {
		$tikilib->set_preference("jukebox_album_list_visits", 'n');

		$smarty->assign("jukebox_album_list_visits", 'n');
	}

	if (isset($_REQUEST["jukebox_album_list_tracks"]) && $_REQUEST["jukebox_album_list_tracks"] == "on") {
		$tikilib->set_preference("jukebox_album_list_tracks", 'y');

		$smarty->assign("jukebox_album_list_tracks", 'y');
	} else {
		$tikilib->set_preference("jukebox_album_list_tracks", 'n');

		$smarty->assign("jukebox_album_list_tracks", 'n');
	}

	if (isset($_REQUEST["jukebox_album_list_lastmodif"]) && $_REQUEST["jukebox_album_list_lastmodif"] == "on") {
		$tikilib->set_preference("jukebox_album_list_lastmodif", 'y');

		$smarty->assign("jukebox_album_list_lastmodif", 'y');
	} else {
		$tikilib->set_preference("jukebox_album_list_lastmodif", 'n');

		$smarty->assign("jukebox_album_list_lastmodif", 'n');
	}

	if (isset($_REQUEST["jukebox_album_list_user"]) && $_REQUEST["jukebox_album_list_user"] == "on") {
		$tikilib->set_preference("jukebox_album_list_user", 'y');

		$smarty->assign("jukebox_album_list_user", 'y');
	} else {
		$tikilib->set_preference("jukebox_album_list_user", 'n');

		$smarty->assign("jukebox_album_list_user", 'n');
	}

	if (isset($_REQUEST["jukebox_album_list_created"]) && $_REQUEST["jukebox_album_list_created"] == "on") {
		$tikilib->set_preference("jukebox_album_list_created", 'y');

		$smarty->assign("jukebox_album_list_created", 'y');
	} else {
		$tikilib->set_preference("jukebox_album_list_created", 'n');

		$smarty->assign("jukebox_album_list_created", 'n');
	}

	if (isset($_REQUEST["jukebox_album_list_genre"]) && $_REQUEST["jukebox_album_list_genre"] == "on") {
		$tikilib->set_preference("jukebox_album_list_genre", 'y');

		$smarty->assign("jukebox_album_list_genre", 'y');
	} else {
		$tikilib->set_preference("jukebox_album_list_genre", 'n');

		$smarty->assign("jukebox_album_list_genre", 'n');
	}
}

ask_ticket('admin-inc-jukebox');

?>
