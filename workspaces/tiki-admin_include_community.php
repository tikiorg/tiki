<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_community.php,v 1.5 2007-03-06 19:29:45 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (isset($_REQUEST["userfeatures"])) {
	check_ticket('admin-inc-community');
	simple_set_toggle("feature_community_gender");
	simple_set_toggle("feature_community_mouseover");
	simple_set_toggle("feature_community_mouseover_name");
	simple_set_toggle("feature_community_mouseover_gender");
	simple_set_toggle("feature_community_mouseover_picture");
	simple_set_toggle("feature_community_mouseover_friends");
	simple_set_toggle("feature_community_mouseover_score");
	simple_set_toggle("feature_community_mouseover_country");
	simple_set_toggle("feature_community_mouseover_email");
	simple_set_toggle("feature_community_mouseover_lastlogin");
	simple_set_toggle("feature_community_mouseover_distance");
}

if (isset($_REQUEST["userlistfeatures"])) {
	check_ticket('admin-inc-community');
	simple_set_toggle("feature_community_list_name");
	simple_set_toggle("feature_community_list_score");
	simple_set_toggle("feature_community_list_country");
	simple_set_toggle("feature_community_list_distance");
	simple_set_value("user_list_order");
}

ask_ticket('admin-inc-community');
