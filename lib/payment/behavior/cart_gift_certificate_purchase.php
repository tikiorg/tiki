<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function payment_behavior_cart_gift_certificate_purchase(
		$productId = 0,
		$giftcertemail = '',
		$quantity = 1,
		$orderId = 0,
		$orderItemId = 0
		)
{
	$trklib = TikiLib::lib('trk');
	global $prefs;
	$params['trackerId'] = $prefs['payment_cart_giftcert_tracker'];

	$redeemCodeFieldId = $trklib->get_field_id($params['trackerId'], 'Redeem Code');
	$nameFieldId = $trklib->get_field_id($params['trackerId'], 'Name');
	$modeFieldId = $trklib->get_field_id($params['trackerId'], 'Mode');
	$onelineDescriptionFieldId = $trklib->get_field_id($params['trackerId'], 'One line description');
	$longDescriptionFieldId = $trklib->get_field_id($params['trackerId'], 'Long Description');
	$origbalanceFieldId = $trklib->get_field_id($params['trackerId'], 'Original Balance or Percentage');
	$curbalanceFieldId = $trklib->get_field_id($params['trackerId'], 'Current Balance or Percentage');

	$params['copyFieldIds'][] = $nameFieldId;
	$params['copyFieldIds'][] = $trklib->get_field_id($params['trackerId'], 'Type');
	$params['copyFieldIds'][] = $trklib->get_field_id($params['trackerId'], 'Type Reference');
	$params['copyFieldIds'][] = $origbalanceFieldId;
	$params['copyFieldIds'][] = $modeFieldId;
	$params['copyFieldIds'][] = $onelineDescriptionFieldId;
	$params['updateFieldIds'][] = $trklib->get_field_id($params['trackerId'], 'Gift Certificate ID');
	$params['updateFieldIds'][] = $trklib->get_field_id($params['trackerId'], 'Origination');
	$params['updateFieldIds'][] = $redeemCodeFieldId;
	$params['updateFieldIds'][] = $curbalanceFieldId;
	$params['updateFieldIds'][] = $trklib->get_field_id($params['trackerId'], 'Admin notes');
	$params['updateFieldIds'][] = $trklib->get_field_id($params['trackerId'], 'Order ID');
	$params['updateFieldIds'][] = $trklib->get_field_id($params['trackerId'], 'Order Item ID');
	$balancefield = 'f_' .  $origbalanceFieldId;
	$params['updateFieldValues'] = array(
					'',
					'Order',
					'-randomstring-',
					$balancefield,
					"Purchased by $giftcertemail",
					$orderId,
					$orderItemId
	);

	// Product tracker info
	$productsTrackerId = $prefs['payment_cart_product_tracker'];
	$giftcertTemplateFieldId = $trklib->get_field_id($productsTrackerId, $prefs['payment_cart_giftcerttemplate_fieldname']);
	if (!$productId) {
		return false;
	}
	$giftcertId = $trklib->get_item_value($productsTrackerId, $productId, $giftcertTemplateFieldId);
	$params['itemId'] = $giftcertId;
	$params['copies_on_load'] = $quantity;
	$params['return_array'] = 'y';

	include_once ('lib/wiki-plugins/wikiplugin_trackeritemcopy.php');

	$return_array = wikiplugin_trackeritemcopy('', $params);
	$giftcerts = array();

	// Get additional information
	foreach ($return_array['items'] as $newItemId) {
		$newItem = $trklib->get_tracker_item($newItemId);
		$newGiftcert['name'] = $newItem[$nameFieldId];
		$newGiftcert['redeemCode'] = $newItem[$redeemCodeFieldId];
		$newGiftcert['onelineDescription'] = $newItem[$onelineDescriptionFieldId];
		$newGiftcert['longDescription'] = $newItem[$longDescriptionFieldId];
		$newGiftcert['value'] = $newItem[$curbalanceFieldId];
		if (strpos($newItem[$modeFieldId], 'Percentage') !== false) {
			$newGiftcert['isPercentage'] = true;
		} else {
			$newGiftcert['isPercentage'] = false;
		}
		$giftcerts[] = $newGiftcert;
	}
	// Send email to user with gift cert
	require_once('lib/webmail/tikimaillib.php');
	global $prefs;
	$smarty = TikiLib::lib('smarty');
	$smarty->assign('giftcerts', $giftcerts);
	$smarty->assign('numberCodes', count($return_array['items']));
	$mail_subject = $smarty->fetch('mail/cart_gift_cert_subject.tpl');
	$mail_data = $smarty->fetch('mail/cart_gift_cert.tpl');
	$mail = new TikiMail();
	$mail->setSubject($mail_subject);
	$mail->setText($mail_data);
	$mail->send($giftcertemail);

	return true;
}
