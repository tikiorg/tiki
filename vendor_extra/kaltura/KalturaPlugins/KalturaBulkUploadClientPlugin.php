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
abstract class KalturaBulkServiceData extends KalturaObjectBase
{

}


/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Get bulk upload batch job by id
	 * 
	 * @param int $id 
	 * @return KalturaBulkUpload
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("bulkupload_bulk", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBulkUpload");
		return $resultObject;
	}

	/**
	 * List bulk upload batch jobs
	 * 
	 * @param KalturaBulkUploadFilter $bulkUploadFilter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaBulkUploadListResponse
	 */
	function listAction(KalturaBulkUploadFilter $bulkUploadFilter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($bulkUploadFilter !== null)
			$this->client->addParam($kparams, "bulkUploadFilter", $bulkUploadFilter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("bulkupload_bulk", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBulkUploadListResponse");
		return $resultObject;
	}

	/**
	 * Serve action returns the original file.
	 * 
	 * @param int $id Job id
	 * @return file
	 */
	function serve($id)
	{
		if ($this->client->isMultiRequest())
			throw new KalturaClientException("Action is not supported as part of multi-request.", KalturaClientException::ERROR_ACTION_IN_MULTIREQUEST);
		
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("bulkupload_bulk", "serve", $kparams);
		if(!$this->client->getDestinationPath() && !$this->client->getReturnServedResult())
			return $this->client->getServeUrl();
		return $this->client->doQueue();
	}

	/**
	 * ServeLog action returns the log file for the bulk-upload job.
	 * 
	 * @param int $id Job id
	 * @return file
	 */
	function serveLog($id)
	{
		if ($this->client->isMultiRequest())
			throw new KalturaClientException("Action is not supported as part of multi-request.", KalturaClientException::ERROR_ACTION_IN_MULTIREQUEST);
		
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("bulkupload_bulk", "serveLog", $kparams);
		if(!$this->client->getDestinationPath() && !$this->client->getReturnServedResult())
			return $this->client->getServeUrl();
		return $this->client->doQueue();
	}

	/**
	 * Aborts the bulk upload and all its child jobs
	 * 
	 * @param int $id Job id
	 * @return KalturaBulkUpload
	 */
	function abort($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("bulkupload_bulk", "abort", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBulkUpload");
		return $resultObject;
	}
}
/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaBulkService
	 */
	public $bulk = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->bulk = new KalturaBulkService($client);
	}

	/**
	 * @return KalturaBulkUploadClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaBulkUploadClientPlugin($client);
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'bulk' => $this->bulk,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'bulkUpload';
	}
}

