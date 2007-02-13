<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-user_contacts_prefs.php,v 1.5 2007-02-13 03:13:20 nyloth Exp $

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

if (isset($_REQUEST['ext_remove'])) $contactlib->remove_ext($user, $_REQUEST['ext_remove']);
if (isset($_REQUEST['ext_add'])) $contactlib->add_ext($user, $_REQUEST['ext_add']);
if (isset($_REQUEST['ext_show'])) $contactlib->modify_ext($user, $_REQUEST['ext_show'], array('show' => 'y'));
if (isset($_REQUEST['ext_hide'])) $contactlib->modify_ext($user, $_REQUEST['ext_hide'], array('show' => 'n'));

$exts =& $contactlib->get_ext_list($user);
$nb_exts = count($exts);

// consistancy check
foreach ( $exts as $k => $ext ) if ( $ext['order'] != $k ) $contactlib->modify_ext($user, $ext['fieldId'], array('order' => $k));

if (isset($_REQUEST['ext_up'])) {
	if ( is_array($exts) ) foreach ( $exts as $k => $ext ) {
		if ( $k > 0 && $ext['fieldId'] == $_REQUEST['ext_up'] ) {
			$contactlib->modify_ext($user, $_REQUEST['ext_up'], array('order' => $k - 1));
			$contactlib->modify_ext($user, $exts[$k-1]['fieldId'], array('order' => $k));
			break;
		}
	}
}
if (isset($_REQUEST['ext_down'])) {
	if ( is_array($exts) ) foreach ( $exts as $k => $ext ) {
		if ( $k < $nb_exts && $ext['fieldId'] == $_REQUEST['ext_down'] ) {
			$contactlib->modify_ext($user, $_REQUEST['ext_down'], array('order' => $k + 1));
			$contactlib->modify_ext($user, $exts[$k+1]['fieldId'], array('order' => $k));
			break;
		}
	}
}

$exts = $contactlib->get_ext_list($user);
$smarty->assign('exts', $exts);

include_once ('tiki-mytiki_shared.php');

//ask_ticket('user-contacts_prefs');

include_once('tiki-section_options.php');

// Display the template
$smarty->assign('mid', 'tiki-user_contacts_prefs.tpl');
$smarty->display("tiki.tpl");

?>
