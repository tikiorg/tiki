<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
			'description' => tra('Currently a choice between PayPal, and Cclite (in development), or Tiki User Credits.'),
			'hint' => tra('PayPal: see PayPal.com - Cclite: Community currency'),
			'type' => 'list',
			'options' => array(
				'paypal' => tra('PayPal'),
				'cclite' => tra('Cclite'),
				'tikicredits' => tra('Tiki User Credits'),
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
		'payment_cclite_registries' => array(
			'name' => tra('Cclite Registries'),
			'description' => tra('Registries in Cclite. Use comma to separate. e.g. "dogtown, chelsea, dalston, etc"'),
			'hint' => tra('Registry names in Cclite'),
			'type' => 'text',
			'dependencies' => array( 'payment_feature' ),
			'size' => 40,
			'separator' => ',',
		),
		'payment_cclite_currencies' => array(
			'name' => tra('Cclite Registry Currencies'),
			'description' => tra('Currencies in Cclite. Use comma to separate. e.g. "woof, ducket, ducket, etc"'),
			'hint' => tra('Each registry in Cclite can have it\'s own currency. Must be one per registry. (case sensitive)'),
			'type' => 'text',
			'dependencies' => array( 'payment_feature' ),
			'size' => 40,
			'separator' => ',',
		),
		'payment_cclite_gateway' => array(
			'name' => tra('Cclite Server URL'),
			'description' => tra('Full URL of the repository.'),
			'shorthint' => tra('e.g. https://cclite.yourdomain.org/cclite/'),
			'type' => 'text',
			'size' => 60,
		'dependencies' => array( 'payment_cclite_registries' ),
		),
		'payment_cclite_merchant_key' => array(
			'name' => tra('Cclite Merchant Key'),
			'description' => tra('Corresponds with Merchant Key setting in Cclite'),
			'type' => 'text',
			'dependencies' => array( 'payment_cclite_registries' ),
		),
		'payment_cclite_merchant_user' => array(
			'name' => tra('Cclite Merchant User'),
			'description' => tra('User name in Cclite representing "the management". Defaults to "manager"'),
			'type' => 'text',
			'dependencies' => array( 'payment_cclite_registries' ),
		),
		'payment_cclite_mode' => array(
			'name' => tra('Cclite Enable Payments'),
			'description' => tra('Test or Live operation'),
			'type' => 'list',
			'options' => array(
				'live' => tra('Live'),
				'test' => tra('Test'),
			),
			'dependencies' => array( 'payment_cclite_registries' ),
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
			'dependencies' => array( 'payment_cclite_registries' ),
		),
		'payment_cclite_notify' => array(
			'name' => tra('Cclite Payment Notification'),
			'description' => tra('TODO'),
			'type' => 'flag',
			'dependencies' => array( 'payment_cclite_registries' ),
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
		'payment_tikicredits_types' => array(
			'name' => tra('Types of credit to use'),
			'description' => tra('This is a list of the types of Tiki user credits to accept to pay with, separated by ,'),
			'type' => 'text',
			'dependencies' => array( 'payment_feature', 'feature_credits' ),
			'separator' => ',',
		),
		'payment_tikicredits_xcrates' => array(
			'name' => tra('Exchange rate for types of credit to use'),
			'description' => tra('This is a corresponding list of amount of credits equivalent to 1 of the payment currency, separated by ,'),
			'type' => 'text',
			'dependencies' => array( 'payment_feature', 'feature_credits' ),
			'separator' => ',',
		),
		'payment_user_only_his_own' => array(
			'name' => tra('User can only see his own outstanding payments'),
			'description' => tra('Unless with administer payment permissions, a user can only see his own outstanding payments'),
			'type' => 'flag', 
		),
		'payment_user_only_his_own_past' => array(
			'name' => tra('User can only see his own past or cancelled payments'),
			'description' => tra('Unless with administer payment permissions, a user can only see his own past or cancelled payments'),
			'type' => 'flag',
		),
		'payment_cart_inventory' => array(
			'name' => tra('Manage product inventory'),
			'description' => tra('Activate product inventory feature, needs Products tracker to be set up properly, the itemId must be the product code'),
			'type' => 'flag',
			'dependencies' => array( 'payment_cart_product_tracker', 'payment_cart_inventory_type_field', 'payment_cart_inventory_total_field', 'payment_cart_inventory_lesshold_field' ),
		),
		'payment_cart_product_tracker' => array(
			'name' => tra('Products Tracker ID'),
			'description' => tra('Tracker ID of tracker that is the Products tracker, needed for advanced cart features, the itemId will be the product code'),
			'type' => 'text',
			'filter' => 'digits',
			'size' => 3,
		),
		'payment_cart_product_tracker_name' => array(
			'name' => tra('Products Tracker Name'),
			'description' => tra('Name of tracker that is the Products tracker, needed for advanced cart features, the itemId will be the product code'),
			'type' => 'text',
		),
		'payment_cart_inventory_type_field' => array(
			'name' => tra('Inventory Type Field ID'),
			'description' => tra('Field ID in Products tracker to store the inventory type, the value of the field must be "none" or "internal"'),
			'type' => 'text',
			'filter' => 'digits',
			'size' => 3,
		),
		'payment_cart_inventory_total_field' => array(
			'name' => tra('Inventory Total Field ID'),
			'description' => tra('Field ID in Products tracker to store the total inventory of product'),
			'type' => 'text',
			'filter' => 'digits',
			'size' => 3,
		),
		'payment_cart_inventory_lesshold_field' => array(
			'name' => tra('Inventory Total Less Hold Field ID'),
			'description' => tra('Field ID in Products tracker to store the total inventory of product less the amount on hold because they are currently in carts'),
			'type' => 'text',
			'filter' => 'digits',
			'size' => 3,		
		),
		'payment_cart_bundles' => array(
			'name' => tra('Bundled products feature for cart'),
			'description' => tra('Activate bundled products feature, needs Products tracker to be set up properly, the itemId must be the product code'),
			'type' => 'flag',
			'dependencies' => array( 'payment_cart_product_name_fieldname', 'payment_cart_products_inbundle_fieldname' ),
		),
		'payment_cart_product_name_fieldname' => array(
			'name' => tra('Product Name Field Name'),
			'description' => tra('Field Name in Products tracker of the product name that will be used as the label of the product, e.g. in a bundle.'),
			'type' => 'text',
		),
		'payment_cart_products_inbundle_fieldname' => array(
			'name' => tra('Items in Bundle Field Name'),
			'description' => tra('Field Name in Products tracker of a comma separated list of product IDs of products in the bundle (i.e. if the field contains anything, then this product is a bundle), you can also specify the number of the sub-products, e.g. 23:("colon")2,24 means item 23 (x2) + item 24 (x1)'),
			'type' => 'text',
		),
		'payment_cart_associated_event_fieldname' => array(
			'name' => tra('Associated Event ID Field Name'),
			'description' => tra('Field Name in Products tracker of the Associated Event ID, needed for the Associated Events cart feature, requires an Events tracker to be setup where the item ID there is the event ID to associate to'),
			'type' => 'text',
		),
		'payment_cart_product_classid_fieldname' => array(
			'name' => tra('Product Class ID Field Name'),
			'description' => tra('Field Name in Products tracker of the Product Class ID, needed for the Gift Certificates cart feature.'),
			'type' => 'text',		
		),
		'payment_cart_orders' => array(
			'name' => tra('Record cart orders in trackers'),
			'description' => tra('This feature requires 2 trackers, and Orders tracker and an Orders Item tracker to be configured. It also needs profiles to be configured to do the recording.'),
			'type' => 'flag',
			'dependencies' => array( 'payment_cart_orders_profile', 'payment_cart_orderitems_profile' ),
		),
		'payment_cart_orders_profile' => array(
			'name' => tra('Orders Profile'),
			'description' => tra('Wiki page where the profile for creating orders is (page name must be without spaces)'),
			'help' => 'Order Profile',
			'type' => 'text',
		),
		'payment_cart_orderitems_profile' => array(
			'name' => tra('Order Item Profile'),
			'description' => tra('Wiki page where the profile for creating orders items is (page name must be without spaces)'),
			'help' => 'Order Item Profile',
			'type' => 'text',
		),
	);
}

