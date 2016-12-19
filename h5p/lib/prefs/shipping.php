<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_shipping_list()
{
	require_once 'lib/shipping/shippinglib.php';
	$all = glob('lib/shipping/custom/*.php');
	
	$custom_providers = array( '' => tra('None'));

	foreach ( $all as $file ) {
		if ($file === "lib/shipping/custom/index.php")
			continue;
		$name = basename($file, '.php');
		$provider = ShippingLib::getCustomShippingProvider($name);
		$custom_providers[$name] = $provider->getName();
	}

	return array(
		'shipping_service' => array(
			'name' => tra('Shipping Service'),
			'description' => tra('Expose a JSON shipping rate estimation service. Accounts from providers may be required (FedEx, UPS, ...).'),
			'type' => 'flag',
			'help' => 'Shipping',
			'default' => 'n',
		),
		'shipping_fedex_enable' => array(
			'name' => tra('FedEx API'),
			'description' => tra('Enable shipping rate calculation through FedEx APIs'),
			'type' => 'flag',
			'help' => 'Shipping',
			'default' => 'n',
		),
		'shipping_fedex_key' => array(
			'name' => tra('FedEx Key'),
			'description' => tra('Developer Key'),
			'type' => 'text',
			'size' => 16,
			'filter' => 'alnum',
			'default' => '',
		),
		'shipping_fedex_password' => array(
			'name' => tra('FedEx Password'),
			'type' => 'text',
			'size' => 25,
			'filter' => 'rawhtml_unsafe',
			'default' => '',
		),
		'shipping_fedex_meter' => array(
			'name' => tra('FedEx Meter Number'),
			'type' => 'text',
			'size' => 10,
			'filter' => 'digits',
			'default' => '',
		),
		'shipping_fedex_account' => array(
			'name' => tra('FedEx Account Number'),
			'type' => 'text',
			'size' => 10,
			'filter' => 'digits',
			'default' => '',
		),
		'shipping_ups_enable' => array(
			'name' => tra('UPS API'),
			'description' => tra('Enable shipping rate calculation using the UPS carrier.'),
			'type' => 'flag',
			'help' => 'Shipping',
			'default' => 'n',
		),
		'shipping_ups_username' => array(
			'name' => tra('UPS Username'),
			'description' => tra('UPS credentials'),
			'type' => 'text',
			'size' => 15,
			'default' => '',
		),
		'shipping_ups_password' => array(
			'name' => tra('UPS Password'),
			'description' => tra('UPS credentials'),
			'type' => 'text',
			'size' => 25,
			'default' => '',
		),
		'shipping_ups_license' => array(
			'name' => tra('UPS Access Key'),
			'type' => 'text',
			'size' => 25,
			'default' => '',
		),
		'shipping_custom_provider' => array(
			'name' => tra('Custom Shipping Provider'),
			'type' => 'list',
			'size' => 25,
			'default' => '',
			'options' => $custom_providers,
			'dependencies' => array('shipping_service'),
		),
	);
}

