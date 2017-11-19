<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/* Simple shipping calculator example based on number of items in cart
 *
 * Needs to declare getName for the pref list, and getRates
 *
 * Declare the options you require in the constructor
 * and the logic to caluculate the price etc (in this case) in getRate
 */

require_once 'lib/shipping/shippinglib.php';

class CustomShippingProvider_Example extends CustomShippingProvider
{
	private $services;

	function __construct()	// needs to be without params
	{
		$this->services = [
			'standard' => [
				'name' => tra('Standard Shipping'),
				'description' => tra('3 to 5 working days'),
				'zones' => [
					'zone 1' => [	// $5 per package, more than 6 packages go free
						'cost_per_item' => 9,
						'max_total' => 45,
					],
					'zone 2' => [	// $20 per package, max 3
						'cost_per_item' => 20,
						'max_total' => 60,
					],
				],
			],
			'express' => [
				'name' => tra('Express Shipping'),
				'description' => tra('Next day delivery'),
				'zones' => [
					'zone 1' => [	// $20 per package, max 3
						'cost_per_item' => 20,
						'max_total' => 60,
					],
					'zone 2' => [	// $30 per package, max 3
						'cost_per_item' => 30,
						'max_total' => 90,
					],
				],
			],
		];
	}

	function getName()
	{
		return tra('Custom Shipping Example');
	}

	function getCurrency()
	{
		return 'USD';
	}

	function getRates(array $from, array $to, array $packages)
	{
		if (! empty($to) && ! empty($packages)) {
			$rates = [];

			foreach ($this->services as $service => $info) {
				$rates[] = $this->getRate($info, $from, $to, $packages);
			}

			return $rates;
		} else {
			return [];
		}
	}

	private function getRate($service, array $from, array $to, array $packages)
	{
		$ret = [
			'provider' => $this->getName(),
			'currency' => $this->getCurrency(),
			'service' => $service['name'],
			'readable' => $service['description'],
		];

		$itemCount = 0;
		foreach ($packages as $item) {
			if (! empty($item['count'])) {
				$itemCount += (int) $item['count'];
			} else {
				$itemCount++;
			}
		}

		if (in_array(strtoupper($to['country']), [ 'AR', 'BO', 'BR', 'CL', 'CO', 'EC', 'FK', 'GF', 'GY', 'PY', 'PE', 'GS', 'SR', 'UY', 'VE' ])) {
			$zone = $service['zones']['zone 2'];	// zone 2 is South America
		} else {
			$zone = $service['zones']['zone 1'];
		}

		$ret['cost'] = min($itemCount * $zone['cost_per_item'], $zone['max_total']);

		return $ret;
	}
}
