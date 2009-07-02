<?php

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

include_once('lib/score/scorelib.php');

if (isset($_REQUEST["scoreevents"])) {
	check_ticket('admin-inc-score');

	if (isset($_REQUEST['events']) && is_array($_REQUEST['events'])) {
	    $scorelib->update_events($_REQUEST['events']);
	}
}

$smarty->assign('events',$scorelib->get_all_events());

ask_ticket('admin-inc-score');
?>
