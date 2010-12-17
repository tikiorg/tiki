<?php 

function chkRegName($name) {	// AJAX_TODO
	global $smarty, $userlib;
	$pre_no = " <img src='pics/icons/exclamation.png' style='vertical-align: middle;' alt='Error' /> ";
	$pre_yes = " <img src='pics/icons/accept.png' style='vertical-align:middle' alt='Correct' /> ";
//	$ajaxlib->registerTemplate('tiki-register.tpl');
//	$objResponse = new xajaxResponse();
//	if ( empty($name) ) {
//		$objResponse->assign('ajax_msg_name', "innerHTML", $pre_no.tra("Missing User Name"));
//	} elseif ( $userlib->user_exists($name) ) {
//		$objResponse->assign('ajax_msg_name', "innerHTML", $pre_no.tra("User Already Exists"));
//	} else {
//		$objResponse->assign('ajax_msg_name', "innerHTML", $pre_yes.tra("Valid User Name"));
//	}
//	return $objResponse;
}

function chkRegEmail($mail) {	// AJAX_TODO
	global $smarty;
	$pre_no = " <img src='pics/icons/exclamation.png' style='vertical-align: middle;' alt='Error' /> ";
	$pre_yes = " <img src='pics/icons/accept.png' style='vertical-align:middle' alt='Correct' /> ";
//	$ajaxlib->registerTemplate('tiki-register.tpl');
//	$objResponse = new xajaxResponse();
//	if (empty($mail)) {
//		$objResponse->assign("ajax_msg_mail", "innerHTML", $pre_no.tra("Missing Email"));
//	} elseif (!preg_match('/^[_a-z0-9\.\-]+@[_a-z0-9\.\-]+\.[a-z]{2,4}$/i', $mail)) {
//		$objResponse->assign("ajax_msg_mail", "innerHTML", $pre_no.tra('This is not a valid mail address'));
//	} else {
//		$objResponse->assign("ajax_msg_mail", "innerHTML", $pre_yes.tra("Valid Email"));
//	}
//	return $objResponse;
}
