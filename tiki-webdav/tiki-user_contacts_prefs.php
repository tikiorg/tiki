<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'mytiki';
require_once ('tiki-setup.php');
require_once ('lib/webmail/contactlib.php');

$access->check_feature('feature_contacts', '', 'community');
$access->check_user($user);

if (!isset($cookietab)) { $cookietab = '1'; }
if (isset($_REQUEST['prefs'])) {
	$tikilib->set_user_preference($user, 'user_contacts_default_view', $_REQUEST['user_contacts_default_view']);
	$cookietab = '1';
}
$smarty->assign('user_contacts_default_view', $tikilib->get_user_preference($user, 'user_contacts_default_view'), 'group');
if (isset($_REQUEST['ext_remove'])) {
	$contactlib->remove_ext($user, $_REQUEST['ext_remove']);
	$cookietab = 2;
}
if (isset($_REQUEST['ext_add'])) {
	$contactlib->add_ext($user, $_REQUEST['ext_add']);
	$cookietab = 2;
}
if (isset($_REQUEST['ext_show'])) {
	$contactlib->modify_ext($user, $_REQUEST['ext_show'], array('show' => 'y'));
	$cookietab = 2;
}
if (isset($_REQUEST['ext_hide'])) {
	$contactlib->modify_ext($user, $_REQUEST['ext_hide'], array('show' => 'n'));
	$cookietab = 2;
}
if (isset($_REQUEST['ext_public'])) {
	$contactlib->modify_ext($user, $_REQUEST['ext_public'], array('flagsPublic' => 'y'));
	$cookietab = 2;
}
if (isset($_REQUEST['ext_private'])) {
	$contactlib->modify_ext($user, $_REQUEST['ext_private'], array('flagsPublic' => 'n'));
	$cookietab = 2;
}
$exts = & $contactlib->get_ext_list($user);
$nb_exts = count($exts);
// consistancy check
foreach($exts as $k => $ext) if ($ext['order'] != $k) $contactlib->modify_ext($user, $ext['fieldId'], array('order' => $k));
if (isset($_REQUEST['ext_up'])) {
	if (is_array($exts)) foreach($exts as $k => $ext) {
		if ($k > 0 && $ext['fieldId'] == $_REQUEST['ext_up']) {
			$contactlib->modify_ext($user, $_REQUEST['ext_up'], array('order' => $k - 1));
			$contactlib->modify_ext($user, $exts[$k - 1]['fieldId'], array('order' => $k));
			break;
		}
	}
	$cookietab = 2;
}
if (isset($_REQUEST['ext_down'])) {
	if (is_array($exts)) foreach($exts as $k => $ext) {
		if ($k < $nb_exts && $ext['fieldId'] == $_REQUEST['ext_down']) {
			$contactlib->modify_ext($user, $_REQUEST['ext_down'], array('order' => $k + 1));
			$contactlib->modify_ext($user, $exts[$k + 1]['fieldId'], array('order' => $k));
			break;
		}
	}
	$cookietab = 2;
}
$exts = $contactlib->get_ext_list($user);
$smarty->assign('exts', $exts);
setcookie('tab', $cookietab);
$smarty->assign_by_ref('cookietab', $cookietab);
include_once ('tiki-mytiki_shared.php');
//ask_ticket('user-contacts_prefs');
include_once ('tiki-section_options.php');
// Display the template
$smarty->assign('mid', 'tiki-user_contacts_prefs.tpl');
$smarty->display("tiki.tpl");
