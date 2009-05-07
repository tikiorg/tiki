<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_polls.php,v 1.14 2007-10-12 07:55:24 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


if (isset($_REQUEST["pollprefs"])) {
	check_ticket('admin-inc-polls');
	simple_set_value('poll_comments_per_page');
	simple_set_value('poll_comments_default_ordering');
	simple_set_toggle('feature_poll_comments');
	simple_set_toggle('feature_poll_anonymous');
	simple_set_toggle('feature_poll_revote');
	simple_set_toggle('poll_list_categories');
	simple_set_toggle('poll_list_objects');
}
$headerlib->add_cssfile('css/admin.css');

ask_ticket('admin-inc-polls');
?>
