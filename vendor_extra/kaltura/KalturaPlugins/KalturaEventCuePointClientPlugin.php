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
require_once(dirname(__FILE__) . "/KalturaCuePointClientPlugin.php");

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEventCuePointOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const START_TIME_ASC = "+startTime";
	const TRIGGERED_AT_ASC = "+triggeredAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const START_TIME_DESC = "-startTime";
	const TRIGGERED_AT_DESC = "-triggeredAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEventType extends KalturaEnumBase
{
	const BROADCAST_START = "1";
	const BROADCAST_END = "2";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEventCuePoint extends KalturaCuePoint
{
	/**
	 * 
	 *
	 * @var KalturaEventType
	 */
	public $eventType = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaEventCuePointBaseFilter extends KalturaCuePointFilter
{
	/**
	 * 
	 *
	 * @var KalturaEventType
	 */
	public $eventTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $eventTypeIn = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEventCuePointFilter extends KalturaEventCuePointBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEventCuePointClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaEventCuePointClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaEventCuePointClientPlugin($client);
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
		return 'eventCuePoint';
	}
}

