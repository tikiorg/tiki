<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_addtocart_info() {
	return array(
		'name' => tra('Add to cart'),
		'documentation' => 'PluginAddToCart',
		'description' => tra(' Display a button for adding items to the shopping cart'),
		'prefs' => array( 'wikiplugin_addtocart', 'payment_feature' ),
		'filter' => 'wikicontent',
		'format' => 'html',
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

	foreach($params as &$p) {
		$p = trim($p);			// remove some line ends picked up in pretty tracker
	}

	require_once 'lib/smarty_tiki/modifier.escape.php';
	require_once 'lib/smarty_tiki/function.query.php';
	
	$code = smarty_modifier_escape( $params['code'] );
	$price = preg_replace( '/[^\d^\.^,]/', '', $params['price']);
	$add_label = smarty_modifier_escape( $params['label'] );
	$return_uri = smarty_function_query( array('_type' => 'relative', '_keepall' => 'y'), $smarty);
	
	$form = <<<FORM
<form method="post" action="$return_uri" style="display: inline;">
	<input type="hidden" name="code" value="$code"/>
	<input type="text" name="quantity" value="1" size="2"/>
	<input type="submit" value="$add_label"/>
</form>
FORM;

	if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		global $jitPost, $access;

		$quantity = $jitPost->quantity->int();

		if( $jitPost->code->text() == $params['code'] && $quantity > 0 ) {
			global $cartlib; require_once 'lib/payment/cartlib.php';

			$cartlib->add_product( $params['code'], $quantity, array(
				'description' => $params['description'],
				'price' => $price,
				'href' => $params['href'],
			) );

			global $access;
			$access->redirect( $_SERVER['REQUEST_URI'], tr('%0 (%1) was added to your cart', $params['description'], $quantity ) );
		}
	}
	
	return $form;
}

