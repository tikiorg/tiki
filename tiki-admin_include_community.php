<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_community.php,v 1.3 2005-05-18 10:58:53 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (isset($_REQUEST["mouseoverfeatures"])) {
	check_ticket('admin-inc-community');
	simple_set_toggle("feature_community_mouseover");
	simple_set_toggle("feature_community_mouseover_name");
	simple_set_toggle("feature_community_mouseover_picture");
	simple_set_toggle("feature_community_mouseover_friends");
	simple_set_toggle("feature_community_mouseover_score");
	simple_set_toggle("feature_community_mouseover_country");
	simple_set_toggle("feature_community_mouseover_email");
	simple_set_toggle("feature_community_mouseover_lastlogin");
	simple_set_toggle("feature_community_mouseover_distance");
}

/* This is desired future feature
if (isset($_REQUEST["friendshipfeatures"])) {
	check_ticket('admin-inc-community');
	simple_set_toggle("feature_community_friends_permission");
	simple_set_int("feature_community_friends_permission_dep");

}
*/

ask_ticket('admin-inc-community');
?>


