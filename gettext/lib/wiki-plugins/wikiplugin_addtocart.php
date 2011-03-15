<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_addtocart_info() {
	return array(
		'name' => tra('Add to Cart'),
		'documentation' => 'PluginAddToCart',
		'description' => tra(' Display a button for adding items to the shopping cart'),
		'prefs' => array( 'wikiplugin_addtocart', 'payment_feature' ),
		'filter' => 'wikicontent',
		'format' => 'html',
		'icon' => 'pics/icons/cart_add.png',
		'params' => array(
			'code' => array(
				'required' => true,
				'name' => tra('Product code'),
				'description' => tra('Unique identifier for the product. Two products with the same code will be the same and the information used will be the one of the first in.'),
				'filter' => 'text',
				'default' => ''
			),
			'description' => array(
				'required' => true,
				'name' => tra('Description'),
				'description' => tra('Label for the product in the cart.'),
				'filter' => 'text',
				'default' => ''
			),
			'price' => array(
				'required' => true,
				'name' => tra('Price'),
				'description' => tra('The price to charge for the item.'),
				'filter' => 'text',
				'default' => ''
			),
			'href' => array(
				'required' => false,
				'name' => tra('Location'),
				'description' => tra('URL of the product\'s information. The URL may be relative or absolute (begin with http://).'),
				'filter' => 'url',
				'default' => ''
			),
			'label' => array(
				'required' => false,
				'name' => tra('Button label'),
				'description' => tra('Text for the submit button. default: "Add to cart"'),
				'filter' => 'text',
				'default' => 'Add to cart'
			),
			'eventcode' => array(
                                'required' => false,
                                'name' => tra('Associated event code'),
                                'description' => tra('Unique identifier for the event that is associated to the product.'),
                                'filter' => 'text',
                                'default' => ''
                        ),
			'autocheckout' => array(
				'required' => false,
				'name' => tra('Automatically checkout'),
				'description' => tra('Automatically checkout for purchase and send user to pay'),
				'filter' => 'text',
				'default' => 'n'
			),
			'onbehalf' => array(
				'required' => false,
				'name' => tra('Buy on behalf of'),
				'description' => tra('Allows the selection of user to make purchase on behalf of'),
				'filter' => 'text',
				'default' => 'n'
			),
			'forceanon' => array(
				'required' => false,
				'name' => tra('Shop as anonymous always'),
				'description' => tra('Add to cart as anonymous shopper even if logged in'),
				'filter' => 'text',
				'default' => 'n'
			),
			'forwardafterfree' => array(
				'required' => false,
				'name' => tra('Forward to this url after free purchase'),
				'description' => tra('Forward to this url after free purchase'),
				'filter' => 'url',
				'default' => ''
			),
		),
	);
}

function wikiplugin_addtocart( $data, $params ) {
	if( ! session_id() ) {
		return WikiParser_PluginOutput::internalError( tra('A session must be active to use the cart.') );
	}
	
	if( ! isset( $params['code'], $params['description'], $params['price'] ) ) {
		return WikiParser_PluginOutput::argumentError( array_diff( array( 'code', 'description', 'price' ), array_keys( $params ) ) );
	}

	if( ! isset( $params['href'] ) ) {
		$params['href'] = null;
	}
	if (! isset($params['label'])) {
		$params['label'] = tra('Add to cart');
	}
	if (! isset($params['forceanon'])) {
		$params['forceanon'] = 'n';
	}
	// once forceanon is set it will have to affect the whole shopping cart otherwise it will be inconsistent
	if ($params['forceanon'] == 'y') {
		$_SESSION['forceanon'] = 'y'; 
	}
	foreach($params as &$p) {
		$p = trim($p);			// remove some line ends picked up in pretty tracker
	}

	require_once 'lib/smarty_tiki/modifier.escape.php';
	require_once 'lib/smarty_tiki/function.query.php';
	
	$code = smarty_modifier_escape( $params['code'] );
	$price = preg_replace( '/[^\d^\.^,]/', '', $params['price']);
	$add_label = $params['label'];

	global $smarty;
	$smarty->assign('code', $code);
	$smarty->assign('price', $price);
	$smarty->assign('add_label', $add_label);

	global $cartuserlist, $userlib, $globalperms;
	if (!isset($cartuserlist)) {
		$cartuserlist = $userlib->get_users_light();	
	}
	$smarty->assign('cartuserlist', $cartuserlist);

	if ($params['onbehalf'] == 'y' && $globalperms->payment_admin) {
		$smarty->assign('onbehalf', 'y');
	}
	$form = $smarty->fetch('wiki-plugins/wikiplugin_addtocart.tpl');

	if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		global $jitPost, $access, $user;

		$quantity = $jitPost->quantity->int();

		if( $jitPost->code->text() == $params['code'] && $quantity > 0 ) {
			global $cartlib; require_once 'lib/payment/cartlib.php';

			$behaviors = array();
			// Custom++ If not logged in require to submit user information before shopping
			if ((!$user || $params['forceanon'] == 'y') && empty($_SESSION['shopperinfo'])) {
				$access->redirect( $_SERVER['REQUEST_URI'], tr('Please enter your shopper information first') );   
			} // There needs to be a shopperinfo plugin on the page

			if ($globalperms->payment_admin && !empty($_POST['buyonbehalf']) && $userlib->user_exists($_POST['buyonbehalf'])) {
				$onbehalf = $_POST['buyonbehalf'];
			} else {
				$onbehalf = '';
			}
			$cartlib->add_product( $params['code'], $quantity, array(
				'description' => $params['description'],
				'price' => $price,
				'href' => $params['href'],
				'behaviors' => $behaviors,
				'eventcode' => $eventcode,
				'onbehalf' => $onbehalf,
			) );

			global $access, $tikilib, $tikiroot, $prefs;
			if ($params['autocheckout'] == 'y') {
				$invoice = $cartlib->request_payment();
				if( $invoice ) {
					$paymenturl = 'tiki-payment.php?invoice=' . intval( $invoice );
					$paymenturl = $tikilib->httpPrefix( true ) . $tikiroot . $paymenturl;
					if (!$user || $params['forceanon'] == 'y' && !Perms::get('payment', $invoice)->manual_payment) {
						// token access needs to be an optional feature
						// and needs to depend on auth_token_access pref
						require_once 'lib/auth/tokens.php';
						$tokenlib = AuthTokens::build( $prefs );
						$tokenpaymenturl = $tokenlib->includeToken( $paymenturl, array('Temporary Shopper','Anonymous') ); 
					} 
					if (Perms::get('payment', $invoice)->manual_payment) {
						// if able to do manual payment it means it is admin and don't need token
						$access->redirect( $paymenturl, tr('The order was recorded and is now awaiting payment. Reference number is %0.', $invoice) );
					} else {
						$access->redirect( $tokenpaymenturl, tr('The order was recorded and is now awaiting payment. Reference number is %0.', $invoice) );
					} 
				} else {
					if (!empty($params['forwardafterfree'])) {
						$access->redirect( $params['forwardafterfree'], tr('Your free order of %0 (%1) has been processed. An email has been sent to you for your records.', $params['description'], $quantity ) );
					} else { 
						$access->redirect( $_SERVER['REQUEST_URI'], tr('Your free order of %0 (%1) has been processed', $params['description'], $quantity ) );	
					}
				}
				die;
			}
			$access->redirect( $_SERVER['REQUEST_URI'], tr('%0 (%1) was added to your cart', $params['description'], $quantity ) );
		}
	}
	
	return $form;
}

