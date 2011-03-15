<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'lib/shipping/shippinglib.php';

class ShippingProvider_Ups implements ShippingProvider
{
	private $username;
	private $password;
	private $license;

	function __construct( array $config ) {
		$this->username = $config['username'];
		$this->password = $config['password'];
		$this->license = $config['license'];
	}

	function getRates( array $from, array $to, array $packages ) {
		if( $dom = $this->obtain( $from, $to, $packages ) ) {
			$rates = array();

			foreach( $dom->getElementsByTagName( 'RatedShipment' ) as $node ) {
				$rates[] = $this->readShipment( $node );
			}

			return $rates;
		} else {
			return array();
		}
	}

	private function obtain( $from, $to, $packages, $service ) {
		try {
			$auth = $this->getAuth();
			$request = $this->getRequest( $from, $to, $packages, $service );

			require_once 'Zend/Http/Client.php';
			$client = new Zend_Http_Client( 'https://www.ups.com/ups.app/xml/Rate' );
			$client->setRawData( $auth . $request );

			$response = $client->request( 'POST' );
			$body = $response->getBody();

			$dom = new DOMDocument;
			$dom->loadXML( $body );

			return $dom;
		} catch( Zend_Http_Exception $e ) {
			return null;
		}
	}

	private function readShipment( $node ) {
		$xp = new DOMXPath( $node->ownerDocument );
		return array(
			'provider' => 'UPS',
			'service' => 'UPS_CODE_' . $xp->query( 'Service/Code', $node )->item(0)->textContent,
			'readable' => tra( 'UPS_CODE_' . $xp->query( 'Service/Code', $node )->item(0)->textContent ),
			'cost' => $xp->query( 'TotalCharges/MonetaryValue', $node )->item(0)->textContent,
			'currency' => $xp->query( 'TotalCharges/CurrencyCode', $node )->item(0)->textContent,
		);
	}

	private function getAuth() {
		$dom = new DOMDocument( '1.0' );
		$dom->appendChild( $root = $dom->createElement( 'AccessRequest' ) );

		$root->appendChild( $license = $dom->createElement( 'AccessLicenseNumber' ) );
		$root->appendChild( $username = $dom->createElement( 'UserId' ) );
		$root->appendChild( $password = $dom->createElement( 'Password' ) );

		$license->appendChild( $dom->createTextNode( $this->license ) );
		$username->appendChild( $dom->createTextNode( $this->username ) );
		$password->appendChild( $dom->createTextNode( $this->password ) );

		return $dom->saveXML();
	}

	private function getRequest( $from, $to, $packages ) {
		$dom = new DOMDocument( '1.0' );
		$dom->appendChild( $root = $dom->createElement( 'RatingServiceSelectionRequest' ) );
		$root->appendChild( $request = $dom->createElement( 'Request' ) );

		$request->appendChild( $ref = $dom->createElement( 'TransactionReference' ) );
		$ref->appendChild( $dom->createElement( 'CustomerContext', 'Tiki' ) );
		$ref->appendChild( $dom->createElement( 'XpciVersion', '1.0001' ) );
		$request->appendChild( $dom->createElement( 'RequestAction', 'Shop' ) );
		$request->appendChild( $dom->createElement( 'RequestOption', 'Shop' ) );
		$root->appendChild( $pickup = $dom->createElement( 'PickupType' ) );
		$root->appendChild( $shipment = $dom->createElement( 'Shipment' ) );

		$pickup->appendChild( $dom->createElement( 'Code', '01' ) );

		$this->addAddress( $shipment, 'Shipper', $from );
		$this->addAddress( $shipment, 'ShipTo', $to );
		//$this->addAddress( $shipment, 'ShipFrom', $from );

		foreach( $packages as $package ) {
			$this->addPackage( $shipment, $package );
		}

		return $dom->saveXML();
	}

	private function addAddress( $root, $name, $data ) {
		$dom = $root->ownerDocument;

		$root->appendChild( $node = $dom->createElement( $name ) );
		$node->appendChild( $address = $dom->createElement( 'Address' ) );
		$address->appendChild( $zip = $dom->createElement( 'PostalCode' ) );
		$address->appendChild( $country = $dom->createElement( 'CountryCode' ) );

		$zip->appendChild( $dom->createTextNode( $data['zip'] ) );
		$country->appendChild( $dom->createTextNode( $data['country'] ) );
	}

	private function addPackage( $root, $data ) {
		$dom = $root->ownerDocument;

		$root->appendChild( $package = $dom->createElement( 'Package' ) );
		$package->appendChild( $type = $dom->createElement( 'PackagingType' ) );
		$type->appendChild( $dom->createElement( 'Code', '00' ) );
		$package->appendChild( $packageWeight = $dom->createElement( 'PackageWeight' ) );
		$packageWeight->appendChild( $unit = $dom->createElement( 'UnitOfMeasurement' ) );
		$unit->appendChild( $code = $dom->createElement( 'Code', 'KGS' ) );
		$packageWeight->appendChild( $weight = $dom->createElement( 'Weight' ) );

		$weight->appendChild( $dom->createTextNode( $data['weight'] ) );
	}

	/*
	function getRates( array $from, array $to, array $packages ) {
		if( ! class_exists( 'SoapClient' ) ) {
			return array();
		}

		$wsdl = dirname(__FILE__) . '/ups-wsdl/RateWS.wsdl';

		try {
			$client = new SoapClient( $wsdl, array('trace' => 1) );
			$client->__setSoapHeaders( new SoapHeader( 'http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0', 'UPSSecurity', array(
				'UsernameToken' => array(
					'Username' => $this->username,
					'Password' => $this->password,
				),
				'ServiceAccessToken' => array(
					'AccessLicenseNumber' => $this->license,
				),
			) ) );

			$rates = $client->ProcessRate( array(
				'Request' => array(
					'RequestOption' => 'Rate',
				),
				'Shipment' => array(
					'Shipper' => array(
						'Address' => array(
							'CountryCode' => 'CA',
							'PostalCode' => 'H2J 1L8',
						),
					),
					'ShipTo' => array(
						'Address' => array(
							'CountryCode' => 'CA',
							'PostalCode' => 'J7P 1T3',
						),
					),
					'Package' => array(
						array( 'PackagingType' => array( 'Code' => '00' ), 'PackageWeight' => array( 'Weight' => 3, 'UnitOfMeasurement' => array( 'Code' => 'KGS' ) ) ),
					),
				),
			) );

			var_dump($client->__getLastRequest());
			var_dump($client->__getLastResponse());

			var_dump( $rates );
		} catch( SoapFault $e ) {
			var_dump($client->__getLastRequest());
			var_dump($client->__getLastResponse());
			echo $e;
			return array();
		}
	}
	*/
}

