<?php
// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// This file is part of the Kaltura Collaborative Media Suite which allows users
// to do with audio, video, and animation what Wiki platfroms allow them to do with
// text.
//
// Copyright (C) 2006-2016  Kaltura Inc.
//
// This file has been included in the Tiki distribution with special permission 
// from Kaltura Inc. for the convenience of Tiki users. It is not LGPL licensed. 
// Please obtain your own copy from http://kaltura.org if you need it for any other purpose.
//
// @ignore
// ===================================================================================================

/**
 * @package Kaltura
 * @subpackage Client
 */
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");
require_once(dirname(__FILE__) . "/KalturaIntegrationClientPlugin.php");

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCielo24Fidelity extends KalturaEnumBase
{
	const MECHANICAL = "MECHANICAL";
	const PREMIUM = "PREMIUM";
	const PROFESSIONAL = "PROFESSIONAL";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCielo24Priority extends KalturaEnumBase
{
	const PRIORITY = "PRIORITY";
	const STANDARD = "STANDARD";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCielo24JobProviderData extends KalturaIntegrationJobProviderData
{
	/**
	 * Entry ID
	 * 	 
	 *
	 * @var string
	 */
	public $entryId = null;

	/**
	 * Flavor ID
	 * 	 
	 *
	 * @var string
	 */
	public $flavorAssetId = null;

	/**
	 * Caption formats
	 * 	 
	 *
	 * @var string
	 */
	public $captionAssetFormats = null;

	/**
	 * 
	 *
	 * @var KalturaCielo24Priority
	 */
	public $priority = null;

	/**
	 * 
	 *
	 * @var KalturaCielo24Fidelity
	 */
	public $fidelity = null;

	/**
	 * Api key for service provider
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $username = null;

	/**
	 * Api key for service provider
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $password = null;

	/**
	 * Base url for service provider
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $baseUrl = null;

	/**
	 * Transcript content language
	 * 	 
	 *
	 * @var KalturaLanguage
	 */
	public $spokenLanguage = null;

	/**
	 * should replace remote media content
	 * 	 
	 *
	 * @var bool
	 */
	public $replaceMediaContent = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCielo24ClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaCielo24ClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaCielo24ClientPlugin($client);
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'cielo24';
	}
}

