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
		'payment_system' => array(
			'name' => tra('Payment System'),
			'description' => tra('Currently a choice between PayPal, and Cclite (in development).'),
			'hint' => tra('PayPal: see PayPal.com - Cclite: Community currency'),
			'type' => 'list',
			'options' => array(
				'paypal' => tra('PayPal'),
				'cclite' => tra('Cclite'),
			),
			'dependencies' => array( 'payment_feature' ),
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
			'dependencies' => array( 'payment_paypal_business' ),
		),
		'payment_paypal_ipn' => array(
			'name' => tra('Paypal Instant Payment Notification (IPN)'),
			'description' => tra('Enable IPN for automatic payment completion. When enabled, Paypal will ping back the site when a payment is confirmed. The payment will then be entered automatically. This may not be possible if the server is not on a public server.'),
			'type' => 'flag',
			'dependencies' => array( 'payment_paypal_business' ),
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
		'payment_cclite_registry' => array(
			'name' => tra('Cclite Registry'),
			'description' => tra('Enable payments through Cclite.'),
			'hint' => tra('Registry name in Cclite'),
			'type' => 'text',
			'dependencies' => array( 'payment_feature' ),
			'size' => 10,
		),
		'payment_cclite_gateway' => array(
			'name' => tra('Cclite Server URL'),
			'description' => tra('Full URL of the repository.'),
			'shorthint' => tra('e.g. https://cclite.yourdomain.org/cclite/'),
			'type' => 'text',
			'dependencies' => array( 'payment_cclite_registry' ),
		),
		'payment_cclite_merchant_key' => array(
			'name' => tra('Cclite Merchant Key'),
			'description' => tra('Corresponds with Merchant Key setting in Cclite'),
			'type' => 'text',
			'dependencies' => array( 'payment_cclite_registry' ),
		),
		'payment_cclite_merchant_user' => array(
			'name' => tra('Cclite Merchant User'),
			'description' => tra('User name in Cclite representing "the management". Defaults to "manager"'),
			'type' => 'text',
			'dependencies' => array( 'payment_cclite_registry' ),
		),
		'payment_cclite_mode' => array(
			'name' => tra('Cclite Enable Payments'),
			'description' => tra('Test or Live operation'),
			'type' => 'list',
			'options' => array(
				'live' => tra('Live'),
				'test' => tra('Test'),
			),
			'dependencies' => array( 'payment_cclite_registry' ),
		),
		'payment_cclite_hashing_algorithm' => array(
			'name' => tra('Hashing Algorithm'),
			'description' => tra('Encryption type'),
			'type' => 'list',
			'options' => array(
				'sha1' => tra('SHA1'),
				'sha256' => tra('SHA256'),
				'sha512' => tra('SHA512'),
			),
			'dependencies' => array( 'payment_cclite_registry' ),
		),
		'payment_cclite_notify' => array(
			'name' => tra('Cclite Payment Notification'),
			'description' => tra('TODO'),
			'type' => 'flag',
			'dependencies' => array( 'payment_cclite_registry' ),
		),
		'payment_manual' => array(
			'name' => tra('Wiki page containing the instruction to send manual payment like check'),
			'description' => tra('Wiki page'),
			'type' => 'text',
			'dependencies' => array( 'payment_feature' ),
			'default' => '',
		),
		'payment_invoice_prefix' => array(
			'name' => tra('Prefix to the invoice'),
			'description' => tra('Prefix must be set and unique if the same paypal account is used for different tiki sites as paypal checks that the invoice is not paid twice'),
			'type' => 'text',
			'dependencies' => array( 'payment_feature' ),
			'default' => '',
		),
	);
}

