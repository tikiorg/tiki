<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_ResultLoader_WebService
{
	private $client;
	private $offsetKey;
	private $countKey;
	private $resultKey;

	function __construct($client, $offsetKey, $countKey, $resultKey)
	{
		$this->client = $client;
		$this->offsetKey = $offsetKey;
		$this->countKey = $countKey;
		$this->resultKey = $resultKey;
	}

	function __invoke($offset, $count)
	{
		$this->client->setParameterPost(
			array(
				$this->offsetKey => $offset,
				$this->countKey => $count,
			)
		);
		$this->client->setHeaders(array('Accept' => 'application/json'));

		$this->client->setMethod(Zend\Http\Request::METHOD_POST);
		$response = $this->client->send();

		if (! $response->isSuccess()) {
			throw new Services_Exception(tr('Remote service inaccessible (%0)', $response->getStatusCode()), 400);
		}

		$out = json_decode($response->getBody(), true);
		return $out[$this->resultKey];
	}
}

