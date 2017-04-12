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
require_once(dirname(__FILE__) . "/KalturaMetadataClientPlugin.php");

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaIntegrationProviderType extends KalturaEnumBase
{
	const CIELO24 = "cielo24.Cielo24";
	const VOICEBASE = "voicebase.Voicebase";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaIntegrationTriggerType extends KalturaEnumBase
{
	const MANUAL = "1";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaIntegrationJobProviderData extends KalturaObjectBase
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaIntegrationJobTriggerData extends KalturaObjectBase
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaIntegrationJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $callbackNotificationUrl = null;

	/**
	 * 
	 *
	 * @var KalturaIntegrationProviderType
	 */
	public $providerType = null;

	/**
	 * Additional data that relevant for the provider only
	 * 	 
	 *
	 * @var KalturaIntegrationJobProviderData
	 */
	public $providerData;

	/**
	 * 
	 *
	 * @var KalturaIntegrationTriggerType
	 */
	public $triggerType = null;

	/**
	 * Additional data that relevant for the trigger only
	 * 	 
	 *
	 * @var KalturaIntegrationJobTriggerData
	 */
	public $triggerData;


}


/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaIntegrationService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Dispatch integration task
	 * 
	 * @param KalturaIntegrationJobData $data 
	 * @param string $objectType 
	 * @param string $objectId 
	 * @return int
	 */
	function dispatch(KalturaIntegrationJobData $data, $objectType, $objectId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "data", $data->toParams());
		$this->client->addParam($kparams, "objectType", $objectType);
		$this->client->addParam($kparams, "objectId", $objectId);
		$this->client->queueServiceActionCall("integration_integration", "dispatch", $kparams);
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
	 * @param int $id Integration job id
	 */
	function notify($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("integration_integration", "notify", $kparams);
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
class KalturaIntegrationClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaIntegrationService
	 */
	public $integration = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->integration = new KalturaIntegrationService($client);
	}

	/**
	 * @return KalturaIntegrationClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaIntegrationClientPlugin($client);
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'integration' => $this->integration,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'integration';
	}
}

