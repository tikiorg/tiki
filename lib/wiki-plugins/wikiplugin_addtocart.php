<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_addtocart_info()
{
	return array(
		'name' => tra('Add to cart'),
		'documentation' => tra('PluginAddToCart'),
		'description' => tra('Add a product to the virtual cart'),
		'prefs' => array( 'wikiplugin_addtocart', 'payment_feature' ),
		'filter' => 'wikicontent',
		'format' => 'html',
		'introduced' => 5,
		'iconname' => 'cart',
		'tags' => array( 'basic' ),
		'params' => array(
			'code' => array(
				'required' => true,
				'name' => tra('Product code'),
				'description' => tra('Unique identifier for the product. Two products with the same code will be the same and the information used will be the one of the first in.'),
				'filter' => 'text',
				'since' => '5.0',
				'default' => '',
			),
			'description' => array(
				'required' => true,
				'name' => tra('Description'),
				'description' => tra('Label for the product in the cart.'),
				'filter' => 'text',
				'since' => '5.0',
				'default' => '',
			),
			'producttype' => array(
				'required' => false,
				'name' => tra('Product Type'),
				'description' => tra('The product type that is being sold, which will affect fulfillment, e.g. standard product, gift certificate, event ticket'),
				'filter' => 'text',
				'default' => '',
				'since' => '7.0',
			),
			'productclass' => array(
				'required' => false,
				'name' => tra('Product Class'),
				'description' => tra('The class the product belongs to, can be used to limit how gift cards are used'),
				'filter' => 'text',
				'default' => '',
				'since' => '7.0',
			),
			'productbundle' => array(
				'required' => false,
				'name' => tra('Product Bundle'),
				'description' => tra('The bundle the product belongs to, can be used to limit how gift cards are used, will automatically add other products in same class to cart'),
				'filter' => 'text',
				'default' => '',
				'since' => '7.0',
			),
			'bundleclass' => array(
				'required' => false,
				'name' => tra('Bundle Class'),
				'description' => tra('The class the bundle belongs to, can be used to limit how gift cards are used'),
				'filter' => 'text',
				'default' => '',
				'since' => '7.0',
			),
			'price' => array(
				'required' => true,
				'name' => tra('Price'),
				'description' => tra('The price to charge for the item.'),
				'filter' => 'text',
				'since' => '5.0',
				'default' => '',
			),
			'href' => array(
				'required' => false,
				'name' => tra('Location'),
				'description' => tr('URL of the product\'s information. The URL may be relative or absolute (begin
					with %0http://%1).', '<code>', '</code>'),
				'filter' => 'url',
				'since' => '5.0',
				'default' => '',
			),
			'label' => array(
				'required' => false,
				'name' => tra('Button label'),
				'description' => tra('Text for the submit button. default:') . ' ' . '"' . tra('Add to cart') . '"',
				'since' => '6.0',
				'filter' => 'text',
				'default' => 'Add to cart',
			),
			'eventcode' => array(
				'required' => false,
				'name' => tra('Associated event code'),
				'description' => tra('Unique identifier for the event that is associated to the product.'),
				'filter' => 'text',
				'default' => '',
				'since' => '7.0',
			),
			'autocheckout' => array(
				'required' => false,
				'name' => tra('Automatically checkout'),
				'description' => tra('Automatically checkout for purchase and send user to pay (this is disabled when there is already something in the cart)'),
				'since' => '7.0',
				'filter' => 'text',
				'default' => 'n',
			),
			'onbehalf' => array(
				'required' => false,
				'name' => tra('Buy on behalf of'),
				'description' => tra('Allows the selection of user to make purchase on behalf of'),
				'filter' => 'text',
				'default' => 'n',
				'since' => '7',
			),
			'forceanon' => array(
				'required' => false,
				'name' => tra('Shop as anonymous always'),
				'description' => tra('Add to cart as anonymous shopper even if logged in'),
				'filter' => 'text',
				'default' => 'n',
				'since' => '7.0',
			),
			'forwardafterfree' => array(
				'required' => false,
				'name' => tra('Forward to this URL after free purchase'),
				'description' => tra('Forward to this URL after free purchase'),
				'filter' => 'url',
				'default' => '',
				'since' => '7.0',
			),
			'giftcertificate'=> array(
				'required' => false,
				'name' => tra('Gift certificate'),
				'description' => tra('Allows user to add gift certificate from the product view'),
				'filter' => 'alpha',
				'default' => 'n',
				'since' => '7.0',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'exchangeorderitemid' => array(
				'required' => false,
				'name' => tra('Order Item ID to exchange product'),
				'description' => tra('Used in conjunction with exchange feature'),
				'filter' => 'int',
				'default' => '',
				'profile_reference' => 'tracker_item',
				'since' => '7.0',
			),
			'exchangetoproductid' => array(
				'required' => false,
				'name' => tra('Product ID to exchange to'),
				'desctiption' => tra('Used in conjunction with exchange feature'),
				'filter' => 'int',
				'since' => '5.0',
				'default' => '',
			),
			'exchangeorderamount' => array(
				'required' => false,
				'name' => tra('Amount of new product to exchange for'),
				'description' => tra('Should normally be set to the amount of products in the order being exchanged'),
				'filter' => 'int',
				'default' => 1,
				'since' => '7.0',
			),
			'ajaxaddtocart' => array(
				'required' => false,
				'name' => tra('Ajax add to cart feature'),
				'description' => tra('Attempts to turn ajax for cart on'),
				'filter' => 'alpha',
				'since' => '7.0',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'weight' => array(
				'required' => false,
				'name' => tra('Weight'),
				'description' => tra('The weight of the item.'),
				'filter' => 'text',
				'default' => '',
				'since' => '12.1',
			),
		),
	);
}

function wikiplugin_addtocart( $data, $params )
{
	global $cartuserlist, $globalperms;

	$smarty = TikiLib::lib('smarty');
	$userlib = TikiLib::lib('user');
	$headerlib = TikiLib::lib('header');
	$cartlib = TikiLib::lib('cart');

	if ( ! session_id() ) {
		session_start();
	}
	if ( ! isset( $params['code'], $params['description'], $params['price'] ) ) {
		return WikiParser_PluginOutput::argumentError(array_diff(array( 'code', 'description', 'price'), array_keys($params)));
	}

	$plugininfo = wikiplugin_addtocart_info();
	$default = array();
	foreach ($plugininfo['params'] as $key => $param) {
		$default["$key"] = $param['default'];
	}
	$params = array_merge($default, $params);

	// once forceanon is set it will have to affect the whole shopping cart otherwise it will be inconsistent
	if ($params['forceanon'] == 'y') {
		$_SESSION['forceanon'] = 'y';
	}
	foreach ($params as &$p) {
		$p = trim($p);			// remove some line ends picked up in pretty tracker
	}

	$params['price'] = preg_replace('/[^\d^\.^,]/', '', $params['price']);

	$smarty->assign('params', $params);

	if ($params['onbehalf'] == 'y' && $globalperms->payment_admin) {
		$smarty->assign('onbehalf', 'y');

		// Do not load the user list unless it is needed, this light function is not as light as one would expect
		if (!isset($cartuserlist)) {
			$cartuserlist = $userlib->get_users_light();
		}
		$smarty->assign('cartuserlist', $cartuserlist);
	}

	if (!empty($params['exchangeorderitemid']) && !empty($params['exchangetoproductid'])) {
		$smarty->assign('hideamountfield', 'y');
	} else {
		$smarty->assign('hideamountfield', 'n');
	}

	if ( is_numeric($params['productclass']) ) {
		$information_form = $cartlib->get_missing_user_information_form($params['productclass'], 'required');
		$missing_information = $cartlib->get_missing_user_information_fields($params['productclass'], 'required');
		$skip_information_form = $cartlib->skip_user_information_form_if_not_missing($params['productclass']) && empty($missing_information);
		if ( $information_form && !$skip_information_form ) {
			$headerlib->add_jq_onready(
				"$('form.addProductToCartForm{$params['productclass']}')
					.cartProductClassMissingForm({
						informationForm: '$information_form'
					});"
			);
		}
	}

	if ( $params['ajaxaddtocart'] == 'y' ) {
		$headerlib->add_jq_onready("$('.wp_addtocart_form').cartAjaxAdd();");
		$smarty->assign('form_data', ' data-params=\'' . str_replace("'", "\u0027", json_encode(array_filter($params))) . '\'');
	} else {
		$smarty->assign('form_data', '');
	}

	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		global $jitPost, $user;

		$quantity = $jitPost->quantity->int();
		if ( $jitPost->code->text() == $params['code'] && $quantity > 0) {

			$previous_cart_content = $cartlib->get_content();

			$addedOk = $cartlib->add_to_cart($params, $jitPost);

			global $tikiroot, $prefs;
			$access = TikiLib::lib('access');
			$tikilib = TikiLib::lib('tiki');

			if ($addedOk && $params['autocheckout'] == 'y' && empty($previous_cart_content)) {
				$invoice = $cartlib->request_payment();
				if ( $invoice ) {
					$paymenturl = 'tiki-payment.php?invoice=' . intval($invoice);
					$paymenturl = $tikilib->httpPrefix(true) . $tikiroot . $paymenturl;
					if (!$user || $params['forceanon'] == 'y' && !Perms::get('payment', $invoice)->manual_payment) {
						// token access needs to be an optional feature
						// and needs to depend on auth_token_access pref
						require_once 'lib/auth/tokens.php';
						$tokenlib = AuthTokens::build($prefs);
						$tokenpaymenturl = $tokenlib->includeToken($paymenturl, array('Temporary Shopper','Anonymous'));
					}
					if ($globalperms->payment_admin || Perms::get('payment', $invoice)->manual_payment) {
						// if able to do manual payment it means it is admin and don't need token
						$access->redirect($paymenturl, tr('The order was recorded and is now awaiting payment. Reference number is %0.', $invoice));
					} else {
						$access->redirect($tokenpaymenturl, tr('The order was recorded and is now awaiting payment. Reference number is %0.', $invoice));
					}
				} else {
					if (!empty($params['forwardafterfree'])) {
						$access->redirect($params['forwardafterfree'], tr('Your free order of %0 (%1) has been processed. An email has been sent to you for your records.', $params['description'], $quantity));
					} else {
						$access->redirect($_SERVER['REQUEST_URI'], tr('Your free order of %0 (%1) has been processed', $params['description'], $quantity));
					}
				}
				die;
			}
			$access->redirect($_SERVER['REQUEST_URI'], tr('%0 (%1) was added to your cart', $params['description'], $quantity));
		}
	}

	return $smarty->fetch('wiki-plugins/wikiplugin_addtocart.tpl');
}

