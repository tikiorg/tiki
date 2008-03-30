<?php
// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_jukebox.php,v 1.7.2.1 2007-11-04 22:08:04 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// Admin file for Jukebox settings
// Damian aka Damosoft looks after this feature

if (isset($_REQUEST["filesset"]) && isset($_REQUEST["feature_jukebox_files"])) {
	check_ticket('admin-inc-jukebox');
	if (substr($_REQUEST["feature_jukebox_files"], -1) != "\\" && substr($_REQUEST["feature_jukebox_files"], -1) != "/" && $_REQUEST["feature_jukebox_files"] != "") {
		$_REQUEST["feature_jukebox_files"] .= "/";
	}
	$tikilib->set_preference("feature_jukebox_files", $_REQUEST["feature_jukebox_files"]);
}

if (isset($_REQUEST["jukeboxfeatures"])) {
	check_ticket('admin-inc-jukebox');
	$tikilib->set_preference("jukebox_list_order", $_REQUEST["jukebox_list_order"]);
	$tikilib->set_preference("jukebox_list_user", $_REQUEST["jukebox_list_user"]);
}

if (isset($_REQUEST['jukeboxalbumlistconf'])) {
	check_ticket('admin-inc-jukebox');
	if (isset($_REQUEST["jukebox_album_list_title"]) && $_REQUEST["jukebox_album_list_title"] == "on") {
		$tikilib->set_preference("jukebox_album_list_title", 'y');
	} else {
		$tikilib->set_preference("jukebox_album_list_title", 'n');
	}

	if (isset($_REQUEST["jukebox_album_list_description"]) && $_REQUEST["jukebox_album_list_description"] == "on") {
		$tikilib->set_preference("jukebox_album_list_description", 'y');
	} else {
		$tikilib->set_preference("jukebox_album_list_description", 'n');
	}

	if (isset($_REQUEST["jukebox_album_list_visits"]) && $_REQUEST["jukebox_album_list_visits"] == "on") {
		$tikilib->set_preference("jukebox_album_list_visits", 'y');
	} else {
		$tikilib->set_preference("jukebox_album_list_visits", 'n');
	}

	if (isset($_REQUEST["jukebox_album_list_tracks"]) && $_REQUEST["jukebox_album_list_tracks"] == "on") {
		$tikilib->set_preference("jukebox_album_list_tracks", 'y');
	} else {
		$tikilib->set_preference("jukebox_album_list_tracks", 'n');
	}

	if (isset($_REQUEST["jukebox_album_list_lastmodif"]) && $_REQUEST["jukebox_album_list_lastmodif"] == "on") {
		$tikilib->set_preference("jukebox_album_list_lastmodif", 'y');
	} else {
		$tikilib->set_preference("jukebox_album_list_lastmodif", 'n');
	}

	if (isset($_REQUEST["jukebox_album_list_user"]) && $_REQUEST["jukebox_album_list_user"] == "on") {
		$tikilib->set_preference("jukebox_album_list_user", 'y');
	} else {
		$tikilib->set_preference("jukebox_album_list_user", 'n');
	}

	if (isset($_REQUEST["jukebox_album_list_created"]) && $_REQUEST["jukebox_album_list_created"] == "on") {
		$tikilib->set_preference("jukebox_album_list_created", 'y');
	} else {
		$tikilib->set_preference("jukebox_album_list_created", 'n');
	}

	if (isset($_REQUEST["jukebox_album_list_genre"]) && $_REQUEST["jukebox_album_list_genre"] == "on") {
		$tikilib->set_preference("jukebox_album_list_genre", 'y');
	} else {
		$tikilib->set_preference("jukebox_album_list_genre", 'n');
	}
}

ask_ticket('admin-inc-jukebox');

?>
