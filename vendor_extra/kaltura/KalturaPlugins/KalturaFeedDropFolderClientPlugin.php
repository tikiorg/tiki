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
require_once(dirname(__FILE__) . "/KalturaDropFolderClientPlugin.php");

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFeedItemInfo extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $itemXPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $itemPublishDateXPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $itemUniqueIdentifierXPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $itemContentFileSizeXPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $itemContentUrlXPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $itemContentBitrateXPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $itemHashXPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $itemContentXpath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $contentBitrateAttributeName = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFeedDropFolder extends KalturaDropFolder
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $itemHandlingLimit = null;

	/**
	 * 
	 *
	 * @var KalturaFeedItemInfo
	 */
	public $feedItemInfo;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFeedDropFolderFile extends KalturaDropFolderFile
{
	/**
	 * MD5 or Sha1 encrypted string
	 * 	 
	 *
	 * @var string
	 */
	public $hash = null;

	/**
	 * Path of the original Feed content XML
	 * 	 
	 *
	 * @var string
	 */
	public $feedXmlPath = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFeedDropFolderClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaFeedDropFolderClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaFeedDropFolderClientPlugin($client);
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
		return 'FeedDropFolder';
	}
}

