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
class KalturaTag extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $tag = null;

	/**
	 * 
	 *
	 * @var KalturaTaggedObjectType
	 * @readonly
	 */
	public $taggedObjectType = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $instanceCount = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaIndexTagsByPrivacyContextJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $changedCategoryId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $deletedPrivacyContexts = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $addedPrivacyContexts = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaTagFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var KalturaTaggedObjectType
	 */
	public $objectTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagStartsWith = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $instanceCountEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $instanceCountIn = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaTagListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaTag
	 * @readonly
	 */
	public $objects;


}


/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaTagService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * 
	 * 
	 * @param KalturaTagFilter $tagFilter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaTagListResponse
	 */
	function search(KalturaTagFilter $tagFilter, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "tagFilter", $tagFilter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("tagsearch_tag", "search", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaTagListResponse");
		return $resultObject;
	}

	/**
	 * Action goes over all tags with instanceCount==0 and checks whether they need to be removed from the DB. Returns number of removed tags.
	 * 
	 * @return int
	 */
	function deletePending()
	{
		$kparams = array();
		$this->client->queueServiceActionCall("tagsearch_tag", "deletePending", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "integer");
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param int $categoryId 
	 * @param string $pcToDecrement 
	 * @param string $pcToIncrement 
	 */
	function indexCategoryEntryTags($categoryId, $pcToDecrement, $pcToIncrement)
	{
		$kparams = array();
		$this->client->addParam($kparams, "categoryId", $categoryId);
		$this->client->addParam($kparams, "pcToDecrement", $pcToDecrement);
		$this->client->addParam($kparams, "pcToIncrement", $pcToIncrement);
		$this->client->queueServiceActionCall("tagsearch_tag", "indexCategoryEntryTags", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
	}
}
/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaTagSearchClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaTagService
	 */
	public $tag = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->tag = new KalturaTagService($client);
	}

	/**
	 * @return KalturaTagSearchClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaTagSearchClientPlugin($client);
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'tag' => $this->tag,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'tagSearch';
	}
}

