<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_payment_list() {
	return array(
		'payment_feature' => array(
			'name' => tra('Payment'),
			'description' => tra('Feature to manage and track payment requests.'),
			'type' => 'flag',
			'help' => 'Payment',
		),
		'payment_paypal_business' => array(
			'name' => tra('Paypal Business ID'),
			'description' => tra('Enable payments through paypal.'),
			'hint' => tra('Email address'),
			'type' => 'text',
			'filter' => 'email',
			'dependencies' => array( 'payment_feature' ),
			'size' => 50,
		),
		'payment_paypal_environment' => array(
			'name' => tra('Paypal Environment'),
			'description' => tra('Used to switch between the paypal sandbox, used for testing and development and the live environment.'),
			'type' => 'list',
			'options' => array(
				'https://www.paypal.com/cgi-bin/webscr' => tra('Production'),
				'https://www.sandbox.paypal.com/cgi-bin/webscr' => tra('Sandbox'),
			),
			'dependencies' => array( 'payment_feature' ),
		),
		'payment_paypal_ipn' => array(
			'name' => tra('Paypal Instant Payment Notification (IPN)'),
			'description' => tra('Enable IPN for automatic payment completion. When enabled, Paypal will ping back the site when a payment is confirmed. The payment will then be entered automatically. This may not be possible if the server is not on a public server.'),
			'type' => 'flag',
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
			'shorthint' => tra('days'),
			'description' => tra('Amount of days before the payment requests becomes overdue. This can be changed per payment request.'),
			'type' => 'text',
			'filter' => 'digits',
			'size' => 3,
		),
	);
}

