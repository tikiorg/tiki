<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// THIS FILE IS CURRENTLY UNUSED

require_once ('tiki-setup.php');
require_once ('lib/ajax/ajaxlib.php');

if ($prefs['feature_ajax'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled") . ": feature_ajax");
	$smarty->display("error.tpl");
	die;
}

function chkRegName($name) {
	global $smarty, $ajaxlib, $userlib;
//	$ajaxlib->registerTemplate('tiki-register.tpl');
	$pre_no = " <img src='pics/icons/exclamation.png' style='vertical-align: middle;' alt='Error' /> ";
	$pre_yes = " <img src='pics/icons/accept.png' style='vertical-align:middle' alt='Correct' /> ";
	$objResponse = new xajaxResponse();
	if ( empty($name) ) {
		$objResponse->assign('ajax_msg_name', "innerHTML", $pre_no.tra("Missing User Name"));
	} elseif ( $userlib->user_exists($name) ) {
		$objResponse->assign('ajax_msg_name', "innerHTML", $pre_no.tra("User Already Exists"));
	} else {
		$objResponse->assign('ajax_msg_name', "innerHTML", $pre_yes.tra("Valid User Name"));
	}
	return $objResponse;
}

function chkRegEmail($mail) {
	global $smarty, $ajaxlib;
//	$ajaxlib->registerTemplate('tiki-register.tpl');
	$pre_no = " <img src='pics/icons/exclamation.png' style='vertical-align: middle;' alt='Error' /> ";
	$pre_yes = " <img src='pics/icons/accept.png' style='vertical-align:middle' alt='Correct' /> ";
	$objResponse = new xajaxResponse();
	if (empty($mail)) {
		$objResponse->assign("ajax_msg_mail", "innerHTML", $pre_no.tra("Missing Email"));
	} elseif (!eregi("^[_a-z0-9\.\-]+@[_a-z0-9\.\-]+\.[a-z]{2,4}$", $mail)) {
		$objResponse->assign("ajax_msg_mail", "innerHTML", $pre_no.tra('This is not a valid mail adress'));
	} else {
		$objResponse->assign("ajax_msg_mail", "innerHTML", $pre_yes.tra("Valid Email"));
	}
	return $objResponse;
}


// xajax
//$ajaxlib->setRequestURI('tiki-register_ajax.php');
//$ajaxlib->registerFunction('AJAXCheckUserName');
//$ajaxlib->registerFunction('AJAXCheckMail');
//$ajaxlib->processRequests();
