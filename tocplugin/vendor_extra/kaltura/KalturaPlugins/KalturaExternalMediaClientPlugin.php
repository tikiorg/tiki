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
class KalturaExternalMediaEntryOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const DURATION_ASC = "+duration";
	const END_DATE_ASC = "+endDate";
	const LAST_PLAYED_AT_ASC = "+lastPlayedAt";
	const MEDIA_TYPE_ASC = "+mediaType";
	const MODERATION_COUNT_ASC = "+moderationCount";
	const NAME_ASC = "+name";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const PLAYS_ASC = "+plays";
	const RANK_ASC = "+rank";
	const RECENT_ASC = "+recent";
	const START_DATE_ASC = "+startDate";
	const TOTAL_RANK_ASC = "+totalRank";
	const UPDATED_AT_ASC = "+updatedAt";
	const VIEWS_ASC = "+views";
	const WEIGHT_ASC = "+weight";
	const CREATED_AT_DESC = "-createdAt";
	const DURATION_DESC = "-duration";
	const END_DATE_DESC = "-endDate";
	const LAST_PLAYED_AT_DESC = "-lastPlayedAt";
	const MEDIA_TYPE_DESC = "-mediaType";
	const MODERATION_COUNT_DESC = "-moderationCount";
	const NAME_DESC = "-name";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const PLAYS_DESC = "-plays";
	const RANK_DESC = "-rank";
	const RECENT_DESC = "-recent";
	const START_DATE_DESC = "-startDate";
	const TOTAL_RANK_DESC = "-totalRank";
	const UPDATED_AT_DESC = "-updatedAt";
	const VIEWS_DESC = "-views";
	const WEIGHT_DESC = "-weight";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaExternalMediaSourceType extends KalturaEnumBase
{
	const INTERCALL = "InterCall";
	const YOUTUBE = "YouTube";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaExternalMediaEntry extends KalturaMediaEntry
{
	/**
	 * The source type of the external media
	 * 	 
	 *
	 * @var KalturaExternalMediaSourceType
	 * @insertonly
	 */
	public $externalSourceType = null;

	/**
	 * Comma separated asset params ids that exists for this external media entry
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $assetParamsIds = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaExternalMediaEntryListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaExternalMediaEntry
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaExternalMediaEntryBaseFilter extends KalturaMediaEntryFilter
{
	/**
	 * 
	 *
	 * @var KalturaExternalMediaSourceType
	 */
	public $externalSourceTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $externalSourceTypeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $assetParamsIdsMatchOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $assetParamsIdsMatchAnd = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaExternalMediaEntryFilter extends KalturaExternalMediaEntryBaseFilter
{

}


/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaExternalMediaService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Add external media entry
	 * 
	 * @param KalturaExternalMediaEntry $entry 
	 * @return KalturaExternalMediaEntry
	 */
	function add(KalturaExternalMediaEntry $entry)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entry", $entry->toParams());
		$this->client->queueServiceActionCall("externalmedia_externalmedia", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaExternalMediaEntry");
		return $resultObject;
	}

	/**
	 * Get external media entry by ID.
	 * 
	 * @param string $id External media entry id
	 * @return KalturaExternalMediaEntry
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("externalmedia_externalmedia", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaExternalMediaEntry");
		return $resultObject;
	}

	/**
	 * Update external media entry. Only the properties that were set will be updated.
	 * 
	 * @param string $id External media entry id to update
	 * @param KalturaExternalMediaEntry $entry External media entry object to update
	 * @return KalturaExternalMediaEntry
	 */
	function update($id, KalturaExternalMediaEntry $entry)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "entry", $entry->toParams());
		$this->client->queueServiceActionCall("externalmedia_externalmedia", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaExternalMediaEntry");
		return $resultObject;
	}

	/**
	 * Delete a external media entry.
	 * 
	 * @param string $id External media entry id to delete
	 */
	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("externalmedia_externalmedia", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
	}

	/**
	 * List media entries by filter with paging support.
	 * 
	 * @param KalturaExternalMediaEntryFilter $filter External media entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @return KalturaExternalMediaEntryListResponse
	 */
	function listAction(KalturaExternalMediaEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("externalmedia_externalmedia", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaExternalMediaEntryListResponse");
		return $resultObject;
	}

	/**
	 * Count media entries by filter.
	 * 
	 * @param KalturaExternalMediaEntryFilter $filter External media entry filter
	 * @return int
	 */
	function count(KalturaExternalMediaEntryFilter $filter = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("externalmedia_externalmedia", "count", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "integer");
		return $resultObject;
	}
}
/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaExternalMediaClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaExternalMediaService
	 */
	public $externalMedia = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->externalMedia = new KalturaExternalMediaService($client);
	}

	/**
	 * @return KalturaExternalMediaClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaExternalMediaClientPlugin($client);
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'externalMedia' => $this->externalMedia,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'externalMedia';
	}
}

