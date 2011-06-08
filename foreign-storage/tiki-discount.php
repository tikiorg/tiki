<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
include_once('tiki-setup.php');
global $discountlib; include_once('lib/payment/discountlib.php');
$access->check_permission( array('tiki_p_admin') );

$auto_query_args = array();
$errors = array();
$tab = 1;

if (!empty($_REQUEST['save']) && !empty($_REQUEST['code'])) {
	if (empty($_REQUEST['value']) && !empty($_REQUEST['percent'])) {
		$_REQUEST['percent'] = min(100, intval($_REQUEST['percent']));
		$_REQUEST['value'] = $_REQUEST['percent'].'%';
	} elseif (!empty($_REQUEST['value'])) {
		$_REQUEST['value'] = intval($_REQUEST['value']);
	}
	if (!empty($_REQUEST['value'])) {
		$default = array('id'=>0);
		$_REQUEST = array_merge($default, $_REQUEST);
		if (!$discountlib->replace_discount($_REQUEST)) {
			$errors[] = tra('Discount code already exists');
			$smarty->assign_by_ref('info', $_REQUEST);
			$tab = 2;
		} else {
			unset($_REQUEST['id']);
			$tab = 1;
		}
	}
}
if (!empty($_REQUEST['del'])) {
	check_ticket('discount');
	$discountlib->del_discount($_REQUEST['del']);
	$tab = 1;
}

if (!empty($_REQUEST['id'])) {
	if ($info = $discountlib->get_discount($_REQUEST['id'])) {
		if (strstr($info['value'], '%')) {
			$info['percent'] = intval($info['value']);
		}
		$smarty->assign_by_ref('info', $info);
		$tab = 1;
	}
}

$offset = isset($_REQUEST['offset'])? $_REQUEST['offset']: 0;
$max = $prefs['maxRecords'];
$discounts = $discountlib->list_discounts($offset, $max);
$discounts['offset'] = $offset;
$discounts['max'] = $max;
$smarty->assign_by_ref('discounts', $discounts);

setcookie('tab', $tab);
$smarty->assign_by_ref('cookietab', $tab);
$smarty->assign_by_ref('errors', $errors);
ask_ticket('discount');
$smarty->assign('mid', 'tiki-discount.tpl');
$smarty->display('tiki.tpl');
