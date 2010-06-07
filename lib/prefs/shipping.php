<?php

function prefs_shipping_list() {
	return array(
		'shipping_service' => array(
			'name' => tra('Shipping Service'),
			'description' => tra('Expose a JSON shipping rate estimation service. Accounts from providers may be required (FedEx, UPS, ...).'),
			'type' => 'flag',
			'help' => 'Shipping',
		),
		'shipping_fedex_enable' => array(
			'name' => tra('FedEx API'),
			'description' => tra('Enable shipping rate calculation through FedEx APIs'),
			'type' => 'flag',
			'help' => 'Shipping',
		),
		'shipping_fedex_key' => array(
			'name' => tra('FedEx Key'),
			'description' => tra('Developer Key'),
			'type' => 'text',
			'size' => 16,
			'filter' => 'alnum',
		),
		'shipping_fedex_password' => array(
			'name' => tra('FedEx Password'),
			'type' => 'text',
			'size' => 25,
			'filter' => 'htmlraw_unsafe',
		),
		'shipping_fedex_meter' => array(
			'name' => tra('FedEx Meter Number'),
			'type' => 'text',
			'size' => 10,
			'filter' => 'digits',
		),
		'shipping_fedex_account' => array(
			'name' => tra('FedEx Account Number'),
			'type' => 'text',
			'size' => 10,
			'filter' => 'digits',
		),
	);
}

