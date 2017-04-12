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
class KalturaCodeCuePointOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const DURATION_ASC = "+duration";
	const END_TIME_ASC = "+endTime";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const START_TIME_ASC = "+startTime";
	const TRIGGERED_AT_ASC = "+triggeredAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const DURATION_DESC = "-duration";
	const END_TIME_DESC = "-endTime";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const START_TIME_DESC = "-startTime";
	const TRIGGERED_AT_DESC = "-triggeredAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCodeCuePoint extends KalturaCuePoint
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $code = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $endTime = null;

	/**
	 * Duration in milliseconds
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $duration = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaCodeCuePointBaseFilter extends KalturaCuePointFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $codeLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $codeMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $codeMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $codeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $codeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $descriptionLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $descriptionMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $descriptionMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $endTimeGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $endTimeLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $durationGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $durationLessThanOrEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCodeCuePointFilter extends KalturaCodeCuePointBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCodeCuePointClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaCodeCuePointClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaCodeCuePointClientPlugin($client);
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
		return 'codeCuePoint';
	}
}

