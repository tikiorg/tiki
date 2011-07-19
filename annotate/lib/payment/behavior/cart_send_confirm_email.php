<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: sample.php 25244 2010-02-16 06:26:12Z changi67 $

function payment_behavior_cart_send_confirm_email( $u, $email_template_ids = array() ) {
	global $prefs, $smarty, $userlib;
	require_once('lib/webmail/tikimaillib.php');
	$email = $userlib->get_user_email($u);
	if (!$email) return false;
	$smarty->assign("email_template_ids", $email_template_ids);
	$mail_subject = $smarty->fetch('mail/cart_order_received_reg_subject.tpl');
	$mail_data = $smarty->fetch('mail/cart_order_received_reg.tpl');
	$mail = new TikiMail();
	$mail->setSubject($mail_subject);
	if ($mail_data == strip_tags($mail_data)) {
		$mail->setText($mail_data);
	} else {
		$mail->setHtml($mail_data);
	}
	$mail->setHeader("From", $prefs['sender_email']);
	$mail->send($email);
	return true; 
}