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
require_once(dirname(__FILE__) . "/KalturaCaptionClientPlugin.php");

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCaptionAssetItem extends KalturaObjectBase
{
	/**
	 * The Caption Asset object
	 * 	 
	 *
	 * @var KalturaCaptionAsset
	 */
	public $asset;

	/**
	 * The entry object
	 * 	 
	 *
	 * @var KalturaBaseEntry
	 */
	public $entry;

	/**
	 * 
	 *
	 * @var int
	 */
	public $startTime = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $endTime = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $content = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCaptionAssetItemListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaCaptionAssetItem
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryCaptionAssetSearchItem extends KalturaSearchItem
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $contentLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $contentMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $contentMultiLikeAnd = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCaptionAssetItemFilter extends KalturaCaptionAssetFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $contentLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $contentMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $contentMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerDescriptionLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerDescriptionMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerDescriptionMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var KalturaLanguage
	 */
	public $languageEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $languageIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $labelEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $labelIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $startTimeGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $startTimeLessThanOrEqual = null;

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


}


/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCaptionAssetItemService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Search caption asset items by filter, pager and free text
	 * 
	 * @param KalturaBaseEntryFilter $entryFilter 
	 * @param KalturaCaptionAssetItemFilter $captionAssetItemFilter 
	 * @param KalturaFilterPager $captionAssetItemPager 
	 * @return KalturaCaptionAssetItemListResponse
	 */
	function search(KalturaBaseEntryFilter $entryFilter = null, KalturaCaptionAssetItemFilter $captionAssetItemFilter = null, KalturaFilterPager $captionAssetItemPager = null)
	{
		$kparams = array();
		if ($entryFilter !== null)
			$this->client->addParam($kparams, "entryFilter", $entryFilter->toParams());
		if ($captionAssetItemFilter !== null)
			$this->client->addParam($kparams, "captionAssetItemFilter", $captionAssetItemFilter->toParams());
		if ($captionAssetItemPager !== null)
			$this->client->addParam($kparams, "captionAssetItemPager", $captionAssetItemPager->toParams());
		$this->client->queueServiceActionCall("captionsearch_captionassetitem", "search", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaCaptionAssetItemListResponse");
		return $resultObject;
	}

	/**
	 * Search caption asset items by filter, pager and free text
	 * 
	 * @param KalturaBaseEntryFilter $entryFilter 
	 * @param KalturaCaptionAssetItemFilter $captionAssetItemFilter 
	 * @param KalturaFilterPager $captionAssetItemPager 
	 * @return KalturaBaseEntryListResponse
	 */
	function searchEntries(KalturaBaseEntryFilter $entryFilter = null, KalturaCaptionAssetItemFilter $captionAssetItemFilter = null, KalturaFilterPager $captionAssetItemPager = null)
	{
		$kparams = array();
		if ($entryFilter !== null)
			$this->client->addParam($kparams, "entryFilter", $entryFilter->toParams());
		if ($captionAssetItemFilter !== null)
			$this->client->addParam($kparams, "captionAssetItemFilter", $captionAssetItemFilter->toParams());
		if ($captionAssetItemPager !== null)
			$this->client->addParam($kparams, "captionAssetItemPager", $captionAssetItemPager->toParams());
		$this->client->queueServiceActionCall("captionsearch_captionassetitem", "searchEntries", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBaseEntryListResponse");
		return $resultObject;
	}
}
/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCaptionSearchClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaCaptionAssetItemService
	 */
	public $captionAssetItem = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->captionAssetItem = new KalturaCaptionAssetItemService($client);
	}

	/**
	 * @return KalturaCaptionSearchClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaCaptionSearchClientPlugin($client);
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'captionAssetItem' => $this->captionAssetItem,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'captionSearch';
	}
}

