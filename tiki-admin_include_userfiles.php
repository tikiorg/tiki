<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_userfiles.php,v 1.4 2003-12-28 20:12:51 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
if (isset($_REQUEST["userfilesprefs"])) {
	check_ticket('admin-inc-userfiles');
	$tikilib->set_preference("uf_use_db", $_REQUEST["uf_use_db"]);

	$tikilib->set_preference("uf_use_dir", $_REQUEST["uf_use_dir"]);
	$tikilib->set_preference("userfiles_quota", $_REQUEST["userfiles_quota"]);
	$smarty->assign('uf_use_db', $_REQUEST["uf_use_db"]);
	$smarty->assign('uf_use_dir', $_REQUEST["uf_use_dir"]);
	$smarty->assign('userfiles_quota', $_REQUEST['userfiles_quota']);
}
ask_ticket('admin-inc-userfiles');
?>
