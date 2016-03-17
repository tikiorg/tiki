<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

interface ShippingProvider
{
	function getRates( array $from, array $to, array $packages );
}

abstract class CustomShippingProvider implements ShippingProvider
{
	abstract function getName();
}

class ShippingLib
{
	private $providers = array();
	private $formats = array(
		'/^[A-Z][0-9][A-Z]\s?[0-9][A-Z][0-9]$/' => 'CA',
		'/^[0-9]{5}$/' => 'US',
	);

	function addProvider( ShippingProvider $provider )
	{
		$this->providers[] = $provider;
	}

	function getRates(array $from, array $to, array $packages)
	{
		$rates = array();

		$from = $this->completeAddressInformation($from);
		$to = $this->completeAddressInformation($to);

		$packages = $this->expandPackages($packages);

		foreach ( $this->providers as $provider ) {
			$rates = array_merge($rates, $provider->getRates($from, $to, $packages));
		}

		return $rates;
	}

	private function completeAddressInformation($address)
	{
		if ( isset($address['zip']) ) {
			$address['zip'] = strtoupper($address['zip']);
		}

		if ( ! isset( $address['country'] ) ) {
			foreach ( $this->formats as $pattern => $country ) {
				if ( preg_match($pattern, $address['zip']) ) {
					$address['country'] = $country;
					break;
				}
			}
		}

		return $address;
	}

	private function expandPackages($packages)
	{
		$out = array();

		foreach ( $packages as $package ) {
			if ( isset( $package['count'] ) ) {
				$c = $package['count'];
				unset($package['count']);
			} else {
				$c = 1;
			}

			for ( $i = 0; $c > $i; ++$i ) {
				$out[] = $package;
			}
		}

		return $out;
	}

	static function getCustomShippingProvider($name)
	{

		$file = dirname(__FILE__) . '/custom/' . $name . '.php';
		$className = 'CustomShippingProvider_' . ucfirst($name);
		if (is_readable($file)) {
			require_once $file;
			if (class_exists($className) && method_exists($className, 'getName')) {
				$provider = new $className;
				return $provider;
			}
		}
		TikiLib::lib('errorreport')->report(tr('Problem reading custom shipping provider "%0"', $name));
	}

}

global $shippinglib, $prefs;
$shippinglib = new ShippingLib;

if ( !empty($prefs['shipping_fedex_enable']) && $prefs['shipping_fedex_enable'] === 'y' ) {
	require_once 'lib/shipping/provider_fedex.php';
	$shippinglib->addProvider(
		new ShippingProvider_FedEx(
			array(
				'key' => $prefs['shipping_fedex_key'],
				'password' => $prefs['shipping_fedex_password'],
				'meter' => $prefs['shipping_fedex_meter'],
			)
		)
	);
}

if ( !empty($prefs['shipping_ups_enable']) && $prefs['shipping_ups_enable'] === 'y' ) {
	require_once 'lib/shipping/provider_ups.php';
	$shippinglib->addProvider(
		new ShippingProvider_Ups(
			array(
				'username' => $prefs['shipping_ups_username'],
				'password' => $prefs['shipping_ups_password'],
				'license' => $prefs['shipping_ups_license'],
			)
		)
	);
}

if ( !empty($prefs['shipping_custom_provider']) ) {
	$shippinglib->addProvider(ShippingLib::getCustomShippingProvider($prefs['shipping_custom_provider']));
}
