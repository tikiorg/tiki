<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_addtocart_info() {
	return array(
		'name' => tra('Add to cart'),
		'documentation' => tra('PluginAddToCart'),
		'description' => tra('Adds a product to the virtual cart. The cart can be manipulated using the cart module.'),
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
			'producttype' => array(
				'required' => false,
				'name' => tra('Product Type'),
				'description' => tra('The product type that is being sold, which will affect fulfillment, e.g. standard product, gift certificate, event ticket'),
				'filter' => 'text',
				'default' => '',
			),
			'productclass' => array(
				'required' => false,
				'name' => tra('Product Class'),
				'description' => tra('The class the product belongs to, can be used to limit how gift cards are used'),
				'filter' => 'text',
				'default' => ''
			),
			'productbundle' => array(
				'required' => false,
				'name' => tra('Product Bundle'),
				'description' => tra('The bundle the product belongs to, can be used to limit how gift cards are used, will automatically add other products in same class to cart'),
				'filter' => 'text',
				'default' => ''
			),
			'bundleclass' => array(
				'required' => false,
				'name' => tra('Bundle Class'),
				'description' => tra('The class the bundle belongs to, can be used to limit how gift cards are used'),
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
				'description' => tra('Automatically checkout for purchase and send user to pay (this is disabled when there is already something in the cart)'),
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
			'giftcertificate'=> array(
				'required' => false,
				'name' => tra('Gift certificate'),
				'description' => tra('Allows user to add gift certificate from the product view'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'exchangeorderitemid' => array( 
				'required' => false,
				'name' => tra('Order Item ID to exchange product'),
				'description' => tra('Used in conjunction with exchange feature'),
				'filter' => 'int',
				'default' => ''
			),
			'exchangetoproductid' => array(
				'required' => false,
				'name' => tra('Product ID to exchange to'),
				'desctiption' => tra('Used in conjunction with exchange feature'),
				'filter' => 'int',
				'default' => ''
			),
			'exchangeorderamount' => array(
				'required' => false,
				'name' => tra('Amount of new product to exchange for'),
				'description' => tra('Should normally be set to the amount of products in the order being exchanged'),
				'filter' => 'int',
				'default' => 1
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

	$code = $params['code'];
	$product_class = $params['productclass'];
	$product_type = $params['producttype'];
	$product_bundle = $params['productbundle'];
	$bundle_class = $params['bundleclass'];
	$gift_certificate = $params['giftcertificate'];
	$eventcode = $params['eventcode'];
	$price = preg_replace( '/[^\d^\.^,]/', '', $params['price']);
	$add_label = $params['label'];
// Custom2
	global $smarty;
	$smarty->assign('code', $code);
	$smarty->assign('productclass', $product_class );
	$smarty->assign('giftcertificate', $gift_certificate);
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

	if (!empty($params['exchangeorderitemid']) && !empty($params['exchangetoproductid'])) {
		$smarty->assign('exchangeorderitemid', $params['exchangeorderitemid']); 
		$smarty->assign('exchangetoproductid', $params['exchangetoproductid']); 
		$smarty->assign('hideamountfield', 'y');
	} else {
		$smarty->assign('hideamountfield', 'n');
	}

	if ( is_numeric($product_class) ) {
		global $cartlib, $headerlib; require_once 'lib/payment/cartlib.php';
		$information_form = $cartlib->get_missing_user_information_form( $product_class, 'required' );
		$missing_information = $cartlib->get_missing_user_information_fields( $product_class, 'required');
		$skip_information_form = $cartlib->skip_user_information_form_if_not_missing( $product_class ) && empty($missing_information); 
		if ( $information_form && !$skip_information_form ) {
			$headerlib->add_jq_onready("
				$('form.addProductToCartForm" . $product_class . "').each(function(i) {
					$(this)
						.unbind('submit')
						.submit(function() {
							var o = $('<div />')
								.load('tiki-index_raw.php?page=".urlencode($information_form)."', function() {
									o.dialog({
										title: '$information_form',
										modal: true,
										height: $(window).height() * 0.8,
										width: $(window).width() * 0.8
									});
									
									var loading = $('<div><span>Loading...</span><img src=\"img/loading.gif\" /></div>')
										.hide()
										.appendTo(o);
									
									var forms = o.find('form');
									forms.each(function() {
										var form = $(this).submit(function() {
											
											var satisfied = true;
											$('.mandatory_field').each(function() {
												var field = $(this).children().first();
												if (!field.val()) {
													$(this).addClass('ui-state-error');
													satisfied = false;
												}
											});
											if (!satisfied) return false;
											
											$.post(form.attr('action'), form.serialize(), function() {
												form.slideUp(function() {
													o.animate({
														scrollTop: form.next().offset().top
													});
												}).attr('satisfied', true);
												
												satisfied = true;
												
												forms.each(function() {
													if (!$(this).attr('satisfied')) {
														satisfied = false;
													}
												});
												
												if (satisfied) {
													loading
														.show()
														.prevAll().hide();
														
													$('form.addProductToCartForm').eq(i)
														.unbind('submit')
														.submit();
												}
											});
											
											return false;
										});
									});
								});
							return false;
						});
				});
			");
		} 
	}

	if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		global $jitPost, $access, $user; 
		if (!empty($params['exchangeorderitemid']) && !empty($params['exchangetoproductid'])) {	
			if ( $jitPost->exchangeorderitemid->int() == $params['exchangeorderitemid'] && $jitPost->exchangetoproductid->int() == $params['exchangetoproductid'] ) {
				$correct_exchange = true;
			} else {
				$correct_exchange = false;
			}
		} else {
			$correct_exchange = true;
		}
		$quantity = $jitPost->quantity->int();
		if( $jitPost->code->text() == $params['code'] && $quantity > 0 && $correct_exchange ) {
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
			
			$gift_certificate_error = tra("Invalid gift certificate: ");
			if ( $_REQUEST['gift_certificate'] && isset($gift_certificate) ) {
				if ( !$cartlib->add_gift_certificate( $_REQUEST['gift_certificate'] ) ) {
					$smarty->assign('gift_certificate', $_REQUEST['gift_certificate']);
					$smarty->assign('gift_certificate_error', $gift_certificate_error);
					return $smarty->fetch('wiki-plugins/wikiplugin_addtocart.tpl');//TODO: Notify user if gift certificate is invalid
				}
			}

			$product_info = array(
				'description' => $params['description'],
                'price' => $price,
                'href' => $params['href'],
                'behaviors' => $behaviors,
                'eventcode' => $eventcode,
                'onbehalf' => $onbehalf,
				'producttype' => $product_type,
				'productclass' => $product_class,
				'productbundle' => $product_bundle,
				'bundleclass' => $bundle_class
			);

			// Generate behavior for exchanges
			if (!empty($params['exchangeorderitemid']) && !empty($params['exchangetoproductid'])) {
				$product_info['behaviors'][] = array('event' => 'complete', 'behavior' => 'cart_exchange_product', 'arguments' => array($params["exchangeorderitemid"], $params["exchangetoproductid"])); 
				$product_info['exchangeorderitemid'] = $params["exchangeorderitemid"];
				$product_info['exchangetoproductid'] = $params["exchangetoproductid"];
				if (!isset($params['exchangeorderamount']) || !$params['exchangeorderamount']) {
					$exchangeorderamount = 1;
				} else {
					$exchangeorderamount = $params["exchangeorderamount"];
				}
				$product_info['exchangeorderamount'] = $exchangeorderamount;
			}
			// Generate behavior for gift certificate purchase
			if (strtolower($product_type) == 'gift certificate') {
				if ($onbehalf) {
					$giftcert_email = $userlib->get_user_email($onbehalf);
				} elseif (!$user && !empty($_SESSION['shopperinfo']['email'])) {
					$giftcert_email = $_SESSION['shopperinfo']['email'];
				} elseif ($user) {
					$giftcert_email = $userlib->get_user_email($user);
				}
				$product_info['behaviors'][] = array('event' => 'complete', 'behavior' => 'cart_gift_certificate_purchase', 'arguments' => array($code, $giftcert_email)); 
			}
			// Now add product to cart
			$previous_cart_content = $cartlib->get_content();
			$cartlib->add_product( $params['code'], $quantity, $product_info );

			global $access, $tikilib, $tikiroot, $prefs;
			if ($params['autocheckout'] == 'y' && empty($previous_cart_content)) {
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
	
	return $smarty->fetch('wiki-plugins/wikiplugin_addtocart.tpl');
}

