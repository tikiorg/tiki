<?php

function prefs_payment_list() {
	return array(
		'payment_feature' => array(
			'name' => tra('Payment'),
			'description' => tra('Feature to manage and track payment requests.'),
			'type' => 'flag',
		),
		'payment_paypal_business' => array(
			'name' => tra('Paypal Business ID'),
			'description' => tra('Enable payments through paypal.'),
			'hint' => tra('Email address'),
			'type' => 'text',
			'filter' => 'email',
			'dependencies' => array( 'payment_feature' ),
		),
		'payment_currency' => array(
			'name' => tra('Currency'),
			'description' => tra('Currency used when entering payments.'),
			'type' => 'text',
			'size' => 3,
			'filter' => 'alpha',
		),
		'payment_default_delay' => array(
			'name' => tra('Default acceptable payment delay'),
			'hint' => tra('in days'),
			'description' => tra('Amount of days before the payment requests becomes overdue. This can be changed per payment request.'),
			'type' => 'text',
			'filter' => 'digits',
		),
	);
}

