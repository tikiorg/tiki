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

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaKontikiStorageProfileOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaKontikiStorageProfile extends KalturaStorageProfile
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $serviceToken = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaKontikiStorageDeleteJobData extends KalturaStorageDeleteJobData
{
	/**
	 * Unique Kontiki MOID for the content uploaded to Kontiki
	 *      
	 *
	 * @var string
	 */
	public $contentMoid = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $serviceToken = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaKontikiStorageExportJobData extends KalturaStorageExportJobData
{
	/**
	 * Holds the id of the exported asset
	 * 	 
	 *
	 * @var string
	 */
	public $flavorAssetId = null;

	/**
	 * Unique Kontiki MOID for the content uploaded to Kontiki
	 * 	 
	 *
	 * @var string
	 */
	public $contentMoid = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $serviceToken = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaKontikiStorageProfileBaseFilter extends KalturaStorageProfileFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaKontikiStorageProfileFilter extends KalturaKontikiStorageProfileBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaKontikiClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaKontikiClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaKontikiClientPlugin($client);
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
		return 'kontiki';
	}
}

