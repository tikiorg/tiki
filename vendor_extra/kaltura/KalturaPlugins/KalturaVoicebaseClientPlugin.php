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
class KalturaVoicebaseJobProviderData extends KalturaIntegrationJobProviderData
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
	 * input Transcript-asset ID
	 * 	 
	 *
	 * @var string
	 */
	public $transcriptId = null;

	/**
	 * Caption formats
	 * 	 
	 *
	 * @var string
	 */
	public $captionAssetFormats = null;

	/**
	 * Api key for service provider
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $apiKey = null;

	/**
	 * Api key for service provider
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $apiPassword = null;

	/**
	 * Transcript content language
	 * 	 
	 *
	 * @var KalturaLanguage
	 */
	public $spokenLanguage = null;

	/**
	 * Transcript Content location
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $fileLocation = null;

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
class KalturaVoicebaseClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaVoicebaseClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaVoicebaseClientPlugin($client);
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
		return 'voicebase';
	}
}

