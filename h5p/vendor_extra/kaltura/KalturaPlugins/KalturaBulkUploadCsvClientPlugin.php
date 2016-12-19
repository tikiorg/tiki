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
require_once(dirname(__FILE__) . "/KalturaBulkUploadClientPlugin.php");

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadCsvVersion extends KalturaEnumBase
{
	const V1 = 1;
	const V2 = 2;
	const V3 = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadCsvJobData extends KalturaBulkUploadJobData
{
	/**
	 * The version of the csv file
	 * 	 
	 *
	 * @var KalturaBulkUploadCsvVersion
	 * @readonly
	 */
	public $csvVersion = null;

	/**
	 * Array containing CSV headers
	 * 	 
	 *
	 * @var array of KalturaString
	 */
	public $columns;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadCsvClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaBulkUploadCsvClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaBulkUploadCsvClientPlugin($client);
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
		return 'bulkUploadCsv';
	}
}

