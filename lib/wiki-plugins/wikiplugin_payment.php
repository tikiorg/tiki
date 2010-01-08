<?php

function wikiplugin_payment_info() {
	return array(
		'name' => tra('Payment'),
		'description' => tra('Display a payment request\'s details based on the user\'s privileges and the state of the payment. The payment details may include the payment options.'),
		'prefs' => array( 'wikiplugin_payment', 'payment_feature' ),
		'params' => array(
			'id' => array(
				'required' => true,
				'name' => tra('Payment request number'),
				'description' => tra('Unique identifier'),
				'filter' => 'digits',
			),
		),
	);
}

function wikiplugin_payment( $data, $params ) {
	global $smarty;

	require_once 'lib/smarty_tiki/function.payment.php';
	return '^~np~' . smarty_function_payment( $params, $smarty ) . '~/np~^';
}

