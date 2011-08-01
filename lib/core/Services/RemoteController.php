<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_RemoteController
{
	private $url;
	private $controller;

	function __construct($url, $controller)
	{
		$this->url = $url;
		$this->controller = $controller;
	}

	function __call($action, $args)
	{
		$arguments = array();
		if (isset($args[0]) && is_array($args[0])) {
			$arguments = $args[0];
		}

		return $this->getJson($action, $arguments);
	}

	function getResultLoader($action, $arguments, $offsetKey, $maxRecordsKey, $resultKey, $perPage = 20)
	{
		$client = $this->getClient($action, $arguments);
		return new Services_ResultLoader(
			array(new Services_ResultLoader_WebService($client, $offsetKey, $maxRecordsKey, $resultKey), '__invoke'),
			$perPage
		);
	}

	private function getClient($action, $postArguments = array())
	{
		$tikilib = TikiLib::lib('tiki');
		$client = $tikilib->get_http_client($this->url . '/tiki-ajax_services.php');
		$client->setParameterGet(array(
			'controller' => $this->controller,
			'action' => $action,
		));
		$client->setParameterPost($postArguments);

		return $client;
	}

	private function getJson($action, $postArguments = array())
	{
		$client = $this->getClient($action, $postArguments);
		$client->setHeaders('Accept', 'application/json');
		$response = $client->request('POST');

		if (! $response->isSuccessful()) {
			throw new Services_Exception(tr('Remote service unaccessible (%0)', $response->getStatus()), 400);
		}

		return json_decode($response->getBody(), true);
	}
}

