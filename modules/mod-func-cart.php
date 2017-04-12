<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_cart_info()
{
	return array(
		'name' => tra('Cart'),
		'description' => tra('Displays the content of the cart, allows quantities to be modified and proceeds to payment.'),
		'prefs' => array('payment_feature'),
		'params' => array(
			'ajax' => array(
				'name' => tra('Use AJAX'),
				'description' => tra('Use AJAX services for managing the cart') . ' (y/n)',
				'filter' => 'alpha',
				'default' => 'n',
			),
			'showItems' => array(
				'name' => tra('Show Items'),
				'description' => tra('Shows the items in the cart as they are added') . ' (y/n)',
				'filter' => 'alpha',
				'default' => 'y',
			),
			'showCount' => array(
				'name' => tra('Show Item Count'),
				'description' => tra('Shows the number of items in the cart') . ' (y/n)',
				'filter' => 'alpha',
				'default' => 'n',
			),
			'checkoutURL' => array(
				'name' => tra('Checkout URL'),
				'description' => tra('Where to go to when the "Check-out" button is clicked but before the payment invoice is generated') . ' ' . tr('(Default empty: Goes to tiki-payment.php)'),
				'filter' => 'url',
				'default' => '',
			),
			'postPaymentURL' => array(
				'name' => tra('Post-Payment URL'),
				'description' => tra('Where to go to once the payment has been generated, will append "?invoice=xx" parameter on the URL for use in pretty trackers etc.') . ' ' . tr('(Default empty: Goes to tiki-payment.php)'),
				'filter' => 'url',
				'default' => '',
			),
			'showWeight' => array(
				'name' => tra('Show Total Weight'),
				'description' => tra('Shows the weight of the items in the cart') . ' (y/n)',
				'filter' => 'alpha',
				'default' => 'n',
			),
			'weightUnit' => array(
				'name' => tra('Weight Unit'),
				'description' => tra('Shown after the weight'),
				'filter' => 'alpha',
				'default' => 'g',
			),
			'showItemButtons' => array(
				'name' => tra('Show Item Buttons'),
				'description' => tra('Shows add, remove and delete buttons on items') . ' (y/n)',
				'filter' => 'alpha',
				'default' => 'n',
			),
		),
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_cart($mod_reference, & $module_params)
{
	global $jitRequest;

	$smarty = TikiLib::lib('smarty');
	$access = TikiLib::lib('access');
	$cartlib = TikiLib::lib('cart');

	$info = module_cart_info();
	$defaults = array();
	foreach ($info['params'] as $key => $param) {
		$defaults[$key] = $param['default'];
	}

	if (!empty($module_params['ajax']) && $module_params['ajax'] === 'y') {
		$smarty->assign('json_data', ' data-params=\'' . json_encode(array_filter($module_params)) . '\'');
	} else {
		$smarty->assign('json_data', '');
	}

	$module_params = array_merge($defaults, $module_params);

	if ($jitRequest->update->text() && $cart = $jitRequest->cart->asArray()) {
		foreach ($cart as $code => $quantity) {
			$cartlib->update_quantity($code, $quantity);
		}

		if ($module_params['ajax'] !== 'y') {
			$access->redirect($_SERVER['REQUEST_URI'], tra('The quantities in your cart were updated.'));
		}
	}

	if (isset($_POST['checkout'])) {
		if ($module_params['checkoutURL']) {
			$access->redirect($module_params['checkoutURL']);
		} else {
			$invoice = $cartlib->request_payment();
	
			if ($invoice) {
				if ($module_params['postPaymentURL']) {
					$delimiter = (strpos($module_params['postPaymentURL'], '?') === false) ? '?' : '&';
					$access->redirect($module_params['postPaymentURL'] . $delimiter . 'invoice=' . intval($invoice), tr('The order was recorded and is now awaiting payment. Reference number is %0.', $invoice));
				} else {
					$access->redirect('tiki-payment.php?invoice=' . intval($invoice), tr('The order was recorded and is now awaiting payment. Reference number is %0.', $invoice));
				}
			}
		}
	}
	
	if ($cartlib->has_gift_certificate()) {
		if (!empty($_POST['gift_certificate_redeem_code'])) {
			$added = $cartlib->add_gift_certificate($_POST['gift_certificate_redeem_code']);
			if ($added) {
				$access->redirect($_SERVER['REQUEST_URI'], tra('Gift card added'));
			} else {
				$access->redirect($_SERVER['REQUEST_URI'], tra('Gift card not found'));
			}
		}
	
		if (isset($_POST['remove_gift_certificate'])) {
			$cartlib->add_gift_certificate();
			$access->redirect($_SERVER['REQUEST_URI'], tra('Gift card removed'));
		}
	
		$cartlib->get_gift_certificate();
	
		$smarty->assign('has_gift_certificate', true);
		$smarty->assign('gift_certificate_redeem_code', $cartlib->gift_certificate_code);
		$smarty->assign('gift_certificate_amount', $cartlib->gift_certificate_amount);
		$smarty->assign('gift_certificate_mode_symbol_before', $cartlib->gift_certificate_mode_symbol_before);
		$smarty->assign('gift_certificate_mode_symbol_after', $cartlib->gift_certificate_mode_symbol_after);
	}

	$smarty->assign('cart_total', $cartlib->get_total());
	$smarty->assign('cart_content', $cartlib->get_content());
	$smarty->assign('cart_weight', $cartlib->get_total_weight());
	$smarty->assign('cart_count', $cartlib->get_count());
}

