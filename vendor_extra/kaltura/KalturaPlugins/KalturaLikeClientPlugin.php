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
class KalturaLikeOrderBy extends KalturaEnumBase
{
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLike extends KalturaObjectBase
{
	/**
	 * The id of the entry that the like belongs to
	 * 	 
	 *
	 * @var string
	 */
	public $entryId = null;

	/**
	 * The id of user that the like belongs to
	 * 	 
	 *
	 * @var string
	 */
	public $userId = null;

	/**
	 * The date of the like's creation
	 * 	 
	 *
	 * @var int
	 */
	public $createdAt = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLikeListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaLike
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaLikeBaseFilter extends KalturaRelatedFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userIdEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLikeFilter extends KalturaLikeBaseFilter
{

}


/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLikeService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * 
	 * 
	 * @param string $entryId 
	 * @return bool
	 */
	function like($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("like_like", "like", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$resultObject = (bool) $resultObject;
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param string $entryId 
	 * @return bool
	 */
	function unlike($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("like_like", "unlike", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$resultObject = (bool) $resultObject;
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param string $entryId 
	 * @param string $userId 
	 * @return bool
	 */
	function checkLikeExists($entryId, $userId = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "userId", $userId);
		$this->client->queueServiceActionCall("like_like", "checkLikeExists", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$resultObject = (bool) $resultObject;
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param KalturaLikeFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaLikeListResponse
	 */
	function listAction(KalturaLikeFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("like_like", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaLikeListResponse");
		return $resultObject;
	}
}
/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLikeClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaLikeService
	 */
	public $like = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->like = new KalturaLikeService($client);
	}

	/**
	 * @return KalturaLikeClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaLikeClientPlugin($client);
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'like' => $this->like,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'like';
	}
}

