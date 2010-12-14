<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class ShippingProvider_Fedex implements ShippingProvider
{
	private $key;
	private $password;
	private $meter;

	function __construct( array $config ) {
		$this->key = $config['key'];
		$this->password = $config['password'];
		$this->meter = $config['meter'];
	}

	function getRates( array $from, array $to, array $packages ) {
		if( ! class_exists( 'SoapClient' ) ) {
			return array();
		}

		$wsdl = dirname(__FILE__) . '/FedEx_v8.wsdl';
		$args = array();

		$request = $this->getRequest( $from, $to, $packages );

		try {
			$client = new SoapClient( $wsdl, $args );
			$response = $client->getRates( $request );

			$options = $response->RateReplyDetails;
			$out = $this->extractRates( $options );

			return $out;
		} catch( SoapFault $e ) {
			return array();
		}
	}

	private function extractRates( $options ) {
		$out = array();

		foreach( $options as $option ) {
			if( $detail = reset( $option->RatedShipmentDetails ) ) {
				$charge = $detail->ShipmentRateDetail->TotalNetCharge;
				$out[] = array(
					'provider' => 'FedEx',
					'service' => $option->ServiceType,
					'readable' => tra( $option->ServiceType ),
					'cost' => number_format( $charge->Amount, 2, '.', '' ),
					'currency' => $charge->Currency,
				);
			}
		}

		return $out;
	}

	private function getRequest( $from, $to, $packages ) {
		$request = array(
			'WebAuthenticationDetail' => array(
				'UserCredential' => array(
					'Key' => $this->key,
					'Password' => $this->password,
				),
			),
			'ClientDetail' => array(
				'AccountNumber' => $this->account,
				'MeterNumber' => $this->meter,
			),
			'Version' => array(
				'ServiceId' => 'crs',
				'Major' => '8',
				'Intermediate' => '0',
				'Minor' => '0',
			),
			'RequestedShipment' => array(
				'PackagingType' => 'YOUR_PACKAGING',
				'Shipper' => $this->buildAddress( $from ),
				'Recipient' => $this->buildAddress( $to ),
				'RateRequestTypes' => 'LIST',
				'PackageDetail' => 'INDIVIDUAL_PACKAGES',
				'RequestedPackageLineItems' => array_map( array( $this, 'buildPackage' ), $packages ),
			),
		);

		return $request;
	}

	private function buildAddress( $address ) {
		return array(
			'Address' => array(
				'PostalCode' => $address['zip'],
				'CountryCode' => $address['country'],
			),
		);
	}

	private function buildPackage( $package ) {
		return array(
			'Weight' => array(
				'Value' => $package['weight'],
				'Units' => 'KG',
			),
		);
	}
}

