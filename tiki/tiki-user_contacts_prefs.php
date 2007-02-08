<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-user_contacts_prefs.php,v 1.2 2007-02-08 19:19:47 niclone Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'mytiki';
require_once ('tiki-setup.php');
require_once ('lib/webmail/contactlib.php');

if (!$user) {
	$smarty->assign('msg', tra("You must log in to use this feature"));
	$smarty->display("error.tpl");
	die;
}

if ($feature_contacts != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_contacts");
	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST['ext_remove'])) {
	$contactlib->remove_ext($user, pack('H*', $_REQUEST['ext_remove']));
}

if (isset($_REQUEST['ext_add'])) {
	$contactlib->add_ext($user, $_REQUEST['ext_add']);
}

$tmpexts=$contactlib->get_ext_list($user);
$exts=array();
foreach($tmpexts as $ext) $exts[bin2hex($ext)]=$ext;

$smarty->assign('exts', $exts);

include_once ('tiki-mytiki_shared.php');

//ask_ticket('user-contacts_prefs');

include_once('tiki-section_options.php');

// Display the template
$smarty->assign('mid', 'tiki-user_contacts_prefs.tpl');
$smarty->display("tiki.tpl");

?>
