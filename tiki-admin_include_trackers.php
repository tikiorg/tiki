<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_trackers.php,v 1.4 2003-12-28 20:12:51 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
if (isset($_REQUEST["trkset"])) {
	check_ticket('admin-inc-trackers');
	$tikilib->set_preference('t_use_db', $_REQUEST["t_use_db"]);

	$tikilib->set_preference('t_use_dir', $_REQUEST["t_use_dir"]);
	$smarty->assign('t_use_db', $_REQUEST["t_use_db"]);
	$smarty->assign('t_use_dir', $_REQUEST["t_use_dir"]);
}
ask_ticket('admin-inc-trackers');
?>
